<?php

namespace App\Repositories;

use App\Models\GposInvoice;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class GposInvoiceRepository
 * @package App\Repositories
 * @version January 22, 2019, 10:02 am +04
 *
 * @method GposInvoice findWithoutFail($id, $columns = ['*'])
 * @method GposInvoice find($id, $columns = ['*'])
 * @method GposInvoice first($columns = ['*'])
*/
class GposInvoiceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'segmentID',
        'segmentCode',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'invoiceSequenceNo',
        'invoiceCode',
        'financialYearID',
        'financialPeriodID',
        'FYBegin',
        'FYEnd',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'customerID',
        'customerCode',
        'counterID',
        'shiftID',
        'memberID',
        'memberName',
        'memberContactNo',
        'memberEmail',
        'invoiceDate',
        'subTotal',
        'discountPercentage',
        'discountAmount',
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
        'giftCardID',
        'giftCardAmount',
        'cardNumber',
        'cardRefNo',
        'cardBank',
        'isCreditSales',
        'creditSalesAmount',
        'wareHouseAutoID',
        'wareHouseCode',
        'wareHouseLocation',
        'wareHouseDescription',
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
        'customerReceivableAutoID',
        'customerReceivableSystemGLCode',
        'customerReceivableGLAccount',
        'customerReceivableDescription',
        'customerReceivableType',
        'bankGLAutoID',
        'bankSystemGLCode',
        'bankGLAccount',
        'bankGLDescription',
        'bankGLType',
        'bankCurrencyID',
        'bankCurrency',
        'bankCurrencyExchangeRate',
        'bankCurrencyDecimalPlaces',
        'bankCurrencyAmount',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdUserName',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return GposInvoice::class;
    }

    public function getAudit($id)
    {
        return $this->with(['warehouse_by','created_by','company','transaction_currency','details' => function ($q) {
            $q->with('unit');
        }])->findWithoutFail($id);
    }
}
