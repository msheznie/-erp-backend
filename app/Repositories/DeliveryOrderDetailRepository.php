<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;

/**
 * Class DeliveryOrderDetailRepository
 * @package App\Repositories
 * @version May 12, 2020, 8:43 am +04
 *
 * @method DeliveryOrderDetail findWithoutFail($id, $columns = ['*'])
 * @method DeliveryOrderDetail find($id, $columns = ['*'])
 * @method DeliveryOrderDetail first($columns = ['*'])
*/
class DeliveryOrderDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'deliveryOrderID',
        'companySystemID',
        'documentSystemID',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'unitOfMeasureIssued',
        'convertionMeasureVal',
        'qtyIssued',
        'qtyIssuedDefaultMeasure',
        'currentStockQty',
        'currentWareHouseStockQty',
        'currentStockQtyInDamageReturn',
        'wacValueLocal',
        'wacValueReporting',
        'unitTransactionAmount',
        'discountPercentage',
        'discountAmount',
        'transactionCurrencyID',
        'transactionCurrencyER',
        'transactionAmount',
        'companyLocalCurrencyID',
        'companyLocalCurrencyER',
        'companyLocalAmount',
        'companyReportingCurrencyID',
        'companyReportingCurrencyER',
        'companyReportingAmount',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DeliveryOrderDetail::class;
    }

    public function updateMasterTableTransactionAmount($id){

        $detailAmount = DeliveryOrderDetail::
        select(DB::raw("
        IFNULL(SUM(qtyIssuedDefaultMeasure * (unitTransactionAmount-discountAmount)),0) as transAmount,
        IFNULL(SUM(qtyIssuedDefaultMeasure * (companyLocalAmount-(companyLocalAmount*discountPercentage/100))),0) as localAmount,
        IFNULL(SUM(qtyIssuedDefaultMeasure * (companyReportingAmount-(companyReportingAmount*discountPercentage/100))),0) as reportAmount"))
            ->where('deliveryOrderID', $id)
            ->first();

        if(!empty($detailAmount)){
            $array['transactionAmount'] = $detailAmount->transAmount;
            $array['companyLocalAmount'] = $detailAmount->localAmount;
            $array['companyReportingAmount'] = $detailAmount->reportAmount;

            $array['transactionAmount'] = Helper::roundValue($array['transactionAmount']);
            $array['companyLocalAmount'] = Helper::roundValue($array['companyLocalAmount']);
            $array['companyReportingAmount'] = Helper::roundValue($array['companyReportingAmount']);

            DeliveryOrder::where('deliveryOrderID',$id)->update(
                [
                    'transactionAmount'=> Helper::roundValue($array['transactionAmount']),
                    'companyLocalAmount'=> Helper::roundValue($array['companyLocalAmount']),
                    'companyReportingAmount'=> Helper::roundValue($array['companyReportingAmount'])
                ]
            );
        }

    }
}
