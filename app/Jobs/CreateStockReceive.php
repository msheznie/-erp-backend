<?php

namespace App\Jobs;

use App\Models\CompanyFinanceYear;
use App\Models\DocumentMaster;
use App\Models\StockReceive;
use App\Models\StockReceiveDetails;
use App\Models\StockTransfer;
use App\Models\StockTransferDetails;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateStockReceive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $stMaster;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($stMaster)
    {
        $this->stMaster = $stMaster;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/create_stock_receive_jobs.log');
        $st = $this->stMaster;
        $stMaster = StockTransfer::where('stockTransferAutoID',$st->stockTransferAutoID)->first();
        if (!empty($stMaster)) {
            DB::beginTransaction();
            try {
                Log::info('Successfully start  stock_receive' . date('H:i:s'));
                // Log::info($stMaster);
                if ($stMaster->interCompanyTransferYN == -1) {

                    $lastSerial = StockReceive::where('companySystemID', $stMaster->companyToSystemID)
                        ->where('companyFinanceYearID', $stMaster->companyFinanceYearID)
                        ->orderBy('stockReceiveAutoID', 'desc')
                        ->first();

                    $lastSerialNumber = 1;
                    if ($lastSerial) {
                        $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                    }

                    $stockReceive = new StockReceive();
                    $stockReceive->documentSystemID = 10;
                    $documentMaster = DocumentMaster::where('documentSystemID', $stockReceive->documentSystemID)->first();
                    if ($documentMaster) {
                        $stockReceive->documentID = $documentMaster->documentID;
                    }

                    $stockReceive->companySystemID = $stMaster->companyToSystemID;
                    $stockReceive->companyID = $stMaster->companyTo;
                    $stockReceive->serviceLineSystemID = NULl;
                    $stockReceive->serviceLineCode = NULl;
                    $stockReceive->companyFinanceYearID = $stMaster->companyFinanceYearID;
                    $stockReceive->companyFinancePeriodID = $stMaster->companyFinanceYearID;
                    $stockReceive->FYBiggin = $stMaster->FYBiggin;
                    $stockReceive->FYEnd = $stMaster->FYEnd;
                    $stockReceive->serialNo = $lastSerialNumber;
                    $stockReceive->refNo = $stMaster->refNo;
                    $stockReceive->receivedDate = $stMaster->tranferDate;
                    $stockReceive->comment = $stMaster->comment;
                    $stockReceive->companyFromSystemID = $stMaster->companyFromSystemID;
                    $stockReceive->companyFrom = $stMaster->companyFrom;
                    $stockReceive->companyToSystemID = $stMaster->companyToSystemID;
                    $stockReceive->companyTo = $stMaster->companyTo;
                    $stockReceive->locationTo = $stMaster->locationTo;
                    $stockReceive->locationFrom = $stMaster->locationFrom;
                    $stockReceive->confirmedYN = 0;
                    $stockReceive->approved = 0;
                    $stockReceive->interCompanyTransferYN = $stMaster->interCompanyTransferYN;
                    $stockReceive->RollLevForApp_curr = 1;
                    $stockReceive->createdDateTime = $stMaster->createdDateTime;
                    $stockReceive->createdUserGroup = $stMaster->createdUserGroup;
                    $stockReceive->createdPCID = $stMaster->createdPCID;
                    $stockReceive->createdUserSystemID = $stMaster->createdUserSystemID;
                    $stockReceive->createdUserID = $stMaster->createdUserID;

                    $companyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $stMaster->companyFinanceYearID)
                        ->where('companySystemID', $stMaster->companyToSystemID)
                        ->first();

                    if ($companyFinanceYear) {
                        $startYear = $companyFinanceYear['bigginingDate'];
                        $finYearExp = explode('-', $startYear);
                        $finYear = $finYearExp[0];
                    } else {
                        $finYear = date("Y");
                    }

                    $stockTransferCode = ($stockReceive->companyID . '\\' . $finYear . '\\' . $stockReceive->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                    $stockReceive->stockReceiveCode = $stockTransferCode;
                    $stockReceive->save();
                    Log::info($stockReceive);

                    $stDetails = StockTransferDetails::where("stockTransferAutoID", $stMaster->stockTransferAutoID)->get();

                    foreach ($stDetails as $new) {

                        $item = array();
                        $item['stockReceiveAutoID'] = $stockReceive->stockReceiveAutoID;
                        $item['stockReceiveCode'] = $stockReceive->stockReceiveCode;
                        $item['stockTransferAutoID'] = $stMaster->stockTransferAutoID;
                        $item['stockTransferCode'] = $stMaster->stockTransferCode;
                        $item['stockTransferDate'] = $stMaster->tranferDate;

                        $item['itemCodeSystem'] = $new['itemCodeSystem'];
                        $item['itemPrimaryCode'] = $new['itemPrimaryCode'];
                        $item['itemDescription'] = $new['itemDescription'];
                        $item['unitOfMeasure'] = $new['unitOfMeasure'];
                        $item['itemFinanceCategoryID'] = $new['itemFinanceCategoryID'];
                        $item['itemFinanceCategorySubID'] = $new['itemFinanceCategorySubID'];
                        $item['financeGLcodebBS'] = $new['financeGLcodebBS'];
                        $item['financeGLcodebBSSystemID'] = $new['financeGLcodebBSSystemID'];
                        $item['localCurrencyID'] = $new['localCurrencyID'];
                        $item['unitCostLocal'] = $new['unitCostLocal'] * 1.03;
                        $item['reportingCurrencyID'] = $new['reportingCurrencyID'];
                        $item['unitCostRpt'] = $new['unitCostRpt'] * 1.03;
                        $item['qty'] = $new['qty'];

                        if ($item['unitCostLocal'] <= 0 || $item['unitCostRpt'] <= 0) {
                            // return $this->sendError("Cost is not updated", 500);
                        } else {
                            $srdItem = StockReceiveDetails::insert($item);

                            $stDetail = StockTransferDetails::where('stockTransferDetailsID', $new['stockTransferDetailsID'])->first();
                            $stDetail->addedToRecieved = -1;
                            $stDetail->stockRecieved = -1;
                            $stDetail->save();
                        }

                    }

                    $stMaster->fullyReceived = -1;
                    $stMaster->save();
                    Log::info('Successfully created  stock_receive' . date('H:i:s'));
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
            }
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
