<?php

namespace App\QueryFilters;

class Date  extends Filter
{
    protected function applyFilters($builder)
    {
       return $builder->where('created_at', 'LIKE',request($this->filterName()).'%');
    }

}
