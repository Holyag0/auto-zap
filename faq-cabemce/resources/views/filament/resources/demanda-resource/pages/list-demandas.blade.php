<x-filament-panels::page>
    @if(!request()->has('setor'))
        {{-- Cards dos Setores --}}
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                Selecione um Setor
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Clique em um setor para visualizar suas demandas
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($this->getSetores() as $setor)
                <a href="{{ route('filament.admin.resources.demandas.index', ['setor' => $setor->id]) }}"
                   class="block p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 transition-all hover:shadow-lg">
                    
                    {{-- Nome do Setor --}}
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $setor->nome }}
                        </h3>
                        @if($setor->sigla)
                            <span class="px-2 py-1 text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded">
                                {{ $setor->sigla }}
                            </span>
                        @endif
                    </div>

                    @if($setor->descricao)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            {{ $setor->descricao }}
                        </p>
                    @endif

                    {{-- Estatísticas --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $setor->total_demandas }}
                            </div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                Total
                            </div>
                        </div>

                        <div class="text-center p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ $setor->em_analise }}
                            </div>
                            <div class="text-xs text-yellow-600 dark:text-yellow-400">
                                Em Análise
                            </div>
                        </div>

                        <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $setor->em_andamento }}
                            </div>
                            <div class="text-xs text-blue-600 dark:text-blue-400">
                                Em Andamento
                            </div>
                        </div>

                        <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ $setor->concluidas }}
                            </div>
                            <div class="text-xs text-green-600 dark:text-green-400">
                                Concluídas
                            </div>
                        </div>
                    </div>

                    {{-- Indicador de clique --}}
                    <div class="mt-4 text-center">
                        <span class="text-xs text-primary-600 dark:text-primary-400 font-semibold flex items-center justify-center gap-1">
                            Ver Demandas
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        {{-- Breadcrumb e botão de voltar --}}
        <div class="mb-6 flex items-center justify-between">
            <div class="flex-1">
                <a href="{{ route('filament.admin.resources.demandas.index') }}"
                   class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Voltar para Setores
                </a>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mt-2">
                    Demandas - {{ \App\Models\Setor::find(request('setor'))?->nome }}
                </h2>
            </div>
            
            {{-- Botão de Nova Demanda com setor pré-selecionado --}}
            <div>
                <a href="{{ route('filament.admin.resources.demandas.create', ['setor' => request('setor')]) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 rounded-lg shadow transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nova Demanda
                </a>
            </div>
        </div>

        {{-- Tabela padrão do Filament --}}
        {{ $this->table }}
    @endif
</x-filament-panels::page>
