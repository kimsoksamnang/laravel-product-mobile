<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'facebook_page_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_avatar',
        'customer_fb_id',
        'source',
        'status',
        'payment_status',
        'subtotal',
        'shipping_fee',
        'discount_amount',
        'total_amount',
        'notes',
        'fail_reason',
        'closed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'closed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'FB-' . strtoupper(Str::random(8));
            }
            if (empty($order->customer_avatar)) {
                $order->customer_avatar = 'https://ui-avatars.com/api/?name=' . urlencode($order->customer_name) . '&background=random&color=fff';
            }
        });
    }

    public function facebookPage(): BelongsTo
    {
        return $this->belongsTo(FacebookPage::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(OrderMessage::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(OrderMessage::class)->latestOfMany();
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'processing', 'shipped']);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['success', 'fail']);
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFail($query)
    {
        return $query->where('status', 'fail');
    }

    public function scopeForFacebookPage($query, $pageId)
    {
        if (empty($pageId) || $pageId === 'all') {
            return $query;
        }
        return $query->where('facebook_page_id', $pageId);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) {
            return $query;
        }
        return $query->where(function ($q) use ($search) {
            $q->where('order_number', 'like', "%{$search}%")
              ->orWhere('customer_name', 'like', "%{$search}%")
              ->orWhere('customer_phone', 'like', "%{$search}%");
        });
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['pending', 'confirmed', 'processing', 'shipped']);
    }

    public function isClosed(): bool
    {
        return in_array($this->status, ['success', 'fail']);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending Review',
            'confirmed' => 'Confirmed',
            'processing' => 'Packing Order',
            'shipped' => 'Shipped / In Transit',
            'success' => 'Delivered (Success)',
            'fail' => 'Cancelled / Failed',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
            'confirmed' => 'bg-blue-100 text-blue-800 border-blue-200',
            'processing' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'shipped' => 'bg-purple-100 text-purple-800 border-purple-200',
            'success' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'fail' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }

    public function getSourceBadgeAttribute(): array
    {
        return match ($this->source) {
            'messenger' => [
                'label' => 'Messenger',
                'bg' => 'bg-sky-50 text-sky-700 border-sky-200',
                'icon' => 'messenger',
            ],
            'comment' => [
                'label' => 'FB Comment',
                'bg' => 'bg-blue-50 text-blue-700 border-blue-200',
                'icon' => 'comment',
            ],
            'livestream' => [
                'label' => 'Live Stream',
                'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
                'icon' => 'video',
            ],
            default => [
                'label' => 'Manual',
                'bg' => 'bg-slate-50 text-slate-700 border-slate-200',
                'icon' => 'user',
            ],
        };
    }

    public function recalculateTotals(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->total_amount = max(0, ($this->subtotal + $this->shipping_fee) - $this->discount_amount);
        $this->save();
    }
}
