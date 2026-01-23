<?php

namespace App\Repositories;

use App\Models\CustomReportEmployees;
use App\Repositories\BaseRepository;

/**
 * Class CustomReportEmployeesRepository
 * @package App\Repositories
 * @version August 21, 2020, 1:32 pm +04
 *
 * @method CustomReportEmployees findWithoutFail($id, $columns = ['*'])
 * @method CustomReportEmployees find($id, $columns = ['*'])
 * @method CustomReportEmployees first($columns = ['*'])
*/
class CustomReportEmployeesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'report_master_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomReportEmployees::class;
    }
}
