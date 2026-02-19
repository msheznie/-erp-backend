<?php

namespace App\Repositories;

use App\Models\AssetVerification;
use App\Repositories\BaseRepository;

/**
 * Class AssetVerificationRepository
 * @package App\Repositories
 * @version June 10, 2021, 4:57 pm +04
 *
 * @method AssetVerification findWithoutFail($id, $columns = ['*'])
 * @method AssetVerification find($id, $columns = ['*'])
 * @method AssetVerification first($columns = ['*'])
*/
class AssetVerificationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentDate',
        'companySystemID',
        'verficationCode',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'narration',
        'RollLevForApp_curr',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'timesReferred',
        'refferedBackYN',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUser',
        'modifiedUserSystemID',
        'modifiedPc',
        'createdDateAndTime',
        'createdDateTime',
        'deleteComment',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetVerification::class;
    }
}
