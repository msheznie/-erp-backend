<?php

namespace App\Repositories;

use App\Models\SegmentRights;
use App\Repositories\BaseRepository;

/**
 * Class SegmentRightsRepository
 * @package App\Repositories
 * @version February 20, 2020, 7:55 am +04
 *
 * @method SegmentRights findWithoutFail($id, $columns = ['*'])
 * @method SegmentRights find($id, $columns = ['*'])
 * @method SegmentRights first($columns = ['*'])
*/
class SegmentRightsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyrightsID',
        'employeeSystemID',
        'companySystemID',
        'serviceLineSystemID',
        'createdUserSystemID',
        'createdPcID',
        'createdDateTime',
        'modifiedUserSystemID',
        'modifiedPcID',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SegmentRights::class;
    }
}
