<?php

namespace App\Repositories;

use App\Models\AssetVerificationDetail;
use App\Repositories\BaseRepository;

/**
 * Class AssetVerificationDetailRepository
 * @package App\Repositories
 * @version June 15, 2021, 4:53 am +04
 *
 * @method AssetVerificationDetail findWithoutFail($id, $columns = ['*'])
 * @method AssetVerificationDetail find($id, $columns = ['*'])
 * @method AssetVerificationDetail first($columns = ['*'])
*/
class AssetVerificationDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'verification_id',
        'companySystemID',
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
        return AssetVerificationDetail::class;
    }
}
