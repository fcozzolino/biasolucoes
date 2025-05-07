<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'card_id',
        'filename',
        'path',
        'mime_type',
    ];

    // Relacionamento com o card (muitos para um)
    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    // Acessor para retornar a URL pública do anexo
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    // Define os atributos adicionais que serão visíveis na resposta JSON
    protected $appends = ['url'];
}
