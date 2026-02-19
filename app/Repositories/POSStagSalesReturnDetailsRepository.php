<?php

namespace App\Repositories;

use App\Models\POSStagSalesReturnDetails;
use App\Repositories\BaseRepository;

/**
 * Class POSStagSalesReturnDetailsRepository
 * @package App\Repositories
 * @version July 25, 2022, 8:00 am +04
 *
 * @method POSStagSalesReturnDetails findWithoutFail($id, $columns = ['*'])
 * @method POSStagSalesReturnDetails find($id, $columns = ['*'])
 * @method POSStagSalesReturnDetails first($columns = ['*'])
*/
class POSStagSalesReturnDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'salesReturnID',
        'invoiceID',
        'invoiceDetailID',
        'itemAutoID',
        'itemCategory',
        'financeCategory',
        'itemFinanceCategory',
        'itemFinanceCategorySub',
        'defaultUOMID',
        'unitOfMeasure',
        'UOMID',
        'conversionRateUOM',
        'qty',
        'price',
        'discountPer',
        'generalDiscountPercentage',
        'generalDiscountAmount',
        'promotiondiscount',
        'promotiondiscountAmount',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionAmount',
        'transactionCurrencyDecimalPlaces',
        'transactionExchangeRate',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalAmount',
        'companyLocalExchangeRate',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingAmount',
        'companyReportingCurrencyDecimalPlaces',
        'companyReportingExchangeRate',
        'taxCalculationformulaID',
        'taxAmount',
        'expenseGLAutoID',
        'revenueGLAutoID',
        'assetGLAutoID',
        'companyID',
        'companyCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp',
        'transaction_log_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSStagSalesReturnDetails::class;
    }
}
