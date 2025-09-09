<?php

namespace App\Repositories;

use App\Models\ChequeRegister;
use App\Models\ChequeRegisterDetail;
use App\Models\AdvancePaymentDetails;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PurchaseOrderDetails;
use App\Models\PoAdvancePayment;
use App\Models\PoAddons;
use App\Models\ProcumentOrder;
use App\Models\CurrencyMaster;
use App\Models\DirectInvoiceDetails;
use App\Models\CompanyPolicyMaster;
use App\Models\Company;
use App\Models\PaySupplierInvoiceMaster;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;
use Illuminate\Http\Request;
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
                ->where('company_id', $company_system_id)
                ->where('isActive', 1);
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

        /*
        * Updating without the policy check - GCP-5459
        * */
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

    public function paySupplierInvoiceListQuery($request, $input, $search = '', $supplierID, $projectID, $employeeID,$createdBy) {

        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $paymentVoucher = PaySupplierInvoiceMaster::with(['supplier', 'created_by', 'suppliercurrency', 'bankcurrency', 'expense_claim_type', 'paymentmode', 'project','pdc_cheque','localcurrency','rptcurrency'])->whereIN('companySystemID', $subCompanies);

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

        if (array_key_exists('payeeTypeID', $input)) {
            $payeeTypeID = isset($input['payeeTypeID'][0]) ? $input['payeeTypeID'][0] : $input['payeeTypeID'];
            if (($payeeTypeID == 1) && !is_null($payeeTypeID)) {
                $paymentVoucher->where('BPVsupplierID', "!=", NULL);
            }
            if (($payeeTypeID == 2) && !is_null($payeeTypeID)) {
                $paymentVoucher->where('directPaymentPayeeEmpID', "!=", NULL);
            }
            if (($payeeTypeID == 3) && !is_null($payeeTypeID)) {
                $paymentVoucher->where('directPaymentPayeeEmpID', NULL)->where('BPVsupplierID', NULL);
            }
        }

        if (array_key_exists('createdBy', $input)) {
            if($input['createdBy'] && !is_null($input['createdBy']))
            {
                $paymentVoucher->whereIn('createdUserSystemID', $createdBy);
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
            if ($input['supplierID'] && count($supplierID) > 0) {
                $paymentVoucher->whereIn('BPVsupplierID', $supplierID);
            }
        }

        if (array_key_exists('employeeID', $input)) {
            if ($input['employeeID'] && count($employeeID) > 0 && count($supplierID) == 0) {
                $paymentVoucher->whereIn('directPaymentPayeeEmpID', $employeeID);
            }
            if ($input['employeeID'] && count($supplierID) > 0 && count($employeeID) > 0) {
                $paymentVoucher->orWhereIn('directPaymentPayeeEmpID', $employeeID);
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
                    ->orWhere('BPVNarration', 'LIKE', "%{$search}%")->orWhere('suppAmountDocTotal', 'LIKE', "%{$search_without_comma}%")->orWhere('payAmountBank', 'LIKE', "%{$search_without_comma}%")->orWhere('BPVchequeNo', 'LIKE', "%{$search_without_comma}%")->orWhere('directPaymentPayee', 'LIKE', "%{$search_without_comma}%");
            });
        }

        return $paymentVoucher;
    }

    public function setExportExcelData($dataSet,Request $request) {

        $dataSet = $dataSet->get();
        $dataSet = $dataSet->reverse();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][__('custom.payment_code')] = $val->BPVcode;
                $data[$x][__('custom.posted_date')] = $val->postedDate;
                $data[$x][__('custom.payment_type')] = StatusService::getInvoiceType($val->invoiceType);
                if($val->supplier){
                    $data[$x][__('custom.payee_type')] = "Supplier";
                    $data[$x][__('custom.sup_emp_other')] = $val->supplier? $val->supplier->supplierName : '';
                }
                else if($val->directPaymentPayeeEmpID > 0){
                    $data[$x][__('custom.payee_type')] = "Employee";
                    $data[$x][__('custom.sup_emp_other')] = $val->directPaymentPayee? $val->directPaymentPayee : '';
                }
                else if($val->directPaymentPayeeEmpID == null && $val->supplier == null && $val->directPaymentPayee != null){
                    $data[$x][__('custom.payee_type')] = "Other";
                    $data[$x][__('custom.sup_emp_other')] = $val->directPaymentPayee? $val->directPaymentPayee : '';
                }
                else{
                    $data[$x][__('custom.payee_type')] = "";
                    $data[$x][__('custom.sup_emp_other')] = "";
                }
                $data[$x][__('custom.invoice_date')] = \Helper::dateFormat($val->BPVdate);
                $data[$x][__('custom.cheque_no')] = $val->BPVchequeNo;
                $data[$x][__('custom.comments')] = $val->BPVNarration;
                $data[$x][__('custom.created_by')] = $val->created_by? $val->created_by->empName : '';
                $data[$x][__('custom.created_at')] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x][__('custom.e_confirmed_at')] = \Helper::convertDateWithTime($val->confirmedDate);
                $data[$x][__('custom.e_approved_at')] = \Helper::convertDateWithTime($val->approvedDate);
                $data[$x][__('custom.supplier_currency')] = $val->suppliercurrency? $val->suppliercurrency->CurrencyCode : '';
                $data[$x][__('custom.supplier_amount')] = number_format($val->suppAmountDocTotal, $val->suppliercurrency? $val->suppliercurrency->DecimalPlaces : 2, ".", "");
                $data[$x][__('custom.bank_currency')] = $val->bankcurrency? $val->bankcurrency->CurrencyCode : '';
                $data[$x][__('custom.bank_amount')] = number_format($val->payAmountBank, $val->bankcurrency? $val->bankcurrency->DecimalPlaces : 2, ".", "");
                
                $data[$x][__('custom.local_currency')] = $val->localCurrencyID? ($val->localcurrency? $val->localcurrency->CurrencyCode : '') : '';
                $data[$x][__('custom.local_amount')] = $val->localcurrency? number_format($val->payAmountCompLocal,  $val->localcurrency->DecimalPlaces, ".", "") : '';
                $data[$x][__('custom.reporting_currency')] = $val->companyRptCurrencyID? ($val->rptcurrency? $val->rptcurrency->CurrencyCode : '') : '';
                $data[$x][__('custom.reporting_amount')] = $val->rptcurrency? number_format($val->payAmountCompRpt,  $val->rptcurrency->DecimalPlaces, ".", "") : '';
                
                $data[$x][__('custom.status')] = StatusService::getStatus($val->cancelYN, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

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

            $chequeRegister = ChequeRegister::where('bank_id', $bankAccount->bankmasterAutoID)->where('bank_account_id', $bankAccount->bankAccountAutoID)->where('isActive', 1)->first();
            if(empty($chequeRegister)){
                return ['status' => false, 'message' => "No Active cheque register found for the selected bank account"];
            }

            $usedCheckID = $this->getLastUsedChequeID($companySystemID, $bankAccount->bankAccountAutoID);

            $unUsedCheque = ChequeRegisterDetail::whereHas('master', function ($q) use ($companySystemID, $bankAccount) {
                                                    $q->where('bank_account_id', $bankAccount->bankAccountAutoID)
                                                        ->where('company_id', $companySystemID)
                                                        ->where('isActive', 1);
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
                return ['status' => false, 'message' => "There are no unused cheques in the cheque register $chequeRegister->description. Define a new cheque register for the selected bank account"];
            }
        } 

        return ['status' => true, 'nextChequeNo' => $nextChequeNo, 'chequeRegisterAutoID' => $chequeRegisterAutoID, 'chequeGenrated' => $chequeGenrated];
    }

    public function validatePoPayment($purchaseOrderID, $PayMasterAutoId)
    {

        $procumentOrder = ProcumentOrder::with(['transactioncurrency'])->find($purchaseOrderID);
        $poComareAmountRpt = 0;
        $paymentCompareRpt = 0;
        $validateDta = [];
        if ($procumentOrder) {

            $companyData = Company::with(['reportingcurrency'])->find($procumentOrder->companySystemID);

            $rptDecimal = isset($companyData->reportingcurrency->DecimalPlaces) ? $companyData->reportingcurrency->DecimalPlaces : 2;

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

            $logistics = PoAdvancePayment::where('poID', $purchaseOrderID)
                                         ->where('logisticCategoryID', '>', 0)
                                         ->selectRaw('COALESCE(SUM(reqAmount + VATAmount),0) as reqAmountSum, currencyID')
                                         ->groupBy('currencyID')
                                         ->get();


            $poTotalAmountTrans = $poMasterSum['masterTotalSum'] + $poAddonMasterSum['addonTotalSum'] + $poMasterVATSum['masterTotalVATSum'];


            $poConversion = \Helper::currencyConversion($procumentOrder->companySystemID, $procumentOrder->supplierTransactionCurrencyID, $procumentOrder->supplierTransactionCurrencyID, $poTotalAmountTrans);


            $poComareAmountRpt += $poConversion['reportingAmount'];



            $temp['key'] = "Purchase Order Amount";
            $temp['currency'] = isset($procumentOrder->transactioncurrency->CurrencyCode) ? $procumentOrder->transactioncurrency->CurrencyCode : "USD";
            $temp['transAmount'] = number_format($poTotalAmountTrans, (isset($procumentOrder->transactioncurrency->DecimalPlaces) ? $procumentOrder->transactioncurrency->DecimalPlaces : 2));
            $temp['rptAmount'] = number_format($poConversion['reportingAmount'], (isset($companyData->reportingcurrency->DecimalPlaces) ? $companyData->reportingcurrency->DecimalPlaces : 2));


            $validateDta[] = $temp;
            $temp = [];

            $extraCharges = DirectInvoiceDetails::where('purchaseOrderID', $purchaseOrderID)
                                                ->selectRaw('COALESCE(SUM(netAmount),0) as totalExtraCharges, DIAmountCurrency')
                                                ->with(['transactioncurrency'])
                                                ->groupBy('DIAmountCurrency')
                                                ->get();

            if ($extraCharges) {
                foreach ($extraCharges as $key => $value) {
                    $extraChargesConversion = \Helper::currencyConversion($procumentOrder->companySystemID, $value->DIAmountCurrency, $value->DIAmountCurrency, $value->totalExtraCharges);


                    $poComareAmountRpt += $extraChargesConversion['reportingAmount'];


                    $temp['key'] = "Supplier Invoice Extra Charges";
                    $temp['currency'] = CurrencyMaster::getCurrencyCode($value->DIAmountCurrency);
                    $temp['transAmount'] = number_format($value->totalExtraCharges, CurrencyMaster::getDecimalPlaces($value->DIAmountCurrency));
                    $temp['rptAmount'] = number_format($extraChargesConversion['reportingAmount'], (isset($companyData->reportingcurrency->DecimalPlaces) ? $companyData->reportingcurrency->DecimalPlaces : 2));


                    $validateDta[] = $temp;
                    $temp = [];
                }
            }

            foreach ($logistics as $key => $value) {
                $logisticConversion = \Helper::currencyConversion($procumentOrder->companySystemID, $value->currencyID, $value->currencyID, $value->reqAmountSum);


                $poComareAmountRpt += $logisticConversion['reportingAmount'];

                $temp['key'] = "Logistic Amount";
                $temp['currency'] = CurrencyMaster::getCurrencyCode($value->currencyID);
                $temp['transAmount'] = number_format($value->reqAmountSum, CurrencyMaster::getDecimalPlaces($value->currencyID));
                $temp['rptAmount'] = number_format($logisticConversion['reportingAmount'], (isset($companyData->reportingcurrency->DecimalPlaces) ? $companyData->reportingcurrency->DecimalPlaces : 2));


                $validateDta[] = $temp;
                $temp = [];
            }



            $totalAdavancePayment =  AdvancePaymentDetails::where('purchaseOrderID', $purchaseOrderID)
                                                          ->whereHas('advancepaymentmaster', function($query) {
                                                                $query->where('cancelledYN', 0);
                                                          })
                                                          ->whereHas('pay_invoice', function($query) {
                                                                $query->where('refferedBackYN', 0);
                                                          })
                                                          ->selectRaw('COALESCE(SUM(paymentAmount),0) as paymentAmountSum, supplierTransCurrencyID')
                                                          ->groupBy('supplierTransCurrencyID')
                                                          ->get();


            foreach ($totalAdavancePayment as $key => $value) {
                $advConversion = \Helper::currencyConversion($procumentOrder->companySystemID, $value->supplierTransCurrencyID, $value->supplierTransCurrencyID, $value->paymentAmountSum);


                $paymentCompareRpt += $advConversion['reportingAmount'];

                $temp['key'] = "Advance Payment Amount";
                $temp['currency'] = CurrencyMaster::getCurrencyCode($value->supplierTransCurrencyID);
                $temp['transAmount'] = number_format($value->paymentAmountSum, CurrencyMaster::getDecimalPlaces($value->supplierTransCurrencyID));
                $temp['rptAmount'] = number_format($advConversion['reportingAmount'], (isset($companyData->reportingcurrency->DecimalPlaces) ? $companyData->reportingcurrency->DecimalPlaces : 2));


                $validateDta[] = $temp;
                $temp = [];
            }


            $totalSupplierPayment = PaySupplierInvoiceDetail::where('purchaseOrderID', $purchaseOrderID)
                                                            ->where('matchingDocID',0)
                                                            ->whereHas('payment_master', function($query) {
                                                                    $query->where('refferedBackYN', 0);
                                                            })
                                                            ->selectRaw('COALESCE(SUM(supplierPaymentAmount),0) as supplierPaymentAmountSum, supplierTransCurrencyID')
                                                            ->groupBy('supplierTransCurrencyID')
                                                            ->get();


            foreach ($totalSupplierPayment as $key => $value) {
                $suppPayConversion = \Helper::currencyConversion($procumentOrder->companySystemID, $value->supplierTransCurrencyID, $value->supplierTransCurrencyID, $value->supplierPaymentAmountSum);


                $paymentCompareRpt += $suppPayConversion['reportingAmount'];

                $temp['key'] = "Supplier Payment Amount";
                $temp['currency'] = CurrencyMaster::getCurrencyCode($value->supplierTransCurrencyID);
                $temp['transAmount'] = number_format($value->supplierPaymentAmountSum, CurrencyMaster::getDecimalPlaces($value->supplierTransCurrencyID));
                $temp['rptAmount'] = number_format($suppPayConversion['reportingAmount'], (isset($companyData->reportingcurrency->DecimalPlaces) ? $companyData->reportingcurrency->DecimalPlaces : 2));


                $validateDta[] = $temp;
                $temp = [];
            }


            $roundedPoTotal = round($poComareAmountRpt, $rptDecimal);
            $roundedTotalPayment = round($paymentCompareRpt, $rptDecimal);

            $epsilon = 0.00001;
            $rptCurrencyCode = isset($companyData->reportingcurrency->CurrencyCode) ? $companyData->reportingcurrency->CurrencyCode : "USD";


            if ((floatval($roundedTotalPayment) - floatval($roundedPoTotal)) > $epsilon) {
                $message = "<span class='text-danger'> Purchase Order ".$procumentOrder->purchaseOrderCode." will be overpaid.</span><br> <table style='width:100%;'><tr><th style='font-size: small;text-align:left;border:1px solid;'>Currency</th><th style='text-align:center;font-size: small;border:1px solid;'>Rpt (".$rptCurrencyCode.")</th><th colspan='2' style='border:1px solid;text-align:center;font-size: small'>Transaction</th></tr>";

                foreach ($validateDta as $key => $value) {
                    $message .= "<tr><td style='text-align:left;border:1px solid;'>".$value['key']."</td><td style='border:1px solid;'>".$value['rptAmount']."</td><td style='border:1px solid;'>".$value['currency']."</td><td style='border:1px solid;'>".$value['transAmount']."</td><tr>";
                }

                $message .= "</table><br>";


                return ['status' => false, 'message' => $message];
            }

        }

        return ['status' => true];

    }

}
