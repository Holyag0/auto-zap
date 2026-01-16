<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriaSeeder extends Seeder
{
    /**
     * Mapeamento de categorias com informações adicionais
     */
    protected $categoriasConfig = [
        'Empresa' => [
            'descricao' => 'Informações sobre a CABEMCE',
            'cor' => '#3b82f6',
            'icone' => 'heroicon-o-building-office',
            'ordem' => 1,
        ],
        'Horarios' => [
            'descricao' => 'Horários de funcionamento',
            'cor' => '#10b981',
            'icone' => 'heroicon-o-clock',
            'ordem' => 2,
        ],
        'Endereços' => [
            'descricao' => 'Localização e endereço',
            'cor' => '#f59e0b',
            'icone' => 'heroicon-o-map-pin',
            'ordem' => 3,
        ],
        'Contatos' => [
            'descricao' => 'Formas de contato',
            'cor' => '#8b5cf6',
            'icone' => 'heroicon-o-phone',
            'ordem' => 4,
        ],
        'serviços' => [
            'descricao' => 'Serviços oferecidos pela CABEMCE',
            'cor' => '#06b6d4',
            'icone' => 'heroicon-o-wrench-screwdriver',
            'ordem' => 5,
        ],
        'loja' => [
            'descricao' => 'Informações sobre a loja',
            'cor' => '#ec4899',
            'icone' => 'heroicon-o-shopping-bag',
            'ordem' => 6,
        ],
        'creche' => [
            'descricao' => 'Creche Escola Tiradentes',
            'cor' => '#f97316',
            'icone' => 'heroicon-o-academic-cap',
            'ordem' => 7,
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Populando categorias...');

        // Busca categorias únicas da tabela FAQ
        $categoriasExistentes = DB::connection('pgsql_chatwoot')
            ->table('faq')
            ->select('categoria')
            ->distinct()
            ->whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->pluck('categoria')
            ->toArray();

        $this->command->info('📊 Categorias encontradas: ' . implode(', ', $categoriasExistentes));

        $count = 0;
        foreach ($categoriasExistentes as $nomeCategoria) {
            // Pega configuração ou usa valores padrão
            $config = $this->categoriasConfig[$nomeCategoria] ?? [
                'descricao' => "Categoria $nomeCategoria",
                'cor' => '#6366f1',
                'icone' => 'heroicon-o-folder',
                'ordem' => 99,
            ];

            // Cria ou atualiza a categoria
            $categoria = Categoria::updateOrCreate(
                ['nome' => $nomeCategoria],
                [
                    'slug' => Str::slug($nomeCategoria),
                    'descricao' => $config['descricao'],
                    'cor' => $config['cor'],
                    'icone' => $config['icone'],
                    'ordem' => $config['ordem'],
                    'ativo' => true,
                ]
            );

            $count++;
            $this->command->line("  ✅ {$categoria->nome} - {$config['descricao']}");
        }

        $this->command->info("✨ {$count} categorias criadas com sucesso!");

        // Exibe estatísticas
        $this->command->newLine();
        $this->command->info('📈 Estatísticas por categoria:');
        
        $categorias = Categoria::with('faqs')->get();
        foreach ($categorias as $categoria) {
            $totalFaqs = DB::connection('pgsql_chatwoot')
                ->table('faq')
                ->where('categoria', $categoria->nome)
                ->count();
                
            $this->command->line("  📁 {$categoria->nome}: {$totalFaqs} FAQs");
        }
    }
}
