<?php

namespace App\Repositories;

use App\Models\ReportTemplateEmployees;
use App\Repositories\BaseRepository;

/**
 * Class ReportTemplateEmployeesRepository
 * @package App\Repositories
 * @version February 1, 2019, 3:26 pm +04
 *
 * @method ReportTemplateEmployees findWithoutFail($id, $columns = ['*'])
 * @method ReportTemplateEmployees find($id, $columns = ['*'])
 * @method ReportTemplateEmployees first($columns = ['*'])
*/
class ReportTemplateEmployeesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyReportTemplateID',
        'userGroupID',
        'employeeSystemID',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportTemplateEmployees::class;
    }
}
