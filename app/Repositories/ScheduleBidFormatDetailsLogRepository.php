<?php

namespace App\Repositories;

use App\Models\ScheduleBidFormatDetailsLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ScheduleBidFormatDetailsLogRepository
 * @package App\Repositories
 * @version April 6, 2023, 2:00 pm +04
 *
 * @method ScheduleBidFormatDetailsLog findWithoutFail($id, $columns = ['*'])
 * @method ScheduleBidFormatDetailsLog find($id, $columns = ['*'])
 * @method ScheduleBidFormatDetailsLog first($columns = ['*'])
*/
class ScheduleBidFormatDetailsLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bid_format_detail_id',
        'bid_master_id',
        'company_id',
        'master_id',
        'modify_type',
        'red_log_id',
        'schedule_id',
        'tender_edit_version_id',
        'value'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ScheduleBidFormatDetailsLog::class;
    }
}
