<?php

namespace App\Repositories;

use App\Models\POSSTAGInvoice;
use App\Repositories\BaseRepository;

/**
 * Class POSSTAGInvoiceRepository
 * @package App\Repositories
 * @version July 18, 2022, 10:46 am +04
 *
 * @method POSSTAGInvoice findWithoutFail($id, $columns = ['*'])
 * @method POSSTAGInvoice find($id, $columns = ['*'])
 * @method POSSTAGInvoice first($columns = ['*'])
*/
class POSSTAGInvoiceRepository extends BaseRepository
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
        'isPromotion'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSSTAGInvoice::class;
    }
}
