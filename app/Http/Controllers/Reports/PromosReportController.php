<?php

namespace App\Http\Controllers\Reports;

use App\Service\ReportExportationService;
use App\Service\ReportService;
use App\Reports\PromosReport;

use Illuminate\Http\Request;

class PromosReportController extends ReportController {
    CONST reportFields = ['order_cnt', 'order_amt', 'promo_amt', 'delivery_amt'];
    CONST headers = ['Order Count', 'Order Amount', 'Promo Amount', 'Delivery Amount'];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request) {
        $promoReport = new PromosReport($request->all());
        $q = \DB::connection('reports_db')->enableQueryLog();
        $response = $promoReport->buildResponse();
        $q = \DB::connection('reports_db')->getQueryLog();
        return $response;
    }

    /**
     * Export transactions report
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function exportReport(Request $request) {
        $customer_report = new PromosReport($request->all());
        $customer_report->buildQuery();

        $expService = new ReportExportationService();
        $selectedFields = self::reportFields;

        $headers = self::headers;

        if ($customer_report->request['group_by']) {
            $group_bys = explode(',',$customer_report->request['group_by']);
            foreach ($group_bys as $group_by) {
                $groupByHeader = PromosReport::groupByHeaders[$group_by];
                array_push($headers, $groupByHeader);
                array_push($selectedFields, $group_by);
            }
        }
        $res = $expService->exportToCSV($headers, $selectedFields, $customer_report->query);
        if ($res === false) {
            return $this->errorResponse($expService->errors(), 422);
        }
        return response()->download($res)->deleteFileAfterSend(true);
    }
}