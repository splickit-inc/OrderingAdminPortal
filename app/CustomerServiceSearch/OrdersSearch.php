<?php namespace App\CustomerServiceSearch;

use App\Model\Lookup;
use App\Model\OrganizationLookupMap;
use App\Model\PortalLookupHierarchy;
use App\Service\Utility;
use App\Model\Orders;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

use \DB;

use Illuminate\Support\Facades\Log;

class OrdersSearch {

    protected $query;
    public $request;
    protected $variable_bindings = [];
    public $order_types = ['delivery', 'pickup'];
    public $result;
    protected $count_check_query;
    protected $table_string;

    public function applyFilter($request) {
        $this->request = $request;

        $this->setBaseOrdersTable();
        $this->portalVisibilityJoins();

        $this->setWhereCriteria();

        $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';

        $this->order_by = $request->order_by ? $request->order_by : 'order_id';

        $this->setSelect();

        if (isset($request->page)) {
            $skip = 20*($request->page - 1);
            $this->result['data'] = $this->query->orderBy($this->order_by, $order_direction)->skip($skip)->take(21)->get()->toArray();
        }
        else {
            $this->result['data'] = $this->query->orderBy($this->order_by, $order_direction)->take(21)->get()->toArray();
        }

        $this->setPaginationData($request);

        return $this->result;
    }

    public function addLast7DaysToCountCheck() {
        if ($this->request['past_seven_days'] == "false") {
            $this->count_check_query->whereRaw("Orders.created > (now() - interval 7 day)");
        }
    }

    public function formatPhoneNumberForDb($only_alpha_numeric) {
        if (substr($only_alpha_numeric, 0, 1) == 1) {
            $only_alpha_numeric = substr($only_alpha_numeric,1);
        }
        return substr($only_alpha_numeric, 0, 3).'-'.substr($only_alpha_numeric, 3, 3).'-'.substr($only_alpha_numeric, 6, 4);
    }

    protected function setPaginationData($request) {
        if (isset($request->page)) {
            $this->result['current_page'] = $request->page;
            $previous_page = $this->result['current_page'] -1;
            $this->result['prev_page_url'] = $_SERVER['HTTP_REFERER'].'orders_search?page='.$previous_page;
        }
        else {
            $this->result['current_page'] = 1;
            $this->result['prev_page_url'] = null;
        }

        $this->result['from'] = (($this->result['current_page'] - 1)*20) +1;

        if (sizeof($this->result['data']) > 20) {
            $this->result['last_page'] = $this->result['current_page'] + 1;
            $this->result['to'] = $this->result['from'] + 20;
            $this->result['next_page_url'] = $_SERVER['HTTP_REFERER'].'orders_search?page='.$this->result['last_page'];
        }
        else {
            $this->result['to'] = $this->result['from'] + (sizeof($this->result['data']) - 1);
            $this->result['last_page'] = $this->result['current_page'];
            $this->result['next_page_url'] = null;
        }
        $this->result['per_page'] = 20;
    }

    protected function setBaseOrdersTable() {
        if ($this->request['past_seven_days'] == "true") {
            $this->query = DB::connection('reports_db')->table('smawv_nb_orders');
            $this->table_string = 'smawv_nb_orders';
        }
        else {
            $this->query = DB::connection('reports_db')->table('smawv_nb_7day_orders');
            $this->table_string = 'smawv_nb_7day_orders';
        }

    }

    protected function portalVisibilityJoins() {
        if (session('user_visibility') == 'brand') {
            $this->query->join('Merchant', $this->table_string.'.merchant_id', '=', 'Merchant.merchant_id')
                        ->where('Merchant.brand_id', '=', '?');
            array_push($this->variable_bindings, session('brand_manager_brand'));
        }
    }

    public function singleIntSearchCriteria($search_text) {
        $only_alpha_numeric = preg_replace("/[^A-Za-z0-9 ]/", '', $search_text);
        if (strlen($only_alpha_numeric) >= 10) {
            $phone_number = $this->formatPhoneNumberForDb($only_alpha_numeric);

            $this->count_check_query = DB::connection('reports_db')->table('Orders')
                ->join('User', 'Orders.user_id', '=', 'User.user_id')
                ->where('User.contact_no', '=', $phone_number);

            $this->addLast7DaysToCountCheck();
            $phone_count = $this->count_check_query->count();

            if ($phone_count > 0) {
                $this->query = $this->query->whereRaw("(User.contact_no = ?)");
                array_push($this->variable_bindings, $phone_number);
                return;
            }
        }

        $this->count_check_query = Orders::where('order_id', '=', $only_alpha_numeric);
        $this->addLast7DaysToCountCheck();
        $order_count = $this->count_check_query->count();

        if ($order_count > 0) {
            $this->query = $this->query->whereRaw("(order_id = ?)");
            array_push($this->variable_bindings, $only_alpha_numeric);
            return;
        }

        $this->count_check_query = Orders::where('user_id', '=', $only_alpha_numeric);
        $this->addLast7DaysToCountCheck();
        $user_count = $this->count_check_query->count();

        if ($user_count > 0) {
            $this->query = $this->query->whereRaw("(user_id = ?)");
            array_push($this->variable_bindings, $only_alpha_numeric);
            return;
        }

        $this->count_check_query = Orders::where('merchant_id', '=', $only_alpha_numeric);
        $this->addLast7DaysToCountCheck();
        $merchant_count = $this->count_check_query->count();

        if ($merchant_count > 0) {
            $this->query = $this->query->whereRaw("(".$this->table_string.".merchant_id = ?)");
            array_push($this->variable_bindings, $only_alpha_numeric);
            return;
        }

    }

