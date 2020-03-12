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
use App\Reports\MenuItemSalesReport;

class SalesByMenuItemController extends ReportController
{
    CONST reportFields = ['item_size_id', 'Size_Name', 'Item_Name', 'Menu_Type', 'Date', 'Item_Total', 'Item_Count'];
    CONST headers = ['Item Size Id', 'Size Name', 'Item Name', 'Menu Type', 'Date', 'Item Total', 'Item Count'];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request) {
        $menuItemSalesReport = new MenuItemSalesReport($request->all());
        return $menuItemSalesReport->buildResponse();
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
                $group_bys = explode(',',$menu_item_sales_report->request['group_by']);
                foreach ($group_bys as $group_by) {
                    $groupByHeader = self::groupByHeaders[$group_by];
                    array_push($headers, $groupByHeader);
                    array_push($selectedFields, $group_by);
                }
            }
        }

        $res = $expService->exportToCSV($headers, $selectedFields, $menu_item_sales_report->query);
        if ($res === false) {
            return $this->errorResponse($expService->errors(), 422);
        }
        return response()->download($res)->deleteFileAfterSend(true);
    }
}