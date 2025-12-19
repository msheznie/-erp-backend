<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceDirectDetRefferedback;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomerInvoiceDirectDetRefferedbackRepository
 * @package App\Repositories
 * @version November 28, 2018, 9:04 am UTC
 *
 * @method CustomerInvoiceDirectDetRefferedback findWithoutFail($id, $columns = ['*'])
 * @method CustomerInvoiceDirectDetRefferedback find($id, $columns = ['*'])
 * @method CustomerInvoiceDirectDetRefferedback first($columns = ['*'])
*/
class CustomerInvoiceDirectDetRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'custInvDirDetAutoID',
        'custInvoiceDirectID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'customerID',
        'glSystemID',
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
        'discountLocalAmount',
        'discountAmount',
        'discountRptAmount',
        'discountRate',
        'comRptAmount',
        'performaMasterID',
        'clientContractID',
        'contractID',
        'timesReferred',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerInvoiceDirectDetRefferedback::class;
    }
}
