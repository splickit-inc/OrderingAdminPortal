<?php

namespace App\Http\Controllers\Reports;

use App\Service\ReportExportationService;
use App\Service\ReportService;
use App\Reports\CustomerReport;

use Illuminate\Http\Request;

class CustomerController extends ReportController {
    CONST reportFields = ['user_id', 'first_name', 'last_name', 'email',
        'total_orders', 'pickup_orders', 'delivery_orders',
        'total_spent', 'avg_order_value'];
    CONST headers = ['User ID', 'First Name', 'Last Name', 'Email',
        'Total Orders', 'Pickup Orders', 'Delivery Orders',
        'Total Spent', 'Average Order Value'];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request) {
        $customer_report = new CustomerReport($request->all());
        return $customer_report->buildResponse();
    }

    /**
     * Export transactions report
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function exportReport(Request $request) {
        $customer_report = new CustomerReport($request->all());
        $customer_report->buildQuery();

        $expService = new ReportExportationService();
        $selectedFields = self::reportFields;

        $headers = self::headers;

        if (isset($customer_report->request['group_by'])) {
            if ($customer_report->request['group_by']) {
                $groupByHeader = self::groupByHeaders[implode(',',$customer_report->request['group_by'])];
                array_push($headers, $groupByHeader);
                array_push($selectedFields, $this->request['group_by']);
            }
        }
        $res = $expService->exportToCSV($headers, $selectedFields, $customer_report->query);
        if ($res === false) {
            return $this->errorResponse($expService->errors(), 422);
        }
        return response()->download($res)->deleteFileAfterSend(true);
    }
}