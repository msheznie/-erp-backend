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
use App\Traits\AuditTrial;
use AWS\CRT\HTTP\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Exceptions\CustomerInvoiceException;
use App\Models\ApprovalLevel;
use App\Models\DocumentMaster;

class CustomerInvoiceService
{
    /** @var  CustomerInvoiceDirectRepository */
    private $customerInvoiceDirectRepository;

    public function __construct(CustomerInvoiceDirectRepository $customerInvoiceDirectRepo)
    {
        $this->customerInvoiceDirectRepository = $customerInvoiceDirectRepo;
    }

	public static function customerInvoiceCreate($db,$uploadData, $ciData)
	{
        
        $customerInvoiceDirectRepo = app()->make(CustomerInvoiceDirectRepository::class);

        // Instantiate CustomerInvoiceService with the repository as an argument
        $CustomerInvoiceService = new CustomerInvoiceService($customerInvoiceDirectRepo);
       
        $db = isset($db) ? $db : "";
        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);

            
        $uploadCustomerInvoice = $uploadData['uploadCustomerInvoice'];
        $logUploadCustomerInvoice = $uploadData['logUploadCustomerInvoice'];
        $employee = $uploadData['employee'];
        $uploadedCompany = $uploadData['uploadedCompany'];

        $enableProject = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
        ->where('companySystemID', $uploadedCompany)
        ->first();

        $errorMsg = "";
        $cutomerCode = ""; //mandatory
        $crNumber = "";  //mandatory
        $currency = "";  //mandatory
        $headerComments = ""; //mandatory
        $documentDate = ""; //mandatory
        $invoiceDueDate = ""; //mandatory
        $customerInvoiceNo = ""; //mandatory
        $bank = ""; //mandatory
        $accountNo = ""; //mandatory
        $confirmedBy = ""; 
        $approvedBy = "";
        $approvedEmployee = null;
        if (count($ciData) > 0) {
            $value = $ciData[0];
            // HEADER DETAILS
            $cutomerCode = $value[0]; //mandatory
            $crNumber = $value[1];  //mandatory
            $currency = $value[2];  //mandatory
            $headerComments = $value[3]; //mandatory
            $documentDate = $value[4]; //mandatory
            $invoiceDueDate = $value[5]; //mandatory
            $customerInvoiceNo = $value[6]; //mandatory
            $bank = $value[7]; //mandatory
            $accountNo = $value[8]; //mandatory
            $confirmedBy = $value[9]; 
            $approvedBy = $value[10];
            
            if($enableProject){
                $policy = $enableProject->isYesNO;
                $excelRow = ($policy ) ? $value[20] : $value[19];
            } else {
                $excelRow = $value[20];
            }

            //Check Customer Code & CR Number both have value
            if($cutomerCode == null && $crNumber == null){
                $errorMsg = trans('custom.customer_code_cr_number_at_least_one_should_have_value');
                return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];

            }

            //Check Customer Active for cutomerCode
            if($cutomerCode != null && $crNumber != null){
                $customerMasters = CustomerMaster::where('CutomerCode',$cutomerCode)
                ->where('customer_registration_no',$crNumber)
                ->where('approvedYN',1)
                ->first();

                if(!$customerMasters){
                    $errorMsg = trans('custom.active_customer_not_found_for_customer_code_cr_number') . " $cutomerCode & CR Number $crNumber";
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                }
            } elseif ($cutomerCode != null ){
                $customerMasters = CustomerMaster::where('CutomerCode',$cutomerCode)
                ->where('approvedYN',1)
                ->first();

                if(!$customerMasters){
                    $errorMsg = trans('custom.active_customer_not_found_for_customer_code') . " $cutomerCode";
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                }
            } elseif ($crNumber != null){
                $customerMasters = CustomerMaster::where('customer_registration_no',$crNumber)
                ->where('approvedYN',1)
                ->first();
                
                if(!$customerMasters){
                    $errorMsg = trans('custom.active_customer_not_found_for_customer_registration_number') . " $crNumber";
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                }
            }

            //Check Currency for Currency code
            if($currency == null){
                $errorMsg = trans('custom.currency_field_cannot_be_null');
                return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
            }

            if($currency != null){
                $currencyMaster = CurrencyMaster::where('CurrencyCode',$currency)
                ->first();

                if(!$currencyMaster){
                    $errorMsg = trans('custom.currency_master_not_found_for_currency_code') . " $currency";
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                }

                //check customer assigned currency
                if($customerMasters && $currencyMaster){
                    // return $customerMasters;
                    $customerCurrency = CustomerCurrency::where('customerCode',$customerMasters->CutomerCode)
                                                            ->where('currencyID',$currencyMaster->currencyID)
                                                            ->where('isAssigned',-1)
                                                            ->first();

                    if(!$customerCurrency){
                        $errorMsg = trans('custom.currency') . " $currency " . trans('custom.currency_not_assigned_to_customer') . " $customerMasters->customerCode";
                        return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                    }
                }

            }

            
            //Check headerComments
            if($headerComments == null){
                $errorMsg = trans('custom.header_comments_field_cannot_be_null');
                return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
            }

            //Check documentDate  //Check Active Financial Period
            if($documentDate == null){
                $errorMsg = trans('custom.document_date_field_cannot_be_null');
                return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
            }

            if($documentDate != null){
                
                // Normalize the date to ensure proper format
                $normalizedDate = \Carbon\Carbon::createFromFormat('j/n/Y', $documentDate)->format('d/m/Y');

                $validator = Validator::make(['date' => $normalizedDate], [
                    'date' => 'date_format:d/m/Y',
                ]);

                if ($validator->fails()) {
                    $errorMsg = trans('custom.invalid_invoice_document_date_format') . "  $documentDate.";
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' => $excelRow];
                }

                $documentDateBeforeFormat = $documentDate;
                $documentDate = \Carbon\Carbon::createFromFormat('d/m/Y', $documentDate);

                $companyFinanceYear = Helper::companyFinanceYear($uploadedCompany, 0);
                
                if (!isset($companyFinanceYear) && empty($companyFinanceYear)) 
                {
                    $errorMsg = trans('custom.finance_year_not_active');
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' => $excelRow];
                }
                $checkDate = Carbon::parse($documentDate);
                $CompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', '=', $uploadedCompany)
                                        ->where('companyFinanceYearID', $companyFinanceYear[0]['companyFinanceYearID'])
                                        ->where('departmentSystemID', 4)
                                        ->where('isActive', -1)
                                        ->whereRaw('? BETWEEN dateFrom AND dateTo', [$checkDate])
                                        ->first();
            

                if (!$CompanyFinancePeriod) {
                    $errorMsg = trans('custom.financial_period_inactive', ['document_date' => $documentDateBeforeFormat]);
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                }
                $curentDate = Carbon::now();
                if ($checkDate > $curentDate) {
                    $errorMsg = trans('custom.document_date') . $documentDateBeforeFormat . trans('custom.cannot_be_greater_than_current_date');
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                }
            }

