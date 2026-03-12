<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\helper\Helper;
use App\helper\TaxService;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\CreditNote;
use App\Models\CurrencyMaster;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerAssigned;
use App\Models\CustomerCurrency;
use App\Models\CustomerMaster;
use App\Models\DebitNote;
use App\Models\Employee;
use App\Models\ErpProjectMaster;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SegmentAssigned;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Models\Tax;
use App\Models\TaxVatCategories;
use App\Services\API\CreditNoteAPIService;
use App\Services\DocumentAutoApproveService;
use App\Services\PaymentVoucherServices;
use App\Traits\DocumentSystemMappingTrait;
use App\Jobs\InitiateWebhook;
use Carbon\Carbon;
use DateTime;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use App\helper\Workflow\DocumentApprove;

class CreateCreditNote implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DocumentSystemMappingTrait;

    public $input;
    public $timeout = 500;
    public $db;
    public $apiExternalKey;
    public $apiExternalUrl;
    public $authorization;
    public $externalReference;
    public $tenantUuid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($input, $db, $apiExternalKey, $apiExternalUrl, $authorization, $externalReference = null, $tenantUuid = null)
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
        $this->externalReference = $externalReference;
        $this->tenantUuid = $tenantUuid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        CommonJobService::db_switch($this->db);

        $fieldErrors = $masterDatasets = $detailsDataSets = $receiptVoucherDataSets = $errorDocuments = $successDocuments = [];
        $headerData = $detailData = $receiptVoucherData = ['status' => false , 'errors' => []];
        $commentTracker = [];
        $masterIndex = 0;
        $creditNotes = $this->input['credit_notes'];

        foreach ($creditNotes as $creditNote) {
            
            $creditNote['company_id'] = $this->input['company_id'];

            // Validate comment uniqueness in the input array
            if (!empty($creditNote['comments'])) {
                if (in_array($creditNote['comments'], $commentTracker)) {
                    $headerData['errors'][] = [
                        'field' => "comments",
                        'message' => ["The Comments should be unique."]
                    ];
                } else {
                    $commentTracker[] = $creditNote['comments'];
                }
            }

            $datasetMaster = self::validateCNMasterData($creditNote, $masterIndex);

            if (!$datasetMaster['status']) {
                $fieldErrors = $datasetMaster['fieldErrors'];
                $headerData['errors'] = $datasetMaster['data'];
            }

            $detailIndex = 0;
            $details = $creditNote['details'] ?? null;

            if ($details != null) {
                foreach ($details as $detail) {

                    $datasetDetails = self::validateCNDetailsData($creditNote,$detail,$datasetMaster);
                    

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

            $receiptVoucherIndex = 0;
            $receiptVoucherDetails = $creditNote['receipt_voucher_details'] ?? null;

            if (($datasetMaster['data']['creditNoteType'] == 3) && !is_null($receiptVoucherDetails)) {
                foreach ($receiptVoucherDetails as $reciptVoucherDetail) {
                    $datasetReceiptVoucher = self::validateCNReceiptVoucherDetailsData($creditNote, $reciptVoucherDetail, $datasetMaster);

                    if ($datasetReceiptVoucher['status']) {
                        $receiptVoucherDataSets[$masterIndex][] = $datasetReceiptVoucher['data'];
                    } else {
                        $receiptVoucherData['errors'][] = [
                            'index' => $receiptVoucherIndex + 1,
                            'error' => $datasetReceiptVoucher['data'],
                        ];
                        unset($receiptVoucherDataSets[$masterIndex]);
                    }

                    $receiptVoucherIndex++;
                }
            }

            // Validate refund total matches credit note line total for refund type credit note
            if (($datasetMaster['data']['creditNoteType'] == 3) && isset($receiptVoucherDataSets[$masterIndex])) {
                $totalMatchValidation = self::validateRefundAmountWithCreditNoteAmount($detailsDataSets[$masterIndex], $receiptVoucherDataSets[$masterIndex]);
                if (!$totalMatchValidation['status']) {
                    $headerData['errors'] = [
                        'field' => "details",
                        'message' => $totalMatchValidation['message']
                    ];
                }
            }

            if (empty($headerData['errors']) && empty($detailData['errors']) && empty($receiptVoucherData['errors']) && empty($fieldErrors)) {
                $dataWithDetails = Arr::add($datasetMaster['data'], 'details', $detailsDataSets[$masterIndex]);

                if (($datasetMaster['data']['creditNoteType'] == 3) && !is_null($receiptVoucherDetails)) {
                    $dataWithDetails = Arr::add($dataWithDetails, 'receipt_voucher_details', $receiptVoucherDataSets[$masterIndex]);
                }

                $masterDatasets[] = $dataWithDetails;
            }
            else {
                if (empty($headerData['errors'])) {
                    $headerData['status'] = true;
                }

                if (empty($detailData['errors'])) {
                    $detailData['status'] = true;
                }

                if (empty($receiptVoucherData['errors'])) {
                    $receiptVoucherData['status'] = true;
                }

                $errorDocuments[] = self::createErrorResponseDataArray(
                    $creditNote['comments'] ?? "",
                    $masterIndex,
                    $fieldErrors,
                    $headerData,
                    $detailData,
                    !is_null($receiptVoucherDetails),
                    $receiptVoucherData
                );

                $fieldErrors = [];
                $headerData = $detailData = $receiptVoucherData = ['status' => false , 'errors' => []];
            }

            $masterIndex++;
        }

        if(!empty($masterDatasets)) {
            DB::beginTransaction();

            $headerData = $detailData = $receiptVoucherDetailData = ['status' => true , 'errors' => []];

            foreach ($masterDatasets as $masterDataset) {
                $documentStatus = true;
                try {
                    $detailsData = $masterDataset['details'];
                    unset($masterDataset['details']);
                    $receiptVoucherDetailsData = [];
                    if (isset($masterDataset['receipt_voucher_details'])) {
                        $receiptVoucherDetailsData = $masterDataset['receipt_voucher_details'];
                        unset($masterDataset['receipt_voucher_details']);
                    }

                    $customerID = $masterDataset['customerID'];
                    $isVATEligible = TaxService::checkCompanyVATEligible($masterDataset['customerID']);
                    $masterDataset['VATPercentage'] = 0;
    
                    if ($isVATEligible) {
                        $defaultVAT = TaxService::getDefaultVAT($masterDataset['companySystemID'], $customerID, 0);
                        $vatPercentage = $defaultVAT['percentage'];
                        $masterDataset['VATPercentage'] = $vatPercentage;
                    }

                    $masterInsert = CreditNoteAPIService::createCreditNote($masterDataset);

                    if($masterInsert['status']) {
                        $cnMasterAutoId = $masterInsert['data']['creditNoteAutoID'];

                        foreach ($detailsData as $cnDetail) {
                            $cnDetail['creditNoteAutoID'] = $cnMasterAutoId;

                            $detailInsert = CreditNoteAPIService::createCreditNoteDetails($cnDetail);

                            if (!$detailInsert['status']) {
                                $documentStatus = false;
                                DB::rollBack();
                                $error = self::createErrorResponseDataArray($masterDataset['comments'], $masterDataset['initialIndex'], [], $headerData, $detailData, !empty($receiptVoucherDetailsData), $receiptVoucherDetailData);
                                $error['headerData'] = $detailInsert['message'];
                                $errorDocuments[] = $error;
                                break 2;
                            }
                            
                            $updateCreditNoteDetails = CreditNoteAPIService::updateCreditNoteDetails($detailInsert['data']['creditNoteDetailsID'],$detailInsert['data']);

                            if (!$updateCreditNoteDetails['status']) {
                                $documentStatus = false;
                                DB::rollBack();
                                $error = self::createErrorResponseDataArray($masterDataset['comments'], $masterDataset['initialIndex'], [], $headerData, $detailData, !empty($receiptVoucherDetailsData), $receiptVoucherDetailData);
                                $error['headerData'] = $updateCreditNoteDetails['message'];
                                $errorDocuments[] = $error;
                                break 2;
                            }                            
                        }

                        if ($documentStatus && ($masterDataset['creditNoteType'] == 3) && !empty($receiptVoucherDetailsData)) {
                            
                            $receiptVoucherFinalData = [];
                            foreach ($receiptVoucherDetailsData as $rvItem) {
                                $receiptVoucherFinalData[] = [
                                    'creditNoteAutoID' => $cnMasterAutoId,
                                    'custReceivePaymentAutoID' => $rvItem['autoID'],
                                    'refundAmount' => $rvItem['refund_amount'],
                                ];
                            }

                            $receiptVoucherStoreData = [
                                'companySystemID' => $masterDataset['companySystemID'],
                                'selectedVouchers' => $receiptVoucherFinalData,
                            ];

                            $createdReceipts = CreditNoteAPIService::storeCreditNoteReceiptVouchers($receiptVoucherStoreData);

                            if(empty($createdReceipts)) {
                                $documentStatus = false;
                                DB::rollBack();
                                $error = self::createErrorResponseDataArray($masterDataset['comments'], $masterDataset['initialIndex'], [], $headerData, $detailData, !empty($receiptVoucherDetailsData), $receiptVoucherDetailData);
                                $error['headerData'] = "Failed to store receipt vouchers";
                                $errorDocuments[] = $error;
                                break;
                            }
                        }

                        if($documentStatus) {
                            $confirmDataSet = CreditNote::find($cnMasterAutoId)->toArray();
                            $confirmDataSet['confirmedYN'] = 1;
                            $confirmDataSet['isAutoCreateDocument'] = true;
                            $confirmDataSet['documentSystemID'] = 19;

                            $cnUpdateData = CreditNoteAPIService::updateCreditNote($confirmDataSet['creditNoteAutoID'], $confirmDataSet);

                            if($cnUpdateData['status']){

                                $autoApproveParams = DocumentAutoApproveService::getAutoApproveParams($confirmDataSet['documentSystemID'],$confirmDataSet['creditNoteAutoID']);
                                $autoApproveParams['db'] = $this->db;

                                $approveDocument = DocumentApprove::approveDocument($autoApproveParams);

                                if ($approveDocument["success"]) {
                                    DB::commit();
                                    $cnID = $confirmDataSet['creditNoteAutoID'];
                                    $this->storeToDocumentSystemMapping(19,$cnID,$this->authorization);
                                    $success = self::createSuccessResponseDataArray($masterDataset['comments'], $masterDataset['initialIndex'], $confirmDataSet['creditNoteCode']);
                                    $successDocuments[] = $success;
                                }
                                else {
                                    DB::rollBack();
                                    $error = self::createErrorResponseDataArray($masterDataset['comments'], $masterDataset['initialIndex'], [], $headerData, $detailData, !empty($receiptVoucherDetailsData), $receiptVoucherDetailData);
                                    $error['headerData'] = $approveDocument['message'];
                                    $errorDocuments[] = $error;
                                }
                            }
                            else {
                                DB::rollBack();
                                $error = self::createErrorResponseDataArray($masterDataset['comments'], $masterDataset['initialIndex'], [], $headerData, $detailData, !empty($receiptVoucherDetailsData), $receiptVoucherDetailData);
                                $error['headerData'] = $cnUpdateData['message'];
                                $errorDocuments[] = $error;
                            }
                        }
                    }
                    else {
                        DB::rollBack();
                        $error = self::createErrorResponseDataArray($masterDataset['comments'], $masterDataset['initialIndex'], [], $headerData, $detailData, !empty($receiptVoucherDetailsData), $receiptVoucherDetailData);
                        $error['headerData'][] = [
                            'field' => "",
                            'message' => [$masterInsert['message']]
                        ];
                        $errorDocuments[] = $error;
                    }
                }
                catch (\Exception $e) {
                    DB::rollBack();
                    $error = self::createErrorResponseDataArray($masterDataset['comments'], $masterDataset['initialIndex'], [], $headerData, $detailData, !empty($receiptVoucherDetailsData), $receiptVoucherDetailData);
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
                'message' => "Credit Note created Successfully!",
                'code' => 200,
                'data' => $successDocuments,
            ];
        }

        // Dispatch webhook job
        $webhookPayload = ['data' => $returnData, 'externalReference' => $this->externalReference];
        InitiateWebhook::dispatch(
            $this->db,
            $this->apiExternalKey,
            $this->apiExternalUrl,
            $this->input['webhook_url'],
            $webhookPayload,
            $this->externalReference,
            $this->tenantUuid,
            $this->input['company_id'],
            $this->input['log_id'],
            $this->input['thirdPartyIntegrationKeyId']
        );
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

    public static function createErrorResponseDataArray($narration, $masterIndex, $fieldErrors, $headerData, $detailData, $hasReceiptVoucherDetails = false, $receiptVoucherData = null): array {
        $data = [
            'identifier' => [
                'unique-key' => $narration,
                'index' => $masterIndex + 1
            ],
            'fieldErrors' => $fieldErrors,
            'headerData' => [$headerData],
            'detailData' => [$detailData]
        ];

        if ($hasReceiptVoucherDetails && $receiptVoucherData !== null) {
            $data['receiptVoucherData'] = [$receiptVoucherData];
        }

        return $data;
    }

    public static function createSuccessResponseDataArray($narration,$masterIndex,$code): array {
        return [
            'uniqueKey' => $narration,
            'index' => $masterIndex + 1,
            'creditNoteCode' => $code,
        ];
    }

    private static function validateCNMasterData($request, $index): array {

        $errorData = $fieldErrors = [];

        $companyId = $request['company_id'] ?? null;

        $validTypes = [2, 3];
        if (array_key_exists('credit_note_type', $request)) {
            if (in_array($request['credit_note_type'], $validTypes)) {
                $creditNoteType = $request['credit_note_type'];
            }
            else {
                $errorData[] = [
                    'field' => 'credit_note_type',
                    'message' => ["Invalid credit note type selected. Please choose a valid type."],
                ];
            }
        }
        else {
            $creditNoteType = 2;
        }

        // Validate Receipt Voucher Details (Refund type only)
        if ($creditNoteType == 3) {
            $receiptVoucherDetails = $request['receipt_voucher_details'] ?? null;

            if (!isset($receiptVoucherDetails) || !is_array($receiptVoucherDetails) || count($receiptVoucherDetails) == 0) {
                $errorData[] = [
                    'field' => 'receipt_voucher_details',
                    'message' => ["Refund type credit note receipt_voucher_details is required"],
                ];
            }
        }
        
        // Validate Customer
        if (isset($request['customer'])) {
            $approvedCustomer = CustomerMaster::where(function ($query) use ($request) {
                                                    $query->where('CutomerCode', $request['customer'])
                                                        ->orWhere('customer_registration_no', $request['customer']);
                                                })
                                                ->first();

            if(!$approvedCustomer){
                $errorData[] = [
                    'field' => "customer",
                    'message' => ["Selected Customer is not available in the system"]
                ];
            }

            if ($approvedCustomer) {
                if($approvedCustomer->approvedYN == 0) {
                    $errorData[] = [
                        'field' => "customer",
                        'message' => ["Selected Customer is not approved"]
                    ];
                } else {
                    $customer = CustomerAssigned::Where('CutomerCode',$approvedCustomer->CutomerCode)
                    ->where('companySystemID', $request['company_id'])
                    ->where('isAssigned', -1)
                    ->first();
    
                    if(!$customer){
                        $errorData[] = [
                            'field' => "customer",
                            'message' => ["Selected Customer is not assigned to the company"]
                        ];
                    } else {
                        if($customer->isActive == 0) {
                            $errorData[] = [
                                'field' => "customer",
                                'message' => ["Selected Customer is not active"]
                            ];
                        }
        
                        // Validate Currency
                        if (isset($request['currency'])) {
                            $request['currency'] = strtoupper($request['currency']);
        
                            $isCurrencyAvailable = CurrencyMaster::where('CurrencyCode', $request['currency'])
                                                                ->first();
                            if(!$isCurrencyAvailable){
                                $errorData[] = [
                                    'field' => "currency",
                                    'message' => ["Selected currency is not available in the system."]
                                ];
                            } else {
                                $currency = CustomerCurrency::join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')
                                ->where('currencymaster.CurrencyCode', $request['currency'])
                                ->where('customerCodeSystem', $customer->customerCodeSystem)
                                ->where('isAssigned', -1)
                                ->first();
        
                                if(!$currency){
                                    $errorData[] = [
                                        'field' => "currency",
                                        'message' => ["The selected currency is not assigned to customer"]
                                    ];
                                }
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "currency",
                                'message' => ["currency field is required"]
                            ];
                        }
        
                        // Validate Document Date
                        if (isset($request['document_date'])) {
                            if(DateTime::createFromFormat('d-m-Y', $request['document_date'])) {
                                $documentDate = Carbon::parse($request['document_date']);
        
                                $invoiceDueDate = $documentDate->copy();
                                $invoiceDueDate->addDays($customer->creditDays);
        
                                // Validate Financial Year & Period
                                $financeYear = CompanyFinanceYear::where('companySystemID',$request['company_id'])
                                    ->where('isDeleted',0)
                                    ->where('isActive',-1)
                                    ->where('bigginingDate','<=',$documentDate)
                                    ->where('endingDate','>=',$documentDate)
                                    ->first();
        
                                if($financeYear){
                                    $financePeriod = CompanyFinancePeriod::where('companySystemID',$request['company_id'])
                                        ->where('departmentSystemID',4)
                                        ->where('companyFinanceYearID',$financeYear->companyFinanceYearID)
                                        ->where('isActive',-1)
                                        ->whereMonth('dateFrom',$documentDate->month)
                                        ->whereMonth('dateTo',$documentDate->month)
                                        ->first();
                                    if(!$financePeriod){
                                        $errorData[] = [
                                            'field' => "document_date",
                                            'message' => ["Financial period related to the selected Document date is not active for the specified department."]
                                        ];
                                    }
                                }
                                else{
                                    $errorData[] = [
                                        'field' => "document_date",
                                        'message' => ["Financial year related to the selected document date is either not active or not created."]
                                    ];
                                }
                            }
                            else {
                                $errorData[] = [
                                    'field' => "document_date",
                                    'message' => ["document_date format is invalid"]
                                ];
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "document_date",
                                'message' => ["document_date field is required"]
                            ];
                        }
                    }
                }
            }
        }
        else {
            $errorData[] = [
                'field' => "customer",
                'message' => ["customer field is required"]
            ];
        }


        // Validate Comment
        if (isset($request['comments'])) {
            $comments = CreditNote::where('companySystemID', $request['company_id'])
                                        ->where('comments', $request['comments'])     
                                        ->first();
            if($comments){
                $errorData[] = [
                    'field' => "comments",
                    'message' => ["The Comments should be unique."]
                ];
            }
        }  else {
            $errorData[] = [
                'field' => "comments",
                'message' => ["comments field is required"]
            ];
        }

        // Validate Project
        $checkProjectPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
        ->where('companySystemID', $request['company_id'])
        ->where('isYesNO', 1)
        ->exists();

        if($checkProjectPolicy){
            if (isset($request['project'])) {
                $project = ErpProjectMaster::where('companySystemID', $request['company_id'])
                                            ->where('projectCode', $request['project'])     
                                            ->first();
                if(!$project){
                    $errorData[] = [
                        'field' => "project",
                        'message' => ["The selected project code does not match with the system."]
                    ];
                }
            }
            else {
                $project = null;
            }
        } else {
            $project = null;
        }

        // Validate Debit Note
        if (isset($request['debit_note'])) {
            $debitNote = DebitNote::where('companySystemID', $request['company_id'])
                ->where('refferedBackYN', 0)
                ->where('debitNoteCode', $request['debit_note'])
                ->whereHas('company', function ($query) {
                    $query->where('masterCompanySystemIDReorting', '<>', 35);
                })
                ->first();

            if(!$debitNote){
                $errorData[] = [
                    'field' => "debit_note",
                    'message' => ["Selected debit note code is  not available in the system."]
                ];
            } else {
                if($debitNote->approved == 0) {
                    $errorData[] = [
                        'field' => "debit_note",
                        'message' => ["The selected debit note has not been approved"]
                    ];

                }
            }
        }
        else {
            $debitNote = null;
        }

        // Validate secondaryLogoCompanySystemID
        if (isset($request['secondaryLogoCompanySystemID'])) {
            $secondaryLogoCompany = Company::where('companySystemID', $request['secondaryLogoCompanySystemID'])
                                    ->first();

            if(!$secondaryLogoCompany){
                $errorData[] = [
                    'field' => "secondaryLogoCompanySystemID",
                    'message' => ["The selected company is not available in the system"]
                ];
            }
        }
        else {
            $secondaryLogoCompany = null;
        }

        //Validate vat_applicable
        if (isset($request['vat_applicable'])) {
            if($request['vat_applicable'] != 0 && $request['vat_applicable'] != 1) {
                $errorData[] = [
                    'field' => "vat_applicable",
                    'message' => ["Invalid VAT Applicable Type selected. Please choose the correct type."]
                ];
            }
        } else {
            $errorData[] = [
                'field' => "vat_applicable",
                'message' => ["vat_applicable field is required"]
            ];
        }


        // Validate Details
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
                    'customerID' => $customer->customerCodeSystem,
                    'projectID' => $project != null ? $project->id : null,
                    'customerCurrencyID' => $currency->currencyID,
                    'debitNoteAutoID' => $debitNote != null ? $debitNote->debitNoteAutoID : null,
                    'comments' => $request['comments'],
                    'secondaryLogoCompanySystemID' => $secondaryLogoCompany != null ? $secondaryLogoCompany->companySystemID : null,
                    'creditNoteDate' => $request['document_date'],
                    'companyFinanceYearID' => $financeYear->companyFinanceYearID,
                    'companyFinancePeriodID' => $financePeriod->companyFinancePeriodID,
                    'isVATApplicable' => $request['vat_applicable'],
                    'companySystemID' => $companyId,
                    'documentSystemID' => 19,
                    'creditNoteType' => $creditNoteType,
                    'isAutoCreateDocument' => true,
                    'initialIndex' => $index
                ]
            ];
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

    private static function validateCNDetailsData($masterData, $request ,$datasetMaster): array {
        $errorData = [];

        $companyId = $masterData['company_id'] ?? null;

        // Validate GL Code
        if (isset($request['gl_code'])) {
            $chartOfAccount = ChartOfAccount::where('AccountCode',$request['gl_code'])
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
                                        
                                    }
                                    else {
                                        $errorData[] = [
                                            'field' => "gl_code",
                                            'message' => ["Selected GL code is a control account and cannot be used."]
                                        ];
                                    }
                                }
                                else {
                                    $errorData[] = [
                                        'field' => "gl_code",
                                        'message' => ["Selected GL code is bank gl code."]
                                    ];
                                }
                            }
                            else {
                                $errorData[] = [
                                    'field' => "gl_code",
                                    'message' => ["Selected GL code is not active"]
                                ];
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "gl_code",
                                'message' => ["Selected GL code is not assigned to the company."]
                            ];
                        }
                    }
                    else {
                        $errorData[] = [
                            'field' => "gl_code",
                            'message' => ["Selected GL code is not active"]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "gl_code",
                        'message' => ["Selected GL code is not approved."]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "gl_code",
                    'message' => ["Selected GL code does not match any record in the system."]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "gl_code",
                'message' => ["gl_code field is required"]
            ];
        }

        // Validate Project

        $checkProjectPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
        ->where('companySystemID', $companyId)
        ->where('isYesNO', 1)
        ->exists();

        if($checkProjectPolicy){
            if (isset($request['project'])) {
                $project = ErpProjectMaster::where('projectCode', $request['project'])->first();
    
                if (!$project) {
                    $errorData[] = [
                        'field' => "project",
                        'message' => ["The selected project code does not match with the system."]
                    ];
                } else {
                    $projectID = $project->id;
                }
            }
            else {
                $projectID = $datasetMaster['data']['projectID'] ?? null;
            }
        } else {
            $projectID = null;
        }

        // Validate Segment
        if (isset($request['segment'])) {
            $segment = SegmentMaster::withoutGlobalScope('final_level')
                ->where('ServiceLineCode',$request['segment'])
                ->first();

            if ($segment) {
                if($segment->approved_yn == 0) {
                    $errorData[] = [
                        'field' => "segment",
                        'message' => ["The segment is not approved"]
                    ];
                } else {
                    $segmentAssigned = SegmentAssigned::Where('serviceLineSystemID',$segment->serviceLineSystemID)
                        ->where('companySystemID', $companyId)
                        ->where('isAssigned', 1)
                        ->first();

                    if(!$segmentAssigned){
                        $errorData[] = [
                            'field' => "segment",
                            'message' => ["The segment not assigned to selected company"]
                        ];
                    }
                }
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
                    if(isset($masterData['vat_applicable']) && $masterData['vat_applicable'] == 1){
                        $isCompanyVATEligible = Company::where('companySystemID', $companyId)
                            ->where('vatRegisteredYN', 1)
                            ->exists();
                        if(!$isCompanyVATEligible){
                            $errorData[] = [
                                'field' => "vat_applicable",
                                'message' => ["VAT is not registered for this company"]
                            ];
                        } else {
                            $isCompanyVatSetup = Tax::where('companySystemID', $companyId)
                                ->where('taxCategory', 2)
                                ->where('isActive', 1)
                                ->first();
            
                            if(!$isCompanyVatSetup){
                                $errorData[] = [
                                    'field' => "vat_applicable",
                                    'message' => ["VAT setup is not created for this company"]
                                ];
                            } else {
                                $isvatCategories = TaxVatCategories::where('taxMasterAutoID', $isCompanyVatSetup->taxMasterAutoID)
                                ->where('isActive', 1)
                                ->where('subCatgeoryType',1)
                                ->first();
            
                                if(!$isvatCategories){
                                    $errorData[] = [
                                        'field' => "vat_applicable",
                                        'message' => ["VAT subcategory not created "]
                                    ];
                                } else {
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
                                        $vatAmount = ($request['amount'] / (100 + $request['vat_percentage'])) * $request['vat_percentage'];
                                        if (round($vatAmount, 7) != round($request['vat_amount'], 7)) {
                                            $errorData[] = [
                                                'field' => "vat_amount",
                                                'message' => ["VAT% and VAT Amount is not matching"]
                                            ];
                                        }
                                    }
            
                                    if ($amountValidation && (!$vatPercentageValidation && $vatAmountValidation)) {
                                        $request['vat_percentage'] = ($request['vat_amount'] / ($request['amount'] - $request['vat_amount'])) * 100;
                                    }
            
                                    if ($amountValidation && ($vatPercentageValidation && !$vatAmountValidation)) {
                                        $request['vat_amount'] = ($request['amount'] / (100 + $request['vat_percentage'])) * $request['vat_percentage'];
                                    }
            
                                    if($amountValidation && (!$vatPercentageValidation && !$vatAmountValidation)){
            
                                        $isDefaultVatCategories = TaxVatCategories::where('taxMasterAutoID', $isCompanyVatSetup->taxMasterAutoID)
                                            ->where('isActive', 1)
                                            ->where('isDefault',1)
                                            ->first();
                                        if($isDefaultVatCategories){
                                            $request['vat_percentage'] = $isDefaultVatCategories->percentage;
                                            $request['vat_amount'] = ($request['amount'] / (100 + $request['vat_percentage'])) * $request['vat_percentage'];
                                            
                                        } else {
                                            $request['vat_percentage'] = $isvatCategories->percentage;
                                            $request['vat_amount'] = ($request['amount'] / (100 + $request['vat_percentage'])) * $request['vat_percentage'];
                                        }
            
                                        $netAmount = $request['amount'] - $request['vat_amount'];
                                        
                                    } else {
                                        $netAmount = $request['amount'] - $request['vat_amount'];
                                    }
                                    
                                    $isDefaultVatCategories = TaxVatCategories::where('taxMasterAutoID', $isCompanyVatSetup->taxMasterAutoID)
                                            ->where('isActive', 1)
                                            ->where('isDefault',1)
                                            ->first();
                                        if($isDefaultVatCategories){
                                            $request['vatSubCategoryID'] = $isDefaultVatCategories->taxVatSubCategoriesAutoID;
                                            $request['vatMasterCategoryID'] = $isDefaultVatCategories->mainCategory;
                                            
                                        } else {
                                            $request['vatSubCategoryID'] = $isvatCategories->taxVatSubCategoriesAutoID;
                                            $request['vatMasterCategoryID'] = $isvatCategories->mainCategory;
                                        }
                                }
                            }
                        }
                    } else {
                        $request['vat_percentage'] = 0;
                        $request['vat_amount'] = 0;
                        $netAmount = $request['amount'];
                        $request['vatMasterCategoryID'] = null;
                        $request['vatSubCategoryID'] = null;
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "amount",
                        'message' => ["Amount should be greater than 0 for every items."]
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


        // Validate Comment
        if (isset($request['comments'])) {
            $comments = $request['comments'] ?? null;
        }  else {
            $comments = $datasetMaster['data']['comments'] ?? null;
        }

        if (empty($errorData)) {
            $returnData = [
                "status" => true,
                "data" => [
                    'glCode' => $chartOfAccount->chartOfAccountSystemID,
                    'serviceLineSystemID' => $segment->serviceLineSystemID,
                    'ServiceLineCode' => $request['segment'],
                    'comments' => $comments,
                    'amount' => $request['amount'],
                    'VATPercentage' => $request['vat_percentage'],
                    'vatAmount' => $request['vat_amount'],
                    'netAmount' => $netAmount,
                    'vatMasterCategoryID' => $request['vatMasterCategoryID'],
                    'vatSubCategoryID' => $request['vatSubCategoryID'],
                    'detail_project_id' => $projectID,
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

    private static function validateCNReceiptVoucherDetailsData($masterData, $request, $datasetMaster): array
    {
        $errorData = [];

        $companyId = $masterData['company_id'] ?? null;

        $totalPayAmount = null;
        $usedRefundAmount = null;
        $balanceAmount = null;

        if(isset($request['receipt_voucher_code']) && $request['receipt_voucher_code'] != '') {
            $code = $request['receipt_voucher_code'];

            $receiptVoucher = CustomerReceivePayment::where('companySystemID', $companyId)
                ->where('approved', -1)
                ->where('custPaymentReceiveCode', $code)
                ->first();

            if ($receiptVoucher) {
                if ($receiptVoucher->documentType != 13) {
                    $errorData[] = [
                        'field' => 'receipt_voucher_code',
                        'message' => ["Receipt Voucher is not a valid customer invoice receipt for refund."],
                    ];
                }

                if ($receiptVoucher->pdcChequeYN == 1) {
                    $errorData[] = [
                        'field' => 'receipt_voucher_code',
                        'message' => ["Receipt Vouchers of type PDC cheque cannot be used for refund credit notes."],
                    ];
                }

                $creditNoteCustomerId = $datasetMaster['data']['customerID'];
                if ($receiptVoucher->customerID != $creditNoteCustomerId) {
                    $errorData[] = [
                        'field' => 'receipt_voucher_code',
                        'message' => ["Receipt Voucher customer does not match with credit note customer."],
                    ];
                }

                $creditNoteCurrencyId = $datasetMaster['data']['customerCurrencyID'];
                if ($receiptVoucher->custTransactionCurrencyID != $creditNoteCurrencyId) {
                    $errorData[] = [
                        'field' => 'receipt_voucher_code',
                        'message' => ["Receipt Voucher currency does not match with credit note currency."],
                    ];
                }

                if (empty($errorData)) {
                    $amounts = CustomerReceivePayment::selectRaw('ROUND(ABS(IFNULL(erp_customerreceivepayment.receivedAmount, 0)), IFNULL(currencymaster.DecimalPlaces, 2)) as totalPayAmountBank')
                        ->selectRaw('ROUND(IFNULL(refund_sum.usedRefundAmount, 0), IFNULL(currencymaster.DecimalPlaces, 2)) as usedRefundAmount')
                        ->selectRaw('ROUND((ABS(IFNULL(erp_customerreceivepayment.receivedAmount, 0)) - IFNULL(refund_sum.usedRefundAmount, 0)), IFNULL(currencymaster.DecimalPlaces, 2)) as balanceAmount')
                        ->leftJoin('currencymaster', 'currencymaster.currencyID', '=', 'erp_customerreceivepayment.custTransactionCurrencyID')
                        ->leftJoin(DB::raw('(SELECT 
                            erp_creditnote_receipts.custReceivePaymentAutoID,
                            ABS(SUM(erp_creditnote_receipts.refundAmount)) as usedRefundAmount
                            FROM erp_creditnote_receipts
                            WHERE erp_creditnote_receipts.companySystemID = ' . $companyId . '
                            GROUP BY erp_creditnote_receipts.custReceivePaymentAutoID) as refund_sum'), function ($join) {
                            $join->on('refund_sum.custReceivePaymentAutoID', '=', 'erp_customerreceivepayment.custReceivePaymentAutoID');
                        })
                        ->where('erp_customerreceivepayment.custReceivePaymentAutoID', $receiptVoucher->custReceivePaymentAutoID)
                        ->first();

                    if ($amounts) {
                        $totalPayAmount = $amounts->totalPayAmountBank;
                        $usedRefundAmount = $amounts->usedRefundAmount;
                        $balanceAmount = $amounts->balanceAmount;

                        if ($balanceAmount <= 0) {
                            $errorData[] = [
                                'field' => 'receipt_voucher_code',
                                'message' => ["The selected Receipt Voucher is already fully refunded."],
                            ];
                        }
                    }
                }
            } 
            else {
                $errorData[] = [
                    'field' => 'receipt_voucher_code',
                    'message' => ["Receipt voucher code is not approved or not exist"],
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => 'receipt_voucher_code',
                'message' => ["Receipt voucher code is required"],
            ];
        }        

        if(isset($request['refund_amount']) && $request['refund_amount'] != '') {
            $refundAmount = $request['refund_amount'];
            
            if ($refundAmount <= 0) {
                $errorData[] = [
                    'field' => 'refund_amount',
                    'message' => ["Refund amount is mandatory and should be greater than zero."],
                ];
            }

            if (!is_null($balanceAmount)) {
                if ($refundAmount > $balanceAmount) {
                    $errorData[] = [
                        'field' => 'refund_amount',
                        'message' => ["Refund amount cannot be greater than Receipt Voucher balance amount."],
                    ];
                }
            }
        }
        else{
            $errorData[] = [
                'field' => 'refund_amount',
                'message' => ["Refund amount is required"],
            ];
        }

        if (!empty($errorData)) {
            return [
                'status' => false,
                'data' => $errorData,
            ];
        }

        $returnDataset = [
            'autoID' => $receiptVoucher->custReceivePaymentAutoID,
            'receipt_voucher_code' => $request['receipt_voucher_code'],
            'refund_amount' => $request['refund_amount'],
        ];

        return [
            'status' => true,
            'data' => $returnDataset,
        ];
    }

    private static function validateRefundAmountWithCreditNoteAmount(array $details, array $receiptVoucherDetails): array
    {
        $totalLineNetAmount = collect($details)->sum('netAmount');
        $totalRefundAmount = collect($receiptVoucherDetails)->sum('refund_amount');

        $totalLineNetAmount = Helper::roundValue($totalLineNetAmount);
        $totalRefundAmount = Helper::roundValue($totalRefundAmount);

        if ($totalLineNetAmount > $totalRefundAmount) {
            return [
                'status' => false,
                'message' => ['The total amount of the credit note cannot be greater than the total refund amount.'],
            ];
        }

        if ($totalLineNetAmount < $totalRefundAmount) {
            return [
                'status' => false,
                'message' => ['The total amount of the credit note cannot be less than the total refund amount.'],
            ];
        }

        return ['status' => true];
    }

}
