@if(!empty($arquivos) && is_array($arquivos))
<div style="margin-bottom: 1.5rem;">
    <p style="font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 1rem;">
        Total: {{ count($arquivos) }} {{ count($arquivos) === 1 ? 'imagem' : 'imagens' }}
    </p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
        @foreach($arquivos as $index => $arquivo)
            @php
                $url = asset('storage/' . $arquivo);
                $filename = basename($arquivo);
            @endphp
            <div style="border: 2px solid #E5E7EB; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s; background: white;">
                {{-- Preview da Imagem --}}
                <div style="position: relative; width: 100%; height: 220px; background-color: #F3F4F6; overflow: hidden;">
                    <img 
                        src="{{ $url }}" 
                        alt="Imagem {{ $index + 1 }}" 
                        style="width: 100%; height: 100%; object-fit: cover; display: block;"
                        loading="lazy"
                    />
                    {{-- Overlay no hover --}}
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0); transition: background 0.3s;" 
                         onmouseover="this.style.background='rgba(0,0,0,0.2)'" 
                         onmouseout="this.style.background='rgba(0,0,0,0)'">
                    </div>
                </div>

                {{-- Informações e Botão --}}
                <div style="padding: 1rem;">
                    {{-- Nome do arquivo --}}
                    <div style="margin-bottom: 0.75rem;">
                        <p style="font-size: 0.875rem; font-weight: 600; color: #1F2937; margin-bottom: 0.25rem;">
                            Imagem {{ $index + 1 }}
                        </p>
                        <p style="font-size: 0.75rem; color: #6B7280; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $filename }}">
                            {{ $filename }}
                        </p>
                    </div>

                    {{-- Botão para abrir imagem --}}
                    <a 
                        href="{{ $url }}" 
                        target="_blank"
                        style="display: flex !important; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.75rem 1rem; background-color: #2563EB; color: white; font-size: 0.875rem; font-weight: 600; border-radius: 8px; text-decoration: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.2s; border: none; cursor: pointer;"
                        onmouseover="this.style.backgroundColor='#1D4ED8'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)'; this.style.transform='translateY(-2px)';"
                        onmouseout="this.style.backgroundColor='#2563EB'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'; this.style.transform='translateY(0)';"
                    >
                        <svg style="width: 1.25rem; height: 1.25rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span>Abrir em Nova Aba</span>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@else
<div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
    <svg class="mx-auto h-16 w-16 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
    </svg>
    <p class="mt-4 text-base font-medium text-gray-600 dark:text-gray-300">Nenhuma imagem anexada</p>
    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Esta demanda não possui anexos</p>
</div>
@endif
