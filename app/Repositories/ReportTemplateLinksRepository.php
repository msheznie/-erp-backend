<?php

namespace App\Repositories;

use App\Models\ReportTemplateLinks;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ReportTemplateLinksRepository
 * @package App\Repositories
 * @version December 20, 2018, 4:04 am UTC
 *
 * @method ReportTemplateLinks findWithoutFail($id, $columns = ['*'])
 * @method ReportTemplateLinks find($id, $columns = ['*'])
 * @method ReportTemplateLinks first($columns = ['*'])
*/
class ReportTemplateLinksRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templateMasterID',
        'templateDetailID',
        'sortOrder',
        'glAutoID',
        'subCategory',
        'companySystemID',
        'companyID',
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
        return ReportTemplateLinks::class;
    }
}