    public function singleFloatValSearchCriteria($search_text) {
        $this->query = $this->query->whereRaw("(Orders.order_id = ? or Merchant.merchant_id = ? or User.user_id = ?)");
        array_push($this->variable_bindings, $search_text, $search_text, $search_text);
    }

    public function emailCriteria($lower_search_term) {
        if (strpos($lower_search_term, '!') !== false) {
            $lower_search_term = str_replace("!","",$lower_search_term);
            $this->query = $this->query->whereRaw("(email like ?)");
            array_push($this->variable_bindings, "%" . $lower_search_term . "%");
        }
        else {
            $this->query = $this->query->whereRaw("(email = ?)");
            array_push($this->variable_bindings, $lower_search_term);
        }
    }

    public function multiIntSearchCriteria($search_text) {
        $only_alpha_numeric = preg_replace("/[^A-Za-z0-9 ]/", '', $search_text);

        if (strlen($only_alpha_numeric) >= 10) {
            $phone_number = $this->formatPhoneNumberForDb($only_alpha_numeric);

            $this->query = $this->query->whereRaw("(".$this->table_string.".merchant_id = ? or user_id = ? or promo_code = ? or ".$this->table_string.".address1 like ? or contact_no = ?)");
            array_push($this->variable_bindings, $search_text, $search_text, $search_text, "%".$search_text."%", $phone_number);
        }
        else {
            $this->query = $this->query->whereRaw("(".$this->table_string.".merchant_id = ? or user_id = ? or promo_code = ? or ".$this->table_string.".address1 like ?)");
            array_push($this->variable_bindings, $search_text, $search_text, $search_text, "%".$search_text."%");
        }
    }

    public function multiFloatValCriteria($search_text) {
        $this->query = $this->query->whereRaw("(grand_total = ?)");
        array_push($this->variable_bindings, $search_text);
    }

    public function orderTypesCriteria($search_text) {
        if (strtolower($search_text) == 'delivery') {
            $this->query = $this->query->whereRaw("(order_type = 'Delivery')");
        }
        elseif (strtolower($search_text) == 'pickup') {
            $this->query = $this->query->whereRaw("(order_type = 'Pickup')");
        }
    }

    public function multiCharCriteria($search_text) {


        $this->query = $this->query->whereRaw("( promo_code = ? 
                                                or lower(first_name) like ? 
                                                or lower(last_name) like ?
                                                or lower(".$this->table_string.".name) like ?
                                                or lower(".$this->table_string.".address1) like ?
                                                )"
                                                );

        array_push($this->variable_bindings,
                                           //     "%".$search_text."%",   //address1
                                                $search_text,                       //promo code
                                                "%".$search_text."%",   //first_name
                                                "%".$search_text."%",   //last_name
                                                "%".$search_text."%",   //last_name
                                                "%".$search_text."%");  //Merchant.name
        //Merchant.address1 like ?
        //or
    }

    public function dateValidation($date_string, $search_term) {
        $time_stamp = strtotime($date_string);
        $this->query = $this->query->whereRaw("(DATE(order_dt_tm) = ? or grand_total = ?)");

        array_push($this->variable_bindings, date('Y-m-d', $time_stamp), $search_term);
    }

    public function setWhereCriteria() {
        $utility = new Utility();
        $search_terms = explode(" ", $this->request['search_text']);

        if (sizeof($search_terms) == 1) {
            $search_term = $search_terms[0];

            $only_alpha_numeric = preg_replace("/[^A-Za-z0-9 ]/", '', $search_term);
            $lower_search_term = strtolower($search_term);

            if (intval($only_alpha_numeric)) {
                $this->singleIntSearchCriteria($search_term);
            }
            elseif (strpos($lower_search_term, '@') !== false) {
                $this->emailCriteria($lower_search_term);
            }
            else {
                $this->multiCharCriteria($lower_search_term);
            }
        }
        else {
            foreach ($search_terms as $search_term) {
                //order_id, merchant_id, user_id
                //(Order) order_dt_tm, grand_total, status, type, promo_code
                //(User) email, first_name, last_name
                //(Merchant) address1, name
                $lower_search_term = strtolower($search_term);

                if (ctype_digit($search_term)) {
                    $this->multiIntSearchCriteria($search_term);
                }
                elseif (strpos($lower_search_term, '@') !== false) {
                    $this->emailCriteria($lower_search_term);
                }
                elseif (is_numeric($search_term)) {
                    $this->multiFloatValCriteria($search_term);
                }
                elseif ($utility->validateDatetime($search_term)) {
                    $this->dateValidation($search_term, $search_term);
                }
                elseif (in_array(strtolower($search_term), $this->order_types)) {
                    $this->orderTypesCriteria($search_term);
                }
                else {
                    $this->multiCharCriteria($lower_search_term);
                }
            }
        }

        $this->addLastSevenDayCriteria();


        $this->query = $this->query->setBindings($this->variable_bindings);

    }

    public function addLastSevenDayCriteria() {
        //$this->query = $this->query->whereRaw("Orders.created > (now() - interval 7 day)");
    }

    public function setSelect() {
        $this->query = $this->query
            ->select([$this->table_string.".*"]);
    }
}
