<?php

namespace App\Repositories;

use App\Models\ReportTemplateColumnLink;
use App\Repositories\BaseRepository;

/**
 * Class ReportTemplateColumnLinkRepository
 * @package App\Repositories
 * @version December 27, 2018, 10:38 am UTC
 *
 * @method ReportTemplateColumnLink findWithoutFail($id, $columns = ['*'])
 * @method ReportTemplateColumnLink find($id, $columns = ['*'])
 * @method ReportTemplateColumnLink first($columns = ['*'])
*/
class ReportTemplateColumnLinkRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'columnID',
        'templateID',
        'description',
        'shortCode',
        'type',
        'sortOrder',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportTemplateColumnLink::class;
    }
}
