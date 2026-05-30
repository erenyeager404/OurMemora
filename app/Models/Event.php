<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'created_by',
        'title',
        'description',
        'prize_description',
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

    // Foto peserta diurutkan berdasarkan like terbanyak
    public function rankedPhotos()
    {
        return $this->hasManyThrough(
            Photo::class,
            EventParticipation::class,
            'event_id',
            'id',
            'id',
            'photo_id'
        );
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && now()->between($this->start_date, $this->end_date);
    }

    public function isVoting(): bool
    {
        return $this->status === 'voting';
    }

    public function canSubmit(): bool
    {
        return $this->status === 'active'
            && now()->between($this->start_date, $this->end_date);
    }

    public function daysRemaining(): int
    {
        return max(0, (int) now()->diffInDays($this->end_date, false));
    }

    public function hoursRemaining(): int
    {
        return max(0, (int) now()->diffInHours($this->end_date, false));
    }

    public function getPosterUrlAttribute(): string
    {
        return $this->poster_path
            ? asset('storage/' . $this->poster_path)
            : '';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'active' => 'Berlangsung',
            'voting' => 'Penilaian',
            'ended' => 'Selesai',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'voting' => 'yellow',
            'ended' => 'gray',
            default => 'slate',
        };
    }

    // Leaderboard: foto peserta diranking berdasarkan like
    public function getLeaderboard(int $limit = 20)
    {
        return Photo::whereIn(
            'id',
            $this->participations()->pluck('photo_id')
        )
            ->withCount('likes')
            ->with(['files', 'user'])
            ->orderBy('likes_count', 'desc')
            ->take($limit)
            ->get();
    }

    // Cek apakah user sudah ikut event
    public function hasUserJoined(int $userId): bool
    {
        return $this->participations()
            ->where('user_id', $userId)
            ->exists();
    }
}