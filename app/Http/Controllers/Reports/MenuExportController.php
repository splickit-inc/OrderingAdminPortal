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

class MenuExportController extends ReportController
{
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
        $menu_item_sales_report = new MenuItemSalesReport($request->all());
        $menu_item_sales_report->buildQuery();

        $expService = new ReportExportationService();
        $selectedFields = self::reportFields;
        $headers = self::headers;

        if (isset($menu_item_sales_report->request['group_by'])) {
            if ($menu_item_sales_report->request['group_by']) {
                $groupByHeader = self::groupByHeaders[implode(',',$menu_item_sales_report->request['group_by'])];
                array_push($headers, $groupByHeader);
                array_push($selectedFields, $this->request['group_by']);
            }
        }

        $res = $expService->exportToCSV($headers, $selectedFields, $menu_item_sales_report->query);
        if ($res === false) {
            return $this->errorResponse($expService->errors(), 422);
        }
        return response()->download($res)->deleteFileAfterSend(true);
    }
}