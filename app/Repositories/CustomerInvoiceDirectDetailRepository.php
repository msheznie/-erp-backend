<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceDirectDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomerInvoiceDirectDetailRepository
 * @package App\Repositories
 * @version August 6, 2018, 10:03 am UTC
 *
 * @method CustomerInvoiceDirectDetail findWithoutFail($id, $columns = ['*'])
 * @method CustomerInvoiceDirectDetail find($id, $columns = ['*'])
 * @method CustomerInvoiceDirectDetail first($columns = ['*'])
*/
class CustomerInvoiceDirectDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'custInvoiceDirectID',
        'companyID',
        'serviceLineCode',
        'customerID',
        'glCode',
        'glCodeDes',
        'accountType',
        'comments',
        'invoiceAmountCurrency',
        'invoiceAmountCurrencyER',
        'unitOfMeasure',
        'invoiceQty',
        'unitCost',
        'invoiceAmount',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'discountLocalAmount',
        'discountAmount',
        'discountRptAmount',
        'discountRate',
        'performaMasterID',
        'clientContractID',
        'timesReferred',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerInvoiceDirectDetail::class;
    }
}
