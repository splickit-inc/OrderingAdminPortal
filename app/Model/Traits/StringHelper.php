<?php

namespace App\Model\Traits;


trait StringHelper
{

    function phone_number_format($number)
    {
        // get number length.
        $length = strlen($number);

        // if number = 10
        if ($length == 11) {
            $number = preg_replace("/(\d{1})(\d{3})(\d{3})(\d{4})/", "$1-$2-$3-$4", $number);
        } else {
            $number = preg_replace("/(\d{3})(\d{3})(\d{4})/", "$1-$2-$3", $number);
        }

        return $number;
    }
}