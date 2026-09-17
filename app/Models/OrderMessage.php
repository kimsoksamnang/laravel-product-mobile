<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'sender_type',
        'sender_name',
        'sender_avatar',
        'type',
        'message',
        'attachment_url',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isCustomer(): bool
    {
        return $this->sender_type === 'customer';
    }

    public function isPage(): bool
    {
        return $this->sender_type === 'page_agent' || $this->sender_type === 'page';
    }

    public function isSystem(): bool
    {
        return $this->sender_type === 'system';
    }

    public function getAvatarAttribute(): string
    {
        if ($this->sender_avatar) {
            return $this->sender_avatar;
        }

        $bg = match ($this->sender_type) {
            'page_agent', 'page' => '1877F2',
            'system' => '64748B',
            default => '10B981',
        };

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->sender_name) . "&background={$bg}&color=fff";
    }
}
