<?php

namespace App\Repositories;

use App\Models\PricingScheduleDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PricingScheduleDetailRepository
 * @package App\Repositories
 * @version September 21, 2022, 12:09 pm +04
 *
 * @method PricingScheduleDetail findWithoutFail($id, $columns = ['*'])
 * @method PricingScheduleDetail find($id, $columns = ['*'])
 * @method PricingScheduleDetail first($columns = ['*'])
*/
class PricingScheduleDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bid_format_id',
        'boq_applicable',
        'created_by',
        'field_type',
        'formula_string',
        'is_disabled',
        'label',
        'pricing_schedule_master_id',
        'tender_id',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PricingScheduleDetail::class;
    }

    public function getPricingScheduleDetailForAmd($tender_id){
        return $this->model->getPricingScheduleDetailForAmd($tender_id);
    }
}
