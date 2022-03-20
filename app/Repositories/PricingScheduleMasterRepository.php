<?php

namespace App\Repositories;

use App\Models\PricingScheduleMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PricingScheduleMasterRepository
 * @package App\Repositories
 * @version March 20, 2022, 12:57 pm +04
 *
 * @method PricingScheduleMaster findWithoutFail($id, $columns = ['*'])
 * @method PricingScheduleMaster find($id, $columns = ['*'])
 * @method PricingScheduleMaster first($columns = ['*'])
*/
class PricingScheduleMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'scheduler_name',
        'price_bid_format_id',
        'schedule_mandatory',
        'items_mandatory',
        'status',
        'created_by',
        'updated_by',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PricingScheduleMaster::class;
    }
}
