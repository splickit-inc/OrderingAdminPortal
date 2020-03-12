<?php

namespace App\Model\Filters;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class FilterApplier {
    /**
     * @param array $requestArray Array with the expected filter in the key and the search
     *                            field in the value
     * @param Builder $query    Eloquent basic Query
     * @return Builder
     */
    public function applyFiltersToQuery(array $requestArray, Builder $query)
    {
        foreach ($requestArray as $filterName => $value){
            $filter = $this->getAvailableFilter($filterName);
            if($this->isValidClass($filter))
            {
                $applier = App::make($filter);
                $query = $applier->applyAnd($query, $value);
            }
        }
        return $query;
    }

    /**
     * Search on the basic query applying the search fields with the value
     * @param array   $fields
     * @param         $value
     * @param Builder $query
     * @return Builder
     */
    public function searchWithValue(array $fields, $value, Builder $query)
    {
        $query->where(function($query) use ($value, $fields)
        {
            foreach ($fields as $field)
            {
                $filter = $this->getAvailableFilter($field);
                if($this->isValidClass($filter))
                {
                    $applier = App::make($filter);
                    $query = $applier->applyOr($query, $value);
                }
            }
        });
        return $query;
    }

    private function getAvailableFilter($name)
    {
        return __NAMESPACE__ .'\\'. studly_case($name);
    }

    private function isValidClass($className)
    {
        return class_exists($className);
    }

    public function filterByFirstLetter($column_name, $search_value, Builder $query)
    {
        $value['column_name'] = $column_name;
        $value['value'] = $search_value;
        /** @var IFilter $filter */
        $filter = App::make(ModelByFirstLetter::class);
        return $filter->applyAnd($query, $value);
    }
}