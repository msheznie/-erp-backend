<?php

namespace App\Repositories;

use App\Models\BudgetMasterRefferedHistory;
use App\Repositories\BaseRepository;

/**
 * Class BudgetMasterRefferedHistoryRepository
 * @package App\Repositories
 * @version August 12, 2021, 12:44 pm +04
 *
 * @method BudgetMasterRefferedHistory findWithoutFail($id, $columns = ['*'])
 * @method BudgetMasterRefferedHistory find($id, $columns = ['*'])
 * @method BudgetMasterRefferedHistory first($columns = ['*'])
*/
class BudgetMasterRefferedHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'budgetmasterID',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyID',
        'companyFinanceYearID',
        'serviceLineSystemID',
        'serviceLineCode',
        'templateMasterID',
        'Year',
        'month',
        'generateStatus',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByEmpName',
        'confirmedDate',
        'approvedYN',
        'approvedByUserID',
        'approvedByUserSystemID',
        'approvedDate',
        'RollLevForApp_curr',
        'createdByUserSystemID',
        'createdByUserID',
        'createdDateTime',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp',
        'refferedBackYN',
        'timesReferred'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetMasterRefferedHistory::class;
    }
}
