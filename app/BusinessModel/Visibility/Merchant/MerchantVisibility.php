<?php
namespace App\BusinessModel\Visibility\Merchant;

use App\Model\Filters\FilterApplier;
use App\Model\Merchant;

abstract class MerchantVisibility implements IMerchantVisibility {
    /**
     * @var Merchant
     */
    protected $model;
    /**
     * @var FilterApplier
     */
    protected $filterApplier;

    public function __construct(Merchant $model, FilterApplier $filterApplier) {
        $this->model = $model;
        $this->filterApplier = $filterApplier;
    }

    function searchMerchants(array $fields, $search_value) {
        return $this->filterApplier->searchWithValue(
            $fields,
            $search_value,
            $this->getMerchants());
    }

    function searchMerchantsByMenu(array $fields, $search_value) {
        return $this->filterApplier->searchWithValue(
            $fields,
            $search_value,
            $this->getMerchantsByMenu());
    }
}