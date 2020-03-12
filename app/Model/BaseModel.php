<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BaseModel extends Model
{
    protected $rules = [];
    protected $attrNames = [];
    protected $searchParams = [];

    protected $errors;

    public function validate($data)
    {
        $v = \Validator::make($data, $this->rules);
        if (count($this->attrNames) != 0) {
            $v->setAttributeNames($this->attrNames);
        }
        if ($v->fails()) {
            $errors = array_values($v->errors()->getMessages());
            $errors = array_reduce($errors, "array_merge", []);
            $this->errors = $errors;
            return false;
        }
        return true;
    }

    public function errors()
    {
        return $this->errors;
    }

    /**
     * Searches the model database, with the parameters specified in
     * the $searchParams attribute, returns all if no params or search string defined
     * @param string $searchString
     * @param array $searchParams
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function search(string $searchString, array $searchParams = [])
    {
        if (!isset($searchString) || empty(trim($searchString)) || !isset($searchParams) || empty($searchParams)) {
            return static::queryAll(); //all
        }
        $searchString = "%" . strtolower(trim($searchString)) . "%";
        $searchParams = array_map(function ($el) {
            return trim($el); //trim params JIC
        }, $searchParams);
        $params = array_values(array_intersect((new static)->searchParams, $searchParams));
        if (empty($params)) {
            return static::queryAll(); //all
        }
        $query = static::where($params[0], 'like', $searchString);
        for ($i = 1; $i < count($params); $i++) {
            $query->orWhere($params[$i], 'like', $searchString);
        }
        return $query;
    }

    private static function queryAll()
    {
        return static::where((new static)->primaryKey, '<>', '-1');
    }

    public static function createdAtRange($createdAtRange) {
        $parts = explode(',', $createdAtRange);
        $from = Carbon::createFromFormat('Y-m-d', trim($parts[0]), 'EST');
        $to = Carbon::createFromFormat('Y-m-d', trim($parts[1]), 'EST');
        $from->setTime(0, 0);
        $from->setTimezone('UTC');

        $to->setTime(23, 59, 59);
        $to->setTimezone('UTC');

        return Lead::whereBetween(static::CREATED_AT, [$from, $to]);
    }
}
