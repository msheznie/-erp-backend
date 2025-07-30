<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\helper\Helper;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\Contract;
use App\Models\CurrencyMaster;
use App\Models\ErpProjectMaster;
use App\Models\SegmentAssigned;
use App\Models\SegmentMaster;
use App\Services\DocumentAutoApproveService;
use App\Services\JournalVoucherService;
use App\Traits\DocumentSystemMappingTrait;
use App\Jobs\AuditLog\ThirdPartyApiSummaryLogJob;
use App\Jobs\InitiateWebhook;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CreateJournalVoucher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DocumentSystemMappingTrait;
    public $record;
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
    public function __construct($input, $db, $apiExternalKey, $apiExternalUrl, $authorization, $externalReference = null,$tenantUuid = null)
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
        Log::useFiles(storage_path() . '/logs/create_journal_voucher.log');
        CommonJobService::db_switch($this->db);

        $fieldErrors = $masterDatasets = $detailsDataSets = $errorDocuments = $successDocuments = [];
        $headerData = $detailData = ['status' => false , 'errors' => []];

        $masterIndex = 0;
        $jvs = $this->input['journalVouchers'];

        foreach ($jvs as $jv) {
            $jv['company_id'] = $this->input['company_id'];

            $datasetMaster = self::validateMasterData($jv,$masterIndex);

            if (!$datasetMaster['status']) {
                $fieldErrors = $datasetMaster['fieldErrors'];
                $headerData['errors'] = $datasetMaster['data'];
            }

            $detailIndex = 0;
            $details = $jv['details'] ?? null;

            foreach ($details as $detail) {

                $datasetDetails = self::validateDetailsData($jv,$detail);

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

                $errorDocuments[] = self::createErrorResponseDataArray($jv['narration'], $masterIndex, $fieldErrors, $headerData, $detailData);

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

                    $masterInsert = JournalVoucherService::createJournalVoucher($masterDataset);

                    if($masterInsert['status']) {
                        $jvMasterAutoId = $masterInsert['data']['jvMasterAutoId'];

                        foreach ($detailsData as $jvDetail) {
                            $jvDetail['jvMasterAutoId'] = $jvMasterAutoId;

                            $detailInsert = JournalVoucherService::createJournalVoucherDetail($jvDetail);

                            if (!$detailInsert['status']) {
                                $documentStatus = false;
                                DB::rollBack();
                                $error = self::createErrorResponseDataArray($masterDataset['JVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
                                $error['headerData'] = $detailInsert['message'];
                                $errorDocuments[] = $error;
                                break 2;
                            }
                        }

                        if($documentStatus) {
                            $confirmDataSet = $masterInsert['data'];
                            $confirmDataSet['confirmedYN'] = 1;
                            $confirmDataSet['isAutoCreateDocument'] = true;

                            $jvUpdateData = JournalVoucherService::updateJournalVoucher($confirmDataSet['jvMasterAutoId'],$confirmDataSet);

                            if($jvUpdateData['status']){

                                $autoApproveParams = DocumentAutoApproveService::getAutoApproveParams($confirmDataSet['documentSystemID'],$confirmDataSet['jvMasterAutoId']);
                                $autoApproveParams['db'] = $this->db;
                                $autoApproveParams['supplierPrimaryCode'] = $confirmDataSet['JVcode'];

                                $approveDocument = Helper::approveDocument($autoApproveParams);

                                if ($approveDocument["success"]) {
                                    DB::commit();
                                    $jvID = $confirmDataSet['jvMasterAutoId'];
                                    $this->storeToDocumentSystemMapping(11,$jvID,$this->authorization);
                                    $success = self::createSuccessResponseDataArray($masterDataset['JVNarration'], $masterDataset['initialIndex'], $confirmDataSet['JVcode']);
                                    $successDocuments[] = $success;
                                }
                                else {
                                    DB::rollBack();
                                    $error = self::createErrorResponseDataArray($masterDataset['JVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
                                    $error['headerData'] = $approveDocument['message'];
                                    $errorDocuments[] = $error;
                                }
                            }
                            else {
                                DB::rollBack();
                                $error = self::createErrorResponseDataArray($masterDataset['JVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
                                $error['headerData'] = $jvUpdateData['message'];
                                $errorDocuments[] = $error;
                            }
                        }
                    }
                    else {
                        DB::rollBack();
                        $error = self::createErrorResponseDataArray($masterDataset['JVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
                        $error['headerData'][] = [
                            'field' => "",
                            'message' => [$masterInsert['message']]
                        ];
                        $errorDocuments[] = $error;
                    }
                }
                catch (\Exception $e) {
                    DB::rollBack();
                    $error = self::createErrorResponseDataArray($masterDataset['JVNarration'], $masterDataset['initialIndex'], [], $headerData, $detailData);
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
                'errors' => $errorDocuments
            ];
        }

        if(!empty($successDocuments)) {
            $returnData[] = [
                'success' => true,
                'message' => "Journal voucher created Successfully!",
                'code' => 200,
                'data' => $successDocuments
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

    public static function validateAPIDate($date): bool
    {
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

    public static function validateMasterData($request,$index): array {

        $errorData = $fieldErrors = [];

        if (isset($request['journalVoucherType'])) {
            if(is_int($request['journalVoucherType'])) {
                if ($request['journalVoucherType'] != 1) {
                    $errorData[] = [
                        'field' => "journalVoucherType",
                        'message' => ["journalVoucherType format is invalid"]
                    ];
                }
                else {
                    $request['journalVoucherType'] = 0;
                }
            }
            else {
                $errorData[] = [
                    'field' => "journalVoucherType",
                    'message' => ["journalVoucherType must be an integer"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "journalVoucherType",
                'message' => ["journalVoucherType field is required"]
            ];
        }

        if (isset($request['currency'])) {
            $request['currency'] = strtoupper($request['currency']);

            $currency = CurrencyMaster::where('CurrencyCode', $request['currency'])->first();

            if($currency){
                $request['currency'] = $currency->currencyID;
            }
            else {
                $errorData[] = [
                    'field' => "currency",
                    'message' => ["Invalid Currency"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "currency",
                'message' => ["currency field is required"]
            ];
        }

        if (!isset($request['narration'])) {
            $errorData[] = [
                'field' => "narration",
                'message' => ["narration field is required"]
            ];
            $errorData[] = $fieldErrors;
        }

        if (isset($request['jvDate'])) {
            $data = self::validateAPIDate($request['jvDate']);

            if ($data) {
                $documentDate = Carbon::parse($request['jvDate']);

                if($documentDate->lessThanOrEqualTo(Carbon::today())) {
                    $financeYear = CompanyFinanceYear::where('companySystemID',$request['company_id'])
                        ->where('isDeleted',0)
                        ->where('isActive',-1)
                        ->where('isCurrent',-1)
                        ->where('bigginingDate','<=',$documentDate)
                        ->where('endingDate','>=',$documentDate)
                        ->first();

                    if($financeYear){
                        $request['companyFinanceYearID'] = $financeYear['companyFinanceYearID'];

                        $financePeriod = CompanyFinancePeriod::where('companySystemID',$request['company_id'])
                            ->where('departmentSystemID',5)
                            ->where('companyFinanceYearID',$financeYear->companyFinanceYearID)
                            ->where('isActive',-1)
                            ->whereMonth('dateFrom',$documentDate->month)
                            ->whereMonth('dateTo',$documentDate->month)
                            ->first();

                        if($financePeriod){
                            $request['companyFinancePeriodID'] = $financePeriod['companyFinancePeriodID'];

                            if (isset($request['reversalJV'])) {
                                if(is_int($request['reversalJV'])) {
                                    if(in_array($request['reversalJV'],[1,2])) {
                                        if($request['reversalJV'] == 1) {
                                            $request['reversalJV'] = 1;
                                            if(isset($request['reversalDate'])) {
                                                $data = self::validateAPIDate($request['reversalDate']);
                                                if($data) {
                                                    $documentDate = Carbon::parse($request['jvDate']);
                                                    $reversalJVDate = Carbon::parse($request['reversalDate']);

                                                    if(!$reversalJVDate->greaterThan($documentDate)) {
                                                        $errorData[] = [
                                                            'field' => "reversalDate",
                                                            'message' => ["Reversal JV date cannot be less than or equal to the document date"]
                                                        ];
                                                    }

                                                    $today = Carbon::now()->startOfDay();

                                                    if (!$reversalJVDate->greaterThan($today)) {
                                                        $errorData[] = [
                                                            'field' => "reversalDate",
                                                            'message' => ["Reversal JV date cannot be less than or equal to the current date"]
                                                        ];
                                                    }
                                                }
                                                else {
                                                    $errorData[] = [
                                                        'field' => "reversalDate",
                                                        'message' => ["reversalDate format is invalid"]
                                                    ];
                                                }
                                            }
                                            else {
                                                $errorData[] = [
                                                    'field' => "reversalDate",
                                                    'message' => ["reversalDate field is required"]
                                                ];
                                            }
                                        }
                                        else {
                                            $request['reversalJV'] = 0;
                                            $request['reversalDate'] = null;
                                        }
                                    }
                                    else {
                                        $errorData[] = [
                                            'field' => "reversalJV",
                                            'message' => ["reversalJV format is invalid"]
                                        ];
                                    }
                                }
                                else {
                                    $errorData[] = [
                                        'field' => "reversalJV",
                                        'message' => ["reversalJV mus be an integer"]
                                    ];
                                }
                            }
                            else {
                                $request['reversalJV'] = 0;
                                $request['reversalDate'] = null;
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "jvDate",
                                'message' => ["Finance Period Not Active"]
                            ];
                        }
                    }
                    else{
                        $errorData[] = [
                            'field' => "jvDate",
                            'message' => ["Finance Year Not Found"]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "jvDate",
                        'message' => ["The Journal voucher date must be today or before"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "jvDate",
                    'message' => ["jvDate format is invalid"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "jvDate",
                'message' => ["jvDate field is required"]
            ];
        }

        if (isset($request['relatedParty'])) {
            if(is_int($request['relatedParty'])) {
                if(in_array($request['relatedParty'],[1,2])) {
                    $request['relatedParty'] = $request['relatedParty'] == 1 ? 1 : 0;
                }
                else {
                    $errorData[] = [
                        'field' => "relatedParty",
                        'message' => ["relatedParty format is invalid"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "relatedParty",
                    'message' => ["relatedParty mus be an integer"]
                ];
            }
        }
        else{
            $request['relatedParty'] = 0;
        }

        $details = $request['details'] ?? null;

        if(isset($details)) {
            $detailsCollection = collect($details);

            if($detailsCollection->count() >= 2) {
                $totalCredit = $detailsCollection->sum('creditAmount');
                $totalDebit = $detailsCollection->sum('debitAmount');

                if($totalCredit != $totalDebit) {
                    $errorData[] = [
                        'field' => "details",
                        'message' => ["Debit amount total and credit amount total is not matching"]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "details",
                    'message' => ["details cannot be less than two"]
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
                    'jvType' => $request['journalVoucherType'],
                    'JVNarration' => $request['narration'],
                    'currencyID' => $request['currency'],
                    'reversalJV' => $request['reversalJV'],
                    'JVdate' => $request['jvDate'],
                    'isRelatedPartyYN' => $request['relatedParty'],
                    'companySystemID' => $request['company_id'],
                    'companyFinanceYearID' => $request['companyFinanceYearID'],
                    'companyFinancePeriodID' => $request['companyFinancePeriodID'],
                    'isAutoCreateDocument' => true,
                    'initialIndex' => $index
                ]
            ];

            if($request['reversalJV'] == 1){
                $returnDataset['data']['reversalDate'] = $request['reversalDate'];
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

    public static function validateDetailsData($masterData, $request): array {

        $errorData = [];

        if(isset($request['glCode'])) {
            $chartOfAccountAssign = ChartOfAccountsAssigned::whereHas('chartofaccount', function ($q) {
                $q->where('isApproved', 1)
                  ->where('isActive', 1);
            })->where('companySystemID',$masterData['company_id'])
                ->where('AccountCode',$request['glCode'])
                ->where('isAssigned', -1)
                ->where('isActive', 1)
                ->where('isBank', 0)
                ->first();

            if($chartOfAccountAssign){
                if($chartOfAccountAssign->controllAccountYN == 1) {
                    $errorData[] = [
                        'field' => 'glCode',
                        'message' => ['Journal voucher creation is not allowed with a control account.']
                    ];
                }
                else {
                    $request['glCodeID'] = $chartOfAccountAssign->chartOfAccountSystemID;
                }
            }
            else {
                $errorData[] = [
                    'field' => 'glCode',
                    'message' => ['GlCode not found']
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "glCode",
                'message' => ["glCode field is required"]
            ];
        }

        if (isset($request['project'])) {
            $checkProjectSelectionPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
                ->where('companySystemID', $masterData['company_id'])
                ->first();

            if ($checkProjectSelectionPolicy->isYesNO == 1) {
                $projectExist = ErpProjectMaster::where('projectCode', $request['project'])->first();

                if($projectExist) {
                    $request['project'] = $projectExist->id;
                }
                else {
                    $errorData[] = [
                        'field' => 'project',
                        'message' => ['Project code not found in the system']
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => 'project',
                    'message' => ['Project not enabled']
                ];
            }
        }
        else {
            $request['project'] = null;
        }

        if (isset($request['segment'])) {
            $segment = SegmentMaster::where('ServiceLineCode',$request['segment'])
                ->where('isDeleted', 0)
                ->where('isActive', 1)
                ->where('companySystemID', $masterData['company_id'])
                ->first();

            if($segment){
                if($segment->approved_yn == 0) {
                    $errorData[] = [
                            'field' => "segment",
                            'message' => ["Selected segment is not approved"]
                        ];
                } else {
                    $segmentAssigned = SegmentAssigned::where('serviceLineSystemID',$segment->serviceLineSystemID)
                        ->where('companySystemID', $segment->companySystemID)
                        ->where('isAssigned', 1)
                        ->first();

                    if(!$segmentAssigned){
                        $errorData[] = [
                            'field' => "segment",
                            'message' => ["Selected segment is not assigned to the company"]
                        ];
                    } else {
                        $request['segmentID'] = $segment->serviceLineSystemID;
                    }
                }
            } else {
                $errorData[] = [
                    'field' => 'segment',
                    'message' => ['Segment not found']
                ];
            }
        } else {
            $errorData[] = [
                'field' => "segment",
                'message' => ["segment field is required"]
            ];
        }

        if (isset($request['clientContract'])) {
            $checkClientContractPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 93)
                ->where('companySystemID', $masterData['company_id'])
                ->first();

            if ($checkClientContractPolicy->isYesNO == 1) {
                $contract = Contract::where('companySystemID',$masterData['company_id'])
                    ->where('ContractNumber',$request['clientContract'])
                    ->first();

                if($contract) {
                    $request['clientContractID'] = $contract->contractUID;
                }
                else {
                    $errorData[] = [
                        'field' => 'clientContract',
                        'message' => ['Client Contract not found in the system']
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => 'clientContract',
                    'message' => ['clientContract not enabled']
                ];
            }
        }
        else {
            $request['clientContract'] = null;
        }

        $debitValidation = false;
        if(isset($request['debitAmount'])) {
            if (gettype($request['debitAmount']) != 'string') {
                $debitValidation = true;
            }
            else {
                $errorData[] = [
                    'field' => "debitAmount",
                    'message' => ["debitAmount must be a numeric"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "debitAmount",
                'message' => ["debitAmount field is required"]
            ];
        }

        $creditValidation = false;
        if(isset($request['creditAmount'])) {
            if (gettype($request['creditAmount']) != 'string') {
                $creditValidation = true;
            }
            else {
                $errorData[] = [
                    'field' => "creditAmount",
                    'message' => ["creditAmount must be a numeric"]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "creditAmount",
                'message' => ["creditAmount field is required"]
            ];
        }

        if ($creditValidation && $debitValidation) {
            if($request['creditAmount'] < 0) {
                $errorData[] = [
                    'field' => "creditAmount",
                    'message' => ["Credit amount cannot less than 0"]
                ];
            }

            if($request['debitAmount'] < 0) {
                $errorData[] = [
                    'field' => "debitAmount",
                    'message' => ["Debit amount cannot less than 0"]
                ];
            }

            if(($request['creditAmount'] > 0) && ($request['debitAmount'] > 0)) {
                $errorData[] = [
                    'field' => "debitAmount",
                    'message' => ["Cannot enter both credit and debit amount same time"]
                ];
                $errorData[] = [
                    'field' => "creditAmount",
                    'message' => ["Cannot enter both credit and debit amount same time"]
                ];
            }
        }

        if (empty($errorData)) {
            $returnData = [
                "status" => true,
                "data" => [
                    "chartOfAccountSystemID" => $request['glCodeID'],
                    "glAccount" => $request['glCode'],
                    "comments" => $request['comment'] ?? null,
                    "companySystemID" => $masterData['company_id'],
                    "debitAmount" => $request['debitAmount'],
                    "creditAmount" => $request['creditAmount'],
                    "serviceLineSystemID" => $request['segmentID'],
                    "serviceLineCode" => $request['segment'],
                    "detail_project_id" => $request['project'],
                    "contractUID" => isset($request['clientContractID']) ? $request['clientContractID'] : null,
                    "clientContractID" => $request['clientContract'],
                    "isAutoCreateDocument" => 1
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

    public static function createErrorResponseDataArray($narration,$masterIndex,$fieldErrors, $headerData, $detailData): array
    {
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
    

    public static function createSuccessResponseDataArray($narration,$masterIndex,$code): array
    {
        return [
            'uniqueKey' => $narration,
            'index' => $masterIndex + 1,
            'voucherCode' => $code,
        ];
    }
}
