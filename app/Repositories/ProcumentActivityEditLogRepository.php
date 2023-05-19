<?php

namespace App\Repositories;

use App\Models\ProcumentActivityEditLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ProcumentActivityEditLogRepository
 * @package App\Repositories
 * @version April 23, 2023, 6:59 pm +04
 *
 * @method ProcumentActivityEditLog findWithoutFail($id, $columns = ['*'])
 * @method ProcumentActivityEditLog find($id, $columns = ['*'])
 * @method ProcumentActivityEditLog first($columns = ['*'])
*/
class ProcumentActivityEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'category_id',
        'company_id',
        'version_id',
        'modify_type',
        'master_id',
        'ref_log_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ProcumentActivityEditLog::class;
    }
}