            //Check invoiceDueDate
            if($invoiceDueDate == null){
                $errorMsg = trans('custom.invoice_due_date_field_cannot_be_null');
                return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
            }

            if ($invoiceDueDate != null) {
                // Normalize the date to ensure proper format
                $normalizedDate = \Carbon\Carbon::createFromFormat('j/n/Y', $invoiceDueDate)->format('d/m/Y');

                $validator = Validator::make(['date' => $normalizedDate], [
                    'date' => 'date_format:d/m/Y',
                ]);

                if ($validator->fails()) {
                    $errorMsg = trans('custom.invalid_invoice_due_date_format') . "  $invoiceDueDate.";
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' => $excelRow];
                }
                $invoiceDueDateBeforeFormat = $invoiceDueDate;
                $invoiceDueDate = \Carbon\Carbon::createFromFormat('d/m/Y', $invoiceDueDate);

            }

            //customerInvoiceNo
            if($customerInvoiceNo == null){
                $errorMsg = trans('custom.customer_invoice_no_field_cannot_be_null');
                return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
            }

            //bank
            if($bank == null){
                $errorMsg = trans('custom.bank_field_cannot_be_null');
                return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
            }

            if($bank != null){
                $bankMaster = BankAssign::where('bankShortCode',$bank)->first();
                if(!$bankMaster){
                    $errorMsg = trans('custom.bank_not_found_for_bank_code') . " $bank.";
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                }

                if($bankMaster){
                    //Account No
                    if($accountNo == null){
                        $errorMsg = trans('custom.account_no_field_cannot_be_null');
                        return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                    }

                    if($accountNo != null){
                        $account = BankAccount::where('AccountNo',$accountNo)
                                                ->where('bankmasterAutoID',$bankMaster->bankmasterAutoID)
                                                ->first();
                        if(!$account){
                            $errorMsg = trans('custom.bank_account_no_not_found', ['account_no' => $accountNo, 'bank_code'  => $bank]);
                            return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                        }
                    }
                }

            }

            if ($confirmedBy != null){
                $confirmedEmployee = Employee::where('empID',$confirmedBy)
                                                ->where('empActive',1)
                                                ->first();
                if(!$confirmedEmployee){
                    $errorMsg = trans('custom.active_employee_not_found_for_confirmed_by') . " $confirmedBy.";
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                }

            }
            if($confirmedBy == null) {
                $confirmedEmployee = $employee;
            }

            if ($approvedBy != null){
                $approvedEmployee = Employee::where('empID',$approvedBy)
                                    ->where('empActive',1)
                                    ->where('discharegedYN',0)
                                    ->first();

                if(!$approvedEmployee){
                    $errorMsg = trans('custom.active_employee_not_found_for_approved_by') . " $approvedBy.";
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                }

                $document = DocumentMaster::where('documentSystemID', 20)->first();

                // get approval rolls
                $approvalLevel = ApprovalLevel::with('approvalrole' )
                                                ->where('companySystemID', $uploadedCompany)
                                                ->where('documentSystemID', 20)
                                                ->where('departmentSystemID', $document["departmentSystemID"])
                                                ->where('isActive', -1)
                                                ->first();

                $approvalGroupID = [];
                if($approvalLevel){
                    if ($approvalLevel->approvalrole) {
                        foreach ($approvalLevel->approvalrole as $val) {
                            if ($val->approvalGroupID) {
                                $approvalGroupID[] = array('approvalGroupID' => $val->approvalGroupID);
                            } else {
                                $errorMsg = trans('custom.please_set_approval_group');
                                return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                            }
                        }
                    }
                } else {
                    $errorMsg = trans('custom.no_approval_setup_created_for_document');
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                }

                 $approvalGroupID;

                //Check Approval Acces
                 $approvalAccess = EmployeesDepartment::where('employeeGroupID', $approvalGroupID)
                                    ->whereHas('employee', function ($q) {
                                        $q->where('discharegedYN', 0);
                                    })
                                    ->where('companySystemID', $uploadedCompany)
                                    ->where('employeeID',$approvedEmployee->empID)
                                    ->where('documentSystemID', 20)
                                    ->where('isActive', 1)
                                    ->where('removedYN', 0)
                                    ->first();

                if(!$approvalAccess){
                    $errorMsg = $approvedBy . trans('custom.approver_does_not_have_approval_access');
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                }
            }

            if($approvedBy == null){
                $approvedEmployee = $employee;

                //Check Approval Acces

                $document = DocumentMaster::where('documentSystemID', 20)->first();

                // get approval rolls
                $approvalLevel = ApprovalLevel::with('approvalrole' )
                                                ->where('companySystemID', $uploadedCompany)
                                                ->where('documentSystemID', 20)
                                                ->where('departmentSystemID', $document["departmentSystemID"])
                                                ->where('isActive', -1)
                                                ->first();

                $approvalGroupID = [];
                if($approvalLevel){
                    if ($approvalLevel->approvalrole) {
                        foreach ($approvalLevel->approvalrole as $val) {
                            if ($val->approvalGroupID) {
                                $approvalGroupID[] = array('approvalGroupID' => $val->approvalGroupID);
                            } else {
                                $errorMsg = trans('custom.please_set_approval_group');
                                return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                            }
                        }
                    }
                } else {
                    $errorMsg = trans('custom.no_approval_setup_created_for_document');
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                }

                 $approvalGroupID;

                //Check Approval Acces
                 $approvalAccess = EmployeesDepartment::where('employeeGroupID', $approvalGroupID)
                                    ->whereHas('employee', function ($q) {
                                        $q->where('discharegedYN', 0);
                                    })
                                    ->where('companySystemID', $uploadedCompany)
                                    ->where('employeeID',$approvedEmployee->empID)
                                    ->where('documentSystemID', 20)
                                    ->where('isActive', 1)
                                    ->where('removedYN', 0)
                                    ->first();

                if(!$approvalAccess){
                    $errorMsg = trans('custom.uploaded_employee_does_not_have_approval_access') . " $employee->empID.";
                    return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                }
            }

