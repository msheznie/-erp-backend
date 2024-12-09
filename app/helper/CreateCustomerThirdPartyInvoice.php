<?php

namespace App\helper;

use App\Http\Controllers\AppBaseController;
use App\Models\AccountsReceivableLedger;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\BankMaster;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\Contract;
use App\Models\CurrencyMaster;
use App\Models\CustomerAssigned;
use App\Models\CustomerCurrency;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerInvoiceUploadDetail;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DocumentApproved;
use App\Models\Employee;
use App\Models\EmployeesDepartment;
use App\Models\ErpProjectMaster;
use App\Models\GeneralLedger;
use App\Models\LogUploadCustomerInvoice;
use App\Models\TaxLedger;
use App\Models\TaxLedgerDetail;
use App\Repositories\CustomerInvoiceDirectRepository;
use App\Models\SegmentMaster;
use App\Models\Taxdetail;
use App\Models\Unit;
use App\Models\UploadCustomerInvoice;
use App\Services\API\CustomerInvoiceAPIService;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Exceptions\CustomerInvoiceException;
use App\Models\ApprovalLevel;
use App\Models\DocumentMaster;
use App\Models\AssetDisposalDetail;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\AssetDisposalType;
use App\Jobs\GeneralLedgerInsert;
use App\Services\UserTypeService;
use App\Http\Controllers\API\CustomerInvoiceDirectAPIController;
use Illuminate\Http\Request;
class CreateCustomerThirdPartyInvoice
{
    /** @var  CustomerInvoiceDirectRepository */
    private $sourceModel;
    private $db;

