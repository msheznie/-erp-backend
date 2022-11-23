<?php

namespace App\Repositories;

use App\Models\POSSourceSalesReturn;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSSourceSalesReturnRepository
 * @package App\Repositories
 * @version July 25, 2022, 7:58 am +04
 *
 * @method POSSourceSalesReturn findWithoutFail($id, $columns = ['*'])
 * @method POSSourceSalesReturn find($id, $columns = ['*'])
 * @method POSSourceSalesReturn first($columns = ['*'])
*/
class POSSourceSalesReturnRepository extends BaseRepository
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
        return POSSourceSalesReturn::class;
    }
}
