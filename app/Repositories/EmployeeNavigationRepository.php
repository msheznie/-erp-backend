<?php

namespace App\Repositories;

use App\Models\EmployeeNavigation;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EmployeeNavigationRepository
 * @package App\Repositories
 * @version February 13, 2018, 8:59 am UTC
 *
 * @method EmployeeNavigation findWithoutFail($id, $columns = ['*'])
 * @method EmployeeNavigation find($id, $columns = ['*'])
 * @method EmployeeNavigation first($columns = ['*'])
*/
class EmployeeNavigationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'empID',
        'userGroupID',
        'companyID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EmployeeNavigation::class;
    }
}
