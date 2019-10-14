<?php

namespace App\Repositories;

use App\Models\ChequeRegisterDetail;
use App\Models\CompanyPolicyMaster;
use App\Models\PaySupplierInvoiceMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PaySupplierInvoiceMasterRepository
 * @package App\Repositories
 * @version August 9, 2018, 9:52 am UTC
 *
 * @method PaySupplierInvoiceMaster findWithoutFail($id, $columns = ['*'])
 * @method PaySupplierInvoiceMaster find($id, $columns = ['*'])
 * @method PaySupplierInvoiceMaster first($columns = ['*'])
*/
class PaySupplierInvoiceMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'BPVcode',
        'BPVdate',
        'BPVbank',
        'BPVAccount',
        'BPVchequeNo',
        'BPVchequeDate',
        'BPVNarration',
        'BPVbankCurrency',
        'BPVbankCurrencyER',
        'directPaymentpayeeYN',
        'directPaymentPayeeSelectEmp',
        'directPaymentPayeeEmpID',
        'directPaymentPayee',
        'directPayeeCurrency',
        'directPayeeBankMemo',
        'BPVsupplierID',
        'supplierGLCode',
        'supplierTransCurrencyID',
        'supplierTransCurrencyER',
        'supplierDefCurrencyID',
        'supplierDefCurrencyER',
        'localCurrencyID',
        'localCurrencyER',
        'companyRptCurrencyID',
        'companyRptCurrencyER',
        'payAmountBank',
        'payAmountSuppTrans',
        'payAmountSuppDef',
        'payAmountCompLocal',
        'payAmountCompRpt',
        'suppAmountDocTotal',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'invoiceType',
        'matchInvoice',
        'trsCollectedYN',
        'trsCollectedByEmpID',
        'trsCollectedByEmpName',
        'trsCollectedDate',
        'trsClearedYN',
        'trsClearedDate',
        'trsClearedByEmpID',
        'trsClearedByEmpName',
        'trsClearedAmount',
        'bankClearedYN',
        'bankClearedAmount',
        'bankReconciliationDate',
        'bankClearedDate',
        'bankClearedByEmpID',
        'bankClearedByEmpName',
        'chequePaymentYN',
        'chequePrintedYN',
        'chequePrintedDateTime',
        'chequePrintedByEmpID',
        'chequePrintedByEmpName',
        'chequeSentToTreasury',
        'chequeSentToTreasuryByEmpID',
        'chequeSentToTreasuryByEmpName',
        'chequeSentToTreasuryDate',
        'chequeReceivedByTreasury',
        'chequeReceivedByTreasuryByEmpID',
        'chequeReceivedByTreasuryByEmpName',
        'chequeReceivedByTreasuryDate',
        'timesReferred',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'RollLevForApp_curr',
        'noOfApprovalLevels',
        'isRelatedPartyYN',
        'advancePaymentTypeID',
        'isPdcChequeYN',
        'finalSettlementYN',
        'expenseClaimOrPettyCash',
        'interCompanyToID',
        'ReversedYN',
        'cancelYN',
        'cancelComment',
        'cancelDate',
        'canceledByEmpID',
        'canceledByEmpName',
        'createdUserGroup',
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
        return PaySupplierInvoiceMaster::class;
    }

    public function getLastUsedChequeID($company_system_id, $bank_account_id) {
        $usedCheque = ChequeRegisterDetail::whereHas('master', function ($q) use($company_system_id,$bank_account_id) {
            $q->where('bank_account_id', $bank_account_id)
                ->where('company_id', $company_system_id);
        })
            ->where(function ($q) {
            $q->where('status', 1)  // status = 1 => used
                ->orWhere('status', 2); // // status = 2 => cancelled
        })
            ->orderBy('id', 'DESC')
            ->first();

        if(!empty($usedCheque)){
            return $usedCheque->id;
        }
        return null;
    }

    public function is_exist_policy($policy_id,$company_id) {
        $is_exist_policy = CompanyPolicyMaster::where('companySystemID',$company_id)
            ->where('companyPolicyCategoryID',$policy_id)
            ->where('isYesNO',1)
            ->first();

        if(!empty($is_exist_policy)){
            return true;
        }
        return false;
    }

    public function releaseChequeDetails($company_id, $bank_account_id, $cheque_no){

        $is_exist_policy = $this->is_exist_policy(35,$company_id);  // policy id = 35 = Get cheque number from cheque register

        if($is_exist_policy){
            $update_array = [
                'document_id' => null,
                'document_master_id' => null,
                'status' => 0,
            ];
            ChequeRegisterDetail::whereHas('master', function ($q) use($company_id,$bank_account_id) {
                    $q->where('bank_account_id', $bank_account_id)
                        ->where('company_id', $company_id);
                })
                ->where('cheque_no',$cheque_no)
                ->update($update_array);
        }

    }

}
