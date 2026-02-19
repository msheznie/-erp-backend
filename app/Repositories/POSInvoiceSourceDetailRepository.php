<?php

namespace App\Repositories;

use App\Models\POSInvoiceSourceDetail;
use App\Repositories\BaseRepository;

/**
 * Class POSInvoiceSourceDetailRepository
 * @package App\Repositories
 * @version July 19, 2022, 3:13 pm +04
 *
 * @method POSInvoiceSourceDetail findWithoutFail($id, $columns = ['*'])
 * @method POSInvoiceSourceDetail find($id, $columns = ['*'])
 * @method POSInvoiceSourceDetail first($columns = ['*'])
*/
class POSInvoiceSourceDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'invoiceID',
        'itemAutoID',
        'itemCategory',
        'financeCategory',
        'itemFinanceCategory',
        'itemFinanceCategorySub',
        'defaultUOMID',
        'UOMID',
        'unitOfMeasure',
        'conversionRateUOM',
        'expenseGLAutoID',
        'revenueGLAutoID',
        'assetGLAutoID',
        'qty',
        'price',
        'discountPer',
        'discountAmount',
        'generalDiscountPercentage',
        'generalDiscountAmount',
        'promoID',
        'promotiondiscount',
        'promotiondiscountAmount',
        'taxCalculationformulaID',
        'taxAmount',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionAmountBeforeDiscount',
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
        'transaction_log_id',
        'mapping_master_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSInvoiceSourceDetail::class;
    }
}
