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
                
                Log::info('Resposta recebida do n8n', ['data' => $data]);

                return [
                    'success' => true,
                    'resposta' => $data['resposta'] ?? $data['message'] ?? 'Resposta recebida sem conteúdo',
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

