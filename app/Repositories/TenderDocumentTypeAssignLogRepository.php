<?php

namespace App\Repositories;

use App\Models\TenderDocumentTypeAssignLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderDocumentTypeAssignLogRepository
 * @package App\Repositories
 * @version May 17, 2023, 12:21 pm +04
 *
 * @method TenderDocumentTypeAssignLog findWithoutFail($id, $columns = ['*'])
 * @method TenderDocumentTypeAssignLog find($id, $columns = ['*'])
 * @method TenderDocumentTypeAssignLog first($columns = ['*'])
*/
class TenderDocumentTypeAssignLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'document_type_id',
        'master_id',
        'modify_type',
        'ref_log_id',
        'tender_id',
        'version_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderDocumentTypeAssignLog::class;
    }
}
