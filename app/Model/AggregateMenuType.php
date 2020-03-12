<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AggregateMenuType extends Model
{
    protected $table = 'rpt_aggregate_menu_type';

    protected $fillable = [
        'merchant_id',
        'Date',
        'item_size_id',
        'Menu_Type',
        'Item_Name',
        'Size_Name',
        'Item_Total',
        'Item_Count',
        'Item_Total_with_Modifiers'
    ];


    /**
     * @param $merchant_list
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function getSalesByMenuItemSize($merchant_list)
    {
        $this->setConnection('reports_db');
        return $this->newQuery()
            ->whereIn('merchant_id', $merchant_list)
            ->select(DB::raw('item_size_id, Size_Name,Item_Name, Menu_Type, Date,SUM(Item_Total) as Item_Total, SUM(Item_Count) as Item_Count, SUM(Item_Total_with_Modifiers) as Item_Total_with_Modifiers, Count(*) as Records_Count'))
            ->groupBy(['item_size_id']);
    }

    /**
     * @param $merchant_list
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getSalesByMenuItemGroupByWeekDay($merchant_list)
    {
        $this->setConnection('reports_db');
        $baseQuery = $this->getSalesByMenuItemSize($merchant_list);
        return $baseQuery->selectSub('DAYNAME(Date)', 'week_day')->groupBy('week_day');
    }

    /**
     * @param $merchant_list
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getSalesByMenuItemGroupByMonth($merchant_list)
    {
        $this->setConnection('reports_db');
        $baseQuery = $this->getSalesByMenuItemSize($merchant_list);
        return $baseQuery->selectSub('MONTHNAME(Date)', 'month')->groupBy('month');
    }

    /**
     * @param $merchant_list
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getSalesByMenuItemGroupByYear($merchant_list)
    {
        $this->setConnection('reports_db');
        $baseQuery = $this->getSalesByMenuItemSize($merchant_list);
        return $baseQuery->selectSub('YEAR(Date)', 'year')->groupBy('year');
    }

    /**
     * @param array $merchant_list
     * @param string $group_by
     * @param string $order_by
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    public function getSalesByMenuItemPaginatedQueryWithOrderBy(array $merchant_list, $group_by = 'order_date', $order_by = 'item_size_id', $direction = 'DES')
    {
        switch ($group_by) {
            case 'order_date':
                $query = $this->getSalesByMenuItemSize($merchant_list)->orderBy($order_by, $direction);
                break;
            case 'order_day_of_week':
                $query = $this->getSalesByMenuItemGroupByWeekDay($merchant_list)->orderBy($order_by, $direction);
                break;
            case 'order_month':
                $query = $this->getSalesByMenuItemGroupByMonth($merchant_list)->orderBy($order_by, $direction);
                break;
            case 'order_year':
                $query = $this->getSalesByMenuItemGroupByYear($merchant_list)->orderBy($order_by, $direction);
                break;
            default:
                return null;
                break;
        }

        return $query;
    }
}