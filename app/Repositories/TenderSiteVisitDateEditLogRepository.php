<?php

namespace App\Repositories;

use App\Models\TenderSiteVisitDateEditLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderSiteVisitDateEditLogRepository
 * @package App\Repositories
 * @version June 13, 2025, 3:05 pm +04
 *
 * @method TenderSiteVisitDateEditLog findWithoutFail($id, $columns = ['*'])
 * @method TenderSiteVisitDateEditLog find($id, $columns = ['*'])
 * @method TenderSiteVisitDateEditLog first($columns = ['*'])
*/
class TenderSiteVisitDateEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'date',
        'id',
        'is_deleted',
        'level_no',
        'tender_id',
        'version_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderSiteVisitDateEditLog::class;
    }
}
