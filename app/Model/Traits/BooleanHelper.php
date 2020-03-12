<?php

namespace App\Model\Traits;


trait BooleanHelper
{
    /**
     * @param $value
     * @return bool | $value
     */
    private function castYNBoolean($value)
    {
        switch ($value) {
            case 'Y':
                return true;
                break;
            case 'N':
                return false;
                break;
            case true:
                return 'Y';
                break;
            case false:
                return 'N';
                break;
        }
        return $value;
    }
}