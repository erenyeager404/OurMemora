<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'poster_path',
        'start_date',
        'end_date',
        'max_winners',
        'rules',
        'auto_tag',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participations()
    {
        return $this->hasMany(EventParticipation::class);
    }

    public function photos()
    {
        return $this->belongsToMany(Photo::class, 'event_participations')
            ->withCount('likes')
            ->orderByDesc('likes_count');
        // Leaderboard: foto diurutkan berdasarkan jumlah like
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && now()->between($this->start_date, $this->end_date);
    }

    public function daysRemaining(): int
    {
        return max(0, (int) now()->diffInDays($this->end_date, false));
    }

    public function getPosterUrlAttribute(): string
    {
        return $this->poster_path
            ? asset('storage/' . $this->poster_path)
            : '';
    }
}