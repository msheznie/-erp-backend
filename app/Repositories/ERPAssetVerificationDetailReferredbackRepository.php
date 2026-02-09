<?php

namespace App\Repositories;

use App\Models\ERPAssetVerificationDetailReferredback;
use App\Repositories\BaseRepository;

/**
 * Class ERPAssetVerificationDetailReferredbackRepository
 * @package App\Repositories
 * @version August 3, 2021, 1:51 pm +04
 *
 * @method ERPAssetVerificationDetailReferredback findWithoutFail($id, $columns = ['*'])
 * @method ERPAssetVerificationDetailReferredback find($id, $columns = ['*'])
 * @method ERPAssetVerificationDetailReferredback first($columns = ['*'])
*/
class ERPAssetVerificationDetailReferredbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'verification_id',
        'companySystemID',
        'timesReferred',
        'faID',
        'verifiedDate',
        'narration',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUser',
        'modifiedUserSystemID',
        'modifiedPc',
        'createdDateAndTime',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ERPAssetVerificationDetailReferredback::class;
    }
}
