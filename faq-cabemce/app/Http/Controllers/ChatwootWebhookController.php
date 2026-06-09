<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\N8nService;

class ChatwootWebhookController extends Controller
{
    private $evolutionApiUrl = 'http://evolution_api:8080'; // Nome do container no docker-compose
    private $evolutionApiKey = '429683C4C977415CAAFCCE10F7D57E11';
    private $instanceName = 'faq-test';
    private $n8nService;

    public function __construct(N8nService $n8nService)
    {
        $this->n8nService = $n8nService;
    }

    /**
     * Recebe webhook do Chatwoot quando uma mensagem chega
     * Chama o n8n para gerar resposta e envia para Evolution API
     */
    public function handle(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('Chatwoot Webhook recebido', ['data' => $data]);

            // Verificar se é uma mensagem nova
            if (!isset($data['event']) || $data['event'] !== 'message_created') {
                Log::info('Evento ignorado', ['event' => $data['event'] ?? 'unknown']);
                return response()->json(['success' => true, 'message' => 'Evento ignorado']);
            }

            // Extrair informações da mensagem
            // O Chatwoot pode enviar a mensagem em diferentes estruturas:
            // 1. data['message'] - estrutura direta
            // 2. data['conversation']['messages'][0] - dentro do array de mensagens
            $message = $data['message'] ?? [];
            $conversation = $data['conversation'] ?? [];
            $contact = $data['contact'] ?? [];
            
            // Se message está vazio, tentar pegar do array messages
            if (empty($message) && isset($conversation['messages']) && is_array($conversation['messages']) && count($conversation['messages']) > 0) {
                $message = $conversation['messages'][0];
                Log::info('Mensagem extraída do array messages', ['message_id' => $message['id'] ?? 'unknown']);
            }

            // Verificar se é mensagem de entrada (não de saída)
            // message_type: 0 = incoming, 1 = outgoing (integers no Chatwoot)
            // Também pode vir como string "incoming" ou "outgoing"
            $messageType = $message['message_type'] ?? null;
            $isIncoming = ($messageType === 0 || $messageType === 'incoming');
            
            if (!$isIncoming) {
                Log::info('Mensagem de saída ignorada', [
                    'message_type' => $messageType,
                    'message_id' => $message['id'] ?? 'unknown'
                ]);
                return response()->json(['success' => true, 'message' => 'Mensagem de saída ignorada']);
            }

            // Extrair texto da mensagem
            $text = $message['content'] ?? '';
            if (empty($text)) {
                Log::warning('Mensagem sem conteúdo', ['message_id' => $message['id'] ?? 'unknown']);
                return response()->json(['success' => true, 'message' => 'Mensagem sem conteúdo']);
            }

            // Extrair número do telefone
            // Priorizar identifier que já vem no formato correto do WhatsApp
            $phoneNumber = null;
            
            // 1. Do identifier do sender (formato: 558898605941@s.whatsapp.net)
            if (isset($message['sender']['identifier'])) {
                $identifier = $message['sender']['identifier'];
                $phoneNumber = str_replace('@s.whatsapp.net', '', $identifier);
                $phoneNumber = str_replace('@lid', '', $phoneNumber);
                Log::info('Número extraído do sender identifier', ['identifier' => $identifier, 'phone' => $phoneNumber]);
            }
            
            // 2. Do identifier do contact
            if (empty($phoneNumber) && isset($contact['identifier'])) {
                $identifier = $contact['identifier'];
                $phoneNumber = str_replace('@s.whatsapp.net', '', $identifier);
                $phoneNumber = str_replace('@lid', '', $phoneNumber);
                Log::info('Número extraído do contact identifier', ['identifier' => $identifier, 'phone' => $phoneNumber]);
            }
            
            // 3. Do meta.sender.identifier
            if (empty($phoneNumber) && isset($conversation['meta']['sender']['identifier'])) {
                $identifier = $conversation['meta']['sender']['identifier'];
                $phoneNumber = str_replace('@s.whatsapp.net', '', $identifier);
                $phoneNumber = str_replace('@lid', '', $phoneNumber);
                Log::info('Número extraído do meta sender identifier', ['identifier' => $identifier, 'phone' => $phoneNumber]);
            }
            
            // 4. Do phone_number (normalizar apenas se necessário)
            if (empty($phoneNumber)) {
                if (isset($message['sender']['phone_number'])) {
                    $phoneNumber = $this->normalizePhoneNumber($message['sender']['phone_number']);
                } elseif (isset($contact['phone_number'])) {
                    $phoneNumber = $this->normalizePhoneNumber($contact['phone_number']);
                } elseif (isset($conversation['meta']['sender']['phone_number'])) {
                    $phoneNumber = $this->normalizePhoneNumber($conversation['meta']['sender']['phone_number']);
                }
            }
            
            if (empty($phoneNumber)) {
                Log::warning('Não foi possível extrair número do telefone', [
                    'message_id' => $message['id'] ?? 'unknown',
                    'contact' => $contact,
                    'conversation_meta' => $conversation['meta'] ?? null,
                    'message_sender' => $message['sender'] ?? null
                ]);
                return response()->json(['success' => true, 'message' => 'Número não encontrado']);
            }

            Log::info('Processando mensagem para n8n', [
                'text' => $text,
                'phone' => $phoneNumber
            ]);

            // Chamar n8n para gerar resposta
            $n8nResponse = $this->n8nService->testarPergunta($text);

            if (!$n8nResponse['success']) {
                Log::error('Erro ao consultar n8n', ['error' => $n8nResponse['error'] ?? 'Unknown']);
                
                // Enviar mensagem de erro para o usuário
                $this->sendMessageToEvolution($phoneNumber, 'Desculpe, ocorreu um erro ao processar sua mensagem. Tente novamente em alguns instantes.');
                
                return response()->json([
                    'success' => false,
                    'error' => $n8nResponse['error'] ?? 'Erro ao consultar n8n'
                ], 500);
            }

            // Extrair resposta do n8n
            $resposta = $n8nResponse['resposta'] ?? 'Desculpe, não consegui gerar uma resposta.';
            
            Log::info('Resposta do n8n recebida', ['resposta' => $resposta]);

            // Enviar resposta para Evolution API (que envia para WhatsApp)
            $sent = $this->sendMessageToEvolution($phoneNumber, $resposta);

            if ($sent) {
                Log::info('Resposta enviada com sucesso para WhatsApp', [
                    'phone' => $phoneNumber,
                    'resposta' => $resposta
                ]);
            } else {
                Log::error('Erro ao enviar resposta para Evolution API');
            }

            return response()->json([
                'success' => true,
                'message' => 'Mensagem processada com sucesso',
                'resposta' => $resposta
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook do Chatwoot', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extrai número do telefone do contato ou conversa
     */
    private function extractPhoneNumber($contact, $conversation)
    {
        // Tentar extrair do contato
        if (isset($contact['phone_number'])) {
            return $this->normalizePhoneNumber($contact['phone_number']);
        }

        // Tentar extrair do identifier do contato
        if (isset($contact['identifier'])) {
            $identifier = $contact['identifier'];
            // Remover @s.whatsapp.net se presente
            $identifier = str_replace('@s.whatsapp.net', '', $identifier);
            return $this->normalizePhoneNumber($identifier);
        }

        // Tentar extrair do meta da conversa
        if (isset($conversation['meta']['sender']['phone_number'])) {
            return $this->normalizePhoneNumber($conversation['meta']['sender']['phone_number']);
        }

        // Tentar extrair do source_id
        if (isset($conversation['source_id'])) {
            $sourceId = $conversation['source_id'];
            // Remover @s.whatsapp.net se presente
            $sourceId = str_replace('@s.whatsapp.net', '', $sourceId);
            return $this->normalizePhoneNumber($sourceId);
        }

        return null;
    }

    /**
     * Normaliza número de telefone para formato do WhatsApp
     * IMPORTANTE: Não força código 55, mantém o código de país original
     */
    private function normalizePhoneNumber($phone)
    {
        // Remover caracteres não numéricos, exceto o + no início
        $hasPlus = str_starts_with($phone, '+');
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Se tinha +, significa que tinha código de país - não adicionar 55
        if ($hasPlus) {
            // Já tem código de país, retornar como está
            return $phone;
        }
        
        // Se não tinha +, pode ser número brasileiro sem código
        // Se começar com 0, remover
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }
        
        // Se não começar com código de país conhecido, assumir Brasil (55)
        // Códigos de país têm 1-3 dígitos, então se tiver mais de 12 dígitos, já tem código
        if (strlen($phone) <= 12 && !str_starts_with($phone, '55')) {
            $phone = '55' . $phone;
        }

        return $phone;
    }

    /**
     * Envia mensagem para Evolution API
     */
    private function sendMessageToEvolution($phoneNumber, $text)
    {
        try {
            $url = "{$this->evolutionApiUrl}/message/sendText/{$this->instanceName}";
            
            Log::info('Enviando mensagem para Evolution API', [
                'url' => $url,
                'phone' => $phoneNumber,
                'text' => $text
            ]);

            $response = Http::withHeaders([
                'apikey' => $this->evolutionApiKey,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'number' => $phoneNumber,
                'text' => $text,
            ]);

            if ($response->successful()) {
                Log::info('Mensagem enviada com sucesso para Evolution API', [
                    'response' => $response->json()
                ]);
                return true;
            }

            Log::error('Erro ao enviar mensagem para Evolution API', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Exceção ao enviar mensagem para Evolution API', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
