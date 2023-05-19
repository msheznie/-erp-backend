<?php

namespace App\Repositories;

use App\Models\CalendarDatesDetailEditLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CalendarDatesDetailEditLogRepository
 * @package App\Repositories
 * @version April 21, 2023, 1:19 pm +04
 *
 * @method CalendarDatesDetailEditLog findWithoutFail($id, $columns = ['*'])
 * @method CalendarDatesDetailEditLog find($id, $columns = ['*'])
 * @method CalendarDatesDetailEditLog first($columns = ['*'])
*/
class CalendarDatesDetailEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'calendar_date_id',
        'company_id',
        'from_date',
        'master_id',
        'modify_type',
        'ref_log_id',
        'tender_id',
        'to_date',
        'version_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CalendarDatesDetailEditLog::class;
    }
}
