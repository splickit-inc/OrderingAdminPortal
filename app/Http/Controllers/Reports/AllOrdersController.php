<?php

namespace App\Http\Controllers\Reports;


use App\Http\Controllers\Controller;
use App\Model\AggregateMenuType;
use App\Service\ReportExportationService;
use App\Model\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laracsv\Export;
use Illuminate\Database\Eloquent\Collection;
use App\Reports\AllOrdersReport;

class AllOrdersController extends ReportController
{
    CONST reportFields = ['order_id', 'order_date', 'order_amt', 'total_tax_amt', 'promo_amt', 'delivery_amt',
                            'tip_amt', 'grand_total', 'order_qty', 'user_name'];
    CONST headers = ['Order ID', 'Order Date', 'Order Amt', 'Total Tax', 'Promo Amt', 'Delivery Amt', 'Tip Amt',
                    'Grand Total', 'Order Qty', 'User Name'];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request) {
        $allOrdersReport = new AllOrdersReport($request->all());
        $results = $allOrdersReport->buildResponse();
        return $results;
    }

    /**
     * Export transactions report
     *2
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function exportReport(Request $request) {
        $merchant_config_report = new AllOrdersReport($request->all());
        $merchant_config_report->buildQuery();

        $expService = new ReportExportationService();
        $selectedFields = self::reportFields;
        $headers = self::headers;

        $res = $expService->exportToCSV($headers, $selectedFields, $merchant_config_report->query);
        if ($res === false) {
            return $this->errorResponse($expService->errors(), 422);
        }
        return response()->download($res)->deleteFileAfterSend(true);
    }
}