<?php

namespace App\Repositories;

use App\Models\SMESystemEmployeeType;
use App\Repositories\BaseRepository;

/**
 * Class SMESystemEmployeeTypeRepository
 * @package App\Repositories
 * @version March 9, 2021, 8:42 am +04
 *
 * @method SMESystemEmployeeType findWithoutFail($id, $columns = ['*'])
 * @method SMESystemEmployeeType find($id, $columns = ['*'])
 * @method SMESystemEmployeeType first($columns = ['*'])
*/
class SMESystemEmployeeTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'employeeType'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SMESystemEmployeeType::class;
    }
}
