<?php

namespace App\Jobs;

use Exception;
use App\Models\ErpItemLedger;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemLedgerInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $masterModel;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($masterModel)
    {
        $this->masterModel = $masterModel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    /**
     * A common function to inster entry to item ledger
     * @param $params : accept parameters as an object
     * $param 1-documentSystemID : document id
     * no return values
     */
    public function handle()
    {
        $masterModel = $this->masterModel;
        if (!empty($masterModel)) {
            if (!isset($masterModel['documentSystemID'])) {
                Log::warning('Parameter document id is missing' . date('H:i:s'));
            }
            DB::beginTransaction();
            try {
                $docInforArr = array(
                    'confirmColumnName' => '',
                    'approvedColumnName' => '',
                    'modelName' => '',
                    'childRelation' => '',
                    'autoID' => ''
                );

                $detailColumnArray = array();
                $masterColumnArray = array();

                $empID = \Helper::getEmployeeInfo();
                switch ($masterModel["documentSystemID"]) { // check the document id and set relevant parameters
                    case 3: // GRV
                        $docInforArr["approvedColumnName"] = 'approved';
                        $docInforArr["modelName"] = 'GRVMaster';
                        $docInforArr["childRelation"] = 'details';
                        $docInforArr["autoID"] = 'grvAutoID';
                        $docInforArr["approvedYN"] = -1;
                        $masterColumnArray = array(
                            'companySystemID' => 'companySystemID',
                            'companyID' => 'companyID',
                            'serviceLineSystemID' => 'serviceLineSystemID',
                            'serviceLineCode' => 'serviceLineCode',
                            'documentSystemID' => 'documentSystemID',
                            'documentID' => 'documentID',
                            'documentCode' => 'grvPrimaryCode',
                            'wareHouseSystemCode' => 'grvLocation',
                            'referenceNumber' => 'grvDoRefNo');

                        $detailColumnArray = array(
                            'itemSystemCode' => 'itemCode',
                            'itemPrimaryCode' => 'itemPrimaryCode',
                            'itemDescription' => 'itemDescription',
                            'unitOfMeasure' => 'unitOfMeasure',
                            'inOutQty' => 'noQty',
                            'wacLocalCurrencyID' => 'localCurrencyID',
                            'wacLocal' => 'GRVcostPerUnitLocalCur',
                            'wacRptCurrencyID' => 'companyReportingCurrencyID',
                            'wacRpt' => 'GRVcostPerUnitComRptCur',
                            'comments' => 'comment');

                        break;
                    case 8: // Material Issue
                        $docInforArr["approvedColumnName"] = 'approved';
                        $docInforArr["modelName"] = 'ItemIssueMaster';
                        $docInforArr["childRelation"] = 'details';
                        $docInforArr["autoID"] = 'itemIssueAutoID';
                        $docInforArr["approvedYN"] = -1;
                        $masterColumnArray = array(
                            'companySystemID' => 'companySystemID',
                            'companyID' => 'companyID',
                            'serviceLineSystemID' => 'serviceLineSystemID',
                            'serviceLineCode' => 'serviceLineCode',
                            'documentSystemID' => 'documentSystemID',
                            'documentID' => 'documentID',
                            'documentCode' => 'itemIssueCode',
                            'wareHouseSystemCode' => 'wareHouseFrom',
                            'referenceNumber' => 'issueRefNo');

                        $detailColumnArray = array(
                            'itemSystemCode' => 'itemCodeSystem',
                            'itemPrimaryCode' => 'itemPrimaryCode',
                            'itemDescription' => 'itemDescription',
                            'unitOfMeasure' => 'itemUnitOfMeasure',
                            'inOutQty' => 'qtyIssued',
                            'wacLocalCurrencyID' => 'localCurrencyID',
                            'wacLocal' => 'issueCostLocal',
                            'wacRptCurrencyID' => 'reportingCurrencyID',
                            'wacRpt' => 'issueCostRpt',
                            'comments' => 'comments');

                        break;
                    case 12: //Material Return
                        $docInforArr["approvedColumnName"] = 'approved';
                        $docInforArr["modelName"] = 'ItemReturnMaster';
                        $docInforArr["childRelation"] = 'details';
                        $docInforArr["autoID"] = 'itemReturnAutoID';
                        $docInforArr["approvedYN"] = -1;
                        $masterColumnArray = array(
                            'companySystemID' => 'companySystemID',
                            'companyID' => 'companyID',
                            'serviceLineSystemID' => 'serviceLineSystemID',
                            'serviceLineCode' => 'serviceLineCode',
                            'documentSystemID' => 'documentSystemID',
                            'documentID' => 'documentID',
                            'documentCode' => 'itemReturnCode',
                            'wareHouseSystemCode' => 'wareHouseLocation',
                            'referenceNumber' => 'ReturnRefNo');

                        $detailColumnArray = array(
                            'itemSystemCode' => 'itemCodeSystem',
                            'itemPrimaryCode' => 'itemPrimaryCode',
                            'itemDescription' => 'itemDescription',
                            'unitOfMeasure' => 'itemUnitOfMeasure',
                            'inOutQty' => 'qtyIssued',
                            'wacLocalCurrencyID' => 'localCurrencyID',
                            'wacLocal' => 'unitCostLocal',
                            'wacRptCurrencyID' => 'reportingCurrencyID',
                            'wacRpt' => 'unitCostRpt',
                            'comments' => 'comments');

                        break;
                    case 13: //Stock Transfer
                        $docInforArr["approvedColumnName"] = 'approved';
                        $docInforArr["modelName"] = 'StockTransfer';
                        $docInforArr["childRelation"] = 'details';
                        $docInforArr["autoID"] = 'stockTransferAutoID';
                        $docInforArr["approvedYN"] = -1;
                        $masterColumnArray = array(
                            'companySystemID' => 'companySystemID',
                            'companyID' => 'companyID',
                            'serviceLineSystemID' => 'serviceLineSystemID',
                            'serviceLineCode' => 'serviceLineCode',
                            'documentSystemID' => 'documentSystemID',
                            'documentID' => 'documentID',
                            'documentCode' => 'stockTransferCode',
                            'wareHouseSystemCode' => 'locationFrom',
                            'referenceNumber' => 'refNo');

                        $detailColumnArray = array(
                            'itemSystemCode' => 'itemCodeSystem',
                            'itemPrimaryCode' => 'itemPrimaryCode',
                            'itemDescription' => 'itemDescription',
                            'unitOfMeasure' => 'unitOfMeasure',
                            'inOutQty' => 'qty',
                            'wacLocalCurrencyID' => 'localCurrencyID',
                            'wacLocal' => 'unitCostLocal',
                            'wacRptCurrencyID' => 'reportingCurrencyID',
                            'wacRpt' => 'unitCostRpt',
                            'comments' => 'comments');
                        break;
                    default:
                        Log::error('Document ID Not Found' . date('H:i:s'));
                        exit;
                        break;
                }
                $nameSpacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
                $masterRec = $nameSpacedModel::with([$docInforArr["childRelation"] => function($query) use ($masterModel) {
                    if($masterModel["documentSystemID"] == 3){
                        $query->where('itemFinanceCategoryID',1);
                    }
                }])->where($docInforArr["approvedColumnName"],$docInforArr["approvedYN"])->find($masterModel["autoID"]);
                if ($masterRec) {
                    if ($masterRec[$docInforArr["childRelation"]]) {
                        $data = [];
                        $i = 0;
                        foreach ($masterRec[$docInforArr["childRelation"]] as $detail) {
                            foreach ($detailColumnArray as $column => $value) {
                                if($column == 'inOutQty') {
                                    if ($masterModel["documentSystemID"] == 3 || $masterModel["documentSystemID"] == 12) {
                                        $data[$i][$column] = ABS($detail[$value]); // make qty always plus
                                    }else if ($masterModel["documentSystemID"] == 8 || $masterModel["documentSystemID"] == 13){
                                        $data[$i][$column] = ABS($detail[$value]) * -1; // make qty always minus
                                    }else{
                                        $data[$i][$column] = $detail[$value];
                                    }
                                }else{
                                    $data[$i][$column] = $detail[$value];
                                }
                            }
                            foreach ($masterColumnArray as $column => $value) {
                                $data[$i][$column] = $masterRec[$value];
                            }
                            $data[$i]['documentSystemCode'] = $masterModel["autoID"];
                            $data[$i]['createdUserSystemID'] = $empID->employeeSystemID;
                            $data[$i]['createdUserID'] = $empID->empID;
                            $data[$i]['fromDamagedTransactionYN'] = 0;
                            $data[$i]['transactionDate'] = date('Y-m-d H:i:s');
                            $data[$i]['timestamp'] = date('Y-m-d H:i:s');
                            $i++;
                        }
                        if($data){
                            Log::info($data);
                            $itemLedgerInsert = ErpItemLedger::insert($data);
                            $itemassignInsert = \App\Jobs\ItemAssignInsert::dispatch($masterModel)->onQueue('itemassign');
                        }

                    }
                }
                DB::commit();
                Log::info('Item successfully added to item ledger' . date('H:i:s'));

            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error occurred when adding item to item ledger' . date('H:i:s'));
            }
        } else {
            Log::error('Parameter not exist' . date('H:i:s'));
        }

    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
    }
}
