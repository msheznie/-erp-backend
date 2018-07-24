<?php

namespace App\Repositories;

use App\Models\EmployeeProfile;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EmployeeProfileRepository
 * @package App\Repositories
 * @version July 24, 2018, 9:16 am UTC
 *
 * @method EmployeeProfile findWithoutFail($id, $columns = ['*'])
 * @method EmployeeProfile find($id, $columns = ['*'])
 * @method EmployeeProfile first($columns = ['*'])
*/
class EmployeeProfileRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'employeeSystemID',
        'empID',
        'profileImage',
        'modifiedDate',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EmployeeProfile::class;
    }
}
