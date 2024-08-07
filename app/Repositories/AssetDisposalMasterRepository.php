<?php

namespace App\Repositories;

use App\Models\AssetDisposalMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AssetDisposalMasterRepository
 * @package App\Repositories
 * @version September 28, 2018, 10:05 am UTC
 *
 * @method AssetDisposalMaster findWithoutFail($id, $columns = ['*'])
 * @method AssetDisposalMaster find($id, $columns = ['*'])
 * @method AssetDisposalMaster first($columns = ['*'])
*/
class AssetDisposalMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'toCompanySystemID',
        'toCompanyID',
        'customerID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'documentSystemID',
        'documentID',
        'disposalDocumentCode',
        'disposalDocumentDate',
        'narration',
        'vatRegisteredYN',
        'confirmedYN',
        'confimedByEmpSystemID',
        'confimedByEmpID',
        'confirmedByEmpName',
        'confirmedDate',
        'approvedYN',
        'approvedDate',
        'disposalType',
        'createdUserID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetDisposalMaster::class;
    }
}
