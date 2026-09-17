<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacebookPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'name',
        'category',
        'avatar_url',
        'cover_url',
        'access_token',
        'followers_count',
        'phone',
        'email',
        'about',
        'is_active',
        'data_enabled',
        'ai_order_agent_enabled',
    ];

    protected $casts = [
        'followers_count' => 'integer',
        'is_active' => 'boolean',
        'data_enabled' => 'boolean',
        'ai_order_agent_enabled' => 'boolean',
    ];

    public function isDataEnabled(): bool
    {
        return (bool) $this->data_enabled;
    }

    public function isAiAgentEnabled(): bool
    {
        return (bool) $this->ai_order_agent_enabled && $this->isDataEnabled();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'facebook_page_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function openOrders(): HasMany
    {
        return $this->hasMany(Order::class)->whereIn('status', ['pending', 'confirmed', 'processing', 'shipped']);
    }

    public function closedOrders(): HasMany
    {
        return $this->hasMany(Order::class)->whereIn('status', ['success', 'fail']);
    }

    public function successOrders(): HasMany
    {
        return $this->hasMany(Order::class)->where('status', 'success');
    }

    public function failOrders(): HasMany
    {
        return $this->hasMany(Order::class)->where('status', 'fail');
    }

    public function getAvatarAttribute(): string
    {
        return $this->avatar_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1877F2&color=fff';
    }
}
