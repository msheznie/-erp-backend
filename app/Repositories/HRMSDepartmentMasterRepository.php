<?php

namespace App\Repositories;

use App\Models\HRMSDepartmentMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HRMSDepartmentMasterRepository
 * @package App\Repositories
 * @version November 12, 2018, 5:34 am UTC
 *
 * @method HRMSDepartmentMaster findWithoutFail($id, $columns = ['*'])
 * @method HRMSDepartmentMaster find($id, $columns = ['*'])
 * @method HRMSDepartmentMaster first($columns = ['*'])
*/
class HRMSDepartmentMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'DepartmentDescription',
        'isActive',
        'ServiceLineCode',
        'CompanyID',
        'showInCombo',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HRMSDepartmentMaster::class;
    }
}
