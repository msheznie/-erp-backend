<?php

namespace App\Repositories;

use App\Models\AssetDisposalReferred;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AssetDisposalReferredRepository
 * @package App\Repositories
 * @version December 6, 2018, 11:28 am UTC
 *
 * @method AssetDisposalReferred findWithoutFail($id, $columns = ['*'])
 * @method AssetDisposalReferred find($id, $columns = ['*'])
 * @method AssetDisposalReferred first($columns = ['*'])
*/
class AssetDisposalReferredRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'assetdisposalMasterAutoID',
        'companySystemID',
        'companyID',
        'toCompanySystemID',
        'toCompanyID',
        'customerID',
        'serialNo',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'documentSystemID',
        'documentID',
        'disposalDocumentCode',
        'disposalDocumentDate',
        'narration',
        'revenuePercentage',
        'confirmedYN',
        'confimedByEmpSystemID',
        'confimedByEmpID',
        'confirmedByEmpName',
        'confirmedDate',
        'approvedYN',
        'approvedByUserID',
        'approvedByUserSystemID',
        'approvedDate',
        'disposalType',
        'timesReferred',
        'refferedBackYN',
        'RollLevForApp_curr',
        'createdUserSystemID',
        'createdUserID',
        'createdDateTime',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetDisposalReferred::class;
    }
}
