<?php

namespace App\BusinessModel\Visibility\Brands;


use Illuminate\Database\Eloquent\Builder;

interface IVisibility {
    /**
     * @return Builder
     */
    function getAllRecords();

    /**
     * @var array $fields
     * @var string $search_value
     * @return Builder
     */
    function searchRecords(array $fields, $search_value);

    /**
     * @var string $letter
     * @return Builder
     */
    function getByFirstLetter($letter);
}