	public static function customerInvoiceCreate($sourceModel,$db,$empId,$isApproveState = true)
	{   

        DB::beginTransaction();
        try {
            $companySystemId = $sourceModel['companySystemID'];
          
                
                    // $approvalLevel = ApprovalLevel::with('approvalrole' )
                    // ->where('companySystemID', $companySystemId)
                    // ->where('documentSystemID', 20)
                    // ->where('departmentSystemID', 4)
                    // ->where('isActive', -1)
                    // ->first();

                    // $approvalGroupID = [];
                    // if($approvalLevel){
                    //     if ($approvalLevel->approvalrole) {
                    //         foreach ($approvalLevel->approvalrole as $val) {
                    //             if ($val->approvalGroupID) {
                    //               $approvalGroupID[] = array('approvalGroupID' => $val->approvalGroupID);
                    //             } 
                    //             else {
                    //                 $errorMsg = "'Please set the approval group.";
                    //                 return ['status' => false, 'message' => $errorMsg];
                    //             }
                    //         }
                    //     }
                    // } else {
                    //     $errorMsg = "No approval setup created for this document.";
                    //     return ['status' => false, 'message' => $errorMsg];
                    // }

                    // $approvalGroupID;

                    // $approvalAccess = EmployeesDepartment::where('employeeGroupID', $approvalGroupID)
                    //                 ->whereHas('employee', function ($q) {
                    //                     $q->where('discharegedYN', 0);
                    //                 })
                    //                 ->where('companySystemID', $companySystemId)
                    //                 ->where('employeeSystemID',$empId->employeeSystemID)
                    //                 ->where('documentSystemID', 20)
                    //                 ->where('isActive', 1)
                    //                 ->where('removedYN', 0)
                    //                 ->first();




            // if ($approvalAccess) {

                $customerInvoiceData = array();
                $customerInvoiceData['transactionMode'] = null;
                $customerInvoiceData['companySystemID'] = $companySystemId;
                $customerInvoiceData['companyID'] = $sourceModel['companyID'];
                $customerInvoiceData['documentSystemiD'] = 20;
                $customerInvoiceData['documentID'] = 'INV';
    
                $disposalDocumentDate = (new Carbon($sourceModel->disposalDocumentDate))->format('Y-m-d');
    
                $fromCompanyFinanceYear = CompanyFinanceYear::where('companySystemID', $companySystemId)
                    ->whereDate('bigginingDate', '<=', $disposalDocumentDate)
                    ->whereDate('endingDate', '>=', $disposalDocumentDate)
                    ->where('isActive',-1)
                    ->first();


                if (!empty($fromCompanyFinanceYear)) {
    
                    $fromCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $companySystemId)
                        ->where('departmentSystemID', 4)
                        ->where('companyFinanceYearID', $fromCompanyFinanceYear->companyFinanceYearID)
                        ->whereDate('dateFrom', '<=', $disposalDocumentDate)
                        ->whereDate('dateTo', '>=', $disposalDocumentDate)
                        ->where('isActive', -1)
                        ->first();
    
                    if (!empty($fromCompanyFinancePeriod)) {
    
                        $today = $sourceModel['disposalDocumentDate'];
    
                        if (!empty($fromCompanyFinanceYear)) {
    
                            $customerInvoiceData['FYBiggin'] = $fromCompanyFinanceYear->bigginingDate;
                            $customerInvoiceData['FYEnd'] = $fromCompanyFinanceYear->endingDate;
    
                            if (!empty($fromCompanyFinancePeriod)) {
                                $customerInvoiceData['companyFinanceYearID'] = $fromCompanyFinancePeriod->companyFinanceYearID;
                                $customerInvoiceData['companyFinancePeriodID'] = $fromCompanyFinancePeriod->companyFinancePeriodID;
                                $customerInvoiceData['FYPeriodDateFrom'] = $fromCompanyFinancePeriod->dateFrom;
                                $customerInvoiceData['FYPeriodDateTo'] = $fromCompanyFinancePeriod->dateTo;
                            }
                        }
    
                        $cusInvLastSerial = CustomerInvoiceDirect::where('companySystemID', $companySystemId)
                            ->where('companyFinanceYearID', $fromCompanyFinancePeriod->companyFinanceYearID)
                            ->where('serialNo', '>', 0)
                            ->orderBy('serialNo', 'desc')
                            ->first();
    
                        $cusInvLastSerialNumber = 1;
                        if ($cusInvLastSerial) {
                            $cusInvLastSerialNumber = intval($cusInvLastSerial->serialNo) + 1;
                        }
                        $customerInvoiceData['serialNo'] = $cusInvLastSerialNumber;
    
                        $serviceLine = SegmentMaster::ofCompany([$companySystemId])->isPublic()->first();
    
                        if ($serviceLine) {
                            $customerInvoiceData['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
                            $customerInvoiceData['serviceLineCode'] = $serviceLine->ServiceLineCode;
                        }
                        
                        if ($fromCompanyFinancePeriod) {
                            $cusStartYear = $fromCompanyFinanceYear->bigginingDate;
                            $cusFinYearExp = explode('-', $cusStartYear);
                            $cusFinYear = $cusFinYearExp[0];
                        } else {
                            $cusFinYear = date("Y");
                        }
                        $bookingInvCode = ($sourceModel['companyID'] . '\\' . $cusFinYear . '\\' . $customerInvoiceData['documentID'] . str_pad($cusInvLastSerialNumber, 6, '0', STR_PAD_LEFT));
                        $customerInvoiceData['bookingInvCode'] = $bookingInvCode;
                        $customerInvoiceData['bookingDate'] = $today;
    
                        $customerInvoiceData['comments'] = "INV Created by -Sold to 3rd. Party Disposal - ".$sourceModel['disposalDocumentCode'];
    
                        $customer = CustomerMaster::where('customerCodeSystem', $sourceModel['customerID'])->first();
    
                        if (!empty($customer)) {
                            if($customer->custGLaccount != null && $customer->custGLAccountSystemID != null)
                            {
                                $customerInvoiceData['customerID'] = $customer->customerCodeSystem;
                                $customerInvoiceData['customerGLCode'] = $customer->custGLaccount;
                                $customerInvoiceData['customerGLSystemID'] = $customer->custGLAccountSystemID;
                                $customerInvoiceData['customerInvoiceNo'] = $sourceModel['disposalDocumentCode'];
                                $customerInvoiceData['customerInvoiceDate'] = $today;
                            }
                            else
                            {
                                return ['status' => false, 'message' => "customer dont have gl account"];

                            }
                   
                        }
                        $customerInvoiceData['invoiceDueDate'] = $today;
                        $customerInvoiceData['serviceStartDate'] = $today;
                        $customerInvoiceData['serviceEndDate'] = $today;
                        $customerInvoiceData['performaDate'] = $today;
    
                        $fromCompany = Company::where('companySystemID', $companySystemId)->first();

                        if(!$isApproveState) {
                            $customerCurrency = CustomerCurrency::where('customerCodeSystem',$sourceModel['customerID'])
                                ->where('isAssigned', -1)
                                ->where('currencyID', $fromCompany->localCurrencyID)
                                ->first();

                            if (empty($customerCurrency)) {
                                return ['status' => false, 'message' => "The company’s local currency is not defined for the selected customer."];
                            }
                        }

                        $companyCurrencyConversion = \Helper::currencyConversion($companySystemId, $fromCompany->localCurrencyID, $fromCompany->localCurrencyID, 0);
                        $customerInvoiceData['companyReportingCurrencyID'] = $fromCompany->reportingCurrency;
                        $customerInvoiceData['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
    
                        $customerInvoiceData['localCurrencyID'] = $fromCompany->localCurrencyID;
                        $customerInvoiceData['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
    
                        $customerInvoiceData['custTransactionCurrencyID'] = $fromCompany->localCurrencyID;
                        $customerInvoiceData['custTransactionCurrencyER'] = 1;
    
                        $disposalDetail = AssetDisposalDetail::selectRaw('SUM(netBookValueLocal) as netBookValueLocal, SUM(netBookValueRpt) as netBookValueRpt, SUM(COSTUNIT) as COSTUNIT, SUM(depAmountLocal) as depAmountLocal, SUM(costUnitRpt) as costUnitRpt, SUM(depAmountRpt) as depAmountRpt, serviceLineSystemID, ServiceLineCode, 
                        SUM(if(ROUND(netBookValueLocal,2) = 0,COSTUNIT + COSTUNIT * (revenuePercentage/100),netBookValueLocal + (netBookValueLocal * (revenuePercentage/100)))) as localAmountDetail, 
                        SUM(if(ROUND(netBookValueRpt,2) = 0,costUnitRpt + costUnitRpt * (revenuePercentage/100),netBookValueRpt + (netBookValueRpt * (revenuePercentage/100)))) as comRptAmountDetail, sellingPriceLocal, sellingPriceRpt')->OfMaster($sourceModel['assetdisposalMasterAutoID'])->groupBy('assetDisposalDetailAutoID')->get();
    
                        $localAmount = 0;
                        $comRptAmount = 0;
                        $vatAmountLocal = 0;
                        $vatAmountRpt = 0;
    
                        if (count($disposalDetail) > 0) {
                            foreach ($disposalDetail as $val) {
                                $localAmount += $val->sellingPriceLocal;
                                $comRptAmount += $val->sellingPriceRpt;
                                $vatAmountLocal += ($val->vatAmount * $companyCurrencyConversion['trasToRptER']) / $companyCurrencyConversion['trasToLocER'];
                                $vatAmountRpt += $val->vatAmount;
                            }
                        }

                        $bank = BankAssign::select('bankmasterAutoID')
                        ->where('companySystemID', $companySystemId)
                        ->where('isDefault', -1)
                        ->first();
                    if ($bank) {
                        $customerInvoiceData['bankID'] = $bank->bankmasterAutoID;
                        $bankAccount = BankAccount::where('companySystemID', $companySystemId)
                            ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                            ->where('isDefault', 1)
                            ->first();
                        if ($bankAccount) {
                            $customerInvoiceData['bankAccountID'] = $bankAccount->bankAccountAutoID;
                        }
                        else if (!$isApproveState) {
                            return ['status' => false, 'message' => "Default Bank Account not set."];
                        }
                    }
                    else if (!$isApproveState) {
                        return ['status' => false, 'message' => "Default Bank Account not set."];
                    }

                        $systemUser = UserTypeService::getSystemEmployee();
                        $customerInvoiceData['bookingAmountTrans'] = \Helper::roundValue($localAmount);
                        $customerInvoiceData['bookingAmountLocal'] = \Helper::roundValue($localAmount);
                        $customerInvoiceData['bookingAmountRpt'] = \Helper::roundValue($comRptAmount);
                        $customerInvoiceData['vatRegisteredYN'] = $sourceModel->vatRegisteredYN;
                        $customerInvoiceData['customerVATEligible'] = $sourceModel->vatRegisteredYN;
                        $customerInvoiceData['VATPercentage'] = \Helper::roundValue($vatAmountLocal / $localAmount * 100);
                        $customerInvoiceData['VATAmount'] = \Helper::roundValue($vatAmountLocal);
                        $customerInvoiceData['VATAmountLocal'] = \Helper::roundValue($vatAmountLocal);
                        $customerInvoiceData['VATAmountRpt'] = \Helper::roundValue($vatAmountRpt);
                        $customerInvoiceData['postedDate'] = NOW();
                        $customerInvoiceData['isPerforma'] = 0;
                        $customerInvoiceData['documentType'] = 11;
                        $customerInvoiceData['interCompanyTransferYN'] = 0;
                        $customerInvoiceData['createdUserSystemID'] = $systemUser->employeeSystemID;
                        $customerInvoiceData['createdUserID'] = $systemUser->empID;
                        $customerInvoiceData['createdPcID'] = $sourceModel['modifiedPc'];
                        $customerInvoiceData['createdDateAndTime'] = NOW();
                        $customerInvoiceData['isAutoGenerated'] = 1;
                        $customerInvoiceData['isPOS'] = 1;
                        $customerInvoiceData['date_of_supply'] = $today;
                        $customerInvoiceData['modifiedUserSystemID'] = $systemUser->employeeSystemID;
                        $customerInvoiceData['modifiedUser'] = $systemUser->empID;
                        $customerInvoice = CustomerInvoiceDirect::create($customerInvoiceData);
                    
                        $interComAssetDisposal = [
                            'assetDisposalID' => $sourceModel['assetdisposalMasterAutoID'],
                            'customerInvoiceID' => $sourceModel['custInvoiceDirectAutoID']
                        ];
    
    
                        $disposalDetails = AssetDisposalDetail::selectRaw('netBookValueLocal, netBookValueRpt, COSTUNIT, depAmountLocal, costUnitRpt, depAmountRpt, serviceLineSystemID, ServiceLineCode, vatPercentage, vatMasterCategoryID, vatSubCategoryID, vatAmount, if(ROUND(netBookValueLocal,2) = 0,COSTUNIT + COSTUNIT * (revenuePercentage/100),netBookValueLocal + (netBookValueLocal * (revenuePercentage/100))) as localAmountDetail, if(ROUND(netBookValueRpt,2) = 0,costUnitRpt + costUnitRpt * (revenuePercentage/100),netBookValueRpt + (netBookValueRpt * (revenuePercentage/100))) as comRptAmountDetail, sellingPriceLocal, sellingPriceRpt')->OfMaster($sourceModel['assetdisposalMasterAutoID'])->get();
                        $segment = AssetDisposalDetail::OfMaster($sourceModel['assetdisposalMasterAutoID'])->first();
                        foreach ($disposalDetails as $disposalDetail) {
                           $accID = SystemGlCodeScenarioDetail::getGlByScenario($companySystemId, $sourceModel['documentSystemID'], "asset-disposal-inter-company-sales");
                            $comment = "INV Created by -Sold to 3rd. Party Disposal - ".$sourceModel['disposalDocumentCode'];
                       
                                $disposalType = AssetDisposalType::where('disposalTypesID',6)->first();
                                $chartofAccount = ChartOfAccount::find($disposalType->chartOfAccountID);
    
                                $cusInvoiceDetails['custInvoiceDirectID'] = $customerInvoice->custInvoiceDirectAutoID;
                                $cusInvoiceDetails['companyID'] = $sourceModel['companyID'];
                                $cusInvoiceDetails['serviceLineSystemID'] = $segment->serviceLineSystemID;
                                $cusInvoiceDetails['serviceLineCode'] = $segment->serviceLineCode;
                                if ($customer) {
                                    $cusInvoiceDetails['customerID'] = $customer->customerCodeSystem;
                                }
                                $cusInvoiceDetails['glSystemID'] = $chartofAccount->chartOfAccountSystemID;
                                $cusInvoiceDetails['glCode'] = $chartofAccount->AccountCode;
                                $cusInvoiceDetails['glCodeDes'] = $chartofAccount->AccountDescription;
                                $cusInvoiceDetails['accountType'] = $chartofAccount->catogaryBLorPL;
                                $cusInvoiceDetails['comments'] = $comment;
                                $cusInvoiceDetails['unitOfMeasure'] = 1;
                                $cusInvoiceDetails['invoiceQty'] = 1;
                                $cusInvoiceDetails['invoiceAmountCurrency'] = $fromCompany->localCurrencyID;;
                                $cusInvoiceDetails['invoiceAmountCurrencyER'] = 1;
                                $cusInvoiceDetails['localCurrency'] = $fromCompany->localCurrencyID;
                                $cusInvoiceDetails['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                                $cusInvoiceDetails['comRptCurrency'] = $fromCompany->reportingCurrency;;
                                $cusInvoiceDetails['comRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                                $cusInvoiceDetails['clientContractID'] = 'X';
                                $cusInvoiceDetails['contractID'] = 159;
                                $cusInvoiceDetails['performaMasterID'] = 0;
    
                                $localAmountDetail = $disposalDetail->sellingPriceLocal;
                                $comRptAmountDetail = $disposalDetail->sellingPriceRpt;

                                $cusInvoiceDetails['vatMasterCategoryID'] = $disposalDetail->vatMasterCategoryID;
                                $cusInvoiceDetails['vatSubCategoryID'] = $disposalDetail->vatSubCategoryID;
                                $cusInvoiceDetails['VATPercentage'] = $disposalDetail->vatPercentage;
                                $cusInvoiceDetails['VATAmount'] = $disposalDetail->vatAmount * $companyCurrencyConversion['trasToRptER'];
                                $cusInvoiceDetails['VATAmountLocal'] = $disposalDetail->vatAmount * $companyCurrencyConversion['trasToRptER'] / $companyCurrencyConversion['trasToLocER'];
                                $cusInvoiceDetails['VATAmountRpt'] = $disposalDetail->vatAmount;
                                $cusInvoiceDetails['salesPrice'] = \Helper::roundValue($localAmountDetail);
                                $cusInvoiceDetails['localAmount'] = \Helper::roundValue($localAmountDetail);
                                $cusInvoiceDetails['comRptAmount'] = \Helper::roundValue($comRptAmountDetail);
                                $cusInvoiceDetails['invoiceAmount'] = \Helper::roundValue($localAmountDetail);
                                $cusInvoiceDetails['unitCost'] = \Helper::roundValue($localAmountDetail);
                               
                                $customerInvoiceDet = CustomerInvoiceDirectDetail::create($cusInvoiceDetails);
                          
                        }

                        $resVat =  CustomerInvoiceAPIService::updateTotalVAT($customerInvoice->custInvoiceDirectAutoID);

                        if($isApproveState) {
                            $params = array(
                                'autoID' => $customerInvoice->custInvoiceDirectAutoID,
                                'company' => $customerInvoice->companySystemID,
                                'document' => $customerInvoice->documentSystemiD,
                                'segment' => '',
                                'category' => '',
                                'amount' => '',
                                'isAutoCreateDocument' => true
                            );

                            $returnData = \Helper::confirmDocument($params);

                            if($returnData['success']){

                                $request = new Request();
                                $request->replace([
                                    'companyId' => $customerInvoice->companySystemID,
                                    'custInvoiceDirectAutoID' => $customerInvoice->custInvoiceDirectAutoID,
                                    'isAutoCreateDocument' => true
                                ]);
                                $controller = app(CustomerInvoiceDirectAPIController::class);
                                $customerInvoiceApprovalData = $controller->getCustomerInvoiceApproval($request);
                                $customerInvoiceApprovalData = json_decode(json_encode($customerInvoiceApprovalData),true);

                                if($customerInvoiceApprovalData['success']){

                                    $dataset = $customerInvoiceApprovalData['data'];
                                    $dataset['isAutoCreateDocument'] = true;
                                    $dataset['companySystemID'] = $customerInvoice->companySystemID;
                                    $dataset['approvedComments'] = "Created from Disposal";

                                    $dataset['db'] = $db;


                                    $approveDocument = \Helper::approveDocument($dataset);

                                    if ($approveDocument["success"]) {
                                        DB::commit();
                                        return ['status' => true, 'message' => "Customer invoice created successfully"];
                                    }
                                    else {
                                        return ['status' => false, 'message' => $approveDocument['message']];
                                    }

                                }
                                else{
                                    return ['status' => false, 'message' => $customerInvoiceApprovalData['message']];
                                }
                            }
                        }
                        else {
                            $customerInvoice->update([
                                'statusFromDisposal' => 1
                            ]);

                            DB::commit();
                            return ['status' => true, 'message' => "Customer invoice created successfully"];
                        }

                    }
                    else {
                        return ['status' => false, 'message' => "Finance period not activated"];
    
                    }
                }else {
                    return ['status' => false, 'message' => "From Company Finance Year not found, date"];
                }

            // }
            // else
            // {
            //     return ['status' => false, 'message' => "The user does not have customer invoices approval access"];
            // }
        } catch (\Exception $e) {
             DB::rollback();
            //dd($e);
            return ['success' => false, 'message' => $e . 'Error Occurred'];
        }
	}


}
