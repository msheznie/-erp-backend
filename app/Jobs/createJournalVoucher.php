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
use App\Models\SegmentMaster;
use App\Services\DocumentAutoApproveService;
use App\Services\JournalVoucherService;
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

class createJournalVoucher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DocumentSystemMappingTrait;
    public $record;
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
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
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
        Log::useFiles(storage_path() . '/logs/create_journal_voucher.log');
        CommonJobService::db_switch($this->db);
        try {
            $errorDetails = $successDetails = [];
            $inputArray = $this->input;
            if(!empty($inputArray['journalVouchers'])) {
                $compId = $inputArray['company_id'];
                $company = Company::where('companySystemID', $compId)->first();
                if (empty($company)) {
                    $responseData = [
                        "success" => false,
                        "message" => "Validation Failed",
                        "code" => 402,
                        "errors" => [
                            'fieldErrors' => [
                                'field' => '',
                                'message' => ['Company not found']
                            ],
                        ]
                    ];
                }

                $jvNo = 1;
                DB::beginTransaction();
                foreach ($inputArray['journalVouchers'] as $input)
                {
                    $validationError = $headerError = [];
                    if(empty($input['journalVoucherType'])) {
                        $validationError[] = [
                            'field' => 'journalVoucherType',
                            'message' => ['Journal voucher type field is required']
                        ];
                    } else if($input['journalVoucherType'] != 1) {
                        $headerError[] = [
                            'field' => 'journalVoucherType',
                            'message' => ['Journal voucher type is not valid.']
                        ];
                    }

                    $masterDetails = [
                        "JVNarration" => $input['narration'],
                        "JVdate" => $input['jvDate'],
                        "companySystemID" => $compId,
                        "jvType" => 0,
                        "reversalJV" => (isset($input['reversalJV']) && $input['reversalJV'] == 1) ? 1 : 0,
                        "reversalDate" => $input['reversalDate'] ?? null,
                        "isRelatedPartyYN" => $input['relatedParty'] ?? null,
                        "isAutoCreateDocument" => 1
                    ];

                    if(empty($input['currency'])) {
                        $validationError[] = [
                            'field' => 'currency',
                            'message' => ['Currency field is required']
                        ];
                    } else {
                        $currencyExist = CurrencyMaster::where('CurrencyCode', $input['currency'])->first();
                        if(empty($currencyExist)) {
                            $headerError[] = [
                                'field' => 'currency',
                                'message' => ['Currency code not available in the system.']
                            ];
                        } else {
                            $masterDetails['currencyID'] = $currencyExist->currencyID;
                        }
                    }
                    if(empty($input['narration'])) {
                        $validationError[] = [
                            'field' => 'narration',
                            'message' => ['Narration field is required']
                        ];
                    }

                    if(empty($input['jvDate'])) {
                        $validationError[] = [
                            'field' => 'jvDate',
                            'message' => ['Journal voucher date field is required']
                        ];
                    }
                    else {
                        $jvDate = Carbon::parse($input['jvDate']);
                        $currentDate = Carbon::now()->startOfDay();
                        if ($jvDate->gt($currentDate)) {
                            $headerError[] = [
                                'field' => 'jvDate',
                                'message' => ['The Journal voucher date must be today or before.']
                            ];
                        } else {
                            $financeYear = CompanyFinanceYear::active_finance_year($compId, $input['jvDate']);
                            if (empty($financeYear)) {
                                $headerError[] = [
                                    'field' => 'jvDate',
                                    'message' => ['Finance Year not found']
                                ];
                            } else {
                                $masterDetails['companyFinanceYearID'] = $financeYear['companyFinanceYearID'];
                            }

                            $financePeriod = CompanyFinancePeriod::activeFinancePeriod($compId, 5, $input['jvDate']);
                            if (empty($financePeriod)) {
                                $headerError[] = [
                                    'field' => 'jvDate',
                                    'message' => ['Finance Period not found']
                                ];
                            } else {
                                $masterDetails['companyFinancePeriodID'] = $financePeriod['companyFinancePeriodID'];
                            }
                        }
                    }

                    if(!empty($input['reversalJV']) && $input['reversalJV'] == 1) {
                        if(empty($input['reversalDate'])) {
                            $validationError[] = [
                                'field' => 'reversalDate',
                                'message' => ['Reversal date field is required']
                            ];
                        } else if (!empty($input['jvDate']) && $input['reversalDate'] <= $input['jvDate']) {
                            $headerError[] = [
                                'field' => 'reversalDate',
                                'message' => ['Reversal JV date cannot be less than or equal to the document date.']
                            ];
                        }
                    }

                    $jvDetails = [];
                    if(empty($input['details'])) {
                        $validationError[] = [
                            'field' => 'reversalDate',
                            'message' => ['Journal voucher details are required']
                        ];
                    }
                    else {
                        $totals = collect($input['details'])->reduce(function ($carry, $detail) {
                            $carry['totalDebit'] += $detail['debitAmount'] ?? 0;
                            $carry['totalCredit'] += $detail['creditAmount'] ?? 0;
                            return $carry;
                        }, ['totalDebit' => 0, 'totalCredit' => 0]);

                        if($totals['totalDebit'] != $totals['totalCredit']) {
                            $detailsDataError[] = [
                                'field' => 'debitAmount & creditAmount',
                                'message' => ['Debit amount total and credit amount total is not matching']
                            ];
                        }

                        $detailIndex = 1;
                        $detailsError = [];
                        foreach ($input['details'] as $detail) {
                            $detailsDataError = [];
                            if(empty($detail['glCode'])) {
                                $validationError[] = [
                                    'field' => 'glCode',
                                    'message' => ['Gl code field is required']
                                ];
                            } else {
                                $chartOfAccountAssign = ChartOfAccountsAssigned::whereHas('chartofaccount', function ($q) {
                                        $q->where('isApproved', 1);
                                    })->where('companySystemID',$compId)
                                    ->where('AccountCode',$detail['glCode'])
                                    ->where('isAssigned', -1)
                                    ->where('isBank', 0)
                                    ->first();

                                if(!$chartOfAccountAssign){
                                    $detailsDataError[] = [
                                        'field' => 'glCode',
                                        'message' => ['GlCode not found']
                                    ];
                                } else if($chartOfAccountAssign->controllAccountYN == 1) {
                                    $detailsDataError[] = [
                                        'field' => 'glCode',
                                        'message' => ['Journal voucher creation is not allowed with a control account.']
                                    ];
                                } else if($chartOfAccountAssign->isActive != 1) {
                                    $detailsDataError[] = [
                                        'field' => 'glCode',
                                        'message' => [$detail['glCode'] . ' Gl code is not active.']
                                    ];
                                } else {
                                    $chartOfAccountId = $chartOfAccountAssign->chartOfAccountSystemID;
                                }
                            }

                            if(empty($detail['segment'])) {
                                $validationError[] = [
                                    'field' => 'segment',
                                    'message' => ['Segment field is required']
                                ];
                            } else {
                                $detSegment = SegmentMaster::where('ServiceLineCode',$detail['segment'])
                                    ->where('isDeleted', 0)
                                    ->where('companySystemID', $compId)
                                    ->first();
                                if(!$detSegment){
                                    $detailsDataError[] = [
                                        'field' => 'segment',
                                        'message' => ['Segment not found']
                                    ];
                                } else if($detSegment->isActive != 1) {
                                    $detailsDataError[] = [
                                        'field' => 'glCode',
                                        'message' => [$detail['segment'] . ' Segment is not active.']
                                    ];
                                } else {
                                    $serviceLineSystemID = $detSegment->serviceLineSystemID;
                                }
                            }

                            if(!isset($detail['debitAmount'])) {
                                $validationError[] = [
                                    'field' => 'debitAmount',
                                    'message' => ['Debit amount field is required']
                                ];
                            }

                            if(!isset($detail['creditAmount'])) {
                                $validationError[] = [
                                    'field' => 'creditAmount',
                                    'message' => ['Credit amount field is required']
                                ];
                            }

                            $debit = $detail['debitAmount'] ?? 0;
                            $credit = $detail['creditAmount'] ?? 0;
                            if ($credit > 0 && $debit !== 0) {
                                $detailsDataError[] = [
                                    'field' => 'debitAmount',
                                    'message' => ['Debit must be 0 when Credit is greater than 0']
                                ];
                            } elseif ($debit > 0 && $credit !== 0) {
                                $detailsDataError[] = [
                                    'field' => 'creditAmount',
                                    'message' => ['Credit must be 0 when Debit is greater than 0']
                                ];
                            }

                            if(isset($detail['project']) && !empty($detail['project'])) {
                                $checkProjectSelectionPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
                                    ->where('companySystemID', $compId)
                                    ->first();
                                if ($checkProjectSelectionPolicy->isYesNO != 1) {
                                    $detailsDataError[] = [
                                        'field' => 'project',
                                        'message' => ['Project not enabled']
                                    ];
                                } else {
                                    $projectExist = ErpProjectMaster::where('projectCode', $detail['project'])->first();
                                    if(!$projectExist) {
                                        $detailsDataError[] = [
                                            'field' => 'project',
                                            'message' => ['Project code not found in the system']
                                        ];
                                    } else {
                                        $detailProjectId = $projectExist->id;
                                    }
                                }
                            }

                            if(isset($detail['clientContract']) && !empty($detail['clientContract'])) {
                                $contract = Contract::where('companySystemID',$compId)
                                    ->where('ContractNumber',$detail['clientContract'])
                                    ->first();

                                if(!$contract) {
                                    $detailsDataError[] = [
                                        'field' => 'clientContract',
                                        'message' => ['Client Contract not found in the system']
                                    ];
                                } else {
                                    $contractUid = $contract->contractUID;
                                }
                            }

                            if (empty($validationError) && empty($headerError) && empty($detailsDataError)) {
                                /** details setting for add details function */
                                $jvDetails[] = [
                                    "chartOfAccountSystemID" => $chartOfAccountId,
                                    "comments" => $detail['comment'] ?? null,
                                    "companySystemID" => $compId,
                                    "debitAmount" => $detail['debitAmount'],
                                    "creditAmount" => $detail['creditAmount'],
                                    "serviceLineSystemID" => $serviceLineSystemID,
                                    "serviceLineCode" => $detail['segment'],
                                    "glAccount" => $detail['glCode'],
                                    "detail_project_id" => isset($detailProjectId) ? $detailProjectId : null,
                                    "contractUID" => $contractUid ?? null,
                                    "clientContractID" => $detail['clientContract'] ?? null,
                                    "isAutoCreateDocument" => 1
                                ];
                            }

                            if(!empty($detailsDataError)) {
                                $detailsError[] = [
                                    'index' => $detailIndex,
                                    'error' => $detailsDataError
                                ];
                            }
                            $detailIndex++;
                        }
                    }

                    if(empty($headerError) && empty($validationError) && empty($detailsError))
                    {
                        /*** Insert details */
                        $createJournalVoucher = self::createJournalVoucher($masterDetails, $jvDetails, $compId);
                        if(!$createJournalVoucher['status']) {
                            $errorDetails[] =
                                ['identifier' =>
                                    [
                                        'uniqueKey' => isset($masterDetails['JVNarration']) ? $masterDetails['JVNarration']: "",
                                        'index' => $jvNo
                                    ],
                                    'fieldErrors' => [],
                                    'headerData' => $createJournalVoucher['error'],
                                    'detailData' => []
                                ];
                        }
                        else {
                            $successDetails[] = [
                                'uniqueKey' => isset($masterDetails['JVNarration']) ? $masterDetails['JVNarration']: "",
                                'index' => $jvNo,
                                'voucherCode' => $createJournalVoucher['jvCode'] ?? ''
                            ];
                        }
                    }
                    else {
                        if(empty($headerError)) {
                            $headerError = [
                                'status' => true,
                                'errors' => []
                            ];
                        } else {
                            $headerError = [
                                'status' => false,
                                'errors' => $headerError
                            ];
                        }

                        if(empty($detailsError)) {
                            $detailsError = [
                                'status' => true,
                                'errors' => []
                            ];
                        } else {
                            $detailsError = [
                                'status' => false,
                                'errors' => $detailsError
                            ];
                        }

                        $errorDetails[] =
                            ['identifier' =>
                                [
                                    'uniqueKey' => isset($input['narration']) ? $input['narration']: "",
                                    'index' => $jvNo
                                ],
                                'fieldErrors' => $validationError,
                                'headerData' => $headerError,
                                'detailData' => $detailsError
                            ];
                    }
                    $jvNo++;
                }
                if(!empty($errorDetails)) {
                    DB::rollBack();
                    $responseData = [
                        "success" => false,
                        "message" => "Validation Failed",
                        "code" => 422,
                        "errors" => $errorDetails
                    ];
                    Log::error($responseData);
                } else {
                    DB::commit();
                    $responseData = [
                        "success" => true,
                        "message" => "Journal voucher created Successfully!",
                        "code" => 200,
                        "data" => $successDetails
                    ];
                    Log::info($responseData);
                }
            }

            $apiExternalKey = $this->apiExternalKey;
            $apiExternalUrl = $this->apiExternalUrl;
            if($apiExternalKey != null && $apiExternalUrl != null) {
                $client = new Client();
                $headers = [
                    'content-type' => 'application/json',
                    'Authorization' => 'ERP '.$apiExternalKey
                ];
                $res = $client->request('POST', $apiExternalUrl . '/journal-vouchers/webhook', [
                    'headers' => $headers,
                    'json' => [
                        'data' => $responseData
                    ]
                ]);
                $json = $res->getBody();
            }

        } catch (\Exception $exception) {
            Log::error('Error');
            Log::error($exception->getMessage());
            Log::error('File: ' . $exception->getFile() . ' at line ' . $exception->getLine());
        }
    }

    function createJournalVoucher($masterData, $detailsData, $compId)
    {
        $masterInsert = JournalVoucherService::createJournalVoucher($masterData);
        if($masterInsert['status']) {
            $jvMasterAutoId = $masterInsert['data']['jvMasterAutoId'];
            foreach ($detailsData as $jvDetail) {
                $jvDetail['jvMasterAutoId'] = $jvMasterAutoId;
                $detailInsert = JournalVoucherService::createJournalVoucherDetail($jvDetail);
                if (!$detailInsert['status']) {
                    return [
                        'status' => false,
                        'error' => $detailInsert['message']
                    ];
                }
            }

            $params = array(
                'autoID' => $masterInsert['data']['jvMasterAutoId'],
                'company' => $compId,
                'document' => $masterInsert['data']['documentSystemID'],
                'segment' => '',
                'category' => '',
                'amount' => '',
                'isAutoCreateDocument' => 1
            );
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return [
                    'status' => false,
                    'error' => $confirm["message"]
                ];
            } else {
                $autoApproveParams = DocumentAutoApproveService::getAutoApproveParams($masterInsert['data']['documentSystemID'],$masterInsert['data']['jvMasterAutoId']);
                $autoApproveParams['db'] = $this->db;
                $autoApproveParams['supplierPrimaryCode'] = $masterInsert['data']['JVcode'];
                $approveDocument = Helper::approveDocument($autoApproveParams);
                if ($approveDocument["success"]) {
                    $jvId[] = $masterInsert['data']['jvMasterAutoId'];
                    $this->storeToDocumentSystemMapping(11,$jvId,$this->authorization);
                    return [
                        'status' => true,
                        'error' => 'Journal voucher created successfully!',
                        'jvCode' => $masterInsert['data']['JVcode']
                    ];
                } else {
                    return [
                        'status' => false,
                        'error' => $approveDocument['message']
                    ];
                }
            }
        } else {
            return [
                'status' => false,
                'error' => $masterInsert['message']
            ];
        }
    }
}
