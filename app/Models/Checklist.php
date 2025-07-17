<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checklist extends Model
{
    protected $fillable = ['card_id', 'title', 'position'];

    protected $appends = ['progress', 'completed_count', 'total_count'];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('position');
    }

    public function getProgressAttribute(): int
    {
        $total = $this->items()->count();
        if ($total === 0) return 0;

        $completed = $this->items()->where('is_completed', true)->count();
        return round(($completed / $total) * 100);
    }

    public function getCompletedCountAttribute(): int
    {
        return $this->items()->where('is_completed', true)->count();
    }

    public function getTotalCountAttribute(): int
    {
        return $this->items()->count();
    }
}