            $DirectInvoiceHeaderData = [
                'bookingDate'=> $documentDate,
                'comments'=> $headerComments,
                'companyFinancePeriodID'=> $CompanyFinancePeriod->companyFinancePeriodID,
                'companyFinanceYearID'=> $companyFinanceYear[0]['companyFinanceYearID'],
                'companyID'=> $uploadedCompany,
                'custTransactionCurrencyID'=> $customerCurrency->currencyID,
                'customerID'=> $customerMasters->customerCodeSystem,
                'invoiceDueDate'=> $invoiceDueDate,
                'date_of_supply'=> $documentDate,
                'isPerforma'=> 0,
                'isUpload'=> 1,
                'excelRow'=> $excelRow,
                'bankID'=> $bankMaster->bankAssignedAutoID,
                'bankAccountID'=> $account->bankAccountAutoID,
                'customerInvoiceNo'=> $customerInvoiceNo,
                'uploadCustomerInvoice'=> $uploadCustomerInvoice->id,
                'logUploadCustomerInvoice'=> $logUploadCustomerInvoice->id
            ];

            $checkUploadStatus = UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->first();

            if ($checkUploadStatus->uploadStatus == 0) {
                return ['status' => false];
            }

            $directInvoiceHeader = $CustomerInvoiceService->createDirectInvoiceHeader($DirectInvoiceHeaderData);

            $enableProject = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
            ->where('companySystemID', $uploadedCompany)
            ->first();
            
            if ($directInvoiceHeader['status']) {
                foreach ($ciData as $deatilKey => $value) {
                    //DETAIL LEVEL DATA
                    $glCode = $value[11]; //mandatory

                    if($enableProject){
                        $policy = $enableProject->isYesNO;
                        
                        $project = ($policy) ? $value[12] : null;
                        $segment = ($policy) ? $value[13] : $value[12];  // mandatory
                        $detailComments = ($policy) ? $value[14] : $value[13];
                        $UOM = ($policy) ? $value[15] : $value[14]; // mandatory
                        $Qty = ($policy) ? $value[16] : $value[15]; // mandatory
                        $salesPrice = ($policy) ? $value[17] : $value[16]; // mandatory
                        $discountAmount = ($policy) ? $value[18] : $value[17];
                        $vatAmount = ($policy) ? $value[19] : $value[18];
                        $excelRow = ($policy ) ? $value[20] : $value[19];
                         
                    } else {
                        $project = $value[12];
                        $segment = $value[13];  // mandatory
                        $detailComments = $value[14];
                        $UOM = $value[15]; // mandatory
                        $Qty = $value[16]; // mandatory
                        $salesPrice = $value[17]; // mandatory
                        $discountAmount = $value[18];
                        $vatAmount = $value[19];
                        $excelRow = $value[20];
                    }
                    



                    if($glCode == null){
                        $errorMsg = trans('custom.gl_account_field_cannot_be_null');
                        return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                    }

                    if($glCode != null){
                        $chartOfAccounts = ChartOfAccountsAssigned::where('AccountCode',$glCode)
                                                            ->where('companySystemID', $uploadedCompany)
                                                            ->where('isAssigned', -1)
                                                            ->where('isActive', 1)
                                                            ->first();

                        if(!$chartOfAccounts){
                            $errorMsg = trans('custom.chart_of_account_not_found_for_gl_code') . " $glCode.";
                            return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                        }
                    }


                    if($project != null){
                        $projectExist = ErpProjectMaster::where('projectCode',$project)
                                                            ->where('companySystemID',$uploadedCompany)
                                                            ->first();
                        
                        if(!$projectExist){
                            $errorMsg = trans('custom.project_master_not_found_for_project_code') . " $project.";
                            return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                        }
                    }
                    if($project == null){
                        $project = null;
                    } else {
                        $project = $projectExist->id;
                    }

                    if($segment == null){
                        $errorMsg = trans('custom.segment_field_cannot_be_null');
                        return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                    }

                    if($segment != null){
                        $segmentExist = SegmentMaster::where('ServiceLineCode',$segment)
                                                        ->where('companySystemID',$uploadedCompany)
                                                        ->where('isActive',1)
                                                        ->where('isDeleted',0)
                                                        ->first();

                        if(!$segmentExist){
                            $errorMsg = trans('custom.active_segment_master_not_found_for_segment_code') . " $segment.";
                            return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                        }
                    }

                    if($UOM == null){
                        $errorMsg = trans('custom.uom_field_cannot_be_null');
                        return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                    }

                    if($UOM != null){
                        $UOMExist = Unit::where('UnitShortCode',$UOM)
                                        ->where('is_active',1)
                                        ->first();

                        if(!$UOMExist){
                            $errorMsg = trans('custom.active_uom_master_not_found_for_uom_short_code') . " $UOM.";
                            return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                        }
                    }

                    
                    if($Qty == null){
                        $errorMsg = trans('custom.quantity_field_cannot_be_null');
                        return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                    }
                    if($Qty != null){
                        if (is_numeric($Qty)) {
                            if(0 > $Qty){
                                $errorMsg = trans('custom.quantity_cannot_have_negative_value');
                                return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                            }
                        } else {
                            $errorMsg = trans('custom.quantity_is_not_numeric_value') . " $Qty.";
                            return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                        }

                    }

                    if($salesPrice == null){
                        $errorMsg = trans('custom.sales_price_field_cannot_be_null');
                        return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                    }
                    if($salesPrice != null){
                        if (is_numeric($salesPrice)) {
                            if( 0 > $salesPrice){
                                $errorMsg = trans('custom.sales_price_cannot_have_negative_value');
                                return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                            }
                        } else {
                            $errorMsg = trans('custom.sales_price_is_not_numeric_value') . " $salesPrice.";
                            return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                        }

                    }

                    if($discountAmount != null){
                        if (is_numeric($discountAmount)) {
                            if( 0 > $discountAmount){
                                $errorMsg = trans('custom.discount_amount_cannot_have_negative_value');
                                return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                            }
                        } else {
                            $errorMsg = trans('custom.discount_amount_is_not_numeric_value') . " $discountAmount.";
                            return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                        }

                        $byDiscount = 'discountAmountLine';
                        $discountPercentage = 0;
                    } else {
                        $byDiscount = 'discountPercentage';
                        $discountAmount = 0;
                        $discountPercentage = 0;
                    }

                    if($vatAmount != null){
                        if (is_numeric($vatAmount)) {
                            if( 0 > $vatAmount){
                                $errorMsg = trans('custom.vat_amount_cannot_have_negative_value');
                                return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                            }
                        } else {
                            $errorMsg = trans('custom.vat_amount_is_not_numeric_value') . " $vatAmount.";
                            return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];
                        }

                        $by = 'vatAmount';
                        $VATPercentage = 0;
                    } else {
                        $by = 'VATPercentage';
                        $vatAmount = 0;
                        $VATPercentage = 0;
                    }



                    $DirectInvoiceDetailData = [
                        'glCode'=>$chartOfAccounts->chartOfAccountSystemID,
                        'project'=>$project,
                        'comments'=>$detailComments,
                        'segment'=>$segmentExist->serviceLineSystemID,
                        'segmentCode'=>$segmentExist->ServiceLineCode,
                        'companySystemID'=>$uploadedCompany,
                        'UOM'=>$UOMExist->UnitID,
                        'Qty'=>$Qty,
                        'salesPrice'=>$salesPrice,
                        'discountAmountLine'=>$discountAmount,
                        'vatAmount'=>$vatAmount,
                        'by'=>$by,
                        'VATPercentage'=>$VATPercentage,
                        'discountPercentage'=>$discountPercentage,
                        'excelRow'=>$excelRow,
                    ];

                    $customerInvoiceDirects = $directInvoiceHeader['data'];
                    $DirectInvoiceDetailData['custInvoiceDirectAutoID'] = $customerInvoiceDirects->custInvoiceDirectAutoID;

                    $createDirectInvoiceDetails = $CustomerInvoiceService->createDirectInvoiceDetails($DirectInvoiceDetailData);

                    if($createDirectInvoiceDetails['status']){
                        $customerInvoiceDirectDetails = $createDirectInvoiceDetails['data'];
                        $customerInvoiceDirectDetails['excelRow'] = $excelRow;
                        $updateDirectInvoiceDetails = $CustomerInvoiceService->updateDirectInvoice($customerInvoiceDirectDetails);

                    }


                }

