<?php

namespace App\Repositories;

use App\Models\SrmTenderBidEmployeeDetailsEditLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SrmTenderBidEmployeeDetailsEditLogRepository
 * @package App\Repositories
 * @version April 4, 2023, 1:00 pm +04
 *
 * @method SrmTenderBidEmployeeDetailsEditLog findWithoutFail($id, $columns = ['*'])
 * @method SrmTenderBidEmployeeDetailsEditLog find($id, $columns = ['*'])
 * @method SrmTenderBidEmployeeDetailsEditLog first($columns = ['*'])
*/
class SrmTenderBidEmployeeDetailsEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'commercial_eval_remarks',
        'commercial_eval_status',
        'emp_id',
        'modify_type',
        'remarks',
        'status',
        'tender_award_commite_mem_comment',
        'tender_award_commite_mem_status',
        'tender_edit_version_id',
        'tender_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SrmTenderBidEmployeeDetailsEditLog::class;
    }
}
