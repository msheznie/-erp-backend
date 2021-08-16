<?php

namespace App\Repositories;

use App\Models\BudgetAdditionRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetAdditionRefferedBackRepository
 * @package App\Repositories
 * @version August 15, 2021, 10:14 am +04
 *
 * @method BudgetAdditionRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method BudgetAdditionRefferedBack find($id, $columns = ['*'])
 * @method BudgetAdditionRefferedBack first($columns = ['*'])
 */
class BudgetAdditionRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templatesMasterAutoID',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyFinanceYearID',
        'companyID',
        'id',
        'serialNo',
        'year',
        'additionVoucherNo',
        'createdDate',
        'comments',
        'confirmedYN',
        'confirmedDate',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByEmpName',
        'approvedYN',
        'approvedDate',
        'approvedByUserSystemID',
        'approvedEmpID',
        'approvedEmpName',
        'timesReferred',
        'RollLevForApp_curr',
        'createdDateTime',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedPc',
        'modifiedUser',
        'refferedBackYN',
        'modifiedUserSystemID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetAdditionRefferedBack::class;
    }

    public function fetchBudgetData($id)
    {
        $data = BudgetAdditionRefferedBack::with(['created_by', 'company' => function ($q) {
            $q->with(['reportingcurrency']);
        },'confirmed_by'])
            ->where('budgetAdditionRefferedBackID', $id)
            ->first();
        return $data;
    }
}
