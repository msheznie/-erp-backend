<?php

namespace App\Repositories;

use App\Models\BudgetTransferForm;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetTransferFormRepository
 * @package App\Repositories
 * @version October 17, 2018, 12:24 pm UTC
 *
 * @method BudgetTransferForm findWithoutFail($id, $columns = ['*'])
 * @method BudgetTransferForm find($id, $columns = ['*'])
 * @method BudgetTransferForm first($columns = ['*'])
*/
class BudgetTransferFormRepository extends BaseRepository
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
        'year',
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
        'RollLevForApp_curr',
        'createdDateTime',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetTransferForm::class;
    }

    public function getAudit($id)
    {
        return $this->with(['detail' => function ($query) {
            //$query->with('segment');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 46);
        }, 'company','confirmed_by', 'created_by', 'modified_by'])
            ->findWithoutFail($id);
    }
}
