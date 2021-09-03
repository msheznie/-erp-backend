<?php

namespace App\Repositories;

use App\Models\NotificationCompanyScenario;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class NotificationCompanyScenarioRepository
 * @package App\Repositories
 * @version August 18, 2021, 12:36 pm +04
 *
 * @method NotificationCompanyScenario findWithoutFail($id, $columns = ['*'])
 * @method NotificationCompanyScenario find($id, $columns = ['*'])
 * @method NotificationCompanyScenario first($columns = ['*'])
*/
class NotificationCompanyScenarioRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'scenarioID',
        'companyID',
        'isActive',
        'createdBy',
        'updatedBy'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return NotificationCompanyScenario::class;
    }
}
