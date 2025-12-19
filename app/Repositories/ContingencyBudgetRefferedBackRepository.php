<?php

namespace App\Repositories;

use App\Models\ContingencyBudgetRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ContingencyBudgetRefferedBackRepository
 * @package App\Repositories
 * @version August 15, 2021, 2:34 pm +04
 *
 * @method ContingencyBudgetRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method ContingencyBudgetRefferedBack find($id, $columns = ['*'])
 * @method ContingencyBudgetRefferedBack first($columns = ['*'])
*/
class ContingencyBudgetRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'ID',
        'documentSystemID',
        'companyFinanceYearID',
        'documentID',
        'companySystemID',
        'companyID',
        'serialNo',
        'contingencyBudgetNo',
        'currencyID',
        'contigencyAmount',
        'year',
        'serviceLineSystemID',
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
        'refferedBackYN',
        'RollLevForApp_curr',
        'createdDateTime',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedPc',
        'modifiedUser',
        'modifiedUserSystemID',
        'timestamp',
        'budgetID',
        'templateMasterID',
        'contingencyPercentage',
        'budgetAmount'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ContingencyBudgetRefferedBack::class;
    }
}
