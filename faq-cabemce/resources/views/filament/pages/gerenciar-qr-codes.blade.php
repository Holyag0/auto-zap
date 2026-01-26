<x-filament-panels::page>
    <div class="space-y-6">
        
        <!-- Header com Instruções -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-blue-800">
                        Como usar os QR Codes
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Clique em "Visualizar QR Code" para ver o código do setor</li>
                            <li>Baixe o QR Code e imprima</li>
                            <li>Coloque o QR Code impresso no local de atendimento</li>
                            <li>Associados podem escanear o código para gerar senhas</li>
                            <li>Forneça o código de acesso presencialmente para segurança</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Setores com QR Codes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($this->setores as $setor)
                <div class="bg-white rounded-lg shadow-lg border-2 border-gray-200 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    
                    <!-- Header do Card -->
                    <div class="bg-gradient-to-r from-cabemce-blue to-blue-800 p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold">{{ $setor->sigla }}</h3>
                                <p class="text-sm text-blue-100">{{ $setor->nome }}</p>
                            </div>
                            @if($setor->configuracao && $setor->configuracao->permite_autoatendimento)
                                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    ATIVO
                                </span>
                            @else
                                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    INATIVO
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- QR Code Preview -->
                    <div class="p-6 bg-gray-50 flex justify-center">
                        <div class="bg-white p-4 rounded-lg shadow-md border-4 border-cabemce-blue">
                            <img 
                                src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('senha.create', $setor->id)) }}" 
                                alt="QR Code {{ $setor->sigla }}"
                                class="w-48 h-48"
                            >
                        </div>
                    </div>

                    <!-- Informações -->
                    <div class="p-4 space-y-3 bg-white">
                        
                        <!-- Código de Acesso -->
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-yellow-800 uppercase">Código de Acesso</p>
                                    <p class="text-2xl font-mono font-bold text-yellow-900 tracking-wider">
                                        {{ $setor->configuracao->codigo_acesso ?? 'N/A' }}
                                    </p>
                                </div>
                                <button 
                                    wire:click="gerarNovoCodigo({{ $setor->id }})"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-full transition-colors duration-200"
                                    title="Gerar novo código"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Prefixo das Senhas -->
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Prefixo das Senhas:</span>
                            <span class="font-bold text-cabemce-blue">{{ $setor->configuracao->prefixo ?? $setor->sigla }}</span>
                        </div>

                        <!-- Contador Atual -->
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Contador Atual:</span>
                            <span class="font-bold text-cabemce-blue">{{ $setor->configuracao->contador_atual ?? 0 }}</span>
                        </div>

                    </div>

                    <!-- Botões de Ação -->
                    <div class="p-4 bg-gray-100 border-t border-gray-200 grid grid-cols-2 gap-2">
                        
                        <!-- Visualizar QR Code (Página Pública) -->
                        <a 
                            href="{{ route('senha.qrcode', $setor->id) }}" 
                            target="_blank"
                            class="flex items-center justify-center bg-cabemce-blue hover:bg-blue-800 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 text-sm"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver QR Code
                        </a>

                        <!-- Baixar QR Code -->
                        <a 
                            href="https://api.qrserver.com/v1/create-qr-code/?size=1000x1000&data={{ urlencode(route('senha.create', $setor->id)) }}&download=1&filename=qrcode-{{ $setor->sigla }}.png"
                            target="_blank"
                            class="flex items-center justify-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 text-sm"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Baixar
                        </a>

                        <!-- Ver Painel Público -->
                        <a 
                            href="{{ route('painel.setor', $setor->id) }}" 
                            target="_blank"
                            class="flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 text-sm"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Ver Painel
                        </a>

                        <!-- Controle Operador -->
                        <a 
                            href="{{ route('operador.painel', $setor->id) }}" 
                            target="_blank"
                            class="flex items-center justify-center bg-cabemce-red hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 text-sm"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                            Controle
                        </a>

                    </div>

                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Nenhum setor ativo encontrado. Ative setores para gerar QR Codes.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Informações Adicionais -->
        <div class="mt-8 bg-gray-50 rounded-lg p-6 border border-gray-200">
            <h3 class="text-lg font-bold text-cabemce-blue mb-4">📌 Informações Importantes</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                <div class="flex items-start space-x-2">
                    <span class="text-cabemce-blue font-bold">•</span>
                    <p><strong>QR Code:</strong> Leva o associado para o formulário de gerar senha</p>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-cabemce-blue font-bold">•</span>
                    <p><strong>Código de Acesso:</strong> Deve ser fornecido presencialmente por segurança</p>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-cabemce-blue font-bold">•</span>
                    <p><strong>Baixar:</strong> Download do QR Code em alta resolução para impressão</p>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-cabemce-blue font-bold">•</span>
                    <p><strong>Controle:</strong> Painel do operador para chamar e atender senhas</p>
                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>
