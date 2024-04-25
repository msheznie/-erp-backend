<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\JvMasterAPIController;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\GeneralLedger;
use App\Models\JvDetail;
use App\Services\UserTypeService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\JvMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReverseEntryToPOAccrualJV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:reversePoAccrual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command reverse po accrual entry';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currentDateAndTime = Carbon::now();

        $jvMaster = JvMaster::where('reversalDate',$currentDateAndTime)->where('confirmedYN',1)->where('approved',-1)->first();


        if($jvMaster)
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

                $jvInsertData['createdPcID'] = gethostname();
                $jvInsertData['modifiedPc'] = gethostname();
                $jvInsertData['timestamp'] = Carbon::now();
                $jvInsertData['createdDateTime'] = Carbon::now();
                $jvInsertData['postedDate'] = null;
                $jvInsertData['createdUserID'] = $jvMaster->createdUserID;
                $jvInsertData['modifiedUser'] = $jvMaster->modifiedUser;
                $jvInsertData['createdUserSystemID'] = $jvMaster->createdUserSystemID;
                $jvInsertData['modifiedUserSystemID'] = $jvMaster->modifiedUserSystemID;

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

                $empInfo = UserTypeService::getSystemEmployee();
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

                try {
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
                        'rollLevelOrder' => $documentApporved->rollLevelOrder
                    ];

                    $approval = \Helper::approveDocument($data);

                } catch (\Exception $exception) {
                    Log::error($exception->getMessage());
                }
            }


        }

    }
}
