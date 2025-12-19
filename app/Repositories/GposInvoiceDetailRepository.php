<?php

namespace App\Repositories;

use App\Models\GposInvoiceDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class GposInvoiceDetailRepository
 * @package App\Repositories
 * @version January 22, 2019, 10:14 am +04
 *
 * @method GposInvoiceDetail findWithoutFail($id, $columns = ['*'])
 * @method GposInvoiceDetail find($id, $columns = ['*'])
 * @method GposInvoiceDetail first($columns = ['*'])
*/
class GposInvoiceDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'invoiceID',
        'companySystemID',
        'companyID',
        'itemAutoID',
        'itemSystemCode',
        'itemDescription',
        'itemCategory',
        'financeCategory',
        'itemFinanceCategory',
        'itemFinanceCategorySub',
        'defaultUOM',
        'unitOfMeasure',
        'conversionRateUOM',
        'expenseGLAutoID',
        'expenseGLCode',
        'expenseSystemGLCode',
        'expenseGLDescription',
        'expenseGLType',
        'revenueGLAutoID',
        'revenueGLCode',
        'revenueSystemGLCode',
        'revenueGLDescription',
        'revenueGLType',
        'assetGLAutoID',
        'assetGLCode',
        'assetSystemGLCode',
        'assetGLDescription',
        'assetGLType',
        'qty',
        'price',
        'totalAmount',
        'discountPercentage',
        'discountAmount',
        'wacAmount',
        'netAmount',
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
        return GposInvoiceDetail::class;
    }
}
