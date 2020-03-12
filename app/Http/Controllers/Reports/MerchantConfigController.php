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
use App\Reports\ConfigReport\MerchantInfoReport;

class MerchantConfigController extends ReportController
{
    CONST reportFields = ['merchant_id', 'display_name', 'ordering_on', 'delivery', 'advanced_ordering',
                            'group_ordering_on', 'catering_on'];
    CONST headers = ['Merchant ID', 'Store Name', 'Ordering', 'Delivery',
        'Advanced Ordering', 'Group Ordering', 'Catering'];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request) {
        $merchantInfoReport = new MerchantInfoReport($request->all());
        $results = $merchantInfoReport->buildResponse();
        return $results;
    }

    /**
     * Export transactions report
     *2
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function exportReport(Request $request) {
        $merchant_config_report = new MerchantInfoReport($request->all());
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