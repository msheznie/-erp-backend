<?php

namespace App\Repositories;

use App\Models\AdvancePaymentDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AdvancePaymentDetailsRepository
 * @package App\Repositories
 * @version August 9, 2018, 10:02 am UTC
 *
 * @method AdvancePaymentDetails findWithoutFail($id, $columns = ['*'])
 * @method AdvancePaymentDetails find($id, $columns = ['*'])
 * @method AdvancePaymentDetails first($columns = ['*'])
*/
class AdvancePaymentDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'PayMasterAutoId',
        'poAdvPaymentID',
        'companyID',
        'purchaseOrderID',
        'purchaseOrderCode',
        'comments',
        'paymentAmount',
        'amountBeforeVAT',
        'supplierTransCurrencyID',
        'supplierTransER',
        'supplierDefaultCurrencyID',
        'supplierDefaultCurrencyER',
        'localCurrencyID',
        'localER',
        'comRptCurrencyID',
        'comRptER',
        'supplierDefaultAmount',
        'supplierTransAmount',
        'localAmount',
        'comRptAmount',
        'timesReferred',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AdvancePaymentDetails::class;
    }
}
