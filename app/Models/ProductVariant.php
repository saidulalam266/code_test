<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
	public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function variant_m()
    {
        return $this->belongsTo('App\Models\Variant', 'variant_id', 'id');
    }

    public function product_variant_ones()
    {
        return $this->hasMany('App\Models\ProductVariantPrice', 'product_variant_one', 'id');
    }
    public function product_variant_twos()
    {
        return $this->hasMany('App\Models\ProductVariantPrice', 'product_variant_two', 'id');
    }
    public function product_variant_threes()
    {
        return $this->hasMany('App\Models\ProductVariantPrice', 'product_variant_three', 'id');
    }
}
