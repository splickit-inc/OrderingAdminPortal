<?php namespace App\Reports;

use Session;
use App\Service\ReportExportationService;

abstract class BaseReport{
    CONST groupByHeaders = ['order_date' => 'Date',
        'order_hour' => 'Hour of Day', 'order_day_of_week' => 'Day of Week',
        'order_month' => 'Month', 'order_year' => 'Year', 'order_type' => 'Order Type',
        'payment_type' => 'Payment Type', 'order_id' => null, 'o.user_id' => null, 'merchant_id'=>'Merchant Id', 'promo_code'=>'Promo Code', 'Date'=>'Date',
        'group_order'=>'Group Order', 'promo_id'=> 'Promo ID'];

    const CONNECTION = 'reports_db';
    const PER_PAGE = 25;

    public $group_by_select = [];
    public $query;
    public $request;
    public $total;
    public $order_by;
    public $total_records;
    public $last_page;

    public $all_group_bys;


    public $response = [];

    public function __construct($request)
    {
        $this->request = $request;
        if (!isset($this->request['group_by'])) {
            $this->request['group_by'] = false;
        }
    }

    public function getQueryTotal() {
        return $this->query->get()->count();
    }

    public function getCurrentData($offset) {
        return $this->query->skip($offset)->take(BaseReport::PER_PAGE)->get()->toArray();
    }

    public function getNextPageUrl() {
        $next_page = $this->request['page'] + 1;

        if ($next_page < $this->last_page) {
            return $_SERVER['HTTP_REFERER'].'/reports/'.$this->url_value.'?page='.$next_page;
        }
        else {
            return null;
        }
    }

    public function getPreviousPageUrl() {
        if ($this->request['page'] > 1) {
            $previous_page = $this->request['page'] - 1;
            return $_SERVER['HTTP_REFERER'].'/reports/'.$this->url_value.'?page='.$previous_page;
        }
        else {
            return null;
        }
    }

    public function setOrderBy() {
        if ($this->order_by) {
            if (substr($this->order_by, 0, 1) == '-') {
                $order_by_direction = 'desc';
            }
            else {

                $order_by_direction = 'asc';
            }
            $order_by_column = substr($this->order_by, 1);
            $this->query = $this->query->orderBy($order_by_column, $order_by_direction);
        }
    }

    public function setSessionData() {
        $new_order_by = false;
        if (isset($this->request['order_by'])) {
            if (Session::has('report_data')) {
                $session_report_data = session('report_data');

                if (isset($session_report_data['order_by'])) {
                    if ($this->request['order_by'] != $session_report_data['order_by']) {
                        $new_order_by = true;
                    }
                }
                else {
                    $new_order_by = true;
                }
            }
        }

        if ($this->request['page'] == 1 || $new_order_by) {
            if (isset($this->request['order_by'])) {
                $this->order_by = $this->request['order_by'];
            }
            else {
                $this->order_by = false;
            }

            $total = $this->getQueryTotal();
            $last_page = round(($total/BaseReport::PER_PAGE));
            session(['report_data' => [
                'total'=>$total,
                'last_page'=>$last_page,
                'order_by'=>$this->order_by
            ]]);
            $this->total_records = $total;
            $this->last_page = $last_page;
        }
        else {
            $report_data = session('report_data');
            $this->total_records = $report_data['total'];
            $this->last_page = $report_data['last_page'];
        }
    }

    public function buildResponse() {
        $this->setBaseQuery();
        $this->setSelect();
        $this->setWhereClause();
        $this->setGroupBy();
        $this->setSessionData();
        $this->setOrderBy();

        $offset = (BaseReport::PER_PAGE * $this->request['page']) - BaseReport::PER_PAGE;
        $this->response['from'] = $offset + 1;
        $this->response['to'] = $offset + BaseReport::PER_PAGE;
        $this->response['data'] = $this->getCurrentData($offset);
        $this->response['per_page'] = BaseReport::PER_PAGE;
        $this->response['current_page'] = $this->request['page'];
        $this->response['next_page_url'] = $this->getNextPageUrl();
        $this->response['prev_page_url'] = $this->getPreviousPageUrl();
        $this->response['total'] = $this->total_records;
        $this->response['last_page'] = $this->last_page;

        return $this->response;
    }

    public function buildQuery() {
        $this->setBaseQuery();

        $this->setSelect();
        $this->setWhereClause();
        $this->setGroupBy();
        $this->setOrderBy();
    }

    public function getReportCsv() {
        $this->buildQuery();

        $group_by = $this->request['group_by'];

        $expService = new ReportExportationService();
        $selectedFields = static::reportFields;
        $groupByHeader = static::groupByHeaders[$this->request['group_by']];
        $headers = static::headers;
        if (!is_null($groupByHeader)) {
            array_push($headers, $groupByHeader);
            array_push($selectedFields, $group_by);
        }
        $resp = $expService->exportToCSV($headers, $selectedFields, $this->query);
        return $resp;
    }
}
