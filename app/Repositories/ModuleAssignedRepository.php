<?php

namespace App\Repositories;

use App\Models\ModuleAssigned;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ModuleAssignedRepository
 * @package App\Repositories
 * @version September 1, 2021, 1:59 pm +04
 *
 * @method ModuleAssigned findWithoutFail($id, $columns = ['*'])
 * @method ModuleAssigned find($id, $columns = ['*'])
 * @method ModuleAssigned first($columns = ['*'])
*/
class ModuleAssignedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'moduleID',
        'subModuleID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ModuleAssigned::class;
    }
}
