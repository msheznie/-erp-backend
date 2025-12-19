<?php

namespace App\Repositories;

use App\Models\CreditNoteDetailsRefferdback;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CreditNoteDetailsRefferdbackRepository
 * @package App\Repositories
 * @version November 26, 2018, 11:09 am UTC
 *
 * @method CreditNoteDetailsRefferdback findWithoutFail($id, $columns = ['*'])
 * @method CreditNoteDetailsRefferdback find($id, $columns = ['*'])
 * @method CreditNoteDetailsRefferdback first($columns = ['*'])
*/
class CreditNoteDetailsRefferdbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'creditNoteDetailsID',
        'creditNoteAutoID',
        'companyID',
        'customerID',
        'glCode',
        'glCodeDes',
        'serviceLineCode',
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
        'timesReferred',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CreditNoteDetailsRefferdback::class;
    }
}
