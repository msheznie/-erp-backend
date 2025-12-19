<?php

namespace App\Repositories;

use App\Models\DebitNoteDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DebitNoteDetailsRepository
 * @package App\Repositories
 * @version August 16, 2018, 10:17 am UTC
 *
 * @method DebitNoteDetails findWithoutFail($id, $columns = ['*'])
 * @method DebitNoteDetails find($id, $columns = ['*'])
 * @method DebitNoteDetails first($columns = ['*'])
*/
class DebitNoteDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'debitNoteAutoID',
        'companyID',
        'serviceLineCode',
        'contractID',
        'supplierID',
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
        return DebitNoteDetails::class;
    }
}
