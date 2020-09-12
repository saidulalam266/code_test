<?php

namespace App\QueryFilters;

class PriceTo  extends Filter
{
    protected function applyFilters($builder)
    {

       	$price = request($this->filterName());
       	return $builder->whereHas('product_variant_prices', function ($query) use($price) {
       		if ($price) {
       			$query->where('price','<=',$price);
       		}
        });
    }
}
