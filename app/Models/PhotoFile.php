<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoFile extends Model
{
    protected $fillable = ['photo_id', 'file_path', 'thumb_path', 'order'];

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // URL gambar penuh (untuk detail/download)
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    // URL thumbnail (untuk dashboard, cepat)
    public function getThumbUrlAttribute(): string
    {
        return $this->thumb_path
            ? asset('storage/' . $this->thumb_path)
            : $this->url;
    }
}