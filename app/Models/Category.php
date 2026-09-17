<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'facebook_page_id',
        'name',
        'slug',
        'icon',
        'color_code',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function facebookPage(): BelongsTo
    {
        return $this->belongsTo(FacebookPage::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeForFacebookPage($query, $pageId)
    {
        if (empty($pageId) || $pageId === 'all') {
            return $query;
        }
        return $query->where('facebook_page_id', $pageId);
    }
}
