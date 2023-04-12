<?php

namespace App\Repositories;

use App\Models\TenderCircularsEditLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderCircularsEditLogRepository
 * @package App\Repositories
 * @version April 11, 2023, 11:53 am +04
 *
 * @method TenderCircularsEditLog findWithoutFail($id, $columns = ['*'])
 * @method TenderCircularsEditLog find($id, $columns = ['*'])
 * @method TenderCircularsEditLog first($columns = ['*'])
*/
class TenderCircularsEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'attachment_id',
        'circular_name',
        'company_id',
        'description',
        'master_id',
        'modify_type',
        'ref_log_id',
        'status',
        'tender_id',
        'vesion_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderCircularsEditLog::class;
    }
}
