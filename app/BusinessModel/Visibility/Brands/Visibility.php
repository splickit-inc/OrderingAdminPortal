<?php
/**
 * Created by PhpStorm.
 * User: pablo.daza
 * Date: 5/3/18
 * Time: 2:53 PM
 */

namespace App\BusinessModel\Visibility\Brands;


use App\Model\Brand;
use App\Model\Filters\FilterApplier;
use Illuminate\Database\Eloquent\Builder;

abstract class Visibility implements IVisibility {

    /**
     * @var Brand
     */
    protected $model;
    /**
     * @var FilterApplier
     */
    protected $filterApplier;

    public function __construct(Brand $model, FilterApplier $filterApplier) {
        $this->model = $model;
        $this->filterApplier = $filterApplier;
    }

    function searchRecords(array $fields, $search_value) {
        return $this->filterApplier->searchWithValue(
            $fields,
            $search_value,
            $this->getAllRecords()
        );
    }

    function getByFirstLetter($letter) {
        return $this->filterApplier->filterByFirstLetter(
            'brand_name',
            $letter,
            $this->getAllRecords()
        );
    }
}