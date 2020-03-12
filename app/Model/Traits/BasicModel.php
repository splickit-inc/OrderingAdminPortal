<?php

namespace App\Model\Traits;

trait BasicModel
{
    /**
     * Clean the data array to fill just the fillable data
     * @param array $data
     * @return array
     */
    public function getValidDataToFill($data)
    {
        return array_intersect_key($data, array_flip($this->getFillable()));
    }
}