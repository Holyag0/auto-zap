<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('setor_id')->nullable()->after('email')->constrained('setors')->onDelete('set null');
            $table->foreignId('cargo_id')->nullable()->after('setor_id')->constrained('cargos')->onDelete('set null');
            
            $table->index('setor_id');
            $table->index('cargo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['setor_id']);
            $table->dropForeign(['cargo_id']);
            $table->dropColumn(['setor_id', 'cargo_id']);
        });
    }
};
