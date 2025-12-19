<?php

namespace App\Repositories;

use App\Models\AssetCapitalization;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AssetCapitalizationRepository
 * @package App\Repositories
 * @version September 26, 2018, 7:02 am UTC
 *
 * @method AssetCapitalization findWithoutFail($id, $columns = ['*'])
 * @method AssetCapitalization find($id, $columns = ['*'])
 * @method AssetCapitalization first($columns = ['*'])
*/
class AssetCapitalizationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
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
        'assetNBVLocal',
        'assetNBVRpt',
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
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetCapitalization::class;
    }
}
