<?php

namespace App\Repositories;

use App\Models\SegmentMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SegmentMasterRepository
 * @package App\Repositories
 * @version March 19, 2018, 10:57 am UTC
 *
 * @method SegmentMaster findWithoutFail($id, $columns = ['*'])
 * @method SegmentMaster find($id, $columns = ['*'])
 * @method SegmentMaster first($columns = ['*'])
*/
class SegmentMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'ServiceLineCode',
        'serviceLineMasterCode',
        'companyID',
        'ServiceLineDes',
        'locationID',
        'isActive',
        'isPublic',
        'isServiceLine',
        'isDepartment',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdUserSystemID',
        'modifiedUserSystemID',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SegmentMaster::class;
    }
}
