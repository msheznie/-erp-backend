<?php

namespace App\Repositories;

use App\Models\ContingencyBudgetPlan;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ContingencyBudgetPlanRepository
 * @package App\Repositories
 * @version June 29, 2021, 2:17 pm +04
 *
 * @method ContingencyBudgetPlan findWithoutFail($id, $columns = ['*'])
 * @method ContingencyBudgetPlan find($id, $columns = ['*'])
 * @method ContingencyBudgetPlan first($columns = ['*'])
*/
class ContingencyBudgetPlanRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyID',
        'serialNo',
        'currencyID',
        'contigencyAmount',
        'year',
        'segmentID',
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
        'modifiedUserSystemID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ContingencyBudgetPlan::class;
    }
}
