<?php namespace App\CustomerServiceSearch;

use App\Model\Lookup;
use App\Model\OrganizationLookupMap;
use App\Model\PortalLookupHierarchy;
use App\Service\Utility;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use App\Model\CustomerServiceUser;

use \DB;

use Illuminate\Support\Facades\Log;

class UserSearch {

    protected $query;
    public $request;
    protected $variable_bindings = [];
    public $result;


    public function applyFilter($request) {
        $this->request = $request;

        $this->setBaseOrdersTable();

        $this->setWhereCriteria($request->search_text);

        $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';

        $this->order_by = $request->order_by ? $request->order_by : 'User.user_id';

        $this->setSelect();

        if (isset($request->page)) {
            $skip = 20*($request->page - 1);
            $this->result['data'] = $this->query->orderBy($this->order_by, $order_direction)->skip($skip)->take(21)->get()->toArray();
        }
        else {
            $this->result['data'] = $this->query->orderBy($this->order_by, $order_direction)->take(21)->get()->toArray();
        }

        $this->setPaginationData($request);

//        current_page: 1
//        data: [{user_id: 20000, first_name: "adam", last_name: "Rosenthal", email: "arosenthal@yourcompany.com",…},…]
//        from: 1
//        last_page: 1
//        next_page_url: null
//        per_page: 20
//        prev_page_url: null
//        to: 5
//        total: 5
        return $this->result;
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

    public function setBaseOrdersTable() {
        if (session('user_visibility') == 'brand') {
            $this->query = DB::connection('reports_db')->table('User')
                ->join('User_Brand_Maps', 'User.user_id', '=', 'User_Brand_Maps.user_id')
                ->where('User_Brand_Maps.brand_id', '=', '?');

            array_push($this->variable_bindings, session('brand_manager_brand'));
        }
        else {
            $this->query = new CustomerServiceUser();
        }
    }

    public function formatPhoneNumberForDb($only_alpha_numeric) {
        if (substr($only_alpha_numeric, 0, 1) == 1) {
            $only_alpha_numeric = substr($only_alpha_numeric,1);
        }
        return substr($only_alpha_numeric, 0, 3).'-'.substr($only_alpha_numeric, 3, 3).'-'.substr($only_alpha_numeric, 6, 4);
    }

    public function setWhereCriteria($search_text)
    {
        $search_terms = explode(" ", $search_text);

        foreach ($search_terms as $search_term) {

            $only_alpha_numeric = preg_replace("/[^A-Za-z0-9 ]/", '', $search_term);

            $lower_search_term = strtolower($search_term);

            if (intval($only_alpha_numeric)) {
                if (strlen($only_alpha_numeric) >= 10) {
                    $phone_number = $this->formatPhoneNumberForDb($only_alpha_numeric);

                    $this->query = $this->query->whereRaw("(User.contact_no = ?)");

                    array_push($this->variable_bindings, $phone_number);
                }
                else {
                    $this->query = $this->query->whereRaw("(User.user_id = ?)");

                    array_push($this->variable_bindings, $only_alpha_numeric);
                }
            } elseif (strpos($lower_search_term, '@') !== false) {

                if (strpos($lower_search_term, '!') !== false) {
                    $lower_search_term = str_replace("!","",$lower_search_term);
                    $this->query = $this->query->whereRaw("(email like ?)");
                    array_push($this->variable_bindings, "%" . $lower_search_term . "%");
                }
                else {
                    $this->query = $this->query->whereRaw("(email = ?)");
                    array_push($this->variable_bindings, $lower_search_term);
                }
            } elseif (strlen($search_term) > 0) {
                $this->query = $this->query->whereRaw("(LOWER(first_name) like ? or LOWER(last_name) like ? or LOWER(email) like ?)");

                array_push($this->variable_bindings, "%" . $lower_search_term . "%", "%" . $lower_search_term . "%", "%" . $lower_search_term . "%");
            }

        }
        $this->query = $this->query->setBindings($this->variable_bindings);
    }

    public function setSelect() {
        $this->query = $this->query
            ->select(['User.user_id', 'first_name', 'last_name', 'email', 'contact_no', 'balance', 'orders', 'User.created', 'last_four']);
    }
}
