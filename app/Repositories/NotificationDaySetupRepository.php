<?php

namespace App\Repositories;

use App\Models\NotificationDaySetup;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class NotificationDaySetupRepository
 * @package App\Repositories
 * @version August 18, 2021, 12:38 pm +04
 *
 * @method NotificationDaySetup findWithoutFail($id, $columns = ['*'])
 * @method NotificationDaySetup find($id, $columns = ['*'])
 * @method NotificationDaySetup first($columns = ['*'])
*/
class NotificationDaySetupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyScenarionID',
        'beforeAfter',
        'days',
        'isActive',
        'createdBy',
        'updatedBy'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return NotificationDaySetup::class;
    }
}
