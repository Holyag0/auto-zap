<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class N8nService
{
    /**
     * URL do webhook do n8n
     */
    protected string $webhookUrl;

    public function __construct()
    {
        // URL do n8n - ajuste conforme necessário
        $this->webhookUrl = config('services.n8n.webhook_url', 'http://auto-zap-n8n-1:5678/webhook');
    }

    /**
     * Envia uma pergunta para o modelo via n8n
     *
     * @param string $pergunta
     * @param array $context Contexto adicional (opcional)
     * @return array
     */
    public function testarPergunta(string $pergunta, array $context = []): array
    {
        try {
            $payload = [
                'chatInput' => $pergunta,
                'pergunta' => $pergunta, // Manter compatibilidade
                'context' => $context,
                'timestamp' => now()->toIso8601String(),
            ];

            Log::info('Enviando pergunta para n8n', ['payload' => $payload]);

            $response = Http::timeout(30)
                ->post($this->webhookUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Resposta recebida do n8n', ['data' => $data, 'body' => $response->body()]);

                // Tentar extrair resposta de diferentes formatos possíveis
                $resposta = null;
                
                // Formato 1: resposta direta no campo 'resposta' (pode ser null se $json.output estiver vazio)
                if (isset($data['resposta'])) {
                    // Verificar se não é null, não é string vazia, e não é apenas espaços
                    $respostaValue = $data['resposta'];
                    if ($respostaValue !== null && $respostaValue !== '' && trim($respostaValue) !== '') {
                        $resposta = $respostaValue;
                    }
                }
                // Formato 2: resposta no campo 'message'
                if (empty($resposta) && isset($data['message']) && !empty($data['message'])) {
                    $resposta = $data['message'];
                }
                // Formato 3: resposta no campo 'text'
                if (empty($resposta) && isset($data['text']) && !empty($data['text'])) {
                    $resposta = $data['text'];
                }
                // Formato 4: resposta no campo 'output' (caso o n8n retorne diretamente)
                if (empty($resposta) && isset($data['output']) && !empty($data['output'])) {
                    $resposta = $data['output'];
                }
                // Formato 5: resposta como string direta (se o n8n retornar apenas texto)
                if (empty($resposta) && is_string($data) && !empty($data)) {
                    $resposta = $data;
                }
                // Formato 6: resposta em array aninhado
                if (empty($resposta) && isset($data['data']['resposta']) && !empty($data['data']['resposta'])) {
                    $resposta = $data['data']['resposta'];
                }
                if (empty($resposta) && isset($data['data']['message']) && !empty($data['data']['message'])) {
                    $resposta = $data['data']['message'];
                }
                if (empty($resposta) && isset($data['data']['output']) && !empty($data['data']['output'])) {
                    $resposta = $data['data']['output'];
                }
                // Formato 7: resposta no primeiro item de array
                if (empty($resposta) && is_array($data) && isset($data[0]) && is_string($data[0]) && !empty($data[0])) {
                    $resposta = $data[0];
                }

                if (empty($resposta)) {
                    Log::warning('Resposta do n8n não contém conteúdo válido', [
                        'data' => $data,
                        'body' => $response->body(),
                        'status' => $response->status(),
                        'resposta_field' => $data['resposta'] ?? 'não existe',
                        'resposta_is_null' => isset($data['resposta']) && $data['resposta'] === null,
                        'resposta_is_empty' => isset($data['resposta']) && $data['resposta'] === '',
                    ]);
                    $resposta = 'Desculpe, não consegui processar sua mensagem no momento. Por favor, tente novamente.';
                }

                return [
                    'success' => true,
                    'resposta' => $resposta,
                    'dados_completos' => $data,
                ];
            }

            Log::error('Erro ao consultar n8n', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao consultar o modelo. Status: ' . $response->status(),
                'details' => $response->body(),
            ];

        } catch (\Exception $e) {
            Log::error('Exceção ao consultar n8n', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao conectar com o n8n: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Testa uma FAQ específica
     *
     * @param \App\Models\Faq $faq
     * @return array
     */
    public function testarFaq(\App\Models\Faq $faq): array
    {
        return $this->testarPergunta($faq->pergunta, [
            'faq_id' => $faq->id,
            'categoria' => $faq->categoria,
            'resposta_esperada' => $faq->resposta,
        ]);
    }

    /**
     * Verifica se o n8n está acessível
     *
     * @return bool
     */
    public function verificarConexao(): bool
    {
        try {
            $response = Http::timeout(5)->get(str_replace('/webhook', '/healthz', $this->webhookUrl));
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}

