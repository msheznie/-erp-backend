<?php

namespace App\Repositories;

use App\Models\ConsoleJVDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ConsoleJVDetailRepository
 * @package App\Repositories
 * @version March 6, 2019, 3:29 pm +04
 *
 * @method ConsoleJVDetail findWithoutFail($id, $columns = ['*'])
 * @method ConsoleJVDetail find($id, $columns = ['*'])
 * @method ConsoleJVDetail first($columns = ['*'])
*/
class ConsoleJVDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'consoleJvMasterAutoId',
        'jvDetailAutoID',
        'jvMasterAutoId',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'documentCode',
        'glDate',
        'glAccountSystemID',
        'glAccount',
        'glAccountDescription',
        'comments',
        'currencyID',
        'currencyER',
        'debitAmount',
        'creditAmount',
        'localDebitAmount',
        'rptDebitAmount',
        'localCreditAmount',
        'rptCreditAmount',
        'consoleType',
        'createdDateTime',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ConsoleJVDetail::class;
    }
}
