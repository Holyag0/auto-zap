<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Descrição -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Sistema de Controle para Atendentes</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Interface simplificada para atendentes chamarem e gerenciarem senhas sem acesso ao admin completo.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acesso Rápido -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Acesso Rápido ao Sistema de Operador</h2>
            </div>
            <div class="p-6">
                <a href="/operador" 
                   target="_blank"
                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    Abrir Sistema de Operador
                </a>
                <p class="mt-2 text-sm text-gray-500">Abre em uma nova aba</p>
            </div>
        </div>

        <!-- Grid de Setores -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Acesso Direto por Setor</h2>
                <p class="text-sm text-gray-600 mt-1">Clique para acessar o painel de controle de um setor específico</p>
            </div>
            
            <div class="p-6">
                @php
                    $setores = $this->getSetores();
                @endphp

                @if($setores->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($setores as $setor)
                            @php
                                $aguardando = App\Models\Senha::porSetor($setor->id)->aguardando()->hoje()->count();
                                $atendidas = App\Models\Senha::porSetor($setor->id)->atendidas()->hoje()->count();
                            @endphp
                            
                            <a href="/operador/{{ $setor->id }}" 
                               target="_blank"
                               class="block bg-gradient-to-br from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 rounded-lg p-4 border border-blue-200 transition-all hover:shadow-md">
                                
                                <!-- Header -->
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-bold text-gray-900 text-lg">{{ $setor->nome }}</h3>
                                    @if($setor->sigla)
                                        <span class="bg-blue-600 text-white px-2 py-1 rounded text-xs font-semibold">
                                            {{ $setor->sigla }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Descrição -->
                                @if($setor->descricao)
                                    <p class="text-sm text-gray-600 mb-3">{{ $setor->descricao }}</p>
                                @endif

                                <!-- Stats -->
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div class="bg-white rounded-lg p-2 text-center">
                                        <p class="text-xs text-gray-500 font-medium">Aguardando</p>
                                        <p class="text-xl font-bold text-yellow-600">{{ $aguardando }}</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-2 text-center">
                                        <p class="text-xs text-gray-500 font-medium">Atendidas</p>
                                        <p class="text-xl font-bold text-green-600">{{ $atendidas }}</p>
                                    </div>
                                </div>

                                <!-- Código -->
                                @if($setor->configuracao)
                                    <div class="bg-white rounded-lg p-2">
                                        <p class="text-xs text-gray-500">Código de Acesso:</p>
                                        <p class="text-sm font-mono font-bold text-gray-700">{{ $setor->configuracao->codigo_acesso }}</p>
                                    </div>
                                @endif

                                <!-- Footer -->
                                <div class="mt-3 pt-3 border-t border-blue-200 flex items-center justify-between">
                                    <span class="text-sm font-semibold text-blue-600">Acessar Controle →</span>
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="mt-4 text-lg text-gray-500">Nenhum setor com sistema de senhas configurado</p>
                        <p class="text-gray-400 mt-2">Configure os setores em <strong>Setores</strong> > <strong>Configuração de Senhas</strong></p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Links Úteis -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Painéis Públicos</h3>
                        <a href="/painel" target="_blank" class="text-sm text-blue-600 hover:text-blue-700">
                            Ver painéis →
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Gerenciar Senhas</h3>
                        <a href="/admin/senhas" class="text-sm text-blue-600 hover:text-blue-700">
                            Ir para senhas →
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Configurar Setores</h3>
                        <a href="/admin/setors" class="text-sm text-blue-600 hover:text-blue-700">
                            Ir para setores →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instruções -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Importante</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>O Sistema de Operador é uma interface separada, acessível sem login do admin</li>
                            <li>Atendentes podem usar <strong>/operador</strong> diretamente</li>
                            <li>Cada setor tem seu próprio painel de controle independente</li>
                            <li>Os códigos de acesso são visíveis para facilitar o autoatendimento</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
