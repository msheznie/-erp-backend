<?php

namespace App\Repositories;

use App\Models\AssetCapitalizationReferred;
use App\Repositories\BaseRepository;

/**
 * Class AssetCapitalizationReferredRepository
 * @package App\Repositories
 * @version December 6, 2018, 4:41 am UTC
 *
 * @method AssetCapitalizationReferred findWithoutFail($id, $columns = ['*'])
 * @method AssetCapitalizationReferred find($id, $columns = ['*'])
 * @method AssetCapitalizationReferred first($columns = ['*'])
*/
class AssetCapitalizationReferredRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'capitalizationID',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'capitalizationCode',
        'documentDate',
        'companyFinanceYearID',
        'serialNo',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'narration',
        'allocationTypeID',
        'faCatID',
        'faID',
        'contraAccountSystemID',
        'contraAccountGLCode',
        'assetNBVLocal',
        'assetNBVRpt',
        'timesReferred',
        'refferedBackYN',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'createdDateTime',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'cancelYN',
        'cancelComment',
        'cancelDate',
        'cancelledByEmpSystemID',
        'canceledByEmpID',
        'canceledByEmpName',
        'RollLevForApp_curr',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetCapitalizationReferred::class;
    }
}
