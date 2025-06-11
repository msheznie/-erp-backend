<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\helper\Helper;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ChequeRegister;
use App\Models\ChequeRegisterDetail;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
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
        if (env('QUEUE_DRIVER_CHANGE','database') == 'database') {
            if (env('IS_MULTI_TENANCY',false)) {
                self::onConnection('database_main');
            }
            else {
                self::onConnection('database');
            }
        }
        else {
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

        $fieldErrors = $masterDatasets = $detailsDataSets = $pdcChequeDetailsDataSets = $errorDocuments = $successDocuments = [];
        $headerData = $detailData = $pdcChequeData = ['status' => false , 'errors' => []];

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

            if ($details != null) {
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
            }

            $pdcChequeDetailIndex = 0;
            $pdcChequeDetails = $paymentVoucher['pdc_cheque_details'] ?? null;

            if (!is_null($pdcChequeDetails)) {
                $validationData = $datasetMaster['validationData'] ?? null;
                foreach ($pdcChequeDetails as $pdcChequeDetail) {

                    $datasetPDCChequeDetails = self::validatePDCChequeDetailsData($paymentVoucher,$pdcChequeDetail,$validationData);

                    if ($datasetPDCChequeDetails['status']) {
                        $pdcChequeDetailsDataSets[$masterIndex][] = $datasetPDCChequeDetails['data'];
                    }
                    else {
                        $pdcChequeData['errors'][] = [
                            'index' => $pdcChequeDetailIndex + 1,
                            'error' => $datasetPDCChequeDetails['data']
                        ];
                        unset($pdcChequeDetailsDataSets[$masterIndex]);
                    }

                    $pdcChequeDetailIndex++;
                }
            }

            if (empty($headerData['errors']) && empty($detailData['errors']) && empty($pdcChequeData['errors']) && empty($fieldErrors)) {
                $finalArray = array_add($datasetMaster['data'],'details',$detailsDataSets[$masterIndex]);
                if (!is_null($pdcChequeDetails)) {
                    $finalArray = array_add($finalArray,'pdcChequeDetails',$pdcChequeDetailsDataSets[$masterIndex]);
                }
                $masterDatasets[] = $finalArray;
            }
            else {
                if (empty($headerData['errors'])) {
                    $headerData['status'] = true;
                }

                if (empty($detailData['errors'])) {
                    $detailData['status'] = true;
                }

                if (empty($pdcChequeData['errors'])) {
                    $pdcChequeData['status'] = true;
                }

                $errorDocuments[] = self::createErrorResponseDataArray(
                    $paymentVoucher['narration'] ?? "",
                    $masterIndex,
                    $fieldErrors,
                    $headerData,
                    $detailData,
                     !is_null($pdcChequeDetails),
                    $pdcChequeData
                );

                $fieldErrors = [];
                $headerData = $detailData = $pdcChequeData = ['status' => false , 'errors' => []];
            }

            $masterIndex++;
        }

        if(!empty($masterDatasets)) {
            DB::beginTransaction();

            $headerData = $detailData = $pdcDetailData = ['status' => true , 'errors' => []];

            foreach ($masterDatasets as $masterDataset) {
                $documentDetailsStatus = true;
                $documentPDCChequeDetailsStatus = true;

                $isPDCAvailable = isset($masterDataset['pdcChequeYN']) && $masterDataset['pdcChequeYN'];

                try {
                    $detailsData = $masterDataset['details'];
                    unset($masterDataset['details']);
                    $pdcDetailsData = [];
                    if (isset($masterDataset['pdcChequeDetails'])) {
                        $pdcDetailsData = $masterDataset['pdcChequeDetails'];
                        unset($masterDataset['pdcChequeDetails']);
                    }

                    $masterInsert = PaymentVoucherServices::createPaymentVoucher($masterDataset);

                    if($masterInsert['status']) {
                        $pvMasterAutoId = $masterInsert['data']['PayMasterAutoId'];

                        foreach ($detailsData as $pvDetail) {
                            $pvDetail['directPaymentAutoID'] = $pvMasterAutoId;

                            $detailInsert = PaymentVoucherServices::storeDirectPaymentDetails($pvDetail);

                            if (!$detailInsert['status']) {
                                $documentDetailsStatus = false;
                                DB::rollBack();
                                $error = self::createErrorResponseDataArray(
                                    $masterDataset['BPVNarration'],
                                    $masterDataset['initialIndex'],
                                    [],
                                    $headerData,
                                    $detailData,
                                    $isPDCAvailable,
                                    $pdcDetailData
                                );
                                $error['headerData'] = $detailInsert['message'];
                                $errorDocuments[] = $error;
                                break 2;
                            }
                        }

                        if ($documentDetailsStatus && ($masterInsert['data']['payment_mode'] == 2) && ($masterInsert['data']['pdcChequeYN'] == 1)) {

                            $tempData = [
                                'PayMasterAutoId' => $pvMasterAutoId,
                                'noOfCheques' => $masterDataset['noOfCheques'],
                                'totalAmount' => $masterDataset['totalAmount'],
                                'documentSystemID' => $masterDataset['documentSystemID'],
                                'isAutoCreateDocument' => true
                            ];
                            $pdcChequeGenerateStatus = PaymentVoucherServices::generatePdcForPv($tempData);

                            if ($pdcChequeGenerateStatus['status']) {
                                $pdcChequeIndex = 0;
                                foreach ($pdcDetailsData as $pdcDetail) {
                                    $id = $pdcChequeGenerateStatus['data'][$pdcChequeIndex]['id'];
                                    $pdcDetail['documentmasterAutoID'] = $pvMasterAutoId;

                                    $pdcInsert = PaymentVoucherServices::updatePDCCheque($id,$pdcDetail);

                                    if (!$pdcInsert['status']) {
                                        $documentPDCChequeDetailsStatus = false;
                                        DB::rollBack();
                                        $error = self::createErrorResponseDataArray(
                                            $masterDataset['BPVNarration'],
                                            $masterDataset['initialIndex'],
                                            [],
                                            $headerData,
                                            $detailData,
                                            $isPDCAvailable,
                                            $pdcDetailData
                                        );
                                        $error['headerData'] = $pdcInsert['message'];
                                        $errorDocuments[] = $error;
                                        break 2;
                                    }

                                    $pdcChequeIndex++;
                                }
                            }
                            else {
                                $documentPDCChequeDetailsStatus = false;
                                DB::rollBack();
                                $error = self::createErrorResponseDataArray(
                                    $masterDataset['BPVNarration'],
                                    $masterDataset['initialIndex'],
                                    [],
                                    $headerData,
                                    $detailData,
                                    $isPDCAvailable,
                                    $pdcDetailData
                                );
                                $error['headerData'] = $pdcChequeGenerateStatus['message'];
                                $errorDocuments[] = $error;
                            }
                        }

                        if($documentDetailsStatus && $documentPDCChequeDetailsStatus) {
                            $confirmDataSet = PaySupplierInvoiceMaster::find($pvMasterAutoId)->toArray();
                            $confirmDataSet['confirmedYN'] = 1;
                            $confirmDataSet['payeeType'] = $masterDataset['payeeType'];
                            $confirmDataSet['paymentMode'] = $masterDataset['paymentMode'];
                            $confirmDataSet['isSupplierBlocked'] = true;
                            $confirmDataSet['isAutoCreateDocument'] = true;
                            if ($masterInsert['data']['payment_mode'] == 2) {
                                $confirmDataSet['BPVchequeNoDropdown'] = $masterDataset['BPVchequeNoDropdown'];
                            }

                            $pvUpdateData = PaymentVoucherServices::updatePaymentVoucher($confirmDataSet['PayMasterAutoId'],$confirmDataSet);

                            if($pvUpdateData['status']){

                                $autoApproveParams = DocumentAutoApproveService::getAutoApproveParams($confirmDataSet['documentSystemID'],$confirmDataSet['PayMasterAutoId']);
                                $autoApproveParams['supplierPrimaryCode'] = $confirmDataSet['BPVcode'];
                                $autoApproveParams['createMonthlyDeduction'] = $confirmDataSet['createMonthlyDeduction'];
                                $autoApproveParams['db'] = $this->db;

                                $approveDocument = Helper::approveDocument($autoApproveParams);

                                if ($approveDocument["success"]) {
                                    DB::commit();
                                    $pvID = $confirmDataSet['PayMasterAutoId'];
                                    $this->storeToDocumentSystemMapping(4,$pvID,$this->authorization);
                                    $success = self::createSuccessResponseDataArray($masterDataset['BPVNarration'], $masterDataset['initialIndex'], $confirmDataSet['BPVcode']);
                                    $successDocuments[] = $success;
                                }
                                else {
                                    DB::rollBack();
                                    $error = self::createErrorResponseDataArray(
                                        $masterDataset['BPVNarration'],
                                        $masterDataset['initialIndex'],
                                        [],
                                        $headerData,
                                        $detailData,
                                        $isPDCAvailable,
                                        $pdcDetailData
                                    );
                                    $error['headerData'] = $approveDocument['message'];
                                    $errorDocuments[] = $error;
                                }
                            }
                            else {
                                DB::rollBack();
                                $error = self::createErrorResponseDataArray(
                                    $masterDataset['BPVNarration'],
                                    $masterDataset['initialIndex'],
                                    [],
                                    $headerData,
                                    $detailData,
                                    $isPDCAvailable,
                                    $pdcDetailData
                                );
                                $error['headerData'] = $pvUpdateData['message'];
                                $errorDocuments[] = $error;
                            }
                        }
                    }
                    else {
                        DB::rollBack();
                        $error = self::createErrorResponseDataArray(
                            $masterDataset['BPVNarration'],
                            $masterDataset['initialIndex'],
                            [],
                            $headerData,
                            $detailData,
                            $isPDCAvailable,
                            $pdcDetailData
                        );
                        $error['headerData'][] = [
                            'field' => "",
                            'message' => [$masterInsert['message']]
                        ];
                        $errorDocuments[] = $error;
                    }
                }
                catch (\Exception $e) {
                    DB::rollBack();
                    $error = self::createErrorResponseDataArray(
                        $masterDataset['BPVNarration'],
                        $masterDataset['initialIndex'],
                        [],
                        $headerData,
                        $detailData,
                        $isPDCAvailable,
                        $pdcDetailData
                    );
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
                        $financePeriodGL = CompanyFinancePeriod::where('companySystemID',$companyId)
                            ->where('companyFinanceYearID',$financeYear->companyFinanceYearID)
                            ->where('isActive', -1)
                            ->whereMonth('dateFrom',$payInvoiceDate->month)
                            ->whereMonth('dateTo',$payInvoiceDate->month)
                            ->where('departmentSystemID', 5)
                            ->first();

                        if ($financePeriodGL) {
                            $financePeriodAP = CompanyFinancePeriod::where('companySystemID',$companyId)
                                ->where('companyFinanceYearID',$financeYear->companyFinanceYearID)
                                ->where('isActive', -1)
                                ->whereMonth('dateFrom',$payInvoiceDate->month)
                                ->whereMonth('dateTo',$payInvoiceDate->month)
                                ->where('departmentSystemID', 1)
                                ->first();

                            if (!$financePeriodAP) {
                                $errorData[] = [
                                    'field' => "pay_invoice_date",
                                    'message' => ["Financial period related to the selected pay invoice date is not active for the specified department."]
                                ];
                            }
                        }
                        else {
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

        if (isset($request['payee_type'])) {
            if (is_int($request['payee_type'])) {
                if (in_array($request['payee_type'],[1,2,3])) {

                    switch ($request['payee_type']) {
                        // Validate Supplier
                        case 1:
                            if (isset($request['supplier'])) {
                                $supplier = SupplierMaster::where('primarySupplierCode', $request['supplier'])
                                    ->orWhere('registrationNumber',$request['supplier'])
                                    ->first();

                                if ($supplier) {
                                    if ($supplier->approvedYN == 1) {
                                        $supplierAssign = SupplierAssigned::where('supplierCodeSytem', $supplier->supplierCodeSystem)
                                            ->where('companySystemID', $companyId)
                                            ->first();

                                        if ($supplierAssign && $supplierAssign->isAssigned == -1) {
                                            if ($supplierAssign->isActive == 1) {
                                                $invoiceDate = $request['pay_invoice_date'] ?? null;
                                                $validatorResult = Helper::checkBlockSuppliers($invoiceDate, $supplier->supplierCodeSystem);
                                                if (!$validatorResult['success']) {
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
                                $employee = Employee::where('empID', $request['employee'])
                                    ->when(Helper::checkHrmsIntergrated($companyId), function ($query) use ($request) {
                                        $query->orWhereHas('hr_emp', function ($q) use ($request) {
                                            $q->where('EmpSecondaryCode', $request['employee']);
                                        });
                                    })->first();

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

        $paymentMode = null;
        if (isset($request['payment_mode'])) {
            if (is_int($request['payment_mode'])) {
                if (in_array($request['payment_mode'],[1,2,3,4])) {
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
                        case 4:
                            $paymentMode = 2;
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

        $isAvailableChequeDate = false;
        $isAvailableChequeData = false;
        $isPdcCheque = false;
        $numberOfCheque = null;

        if (isset($request['currency'])) {
            $request['currency'] = strtoupper($request['currency']);
            $currency = CurrencyMaster::where('CurrencyCode', $request['currency'])->first();
            if ($currency) {
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
                                            if ($bankAccount->approvedYN == 1) {
                                                // Process Check Payment Data
                                                if (!is_null($paymentMode) && $paymentMode == 2) {

                                                    if (isset($request['is_pdc_cheque'])) {
                                                        if (is_int($request['is_pdc_cheque'])) {
                                                            if (in_array($request['is_pdc_cheque'], [1,2])) {
                                                                if ($request['is_pdc_cheque'] == 1) {
                                                                    $isPdcCheque = true;
                                                                    if (isset($request['no_of_cheques'])) {
                                                                        if (is_int($request['no_of_cheques'])) {
                                                                            $numberOfCheque = $request['no_of_cheques'];
                                                                        }
                                                                        else {
                                                                            $errorData[] = [
                                                                                'field' => "no_of_cheques",
                                                                                'message' => ["no_of_cheques must be an integer"]
                                                                            ];
                                                                        }
                                                                    }
                                                                    else {
                                                                        $errorData[] = [
                                                                            'field' => "no_of_cheques",
                                                                            'message' => ["no_of_cheques field is required"]
                                                                        ];
                                                                    }
                                                                }
                                                                else {

                                                                    $companyCurrency = Helper::companyCurrency($companyId);
                                                                    $localCurrency = null;
                                                                    if ($companyCurrency) {
                                                                        $localCurrency = $companyCurrency->localcurrency->currencyID;
                                                                    }

                                                                    if (($currency->currencyID == $localCurrency) && ($localCurrency == $bankAccount->accountCurrencyID)) {
                                                                        if (isset($request['cheque_number'])) {
                                                                            $isExistPolicyGCNFCR = CompanyPolicyMaster::where('companySystemID', $companyId)
                                                                                ->where('companyPolicyCategoryID', 35)
                                                                                ->where('isYesNO', 1)
                                                                                ->exists();

                                                                            $isAvailableChequeData = true;
                                                                            if ($isExistPolicyGCNFCR) {
                                                                                $chequeRegister = ChequeRegister::where('company_id', $companyId)
                                                                                    ->where('bank_id',$bank->bankmasterAutoID)
                                                                                    ->where('bank_account_id',$bankAccount->bankAccountAutoID)
                                                                                    ->get();

                                                                                    if (count($chequeRegister) > 0) {
                                                                                        $activeChequeRegister = $chequeRegister->where('isActive',1)->first();
                                                                                        if (!is_null($activeChequeRegister)) {
                                                                                            $chequeRegisterDetails = ChequeRegisterDetail::where('company_id',$companyId)->where('cheque_register_master_id', $activeChequeRegister->id)->where('status', 0)->get();

                                                                                        if (count($chequeRegisterDetails) > 0) {
                                                                                            $selectedChequeData = $chequeRegisterDetails->where('cheque_no', $request['cheque_number'])->first();
                                                                                            if (!is_null($selectedChequeData)) {
                                                                                                $chequeID = $selectedChequeData->id;
                                                                                            }
                                                                                            else {
                                                                                                $errorData[] = [
                                                                                                    'field' => "cheque_number",
                                                                                                    'message' => ["Entered cheque number does not match the available cheques in the register."]
                                                                                                ];
                                                                                            }
                                                                                        }
                                                                                        else {
                                                                                            $errorData[] = [
                                                                                                'field' => "cheque_number",
                                                                                                'message' => ["No unused cheques available in the cheque register for the selected bank account."]
                                                                                            ];
                                                                                        }
                                                                                    }
                                                                                    else {
                                                                                        $errorData[] = [
                                                                                            'field' => "cheque_number",
                                                                                            'message' => ["The cheque register for the selected bank account is inactive."]
                                                                                        ];
                                                                                    }
                                                                                }
                                                                                else {
                                                                                    $errorData[] = [
                                                                                        'field' => "cheque_number",
                                                                                        'message' => ["Cheque register not found for the selected bank account."]
                                                                                    ];
                                                                                }
                                                                            }
                                                                            else {
                                                                                $chequeID = $request['cheque_number'];
                                                                            }
                                                                        }
                                                                        else {
                                                                            $errorData[] = [
                                                                                'field' => "cheque_number",
                                                                                'message' => ["cheque_number field is required"]
                                                                            ];
                                                                        }
                                                                    }
                                                                    else {
                                                                        $errorData[] = [
                                                                            'field' => "payment_mode",
                                                                            'message' => ["Cheque payment mode cannot be selected as the transaction currency and bank account currency is different."]
                                                                        ];
                                                                    }

                                                                    if (isset($request['cheque_date'])) {
                                                                        $data = self::validateAPIDate($request['cheque_date']);
                                                                        if ($data) {
                                                                            $chequeDate = Carbon::parse($request['cheque_date']);
                                                                            $isAvailableChequeDate = true;
                                                                        }
                                                                        else {
                                                                            $errorData[] = [
                                                                                'field' => "cheque_date",
                                                                                'message' => ["cheque_date format is invalid"]
                                                                            ];
                                                                        }
                                                                    }
                                                                    else {
                                                                        $errorData[] = [
                                                                            'field' => "cheque_date",
                                                                            'message' => ["cheque_date field is required"]
                                                                        ];
                                                                    }

                                                                }
                                                            }
                                                            else {
                                                                $errorData[] = [
                                                                    'field' => "is_pdc_cheque",
                                                                    'message' => ["Invalid is_pdc_cheque selected. Please choose the correct type."]
                                                                ];
                                                            }
                                                        }
                                                        else {
                                                            $errorData[] = [
                                                                'field' => "is_pdc_cheque",
                                                                'message' => ["is_pdc_cheque must be an integer"]
                                                            ];
                                                        }
                                                    }
                                                    else {
                                                        $errorData[] = [
                                                            'field' => "is_pdc_cheque",
                                                            'message' => ["is_pdc_cheque field is required"]
                                                        ];
                                                    }
                                                }
                                            }
                                            else {
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
            }
            else {
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

        $totalAmount = 0;
        if (isset($details)) {
            if (is_array($details)) {
                $detailsCollection = collect($details);

                if($detailsCollection->count() > 0) {
                    $totalAmount = $detailsCollection->sum('amount');
                }
                else {
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

        if ($paymentMode == 2 && $isPdcCheque && (!is_null($numberOfCheque) && $numberOfCheque > 0)) {
            $pdcChequeDetails = $request['pdc_cheque_details'] ?? null;

            if (isset($pdcChequeDetails)) {
                if (is_array($pdcChequeDetails)) {
                    $pdcChequeDetailsCount = collect($pdcChequeDetails)->count();

                    if($pdcChequeDetailsCount >= 1) {
                        if($pdcChequeDetailsCount != $numberOfCheque) {
                            $errorData[] = [
                                'field' => "pdc_cheque_details",
                                'message' => ["pdc_cheque_details count and no_of_cheques must be same"]
                            ];
                        }
                    }
                    else {
                        $errorData[] = [
                            'field' => "pdc_cheque_details",
                            'message' => ["pdc_cheque_details cannot be less than one"]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "pdc_cheque_details",
                        'message' => ["pdc_cheque_details format invalid"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "pdc_cheque_details",
                    'message' => ["pdc_cheque_details field is required"]
                ];
            }
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
                    'companyFinancePeriodID' => $financePeriodAP->companyFinancePeriodID,
                    'rcmActivated' => $reverseChargeMechanism,
                    'companySystemID' => $companyId,
                    'documentSystemID' => 4,
                    'isAutoCreateDocument' => true,
                    'initialIndex' => $index
                ]
            ];

            if ($isAvailableChequeDate) {
                $returnDataset['data']['BPVchequeDate'] = $chequeDate->format('Y-m-d');
            }
            else {
                $returnDataset['data']['BPVchequeDate'] = Carbon::today()->startOfDay()->format('Y-m-d');
            }

            if (!is_null($paymentMode) && $paymentMode == 2) {
                $returnDataset['data']['pdcChequeYN'] = $isPdcCheque;
                $returnDataset['data']['BPVchequeNoDropdown'] = $isAvailableChequeData ? $chequeID : [];
                if ($isPdcCheque) {
                    $returnDataset['data']['noOfCheques'] = $numberOfCheque;
                    $returnDataset['data']['totalAmount'] = $totalAmount;

                    $returnDataset['validationData'] = [
                        'currency' => $currency,
                        'bank' => $bank,
                        'account' => $bankAccount
                    ];
                }
            }

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

            if (!is_null($paymentMode) && $paymentMode == 2 && $isPdcCheque) {
                $returnDataset['validationData'] = [
                    'currency' => $currency ?? null,
                    'bank' => $bank ?? null,
                    'account' => $bankAccount ?? null
                ];
            }
        }

        return $returnDataset;
    }

    public static function validatePVDetailsData($masterData, $request): array {
        $errorData = [];

        $companyId = $masterData['company_id'] ?? null;

        // Validate GL Code
        if (isset($request['gl_account'])) {
            $chartOfAccount = ChartOfAccount::where('primaryCompanySystemID', $companyId)
                ->where('AccountCode',$request['gl_account'])
                ->first();

            if ($chartOfAccount){
                if ($chartOfAccount->isApproved == 1) {
                    if ($chartOfAccount->isActive == 1) {
                        $chartOfAccountAssigned = ChartOfAccountsAssigned::where('companySystemID', $companyId)
                            ->where('chartOfAccountSystemID', $chartOfAccount->chartOfAccountSystemID)->first();

                        if ($chartOfAccountAssigned && $chartOfAccountAssigned->isAssigned == -1) {
                            if ($chartOfAccountAssigned->isActive == 1) {
                                if ($chartOfAccountAssigned->isBank == 0) {
                                    if ($chartOfAccountAssigned->controllAccountYN == 0) {
                                        if ($chartOfAccountAssigned->controlAccountsSystemID == 1) {
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
                                        'message' => ["Selected GL code is bank gl code."]
                                    ];
                                }
                            }
                            else {
                                $errorData[] = [
                                    'field' => "gl_account",
                                    'message' => ["Selected GL code is not active"]
                                ];
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "gl_account",
                                'message' => ["Selected GL code is not assigned to the company."]
                            ];
                        }
                    }
                    else {
                        $errorData[] = [
                            'field' => "gl_account",
                            'message' => ["Selected GL code is not active"]
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

    public static function validatePDCChequeDetailsData($masterData, $pdcChequeDetails, $validationData): array {
        $errorData = [];

        $companyId = $masterData['company_id'] ?? null;

        if (isset($pdcChequeDetails['cheque_number'])) {
            $companyCurrency = Helper::companyCurrency($companyId);
            $localCurrency = null;
            if ($companyCurrency) {
                $localCurrency = $companyCurrency->localcurrency->currencyID;
            }

            $chequeNumber = null;
            $chequeNumberID = null;
            if (!is_null($validationData)) {
                if ((isset($validationData['currency']) && $validationData['currency']['currencyID'] == $localCurrency) && (isset($validationData['account']) && $validationData['account']['accountCurrencyID'] == $localCurrency)) {
                    $isExistPolicyGCNFCR = CompanyPolicyMaster::where('companySystemID', $companyId)
                        ->where('companyPolicyCategoryID', 35)
                        ->where('isYesNO', 1)
                        ->exists();

                    if ($isExistPolicyGCNFCR && isset($validationData['bank'])) {
                        $chequeRegister = ChequeRegister::where('company_id', $companyId)
                            ->where('bank_id',$validationData['bank']['bankmasterAutoID'])
                            ->where('bank_account_id',$validationData['account']['bankAccountAutoID'])
                            ->get();

                        if (count($chequeRegister) > 0) {
                            $activeChequeRegister = $chequeRegister->where('isActive',1)->first();
                            if (!is_null($activeChequeRegister)) {
                                $chequeRegisterDetails = ChequeRegisterDetail::where('company_id',$companyId)->where('cheque_register_master_id', $activeChequeRegister->id)->where('status', 0)->get();

                                if (count($chequeRegisterDetails) > 0) {
                                    $selectedChequeData = $chequeRegisterDetails->where('cheque_no', $pdcChequeDetails['cheque_number'])->first();
                                    if (!is_null($selectedChequeData)) {
                                        $chequeNumber = $pdcChequeDetails['cheque_number'];
                                        $chequeNumberID = $selectedChequeData->id;
                                    }
                                    else {
                                        $errorData[] = [
                                            'field' => "cheque_number",
                                            'message' => ["Entered cheque number does not match the available cheques in the register."]
                                        ];
                                    }
                                }
                                else {
                                    $errorData[] = [
                                        'field' => "cheque_number",
                                        'message' => ["No unused cheques available in the cheque register for the selected bank account."]
                                    ];
                                }
                            }
                            else {
                                $errorData[] = [
                                    'field' => "cheque_number",
                                    'message' => ["The cheque register for the selected bank account is inactive."]
                                ];
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "cheque_number",
                                'message' => ["Cheque register not found for the selected bank account."]
                            ];
                        }
                    }
                    else {
                        $chequeNumber = $pdcChequeDetails['cheque_number'];
                    }
                }
                else {
                    $chequeNumber = $pdcChequeDetails['cheque_number'];
                }
            }
            else {
                $chequeNumber = $pdcChequeDetails['cheque_number'];
            }
        }
        else {
            $errorData[] = [
                'field' => "cheque_number",
                'message' => ["cheque_number field is required."]
            ];
        }

        if (isset($pdcChequeDetails['cheque_date'])) {
            $data = self::validateAPIDate($pdcChequeDetails['cheque_date']);
            if ($data) {
                $chequeDate = Carbon::parse($pdcChequeDetails['cheque_date']);
            }
            else {
                $errorData[] = [
                    'field' => "cheque_date",
                    'message' => ["cheque_date format is invalid"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "cheque_date",
                'message' => ["cheque_date field is required"]
            ];
        }

        if (isset($pdcChequeDetails['amount'])) {
            if (gettype($pdcChequeDetails['amount']) == 'string') {
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

        if (empty($errorData)) {
            $returnData = [
                "status" => true,
                "data" => [
                    'chequeNo' => $chequeNumber,
                    'chequeRegisterAutoID' => $chequeNumberID,
                    'chequeDate' => $chequeDate->format('Y-m-d'),
                    'comments' => $pdcChequeDetails['comments'] ?? null,
                    'amount' => $pdcChequeDetails['amount']
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

    public static function createErrorResponseDataArray($narration,$masterIndex,$fieldErrors, $headerData, $detailData, $isPDCCheque, $pdcChequeData): array {
        $data = [
            'identifier' => [
                'unique-key' => $narration,
                'index' => $masterIndex + 1
            ],
            'fieldErrors' => $fieldErrors,
            'headerData' => [$headerData],
            'detailData' => [$detailData]
        ];

        if ($isPDCCheque) {
            $data['pdcChequeData'] = [$pdcChequeData];
        }

        return $data;
    }

    public static function createSuccessResponseDataArray($narration,$masterIndex,$code): array {
        return [
            'uniqueKey' => $narration,
            'index' => $masterIndex + 1,
            'paymentVoucherCode' => $code,
        ];
    }
}
