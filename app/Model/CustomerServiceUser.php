<?php namespace App\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;

class CustomerServiceUser extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'User';
    protected $primaryKey = 'user_id';
    public $variable_bindings = [];
    public $order_by;


    protected $connection = 'reports_db';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function globalWhereClause($search_text, $order_column)
    {
        $this->search_query = DB::table('User');

        $this->searchWhereClause($search_text);

        $this->search_query->setBindings($this->variable_bindings);

        $this->search_query->orderBy($order_column);

        return $this->search_query;
    }



    /**
     * @param $id
     * @return Builder|Model
     */
    public function getById($id)
    {
        $this->setConnection('mysql');
        return $this->newQuery()->where('user_id', '=', $id)->firstOrFail();
    }

    public function setSearchConditionalWhereClauseBrand($search_text)
    {

        $this->user_search = DB::connection('reports_db')->table('User')
            ->join('User_Brand_Maps', 'User.user_id', '=', 'User_Brand_Maps.user_id')
            ->where('User_Brand_Maps.brand_id', '=', '?');

        $this->variable_bindings = [session('brand_manager_brand')];

        $search_terms = explode(" ", $search_text);

        foreach ($search_terms as $search_term) {

            $only_alpha_numeric = preg_replace("/[^A-Za-z0-9 ]/", '', $search_term);

            $lower_search_term = strtolower($search_term);

            if (intval($only_alpha_numeric)) {
                if (strlen($only_alpha_numeric) >= 10) {
                    $phone_number = $this->formatPhoneNumberForDb($only_alpha_numeric);

                    $this->user_search = $this->user_search->whereRaw("(User.contact_no = ?)");

                    array_push($this->variable_bindings, $phone_number);
                }
                else {
                    $this->user_search = $this->user_search->whereRaw("(User.user_id = ?)");

                    array_push($this->variable_bindings, $only_alpha_numeric);
                }
            } elseif (strpos($lower_search_term, '@') !== false) {

                if (strpos($lower_search_term, '!') !== false) {
                    $lower_search_term = str_replace("!","",$lower_search_term);
                    $this->user_search = $this->user_search->whereRaw("(email like ?)");
                    array_push($this->variable_bindings, "%" . $lower_search_term . "%");
                }
                else {
                    $this->user_search = $this->user_search->whereRaw("(email = ?)");
                    array_push($this->variable_bindings, $lower_search_term);
                }
            } elseif (strlen($search_term) > 0) {
                $this->user_search = $this->user_search->whereRaw("(LOWER(first_name) like ? or LOWER(last_name) like ? or LOWER(email) like ?)");

                array_push($this->variable_bindings, "%" . $lower_search_term . "%", "%" . $lower_search_term . "%", "%" . $lower_search_term . "%");
            }
        }

        $this->user_search = $this->user_search->setBindings($this->variable_bindings);
    }

    public function setSearchConditionalWhereClauseGlobal($search_text)
    {
        $this->user_search = new CustomerServiceUser();

        $search_terms = explode(" ", $search_text);

        $this->variable_bindings = [];

        foreach ($search_terms as $search_term) {

            $only_alpha_numeric = preg_replace("/[^A-Za-z0-9 ]/", '', $search_term);

            $lower_search_term = strtolower($search_term);

            if (intval($only_alpha_numeric)) {
                if (strlen($only_alpha_numeric) >= 10) {
                    $phone_number = $this->formatPhoneNumberForDb($only_alpha_numeric);

                    $this->user_search = $this->user_search->whereRaw("(User.contact_no = ?)");

                    array_push($this->variable_bindings, $phone_number);
                }
                else {
                    $this->user_search = $this->user_search->whereRaw("(User.user_id = ?)");

                    array_push($this->variable_bindings, $only_alpha_numeric);
                }
            } elseif (strpos($lower_search_term, '@') !== false) {

                if (strpos($lower_search_term, '!') !== false) {
                    $lower_search_term = str_replace("!","",$lower_search_term);
                    $this->user_search = $this->user_search->whereRaw("(email like ?)");
                    array_push($this->variable_bindings, "%" . $lower_search_term . "%");
                }
                else {
                    $this->user_search = $this->user_search->whereRaw("(email = ?)");
                    array_push($this->variable_bindings, $lower_search_term);
                }
            } elseif (strlen($search_term) > 0) {
                $this->user_search = $this->user_search->whereRaw("(LOWER(first_name) like ? or LOWER(last_name) like ? or LOWER(email) like ?)");

                array_push($this->variable_bindings, "%" . $lower_search_term . "%", "%" . $lower_search_term . "%", "%" . $lower_search_term . "%");
            }

        }

        $this->user_search = $this->user_search->setBindings($this->variable_bindings);
    }

    public function searchTotalCount()
    {
        $count_query = $this->user_search;
        return $count_query->distinct()->count(['User.user_id']);
    }

    /**
     *
     * @return Builder
     * @throws \Exception
     */
    public function getCurrentSearchRecordSet()
    {
        try {
            $record_query = $this->user_search;
            return $record_query
                ->distinct()
                ->select(['User.user_id', 'first_name', 'last_name', 'email', 'contact_no', 'balance', 'orders', 'User.created', 'last_four']);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }


}
