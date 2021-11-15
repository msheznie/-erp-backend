<?php

namespace App\Repositories;

use App\Models\SlotMasterWeekDays;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SlotMasterWeekDaysRepository
 * @package App\Repositories
 * @version November 10, 2021, 3:33 pm +04
 *
 * @method SlotMasterWeekDays findWithoutFail($id, $columns = ['*'])
 * @method SlotMasterWeekDays find($id, $columns = ['*'])
 * @method SlotMasterWeekDays first($columns = ['*'])
*/
class SlotMasterWeekDaysRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'day_id',
        'slot_master_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SlotMasterWeekDays::class;
    }
}
