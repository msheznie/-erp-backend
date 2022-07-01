<?php

namespace App\Repositories;

use App\Models\BidSchedule;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BidScheduleRepository
 * @package App\Repositories
 * @version June 21, 2022, 2:04 pm +04
 *
 * @method BidSchedule findWithoutFail($id, $columns = ['*'])
 * @method BidSchedule find($id, $columns = ['*'])
 * @method BidSchedule first($columns = ['*'])
*/
class BidScheduleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'schedule_id',
        'bid_master_id',
        'tender_id',
        'supplier_registration_id',
        'remarks',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BidSchedule::class;
    }
}
