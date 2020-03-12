<?php

namespace App\Model\Traits;


class ArrayHelper
{
    static function cleanArray($array)
    {
        return array_filter($array, function ($value) {
            return !empty($value);
        });
    }
}