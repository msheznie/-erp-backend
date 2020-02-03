<?php

namespace App\Repositories;

use App\Models\LptPermission;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class LptPermissionRepository
 * @package App\Repositories
 * @version February 3, 2020, 3:01 pm +04
 *
 * @method LptPermission findWithoutFail($id, $columns = ['*'])
 * @method LptPermission find($id, $columns = ['*'])
 * @method LptPermission first($columns = ['*'])
*/
class LptPermissionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'empID',
        'employeeSystemID',
        'companyID',
        'isLPTReview',
        'isLPTClose',
        'createdBy',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LptPermission::class;
    }
}
