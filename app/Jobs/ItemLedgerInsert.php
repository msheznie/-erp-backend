<?php

namespace App\Jobs;

use App\Models\ErpItemLedger;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Auth;
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
                return ['success' => false, 'message' => 'Parameter document id is missing'];
            }
            //DB::beginTransaction();
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
                            'transactionDate' => 'grvDate',
                            'referenceNumber' => '');

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
                            'comments' => '',
                            'fromDamagedTransactionYN' => 0);

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
                            'transactionDate' => 'issueDate',
                            'referenceNumber' => '');

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
                            'comments' => 'comments',
                            'fromDamagedTransactionYN' => 0);

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
                            'transactionDate' => 'ReturnDate',
                            'referenceNumber' => '');

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
                            'comments' => 'comments',
                            'fromDamagedTransactionYN' => 0);

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
                            'transactionDate' => 'tranferDate',
                            'referenceNumber' => '');

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
                            'comments' => 'comments',
                            'fromDamagedTransactionYN' => 0);
                        break;
                    default:
                        return ['success' => false, 'message' => 'Document ID not found'];
                }
                $nameSpacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
                $masterRec = $nameSpacedModel::with([$docInforArr["childRelation"]])->where($docInforArr["approvedColumnName"],$docInforArr["approvedYN"])->find($masterModel["autoID"]);
                if ($masterRec) {
                    if ($masterRec[$docInforArr["childRelation"]]) {
                        $data = [];
                        $i = 0;
                        foreach ($masterRec[$docInforArr["childRelation"]] as $detail) {
                            foreach ($detailColumnArray as $column => $value) {
                                if($value == 'inOutQty') {
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
                            $i++;
                        }
                        Log::info($data);
                        //ErpItemLedger::insert($data);
                    }
                }

                Log::info('location: item ledger Add' . date('H:i:s'));

            } catch (\Exception $e) {
                // DB::rollback();
                return ['success' => false, 'message' => $e . 'Error Occurred'];
            }
        } else {
            Log::info('location: Not exist' . date('H:i:s'));
            return ['success' => false, 'message' => 'Error'];
        }

    }
}
