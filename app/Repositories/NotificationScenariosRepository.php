<?php

namespace App\Repositories;

use App\Models\NotificationScenarios;
use App\Repositories\BaseRepository;

/**
 * Class NotificationScenariosRepository
 * @package App\Repositories
 * @version August 18, 2021, 12:40 pm +04
 *
 * @method NotificationScenarios findWithoutFail($id, $columns = ['*'])
 * @method NotificationScenarios find($id, $columns = ['*'])
 * @method NotificationScenarios first($columns = ['*'])
*/
class NotificationScenariosRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'moduleID',
        'scenarioDescription',
        'comment',
        'isActive',
        'dayCheckYN',
        'createdBy',
        'updatedBy'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return NotificationScenarios::class;
    }
}
