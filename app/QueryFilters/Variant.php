<?php

namespace App\QueryFilters;

class Variant  extends Filter
{
    protected function applyFilters($builder)
    {
       	$variant = request($this->filterName());
       	return $builder->whereHas('product_variants', function ($query) use($variant) {
       		if ($variant) {
       		$query->where('variant', $variant);
       		}
        });
    }
}
