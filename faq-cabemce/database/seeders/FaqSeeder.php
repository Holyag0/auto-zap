<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpa a tabela antes de popular
        Faq::truncate();

        $csvFile = database_path('seeders/faq_seed.csv');

        if (!file_exists($csvFile)) {
            $this->command->error("Arquivo CSV não encontrado: {$csvFile}");
            return;
        }

        $file = fopen($csvFile, 'r');
        
        // Pula o cabeçalho
        $header = fgetcsv($file);

        $count = 0;
        while (($data = fgetcsv($file)) !== false) {
            if (count($data) < 4) {
                continue; // Pula linhas incompletas
            }

            Faq::create([
                'pergunta' => $data[0],
                'resposta' => $data[1],
                'categoria' => $data[2],
                'ativo' => strtolower($data[3]) === 'sim',
            ]);

            $count++;
        }

        fclose($file);

        $this->command->info("✅ {$count} FAQs importadas com sucesso!");
    }
}
