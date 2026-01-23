<?php

namespace App\Repositories;

use App\Models\SalesReturnDetailRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class SalesReturnDetailRefferedBackRepository
 * @package App\Repositories
 * @version December 24, 2020, 2:10 pm +04
 *
 * @method SalesReturnDetailRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method SalesReturnDetailRefferedBack find($id, $columns = ['*'])
 * @method SalesReturnDetailRefferedBack first($columns = ['*'])
*/
class SalesReturnDetailRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'salesReturnDetailID',
        'salesReturnID',
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
        'qtyReturned',
        'qtyReturnedDefaultMeasure',
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
        'deliveryOrderID',
        'deliveryOrderDetailID',
        'remarks',
        'qtyIssued',
        'balanceQty',
        'fullyReturned',
        'timestamp',
        'doInvRemainingQty',
        'customerItemDetailID',
        'custInvoiceDirectAutoID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SalesReturnDetailRefferedBack::class;
    }
}
