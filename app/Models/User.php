<?php
namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'google_id',
        'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_admin' => 'boolean',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];
    //Tamplate email
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    // ── Relasi ──────────────────────────────────
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    public function saves()
    {
        return $this->hasMany(Save::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function followers()
    {
        return $this->hasMany(Follow::class, 'following_id');
        // siapa yang follow user ini
    }

    public function following()
    {
        return $this->hasMany(Follow::class, 'follower_id');
        // siapa yang di-follow user ini
    }

    // ── Helpers ─────────────────────────────────
    public function isFollowing(int $userId): bool
    {
        return $this->following()->where('following_id', $userId)->exists();
    }

    // Avatar: foto profil atau inisial nama
    public function getAvatarUrlAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name)
            . '&background=7C3AED&color=fff&size=64';
    }
}