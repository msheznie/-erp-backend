<?php

namespace App\Repositories;

use App\Models\ExpenseClaimMaster;
use App\Repositories\BaseRepository;

/**
 * Class ExpenseClaimMasterRepository
 * @package App\Repositories
 * @version January 5, 2022, 12:52 pm +04
 *
 * @method ExpenseClaimMaster findWithoutFail($id, $columns = ['*'])
 * @method ExpenseClaimMaster find($id, $columns = ['*'])
 * @method ExpenseClaimMaster first($columns = ['*'])
*/
class ExpenseClaimMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentID',
        'serialNo',
        'expenseClaimCode',
        'expenseClaimDate',
        'claimedByEmpID',
        'claimedByEmpName',
        'comments',
        'isCRM',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedByEmpID',
        'approvedByEmpName',
        'approvedDate',
        'approvalComments',
        'glCodeAssignedYN',
        'addedForPayment',
        'addedToSalary',
        'companyID',
        'companyCode',
        'segmentID',
        'segmentCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ExpenseClaimMaster::class;
    }

    public function getAudit($id)
    {
        return $this->with(['created_by', 'confirmed_by', 'modified_by', 'company.localcurrency', 'details' => function ($q) {
            $q->with(['segment','chart_of_account','currency','local_currency','category']);
        }, 'approved_by' => function ($query) {
            $query->with(['employee' => function ($q) {
                $q->with(['details.designation']);
            }])->where('documentID', 'EC');
        },'audit_trial.modified_by'])->findWithoutFail($id);
    }

    public function expenseClaimMasterListQuery($request, $input, $search = '') {
        
        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $expenseClaims = ExpenseClaimMaster::whereIn('companyID', $subCompanies)
        ->with('created_by');

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $expenseClaims = $expenseClaims->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == 1) && !is_null($input['approvedYN'])) {
                $expenseClaims = $expenseClaims->where('approvedYN', $input['approvedYN']);
            }
        }

        if (array_key_exists('glCodeAssignedYN', $input)) {
            if (($input['glCodeAssignedYN'] == 0 || $input['glCodeAssignedYN'] == -1) && !is_null($input['glCodeAssignedYN'])) {
                $expenseClaims = $expenseClaims->where('glCodeAssignedYN', '=', $input['glCodeAssignedYN']);
            }
        }


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $expenseClaims = $expenseClaims->where(function ($query) use ($search) {
                $query->where('expenseClaimCode', 'LIKE', "%{$search}%");
            });
        }

        return $expenseClaims;
    }

    // public function getAudit($id)
    // {
    //     return $this->findWithoutFail($id);
    // }
}
