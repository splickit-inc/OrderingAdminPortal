<?php

namespace App\BusinessModel\Visibility;


interface IVisibilityFactory
{
    static function configure($interface);

    static function setVisibility($visibility, $interface);

    static function getVisibility($visibility);
}