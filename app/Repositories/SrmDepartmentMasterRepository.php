<?php

namespace App\Repositories;

use App\Models\SrmDepartmentMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SrmDepartmentMasterRepository
 * @package App\Repositories
 * @version December 8, 2023, 10:55 am +04
 *
 * @method SrmDepartmentMaster findWithoutFail($id, $columns = ['*'])
 * @method SrmDepartmentMaster find($id, $columns = ['*'])
 * @method SrmDepartmentMaster first($columns = ['*'])
*/
class SrmDepartmentMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'is_active',
        'created_by',
        'updated_by',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SrmDepartmentMaster::class;
    }
}
