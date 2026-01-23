<?php

namespace App\Repositories;

use App\Models\JvDetail;
use App\Repositories\BaseRepository;

/**
 * Class JvDetailRepository
 * @package App\Repositories
 * @version September 25, 2018, 1:05 pm UTC
 *
 * @method JvDetail findWithoutFail($id, $columns = ['*'])
 * @method JvDetail find($id, $columns = ['*'])
 * @method JvDetail first($columns = ['*'])
*/
class JvDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'jvMasterAutoId',
        'documentSystemID',
        'documentID',
        'recurringjvMasterAutoId',
        'recurringjvDetailAutoID',
        'recurringMonth',
        'serviceLineSystemID',
        'serviceLineCode',
        'companySystemID',
        'companyID',
        'chartOfAccountSystemID',
        'glAccount',
        'glAccountDescription',
        'referenceGLCode',
        'referenceGLDescription',
        'comments',
        'clientContractID',
        'currencyID',
        'currencyER',
        'debitAmount',
        'creditAmount',
        'timesReferred',
        'companyIDForConsole',
        'selectedForConsole',
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
        return JvDetail::class;
    }
}
