<?php

namespace App\Repositories;

use App\Models\DocumentModifyRequest;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentModifyRequestRepository
 * @package App\Repositories
 * @version March 21, 2023, 3:13 pm +04
 *
 * @method DocumentModifyRequest findWithoutFail($id, $columns = ['*'])
 * @method DocumentModifyRequest find($id, $columns = ['*'])
 * @method DocumentModifyRequest first($columns = ['*'])
*/
class DocumentModifyRequestRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'approved',
        'approved_by_user_system_id',
        'approved_date',
        'companySystemID',
        'document_master_id',
        'documentSystemCode',
        'rejected',
        'rejected_by_user_system_id',
        'rejected_date',
        'requested_date',
        'requested_document_master_id',
        'requested_employeeSystemID',
        'RollLevForApp_curr',
        'status',
        'type',
        'version'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentModifyRequest::class;
    }
}
