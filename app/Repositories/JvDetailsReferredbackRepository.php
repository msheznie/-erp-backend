<?php

namespace App\Repositories;

use App\Models\JvDetailsReferredback;
use App\Repositories\BaseRepository;

/**
 * Class JvDetailsReferredbackRepository
 * @package App\Repositories
 * @version December 5, 2018, 5:35 am UTC
 *
 * @method JvDetailsReferredback findWithoutFail($id, $columns = ['*'])
 * @method JvDetailsReferredback find($id, $columns = ['*'])
 * @method JvDetailsReferredback first($columns = ['*'])
*/
class JvDetailsReferredbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'jvDetailAutoID',
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
        return JvDetailsReferredback::class;
    }
}
