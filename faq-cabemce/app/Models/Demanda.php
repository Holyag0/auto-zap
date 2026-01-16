<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Demanda extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocolo',
        'setor_id',
        'tipo_demanda_id',
        'responsavel_id',
        'descricao',
        'situacao',
        'arquivos',
        'data_conclusao',
        'observacoes',
    ];

    protected $casts = [
        'arquivos' => 'array',
        'data_conclusao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the full URLs for the attached files
     */
    public function getArquivosUrlsAttribute(): array
    {
        if (!$this->arquivos) {
            return [];
        }

        return array_map(function ($arquivo) {
            return asset('storage/' . $arquivo);
        }, $this->arquivos);
    }

    /**
     * Boot do model para gerar protocolo automático
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($demanda) {
            if (empty($demanda->protocolo)) {
                $demanda->protocolo = self::gerarProtocolo();
            }
        });
    }

    /**
     * Gera protocolo único
     */
    public static function gerarProtocolo(): string
    {
        do {
            $protocolo = date('ymd') . random_int(100000, 999999);
        } while (self::where('protocolo', $protocolo)->exists());

        return $protocolo;
    }

    /**
     * Relacionamento com setor
     */
    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }

    /**
     * Relacionamento com tipo de demanda
     */
    public function tipoDemanda(): BelongsTo
    {
        return $this->belongsTo(TipoDemanda::class);
    }

    /**
     * Relacionamento com responsável
     */
    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    /**
     * Scopes
     */
    public function scopeEmAnalise($query)
    {
        return $query->where('situacao', 'em_analise');
    }

    public function scopeEmAndamento($query)
    {
        return $query->where('situacao', 'em_andamento');
    }

    public function scopeConcluidas($query)
    {
        return $query->where('situacao', 'concluida');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('situacao', 'cancelada');
    }

    /**
     * Labels para situações
     */
    public static function getSituacoesLabels(): array
    {
        return [
            'em_analise' => 'Em Análise',
            'em_andamento' => 'Em Andamento',
            'concluida' => 'Concluída',
            'cancelada' => 'Cancelada',
        ];
    }

    /**
     * Retorna o label da situação
     */
    public function getSituacaoLabelAttribute(): string
    {
        return self::getSituacoesLabels()[$this->situacao] ?? $this->situacao;
    }
}
