<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'facebook_user_id',
        'avatar_url',
        'role',
        'fb_profile_url',
        'bio',
        'is_system_admin',
        'facebook_connected_at',
        'facebook_access_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'facebook_access_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_system_admin' => 'boolean',
            'facebook_connected_at' => 'datetime',
        ];
    }

    public function facebookPages(): BelongsToMany
    {
        return $this->belongsToMany(FacebookPage::class, 'facebook_page_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function isSystemAdmin(): bool
    {
        return (bool) $this->is_system_admin;
    }

    public function isFacebookConnected(): bool
    {
        return !empty($this->facebook_user_id) && $this->facebook_connected_at !== null;
    }

    public function connectFacebook(string $fbId, ?string $profileUrl = null, ?string $token = null): void
    {
        $this->update([
            'facebook_user_id' => $fbId,
            'facebook_connected_at' => now(),
            'fb_profile_url' => $profileUrl ?: ($this->fb_profile_url ?: 'https://facebook.com/' . $fbId),
            'facebook_access_token' => $token ?: $this->facebook_access_token,
        ]);
    }

    public function disconnectFacebook(): void
    {
        $this->update([
            'facebook_user_id' => null,
            'facebook_connected_at' => null,
            'facebook_access_token' => null,
        ]);
    }

    public function getAvatarAttribute(): string
    {
        if (!empty($this->facebook_user_id)) {
            $token = $this->facebook_access_token ? '&access_token=' . $this->facebook_access_token : '';
            return 'https://graph.facebook.com/v20.0/' . $this->facebook_user_id . '/picture?type=large' . $token;
        }

        if (!empty($this->avatar_url)) {
            return $this->avatar_url;
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0ea5e9&color=fff';
    }
}
