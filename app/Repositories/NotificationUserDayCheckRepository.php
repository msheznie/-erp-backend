<?php

namespace App\Repositories;

use App\Models\NotificationUserDayCheck;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class NotificationUserDayCheckRepository
 * @package App\Repositories
 * @version August 18, 2021, 12:39 pm +04
 *
 * @method NotificationUserDayCheck findWithoutFail($id, $columns = ['*'])
 * @method NotificationUserDayCheck find($id, $columns = ['*'])
 * @method NotificationUserDayCheck first($columns = ['*'])
*/
class NotificationUserDayCheckRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'notificationUserID',
        'notificationDaySetupID',
        'pushNotification',
        'emailNotification',
        'webNotification',
        'createdBy',
        'updatedBy'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return NotificationUserDayCheck::class;
    }
}
