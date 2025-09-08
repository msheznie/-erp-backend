<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\BookInvSuppDet;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\DeliveryOrderDetail;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\ItemIssueDetails;
use App\Models\PurchaseReturnDetails;
use App\Models\StockTransferDetails;
use App\Models\UnbilledGrvGroupBy;
use App\Models\FixedAssetMaster;
use Carbon\Carbon;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;
use Illuminate\Http\Request;

/**
 * Class GRVMasterRepository
 * @package App\Repositories
 * @version April 11, 2018, 12:12 pm UTC
 *
 * @method GRVMaster findWithoutFail($id, $columns = ['*'])
 * @method GRVMaster find($id, $columns = ['*'])
 * @method GRVMaster first($columns = ['*'])
*/
class GRVMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'grvType',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'companyAddress',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'documentSystemID',
        'documentID',
        'grvDate',
        'grvSerialNo',
        'grvPrimaryCode',
        'grvDoRefNo',
        'grvNarration',
        'grvLocation',
        'grvDOpersonName',
        'grvDOpersonResID',
        'grvDOpersonTelNo',
        'grvDOpersonVehicleNo',
        'supplierID',
        'supplierPrimaryCode',
        'supplierName',
        'supplierAddress',
        'supplierTelephone',
        'supplierFax',
        'supplierEmail',
        'liabilityAccountSysemID',
        'liabilityAccount',
        'UnbilledGRVAccountSystemID',
        'UnbilledGRVAccount',
        'localCurrencyID',
        'localCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierTransactionCurrencyID',
        'supplierTransactionER',
        'grvConfirmedYN',
        'grvConfirmedByEmpID',
        'grvConfirmedByName',
        'grvConfirmedDate',
        'grvCancelledYN',
        'grvCancelledBy',
        'grvCancelledByName',
        'grvCancelledDate',
        'grvTotalComRptCurrency',
        'grvTotalLocalCurrency',
        'grvTotalSupplierDefaultCurrency',
        'grvTotalSupplierTransactionCurrency',
        'grvDiscountPercentage',
        'grvDiscountAmount',
        'approved',
        'approvedDate',
        'timesReferred',
        'RollLevForApp_curr',
        'invoiceBeforeGRVYN',
        'deliveryConfirmedYN',
        'interCompanyTransferYN',
        'FromCompanyID',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return GRVMaster::class;
    }

    public function isGrvEligibleForCancellation($input, $type = null){

        $grvAutoID = $input['grvAutoID'];

        $grvMaster = GRVMaster::find($grvAutoID);
        $document = 'cancel';

        if ($type == "reversal") {
            $document = 'reverse';
        }

        if (empty($grvMaster)) {
            return [
                'status' => 0,
                'msg' => 'GRV not found'
            ];
        }

        if(!isset($input['companySystemID']))
        {
            return [
                'status' => 0,
                'msg' => 'companySystemID not found'
            ];
        }

        if ($grvMaster->approved != -1) {
            return [
                'status' => 0,
                'msg' => 'You cannot '.$document.', This document not approved.'
            ];
        }

        if ($grvMaster->grvCancelledYN == -1) {
            return [
                'status' => 0,
                'msg' => 'GRV already cancelled'
            ];
        }

        $grvDetails = GRVDetails::where('grvAutoID',$grvAutoID)->get();

        $inventoryItems = [];
        $otherItems = [];

        $itemList = [];

        // filter inventory items and other items
        foreach ($grvDetails as $grvDetail) {
            if($grvDetail->itemFinanceCategoryID == 1) {
                $inventoryItems[] = $grvDetail->itemCode;
            }
            else {
                $otherItems[] = $grvDetail->itemCode;
            }
            // create item object array
            $itemList[] = (object)['itemSystemCode' => $grvDetail->itemCode];
        }

        // purchase return
        $isPullPurchaseReturn = PurchaseReturnDetails::where('grvAutoID',$grvAutoID)->exists();
        if($isPullPurchaseReturn) {
            return [
                'status' => 0,
                'msg' => 'You cannot reverse the GRV. The GRV is already added to Supplier Invoice or purchase return'
            ];
        }
        else {
            // supplier invoice
            $isPullSupplierInvoice = BookInvSuppDet::where('grvAutoID',$grvAutoID)->exists();
            if($isPullSupplierInvoice) {
                return [
                    'status' => 0,
                    'msg' => 'You cannot reverse the GRV. The GRV is already added to Supplier Invoice or purchase return'
                ];
            }
        }

        // only inventory items or both inventory and other items
        if((!empty($inventoryItems) && empty($otherItems)) || (!empty($inventoryItems) && !empty($otherItems))) {
            $deliveryNote = DeliveryOrderDetail::whereHas('master', function ($query) use ($grvMaster) {
                $query->where('wareHouseSystemCode', $grvMaster->grvLocation);
                $query->where('createdDateTime', '>', $grvMaster->createdDateTime);
            })->whereIn('itemCodeSystem',$inventoryItems)->exists();

            $directItemInvoice = CustomerInvoiceItemDetails::whereHas('master', function ($query) use ($grvMaster) {
                $query->where('wareHouseSystemCode', $grvMaster->grvLocation);
                $query->where('createdDateAndTime', '>', $grvMaster->createdDateTime);
            })->whereIn('itemCodeSystem',$inventoryItems)->exists();

            $materialIssue = ItemIssueDetails::whereHas('master', function ($query) use ($grvMaster) {
                $query->where('wareHouseFrom', $grvMaster->grvLocation);
                $query->where('createdDateTime', '>', $grvMaster->createdDateTime);
            })->whereIn('itemCodeSystem',$inventoryItems)->exists();

            $stockTransferOut = StockTransferDetails::whereHas('master_by', function ($query) use ($grvMaster) {
                $query->where('locationFrom', $grvMaster->grvLocation);
                $query->where('createdDateTime', '>', $grvMaster->createdDateTime);
            })->whereIn('itemCodeSystem',$inventoryItems)->exists();

            if($deliveryNote || $directItemInvoice || $materialIssue || $stockTransferOut) {
                return [
                    'status' => 0,
                    'msg' => 'The Stock-Out Document Created for Selected GRV'
                ];
            }

            // document id list for item ledger
            $documentList = [71,3,61,20,8,24,10,7,97,11,87,12,13];

            // create document id object array
            $docs = array_map(function($item) {
                return (object) ['documentSystemID' => $item];
            }, $documentList);

            $itemLedgerInputData = [
                'companySystemID' => $input['companySystemID'],
                'reportID' => "SL",
                'reportType' => 1,
                'toDate' => Carbon::now()->format('Y-m-d'),
                'fromDate' => Carbon::now()->format('Y-m-d'),
                'Warehouse' => [(object)['wareHouseSystemCode' => $grvMaster->grvLocation]],
                'Items' => $itemList,
                'Docs' => $docs
            ];

            // get data from item ledger repository
            $itemLedgerOutputDataset = ErpItemLedgerRepository::getItemLedgerDetails($itemLedgerInputData, true);
            $itemLedgerOutputDataset = $itemLedgerOutputDataset['data'];

            // store invalid item data to return FE side
            $invalidItemData = [];

            // validate grv data with item ledger data set
            foreach ($grvDetails as $grvDetail) {
                try{
                    // check the item is in item ledger or not (inventory items only)
                    if (array_key_exists($grvDetail->itemPrimaryCode, $itemLedgerOutputDataset)) {
                        // get grv item data from item ledger output data set using key value
                        $itemLedgerData = $itemLedgerOutputDataset[$grvDetail->itemPrimaryCode];

                        $itemLedgerItemTotalQty = 0;
                        $itemLedgerItemTotalCost = 0;

                        // get total qty & cost of item in item ledger
                        foreach ($itemLedgerData as $value) {
                            $itemLedgerItemTotalQty += $value->inOutQty;
                            $itemLedgerItemTotalCost += $value->TotalWacLocal;
                        }

                        $grvItemQty = $grvDetail->noQty;
                        // convert net amount to item ledger currency format
                        $grvItemCost = (1 / $grvDetail->localCurrencyER) * ($grvDetail->noQty * $grvDetail->unitCost);

                        // change decimal places
                        $decimalPlaces = Helper::getCurrencyDecimalPlace($grvDetail->localCurrencyID);
                        $itemLedgerItemTotalCost = round($itemLedgerItemTotalCost, $decimalPlaces);
                        $grvItemCost = round($grvItemCost, $decimalPlaces);

                        // check if grv data is match or not with item ledger data using below conditions
                        $invalidState = false;
                        if(($itemLedgerItemTotalQty == $grvItemQty) && ($itemLedgerItemTotalCost > $grvItemCost)) {
                            $invalidState = true;
                        }
                        else if($itemLedgerItemTotalCost < $grvItemCost) {
                            $invalidState = true;
                        }
                        else if($itemLedgerItemTotalQty < $grvItemQty) {
                            $invalidState = true;
                        }

                        // if invalid state found then add to invalid item data
                        if($invalidState) {
                            $invalidItemData[] = [
                                'itemCode' => $grvDetail->itemPrimaryCode,
                                'itemDescription' => $grvDetail->itemDescription,
                                'grvQty' => $grvItemQty,
                                'grvValue' => $grvItemCost,
                                'itemLedgerQty' => $itemLedgerItemTotalQty,
                                'itemLedgerValue' => $itemLedgerItemTotalCost
                            ];
                        }
                    }
                }
                catch (\Exception $e) {
                    return [
                        'status' => 0,
                        'msg' => $e->getMessage()
                    ];
                }
            }

            // return invalid item data
            if (!empty($invalidItemData)) {
                return [
                    'status' => 0,
                    'msg' => 'You cannot reverse the GRV. Item not sufficient to reverse the GRV',
                    'data' => $invalidItemData,
                    'code' => 502
                ];
            }
        }

        // only other items or both inventory and other items
        if(empty($inventoryItems) && !empty($otherItems) || (!empty($inventoryItems) && !empty($otherItems))) {
            $checkInAllocation = FixedAssetMaster::where('docOriginDocumentSystemID', 3)->where('docOriginSystemCode', $input['grvAutoID'])->first();
            if ($checkInAllocation) {
                return [
                    'status' => 0,
                    'msg' => 'You cannot '.$document.' the GRV. The GRV is already added to Asset Allocation',
                ];
            }
        }

        return [
            'status' => 1,
            'msg' => 'success',
        ];
    }

    public function grvListQuery($request, $input, $search = '', $grvLocation, $serviceLineSystemID, $projectID) {

        $grvMaster = GRVMaster::where('companySystemID', $input['companyId']);
        $grvMaster->where('documentSystemID', $input['documentId']);
        $grvMaster->with(['local_currency_by','reporting_currency_by','created_by' => function ($query) {
            }, 'segment_by' => function ($query) {
            }, 'location_by' => function ($query) {
            }, 'supplier_by' => function ($query) {
            }, 'currency_by' => function ($query) {
            }, 'grvtype_by' => function ($query) {
            }, 'project' => function ($query) {
            }, 'evaluationMaster' => function ($query) {
                $query->where('documentCode', 'GRV');
        }]);


        if (array_key_exists('createdBy', $input)) {
            if($input['createdBy'] && !is_null($input['createdBy']))
            {
                $createdBy = collect($input['createdBy'])->pluck('id')->toArray();
                $grvMaster->whereIn('createdUserSystemID', $createdBy);
            }

        }
        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $grvMaster->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if (array_key_exists('projectID', $input)) {
            if ($input['projectID'] && !is_null($input['projectID'])) {
                $grvMaster->whereIn('projectID', $projectID);
            }
        }

        if (array_key_exists('grvLocation', $input)) {
            if ($input['grvLocation'] && !is_null($input['grvLocation'])) {
                $grvMaster->whereIn('grvLocation', $grvLocation);
            }
        }

        if (array_key_exists('grvCancelledYN', $input)) {
            if (($input['grvCancelledYN'] == 0 || $input['grvCancelledYN'] == -1) && !is_null($input['grvCancelledYN'])) {
                $grvMaster->where('grvCancelledYN', $input['grvCancelledYN']);
            }
        }

        if (array_key_exists('grvConfirmedYN', $input)) {
            if (($input['grvConfirmedYN'] == 0 || $input['grvConfirmedYN'] == 1) && !is_null($input['grvConfirmedYN'])) {
                $grvMaster->where('grvConfirmedYN', $input['grvConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $grvMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $grvMaster->whereMonth('grvDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $grvMaster->whereYear('grvDate', '=', $input['year']);
            }
        }

        if (array_key_exists('grvTypeID', $input)) {
            if ($input['grvTypeID'] && !is_null($input['grvTypeID'])) {
                $grvMaster->where('grvTypeID', $input['grvTypeID']);
            }
        }

        $grvMaster = $grvMaster->select(
            [   'erp_grvmaster.grvAutoID',
                'erp_grvmaster.grvPrimaryCode',
                'erp_grvmaster.documentSystemID',
                'erp_grvmaster.grvDoRefNo',
                'erp_grvmaster.createdDateTime',
                'erp_grvmaster.createdUserSystemID',
                'erp_grvmaster.grvNarration',
                'erp_grvmaster.grvLocation',
                'erp_grvmaster.grvDate',
                'erp_grvmaster.supplierID',
                'erp_grvmaster.serviceLineSystemID',
                'erp_grvmaster.grvConfirmedDate',
                'erp_grvmaster.approvedDate',
                'erp_grvmaster.postedDate',
                'erp_grvmaster.supplierTransactionCurrencyID',
                'erp_grvmaster.grvTotalSupplierTransactionCurrency',
                'erp_grvmaster.grvTotalComRptCurrency',
                'erp_grvmaster.grvTotalLocalCurrency',
                'erp_grvmaster.grvCancelledYN',
                'erp_grvmaster.timesReferred',
                'erp_grvmaster.grvConfirmedYN',
                'erp_grvmaster.approved',
                'erp_grvmaster.grvLocation',
                'erp_grvmaster.refferedBackYN',
                'erp_grvmaster.grvTypeID',
                'erp_grvmaster.companyReportingCurrencyID',
                'erp_grvmaster.localCurrencyID',
                'erp_grvmaster.projectID',
                'erp_grvmaster.companySystemID',
                'erp_grvmaster.companySystemID'
            ]);
            
            if ($search) {
                $search = str_replace("\\", "\\\\", $search);
                $grvMaster = $grvMaster->where(function ($query) use ($search) {
                    $query->where('grvPrimaryCode', 'LIKE', "%{$search}%")
                        ->orWhere('grvNarration', 'LIKE', "%{$search}%")
                        ->orWhere('supplierName', 'LIKE', "%{$search}%");
                });
            }
            return $grvMaster;
    }

    public function setExportExcelData($dataSet) {
        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][__('custom.e_type')] = $val->grvtype_by ? $val->grvtype_by->des : '';
                $data[$x][__('custom.e_grv_code')] = $val->grvPrimaryCode;
                $data[$x][__('custom.e_segment')] = $val->segment_by ? $val->segment_by->ServiceLineDes : '';
                $data[$x][__('custom.e_reference_no')] = $val->grvDoRefNo;
                $data[$x][__('custom.e_grv_date')] = Helper::dateFormat($val->grvDate);
                $data[$x][__('custom.e_supplier_code')] = $val->supplier_by ? $val->supplier_by->primarySupplierCode : '';
                $data[$x][__('custom.e_supplier_name')] = $val->supplier_by ? $val->supplier_by->supplierName : '';
                $data[$x][__('custom.e_location')] = $val->location_by ? $val->location_by->wareHouseDescription : '';
                $data[$x][__('custom.e_narration')] = $val->grvNarration;
                $data[$x][__('custom.e_created_by')] = $val->created_by ? $val->created_by->empName : '';
                $data[$x][__('custom.e_created_date')] = Helper::convertDateWithTime($val->createdDateTime);
                $data[$x][__('custom.e_confirmed_date')] = Helper::convertDateWithTime($val->grvConfirmedDate);
                $data[$x][__('custom.e_approved_date')] = Helper::convertDateWithTime($val->approvedDate);
                $data[$x][__('custom.e_transaction_currency')] = $val->supplierTransactionCurrencyID ? ($val->currency_by ? $val->currency_by->CurrencyCode : '') : '';
                $data[$x][__('custom.e_transaction_amount')] = number_format($val->grvTotalSupplierTransactionCurrency, $val->currency_by ? $val->currency_by->DecimalPlaces : 0, ".", "");
                $data[$x][__('custom.e_local_currency')] = $val->localCurrencyID ? ($val->local_currency_by ? $val->local_currency_by->CurrencyCode : '') : '';
                $data[$x][__('custom.e_local_amount')] = number_format($val->grvTotalLocalCurrency, $val->local_currency_by ? $val->local_currency_by->DecimalPlaces : 0, ".", "");
                $data[$x][__('custom.e_reporting_currency')] = $val->companyReportingCurrencyID ? ($val->reporting_currency_by ? $val->reporting_currency_by->CurrencyCode : '') : '';
                $data[$x][__('custom.e_reporting_amount')] = number_format($val->grvTotalComRptCurrency, $val->reporting_currency_by ? $val->reporting_currency_by->DecimalPlaces : 0, ".", "");
                $data[$x][__('custom.e_status')] = StatusService::getStatus($val->grvCancelledYN, null, $val->grvConfirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
