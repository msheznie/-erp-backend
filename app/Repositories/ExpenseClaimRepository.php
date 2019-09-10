<?php

namespace App\Repositories;

use App\helper\Helper;
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
                                    ->join('companymaster','erp_expenseclaimmaster.companyID','=','companymaster.CompanyID')
                                    ->leftJoin('erp_qry_expenseclaimstatus','erp_expenseclaimmaster.expenseClaimMasterAutoID','=','erp_qry_expenseclaimstatus.expenseClaimMasterAutoID')
                                    ->leftJoin('erp_expenseclaimtype','erp_expenseclaimmaster.pettyCashYN','=','erp_expenseclaimtype.expenseClaimTypeID')
                                    ->leftJoin('erp_qry_expenseclaimstatus_monthlyaddition','erp_qry_expenseclaimstatus_monthlyaddition.expenseClaimMasterAutoID','=','erp_expenseclaimmaster.expenseClaimMasterAutoID')
                                    ->where('erp_expenseclaimmaster.createdUserID',$emp_id)
                                    ->orderBy('erp_expenseclaimmaster.expenseClaimMasterAutoID','DESC')
                                    ->get();

    }
}
