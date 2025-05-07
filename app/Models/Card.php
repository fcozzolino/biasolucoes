<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'status' // <- adicionado
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

}
