<?php

namespace App\Repositories;

use App\Models\DebitNoteDetailsRefferedback;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DebitNoteDetailsRefferedbackRepository
 * @package App\Repositories
 * @version December 3, 2018, 4:57 am UTC
 *
 * @method DebitNoteDetailsRefferedback findWithoutFail($id, $columns = ['*'])
 * @method DebitNoteDetailsRefferedback find($id, $columns = ['*'])
 * @method DebitNoteDetailsRefferedback first($columns = ['*'])
*/
class DebitNoteDetailsRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'debitNoteDetailsID',
        'debitNoteAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'contractID',
        'supplierID',
        'chartOfAccountSystemID',
        'glCode',
        'glCodeDes',
        'comments',
        'debitAmountCurrency',
        'debitAmountCurrencyER',
        'debitAmount',
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
        return DebitNoteDetailsRefferedback::class;
    }
}
