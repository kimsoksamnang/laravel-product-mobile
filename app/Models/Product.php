<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'description',
        'price',
        'cost_price',
        'stock_quantity',
        'low_stock_threshold',
        'image_path',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    protected $appends = [
        'stock_status',
        'stock_badge_label',
        'image_url',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'SKU-' . strtoupper(Str::random(6));
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        }
        if ($this->stock_quantity <= $this->low_stock_threshold) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function getStockBadgeLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            default => 'In Stock',
        };
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }
        if ($this->image_path && (Str::startsWith($this->image_path, 'http://') || Str::startsWith($this->image_path, 'https://'))) {
            return $this->image_path;
        }
        // Fallback default SVG placeholder
        return '/images/placeholder.svg';
    }

    public function getProfitMarginAttribute(): ?float
    {
        if (!$this->cost_price || $this->price <= 0) {
            return null;
        }
        return round((($this->price - $this->cost_price) / $this->price) * 100, 1);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    public function scopeStockStatus($query, ?string $status)
    {
        return match ($status) {
            'in_stock' => $query->whereColumn('stock_quantity', '>', 'low_stock_threshold'),
            'low_stock' => $query->where('stock_quantity', '>', 0)
                                 ->whereColumn('stock_quantity', '<=', 'low_stock_threshold'),
            'out_of_stock' => $query->where('stock_quantity', '<=', 0),
            default => $query,
        };
    }

    public function scopeCategoryFilter($query, $categoryId)
    {
        if (empty($categoryId) || $categoryId === 'all') {
            return $query;
        }
        return $query->where('category_id', $categoryId);
    }
}
