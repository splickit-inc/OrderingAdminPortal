<?php

namespace App\BusinessModel\Reports;


use App\Service\ReportExportationService;
use App\Service\ReportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Reports\CustomerReport;
use App\Reports\MenuItemSalesReport;
use App\Reports\TransactionReport;
use App\Reports\AllOrdersReport;

class ReportJob implements IReportJob
{
    /** @var Collection $reportData */
    protected $reportData;
    protected $reportService;
    protected $cvsCreator;

    public function __construct(ReportExportationService $exportationService, Collection $reportData = null)
    {
        $this->reportData = $reportData;
        //$this->reportService = $reportService;
        $this->cvsCreator = $exportationService;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }

    /**
     * @param $type
     * @param $dateRange
     * @param $brand_id
     * @param $selected_merchant
     * @return Builder|null
     */
    function getBasicQuery($type, $dateRange, $brand_id, $selected_merchant)
    {
        try {
            switch ($type) {
                case 'transactions':
                    if (!empty($brand_id)) {
                        return $this->reportService->transactionsReport($dateRange)
                            ->where('Merchant.brand_id', $brand_id);
                    }
                    return $this->reportService->transactionsReport($dateRange)
                        ->whereIn('Merchant.merchant_id', $selected_merchant);
                    break;
                case 'customers':
                    if (!empty($brand_id)) {
                        return $this->reportService->customersReport($dateRange);
                        //->where('Merchant.brand_id', $brand_id);
                    }
                    return $this->reportService->customersReport($dateRange);
                    //->whereIn('Merchant.merchant_id', $selected_merchant);
                    break;
            }
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    function getReportCVS()
    {
        try {

            if ($this->reportData['report_type'] == 'transactions') {
                //$this->reportData['date_range'] = $this->reportData['period_start_date'] . ',' . $this->reportData['period_end_date'];
                $transactionReport = new TransactionReport($this->reportData);
                $file = $transactionReport->getReportCsv();
                return $file;
            }
            elseif ($this->reportData['report_type'] == 'customers') {
                //$this->reportData['date_range'] = $this->reportData['period_start_date'] . ',' . $this->reportData['period_end_date'];
                $customerReport = new CustomerReport($this->reportData);
                $file = $customerReport->getReportCsv();
                return $file;
            }
            elseif ($this->reportData['report_type'] == 'sales_by_menu_items') {
                //$this->reportData['date_range'] = $this->reportData['period_start_date'] . ',' . $this->reportData['period_end_date'];
                $menuItemsSalesReport = new MenuItemSalesReport($this->reportData);
                $file = $menuItemsSalesReport->getReportCsv();
                return $file;
            }
            elseif ($this->reportData['report_type'] == 'all_orders') {
                //$this->reportData['date_range'] = $this->reportData['period_start_date'] . ',' . $this->reportData['period_end_date'];
                $allOrdersReport = new AllOrdersReport($this->reportData);
                $file = $allOrdersReport->getReportCsv();
                return $file;
            }


//            $dateRange = $this->reportData->get('period_start_date') . ',' . $this->reportData->get('period_end_date');
//            $query = $this->getBasicQuery($this->reportData->get('report_type'), $dateRange, $this->reportData->get('brand_id'), $this->reportData->get('selected_merchants'));
//
//            if ($this->reportData->get('report_type') == 'customers') {
//                $groupBy = !empty($this->reportData->get('selected_group_by')) ? $this->reportData->get('selected_group_by') : 'o.user_id';
//                $orderBy = !empty($this->reportData->get('selected_order_by')) ? $this->reportData->get('selected_order_by') : 'o.user_id';
//                $query = $query->groupBy($groupBy);
//                $query = $query->orderBy($orderBy);
//
//                if (!empty($query)) {
//
//                    $file = $this->cvsCreator->exportToCSV([
//                        'User ID', 'First Name', 'Last Name', 'Email',
//                        '1st Order Date', 'Last Order Date', 'Total Orders', 'Pickup Orders', 'Delivery Orders',
//                        'Order Spent', 'Total Spent', 'Average Order Value'
//                    ], [
//                        'user_id', 'first_name', 'last_name', 'email',
//                        'first_order_date', 'last_order_date', 'total_orders', 'pickup_orders', 'delivery_orders',
//                        'order_spent', 'total_spent', 'avg_order_value'
//                    ], $query);
//
//                    return $file;
//                }
//            }
//
//            if ($this->reportData->get('report_type') == 'transactions') {
//                $groupBy = !empty($this->reportData->get('selected_group_by')) ? $this->reportData->get('selected_group_by') : 'order_id';
//                $orderBy = !empty($this->reportData->get('selected_order_by')) ? $this->reportData->get('selected_order_by') : 'order_id';
//                $query = $query->groupBy($groupBy);
//                $query = $query->orderBy($orderBy);
//
//                if (!empty($query)) {
//
//                    $file = $this->cvsCreator->exportToCSV([
//                        'Merchant ID',
//                        'Store Name',
//                        'Total Orders',
//                        'Pickup Orders',
//                        'Delivery Orders',
//                        'Order Amount',
//                        'Tip',
//                        'Tax',
//                        'Discount',
//                        'Delivery Fee',
//                        'Grand Total',
//                        'Average Order Value',
//                    ], [
//                        'merchant_id',
//                        'store_name',
//                        'total_orders',
//                        'pickup_orders',
//                        'delivery_orders',
//                        'order_amount',
//                        'tip',
//                        'tax',
//                        'discount',
//                        'delivery_fee',
//                        'grand_total',
//                        'avg_order_value'
//                    ], $query);
//                    /*
//                    $csv = $this->cvsCreator->build($result, [
//                        'merchant_id' => 'Merchant ID',
//                        'store_name' => 'Store Name',
//                        'total_orders' => 'Total Orders',
//                        'pickup_orders' => 'Pickup Orders',
//                        'delivery_orders' => 'Delivery Orders',
//                        'order_amount' => 'Order Amount',
//                        'tip' => 'Tip',
//                        'tax' => 'Tax',
//                        'discount' => 'Discount',
//                        'delivery_fee' => 'Delivery Fee',
//                        'grand_total' => 'Grand Total',
//                        'avg_order_value' => 'Average Order Value',
//                    ])->getCsv();*/
//                    return $file;
//                }


            return '';
        } catch (\Exception $exception) {
            throw $exception;
        }

    }
}