<?php

namespace App\Repositories;

use App\Models\CreditNoteDetails;
use App\Repositories\BaseRepository;

/**
 * Class CreditNoteDetailsRepository
 * @package App\Repositories
 * @version August 21, 2018, 10:00 am UTC
 *
 * @method CreditNoteDetails findWithoutFail($id, $columns = ['*'])
 * @method CreditNoteDetails find($id, $columns = ['*'])
 * @method CreditNoteDetails first($columns = ['*'])
*/
class CreditNoteDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'creditNoteAutoID',
        'companyID',
        'customerID',
        'chartOfAccountSystemID',
        'glCode',
        'glCodeDes',
        'serviceLineCode',
        'clientContractID',
        'comments',
        'creditAmountCurrency',
        'creditAmountCurrencyER',
        'creditAmount',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'budgetYear',
        'timesReferred',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CreditNoteDetails::class;
    }
}
