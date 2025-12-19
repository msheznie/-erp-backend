<?php

namespace App\Repositories;

use App\Models\ScheduleMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ScheduleMasterRepository
 * @package App\Repositories
 * @version September 1, 2019, 11:16 am +04
 *
 * @method ScheduleMaster findWithoutFail($id, $columns = ['*'])
 * @method ScheduleMaster find($id, $columns = ['*'])
 * @method ScheduleMaster first($columns = ['*'])
*/
class ScheduleMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'scheduleCode',
        'scheduleDescription',
        'leavesEntitled',
        'noofTickets',
        'calculateCalendarDays',
        'is13MonthApplicable',
        'createDate',
        'createdUserGroup',
        'createdPCid',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ScheduleMaster::class;
    }
}
