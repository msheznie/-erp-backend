<?php

namespace App\Repositories;

use App\Models\BudgetMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetMasterRepository
 * @package App\Repositories
 * @version October 16, 2018, 3:21 am UTC
 *
 * @method BudgetMaster findWithoutFail($id, $columns = ['*'])
 * @method BudgetMaster find($id, $columns = ['*'])
 * @method BudgetMaster first($columns = ['*'])
*/
class BudgetMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'companyFinanceYearID',
        'serviceLineSystemID',
        'serviceLineCode',
        'templateMasterID',
        'Year',
        'month',
        'createdByUserSystemID',
        'createdByUserID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetMaster::class;
    }
}
