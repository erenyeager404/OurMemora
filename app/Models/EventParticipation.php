<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventParticipation extends Model
{
    protected $fillable = ['event_id', 'photo_id', 'user_id'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}