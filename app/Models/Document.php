<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'owner_id',
    ];

     public function changes()
    {
        return $this->hasMany(DocumentChange::class);
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}