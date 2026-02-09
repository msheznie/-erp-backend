<?php

namespace App\Repositories;

use App\Models\POSInvoiceSource;
use App\Repositories\BaseRepository;

/**
 * Class POSInvoiceSourceRepository
 * @package App\Repositories
 * @version July 19, 2022, 3:12 pm +04
 *
 * @method POSInvoiceSource findWithoutFail($id, $columns = ['*'])
 * @method POSInvoiceSource find($id, $columns = ['*'])
 * @method POSInvoiceSource first($columns = ['*'])
*/
class POSInvoiceSourceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemCode',
        'documentCode',
        'serialNo',
        'invoiceSequenceNo',
        'invoiceCode',
        'isGroupBasedTax',
        'customerID',
        'customerCode',
        'counterID',
        'shiftID',
        'memberID',
        'memberName',
        'invoiceDate',
        'subTotal',
        'discountPer',
        'discountAmount',
        'generalDiscountPercentage',
        'generalDiscountAmount',
        'netTotal',
        'paidAmount',
        'balanceAmount',
        'cashAmount',
        'chequeAmount',
        'chequeNo',
        'chequeDate',
        'cardAmount',
        'creditNoteID',
        'creditNoteAmount',
        'cardNumber',
        'cardRefNo',
        'cardBank',
        'isCreditSales',
        'creditSalesAmount',
        'wareHouseAutoID',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionExchangeRate',
        'transactionCurrencyDecimalPlaces',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalExchangeRate',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingExchangeRate',
        'companyReportingCurrencyDecimalPlaces',
        'customerCurrencyID',
        'customerCurrency',
        'customerCurrencyExchangeRate',
        'customerCurrencyDecimalPlaces',
        'segmentID',
        'companyID',
        'companyCode',
        'customerReceivableAutoID',
        'bankGLAutoID',
        'bankCurrencyID',
        'bankCurrency',
        'bankCurrencyExchangeRate',
        'bankCurrencyDecimalPlaces',
        'bankCurrencyAmount',
        'isVoid',
        'voidBy',
        'voidDatetime',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdUserName',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'modifiedDateTime',
        'timestamp',
        'promotionID',
        'promotiondiscount',
        'promotiondiscountAmount',
        'isPromotion',
        'transaction_log_id',
        'mapping_master_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSInvoiceSource::class;
    }
}
