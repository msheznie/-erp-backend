<?php

namespace App\Repositories;

use App\Models\AdvancePaymentReferback;
use App\Repositories\BaseRepository;

/**
 * Class AdvancePaymentReferbackRepository
 * @package App\Repositories
 * @version November 21, 2018, 5:31 am UTC
 *
 * @method AdvancePaymentReferback findWithoutFail($id, $columns = ['*'])
 * @method AdvancePaymentReferback find($id, $columns = ['*'])
 * @method AdvancePaymentReferback first($columns = ['*'])
*/
class AdvancePaymentReferbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'advancePaymentDetailAutoID',
        'PayMasterAutoId',
        'poAdvPaymentID',
        'companySystemID',
        'companyID',
        'purchaseOrderID',
        'purchaseOrderCode',
        'comments',
        'paymentAmount',
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
        return AdvancePaymentReferback::class;
    }
}
