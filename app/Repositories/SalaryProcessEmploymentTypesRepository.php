<?php

namespace App\Repositories;

use App\Models\SalaryProcessEmploymentTypes;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SalaryProcessEmploymentTypesRepository
 * @package App\Repositories
 * @version November 7, 2018, 10:37 am UTC
 *
 * @method SalaryProcessEmploymentTypes findWithoutFail($id, $columns = ['*'])
 * @method SalaryProcessEmploymentTypes find($id, $columns = ['*'])
 * @method SalaryProcessEmploymentTypes first($columns = ['*'])
*/
class SalaryProcessEmploymentTypesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'salaryProcessID',
        'empType',
        'periodID',
        'companyID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SalaryProcessEmploymentTypes::class;
    }
}
