<?php

namespace App\Repositories;

use App\Models\TenderDocumentTypeAssign;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderDocumentTypeAssignRepository
 * @package App\Repositories
 * @version July 6, 2022, 7:56 am +04
 *
 * @method TenderDocumentTypeAssign findWithoutFail($id, $columns = ['*'])
 * @method TenderDocumentTypeAssign find($id, $columns = ['*'])
 * @method TenderDocumentTypeAssign first($columns = ['*'])
*/
class TenderDocumentTypeAssignRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'document_type_id',
        'tender_id',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderDocumentTypeAssign::class;
    }
}
