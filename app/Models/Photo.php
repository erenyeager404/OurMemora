<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'caption',
        'description',
        'status',
        'views',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function likes(

    ) {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function saves()
    {
        return $this->hasMany(Save::class);
    }


    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function isSavedBy($userId)
    {
        return $this->saves()->where('user_id', $userId)->exists();
    }
}
