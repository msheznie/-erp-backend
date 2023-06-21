<?php

namespace App\Repositories;

use App\Models\PricingScheduleDetailEditLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PricingScheduleDetailEditLogRepository
 * @package App\Repositories
 * @version April 5, 2023, 8:58 am +04
 *
 * @method PricingScheduleDetailEditLog findWithoutFail($id, $columns = ['*'])
 * @method PricingScheduleDetailEditLog find($id, $columns = ['*'])
 * @method PricingScheduleDetailEditLog first($columns = ['*'])
*/
class PricingScheduleDetailEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bid_format_detail_id',
        'bid_format_id',
        'boq_applicable',
        'company_id',
        'created_by',
        'deleted_by',
        'description',
        'field_type',
        'formula_string',
        'is_disabled',
        'label',
        'modify_type',
        'pricing_schedule_master_id',
        'tender_edit_version_id',
        'tender_id',
        'tender_ranking_line_item',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PricingScheduleDetailEditLog::class;
    }
}
