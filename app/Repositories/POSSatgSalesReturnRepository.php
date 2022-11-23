<?php

namespace App\Repositories;

use App\Models\POSSatgSalesReturn;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSSatgSalesReturnRepository
 * @package App\Repositories
 * @version July 25, 2022, 7:57 am +04
 *
 * @method POSSatgSalesReturn findWithoutFail($id, $columns = ['*'])
 * @method POSSatgSalesReturn find($id, $columns = ['*'])
 * @method POSSatgSalesReturn first($columns = ['*'])
*/
class POSSatgSalesReturnRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'invoiceID',
        'documentSystemCode',
        'documentCode',
        'serialNo',
        'customerID',
        'customerCode',
        'counterID',
        'shiftID',
        'salesReturnDate',
        'discountPer',
        'discountAmount',
        'generalDiscountPercentage',
        'generalDiscountAmount',
        'promotionID',
        'promotiondiscount',
        'promotiondiscountAmount',
        'subTotal',
        'netTotal',
        'returnMode',
        'isRefund',
        'refundAmount',
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
        'customerCurrencyAmount',
        'customerCurrencyDecimalPlaces',
        'segmentID',
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
        'customerReceivableAutoID',
        'timestamp',
        'isGroupBasedTax',
        'transaction_log_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSSatgSalesReturn::class;
    }
}
