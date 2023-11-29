<?php

namespace App\helper;

use App\Http\Controllers\AppBaseController;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\BankMaster;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\CurrencyMaster;
use App\Models\CustomerAssigned;
use App\Models\CustomerCurrency;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerMaster;
use App\Models\Employee;
use App\Models\EmployeesDepartment;
use App\Models\ErpProjectMaster;
use App\Models\LogUploadCustomerInvoice;
use App\Repositories\CustomerInvoiceDirectRepository;
use App\Models\SegmentMaster;
use App\Models\Taxdetail;
use App\Models\Unit;
use App\Models\UploadCustomerInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CustomerInvoiceService
{
    /** @var  CustomerInvoiceDirectRepository */
    private $customerInvoiceDirectRepository;

    public function __construct(CustomerInvoiceDirectRepository $customerInvoiceDirectRepo)
    {
        $this->customerInvoiceDirectRepository = $customerInvoiceDirectRepo;
    }

	public static function customerInvoiceCreate($uploadData)
	{
        
        $customerInvoiceDirectRepo = app()->make(CustomerInvoiceDirectRepository::class);

        // Instantiate CustomerInvoiceService with the repository as an argument
        $CustomerInvoiceService = new CustomerInvoiceService($customerInvoiceDirectRepo);


        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);

            
        $uploadCustomerInvoice = $uploadData['uploadCustomerInvoice'];
        $logUploadCustomerInvoice = $uploadData['logUploadCustomerInvoice'];
        $employee = $uploadData['employee'];
        $objPHPExcel = $uploadData['objPHPExcel'];
        $uploadedCompany = $uploadData['uploadedCompany'];

        $sheet  = $objPHPExcel->getActiveSheet();
        $startRow = 11;
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $detailRows = [];

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
                        $cellValue = sprintf('%02d-%02d-%04d', $month, $day, $year);
                    }
                }
                
                $rowData[] = $cellValue;
            }
            $detailRows[] = $rowData;
        }
        
        $excelRow = 10;
        $errorMsg = "";
        $errorEnabled = false;

        foreach($detailRows as $detailValue){
            $excelRow++;
            $invoiceNo = $detailValue[6]; 
            if($invoiceNo != null){
                $ifExistCustomerInvoiceDirect = CustomerInvoiceDirect::where('customerInvoiceNo',$invoiceNo)->first();
                if($ifExistCustomerInvoiceDirect){
                    $errorMsg = "Customer Invoice No $invoiceNo already exist.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            }
        }

        $excelRow = 10;
        $errorMsg = "";
        foreach($detailRows as $value){
            $excelRow++;

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
            

            //Check Customer Code & CR Number both have value
            if($cutomerCode == null && $crNumber == null){
                $errorMsg = "In the fields Customer Code & CR Number at lease one of the field should have a value.";
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                return ['status' => false];
            }

            //Check Customer Active for cutomerCode
            if($cutomerCode != null && $crNumber != null){
                $customerMasters = CustomerMaster::where('CutomerCode',$cutomerCode)
                ->where('customer_registration_no',$crNumber)
                ->where('approvedYN',1)
                ->first();

                if(!$customerMasters){
                    $errorMsg = "Active cutomer not found for the customer code $cutomerCode & CR Number $crNumber";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            } elseif ($cutomerCode != null){
                $customerMasters = CustomerMaster::where('CutomerCode',$cutomerCode)
                ->where('approvedYN',1)
                ->first();

                if(!$customerMasters){
                    $errorMsg = "Active cutomer not found for the customer code $cutomerCode";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            } elseif ($crNumber != null){
                $customerMasters = CustomerMaster::where('customer_registration_no',$crNumber)
                ->where('approvedYN',1)
                ->first();
                
                if(!$customerMasters){
                    $errorMsg = "Active cutomer not found for the customer registration number $crNumber";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            }

            //Check Currency for Currency code
            if($currency == null){
                $errorMsg = "Currency field can not be null.";
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                return ['status' => false];
            }

            if($currency != null){
                $currencyMaster = CurrencyMaster::where('CurrencyCode',$currency)
                ->first();

                if(!$currencyMaster){
                    $errorMsg = "Currency master not found for the currency code $currency";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }

                //check customer assigned currency
                if($customerMasters && $currencyMaster){
                    // return $customerMasters;
                    $customerCurrency = CustomerCurrency::where('customerCode',$customerMasters->CutomerCode)
                                                            ->where('currencyID',$currencyMaster->currencyID)
                                                            ->where('isAssigned',-1)
                                                            ->first();

                    if(!$customerCurrency){
                        $errorMsg = "Currency $currency is not assigned to the customer $customerMasters->customerCode";
                        UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                        LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                        return ['status' => false];
                    }
                }

            }

            
            //Check headerComments
            if($headerComments == null){
                $errorMsg = "Header comments field can not be null.";
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                return ['status' => false];
            }

            //Check documentDate  //Check Active Financial Period
            if($documentDate == null){
                $errorMsg = "Document Date field can not be null.";
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                return ['status' => false];
            }
            
            if($documentDate != null){

                try{
                    $documentDate = Carbon::parse($documentDate)->format('Y-m-d') . ' 00:00:00';
                }
                catch (\Exception $e){

                    $errorMsg = "Invalid Document date format  $documentDate.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }

                $companyFinanceYear = Helper::companyFinanceYear($uploadedCompany, 0);

                $CompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', '=', $uploadedCompany)
                                        ->where('companyFinanceYearID', $companyFinanceYear[0]['companyFinanceYearID'])
                                        ->where('departmentSystemID', 4)
                                        ->where('isActive', -1)
                                        ->where('isCurrent', -1)
                                        ->first();

                $fromDate = Carbon::parse($CompanyFinancePeriod->dateFrom);
                $toDate = Carbon::parse($CompanyFinancePeriod->dateTo);
                $checkDate = Carbon::parse($documentDate);

                if (!$checkDate->between($fromDate, $toDate)) {
                    $errorMsg = "The financial period for the  Document date $documentDate  is not active.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            }

            //Check invoiceDueDate
            if($invoiceDueDate == null){
                $errorMsg = "invoice Due Date field can not be null.";
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                return ['status' => false];
            }
            
            if($invoiceDueDate != null){
                try{
                    $invoiceDueDate = Carbon::parse($invoiceDueDate)->format('Y-m-d') . ' 00:00:00';
                }
                catch (\Exception $e){

                    $errorMsg = "Invalid Invoice Due Date format  $invoiceDueDate.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            }

            //customerInvoiceNo
            if($customerInvoiceNo == null){
                $errorMsg = "customerInvoiceNo field can not be null.";
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                return ['status' => false];
            }

            //bank
            if($bank == null){
                $errorMsg = "Bank field can not be null.";
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                return ['status' => false];
            }

            if($bank != null){
                $bankMaster = BankAssign::where('bankShortCode',$bank)->first();
                if(!$bankMaster){
                    $errorMsg = "Bank not found for bank code $bank.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }

                if($bankMaster){
                    //Account No
                    if($accountNo == null){
                        $errorMsg = "Account No field can not be null.";
                        UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                        LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                        return ['status' => false];
                    }

                    if($accountNo != null){
                        $account = BankAccount::where('AccountNo',$accountNo)
                                                ->where('bankmasterAutoID',$bankMaster->bankmasterAutoID)
                                                ->first();
                        if(!$account){
                            $errorMsg = "Bank Account not found for bank code $bank & Account No $accountNo.";
                            UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                            LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                            return ['status' => false];
                        }
                    }
                }

            }

            if ($confirmedBy != null){
                $confirmedEmployee = Employee::where('empID',$confirmedBy)
                                                ->where('empActive',1)
                                                ->first();
                if(!$confirmedEmployee){
                    $errorMsg = "Active employee not found for Confirmed By $confirmedBy.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }

            }

            if ($approvedBy != null){
                $approvedEmployee = Employee::where('empID',$approvedBy)
                                    ->where('empActive',1)
                                    ->first();

                if(!$approvedEmployee){
                    $errorMsg = "Active employee not found for Approved By $approvedBy.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }

                //Check Approval Acces
                $approvalAccess = EmployeesDepartment::where('employeeID',$approvedEmployee->empID)
                                                        ->where('documentSystemID',18)
                                                        ->where('companySystemID',$uploadedCompany)
                                                        ->where('isActive',1)
                                                        ->first();
                
                if(!$approvalAccess){
                    $errorMsg = "Approver $approvedBy not found in approval list.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            }

            if($approvedBy == null){
                $approvedEmployee = $employee;

                //Check Approval Acces
                $approvalAccess = EmployeesDepartment::where('employeeID',$employee->empID)
                                                        ->where('documentSystemID',18)
                                                        ->where('companySystemID',$uploadedCompany)
                                                        ->where('isActive',1)
                                                        ->first();
                
                if(!$approvalAccess){
                    $errorMsg = "Uploaded employee $employee->empID not found in approval list.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            }


            //DETAIL LEVEL DATA
            $glCode = $value[11]; //mandatory
            $project = $value[12];  
            $segment = $value[13];  //mandatory
            $detailComments = $value[14];
            $UOM = $value[15]; //mandatory
            $Qty = $value[16]; //mandatory
            $salesPrice = $value[17]; //mandatory
            $discountAmount = $value[18]; 
            $vatAmount = $value[19];

            if($Qty != null){
                if (!is_numeric($Qty)) {
                    $errorMsg = "QTY should be numeric.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            } elseif($salesPrice != null) {
                if (!is_numeric($salesPrice)) {
                    $errorMsg = "Sales Price should be numeric.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            } elseif($discountAmount != null) {
                if (!is_numeric($discountAmount)) {
                    $errorMsg = "Discount Amount should be numeric.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            } elseif($vatAmount != null) {
                if (!is_numeric($vatAmount)) {
                    $errorMsg = "VAT Amount should be numeric.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            }

            if($glCode == null){
                $errorMsg = "GL Account field can not be null.";
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                return ['status' => false];
            }

            if($glCode != null){
                $chartOfAccounts = ChartOfAccountsAssigned::where('AccountCode',$glCode)
                                                    ->where('companySystemID', $uploadedCompany)
                                                    ->where('isAssigned', -1)
                                                    ->where('isActive', 1)
                                                    ->first();

                if(!$chartOfAccounts){
                    $errorMsg = "Chart Of Account not found for the GL Code $glCode.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            }


            if($project != null){
                $projectExist = ErpProjectMaster::where('projectCode',$project)
                                                    ->first();
                
                if(!$projectExist){
                    $project= null;
                    $errorMsg = "Project master not for the Project Code $project.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                } else {
                    $project= $projectExist->id;
                }
            }  else {
                $project= null;
            }

            if($segment == null){
                $errorMsg = "Segment field can not be null.";
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                return ['status' => false];
            }

            if($segment != null){
                $segmentExist = SegmentMaster::where('ServiceLineCode',$segment)
                                                ->where('companySystemID',$uploadedCompany)
                                                ->where('isActive',1)
                                                ->where('isDeleted',0)
                                                ->first();

                if(!$segmentExist){
                    $errorMsg = "Active Segment master not for the Segment Code $segment.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            }

            if($UOM == null){
                $errorMsg = "UOM field can not be null.";
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                return ['status' => false];
            }

            if($UOM != null){
                $UOMExist = Unit::where('UnitShortCode',$UOM)
                                ->where('is_active',1)
                                ->first();

                if(!$UOMExist){
                    $errorMsg = "Active UOM master not for the UOM Short Code $UOM.";
                    UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                    LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                    return ['status' => false];
                }
            }

            
            if($Qty == null){
                $errorMsg = "Quantity field can not be null.";
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                return ['status' => false];
            }

            if($salesPrice == null){
                $errorMsg = "Sales Price field can not be null.";
                UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 0]);
                LogUploadCustomerInvoice::where('id', $logUploadCustomerInvoice->id)->update(['is_failed' => 1,'error_line'=>$excelRow, 'log_message' => $errorMsg]);
                return ['status' => false];
            }

            if($vatAmount != null){
                $by = 'vatAmount';
                $VATPercentage = 0;
            } else {
                $by = 'VATPercentage';
                $vatAmount = 0;
                $VATPercentage = 0;
            }

            if($discountAmount != null){
                $byDiscount = 'discountAmountLine';
                $discountPercentage = 0;
            } else {
                $byDiscount = 'discountPercentage';
                $discountAmount = 0;
                $discountPercentage = 0;
            }

            

            $DirectInvoiceHeaderData = [
                'bookingDate'=>$documentDate,
                'comments'=>$headerComments,
                'companyFinancePeriodID'=>$CompanyFinancePeriod->companyFinancePeriodID,
                'companyFinanceYearID'=>$companyFinanceYear[0]['companyFinanceYearID'],
                'companyID'=>$uploadedCompany,
                'custTransactionCurrencyID'=>$customerCurrency->currencyID,
                'customerID'=>$customerMasters->customerCodeSystem,
                'invoiceDueDate'=>$invoiceDueDate,
                'date_of_supply'=>$documentDate,
                'isPerforma'=>0,
                'isUpload'=>1,
                'excelRow'=>$excelRow,
                'bankID'=>$bankMaster->bankAssignedAutoID,
                'bankAccountID'=>$account->bankAccountAutoID,
                'customerInvoiceNo'=>$customerInvoiceNo,
                'uploadCustomerInvoice'=>$uploadCustomerInvoice->id,
                'logUploadCustomerInvoice'=>$logUploadCustomerInvoice->id
            ];

            $DirectInvoiceDetailData = [
                'glCode'=>$chartOfAccounts->chartOfAccountSystemID,
                'project'=>$project,
                'comments'=>$detailComments,
                'segment'=>$segmentExist->serviceLineSystemID,
                'companySystemID'=>$uploadedCompany,
                'UOM'=>$UOMExist->UnitID,
                'Qty'=>$Qty,
                'salesPrice'=>$salesPrice,
                'discountAmountLine'=>$discountAmount,
                'vatAmount'=>$vatAmount,
                'by'=>$by,
                'VATPercentage'=>$VATPercentage,
                'discountPercentage'=>$discountPercentage,
            ];
            
            $directInvoiceHeader = $CustomerInvoiceService->createDirectInvoiceHeader($DirectInvoiceHeaderData);

            if ($directInvoiceHeader['status']) {
                $customerInvoiceDirects = $directInvoiceHeader['data'];
                $DirectInvoiceDetailData['custInvoiceDirectAutoID'] = $customerInvoiceDirects->custInvoiceDirectAutoID;

                $createDirectInvoiceDetails = $CustomerInvoiceService->createDirectInvoiceDetails($DirectInvoiceDetailData);
                
                if($createDirectInvoiceDetails['status']){
                    $customerInvoiceDirectDetails = $createDirectInvoiceDetails['data'];
                    $updateDirectInvoiceDetails = $CustomerInvoiceService->updateDirectInvoice($customerInvoiceDirectDetails);

                }
            } 

        }

        UploadCustomerInvoice::where('id', $uploadCustomerInvoice->id)->update(['uploadStatus' => 1]);
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
        try{
            $input['invoiceDueDate'] = Carbon::parse($input['invoiceDueDate'])->format('Y-m-d') . ' 00:00:00';
        }
        catch (\Exception $e){
            $errorMsg = "Invalid Due Date format.";
            UploadCustomerInvoice::where('id',$input['uploadCustomerInvoice'])->update(['uploadStatus' => 0]);
            LogUploadCustomerInvoice::where('id',$input['logUploadCustomerInvoice'])->update(['is_failed' => 1,'error_line'=>$input['excelRow'], 'log_message' => $errorMsg]);
            return ['status' => false];
        }
        $input['bookingDate'] = Carbon::parse($input['bookingDate'])->format('Y-m-d') . ' 00:00:00';
        $input['date_of_supply'] = Carbon::parse($input['date_of_supply'])->format('Y-m-d') . ' 00:00:00';
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


        $curentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
        if ($input['bookingDate'] > $curentDate) {
            $errorMsg = "Document date cannot be greater than current date.";
            UploadCustomerInvoice::where('id',$input['uploadCustomerInvoice'])->update(['uploadStatus' => 0]);
            LogUploadCustomerInvoice::where('id',$input['logUploadCustomerInvoice'])->update(['is_failed' => 1,'error_line'=>$input['excelRow'], 'log_message' => $errorMsg]);
            return ['status' => false];
        }
        if (($input['bookingDate'] >= $FYPeriodDateFrom) && ($input['bookingDate'] <= $FYPeriodDateTo)) {
            $customerInvoiceDirects = $this->customerInvoiceDirectRepository->create($input);
            return ['status' => true,'data'=>$customerInvoiceDirects];
        } else {
            $errorMsg = "Document date should be between financial period start date and end date.";
            UploadCustomerInvoice::where('id',$input['uploadCustomerInvoice'])->update(['uploadStatus' => 0]);
            LogUploadCustomerInvoice::where('id',$input['logUploadCustomerInvoice'])->update(['is_failed' => 1,'error_line'=>$input['excelRow'], 'log_message' => $errorMsg]);
            return ['status' => false];
        }

        return ['status' => true];
    }

    public function createDirectInvoiceDetails($DirectInvoiceDetailData)
    {


        $request = $DirectInvoiceDetailData;
        $companySystemID = $request['companySystemID'];
        /* $contractID = $request['contractID'];*/
        $custInvoiceDirectAutoID = $request['custInvoiceDirectAutoID'];
        $glCode = $request['glCode'];
        /* $qty = $request['qty'];*/
        /* $serviceLineSystemID = $request['serviceLineSystemID'];
         $unitCost = $request['unitCost'];
         $unitID = $request['unitID'];*/


        /*this*/


        /*get master*/
        $master = CustomerInvoiceDirect::select('*')->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();
        $bookingInvCode = $master->bookingInvCode;
        /*selectedPerformaMaster*/


        $tax = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('companySystemID', $master->companySystemID)
            ->where('documentSystemID', $master->documentSystemiD)
            ->first();
        if (!empty($tax)) {
            // return $this->sendError('Please delete tax details to continue !');
        }

        $myCurr = $master->custTransactionCurrencyID;
        /*currencyID*/

        //$companyCurrency = \Helper::companyCurrency($myCurr);
        $decimal = \Helper::getCurrencyDecimalPlace($myCurr);
        $x = 0;


        /*$serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')->where('serviceLineSystemID', $serviceLineSystemID)->first();*/
        $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $glCode)->first();
        

        $addToCusInvDetails['custInvoiceDirectID'] = $custInvoiceDirectAutoID;
        $addToCusInvDetails['companyID'] = $master->companyID;
        /*  $addToCusInvDetails['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;*/
        /*        $addToCusInvDetails['serviceLineCode'] = $serviceLine->ServiceLineCode;*/
        $addToCusInvDetails['customerID'] = $master->customerID;
        $addToCusInvDetails['glSystemID'] = $chartOfAccount->chartOfAccountSystemID;
        $addToCusInvDetails['glCode'] = $chartOfAccount->AccountCode;
        $addToCusInvDetails['glCodeDes'] = $chartOfAccount->AccountDescription;
        $addToCusInvDetails['accountType'] = $chartOfAccount->catogaryBLorPL;
        $addToCusInvDetails['serviceLineSystemID'] = $request['segment'];

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

        if($request['project'] != null){
            $addToCusInvDetails['projectID'] = $request['project'];
        }


        $addToCusInvDetails['localCurrency'] = $master->localCurrencyID;
        $addToCusInvDetails['localCurrencyER'] = $master->localCurrencyER;

        $addToCusInvDetails['comRptCurrency'] = $master->companyReportingCurrencyID;
        $addToCusInvDetails['comRptCurrencyER'] = $master->companyReportingER;
        $addToCusInvDetails["comRptAmount"] = 0; // \Helper::roundValue($MyRptAmount);
        $addToCusInvDetails["localAmount"] = 0; // \Helper::roundValue($MyLocalAmount);

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

        /**/


        DB::beginTransaction();

        try {
            $createdData = CustomerInvoiceDirectDetail::create($addToCusInvDetails);
            $details = CustomerInvoiceDirectDetail::select(DB::raw("SUM(invoiceAmount) as bookingAmountTrans"), DB::raw("SUM(localAmount) as bookingAmountLocal"), DB::raw("SUM(comRptAmount) as bookingAmountRpt"))->where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first()->toArray();

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($details);


            DB::commit();
            return ['status' => true,'data'=>$createdData];

        } catch (\Exception $exception) {
            DB::rollback();

            $errorMsg = "Error Occured !.";
            UploadCustomerInvoice::where('id',$request['uploadCustomerInvoice'])->update(['uploadStatus' => 0]);
            LogUploadCustomerInvoice::where('id',$request['logUploadCustomerInvoice'])->update(['is_failed' => 1,'error_line'=>$input['excelRow'], 'log_message' => $errorMsg]);
            return ['status' => false];
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
            return $this->sendError('Customer Invoice Direct Detail not found');
        }

        $master = CustomerInvoiceDirect::select('*')->where('custInvoiceDirectAutoID', $detail->custInvoiceDirectID)->first();

        if (empty($master)) {
            return $this->sendError('Customer Invoice Direct not found');
        }

        $tax = Taxdetail::where('documentSystemCode', $detail->custInvoiceDirectID)
            ->where('companySystemID', $master->companySystemID)
            ->where('documentSystemID', $master->documentSystemiD)
            ->first();

        if (!empty($tax)) {
            // return $this->sendError('Please delete tax details to continue');
        }


        $validateVATCategories = TaxService::validateVatCategoriesInDocumentDetails($master->documentSystemiD, $master->companySystemID, $id, $input, $master->customerID, $master->isPerforma);

        if (!$validateVATCategories['status']) {
            return $this->sendError($validateVATCategories['message'], 500, array('type' => 'vat'));
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
                        return $this->sendError('Payment Period is not updated in the contract. Please update and try again');
                    }
                }
            } else {
                return $this->sendError('Contract not exist.');

            }
        }

        if (isset($input["discountPercentage"]) && $input["discountPercentage"] > 100) {
            return $this->sendError('Discount Percentage cannot be greater than 100 percentage');
        }

        if (isset($input["discountAmountLine"]) && isset($input['salesPrice']) && $input['discountAmountLine'] > $input['salesPrice']) {
            return $this->sendError('Discount amount cannot be greater than sales price');
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
            //$companyCurrency = \Helper::companyCurrency($myCurr);
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

        DB::beginTransaction();

        try {
            $inputArray = json_decode(json_encode($input), true);
            $x=CustomerInvoiceDirectDetail::where('custInvDirDetAutoID', $detail->custInvDirDetAutoID)->update($inputArray);
            $allDetail = CustomerInvoiceDirectDetail::select(DB::raw("IFNULL(SUM(invoiceAmount),0) as bookingAmountTrans"), DB::raw("IFNULL(SUM(localAmount),0) as bookingAmountLocal"), DB::raw("IFNULL(SUM(comRptAmount),0) as bookingAmountRpt"))->where('custInvoiceDirectID', $detail->custInvoiceDirectID)->first()->toArray();

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $detail->custInvoiceDirectID)->update($allDetail);

            if($master->isPerforma != 2) {
                $resVat = $this->updateTotalVAT($master->custInvoiceDirectAutoID);
                if (!$resVat['status']) {
                   return $this->sendError($resVat['message']); 
                } 
            }

            DB::commit();
            return ['status' => true];
            
        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => false, 'message' => $exception]; 
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
            return ['status' => false, 'message' => 'Customer Invoice not found.'];
        }

        $invoiceDetail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();
      
        if (empty($invoiceDetail)) {
            return ['status' => false, 'message' => 'Invoice Details not found.'];
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
            return ['status' => false, 'message' => 'VAT Detail Already exist.'];
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




}