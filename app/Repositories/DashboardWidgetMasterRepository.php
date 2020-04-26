<?php

namespace App\Repositories;

use App\Models\DashboardWidgetMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DashboardWidgetMasterRepository
 * @package App\Repositories
 * @version April 15, 2020, 4:03 pm +04
 *
 * @method DashboardWidgetMaster findWithoutFail($id, $columns = ['*'])
 * @method DashboardWidgetMaster find($id, $columns = ['*'])
 * @method DashboardWidgetMaster first($columns = ['*'])
*/
class DashboardWidgetMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'WidgetMasterName',
        'departmentID',
        'sortOrder',
        'widgetMasterIcon',
        'isActive',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DashboardWidgetMaster::class;
    }
}
