<?php
// app/Models/Label.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Label extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'color',
        'board_id',
        'user_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the cards that have this label.
     */
    public function cards(): BelongsToMany
    {
        return $this->belongsToMany(Card::class, 'card_label')
                    ->withTimestamps();
    }

    /**
     * Get the board that owns the label.
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * Get the user that owns the label.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar etiquetas por board
     */
    public function scopeForBoard($query, $boardId)
    {
        return $query->where('board_id', $boardId)
                     ->orWhereNull('board_id'); // Etiquetas globais
    }

    /**
     * Scope para filtrar etiquetas do usuário
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId)
                     ->orWhereNull('user_id'); // Etiquetas globais
    }
}
