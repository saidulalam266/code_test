<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    protected $guarded = [];
	public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function product_variant_ones()
    {
        return $this->belongsTo('App\Models\ProductVariant', 'product_variant_one', 'id');
    }
    public function product_variant_twos()
    {
        return $this->belongsTo('App\Models\ProductVariant', 'product_variant_two', 'id');
    }
    public function product_variant_threes()
    {
        return $this->belongsTo('App\Models\ProductVariant', 'product_variant_three', 'id');
    }
}
