<?php

namespace App\Repositories;

use App\Models\POSSTAGInvoiceDetail;
use App\Repositories\BaseRepository;

/**
 * Class POSSTAGInvoiceDetailRepository
 * @package App\Repositories
 * @version July 18, 2022, 10:47 am +04
 *
 * @method POSSTAGInvoiceDetail findWithoutFail($id, $columns = ['*'])
 * @method POSSTAGInvoiceDetail find($id, $columns = ['*'])
 * @method POSSTAGInvoiceDetail first($columns = ['*'])
*/
class POSSTAGInvoiceDetailRepository extends BaseRepository
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
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSSTAGInvoiceDetail::class;
    }
}
