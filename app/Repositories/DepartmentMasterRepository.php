<?php

namespace App\Repositories;

use App\Models\DepartmentMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DepartmentMasterRepository
 * @package App\Repositories
 * @version March 22, 2018, 2:40 pm UTC
 *
 * @method DepartmentMaster findWithoutFail($id, $columns = ['*'])
 * @method DepartmentMaster find($id, $columns = ['*'])
 * @method DepartmentMaster first($columns = ['*'])
*/
class DepartmentMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'DepartmentID',
        'DepartmentDescription',
        'isActive',
        'depImage',
        'masterLevel',
        'companyLevel',
        'listOrder',
        'isReport',
        'ReportMenu',
        'menuInitialImage',
        'menuInitialSelectedImage',
        'showInCombo',
        'hrLeaveApprovalLevels',
        'managerfield',
        'isFunctionalDepartment',
        'isReportGroupYN',
        'hrObjectiveSetting',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DepartmentMaster::class;
    }
}
