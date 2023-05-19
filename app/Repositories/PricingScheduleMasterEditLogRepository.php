<?php

namespace App\Repositories;

use App\Models\PricingScheduleMasterEditLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PricingScheduleMasterEditLogRepository
 * @package App\Repositories
 * @version April 5, 2023, 8:56 am +04
 *
 * @method PricingScheduleMasterEditLog findWithoutFail($id, $columns = ['*'])
 * @method PricingScheduleMasterEditLog find($id, $columns = ['*'])
 * @method PricingScheduleMasterEditLog first($columns = ['*'])
*/
class PricingScheduleMasterEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'items_mandatory',
        'modify_type',
        'price_bid_format_id',
        'schedule_mandatory',
        'scheduler_name',
        'status',
        'tender_edit_version_id',
        'tender_id',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PricingScheduleMasterEditLog::class;
    }
}
