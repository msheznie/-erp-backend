<?php

namespace App\Repositories;

use App\Models\TenderDocumentTypes;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderDocumentTypesRepository
 * @package App\Repositories
 * @version June 2, 2022, 10:58 am +04
 *
 * @method TenderDocumentTypes findWithoutFail($id, $columns = ['*'])
 * @method TenderDocumentTypes find($id, $columns = ['*'])
 * @method TenderDocumentTypes first($columns = ['*'])
*/
class TenderDocumentTypesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'document_type',
        'srm_action',
        'created_by',
        'updated_by',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderDocumentTypes::class;
    }
}
