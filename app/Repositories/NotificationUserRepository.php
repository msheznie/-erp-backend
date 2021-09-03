<?php

namespace App\Repositories;

use App\Models\NotificationUser;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class NotificationUserRepository
 * @package App\Repositories
 * @version August 18, 2021, 12:39 pm +04
 *
 * @method NotificationUser findWithoutFail($id, $columns = ['*'])
 * @method NotificationUser find($id, $columns = ['*'])
 * @method NotificationUser first($columns = ['*'])
*/
class NotificationUserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'empID',
        'companyScenarionID',
        'applicableCategoryID',
        'isActive',
        'createdBy',
        'updatedBy'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return NotificationUser::class;
    }
}
