<?php

namespace App\Repositories;

use App\Models\ReportTemplateDetails;
use App\Repositories\BaseRepository;

/**
 * Class ReportTemplateDetailsRepository
 * @package App\Repositories
 * @version December 20, 2018, 4:03 am UTC
 *
 * @method ReportTemplateDetails findWithoutFail($id, $columns = ['*'])
 * @method ReportTemplateDetails find($id, $columns = ['*'])
 * @method ReportTemplateDetails first($columns = ['*'])
*/
class ReportTemplateDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyReportTemplateID',
        'description',
        'itemType',
        'sortOrder',
        'masterID',
        'accountType',
        'companySystemID',
        'netProfitStatus',
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
        return ReportTemplateDetails::class;
    }
}