                $directInvoiceHeaderData = $directInvoiceHeader['data'];
                $params = array('autoID' => $directInvoiceHeaderData->custInvoiceDirectAutoID,
                    'company' => $directInvoiceHeaderData->companySystemID,
                    'document' => $directInvoiceHeaderData->documentSystemiD,
                    'confirmedBy' => $confirmedEmployee->employeeSystemID,
                    'employee_id' => $confirmedEmployee->employeeSystemID,
                    'segment' => '',
                    'fromUpload' => true,
                    'category' => '',
                    'amount' => ''
                );

                //checking whether document approved table has a data for the same document
                $docExist = DocumentApproved::where('documentSystemID', $params["document"])->where('documentSystemCode', $params["autoID"])->first();
                if (!$docExist) {
                    $confirm = \Helper::confirmDocument($params);
                    if (!$confirm["success"]) {

                        $errorMsg = $confirm["message"];
                        return ['status' => false, 'message' => $errorMsg, 'excelRow' =>$excelRow];

                    }
                }

            } else {
                return ['status' => false, 'message' => $directInvoiceHeader['message']];
            }

            $CIUploadDetailData['companySystemID']= $uploadedCompany;
            $CIUploadDetailData['customerInvoiceUploadID']= $uploadCustomerInvoice->id;
            $CIUploadDetailData['custInvoiceDirectID'] = $customerInvoiceDirects->custInvoiceDirectAutoID;
            $CIUploadDetailData['approvedByUserSystemID'] = $approvedEmployee ? $approvedEmployee->employeeSystemID : null;

            $createCIUploadDetail = CustomerInvoiceUploadDetail::create($CIUploadDetailData);

        }

		return ['status' => true];
	}

    public function createDirectInvoiceHeader($DirectInvoiceHeaderData)
    {
        $input = $DirectInvoiceHeaderData;

        $CustomerInvoiceDirectExist = CustomerInvoiceDirect::where('companySystemID', $input['companyID'])
                                    ->where('customerInvoiceNo', $input['customerInvoiceNo'])
                                    ->first();
        if($CustomerInvoiceDirectExist){
            return ['status' => true,'data'=>$CustomerInvoiceDirectExist];
        }
        
        $companyFinanceYearID = $input['companyFinanceYearID'];

        $company = Company::where('companySystemID', $input['companyID'])->first()->toArray();

        $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $companyFinanceYearID)->first();
        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $FYPeriodDateFrom = $companyfinanceperiod->dateFrom;
        $FYPeriodDateTo = $companyfinanceperiod->dateTo;
        $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
        $myCurr = $input['custTransactionCurrencyID'];

        $companyCurrency = \Helper::companyCurrency($company['companySystemID']);
        $companyCurrencyConversion = \Helper::currencyConversion($company['companySystemID'], $myCurr, $myCurr, 0);
        /*exchange added*/
        $input['custTransactionCurrencyER'] = 1;
        $input['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
        $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
        $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
        $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
   

        $lastSerial = CustomerInvoiceDirect::where('companySystemID', $input['companyID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));
        $bookingInvCode = ($company['CompanyID'] . '\\' . $y . '\\INV' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

        $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerID'])
            ->where('companySystemID', $input['companyID'])
            ->first();
        if ($customerGLCodeUpdate) {
            $input['customerVATEligible'] = $customerGLCodeUpdate->vatEligible;
        }

        $company = Company::where('companySystemID', $input['companyID'])->first();

        if ($company) {
            $input['vatRegisteredYN'] = $company->vatRegisteredYN;
        }

        $input['documentID'] = "INV";
        $input['documentSystemiD'] = 20;
        $input['bookingInvCode'] = $bookingInvCode;
        $input['serialNo'] = $lastSerialNumber;
        $input['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
        $input['FYEnd'] = $CompanyFinanceYear->endingDate;
        $input['FYPeriodDateFrom'] = $FYPeriodDateFrom;
        $input['FYPeriodDateTo'] = $FYPeriodDateTo;

        $input['invoiceDueDate'] = Carbon::parse($input['invoiceDueDate']);
        $input['bookingDate'] = Carbon::parse($input['bookingDate']);
        $input['date_of_supply'] = Carbon::parse($input['date_of_supply']);
        $input['customerInvoiceDate'] = $input['bookingDate'];
        $input['companySystemID'] = $input['companyID'];
        $input['companyID'] = $company['CompanyID'];
        $input['customerGLCode'] = $customer->custGLaccount;
        $input['customerGLSystemID'] = $customer->custGLAccountSystemID;
        $input['documentType'] = 11;
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdPcID'] = getenv('COMPUTERNAME');
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();



        if (($input['bookingDate'] >= $FYPeriodDateFrom) && ($input['bookingDate'] <= $FYPeriodDateTo)) {
            $customerInvoiceDirects = $this->customerInvoiceDirectRepository->create($input);
            return ['status' => true,'data'=>$customerInvoiceDirects];
        } else {
            $errorMsg = trans('custom.document_date_should_be_between_financial_period_start_end');
            return ['status' => false, 'message' => $errorMsg, 'excelRow' => $input['excelRow']];

        }

        return ['status' => true];
    }

    public function createDirectInvoiceDetails($DirectInvoiceDetailData)
    {


        $request = $DirectInvoiceDetailData;
        $companySystemID = $request['companySystemID'];
        $custInvoiceDirectAutoID = $request['custInvoiceDirectAutoID'];
        $glCode = $request['glCode'];
  
        $master = CustomerInvoiceDirect::select('*')->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();
        $bookingInvCode = $master->bookingInvCode;
   


        $tax = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('companySystemID', $master->companySystemID)
            ->where('documentSystemID', $master->documentSystemiD)
            ->first();
        if (!empty($tax)) {

        }

        $myCurr = $master->custTransactionCurrencyID;
        /*currencyID*/

        $decimal = \Helper::getCurrencyDecimalPlace($myCurr);
        $x = 0;


        $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $glCode)->first();
        

        $addToCusInvDetails['custInvoiceDirectID'] = $custInvoiceDirectAutoID;
        $addToCusInvDetails['companyID'] = $master->companyID;
        $addToCusInvDetails['customerID'] = $master->customerID;
        $addToCusInvDetails['glSystemID'] = $chartOfAccount->chartOfAccountSystemID;
        $addToCusInvDetails['glCode'] = $chartOfAccount->AccountCode;
        $addToCusInvDetails['glCodeDes'] = $chartOfAccount->AccountDescription;
        $addToCusInvDetails['accountType'] = $chartOfAccount->catogaryBLorPL;
        $addToCusInvDetails['serviceLineSystemID'] = $request['segment'];
        $addToCusInvDetails['serviceLineCode'] = $request['segmentCode'];

        if($request['comments'] != null){
            $addToCusInvDetails['comments'] = $request['comments'];
        } else {
            $addToCusInvDetails['comments'] = $master->comments;
        }

        $addToCusInvDetails['invoiceAmountCurrency'] = $master->custTransactionCurrencyID;
        $addToCusInvDetails['invoiceAmountCurrencyER'] = 1;

        $addToCusInvDetails['unitOfMeasure'] = $request['UOM'];
        $addToCusInvDetails['invoiceQty'] = $request['Qty'];
        $addToCusInvDetails['unitCost'] = $request['salesPrice'];
        $addToCusInvDetails['salesPrice'] = $request['salesPrice'];
        $addToCusInvDetails['VATAmount'] = $request['vatAmount'];
        $addToCusInvDetails['discountAmountLine'] = $request['discountAmountLine'];

        $addToCusInvDetails['projectID'] = $request['project'];


        $addToCusInvDetails['localCurrency'] = $master->localCurrencyID;
        $addToCusInvDetails['localCurrencyER'] = $master->localCurrencyER;

        $addToCusInvDetails['comRptCurrency'] = $master->companyReportingCurrencyID;
        $addToCusInvDetails['comRptCurrencyER'] = $master->companyReportingER;
        $totalAmount = ($addToCusInvDetails['unitCost'] != ''?$addToCusInvDetails['unitCost']:0) * ($addToCusInvDetails['invoiceQty'] != ''?$addToCusInvDetails['invoiceQty']:0);
        $totalAmount = ($totalAmount - ($addToCusInvDetails['discountAmountLine'] != ''?$addToCusInvDetails['discountAmountLine']:0) * ($addToCusInvDetails['invoiceQty'] != ''?$addToCusInvDetails['invoiceQty']:0));
        $MyRptAmount = 0;
        if ($master->custTransactionCurrencyID == $master->companyReportingCurrencyID) {
            $MyRptAmount = $totalAmount;
        } else {
            if ($master->companyReportingER > $master->custTransactionCurrencyER) {
                if ($master->companyReportingER > 1) {
                    $MyRptAmount = ($totalAmount / $master->companyReportingER);
                } else {
                    $MyRptAmount = ($totalAmount * $master->companyReportingER);
                }
            } else {
                if ($master->companyReportingER > 1) {
                    $MyRptAmount = ($totalAmount * $master->companyReportingER);
                } else {
                    $MyRptAmount = ($totalAmount / $master->companyReportingER);
                }
            }
        }
        $addToCusInvDetails["comRptAmount"] =   \Helper::roundValue($MyRptAmount);
        if ($master->custTransactionCurrencyID == $master->localCurrencyID) {
            $MyLocalAmount = $totalAmount;
        } else {
            if ($master->localCurrencyER > $master->custTransactionCurrencyER) {
                if ($master->localCurrencyER > 1) {
                    $MyLocalAmount = ($totalAmount / $master->localCurrencyER);
                } else {
                    $MyLocalAmount = ($totalAmount * $master->localCurrencyER);
                }
            } else {
                if ($master->localCurrencyER > 1) {
                    $MyLocalAmount = ($totalAmount * $master->localCurrencyER);
                } else {
                    $MyLocalAmount = ($totalAmount / $master->localCurrencyER);
                }
            }
        }
        $addToCusInvDetails["localAmount"] =  \Helper::roundValue($MyLocalAmount);


        if ($master->isVatEligible) {
            $vatDetails = TaxService::getDefaultVAT($master->companySystemID, $master->customerID, 0);
            $addToCusInvDetails['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
            $addToCusInvDetails['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
            $addToCusInvDetails['VATPercentage'] = $vatDetails['percentage'];
        }


        


        if(isset($request['byDiscount']) && ($request['byDiscount'] == 'discountPercentage' || $request['byDiscount'] == 'discountAmountLine')){
            if ($request['byDiscount'] === 'discountPercentage') {
              $addToCusInvDetails["discountAmountLine"] = $request['salesPrice'] * $request["discountPercentage"] / 100;
            } else if ($request['byDiscount'] === 'discountAmountLine') {
                if($request['salesPrice'] > 0){
                    $addToCusInvDetails["discountPercentage"] = ($request["discountAmountLine"] / $request['salesPrice']) * 100;
                } else {
                    $addToCusInvDetails["discountPercentage"] = 0;
                }
            }
        } else {
            if ($request['discountPercentage'] != 0) {
              $addToCusInvDetails["discountAmountLine"] = $request['salesPrice'] * $request["discountPercentage"] / 100;
            } else if ($request['discountAmountLine'] != 0){
                if($request['salesPrice'] > 0){
                    $addToCusInvDetails["discountPercentage"] = ($request["discountAmountLine"] / $request['salesPrice']) * 100;
                } else {
                    $addToCusInvDetails["discountPercentage"] = 0;
                }
            }
        }

        $addToCusInvDetails['unitCost'] = $request['salesPrice'] - $request["discountAmountLine"];

        $totalAmount = $addToCusInvDetails['unitCost'] * $request['Qty'];

        $addToCusInvDetails['invoiceAmount'] = round($totalAmount, $decimal);

        $addToCusInvDetails["VATPercentage"] = ($request["vatAmount"] / $addToCusInvDetails['unitCost']) * 100;

        $createdData = CustomerInvoiceDirectDetail::create($addToCusInvDetails);
        $details = CustomerInvoiceDirectDetail::select(DB::raw("SUM(invoiceAmount) as bookingAmountTrans"), DB::raw("SUM(localAmount) as bookingAmountLocal"), DB::raw("SUM(comRptAmount) as bookingAmountRpt"))->where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first()->toArray();

        CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($details);

        if($createdData){
            return ['status' => true,'data'=>$createdData];
        } else {
            $errorMsg = trans('custom.error_occurred');
            return ['status' => false, 'message' => $errorMsg, 'excelRow' => $request['excelRow']];
        }

    }

    public function updateDirectInvoice($customerInvoiceDirectDetails)
    {

        $input = $customerInvoiceDirectDetails;
        $input = array_except($input, array('unit', 'department','performadetails','contract', 'project'));
        $AppBaseController = new AppBaseController();
        $input = $AppBaseController->convertArrayToValue($input);
        $id = $input['custInvDirDetAutoID'];

        $detail = CustomerInvoiceDirectDetail::where('custInvDirDetAutoID', $id)->first();


        if (empty($detail)) {
            $errorMsg = trans('custom.customer_invoice_direct_detail_not_found');
            return ['status' => false, 'message' => $errorMsg, 'excelRow' => $input['excelRow']];

        }

        $master = CustomerInvoiceDirect::select('*')->where('custInvoiceDirectAutoID', $detail->custInvoiceDirectID)->first();

        if (empty($master)) {
            $errorMsg = trans('custom.customer_invoice_direct_not_found');
            return ['status' => false, 'message' => $errorMsg, 'excelRow' => $input['excelRow']];
        }

        $tax = Taxdetail::where('documentSystemCode', $detail->custInvoiceDirectID)
            ->where('companySystemID', $master->companySystemID)
            ->where('documentSystemID', $master->documentSystemiD)
            ->first();

        if (!empty($tax)) {

        }


        $validateVATCategories = TaxService::validateVatCategoriesInDocumentDetails($master->documentSystemiD, $master->companySystemID, $id, $input, $master->customerID, $master->isPerforma);

        if (!$validateVATCategories['status']) {
            $errorMsg = $validateVATCategories['message'];
            return ['status' => false, 'message' => $errorMsg, 'excelRow' => $input['excelRow']];
        } else {
            $input['vatMasterCategoryID'] = $validateVATCategories['vatMasterCategoryID'];        
            $input['vatSubCategoryID'] = $validateVATCategories['vatSubCategoryID'];        
        }

        if ($input['contractID'] != $detail->contractID) {

            $contract = Contract::select('ContractNumber', 'isRequiredStamp', 'paymentInDaysForJob', 'contractStatus')
                ->where('CompanyID', $detail->companyID)
                ->where('contractUID', $input['contractID'])
                ->first();

            $input['clientContractID'] = $contract->ContractNumber;

            if (!empty($contract)) {
                if($contract->contractStatus != 6){
                    if ($contract->paymentInDaysForJob <= 0) {
                        $errorMsg = trans('custom.payment_period_not_updated_in_contract');
                        return ['status' => false, 'message' => $errorMsg, 'excelRow' => $input['excelRow']];
                    }
                }
            } else {
                $errorMsg = trans('custom.contract_not_exist');
                return ['status' => false, 'message' => $errorMsg, 'excelRow' => $input['excelRow']];
            }
        }

        if (isset($input["discountPercentage"]) && $input["discountPercentage"] > 100) {
            $errorMsg = trans('custom.discount_percentage_cannot_be_greater_than_100');
            return ['status' => false, 'message' => $errorMsg, 'excelRow' => $input['excelRow']];
        }

        if (isset($input["discountAmountLine"]) && isset($input['salesPrice']) && $input['discountAmountLine'] > $input['salesPrice']) {
            $errorMsg = trans('custom.discount_amount_cannot_be_greater_than_sales_price');
            return ['status' => false, 'message' => $errorMsg, 'excelRow' => $input['excelRow']];
        }

        if ($input['serviceLineSystemID'] != $detail->serviceLineSystemID) {

            $serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')->where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
            $input['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
            $input['serviceLineCode'] = $serviceLine->ServiceLineCode;
            $input['contractID'] = NULL;
            $input['clientContractID'] = NULL;
        }

        if($input['serviceLineSystemID'] == 0){
            $input['serviceLineSystemID'] = null;
            $input['serviceLineCode'] = null;
        }

        $input['invoiceQty']= ($input['invoiceQty'] != ''?$input['invoiceQty']:0);
        $input['salesPrice']= ($input['salesPrice'] != '' ? $input['salesPrice'] : 0);


        if(isset($input['by']) && ($input['by'] == 'discountPercentage' || $input['by'] == 'discountAmountLine')){
            if ($input['by'] === 'discountPercentage') {
              $input["discountAmountLine"] = $input['salesPrice'] * $input["discountPercentage"] / 100;
            } else if ($input['by'] === 'discountAmountLine') {
                if($input['salesPrice'] > 0){
                    $input["discountPercentage"] = ($input["discountAmountLine"] / $input['salesPrice']) * 100;
                } else {
                    $input["discountPercentage"] = 0;
                }
            }
        } else {
            if ($input['discountPercentage'] != 0) {
              $input["discountAmountLine"] = $input['salesPrice'] * $input["discountPercentage"] / 100;
            } else if ($input['discountAmountLine'] != 0){
                if($input['salesPrice'] > 0){
                    $input["discountPercentage"] = ($input["discountAmountLine"] / $input['salesPrice']) * 100;
                } else {
                    $input["discountPercentage"] = 0;
                }
            }
        }

        $input['unitCost'] = $input['salesPrice'] - $input["discountAmountLine"];
        if ($input['invoiceQty'] != $detail->invoiceQty || $input['unitCost'] != $detail->unitCost) {
            $myCurr = $master->custTransactionCurrencyID;               /*currencyID*/
            $decimal = \Helper::getCurrencyDecimalPlace($myCurr);

            $input['invoiceAmountCurrency'] = $master->custTransactionCurrencyID;
            $input['invoiceAmountCurrencyER'] = 1;
            $totalAmount = ($input['unitCost'] != ''?$input['unitCost']:0) * ($input['invoiceQty'] != ''?$input['invoiceQty']:0);
            $input['invoiceAmount'] = round($totalAmount, $decimal);
            
            if($master->isPerforma == 2) {
                $totalAmount = $input['salesPrice'];
                $input['invoiceAmount'] = round($input['salesPrice'], $decimal);
            }

            /**/
               $MyRptAmount = 0;
               if ($master->custTransactionCurrencyID == $master->companyReportingCurrencyID) {
                   $MyRptAmount = $totalAmount;
               } else {
                   if ($master->companyReportingER > $master->custTransactionCurrencyER) {
                       if ($master->companyReportingER > 1) {
                           $MyRptAmount = ($totalAmount / $master->companyReportingER);
                       } else {
                           $MyRptAmount = ($totalAmount * $master->companyReportingER);
                       }
                   } else {
                       if ($master->companyReportingER > 1) {
                           $MyRptAmount = ($totalAmount * $master->companyReportingER);
                       } else {
                           $MyRptAmount = ($totalAmount / $master->companyReportingER);
                       }
                   }
               }
            $input["comRptAmount"] =   \Helper::roundValue($MyRptAmount);
                if ($master->custTransactionCurrencyID == $master->localCurrencyID) {
                     $MyLocalAmount = $totalAmount;
                 } else {
                     if ($master->localCurrencyER > $master->custTransactionCurrencyER) {
                         if ($master->localCurrencyER > 1) {
                             $MyLocalAmount = ($totalAmount / $master->localCurrencyER);
                         } else {
                             $MyLocalAmount = ($totalAmount * $master->localCurrencyER);
                         }
                     } else {
                         if ($master->localCurrencyER > 1) {
                             $MyLocalAmount = ($totalAmount * $master->localCurrencyER);
                         } else {
                             $MyLocalAmount = ($totalAmount / $master->localCurrencyER);
                         }
                     }
                 }
            $input["localAmount"] =  \Helper::roundValue($MyLocalAmount);


        }

        if(isset($input['by']) && ($input['by'] == 'VATPercentage' || $input['by'] == 'VATAmount')){
            if ($input['by'] === 'VATPercentage') {
              $input["VATAmount"] = $input['unitCost'] * $input["VATPercentage"] / 100;
            } else if ($input['by'] === 'VATAmount') {
                if($input['unitCost'] > 0){
                    $input["VATPercentage"] = ($input["VATAmount"] / $input['unitCost']) * 100;
                } else {
                    $input["VATPercentage"] = 0;
                }
            }
        } else {
            if ($input['VATPercentage'] != 0) {
              $input["VATAmount"] = $input['unitCost'] * $input["VATPercentage"] / 100;
            } else if ($input['VATAmount'] != 0){
                if($input['unitCost'] > 0){
                    $input["VATPercentage"] = ($input["VATAmount"] / $input['unitCost']) * 100;
                } else {
                    $input["VATPercentage"] = 0;
                }
            }
        }

        $currencyConversionVAT = \Helper::currencyConversion($master->companySystemID, $master->custTransactionCurrencyID, $master->custTransactionCurrencyID, $input['VATAmount']);
        $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();
        $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;
        if($policy == true) {
            $input['VATAmountLocal'] = \Helper::roundValue($input["VATAmount"] / $master->localCurrencyER);
            $input['VATAmountRpt'] = \Helper::roundValue($input["VATAmount"] / $master->companyReportingER);
        }
        if($policy == false) {
            $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
            $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
        }
        if (isset($input['by'])) {
            unset($input['by']);
        }

        if (isset($input['vatMasterCategoryAutoID'])) {
            unset($input['vatMasterCategoryAutoID']);
        }

        if (isset($input['itemPrimaryCode'])) {
            unset($input['itemPrimaryCode']);
        }
        
        if (isset($input['itemDescription'])) {
            unset($input['itemDescription']);
        }

        if (isset($input['subCategoryArray'])) {
            unset($input['subCategoryArray']);
        }

        if (isset($input['subCatgeoryType'])) {
            unset($input['subCatgeoryType']);
        }

        if (isset($input['exempt_vat_portion'])) {
            unset($input['exempt_vat_portion']);
        }

        $excelRow = $input['excelRow'];
        if (isset($input['excelRow'])) {
            unset($input['excelRow']);
        }

        $inputArray = json_decode(json_encode($input), true);


        $x=CustomerInvoiceDirectDetail::where('custInvDirDetAutoID', $detail->custInvDirDetAutoID)->update($inputArray);
        $allDetail = CustomerInvoiceDirectDetail::select(DB::raw("IFNULL(SUM(invoiceAmount),0) as bookingAmountTrans"), DB::raw("IFNULL(SUM(localAmount),0) as bookingAmountLocal"), DB::raw("IFNULL(SUM(comRptAmount),0) as bookingAmountRpt"))->where('custInvoiceDirectID', $detail->custInvoiceDirectID)->first()->toArray();

        CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $detail->custInvoiceDirectID)->update($allDetail);

        if($master->isPerforma != 2) {
            $resVat = $this->updateTotalVAT($master->custInvoiceDirectAutoID);
            if (!$resVat['status']) {
                $errorMsg = $resVat['message'];
                return ['status' => false, 'message' => $errorMsg, 'excelRow' => $excelRow];
            } 
        }
        if($x){
            return ['status' => true];
        } else {
            $errorMsg = trans('custom.error_occurred_while_updating_customer_invoice_direct_details');
            return ['status' => false, 'message' => $errorMsg, 'excelRow' => $excelRow];
        }

    }

    public function updateTotalVAT($custInvoiceDirectAutoID)
    {
        $invoiceDetails = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)
                                                    ->get();

        $totalVATAmount = 0;
        $invoice = CustomerInvoiceDirect::find($custInvoiceDirectAutoID);

        foreach ($invoiceDetails as $key => $value) {
            $totalVATAmount += $value->invoiceQty * $value->VATAmount;
        }

        $taxDelete = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
                              ->where('documentSystemID', 20)
                              ->delete();

        if ($totalVATAmount > 0) {
            $res = $this->savecustomerInvoiceTaxDetails($custInvoiceDirectAutoID, $totalVATAmount);

            if (!$res['status']) {
               return ['status' => false, 'message' => $res['message']];
            } 
        } else {
            $vatAmount['vatOutputGLCodeSystemID'] = null;
            $vatAmount['vatOutputGLCode'] = null;
            $vatAmount['VATPercentage'] = 0;
            $vatAmount['VATAmount'] = 0;
            $vatAmount['VATAmountLocal'] = 0;
            $vatAmount['VATAmountRpt'] = 0;

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);
        }


        return ['status' => true];
    }

    public function savecustomerInvoiceTaxDetails($custInvoiceDirectAutoID, $totalVATAmount)
    {
        $percentage = 0;
        $taxMasterAutoID = 0;

        $master = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();

        if (empty($master)) {
            return ['status' => false, 'message' => trans('custom.customer_invoice_not_found')];
        }

        $invoiceDetail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();
      
        if (empty($invoiceDetail)) {
            return ['status' => false, 'message' => trans('custom.invoice_details_not_found')];
        }

        $totalAmount = 0;
        $decimal = \Helper::getCurrencyDecimalPlace($master->custTransactionCurrencyID);

        $totalDetail = CustomerInvoiceDirectDetail::select(DB::raw("SUM(invoiceAmount) as amount"))->where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();
        if (!empty($totalDetail)) {
            $totalAmount = $totalDetail->amount;
        }

        if ($totalAmount > 0) {
            $percentage = ($totalVATAmount / $totalAmount) * 100;
        }

        $Taxdetail = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('documentSystemID', 20)
            ->first();

        if (!empty($Taxdetail)) {
            return ['status' => false, 'message' => trans('custom.vat_detail_already_exist')];
        }

        $currencyConversion = \Helper::currencyConversion($master->companySystemID, $master->custTransactionCurrencyID, $master->custTransactionCurrencyID, $totalVATAmount);


        $_post['taxMasterAutoID'] = $taxMasterAutoID;
        $_post['companyID'] = $master->companyID;
        $_post['companySystemID'] = $master->companySystemID;
        $_post['documentID'] = 'INV';
        $_post['documentSystemID'] = $master->documentSystemiD;
        $_post['documentSystemCode'] = $custInvoiceDirectAutoID;
        $_post['documentCode'] = $master->bookingInvCode;
        $_post['taxShortCode'] = ''; //$taxMaster->taxShortCode;
        $_post['taxDescription'] = ''; //$taxMaster->taxDescription;
        $_post['taxPercent'] = $percentage; //$taxMaster->taxPercent;
        $_post['payeeSystemCode'] = $master->customerID; //$taxMaster->payeeSystemCode;
        $_post['currency'] = $master->custTransactionCurrencyID;
        $_post['currencyER'] = $master->custTransactionCurrencyER;
        $_post['amount'] = round($totalVATAmount, $decimal);
        $_post['payeeDefaultCurrencyID'] = $master->custTransactionCurrencyID;
        $_post['payeeDefaultCurrencyER'] = $master->custTransactionCurrencyER;
        $_post['payeeDefaultAmount'] = round($totalVATAmount, $decimal);
        $_post['localCurrencyID'] = $master->localCurrencyID;
        $_post['localCurrencyER'] = $master->localCurrencyER;

        $_post['rptCurrencyID'] = $master->companyReportingCurrencyID;
        $_post['rptCurrencyER'] = $master->companyReportingER;

        if ($_post['currency'] == $_post['rptCurrencyID']) {
            $MyRptAmount = $totalVATAmount;
        } else {
            if ($_post['rptCurrencyER'] > $_post['currencyER']) {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                }
            } else {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                }
            }
        }
        $_post["rptAmount"] = \Helper::roundValue($MyRptAmount);
        if ($_post['currency'] == $_post['localCurrencyID']) {
            $MyLocalAmount = $totalVATAmount;
        } else {
            if ($_post['localCurrencyER'] > $_post['currencyER']) {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                }
            } else {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                }
            }
        }

        $_post["localAmount"] = \Helper::roundValue($MyLocalAmount);
       
        Taxdetail::create($_post);
        $company = Company::select('vatOutputGLCode', 'vatOutputGLCodeSystemID')->where('companySystemID', $master->companySystemID)->first();

        $vatAmount['vatOutputGLCodeSystemID'] = $company->vatOutputGLCodeSystemID;
        $vatAmount['vatOutputGLCode'] = $company->vatOutputGLCode;
        $vatAmount['VATPercentage'] = $percentage;
        $vatAmount['VATAmount'] = $_post['amount'];
        $vatAmount['VATAmountLocal'] = $_post["localAmount"];
        $vatAmount['VATAmountRpt'] = $_post["rptAmount"];


        CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);

        return ['status' => true];
    }

    public static function deleteCustomerInvoice($customerInvoiceUploadDetails):array
    {

        $id = $customerInvoiceUploadDetails->custInvoiceDirectID;
        $masterData = CustomerInvoiceDirect::find($id);
        if (empty($masterData)) {
            return ['status' => false, 'message' => trans('custom.customer_invoice_not_found')];
        }
        
        //deleting from approval table
        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $masterData->companySystemID)
            ->where('documentSystemID', $masterData->documentSystemiD)
            ->delete();

        //deleting from general ledger table
        $deleteGLData = GeneralLedger::where('documentSystemCode', $id)
            ->where('companySystemID', $masterData->companySystemID)
            ->where('documentSystemID', $masterData->documentSystemiD)
            ->delete();

        //deleting records from accounts receivable
        $deleteARData = AccountsReceivableLedger::where('documentCodeSystem', $id)
            ->where('companySystemID', $masterData->companySystemID)
            ->where('documentSystemID', $masterData->documentSystemiD)
            ->delete();

        //deleting records from tax ledger
        $deleteTaxLedgerData = TaxLedger::where('documentMasterAutoID', $id)
            ->where('companySystemID', $masterData->companySystemID)
            ->where('documentSystemID', $masterData->documentSystemiD)
            ->delete();


        TaxLedgerDetail::where('documentMasterAutoID', $id)
            ->where('companySystemID', $masterData->companySystemID)
            ->where('documentSystemID', $masterData->documentSystemiD)
            ->delete();

        $masterData->invoicedetail()->delete();
        $masterData->delete();

        return ['status' => true, 'message' => trans('custom.customer_invoice_deleted_successfully')];


    }

    public static function processExcelData($uploadData)
    {
        $objPHPExcel = $uploadData['objPHPExcel'];
        $uploadedCompany = $uploadData['uploadedCompany'];

        $sheet  = $objPHPExcel->getActiveSheet();
        $startRow = 13;
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $detailRows = [];
        $rowNumber = 13;
        for ($row = $startRow; $row <= $highestRow; ++$row) {
            $rowData = [];
            for ($col = 'A'; $col <= $highestColumn; ++$col) {
                $cellValue = $sheet->getCell($col . $row)->getValue();

                if ($col == 'E' || $col == 'F') {
                    // Check if the value looks like a numeric date
                    if (is_numeric($cellValue) && $cellValue > 25569) {
                        // Convert the numeric date to day, month, year
                        $unixTimestamp = ($cellValue - 25569) * 86400;
                        $day = date('d', $unixTimestamp);
                        $month = date('m', $unixTimestamp);
                        $year = date('Y', $unixTimestamp);

                        // Format it as MM/DD/YYYY
                        $cellValue = sprintf('%02d/%02d/%04d', $month, $day, $year);
                    }
                }

                $rowData[] = $cellValue;
            }

            $rowData[] = $rowNumber;
            $detailRows[] = $rowData;
            $rowNumber ++;
        }

        return $detailRows;
    }

    public static function processDeleteCustomerInvoiceUpload($customerInvoiceUploadID)
    {
        $customerInvoiceUploadDetails = CustomerInvoiceUploadDetail::where('customerInvoiceUploadID',$customerInvoiceUploadID)->get();

        foreach ($customerInvoiceUploadDetails as $customerInvoiceUploadDetail) {
            $deleteCustomerInvoice  = self::deleteCustomerInvoice($customerInvoiceUploadDetail);
            if(isset($deleteCustomerInvoice['status']) && !$deleteCustomerInvoice['status']) { 
                return ['status' => false];
            }
        }
    }

}
