<?php

namespace App\Repositories;

use App\Models\DeliveryOrderDetail;
use InfyOm\Generator\Common\BaseRepository;

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
}
