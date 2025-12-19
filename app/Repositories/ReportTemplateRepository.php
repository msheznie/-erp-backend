<?php

namespace App\Repositories;

use App\Models\ReportTemplate;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ReportTemplateRepository
 * @package App\Repositories
 * @version December 20, 2018, 3:55 am UTC
 *
 * @method ReportTemplate findWithoutFail($id, $columns = ['*'])
 * @method ReportTemplate find($id, $columns = ['*'])
 * @method ReportTemplate first($columns = ['*'])
*/
class ReportTemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'reportID',
        'companySystemID',
        'companyID',
        'isActive',
        'isMPREnabled',
        'isAssignToGroup',
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
        return ReportTemplate::class;
    }
}
