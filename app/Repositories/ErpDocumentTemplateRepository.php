<?php

namespace App\Repositories;

use App\Models\ErpDocumentTemplate;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ErpDocumentTemplateRepository
 * @package App\Repositories
 * @version January 30, 2020, 4:30 pm +04
 *
 * @method ErpDocumentTemplate findWithoutFail($id, $columns = ['*'])
 * @method ErpDocumentTemplate find($id, $columns = ['*'])
 * @method ErpDocumentTemplate first($columns = ['*'])
*/
class ErpDocumentTemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentID',
        'companyID',
        'printTemplateID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ErpDocumentTemplate::class;
    }
}
