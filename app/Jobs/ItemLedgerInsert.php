<?php

namespace App\Jobs;

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
                    case 3:
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
                            'fromDamagedTransactionYN' => 0,
                            'createdUserSystemID' => Auth::id(),
                            'createdUserID' => $empID['employeeID']);

                        break;
                    case 8:
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
                            'fromDamagedTransactionYN' => 0,
                            'createdUserSystemID' => Auth::id(),
                            'createdUserID' => $empID['employeeID']);

                        break;
                    case 12:
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
                            'fromDamagedTransactionYN' => 0,
                            'createdUserSystemID' => Auth::id(),
                            'createdUserID' => $empID['employeeID']);

                        break;
                    default:
                        return ['success' => false, 'message' => 'Document ID not found'];
                }

                $nameSpacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
                $masterRec = $nameSpacedModel::with([$docInforArr["childRelation"]])->find($masterModel[$docInforArr["autoID"]])->where($docInforArr["approvedColumnName"],$docInforArr["approvedYN"]);
                if ($masterRec) {
                    if ($masterRec[$docInforArr["childRelation"]]) {
                        $data = [];
                        foreach ($masterRec[$docInforArr["childRelation"]] as $detail) {
                            foreach ($detailColumnArray as $column => $value) {
                                $data[$column] = $detail[$value];
                            }
                            foreach ($masterColumnArray as $column => $value) {
                                $data[$column] = $masterRec[$value];
                            }
                            $data['documentSystemCode'] = $masterModel[$docInforArr["autoID"]];
                        }
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
