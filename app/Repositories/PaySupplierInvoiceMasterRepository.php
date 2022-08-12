<?php

namespace App\Repositories;

use App\Models\ChequeRegisterDetail;
use App\Models\AdvancePaymentDetails;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PurchaseOrderDetails;
use App\Models\PoAddons;
use App\Models\ProcumentOrder;
use App\Models\CompanyPolicyMaster;
use App\Models\PaySupplierInvoiceMaster;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

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
        'interCompanyToSystemID',
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
        'timestamp',
        'payment_mode'
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

    public function paySupplierInvoiceListQuery($request, $input, $search = '', $supplierID, $projectID) {

        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $paymentVoucher = PaySupplierInvoiceMaster::with(['supplier', 'created_by', 'suppliercurrency', 'bankcurrency', 'expense_claim_type', 'paymentmode', 'project'])->whereIN('companySystemID', $subCompanies);

        if (array_key_exists('cancelYN', $input)) {
            if (($input['cancelYN'] == 0 || $input['cancelYN'] == -1) && !is_null($input['cancelYN'])) {
                $paymentVoucher->where('cancelYN', $input['cancelYN']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $paymentVoucher->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $paymentVoucher->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $paymentVoucher->whereMonth('BPVdate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $paymentVoucher->whereYear('BPVdate', '=', $input['year']);
            }
        }

        if (array_key_exists('invoiceType', $input)) {
            if ($input['invoiceType'] && !is_null($input['invoiceType'])) {
                $paymentVoucher->where('invoiceType', $input['invoiceType']);
            }
        }

        if (array_key_exists('supplierID', $input)) {
            if ($input['supplierID'] && !is_null($input['supplierID'])) {
                $paymentVoucher->whereIn('BPVsupplierID', $supplierID);
            }
        }

        if (array_key_exists('projectID', $input)) {
            if ($input['projectID'] && !is_null($input['projectID'])) {
                $paymentVoucher->whereIn('projectID', $projectID);
            }
        }

        if (array_key_exists('chequePaymentYN', $input)) {
            if (($input['chequePaymentYN'] == 0 || $input['chequePaymentYN'] == -1) && !is_null($input['chequePaymentYN'])) {
                $paymentVoucher->where('chequePaymentYN', $input['chequePaymentYN']);
            }
        }


        if (array_key_exists('BPVbank', $input)) {
            if ($input['BPVbank'] && !is_null($input['BPVbank'])) {
                $paymentVoucher->where('BPVbank', $input['BPVbank']);
            }
        }

        if (array_key_exists('BPVAccount', $input)) {
            if ($input['BPVAccount'] && !is_null($input['BPVAccount'])) {
                $paymentVoucher->where('BPVAccount', $input['BPVAccount']);
            }
        }

        if (array_key_exists('chequeSentToTreasury', $input)) {
            if (($input['chequeSentToTreasury'] == 0 || $input['chequeSentToTreasury'] == -1) && !is_null($input['chequeSentToTreasury'])) {
                $paymentVoucher->where('chequeSentToTreasury', $input['chequeSentToTreasury']);
            }
        }

        if (array_key_exists('payment_mode', $input)) {
            if (!is_null($input['payment_mode'])) {
                $paymentVoucher->where('payment_mode', $input['payment_mode']);
            }
        }


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $search_without_comma = str_replace(",", "", $search);
            $paymentVoucher = $paymentVoucher->where(function ($query) use ($search, $search_without_comma) {
                $query->where('BPVcode', 'LIKE', "%{$search}%")
                    ->orWhere('BPVNarration', 'LIKE', "%{$search}%")->orWhere('suppAmountDocTotal', 'LIKE', "%{$search_without_comma}%")->orWhere('payAmountBank', 'LIKE', "%{$search_without_comma}%")->orWhere('BPVchequeNo', 'LIKE', "%{$search_without_comma}%");
            });
        }

        return $paymentVoucher;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x]['Payment Code'] = $val->BPVcode;
                $data[$x]['PostedDate'] = $val->PostedDate;
                $data[$x]['Type'] = StatusService::getInvoiceType($val->invoiceType);
                $data[$x]['Supplier'] = $val->supplier? $val->supplier->supplierName : '';
                $data[$x]['Invoice Date'] = \Helper::dateFormat($val->BPVdate);
                $data[$x]['Cheque No'] = $val->BPVchequeNo;
                $data[$x]['Comment'] = $val->BPVNarration;
                $data[$x]['Created By'] = $val->created_by? $val->created_by->empName : '';
                $data[$x]['Created At'] = \Helper::dateFormat($val->createdDateTime);
                $data[$x]['Confirmed at'] = \Helper::dateFormat($val->confirmedDate);
                $data[$x]['Approved at'] = \Helper::dateFormat($val->approvedDate);
                $data[$x]['Supplier Currency'] = $val->suppliercurrency? $val->suppliercurrency->CurrencyCode : '';
                $data[$x]['Supplier Amount'] = number_format($val->suppAmountDocTotal, $val->suppliercurrency? $val->suppliercurrency->DecimalPlaces : 2, ".", "");
                $data[$x]['Bank Currency'] = $val->bankcurrency? $val->bankcurrency->CurrencyCode : '';
                $data[$x]['Bank Amount'] = number_format($val->payAmountBank, $val->bankcurrency? $val->bankcurrency->DecimalPlaces : 2, ".", "");
                $data[$x]['Status'] = StatusService::getStatus($val->cancelYN, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }

    public function getChequeNoForPDC($companySystemID, $bankAccount, $documentID, $documentSystemID)
    {
        $is_exist_policy_GCNFCR = CompanyPolicyMaster::where('companySystemID', $companySystemID)
                ->where('companyPolicyCategoryID', 35)
                ->where('isYesNO', 1)
                ->first();

        $nextChequeNo = null;
        $chequeRegisterAutoID = null;
        $chequeGenrated = false;
        if (!empty($is_exist_policy_GCNFCR)) {
            $chequeGenrated = true;
            $usedCheckID = $this->getLastUsedChequeID($companySystemID, $bankAccount->bankAccountAutoID);

            $unUsedCheque = ChequeRegisterDetail::whereHas('master', function ($q) use ($companySystemID, $bankAccount) {
                                                    $q->where('bank_account_id', $bankAccount->bankAccountAutoID)
                                                        ->where('company_id', $companySystemID);
                                                })
                                                ->where('status', 0)
                                                ->where(function ($q) use ($usedCheckID) {
                                                    if ($usedCheckID) {
                                                        $q->where('id', '>', $usedCheckID);
                                                    }
                                                })
                                                ->orderBy('id', 'ASC')
                                                ->first();

            if (!empty($unUsedCheque)) {
                $nextChequeNo = $unUsedCheque->cheque_no;
                $chequeRegisterAutoID = $unUsedCheque->id;

                $update_array = [
                    'document_id' => $documentID,
                    'document_master_id' => $documentSystemID,
                    'status' => 1,
                ];
                ChequeRegisterDetail::where('id', $unUsedCheque->id)->update($update_array);

            } else {
                return ['status' => false, 'message' => "Could not found unassigned cheques to generate PDC Cheques. Please add cheques to cheque registry"];
            }
        } 

        return ['status' => true, 'nextChequeNo' => $nextChequeNo, 'chequeRegisterAutoID' => $chequeRegisterAutoID, 'chequeGenrated' => $chequeGenrated];
    }

    public function validatePoPayment($purchaseOrderID, $PayMasterAutoId)
    {

        $procumentOrder = ProcumentOrder::with(['transactioncurrency'])->find($purchaseOrderID);

        if ($procumentOrder) {
            $poMasterSum = PurchaseOrderDetails::selectRaw('COALESCE(SUM(netAmount),0) as masterTotalSum')
                                                ->where('purchaseOrderMasterID', $purchaseOrderID)
                                                ->first();

            // po total vat
            $poMasterVATSum = PurchaseOrderDetails::selectRaw('COALESCE(SUM(VATAmount * noQty),0) as masterTotalVATSum')
                                                ->where('purchaseOrderMasterID', $purchaseOrderID)
                                                ->first();

            //getting addon Total for PO
            $poAddonMasterSum = PoAddons::selectRaw('COALESCE(SUM(amount),0) as addonTotalSum')
                                        ->where('poId', $purchaseOrderID)
                                        ->first();


            $poTotalAmount = $poMasterSum['masterTotalSum'] + $poAddonMasterSum['addonTotalSum'] + $poMasterVATSum['masterTotalVATSum'];

            $totalAdavancePayment =  AdvancePaymentDetails::where('purchaseOrderID', $purchaseOrderID)
                                                          ->whereHas('advancepaymentmaster', function($query) {
                                                                $query->where('cancelledYN', 0);
                                                          })
                                                          ->whereHas('pay_invoice', function($query) {
                                                                $query->where('refferedBackYN', 0);
                                                          })
                                                          ->sum('paymentAmount');


            $totalSupplierPayment = PaySupplierInvoiceDetail::where('purchaseOrderID', $purchaseOrderID)
                                                            ->where('matchingDocID',0)
                                                            ->whereHas('payment_master', function($query) {
                                                                    $query->where('refferedBackYN', 0);
                                                            })
                                                            ->sum('supplierPaymentAmount');


            $totalPayment = $totalAdavancePayment + $totalSupplierPayment;


            $decimalPlcaes = isset($procumentOrder->transactioncurrency->DecimalPlaces) ? $procumentOrder->transactioncurrency->DecimalPlaces : 2;
            $currencyCode = isset($procumentOrder->transactioncurrency->CurrencyCode) ? $procumentOrder->transactioncurrency->CurrencyCode : "USD";

            $roundedPoTotal = round($poTotalAmount, $decimalPlcaes);
            $roundedTotalPayment = round($totalPayment, $decimalPlcaes);
            if (floatval($roundedTotalPayment) > floatval($roundedPoTotal)) {
                $message = "Purchase Order ".$procumentOrder->purchaseOrderCode." will be overpaid. Purchase Order Amount : ".$currencyCode." ".number_format($poTotalAmount, $decimalPlcaes).", Supplier Payment : ".$currencyCode." ".number_format($totalSupplierPayment, $decimalPlcaes).", Advance Payment : ".$currencyCode." ".number_format($totalAdavancePayment, $decimalPlcaes);
                return ['status' => false, 'message' => $message];
            }

        }

        return ['status' => true];

    }

}
