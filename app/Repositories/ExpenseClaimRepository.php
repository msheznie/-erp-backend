<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\ExpenseClaim;
use App\Models\ExpenseClaimMaster;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

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
        },'audit_trial.modified_by'])->findWithoutFail($id);
    }

    public function getClaimFullHistory()
    {
        $emp_id = Helper::getEmployeeID();
        return ExpenseClaim::selectRaw('
                                    erp_expenseclaimmaster.expenseClaimMasterAutoID AS expenseClaimMasterAutoID,
                                    erp_expenseclaimmaster.expenseClaimDate AS expenseClaimDate,
                                    erp_expenseclaimmaster.expenseClaimCode AS expenseClaimCode,
                                    erp_expenseclaimtype.expenseClaimTypeDescription AS expenseClaimTypeDescription,
                                    erp_expenseclaimmaster.comments AS comments,
                                    erp_expenseclaimmaster.confirmedYN AS confirmedYN,
                                    erp_expenseclaimmaster.approved AS approved,
                                    erp_expenseclaimmaster.addedForPayment AS addedForPayment,
                                    IF(( `erp_expenseclaimmaster`.`confirmedYN` = 1 ), 1, 0)  AS `myConfirmed`,
	                                IF( ( ( `erp_qry_expenseclaimstatus`.`confirmedYN` = 1 ) OR ( `erp_qry_expenseclaimstatus_monthlyaddition`.`confirmedYN` = 1 ) ), 1, 0 ) AS `paymentConfirmed`,
                                    IF(( ( `erp_qry_expenseclaimstatus`.`approved` = - ( 1 ) ) OR ( `erp_qry_expenseclaimstatus_monthlyaddition`.`approvedYN` = - ( 1 ) ) ),- ( 1 ),0) AS `paymentApproved`')
                                    ->leftJoin('erp_qry_expenseclaimstatus','erp_expenseclaimmaster.expenseClaimMasterAutoID','=','erp_qry_expenseclaimstatus.expenseClaimMasterAutoID')
                                    ->leftJoin('erp_expenseclaimtype','erp_expenseclaimmaster.pettyCashYN','=','erp_expenseclaimtype.expenseClaimTypeID')
                                    ->leftJoin('erp_qry_expenseclaimstatus_monthlyaddition','erp_qry_expenseclaimstatus_monthlyaddition.expenseClaimMasterAutoID','=','erp_expenseclaimmaster.expenseClaimMasterAutoID')
                                    ->where('erp_expenseclaimmaster.createdUserID',$emp_id)
                                    ->groupBy('erp_expenseclaimmaster.expenseClaimMasterAutoID')
                                    ->orderBy('erp_expenseclaimmaster.expenseClaimMasterAutoID','DESC')
                                    ->paginate(50);

    }

    public function expenseClaimListQuery($request, $input, $search = '') {

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

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $expenseClaims = $expenseClaims->where('approved', $input['approved']);
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

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][__('custom.expense_claim_date')] = \Helper::dateFormat($val->expenseClaimDate);
                $data[$x][__('custom.document_code')] = $val->expenseClaimCode;
                $data[$x][__('custom.comments')] = $val->comments;
                $data[$x][__('custom.created_by')] = $val->created_by? $val->created_by->empName : '';
                $data[$x][__('custom.status')] = StatusService::getStatus($val->canceledYN, NULL, $val->confirmedYN, $val->approved, $val->timesReferred);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
