<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Http\Controllers\API\DocumentAttachmentsAPIController;
use App\Http\Controllers\API\JvDetailAPIController;
use App\Http\Controllers\API\JvMasterAPIController;
use App\Models\DocumentAttachments;
use App\Models\JvMaster;
use App\Models\RecurringVoucherSetupSchedule;
use App\Models\RecurringVoucherScheduleError;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RecurringVoucherSetupScheDet;

class CreateRecurringVoucherDocument implements ShouldQueue
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
     * Save error to recurring_voucher_schedule_error table
     *
     * @param int $rrvSetupScheduleAutoID
     * @param string $errorMessage
     * @return void
     */
    private function saveErrorToDatabase($rrvSetupScheduleAutoID, $errorMessage)
    {
        try {
            RecurringVoucherScheduleError::create([
                'rrvSetupScheduleAutoID' => $rrvSetupScheduleAutoID,
                'errorMessage' => $errorMessage
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to save error to database: {$e->getMessage()}");
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        CommonJobService::db_switch($this->tenantDb);


        $today = explode(' ',Carbon::now())[0];

        $recurringVoucherSchedules = RecurringVoucherSetupSchedule::where('stopYN', 0)
            ->where('rrvGeneratedYN', 0)
            ->whereDate('processDate', $today)
            ->orderBy('rrvSetupScheduleAutoID','asc')
            ->with('master')->get();

        foreach ($recurringVoucherSchedules as $rrvSchedule){
            switch ($rrvSchedule->master->documentType){
                case 0: // Recurring JV Type
                    try{
                        // Approve state documents
                        if($rrvSchedule->master->documentStatus == 2){

                            // start the process for schedule
                            $rrvSchedule->update([
                                'isInProccess' => 1
                            ]);

                            DB::beginTransaction();

                            // Convert RRV schedule to JV Document
                            $request = new Request();
                            $request->replace([
                                'companySystemID' => $rrvSchedule->master->companySystemID,
                                'companyFinanceYearID' => $rrvSchedule->companyFinanceYearID,
                                'companyFinancePeriodID' => $rrvSchedule->companyFinancePeriodID,
                                'JVNarration' => $rrvSchedule->master->narration.' '.$rrvSchedule->master->RRVcode,
                                'currencyID' => $rrvSchedule->master->currencyID,
                                'jvType' => 2,
                                'isRelatedPartyYN' => $rrvSchedule->master->isRelatedPartyYN,
                                'JVdate' => $rrvSchedule->processDate,
                                'isAutoCreateDocument' => true
                            ]);
                            $controller = app(JvMasterAPIController::class);
                            $jvMasterReturnData = $controller->store($request);

                            if($jvMasterReturnData['success']){

                                // Convert RRV details to JV details
                                $jvDetailsCreateState = false;
                                $rrvDetails = RecurringVoucherSetupScheDet::where('recurringVoucherSheduleAutoId',$rrvSchedule->rrvSetupScheduleAutoID)
                                                                            ->where('recurringVoucherAutoId',$rrvSchedule->master->recurringVoucherAutoId)
                                                                            ->where('companySystemID',$rrvSchedule->master->companySystemID)
                                                                            ->get();

                                foreach ($rrvDetails as $rrvDetail){
                                    $dataset = [
                                        'jvMasterAutoId' => $jvMasterReturnData['data']['jvMasterAutoId'],
                                        'chartOfAccountSystemID' => $rrvDetail->chartOfAccountSystemID,
                                        'isAutoCreateDocument' => true
                                    ];

                                    $request = new Request();
                                    $request->replace($dataset);
                                    $controller = app(JvDetailAPIController::class);
                                    $jvMasterDetailStoreReturnData = $controller->store($request);

                                    if($jvMasterDetailStoreReturnData['success']) {
                                        $dataset = [
                                            'jvMasterAutoId' => $jvMasterReturnData['data']['jvMasterAutoId'],
                                            'debitAmount' => $rrvDetail->debitAmount,
                                            'creditAmount' => $rrvDetail->creditAmount,
                                            'serviceLineSystemID' => $rrvDetail->serviceLineSystemID,
                                            'contractUID' => $rrvDetail->contractUID,
                                            'detail_project_id' => $rrvDetail->detailProjectID,
                                            'isAutoCreateDocument' => true
                                        ];

                                        $request = new Request();
                                        $request->replace($dataset);
                                        $jvMasterDetailUpdateReturnData = $controller->update($jvMasterDetailStoreReturnData['data']['jvDetailAutoID'],$request);

                                        if($jvMasterDetailUpdateReturnData['success']){
                                            $jvDetailsCreateState = true;
                                        }
                                        else{
                                            $jvDetailsCreateState = false;
                                            Log::channel('recurring_voucher_service')->error("Recurring Voucher Schedule ID (JV details update error) :- {$rrvSchedule->rrvSetupScheduleAutoID} {$jvMasterDetailUpdateReturnData['message']}");
                                            $this->saveErrorToDatabase($rrvSchedule->rrvSetupScheduleAutoID, "JV details update error: {$jvMasterDetailUpdateReturnData['message']}");
                                            break 2;
                                        }
                                    }
                                    else{
                                        $jvDetailsCreateState = false;
                                        Log::channel('recurring_voucher_service')->error("Recurring Voucher Schedule ID (JV details store error) :- {$rrvSchedule->rrvSetupScheduleAutoID} {$jvMasterDetailStoreReturnData['message']}");
                                        $this->saveErrorToDatabase($rrvSchedule->rrvSetupScheduleAutoID, "JV details store error: {$jvMasterDetailStoreReturnData['message']}");
                                        break 2;
                                    }
                                }

                                // Attach Attachments
                                if($jvDetailsCreateState){
                                    $documentAttachments = DocumentAttachments::where('companySystemID',$rrvSchedule->master->companySystemID)
                                        ->where('documentSystemID',$rrvSchedule->master->documentSystemID)
                                        ->where('documentSystemCode',$rrvSchedule->master->recurringVoucherAutoId)
                                        ->get();

                                    $jvAttachmentsUpdateState = !(count($documentAttachments) > 0);

                                    foreach ($documentAttachments as $documentAttachment){
                                        $dataset = $documentAttachment->toArray();
                                        $tempType = explode('.',$dataset['myFileName']);
                                        $dataset['fileType'] = end($tempType);
                                        $dataset['documentSystemID'] = 17;
                                        $dataset['documentSystemCode'] = $jvMasterReturnData['data']['jvMasterAutoId'];
                                        $dataset['isAutoCreateDocument'] = true;
                                        unset($dataset['attachmentID'], $dataset['timeStamp']);

                                        $request = new Request();
                                        $request->replace($dataset);
                                        $controller = app(DocumentAttachmentsAPIController::class);
                                        $jvAttachmentReturnData = $controller->store($request);
                                        if($jvAttachmentReturnData['success']){
                                            $jvAttachmentsUpdateState = true;
                                        }
                                        else{
                                            $jvAttachmentsUpdateState = false;
                                            Log::channel('recurring_voucher_service')->error("Recurring Voucher Schedule ID (attachment update error) :- {$rrvSchedule->rrvSetupScheduleAutoID} {$jvAttachmentReturnData['message']}");
                                            $this->saveErrorToDatabase($rrvSchedule->rrvSetupScheduleAutoID, "attachment update error: {$jvAttachmentReturnData['message']}");
                                            break 2;
                                        }
                                    }

                                    // Document Confirm
                                    if ($jvAttachmentsUpdateState){
                                        $dataset = $jvMasterReturnData['data'];
                                        $dataset['confirmedYN'] = 1;
                                        $dataset['isAutoCreateDocument'] = true;

                                        $request = new Request();
                                        $request->replace($dataset);
                                        $controller = app(JvMasterAPIController::class);
                                        $jvConfirmReturnData = $controller->update($jvMasterReturnData['data']['jvMasterAutoId'],$request);

                                        if($jvConfirmReturnData['success']){

                                            // Get approve document
                                            $request = new Request();
                                            $request->replace([
                                                'companyId' => $jvConfirmReturnData['data']['companySystemID'],
                                                'jvMasterAutoId' => $jvConfirmReturnData['data']['jvMasterAutoId'],
                                                'isAutoCreateDocument' => true
                                            ]);
                                            $controller = app(JvMasterAPIController::class);
                                            $jvApproveDocumentReturnData = $controller->getJournalVoucherMasterApproval($request);
                                            $jvApproveDocumentReturnData = json_decode(json_encode($jvApproveDocumentReturnData),true);

                                            // Approval pre check
                                            if($jvApproveDocumentReturnData['success']){

                                                $dataset = $jvApproveDocumentReturnData['data'];
                                                $dataset['isAutoCreateDocument'] = true;
                                                $request = new Request();
                                                $request->replace($dataset);
                                                $controller = app(JvMasterAPIController::class);
                                                $jvApprovePreCheckReturnData = $controller->approvalPreCheckJV($request);

                                                // Approve Document
                                                if($jvApprovePreCheckReturnData['success']){
                                                    $dataset['db'] = $this->tenantDb;
                                                    $request = new Request();
                                                    $request->replace($dataset);
                                                    $request->merge(['approvedComments' => '']);
                                                    $controller = app(JvMasterAPIController::class);
                                                    $jvApproveReturnData = $controller->approveJournalVoucher($request);

                                                    if($jvApproveReturnData['success']){
                                                        // Update other table fields
                                                        $rrvSchedule->update([
                                                            'generateDocumentID' => $jvConfirmReturnData['data']['jvMasterAutoId'],
                                                            'rrvGeneratedYN' => 1,
                                                            'isInProccess' => 0
                                                        ]);
                                                        JvMaster::find($jvConfirmReturnData['data']['jvMasterAutoId'])->update(['isAutoApprove' => 1]);
                                                        DB::commit();
                                                    }
                                                    else{
                                                        DB::rollBack();
                                                        Log::channel('recurring_voucher_service')->error("Recurring Voucher Schedule ID (Approval error) :- {$rrvSchedule->rrvSetupScheduleAutoID} {$jvApproveReturnData['message']}");
                                                        $this->saveErrorToDatabase($rrvSchedule->rrvSetupScheduleAutoID, "Approval error: {$jvApproveReturnData['message']}");
                                                    }
                                                }
                                                else{
                                                    Log::channel('recurring_voucher_service')->error("Recurring Voucher Schedule ID (Approval JV pre check error) :- {$rrvSchedule->rrvSetupScheduleAutoID} {$jvApprovePreCheckReturnData['message']}");
                                                    $this->saveErrorToDatabase($rrvSchedule->rrvSetupScheduleAutoID, "Approval JV pre check error: {$jvApprovePreCheckReturnData['message']}");
                                                }
                                            }
                                            else{
                                                Log::channel('recurring_voucher_service')->error("Recurring Voucher Schedule ID (Approval JV get error) :- {$rrvSchedule->rrvSetupScheduleAutoID} {$jvApproveDocumentReturnData['message']}");
                                                $this->saveErrorToDatabase($rrvSchedule->rrvSetupScheduleAutoID, "Approval JV get error: {$jvApproveDocumentReturnData['message']}");
                                            }

                                        }
                                        else{
                                            DB::rollBack();
                                            Log::channel('recurring_voucher_service')->error("Recurring Voucher Schedule ID (JV confirm error) :- {$rrvSchedule->rrvSetupScheduleAutoID} {$jvConfirmReturnData['message']}");
                                            $this->saveErrorToDatabase($rrvSchedule->rrvSetupScheduleAutoID, "JV confirm error: {$jvConfirmReturnData['message']}");
                                        }
                                    }
                                    else{
                                        Log::channel('recurring_voucher_service')->error("Recurring Voucher Schedule ID (attachment update error) :- {$rrvSchedule->rrvSetupScheduleAutoID}");
                                        $this->saveErrorToDatabase($rrvSchedule->rrvSetupScheduleAutoID, "attachment update error");
                                        DB::rollBack();
                                    }
                                }
                                else{
                                    Log::channel('recurring_voucher_service')->error("Recurring Voucher Schedule ID (jv details create error) :- {$rrvSchedule->rrvSetupScheduleAutoID}");
                                    $this->saveErrorToDatabase($rrvSchedule->rrvSetupScheduleAutoID, "jv details create error");
                                    DB::rollBack();
                                }
                            }
                            else{
                                Log::channel('recurring_voucher_service')->error("Recurring Voucher Schedule ID (JV create error) :- {$rrvSchedule->rrvSetupScheduleAutoID} {$jvMasterReturnData['message']}");
                                $this->saveErrorToDatabase($rrvSchedule->rrvSetupScheduleAutoID, "JV create error: {$jvMasterReturnData['message']}");
                            }
                        }
                    } catch (\Exception $e){
                        DB::rollBack();
                        Log::channel('recurring_voucher_service')->error("Recurring Voucher Schedule ID (catch error) :- {$rrvSchedule->rrvSetupScheduleAutoID} {$e->getMessage()}");
                        $this->saveErrorToDatabase($rrvSchedule->rrvSetupScheduleAutoID, "catch error: {$e->getMessage()}");
                    }
                    break;
            }
        }
    }
}
