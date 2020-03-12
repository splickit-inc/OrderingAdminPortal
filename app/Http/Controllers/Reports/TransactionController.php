<?php

namespace App\Http\Controllers\Reports;

use App\Service\ReportExportationService;
use App\Service\ReportService;
use App\Reports\TransactionReport;
use App\Reports\TransactionNoGroupByReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends ReportController {


    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request) {
//        $post = $request->all();
        $transaction_report = new TransactionReport($request->all());
//        if (isset($post['group_by'])) {
//            $transaction_report = new TransactionReport($request->all());
//        }
//        else {
//            $transaction_report = new TransactionNoGroupByReport($request->all());
//        }
        $response = $transaction_report->buildResponse();
        return $response;
    }

    /**
     * Export transactions report
     *2
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function exportReport(Request $request) {
        $transaction_report = new TransactionReport($request->all());
        $transaction_report->buildQuery();

//        $group_by = $transaction_report->request['group_by'];

        $expService = new ReportExportationService();
        $selectedFields = TransactionReport::reportFields;


        $headers = TransactionReport::headers;

        if ($transaction_report->request['group_by']) {
            $group_bys = explode(',',$transaction_report->request['group_by']);
            foreach ($group_bys as $group_by) {
                $groupByHeader = TransactionReport::groupByHeaders[$group_by];
                array_push($headers, $groupByHeader);
                array_push($selectedFields, $group_by);
            }
        }

        $res = $expService->exportToCSV($headers, $selectedFields, $transaction_report->query);
        if ($res === false) {
            return $this->errorResponse($expService->errors(), 422);
        }
        return response()->download($res)->deleteFileAfterSend(true);
    }
}