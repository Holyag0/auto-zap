<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionWebhookController extends Controller
{
    private $chatwootUrl = 'http://localhost:3000';
    private $chatwootAccountId = 1;
    private $chatwootInboxId = 1;
    private $chatwootHmacToken = 'p63Ye4S31jPzAa1xuDFzp2je';

    public function handle(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('Evolution API Webhook recebido', ['data' => $data]);

            // Processar evento MESSAGES_UPSERT
            if (isset($data['event']) && $data['event'] === 'messages.upsert') {
                $message = $data['data'] ?? $data;
                $this->processMessage($message);
            }

            // Processar outros eventos
            if (isset($data['event']) && strpos($data['event'], 'messages') !== false) {
                $message = $data['data'] ?? $data;
                $this->processMessage($message);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook Evolution API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function processMessage($message)
    {
        try {
            // Extrair informações da mensagem
            $text = $this->extractMessageText($message);
            $from = $this->extractPhoneNumber($message);
            $messageId = $this->extractMessageId($message);

            if (!$text || !$from) {
                Log::warning('Mensagem sem texto ou remetente', ['message' => $message]);
                return;
            }

            Log::info('Processando mensagem para Chatwoot', [
                'from' => $from,
                'text' => $text
            ]);

            // Buscar ou criar conversa no Chatwoot
            $conversation = $this->findOrCreateConversation($from);

            if (!$conversation) {
                Log::error('Não foi possível criar/encontrar conversa no Chatwoot');
                return;
            }

            // Criar mensagem no Chatwoot
            $this->createMessage($conversation['id'], $text, $from);

        } catch (\Exception $e) {
            Log::error('Erro ao processar mensagem', [
                'error' => $e->getMessage(),
                'message' => $message
            ]);
        }
    }

    private function extractMessageText($message)
    {
        if (isset($message['message']['conversation'])) {
            return $message['message']['conversation'];
        }
        if (isset($message['message']['extendedTextMessage']['text'])) {
            return $message['message']['extendedTextMessage']['text'];
        }
        if (isset($message['text'])) {
            return $message['text'];
        }
        return null;
    }

    private function extractPhoneNumber($message)
    {
        if (isset($message['key']['remoteJid'])) {
            $jid = $message['key']['remoteJid'];
            // Remover @s.whatsapp.net
            return str_replace('@s.whatsapp.net', '', $jid);
        }
        if (isset($message['from'])) {
            return str_replace('@s.whatsapp.net', '', $message['from']);
        }
        return null;
    }

    private function extractMessageId($message)
    {
        return $message['key']['id'] ?? $message['id'] ?? uniqid();
    }

    private function findOrCreateConversation($phoneNumber)
    {
        // Buscar conversa existente
        $response = Http::withHeaders([
            'api_access_token' => $this->chatwootHmacToken,
        ])->get("{$this->chatwootUrl}/api/v1/accounts/{$this->chatwootAccountId}/conversations", [
            'source_id' => $phoneNumber,
            'inbox_id' => $this->chatwootInboxId,
        ]);

        if ($response->successful() && count($response->json()) > 0) {
            return $response->json()[0];
        }

        // Criar nova conversa
        $response = Http::withHeaders([
            'api_access_token' => $this->chatwootHmacToken,
        ])->post("{$this->chatwootUrl}/api/v1/accounts/{$this->chatwootAccountId}/conversations", [
            'source_id' => $phoneNumber,
            'inbox_id' => $this->chatwootInboxId,
            'contact' => [
                'name' => $phoneNumber,
                'phone_number' => '+' . $phoneNumber,
            ],
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    private function createMessage($conversationId, $text, $from)
    {
        $response = Http::withHeaders([
            'api_access_token' => $this->chatwootHmacToken,
        ])->post("{$this->chatwootUrl}/api/v1/accounts/{$this->chatwootAccountId}/conversations/{$conversationId}/messages", [
            'content' => $text,
            'message_type' => 'incoming',
            'private' => false,
        ]);

        if ($response->successful()) {
            Log::info('Mensagem criada no Chatwoot', [
                'conversation_id' => $conversationId,
                'text' => $text
            ]);
        } else {
            Log::error('Erro ao criar mensagem no Chatwoot', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);
        }
    }
}
