<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentChange extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'document_id',
        'user_id',
        'operation', // JSON field
        'version',
    ];

    /**
     * Cast the operation column as array automatically
     */
    protected $casts = [
        'operation' => 'array',
    ];

    /**
     * A change belongs to a document
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * A change belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}