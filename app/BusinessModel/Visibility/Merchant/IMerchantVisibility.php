<?php
namespace App\BusinessModel\Visibility\Merchant;

use Illuminate\Database\Eloquent\Builder;

interface IMerchantVisibility {
    /**
     * @var array $fields
     * @var string $search_value
     * @return Builder
     */
    function searchMerchantsByMenu(array $fields, $search_value);
    /**
     * @var array $fields
     * @var string $search_value
     * @return Builder
     */
    function searchMerchants(array $fields, $search_value);

    /**
     * @return Builder
     */
    function getMerchantsByMenu();

    /**
     * @return Builder
     */
    function getMerchants();
}