<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    const TYPE_PRODUCT = 'product';

    const TYPE_SERVICE = 'service';

    protected $fillable = [
        'umkm_id',
        'type',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'is_active',
        'category_id',
        'image',
    ];

    protected $casts = [
        'umkm_id' => 'integer',
        'price' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'category_id' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function isProduct(): bool
    {
        return $this->type === self::TYPE_PRODUCT;
    }

    public function isService(): bool
    {
        return $this->type === self::TYPE_SERVICE;
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }

    public function scopeActive($query)
    {
        return $query->whereRaw('is_active is true');
    }

    public function scopeProducts($query)
    {
        return $query->where('type', self::TYPE_PRODUCT);
    }

    public function scopeServices($query)
    {
        return $query->where('type', self::TYPE_SERVICE);
    }
}
