<?php

namespace App\Repositories;

use App\Models\POSSourceSalesReturnDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSSourceSalesReturnDetailsRepository
 * @package App\Repositories
 * @version July 25, 2022, 8:00 am +04
 *
 * @method POSSourceSalesReturnDetails findWithoutFail($id, $columns = ['*'])
 * @method POSSourceSalesReturnDetails find($id, $columns = ['*'])
 * @method POSSourceSalesReturnDetails first($columns = ['*'])
*/
class POSSourceSalesReturnDetailsRepository extends BaseRepository
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
        return POSSourceSalesReturnDetails::class;
    }
}
