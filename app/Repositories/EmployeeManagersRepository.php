<?php

namespace App\Repositories;

use App\Models\EmployeeManagers;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EmployeeManagersRepository
 * @package App\Repositories
 * @version September 3, 2019, 11:06 am +04
 *
 * @method EmployeeManagers findWithoutFail($id, $columns = ['*'])
 * @method EmployeeManagers find($id, $columns = ['*'])
 * @method EmployeeManagers first($columns = ['*'])
*/
class EmployeeManagersRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'empID',
        'managerID',
        'level',
        'isFunctionalManager',
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
        return EmployeeManagers::class;
    }
}
