<?php

namespace App\Repositories;

use App\Models\HrmsEmployeeManager;
use App\Repositories\BaseRepository;

/**
 * Class HrmsEmployeeManagerRepository
 * @package App\Repositories
 * @version April 9, 2021, 1:25 pm +04
 *
 * @method HrmsEmployeeManager findWithoutFail($id, $columns = ['*'])
 * @method HrmsEmployeeManager find($id, $columns = ['*'])
 * @method HrmsEmployeeManager first($columns = ['*'])
*/
class HrmsEmployeeManagerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'empID',
        'managerID',
        'level',
        'active',
        'companyID',
        'createdUserID',
        'createdDate',
        'modifiedUserID',
        'modifiedDate',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HrmsEmployeeManager::class;
    }
}
