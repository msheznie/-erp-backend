<?php

namespace App\Repositories;

use App\Models\CompanyFinanceYear;
use InfyOm\Generator\Common\BaseRepository;
use Carbon\Carbon;


/**
 * Class CompanyFinanceYearRepository
 * @package App\Repositories
 * @version June 12, 2018, 6:44 am UTC
 *
 * @method CompanyFinanceYear findWithoutFail($id, $columns = ['*'])
 * @method CompanyFinanceYear find($id, $columns = ['*'])
 * @method CompanyFinanceYear first($columns = ['*'])
*/
class CompanyFinanceYearRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'bigginingDate',
        'endingDate',
        'isActive',
        'isCurrent',
        'isClosed',
        'closedByEmpSystemID',
        'closedByEmpID',
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
        return CompanyFinanceYear::class;
    }

    public function croneJobFinancialPeriodActivation(){
        $currentDate = Carbon::now()->format('Y-m-d');
        // return$financialYear = CompanyFinanceYear::where('YEAR(bigginingDate)' , 'YEAR($currentDate)')-first();
        
    }
}
