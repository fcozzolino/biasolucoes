<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Card extends Model
{
  use HasFactory;

  protected $fillable = [
    'column_id',
    'title',
    'description',
    'order',
    'full_description',
    'link',
    'color',
    'user_id',
    'status',
    'start_date',
    'due_date',
    'reminder_interval'
  ];

  public function column()
  {
    return $this->belongsTo(Column::class);
  }

  public function attachments()
  {
    return $this->hasMany(Attachment::class);
  }
  public function user()
  {
    return $this->belongsTo(User::class);
  }
  public function comments()
  {
    return $this->hasMany(Comment::class)->latest();
  }


  public function labels(): BelongsToMany
  {
    return $this->belongsToMany(Label::class, 'card_label')
      ->withTimestamps();
  }

  public function syncLabels(array $labelIds)
  {
    return $this->labels()->sync($labelIds);
  }
  public function attachLabel($labelId)
  {
    if (!$this->labels()->where('label_id', $labelId)->exists()) {
      $this->labels()->attach($labelId);
    }
  }
  public function detachLabel($labelId)
  {
    $this->labels()->detach($labelId);
  }
}
