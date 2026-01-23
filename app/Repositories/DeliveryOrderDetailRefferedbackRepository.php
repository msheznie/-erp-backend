<?php

namespace App\Repositories;

use App\Models\DeliveryOrderDetailRefferedback;
use App\Repositories\BaseRepository;

/**
 * Class DeliveryOrderDetailRefferedbackRepository
 * @package App\Repositories
 * @version June 24, 2020, 8:20 am +04
 *
 * @method DeliveryOrderDetailRefferedback findWithoutFail($id, $columns = ['*'])
 * @method DeliveryOrderDetailRefferedback find($id, $columns = ['*'])
 * @method DeliveryOrderDetailRefferedback first($columns = ['*'])
*/
class DeliveryOrderDetailRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'deliveryOrderDetailID',
        'deliveryOrderID',
        'companySystemID',
        'documentSystemID',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'unitOfMeasureIssued',
        'convertionMeasureVal',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'financeGLcodeRevenueSystemID',
        'financeGLcodeRevenue',
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
        'quotationMasterID',
        'quotationDetailsID',
        'remarks',
        'requestedQty',
        'balanceQty',
        'fullyReceived',
        'invQty',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DeliveryOrderDetailRefferedback::class;
    }
}
