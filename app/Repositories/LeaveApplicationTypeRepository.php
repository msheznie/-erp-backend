<?php

namespace App\Repositories;

use App\Models\LeaveApplicationType;
use App\Repositories\BaseRepository;

/**
 * Class LeaveApplicationTypeRepository
 * @package App\Repositories
 * @version September 3, 2019, 7:59 am +04
 *
 * @method LeaveApplicationType findWithoutFail($id, $columns = ['*'])
 * @method LeaveApplicationType find($id, $columns = ['*'])
 * @method LeaveApplicationType first($columns = ['*'])
*/
class LeaveApplicationTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'Type',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LeaveApplicationType::class;
    }
}
