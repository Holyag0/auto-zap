<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Categoria extends Model
{
    use HasFactory;

    /**
     * Conexão do banco de dados
     */
    protected $connection = 'pgsql_chatwoot';

    /**
     * Nome da tabela
     */
    protected $table = 'categorias';

    /**
     * Atributos que podem ser preenchidos em massa
     */
    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'cor',
        'icone',
        'ordem',
        'ativo',
    ];

    /**
     * Atributos que devem ser convertidos
     */
    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer',
    ];

    /**
     * Eventos do modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Gera slug automaticamente ao criar/atualizar
        static::saving(function ($categoria) {
            if (empty($categoria->slug)) {
                $categoria->slug = Str::slug($categoria->nome);
            }
        });
    }

    /**
     * Relacionamento com FAQs
     */
    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class, 'categoria_id');
    }

    /**
     * Scope para categorias ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para ordenar por ordem
     */
    public function scopeOrdenadas($query)
    {
        return $query->orderBy('ordem')->orderBy('nome');
    }

    /**
     * Conta o número de FAQs ativas
     */
    public function faqsAtivasCount(): int
    {
        return $this->faqs()->where('ativo', true)->count();
    }

    /**
     * Retorna a cor com fallback
     */
    public function getCorAttribute($value): string
    {
        return $value ?? '#6366f1'; // Cor padrão (indigo)
    }

    /**
     * Retorna o ícone com fallback
     */
    public function getIconeAttribute($value): string
    {
        return $value ?? 'heroicon-o-folder';
    }
}
