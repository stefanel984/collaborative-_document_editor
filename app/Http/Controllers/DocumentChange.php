<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'title',
        'owner_id',
    ];

    /**
     * A document belongs to a user (owner)
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * A document has many changes
     */
    public function changes()
    {
        return $this->hasMany(DocumentChange::class);
    }
}
