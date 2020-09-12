<?php

namespace App\Models;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];
    

    public static function allProduct()
    {
       $products = app(Pipeline::class)
                ->send(\App\Models\Product::query())
                ->through([
                    \App\QueryFilters\Title::class,
                    \App\QueryFilters\Variant::class,
                    \App\QueryFilters\PriceFrom::class,
                    \App\QueryFilters\PriceTo::class,
                    \App\QueryFilters\Date::class,
                ])
                ->thenReturn()
                ->paginate(5);
        return $products;
    }

    public function product_images()
    {
        return $this->hasMany('App\Models\ProductImage', 'product_id', 'id');
    }

    public function product_variants()
    {
        return $this->hasMany('App\Models\ProductVariant', 'product_id', 'id')->with('variant_m');
    }
    public function variants()
    {
         return $this->belongsToMany('App\Models\Variant', 'product_variants', 'product_id', 'variant_id')->with('product_variants');
    }
    public function product_variant_prices()
    {
        return $this->hasMany('App\Models\ProductVariantPrice', 'product_id', 'id')->with(['product_variant_ones','product_variant_twos','product_variant_threes']);
    }

}
