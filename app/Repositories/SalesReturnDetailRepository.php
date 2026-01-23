<?php

namespace App\Repositories;

use App\Models\SalesReturnDetail;
use App\Repositories\BaseRepository;

/**
 * Class SalesReturnDetailRepository
 * @package App\Repositories
 * @version December 21, 2020, 4:14 pm +04
 *
 * @method SalesReturnDetail findWithoutFail($id, $columns = ['*'])
 * @method SalesReturnDetail find($id, $columns = ['*'])
 * @method SalesReturnDetail first($columns = ['*'])
*/
class SalesReturnDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'reasonCode',
        'balanceQty',
        'fullyReturned',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SalesReturnDetail::class;
    }
}
