<?php

namespace App\Repositories;

use App\Models\PricingScheduleDetail;
use App\Models\ScheduleBidFormatDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ScheduleBidFormatDetailsRepository
 * @package App\Repositories
 * @version March 21, 2022, 5:06 pm +04
 *
 * @method ScheduleBidFormatDetails findWithoutFail($id, $columns = ['*'])
 * @method ScheduleBidFormatDetails find($id, $columns = ['*'])
 * @method ScheduleBidFormatDetails first($columns = ['*'])
*/
class ScheduleBidFormatDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bid_format_detail_id',
        'schedule_id',
        'value',
        'created_by',
        'updated_by',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ScheduleBidFormatDetails::class;
    }

    public function getScheduleBidFormatForAmd($tender_id){
        $schedules = PricingScheduleDetail::getPricingScheduleDetailForAmd($tender_id);
        $pluckIds = collect($schedules)->pluck('id')->toArray();
        return $this->model->getScheduleBidFormatForAmd($pluckIds);
    }
}
