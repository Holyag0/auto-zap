<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoDemanda extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'cor',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento com demandas
     */
    public function demandas(): HasMany
    {
        return $this->hasMany(Demanda::class, 'tipo_demanda_id');
    }

    /**
     * Scope para tipos ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Retorna a cor com fallback
     */
    public function getCorAttribute($value): string
    {
        return $value ?? '#6366f1';
    }
}
