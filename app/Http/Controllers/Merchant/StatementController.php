<?php

namespace App\Http\Controllers\Merchant;

use \App\Http\Controllers\Controller;
use App\Model\Merchant;
use Illuminate\Http\Request;
use Laracsv\Export;

class StatementController extends Controller
{
    function getStatementsPaginated(Request $request, Merchant $merchant)
    {
        try {
            $merchant_id = session('current_merchant_id');
            $order_by = $request->order_by ? $request->order_by : 'adm_trans_statement.created';
            $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';

            /** @var Merchant $merchant */
            $statements = $merchant->getMerchantStatements($merchant_id, $order_by, $order_direction)->paginate(10);
            return $statements;
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    function exportStatements(Request $request, Merchant $merchant, Export $cvsExporter)
    {
        try {
            $merchant_id = session('current_merchant_id');
            $order_by = $request->order_by ? $request->order_by : 'adm_trans_statement.created';
            $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';

            /** @var Merchant $merchant */
            $statements = $merchant->getMerchantStatements($merchant_id, $order_by, $order_direction)->get();
            $cvsExporter->build($statements, [
                'generation' => 'Date Generated',
                'invoice' => 'Invoice',
                'merchant_id' => 'Merchant ID',
                'merchant_external_id' => 'External ID',
                'periodByYear' => 'Statement Period',
                'name' => 'Store Name',
                'order_qty' => 'Order Quantity',
                'order_cnt' => 'Order Count',
                'total_tax_amt' => 'Tax',
                'tip_amt' => 'Tips',
                'promo_amt' => 'Discounts',
                'delivery_amt' => 'Delivery Fee',
                'cc_fee_amt' => 'CC Trans Fees',
                'pymt_fee' => 'Payment Fee',
                'trans_fee_amt' => 'Splick-it Customer Fee',
                'bill_comm_fee' => 'Commissions',
                'goodwill' => 'Cancellations & Refunds',
                'reversal_note' => 'Cancellation & Refund Details',
                'order_amt' => 'Grand Total',
                'total_fees' => 'Total Fees',
                'net_proceeds' => 'Net Proceeds',
                'payment' => 'Payment'

            ])->download();
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }
}