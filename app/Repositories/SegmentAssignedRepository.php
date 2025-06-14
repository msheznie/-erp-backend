<?php

namespace App\Repositories;

use App\Models\SegmentAssigned;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SegmentAssignedRepository
 * @package App\Repositories
 * @version June 13, 2025, 10:36 am +04
 *
 * @method SegmentAssigned findWithoutFail($id, $columns = ['*'])
 * @method SegmentAssigned find($id, $columns = ['*'])
 * @method SegmentAssigned first($columns = ['*'])
*/
class SegmentAssignedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'serviceLineSystemID',
        'companySystemID',
        'isActive',
        'isAssigned'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SegmentAssigned::class;
    }
}
