<?php

namespace App\Repositories;

use App\Models\ExpenseClaim;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ExpenseClaimRepository
 * @package App\Repositories
 * @version September 10, 2018, 6:02 am UTC
 *
 * @method ExpenseClaim findWithoutFail($id, $columns = ['*'])
 * @method ExpenseClaim find($id, $columns = ['*'])
 * @method ExpenseClaim first($columns = ['*'])
*/
class ExpenseClaimRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'departmentID',
        'documentID',
        'serialNo',
        'expenseClaimCode',
        'expenseClaimDate',
        'clamiedByName',
        'comments',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'glCodeAssignedYN',
        'addedForPayment',
        'rejectedYN',
        'rejectedComment',
        'seniorManager',
        'pettyCashYN',
        'addedToSalary',
        'createdUserID',
        'createdPcID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ExpenseClaim::class;
    }

    public function getAudit($id)
    {
        return $this->with(['created_by', 'confirmed_by', 'modified_by', 'company.localcurrency', 'details' => function ($q) {
            $q->with(['segment','chart_of_account','currency','local_currency','category']);
        }, 'approved_by' => function ($query) {
            $query->with(['employee' => function ($q) {
                $q->with(['details.designation']);
            }])->where('documentSystemID', 6);
        }])->findWithoutFail($id);
    }
}
