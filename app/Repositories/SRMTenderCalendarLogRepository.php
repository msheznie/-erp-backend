<?php

namespace App\Repositories;

use App\Models\SRMTenderCalendarLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SRMTenderCalendarLogRepository
 * @package App\Repositories
 * @version January 24, 2025, 7:23 am +04
 *
 * @method SRMTenderCalendarLog findWithoutFail($id, $columns = ['*'])
 * @method SRMTenderCalendarLog find($id, $columns = ['*'])
 * @method SRMTenderCalendarLog first($columns = ['*'])
*/
class SRMTenderCalendarLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'filed_description',
        'old_value',
        'new_value',
        'tender_id',
        'company_id',
        'created_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SRMTenderCalendarLog::class;
    }
}
