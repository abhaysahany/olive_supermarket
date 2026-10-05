<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'sub_category_id', 'name', 'slug', 'description', 
        'size', 'short_size', 'price', 'old_price', 'save_pct', 
        'stock', 'image', 'emoji', 'tint', 'tag', 'rating', 
        'reviews_count', 'local'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
