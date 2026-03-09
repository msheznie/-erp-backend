<?php

namespace App\Repositories;

use App\Models\ERPAssetVerificationReferredback;
use App\Repositories\BaseRepository;

/**
 * Class ERPAssetVerificationReferredbackRepository
 * @package App\Repositories
 * @version August 3, 2021, 1:34 pm +04
 *
 * @method ERPAssetVerificationReferredback findWithoutFail($id, $columns = ['*'])
 * @method ERPAssetVerificationReferredback find($id, $columns = ['*'])
 * @method ERPAssetVerificationReferredback first($columns = ['*'])
*/
class ERPAssetVerificationReferredbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
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
        'confirmedByName',
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
        return ERPAssetVerificationReferredback::class;
    }
}
