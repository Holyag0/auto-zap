<?php

namespace Database\Seeders;

use App\Models\Cargo;
use App\Models\Setor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Busca os setores e cria um cargo genérico
        $cargo = Cargo::firstOrCreate(
            ['nome' => 'Atendente'],
            [
                'descricao' => 'Atendente responsável por demandas',
                'ativo' => true,
            ]
        );

        $usuarios = [
            [
                'name' => 'Maria Silva',
                'email' => 'maria.silva@cabemce.com',
                'setor' => 'DIAF',
            ],
            [
                'name' => 'João Santos',
                'email' => 'joao.santos@cabemce.com',
                'setor' => 'TESOURARIA',
            ],
            [
                'name' => 'Ana Costa',
                'email' => 'ana.costa@cabemce.com',
                'setor' => 'JURÍDICO',
            ],
            [
                'name' => 'Pedro Lima',
                'email' => 'pedro.lima@cabemce.com',
                'setor' => 'PECÚLIO',
            ],
            [
                'name' => 'Carlos Mendes',
                'email' => 'carlos.mendes@cabemce.com',
                'setor' => 'TI',
            ],
            [
                'name' => 'Juliana Oliveira',
                'email' => 'juliana.oliveira@cabemce.com',
                'setor' => 'PLANO DE SAÚDE',
            ],
        ];

        foreach ($usuarios as $userData) {
            $setor = Setor::where('nome', $userData['setor'])->first();

            if ($setor) {
                User::updateOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'password' => Hash::make('senha123'),
                        'setor_id' => $setor->id,
                        'cargo_id' => $cargo->id,
                    ]
                );

                $this->command->info("✅ Usuário criado: {$userData['name']} - {$userData['setor']}");
            }
        }

        $this->command->info('');
        $this->command->info('📊 Resumo:');
        $this->command->info('Total de usuários: ' . User::count());
        $this->command->info('Usuários com setor: ' . User::whereNotNull('setor_id')->count());
        $this->command->info('');
        $this->command->info('🔑 Senha padrão para todos: senha123');
    }
}
