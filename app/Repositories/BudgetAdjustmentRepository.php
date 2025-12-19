<?php

namespace App\Repositories;

use App\Models\BudgetAdjustment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetAdjustmentRepository
 * @package App\Repositories
 * @version October 22, 2018, 8:27 am UTC
 *
 * @method BudgetAdjustment findWithoutFail($id, $columns = ['*'])
 * @method BudgetAdjustment find($id, $columns = ['*'])
 * @method BudgetAdjustment first($columns = ['*'])
*/
class BudgetAdjustmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyId',
        'companyFinanceYearID',
        'serviceLineSystemID',
        'serviceLine',
        'adjustedGLCodeSystemID',
        'adjustedGLCode',
        'fromGLCodeSystemID',
        'fromGLCode',
        'toGLCodeSystemID',
        'toGLCode',
        'Year',
        'adjustmedLocalAmount',
        'adjustmentRptAmount',
        'createdUserSystemID',
        'createdByUserID',
        'modifiedUserSystemID',
        'modifiedByUserID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetAdjustment::class;
    }
}
