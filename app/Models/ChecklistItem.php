<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    protected $fillable = ['checklist_id', 'content', 'is_completed', 'position', 'completed_at'];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime'
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }

    public function toggleComplete(): void
    {
        $this->update([
            'is_completed' => !$this->is_completed,
            'completed_at' => !$this->is_completed ? now() : null
        ]);
    }
}
