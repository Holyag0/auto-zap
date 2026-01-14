<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Formulário -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form wire:submit="testar">
                {{ $this->form }}
                
                <div class="mt-6 flex gap-3">
                    <x-filament::button 
                        type="submit"
                        color="success"
                        icon="heroicon-o-paper-airplane"
                        :disabled="$loading"
                    >
                        <span wire:loading.remove wire:target="testar">
                            Enviar para Modelo
                        </span>
                        <span wire:loading wire:target="testar">
                            Enviando...
                        </span>
                    </x-filament::button>
                    
                    <x-filament::button 
                        type="button"
                        color="gray"
                        icon="heroicon-o-arrow-path"
                        wire:click="limpar"
                        :disabled="$loading"
                    >
                        Limpar
                    </x-filament::button>
                </div>
            </form>
        </div>

        <!-- Resposta -->
        @if ($resposta)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center gap-2 mb-4">
                    <x-heroicon-o-sparkles class="w-6 h-6 text-primary-500" />
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Resposta do Modelo
                    </h3>
                </div>
                
                <div class="prose dark:prose-invert max-w-none">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $resposta }}</p>
                    </div>
                </div>

                <!-- Informações adicionais -->
                <div class="mt-4 flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-1">
                        <x-heroicon-o-clock class="w-4 h-4" />
                        <span>{{ now()->format('H:i:s') }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <x-heroicon-o-server class="w-4 h-4" />
                        <span>N8N Workflow</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Card de Informações -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    <x-heroicon-o-information-circle class="w-6 h-6 text-blue-500" />
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">
                        Como funciona
                    </h4>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Esta ferramenta envia sua pergunta diretamente para o modelo de IA através do workflow n8n. 
                        O modelo utilizará as FAQs ativas cadastradas no sistema para gerar a resposta.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
