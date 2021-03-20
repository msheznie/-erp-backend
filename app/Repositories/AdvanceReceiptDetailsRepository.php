<?php

namespace App\Repositories;

use App\Models\AdvanceReceiptDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AdvanceReceiptDetailsRepository
 * @package App\Repositories
 * @version January 22, 2021, 2:42 pm +04
 *
 * @method AdvanceReceiptDetails findWithoutFail($id, $columns = ['*'])
 * @method AdvanceReceiptDetails find($id, $columns = ['*'])
 * @method AdvanceReceiptDetails first($columns = ['*'])
*/
class AdvanceReceiptDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'custReceivePaymentAutoID',
        'soAdvPaymentID',
        'companySystemID',
        'companyID',
        'salesOrderID',
        'salesOrderCode',
        'comments',
        'paymentAmount',
        'customerTransCurrencyID',
        'customerTransER',
        'customerDefaultCurrencyID',
        'customerDefaultCurrencyER',
        'localCurrencyID',
        'localER',
        'comRptCurrencyID',
        'comRptER',
        'supplierDefaultAmount',
        'supplierTransAmount',
        'localAmount',
        'comRptAmount',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'timesReferred',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AdvanceReceiptDetails::class;
    }
}
