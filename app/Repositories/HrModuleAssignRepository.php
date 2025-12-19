<?php

namespace App\Repositories;

use App\Models\HrModuleAssign;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HrModuleAssignRepository
 * @package App\Repositories
 * @version December 6, 2023, 10:25 am +04
 *
 * @method HrModuleAssign findWithoutFail($id, $columns = ['*'])
 * @method HrModuleAssign find($id, $columns = ['*'])
 * @method HrModuleAssign first($columns = ['*'])
*/
class HrModuleAssignRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'module_id',
        'company_id',
        'assign_date'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HrModuleAssign::class;
    }
}
