<?php

namespace App\Repositories;

use App\Models\BudgetTransferFormRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetTransferFormRefferedBackRepository
 * @package App\Repositories
 * @version August 13, 2021, 2:19 pm +04
 *
 * @method BudgetTransferFormRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method BudgetTransferFormRefferedBack find($id, $columns = ['*'])
 * @method BudgetTransferFormRefferedBack first($columns = ['*'])
*/
class BudgetTransferFormRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templatesMasterAutoID',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyID',
        'budgetTransferFormAutoID',
        'serialNo',
        'year',
        'refferedBackYN',
        'transferVoucherNo',
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
        return BudgetTransferFormRefferedBack::class;
    }
}
