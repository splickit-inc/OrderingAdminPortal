<?php
/**
 * Created by PhpStorm.
 * User: diego.rodriguez
 * Date: 12/11/17
 * Time: 2:48 PM
 */

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

abstract class ReportController extends Controller {

    CONST groupByHeaders = ['order_date' => 'Date',
        'order_hour' => 'Hour of Day', 'order_day_of_week' => 'Day of Week',
        'order_month' => 'Month', 'order_year' => 'Year', 'order_type' => 'Order Type',
        'payment_type' => 'Payment Type', 'order_id' => null, 'o.user_id' => null, 'merchant_id'=>'Merchant Id', 'promo_code'=>'Promo Code',
        'merchant_id'=>'Merchant ID'];

    protected function getOrderBy($orderBy) {
        $orderBy = trim($orderBy);
        $parts = explode('+', $orderBy);
        if (count($parts) == 1) {
            $parts = explode('-', $orderBy);
            if (count($parts) == 2) {
                return [$parts[1], 'desc'];
            }
            return [$orderBy, 'asc'];
        }
        return [$parts[1], 'asc'];
    }

}