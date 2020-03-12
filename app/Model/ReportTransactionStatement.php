<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReportTransactionStatement extends Model
{
    protected $table = 'adm_trans_statement_with_date';
    protected $connection = 'reports_db';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'generation',
        'invoice',
        'merchant_id',
        'merchant_external_id',
        'period',
        'name',
        'address1',
        'address2',
        'city',
        'state',
        'zip',
        'cc_processor',
        'payment_cycle',
        'order_qty',
        'order_cnt',
        'order_amt',
        'total_tax_amt',
        'tip_amt',
        'promo_amt',
        'trans_fee_amt',
        'sf_surcharge_fee',
        'delivery_amt',
        'grand_total',
        'cc_fee_amt',
        'pymt_fee',
        'customer_donation_amt',
        'merch_trans_fee',
        'total_fees',
        'owner_cdt_purch',
        'goodwill',
        'reversal_note',
        'net_proceeds',
        'payment',
        'balance',
        'payment_id',
        'weekly',
        'destination_emails',
        'inc_comm_fee',
        'bill_comm_fee',
        'previous_balance',
        'instore_payment_cnt',
        'instore_payment_sum',
        'periodByYear',
        'period_filter'
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'merchant_id');
    }

    /**
     * @param $merchant_list
     * @param string $order_by
     * @param string $order_direction
     * @return mixed
     * @throws \Exception
     */
    public function getStatementsForMerchantList($merchant_list,$order_by = 'adm_trans_statement.created', $order_direction = 'DES')
    {
        try {
            $result = $this
                ->whereIn('merchant_id', $merchant_list)
                ->orderBy($order_by, $order_direction);
            return $result;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}