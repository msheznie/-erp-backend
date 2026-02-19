<?php

namespace App\Repositories;

use App\Models\CompanyFinancePeriod;
use App\Repositories\BaseRepository;

/**
 * Class CompanyFinancePeriodRepository
 * @package App\Repositories
 * @version June 12, 2018, 6:46 am UTC
 *
 * @method CompanyFinancePeriod findWithoutFail($id, $columns = ['*'])
 * @method CompanyFinancePeriod find($id, $columns = ['*'])
 * @method CompanyFinancePeriod first($columns = ['*'])
*/
class CompanyFinancePeriodRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'departmentSystemID',
        'departmentID',
        'companyFinanceYearID',
        'dateFrom',
        'dateTo',
        'isActive',
        'isCurrent',
        'isClosed',
        'closedByEmpID',
        'closedByEmpSystemID',
        'closedByEmpName',
        'closedDate',
        'comments',
        'createdUserGroup',
        'createdUserID',
        'createdPcID',
        'createdDateTime',
        'modifiedUser',
        'modifiedPc',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CompanyFinancePeriod::class;
    }
}
