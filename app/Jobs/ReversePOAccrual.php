<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Http\Controllers\API\DocumentAttachmentsAPIController;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentApproved;
use App\Models\DocumentAttachments;
use App\Models\DocumentMaster;
use App\Models\JvDetail;
use App\Models\JvMaster;
use App\Services\UserTypeService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ReversePOAccrual implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tenantDb;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }
            else{
                self::onConnection('database');
            }
        }
        else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }

        $this->tenantDb = $db;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        CommonJobService::db_switch($this->tenantDb);

        $currentDateAndTime = Carbon::today()->toDateString();
        $jvMasters = JvMaster::whereDate('reversalDate',$currentDateAndTime)->where('isReverseAccYN',0)->where('confirmedYN',1)->where('approved',-1)->where('stopReversalJV', 0)->get();


        if($jvMasters)
        {
            foreach ($jvMasters as $jvMaster)
            {
                if (($jvMaster->isReverseAccYN == 0) && isset($jvMaster->reversalDate))
                {
                    $companyFinanceYear = CompanyFinanceYear::where('companySystemID', $jvMaster->companySystemID)
                        ->where('isActive', -1)
                        ->where('isCurrent', -1)
                        ->first();

                    if (!$companyFinanceYear) {
                        Log::error("Financial year not created or not active -".$currentDateAndTime);
                    }

                    if(!($companyFinanceYear->bigginingDate <= $jvMaster->reversalDate) && ($jvMaster->reversalDate <= $companyFinanceYear->endingDate))
                    {
                        Log::error("reversal date is not within finanical year -".$currentDateAndTime);
                    }


                    $companyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $jvMaster->companySystemID)
                        ->where('isActive', -1)
                        ->where('isCurrent', -1)
                        ->where('departmentSystemID', 5)
                        ->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)
                        ->first();

                    if (!$companyFinancePeriod) {
                        Log::error("Financial period not created or not active -".$currentDateAndTime);
                    }

                    $jvInsertData = $jvMaster->toArray();

                    $jvInsertData['companyFinanceYearID'] = $companyFinanceYear->companyFinanceYearID;
                    $jvInsertData['companyFinancePeriodID'] = $companyFinancePeriod->companyFinancePeriodID;
                    $jvInsertData['FYBiggin'] = $companyFinanceYear->bigginingDate;
                    $jvInsertData['FYEnd'] = $companyFinanceYear->endingDate;
                    $jvInsertData['JVdate'] =$jvMaster->reversalDate;
                    $jvInsertData['reversalDate'] = $jvInsertData['JVdate'];

                    $jvInsertData['FYPeriodDateFrom'] = $companyFinancePeriod->dateFrom;
                    $jvInsertData['FYPeriodDateTo'] = $companyFinancePeriod->dateTo;

                    $documentDate = $jvInsertData['JVdate'];
                    $monthBegin = $jvInsertData['FYPeriodDateFrom'];
                    $monthEnd = $jvInsertData['FYPeriodDateTo'];

                    if (($documentDate < $monthBegin) || ($documentDate > $monthEnd)) {
                        Log::error('reversal date is not within the financial period!, you cannot copy JV');
                    }

                    $empInfo = UserTypeService::getSystemEmployee();

                    $jvInsertData['createdPcID'] = gethostname();
                    $jvInsertData['modifiedPc'] = gethostname();
                    $jvInsertData['timestamp'] = Carbon::now();
                    $jvInsertData['createdDateTime'] = Carbon::now();
                    $jvInsertData['postedDate'] = null;
                    $jvInsertData['createdUserID'] = $empInfo->empID;
                    $jvInsertData['modifiedUser'] = $empInfo->empID;
                    $jvInsertData['createdUserSystemID'] = $empInfo->employeeSystemID;
                    $jvInsertData['modifiedUserSystemID'] = $empInfo->employeeSystemID;

                    $lastSerial = JvMaster::where('companySystemID', $jvMaster->companySystemID)
                        ->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)
                        ->orderBy('serialNo', 'desc')
                        ->first();

                    $lastSerialNumber = 1;
                    if ($lastSerial) {
                        $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                    }


                    $jvInsertData['serialNo'] = $lastSerialNumber;

                    $documentMaster = DocumentMaster::where('documentSystemID', $jvInsertData['documentSystemID'])->first();

                    if ($companyFinanceYear) {
                        $startYear = $companyFinanceYear->bigginingDate;
                        $finYearExp = explode('-', $startYear);
                        $finYear = $finYearExp[0];
                    } else {
                        $finYear = date("Y");
                    }

                    $company = Company::where('companySystemID', $jvMaster->companySystemID)->first();

                    $oldCode = $jvInsertData['JVcode'];

                    if ($documentMaster) {
                        $jvCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                        $jvInsertData['JVcode'] = $jvCode;
                    }

                    $jvInsertData['isReverseAccYN'] = -1;
                    $jvInsertData['approvedYN'] = 0;
                    $jvInsertData['confirmedYN'] = 0;
                    $jvInsertData['confirmedByEmpID'] = null;
                    $jvInsertData['confirmedByEmpSystemID'] = null;
                    $jvInsertData['confirmedByName'] = null;
                    $jvInsertData['confirmedDate'] = null;
                    $jvInsertData['approvedByUserID'] = null;
                    $jvInsertData['approvedByUserSystemID'] = null;
                    $jvInsertData['approvedDate'] = null;
                    $jvInsertData['RollLevForApp_curr'] = 1;

                    $jvInsertData['JVNarration'] = ($jvInsertData['JVNarration'] == " " || $jvInsertData['JVNarration'] == null) ? "Reversal JV for ". $oldCode : $jvInsertData['JVNarration']. " - Reversal JV for ". $oldCode;

                    $jvMasterRes = JvMaster::create($jvInsertData);
                    $fetchJVDetail = JvDetail::where('jvMasterAutoId', $jvMaster->jvMasterAutoId)
                        ->get()
                        ->toArray();

                    foreach ($fetchJVDetail as $key => $value) {
                        $value['jvMasterAutoId'] = $jvMasterRes->jvMasterAutoId;


                        $debitAmount = $value['debitAmount'];
                        $creditAmount = $value['creditAmount'];
                        $value['debitAmount'] = $creditAmount;
                        $value['creditAmount'] = $debitAmount;


                        $value['createdDateTime'] = Carbon::now();
                        $value['timeStamp'] = Carbon::now();
                        $value['createdUserID'] = $jvMaster->createdUserID;
                        $value['createdUserSystemID'] = $jvMaster->createdUserSystemID;
                        $value['createdPcID'] = gethostname();

                        JvDetail::create($value);


                    }
                    
                    if($jvMasterRes && $jvMaster->jvType == 0) {
                        $documentAttachments = DocumentAttachments::where('companySystemID', $jvMaster->companySystemID)
                            ->where('documentSystemID', $jvMaster->documentSystemID)
                            ->where('documentSystemCode', $jvMaster->jvMasterAutoId)
                            ->get();

                        foreach ($documentAttachments as $documentAttachment){
                            $dataset = $documentAttachment->toArray();
                            $tempType = explode('.',$dataset['myFileName']);
                            $dataset['fileType'] = end($tempType);
                            $dataset['documentSystemID'] = 17;
                            $dataset['documentSystemCode'] = $jvMasterRes->jvMasterAutoId;
                            $dataset['isAutoCreateDocument'] = true;
                            unset($dataset['attachmentID'], $dataset['timeStamp']);

                            $request = new Request();
                            $request->replace($dataset);
                            $controller = app(DocumentAttachmentsAPIController::class);
                            $controller->store($request);
                        }
                    }

                        //confirm document
                    $JvDetailDebitSum = JvDetail::where('jvMasterAutoId', $jvMasterRes->jvMasterAutoId)->get();

                    $params = array(
                        'autoID' => $jvMasterRes->jvMasterAutoId,
                        'company' => $jvMasterRes->companySystemID,
                        'document' => $jvMasterRes->documentSystemID,
                        'segment' => 0,
                        'category' => 0,
                        'amount' => $JvDetailDebitSum,
                        'isAutoCreateDocument' => true
                    );

                    $confirm = \Helper::confirmDocument($params);

                    $documentApporved = DocumentApproved::where('documentSystemCode',$jvMasterRes->jvMasterAutoId)->where('departmentSystemID',5)->first();
                    // approve document
                    $data = [
                        'approvedComments' => "System Approved",
                        'documentSystemCode' => $jvMasterRes->jvMasterAutoId,
                        'documentSystemID' => $jvMasterRes->documentSystemID,
                        'jvMasterAutoId' => $jvMasterRes->jvMasterAutoId,
                        'jvType' => $jvMasterRes->jvType,
                        'isCheckPrivilages' => false,
                        'approvedDate' => $jvMasterRes->approvedDate,
                        'isAutoCreateDocument' => true,
                        'documentApprovedID' => $documentApporved->documentApprovedID,
                        'approvalLevelID' => $documentApporved->approvalLevelID,
                        'rollLevelOrder' => $documentApporved->rollLevelOrder,
                        'db' => $this->tenantDb
                    ];

                    $approval = \Helper::approveDocument($data);

                    JvMaster::whereDate('reversalDate',$currentDateAndTime)->where('isReverseAccYN',0)->where('confirmedYN',1)->where('approved',-1)->where('stopReversalJV', 0)->update(['reversedYN' => 1]);
                }

            }


        }
    }
}
