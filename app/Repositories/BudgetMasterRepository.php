<?php

namespace App\Repositories;

use App\Models\BudgetMaster;
use App\Repositories\BaseRepository;

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

    public function getAudit($id)
    {
        return $this->with(['approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 65);
        }, 'company','confirmed_by', 'created_by', 'modified_by','audit_trial.modified_by'])
            ->findWithoutFail($id);
    }
}
