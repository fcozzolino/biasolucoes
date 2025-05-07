<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Column extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'order', 'board_id', 'color'];


    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class)->orderBy('order');
    }
}
