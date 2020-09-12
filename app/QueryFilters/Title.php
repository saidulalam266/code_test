<?php

namespace App\QueryFilters;

class Title  extends Filter
{
    protected function applyFilters($builder)
    {

       		//dd(request($this->filterName()));
       return $builder->where('title', 'LIKE','%'.request($this->filterName()).'%');
    }

}
