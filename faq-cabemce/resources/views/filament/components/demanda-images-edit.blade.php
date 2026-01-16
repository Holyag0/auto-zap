<div 
    x-data="imagemManager"
    x-init="init(@js(!empty($arquivos) && is_array($arquivos) ? $arquivos : []))"
    class="space-y-4"
>
    
    <!-- Modal de Confirmação Moderno -->
    <div 
        x-show="modalAberto"
        x-cloak
        @keydown.escape.window="fecharModal()"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <!-- Overlay -->
        <div 
            x-show="modalAberto"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"
            @click="fecharModal()"
        ></div>
        
        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div 
                x-show="modalAberto"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                @click.stop
            >
                <!-- Ícone de Aviso -->
                <div class="sm:flex sm:items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/20 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                            Remover Imagem?
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Tem certeza que deseja remover esta imagem? 
                                <strong>Esta ação não pode ser desfeita.</strong>
                            </p>
                        </div>
                        
                        <!-- Preview da Imagem -->
                        <div class="mt-4 flex justify-center" x-show="imagemParaRemover">
                            <div class="relative w-32 h-32 rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700">
                                <img 
                                    :src="`{{ asset('storage') }}/${imagemParaRemover}`" 
                                    alt="Preview" 
                                    class="w-full h-full object-cover"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botões de Ação -->
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button 
                        type="button"
                        @click="confirmarRemocao()"
                        style="background-color: #dc2626 !important; border-color: #dc2626 !important;"
                        class="inline-flex items-center justify-center w-full px-5 py-2.5 text-base font-semibold text-white rounded-lg shadow-lg hover:brightness-90 focus:outline-none focus:ring-4 focus:ring-red-300 dark:focus:ring-red-800 sm:ml-3 sm:w-auto transition-all transform hover:scale-105"
                    >
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Sim, Remover
                    </button>
                    <button 
                        type="button"
                        @click="fecharModal()"
                        class="inline-flex items-center justify-center w-full px-5 py-2.5 mt-3 text-base font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-700 sm:mt-0 sm:w-auto transition-all"
                    >
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <template x-if="listaArquivos.length > 0">
        <div>
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    📎 Imagens Atuais (<span x-text="listaArquivos.length"></span>/5)
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    💡 Clique no botão vermelho para remover
                </p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <template x-for="(arquivo, index) in listaArquivos" :key="arquivo">
                    <div class="relative rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                        <!-- Botão de remover - SEMPRE VISÍVEL -->
                        <button 
                            type="button"
                            @click.prevent="removerArquivo(index, arquivo)"
                            style="background-color: #dc2626 !important;"
                            class="absolute top-2 right-2 z-10 hover:brightness-90 text-white rounded-full p-2.5 shadow-xl transform hover:scale-110 transition-all border-2 border-white"
                            title="Remover imagem"
                        >
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        
                        <!-- Imagem -->
                        <a :href="`{{ asset('storage') }}/${arquivo}`" target="_blank" class="block">
                            <img 
                                :src="`{{ asset('storage') }}/${arquivo}`" 
                                :alt="`Anexo ${index + 1}`" 
                                class="w-full h-48 object-cover hover:opacity-90 transition-opacity"
                                loading="lazy"
                            />
                        </a>
                        
                        <!-- Nome do arquivo e ações -->
                        <div class="p-2 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <p class="text-xs text-gray-600 dark:text-gray-400 truncate flex-1" x-text="'Imagem ' + (index + 1)"></p>
                            <a 
                                :href="`{{ asset('storage') }}/${arquivo}`" 
                                target="_blank"
                                class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 ml-2"
                                title="Abrir em nova aba"
                            >
                                🔍
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>
    
    <template x-if="listaArquivos.length === 0">
        <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nenhuma imagem anexada</p>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Adicione novas imagens abaixo</p>
        </div>
    </template>
    
    <!-- Aviso de arquivos marcados para remoção -->
    <template x-if="listaRemovidos.length > 0">
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                        ⚠️ Atenção: Imagens marcadas para remoção
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                        <p>
                            <span x-text="listaRemovidos.length"></span> 
                            <span x-text="listaRemovidos.length === 1 ? 'imagem será removida' : 'imagens serão removidas'"></span>
                            quando você clicar em <strong>"Salvar"</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('imagemManager', () => ({
        listaArquivos: [],
        listaRemovidos: [],
        modalAberto: false,
        imagemParaRemover: null,
        indexParaRemover: null,
        
        init(arquivosIniciais) {
            // Garantir que arquivosIniciais seja sempre um array
            if (!arquivosIniciais || !Array.isArray(arquivosIniciais)) {
                console.warn('⚠️ arquivosIniciais inválido, usando array vazio');
                arquivosIniciais = [];
            }
            
            this.listaArquivos = [...arquivosIniciais];
            console.log('✅ Manager inicializado com arquivos:', this.listaArquivos);
        },
        
        removerArquivo(index, arquivo) {
            console.log('🗑️ Abrindo modal para remover:', index, arquivo);
            
            // Armazenar informações temporárias
            this.indexParaRemover = index;
            this.imagemParaRemover = arquivo;
            
            // Abrir modal
            this.modalAberto = true;
        },
        
        fecharModal() {
            console.log('❌ Modal fechado - ação cancelada');
            this.modalAberto = false;
            this.imagemParaRemover = null;
            this.indexParaRemover = null;
        },
        
        confirmarRemocao() {
            console.log('✅ Confirmado! Removendo imagem...');
            
            // Adicionar à lista de removidos
            this.listaRemovidos.push(this.imagemParaRemover);
            
            // Remover da lista atual
            this.listaArquivos.splice(this.indexParaRemover, 1);
            
            // Atualizar campos hidden
            this.atualizarCampos();
            
            console.log('📊 Status:', {
                restantes: this.listaArquivos.length,
                removidos: this.listaRemovidos.length
            });
            
            // Fechar modal
            this.fecharModal();
            
            // Mostrar notificação de sucesso
            this.$nextTick(() => {
                const notification = document.createElement('div');
                notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-bounce';
                notification.innerHTML = '✅ Imagem removida! Lembre-se de salvar.';
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => notification.remove(), 500);
                }, 3000);
            });
        },
        
        atualizarCampos() {
            console.log('📝 Atualizando campos...', {
                removidos: this.listaRemovidos,
                restantes: this.listaArquivos
            });
            
            // Tentar encontrar o componente Livewire e atualizar via wire
            const livewireComponent = Livewire.find(
                document.querySelector('[wire\\:id]')?.getAttribute('wire:id')
            );
            
            if (livewireComponent) {
                // Atualizar via Livewire
                livewireComponent.set('data.arquivos_removidos', JSON.stringify(this.listaRemovidos));
                console.log('✅ Arquivos removidos enviados via Livewire');
            } else {
                console.warn('⚠️ Componente Livewire não encontrado, usando fallback');
                
                // Fallback: atualizar input hidden diretamente
                const inputRemovidos = document.querySelector('input[name="data.arquivos_removidos"], input[id*="arquivos_removidos"]');
                if (inputRemovidos) {
                    inputRemovidos.value = JSON.stringify(this.listaRemovidos);
                    inputRemovidos.dispatchEvent(new Event('input', { bubbles: true }));
                    inputRemovidos.dispatchEvent(new Event('change', { bubbles: true }));
                    console.log('✅ Input hidden atualizado via fallback');
                }
            }
        }
    }));
});
</script>
