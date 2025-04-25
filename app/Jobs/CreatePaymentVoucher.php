<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\helper\Helper;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\ChartOfAccountsAssigned;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
use App\Models\Employee;
use App\Models\ErpProjectMaster;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Services\DocumentAutoApproveService;
use App\Services\PaymentVoucherServices;
use App\Traits\DocumentSystemMappingTrait;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CreatePaymentVoucher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DocumentSystemMappingTrait;

    public $input;
    public $timeout = 500;
    public $db;
    public $apiExternalKey;
    public $apiExternalUrl;
    public $authorization;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($input, $db, $apiExternalKey, $apiExternalUrl, $authorization)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }

        $this->input = $input;
        $this->db = $db;
        $this->apiExternalKey = $apiExternalKey;
        $this->apiExternalUrl = $apiExternalUrl;
        $this->authorization = $authorization;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/create_payment_voucher.log');

        CommonJobService::db_switch($this->db);

        $fieldErrors = $masterDatasets = $detailsDataSets = $errorDocuments = $successDocuments = [];
        $headerData = $detailData = ['status' => false , 'errors' => []];

        $masterIndex = 0;
        $paymentVouchers = $this->input['payment_vouchers'];

        foreach ($paymentVouchers as $paymentVoucher) {

            $paymentVoucher['company_id'] = $this->input['company_id'];

            $datasetMaster = self::validatePVMasterData($paymentVoucher, $masterIndex);

            if (!$datasetMaster['status']) {
                $fieldErrors = $datasetMaster['fieldErrors'];
                $headerData['errors'] = $datasetMaster['data'];
            }

            $detailIndex = 0;
            $details = $paymentVoucher['details'] ?? null;

            foreach ($details as $detail) {

                $datasetDetails = self::validatePVDetailsData($paymentVoucher,$detail);

                if ($datasetDetails['status']) {
                    $detailsDataSets[$masterIndex][] = $datasetDetails['data'];
                }
                else {
                    $detailData['errors'][] = [
                        'index' => $detailIndex + 1,
                        'error' => $datasetDetails['data']
                    ];
                    unset($detailsDataSets[$masterIndex]);
                }

                $detailIndex++;
            }

            if (empty($headerData['errors']) && empty($detailData['errors']) && empty($fieldErrors)) {
                $masterDatasets[] = array_add($datasetMaster['data'],'details',$detailsDataSets[$masterIndex]);
            }
            else {
                if (empty($headerData['errors'])) {
                    $headerData['status'] = true;
                }

                if (empty($detailData['errors'])) {
                    $detailData['status'] = true;
                }

                $errorDocuments[] = self::createErrorResponseDataArray(
                    $paymentVoucher['narration'] ?? "",
                    $masterIndex,
                    $fieldErrors,
                    $headerData,
                    $detailData
                );

                $fieldErrors = [];
                $headerData = $detailData = ['status' => false , 'errors' => []];
            }

            $masterIndex++;
        }

        if(!empty($masterDatasets)) {
            DB::beginTransaction();

            $headerData = $detailData = ['status' => true , 'errors' => []];

            foreach ($masterDatasets as $masterDataset) {
                $documentStatus = true;
                try {
                    $detailsData = $masterDataset['details'];
                    unset($masterDataset['details']);

                    $masterInsert = PaymentVoucherServices::createPaymentVoucher($masterDataset);

                    if($masterInsert['status']) {
                        $pvMasterAutoId = $masterInsert['data']['PayMasterAutoId'];

                        foreach ($detailsData as $pvDetail) {
                            $pvDetail['directPaymentAutoID'] = $pvMasterAutoId;

                            $detailInsert = PaymentVoucherServices::storeDirectPaymentDetails($pvDetail);

                            if (!$detailInsert['status']) {
                                $documentStatus = false;
                                DB::rollBack();
                                $error = self::createErrorResponseDataArray($masterDataset['BPVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
                                $error['headerData'] = $detailInsert['message'];
                                $errorDocuments[] = $error;
                                break 2;
                            }
                        }

                        if($documentStatus) {
                            $confirmDataSet = $masterInsert['data'];
                            $confirmDataSet['confirmedYN'] = 1;
                            $confirmDataSet['payeeType'] = $masterDataset['payeeType'];
                            $confirmDataSet['paymentMode'] = $masterDataset['paymentMode'];
                            $confirmDataSet['isSupplierBlocked'] = true;
                            $confirmDataSet['isAutoCreateDocument'] = true;

                            $pvUpdateData = PaymentVoucherServices::updatePaymentVoucher($confirmDataSet['PayMasterAutoId'],$confirmDataSet);

                            if($pvUpdateData['status']){

                                $autoApproveParams = DocumentAutoApproveService::getAutoApproveParams($confirmDataSet['documentSystemID'],$confirmDataSet['PayMasterAutoId']);
                                $autoApproveParams['supplierPrimaryCode'] = $confirmDataSet['BPVcode'];
                                $autoApproveParams['createMonthlyDeduction'] = $confirmDataSet['createMonthlyDeduction'];
                                $autoApproveParams['db'] = $this->db;

                                $approveDocument = Helper::approveDocument($autoApproveParams);

                                if ($approveDocument["success"]) {
                                    DB::commit();
                                    $pvID[] = $confirmDataSet['PayMasterAutoId'];
                                    $this->storeToDocumentSystemMapping(4,$pvID,$this->authorization);
                                    $success = self::createSuccessResponseDataArray($masterDataset['BPVNarration'], $masterDataset['initialIndex'], $confirmDataSet['BPVcode']);
                                    $successDocuments[] = $success;
                                }
                                else {
                                    DB::rollBack();
                                    $error = self::createErrorResponseDataArray($masterDataset['BPVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
                                    $error['headerData'] = $approveDocument['message'];
                                    $errorDocuments[] = $error;
                                }
                            }
                            else {
                                DB::rollBack();
                                $error = self::createErrorResponseDataArray($masterDataset['BPVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
                                $error['headerData'] = $pvUpdateData['message'];
                                $errorDocuments[] = $error;
                            }
                        }
                    }
                    else {
                        DB::rollBack();
                        $error = self::createErrorResponseDataArray($masterDataset['BPVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
                        $error['headerData'][] = [
                            'field' => "",
                            'message' => [$masterInsert['message']]
                        ];
                        $errorDocuments[] = $error;
                    }
                }
                catch (\Exception $e) {
                    DB::rollBack();
                    $error = self::createErrorResponseDataArray($masterDataset['BPVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
                    $error['headerData'][] = [
                        'field' => "",
                        'message' => [$e->getMessage()]
                    ];
                    $errorDocuments[] = $error;
                }
            }
        }

        $returnData = [];

        if(!empty($errorDocuments)) {
            $returnData[] = [
                'success' => false,
                'message' => "Validation Failed",
                'code' => 422,
                'errors' => $errorDocuments,
            ];
        }

        if(!empty($successDocuments)) {
            $returnData[] = [
                'success' => true,
                'message' => "Payment voucher created Successfully!",
                'code' => 200,
                'data' => $successDocuments,
            ];
        }

        Log::error($returnData);


        $apiExternalKey = $this->apiExternalKey;
        $apiExternalUrl = $this->apiExternalUrl;
        if($apiExternalKey != null && $apiExternalUrl != null) {
            try {
                $client = new Client();
                $headers = [
                    'content-type' => 'application/json',
                    'Authorization' => 'ERP '.$apiExternalKey
                ];
                $res = $client->request('POST', $apiExternalUrl . '/payment-voucher/webhook', [
                    'headers' => $headers,
                    'json' => [
                        'data' => $returnData
                    ]
                ]);
                $json = $res->getBody();
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }

    public static function validateAPIDate($date): bool {
        $data = ['date' => $date];

        $rules = [
            'date' => [
                'required',
                'regex:/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/',
                function ($attribute, $value, $fail) {
                    $parts = explode('-', $value);
                    if (!checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0])) {
                        $fail("The $attribute is not a valid date.");
                    }
                }
            ],
        ];

        $validator = Validator::make($data, $rules);

        if (!$validator->fails()) {
            return true;
        }
        else {
            return false;
        }
    }

    private static function validatePVMasterData($request, $index): array {
        $errorData = $fieldErrors = [];

        $companyId = $request['company_id'] ?? null;

        if (isset($request['payment_type'])) {
            if (is_int($request['payment_type'])) {
                if ($request['payment_type'] == 1) {
                    $paymentType = 3;

                    if (isset($request['payee_type'])) {
                        if (is_int($request['payee_type'])) {
                            if (in_array($request['payee_type'],[1,2,3])) {

                                switch ($request['payee_type']) {
                                    // Validate Supplier
                                    case 1:
                                        if (isset($request['supplier'])) {
                                            $supplier = SupplierMaster::where('primarySupplierCode', $request['supplier'])
                                                ->orWhere('registrationNumber',$request['supplier'])
                                                ->where('primaryCompanySystemID',$companyId)
                                                ->first();

                                            if ($supplier) {
                                                if ($supplier->approvedYN == 1) {
                                                    $supplierAssign = SupplierAssigned::where('supplierCodeSytem', $supplier->supplierCodeSystem)
                                                        ->where('companySystemID', $companyId)
                                                        ->first();

                                                    if ($supplierAssign) {
                                                        if ($supplierAssign->isActive == 1) {
                                                            if($supplierAssign->isBlocked != 0){
                                                                $errorData[] = [
                                                                    'field' => "supplier",
                                                                    'message' => ["Selected supplier is blocked."]
                                                                ];
                                                            }
                                                        }
                                                        else {
                                                            $errorData[] = [
                                                                'field' => "supplier",
                                                                'message' => ["Selected supplier is not active."]
                                                            ];
                                                        }
                                                    }
                                                    else {
                                                        $errorData[] = [
                                                            'field' => "supplier",
                                                            'message' => ["Selected supplier is not assigned to the company."]
                                                        ];
                                                    }
                                                }
                                                else {
                                                    $errorData[] = [
                                                        'field' => "supplier",
                                                        'message' => ["Selected supplier is not approved."]
                                                    ];
                                                }
                                            }
                                            else {
                                                $errorData[] = [
                                                    'field' => "supplier",
                                                    'message' => ["Selected Payee type (supplier) is not available in the system."]
                                                ];
                                            }
                                        }
                                        else {
                                            $errorData[] = [
                                                'field' => "supplier",
                                                'message' => ["supplier field is required."]
                                            ];
                                        }

                                        break;
                                    // Validate Employee
                                    case 2:
                                        if (isset($request['employee'])) {
                                            $employee = Employee::where('empID', $request['employee']);
                                            if(Helper::checkHrmsIntergrated($companyId)){
                                                $employee = $employee->whereHas('hr_emp', function($q) use ($request) {
                                                    $q->orWhere('EmpSecondaryCode', $request['employee']);
                                                });
                                            }
                                            $employee = $employee->first();

                                            if ($employee) {
                                                if ($employee->empActive == 1) {
                                                    if($employee->discharegedYN != 0){
                                                        $errorData[] = [
                                                            'field' => "employee",
                                                            'message' => ["Selected employee has already been discharged."]
                                                        ];
                                                    }
                                                }
                                                else {
                                                    $errorData[] = [
                                                        'field' => "employee",
                                                        'message' => ["Selected employee is not active."]
                                                    ];
                                                }
                                            }
                                            else {
                                                $errorData[] = [
                                                    'field' => "employee",
                                                    'message' => ["Selected Payee Type (employee) is not available in the system."]
                                                ];
                                            }
                                        }
                                        else {
                                            $errorData[] = [
                                                'field' => "employee",
                                                'message' => ["employee field is required."]
                                            ];
                                        }

                                        break;
                                    // Validate Other
                                    case 3:
                                        if (!isset($request['other'])) {
                                            $errorData[] = [
                                                'field' => "other",
                                                'message' => ["other field is required."]
                                            ];
                                        }

                                        break;
                                }
                            }
                            else {
                                $errorData[] = [
                                    'field' => "payee_type",
                                    'message' => ["Selected payee type not match with system"]
                                ];
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "payee_type",
                                'message' => ["Payee Type must be an integer"]
                            ];
                        }
                    }
                    else {
                        $errorData[] = [
                            'field' => "payee_type",
                            'message' => ["Payee Type field is required"]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "payment_type",
                        'message' => ["payment_type is invalid."]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "payment_type",
                    'message' => ["payment_type must be an integer."]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "payment_type",
                'message' => ["payment_type field is required."]
            ];
        }

        if (isset($request['payment_mode'])) {
            if (is_int($request['payment_mode'])) {
                if (in_array($request['payment_mode'],[1,2,3])) {
                    switch ($request['payment_mode']) {
                        case 1:
                            $paymentMode = 1;
                            break;
                        case 2:
                            $paymentMode = 3;
                            break;
                        case 3:
                            $paymentMode = 4;
                            break;
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "payment_mode",
                        'message' => ["Payment Mode type is invalid"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "payment_mode",
                    'message' => ["Payment Mode must be an integer"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "payment_mode",
                'message' => ["Payment Mode field is required"]
            ];
        }

        if (isset($request['currency'])) {
            $request['currency'] = strtoupper($request['currency']);
            $currency = CurrencyMaster::where('CurrencyCode', $request['currency'])->first();
            if (!$currency) {
                $errorData[] = [
                    'field' => "currency",
                    'message' => ["Selected currency is not available in the system."]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "currency",
                'message' => ["currency field is required"]
            ];
        }

        if (isset($request['bank'])) {
            $bank = BankAssign::where('companySystemID', $request['company_id'])
                ->where('bankShortCode',$request['bank'])
                ->first();

            if ($bank) {
                if ($bank->isActive == 1) {
                    if ($bank->isAssigned == -1) {

                        if (isset($request['account'])) {
                            $bankAccount = BankAccount::where('companySystemID', $companyId)
                                ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                                ->where('AccountNo', $request['account'])
                                ->first();

                            if ($bankAccount) {
                                if ($bankAccount->isAccountActive == 1) {
                                    if ($bankAccount->approvedYN != 1) {
                                        $errorData[] = [
                                            'field' => "account",
                                            'message' => ["Selected bank account is not approved in the system."]
                                        ];
                                    }
                                }
                                else {
                                    $errorData[] = [
                                        'field' => "account",
                                        'message' => ["Selected bank account is not active in the system."]
                                    ];
                                }
                            }
                            else {
                                $errorData[] = [
                                    'field' => "account",
                                    'message' => ["Selected bank account is not available in the system."]
                                ];
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "account",
                                'message' => ["account field is required"]
                            ];
                        }
                    }
                    else {
                        $errorData[] = [
                            'field' => "bank",
                            'message' => ["Selected bank is not assigned/active to the company"]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "bank",
                        'message' => ["Selected bank is not active."]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "bank",
                    'message' => ["Selected bank is not available in the system."]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "bank",
                'message' => ["bank field is required"]
            ];
        }

        if (isset($request['narration'])) {
            $paymentVoucher = PaySupplierInvoiceMaster::where('BPVNarration', $request['narration'])->where('companySystemID', $companyId)->exists();
            if ($paymentVoucher) {
                $fieldErrors = [
                    'field' => "narration",
                    'message' => ["narration already exists in the system"]
                ];
                $errorData[] = $fieldErrors;
            }
        }
        else {
            $fieldErrors = [
                'field' => "narration",
                'message' => ["narration field is required"]
            ];
            $errorData[] = $fieldErrors;
        }

        if (isset($request['pay_invoice_date'])) {
            $data = self::validateAPIDate($request['pay_invoice_date']);
            if ($data) {
                $payInvoiceDate = Carbon::parse($request['pay_invoice_date']);

                if ($payInvoiceDate->lessThanOrEqualTo(Carbon::today())) {
                    $financeYear = CompanyFinanceYear::where('companySystemID',$companyId)
                        ->where('isDeleted',0)
                        ->where('bigginingDate','<=',$payInvoiceDate)
                        ->where('endingDate','>=',$payInvoiceDate)
                        ->where('isActive', -1)
                        ->first();

                    if ($financeYear) {
                        $financePeriod = CompanyFinancePeriod::where('companySystemID',$companyId)
                            ->where('companyFinanceYearID',$financeYear->companyFinanceYearID)
                            ->where('isActive', -1)
                            ->whereMonth('dateFrom',$payInvoiceDate->month)
                            ->whereMonth('dateTo',$payInvoiceDate->month)
                            ->where(function ($query) {
                                $query->where('departmentSystemID',1)
                                    ->orWhere('departmentSystemID',5);
                            })
                            ->first();
                        if (!$financePeriod) {
                            $errorData[] = [
                                'field' => "pay_invoice_date",
                                'message' => ["Financial period related to the selected pay invoice date is not active for the specified department."]
                            ];
                        }
                    }
                    else{
                        $errorData[] = [
                            'field' => "pay_invoice_date",
                            'message' => ["Financial year related to the selected pay invoice date is either not active or not created."]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "pay_invoice_date",
                        'message' => ["Payment voucher date must be today or before"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "pay_invoice_date",
                    'message' => ["pay_invoice_date format is invalid"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "pay_invoice_date",
                'message' => ["pay_invoice_date field is required"]
            ];
        }

        if (isset($request['reverse_charge_mechanism'])) {
            if (is_int($request['reverse_charge_mechanism'])) {
                if (in_array($request['reverse_charge_mechanism'], [1,2])) {
                    if ($request['reverse_charge_mechanism'] == 1) {
                        $reverseChargeMechanism = 1;
                    }
                    else {
                        $reverseChargeMechanism = 0;
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "reverse_charge_mechanism",
                        'message' => ["Invalid RCM Type selected. Please choose the correct type."]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "reverse_charge_mechanism",
                    'message' => ["reverse_charge_mechanism must be an integer."]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "reverse_charge_mechanism",
                'message' => ["reverse_charge_mechanism field is required"]
            ];
        }

        $details = $request['details'] ?? null;

        if (isset($details)) {
            if (is_array($details)) {
                $detailsCollection = collect($details);

                if($detailsCollection->count() < 1) {
                    $errorData[] = [
                        'field' => "details",
                        'message' => ["details cannot be less than one"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "details",
                    'message' => ["details format invalid"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "details",
                'message' => ["details field is required"]
            ];
        }

        if (empty($errorData) && empty($fieldErrors)) {
            $returnDataset = [
                'status' => true,
                'data' => [
                    'invoiceType' => $paymentType,
                    'paymentMode' => $paymentMode,
                    'payeeType' => $request['payee_type'],
                    'supplierTransCurrencyID' => $currency->currencyID,
                    'BPVbank' => $bank->bankmasterAutoID,
                    'BPVAccount' => $bankAccount->bankAccountAutoID,
                    'BPVNarration' => $request['narration'],
                    'BPVdate' => $payInvoiceDate->toDateString(),
                    'companyFinanceYearID' => $financeYear->companyFinanceYearID,
                    'companyFinancePeriodID' => $financePeriod->companyFinancePeriodID,
                    'rcmActivated' => $reverseChargeMechanism,
                    'BPVchequeDate' => Carbon::today()->startOfDay()->format('Y-m-d'),
                    'companySystemID' => $companyId,
                    'documentSystemID' => 4,
                    'isAutoCreateDocument' => true,
                    'initialIndex' => $index
                ]
            ];

            switch ($request['payee_type']) {
                case 1:
                    $returnDataset['data']['BPVsupplierID'] = $supplier->supplierCodeSystem;
                    break;
                case 2:
                    $returnDataset['data']['directPaymentPayeeEmpID'] = $employee->employeeSystemID;
                    break;
                case 3:
                    $returnDataset['data']['directPaymentPayee'] = $request['other'];
                    break;
            }
        }
        else {
            $returnDataset = [
                'status' => false,
                'data' => $errorData,
                'fieldErrors' => $fieldErrors
            ];
        }

        return $returnDataset;
    }

    public static function validatePVDetailsData($masterData, $request): array {
        $errorData = [];

        $companyId = $masterData['company_id'] ?? null;

        // Validate GL Code
        if (isset($request['gl_account'])) {
            $chartOfAccount = ChartOfAccountsAssigned::with('chartofaccount')
                ->where('companySystemID', $companyId)
                ->where('AccountCode',$request['gl_account'])
                ->first();

            if ($chartOfAccount){
                if (($chartOfAccount->isActive == 1) && ($chartOfAccount->isAssigned == -1)) {
                    if ($chartOfAccount->isBank == 0) {
                        $chartOfAccountMaster = $chartOfAccount->chartofaccount;
                        if ($chartOfAccountMaster->isApproved == 1) {
                            if ($chartOfAccount->controllAccountYN == 0) {
                                if ($chartOfAccount->controlAccountsSystemID == 1) {
                                    $errorData[] = [
                                        'field' => "gl_account",
                                        'message' => ["Selected GL code is of type 'Income' and is not allowed for this transaction."]
                                    ];
                                }
                            }
                            else {
                                $errorData[] = [
                                    'field' => "gl_account",
                                    'message' => ["Selected GL code is a control account and cannot be used."]
                                ];
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "gl_account",
                                'message' => ["Selected GL code is not approved."]
                            ];
                        }
                    }
                    else {
                        $errorData[] = [
                            'field' => "gl_account",
                            'message' => ["Selected GL code is bank gl code."]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "gl_account",
                        'message' => ["Selected GL code is either not active or not assigned to the company."]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "gl_account",
                    'message' => ["Selected GL code does not match any record in the system."]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "gl_account",
                'message' => ["gl_account field is required"]
            ];
        }

        // Validate Project
        if (isset($request['project'])) {
            $project = ErpProjectMaster::where('projectCode', $request['project'])->first();

            if (!$project) {
                $errorData[] = [
                    'field' => "project",
                    'message' => ["The selected project code does not match with the system."]
                ];
            }
        }
        else {
            $project = null;
        }

        // Validate Segment
        if (isset($request['segment'])) {
            $segment = SegmentMaster::where('ServiceLineCode',$request['segment'])
                ->where('companySystemID', $companyId)
                ->first();

            if ($segment) {
                if ($segment->isActive == 1) {
                    if ($segment->isDeleted != 0) {
                        $errorData[] = [
                            'field' => "segment",
                            'message' => ["Selected segment is deleted"]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "segment",
                        'message' => ["Selected segment not active"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "segment",
                    'message' => ["Selected segment code does not match with system"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "segment",
                'message' => ["segment field is required"]
            ];
        }

        // Validate Amount
        $amountValidation = false;
        if (isset($request['amount'])) {
            if (gettype($request['amount']) != 'string') {
                if ($request['amount'] > 0) {
                    $amountValidation = true;
                }
                else {
                    $errorData[] = [
                        'field' => "amount",
                        'message' => ["The amount should be a positive value."]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "amount",
                    'message' => ["amount must be a numeric"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "amount",
                'message' => ["amount field is required"]
            ];
        }

        // Validate VAT Percentage
        $vatPercentageValidation = false;
        if (isset($request['vat_percentage'])) {
            if (gettype($request['vat_percentage']) != 'string') {
                if ($request['vat_percentage'] >= 0) {
                    $vatPercentageValidation = true;
                }
                else {
                    $errorData[] = [
                        'field' => "vat_percentage",
                        'message' => ["vat_percentage must be at least 0"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "vat_percentage",
                    'message' => ["vat_percentage must be a numeric"]
                ];
            }
        }

        // Validate VAT Amount
        $vatAmountValidation = false;
        if (isset($request['vat_amount'])) {
            if (gettype($request['vat_amount']) != 'string') {
                if ($request['vat_amount'] >= 0) {
                    $vatAmountValidation = true;
                }
                else {
                    $errorData[] = [
                        'field' => "vat_amount",
                        'message' => ["vat_amount must be at least 0"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "vat_amount",
                    'message' => ["vat_amount must be a numeric"]
                ];
            }
        }

        if ($amountValidation && ($vatPercentageValidation && $vatAmountValidation)) {
            $vatAmount = ($request['amount'] * $request['vat_percentage']) / 100;
            if ($vatAmount != $request['vat_amount']) {
                $errorData[] = [
                    'field' => "vat_amount",
                    'message' => ["VAT% and VAT Amount is not matching"]
                ];
            }
        }

        if ($amountValidation && (!$vatPercentageValidation && $vatAmountValidation)) {
            $request['vat_percentage'] = ($request['vat_amount'] / $request['amount']) * 100;
        }

        if ($amountValidation && ($vatPercentageValidation && !$vatAmountValidation)) {
            $request['vat_amount'] = ($request['amount'] * $request['vat_percentage']) / 100;
        }

        if (empty($errorData)) {
            $returnData = [
                "status" => true,
                "data" => [
                    'chartOfAccountSystemID' => $chartOfAccount->chartOfAccountSystemID,
                    'serviceLineSystemID' => $segment->serviceLineSystemID,
                    'comments' => $request['comments'] ?? null,
                    'DPAmount' => $request['amount'],
                    'VATPercentage' => $request['vat_percentage'] ?? 0,
                    'vatAmount' => $request['vat_amount'] ?? 0,
                    'netAmount' => $request['amount'],
                    'detail_project_id' => $project != null ? $project->id : null,
                    'companySystemID' => $companyId,
                    'isAutoCreateDocument' => true
                ]
            ];
        }
        else{
            $returnData = [
                "status" => false,
                "data" => $errorData
            ];
        }

        return $returnData;
    }

    public static function createErrorResponseDataArray($narration,$masterIndex,$fieldErrors, $headerData, $detailData): array {
        return [
            'identifier' => [
                'unique-key' => $narration,
                'index' => $masterIndex + 1
            ],
            'fieldErrors' => $fieldErrors,
            'headerData' => [$headerData],
            'detailData' => [$detailData]
        ];
    }

    public static function createSuccessResponseDataArray($narration,$masterIndex,$code): array {
        return [
            'uniqueKey' => $narration,
            'index' => $masterIndex + 1,
            'paymentVoucherCode' => $code,
        ];
    }
}
