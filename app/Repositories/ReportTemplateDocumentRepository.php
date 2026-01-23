<?php

namespace App\Repositories;

use App\Models\ReportTemplateDocument;
use App\Repositories\BaseRepository;

/**
 * Class ReportTemplateDocumentRepository
 * @package App\Repositories
 * @version January 21, 2019, 9:42 am +04
 *
 * @method ReportTemplateDocument findWithoutFail($id, $columns = ['*'])
 * @method ReportTemplateDocument find($id, $columns = ['*'])
 * @method ReportTemplateDocument first($columns = ['*'])
*/
class ReportTemplateDocumentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentID',
        'isActive',
        'companySystemID',
        'companyID',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'createdDateTime',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportTemplateDocument::class;
    }
}
