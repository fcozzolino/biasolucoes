<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Board extends Model
{
  use HasFactory;

  protected $fillable = [
    'title',
    'user_id',
    'color',
    'status',
    'last_viewed_at',
    'uuid'
  ];

  protected $casts = [
    'last_viewed_at' => 'datetime',
  ];

  // Gerar UUID automaticamente
  protected static function boot()
  {
    parent::boot();

    static::creating(function ($board) {
      $board->uuid = Str::uuid();
    });
  }

  // Usar UUID como rota padrão
  public function getRouteKeyName()
  {
    return 'uuid';
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function columns()
  {
    return $this->hasMany(Column::class)->orderBy('order');
  }
}
