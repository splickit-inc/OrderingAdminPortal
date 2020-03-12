<?php


namespace App\Http\Controllers\Reports;


use App\Http\Controllers\Controller;
use App\Model\Brand;
use App\Model\ReportTransactionStatement;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Laracsv\Export;
use Illuminate\Support\Facades\DB;

class StatementController extends Controller
{
    function getStatementsPaginated(Request $request, ReportTransactionStatement $statements, Brand $brand)
    {
        try {
            $order_by = $request->order_by ? $request->order_by : 'adm_trans_statement_with_date.created';
            $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';

            if ($request->has('merchant_list') && $request->filter_type == 'merchant') {
                $merchant_list = explode(',', $request->merchant_list);
                $result = $statements->getStatementsForMerchantList($merchant_list, $order_by, $order_direction)
                    ->where('period_filter', '>=', Carbon::parse($request->start_date)->firstOfMonth())
                    ->where('period_filter', '<=', Carbon::parse($request->end_date)->lastOfMonth())
                    ->paginate(20);
                return $result;
            }
            if ($request->has('brand_id') && $request->filter_type == 'brand') {
                /** @var Brand $brand */
                $brand = $brand->find($request->brand_id);
                $merchants = $brand->merchants(session('user_visibility'))->pluck('merchant_id');
                $result = $statements->getStatementsForMerchantList($merchants, $order_by, $order_direction)
                    ->where('period_filter', '>=', Carbon::parse($request->start_date)->firstOfMonth())
                    ->where('period_filter', '<=', Carbon::parse($request->end_date)->lastOfMonth())
                    ->paginate(20);

                return $result;
            }

            return response()->json(['error' => 'Bad Request'], 404);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 404);
        }
    }

    public function exportStatements(Request $request, ReportTransactionStatement $statementsModel, Brand $brand, Export $cvsExporter)
    {
        try {
            $order_by = $request->order_by ? $request->order_by : 'adm_trans_statement_with_date.created';
            $order_direction = $request->exists('order_direction') && $request->order_direction == 'false' ? 'ASC' : 'DES';
            $statements = new Collection([]);
            if ($request->has('merchant_list') && $request->filter_type == 'merchant') {
                $merchant_list = explode(',', $request->merchant_list);
                $statements = $statementsModel->getStatementsForMerchantList($merchant_list, $order_by, $order_direction)
                    ->where('period_filter', '>=', Carbon::parse($request->start_date)->firstOfMonth())
                    ->where('period_filter', '<=', Carbon::parse($request->end_date)->lastOfMonth())
                    ->get();
            }
            if ($request->has('brand_id') && $request->filter_type == 'brand') {
                /** @var Brand $brand */
                $brand = $brand->find($request->brand_id);
                $merchants = $brand->merchants(session('user_visibility'))->pluck('merchant_id');
                $statements = $statementsModel->getStatementsForMerchantList($merchants, $order_by, $order_direction)
                    ->where('period_filter', '>=', Carbon::parse($request->start_date)->firstOfMonth())
                    ->where('period_filter', '<=', Carbon::parse($request->end_date)->lastOfMonth())
                    ->get();
            }
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