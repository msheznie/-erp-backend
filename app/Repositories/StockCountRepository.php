<?php

namespace App\Repositories;

use App\Models\StockCount;
use App\Models\Company;
use App\Models\ItemAssigned;
use App\Models\ItemReturnDetails;
use App\Models\ItemIssueDetails;
use App\Models\StockTransferDetails;
use App\Models\StockReceiveDetails;
use App\Models\PurchaseReturnDetails;
use App\Models\DeliveryOrderDetail;
use App\Models\StockCountDetailsRefferedBack;
use App\Models\StockCountRefferedBack;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\InventoryReclassificationDetail;
use App\Models\StockAdjustmentDetails;
use App\Models\GRVDetails;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\DocumentApproved;
use App\Models\StockCountDetail;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\Unit;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\helper\StatusService;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StockCountRepository
 * @package App\Repositories
 * @version June 10, 2021, 2:09 pm +04
 *
 * @method StockCount findWithoutFail($id, $columns = ['*'])
 * @method StockCount find($id, $columns = ['*'])
 * @method StockCount first($columns = ['*'])
*/
class StockCountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'serialNo',
        'stockCountCode',
        'refNo',
        'stockCountDate',
        'location',
        'comment',
        'stockCountType',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'refferedBackYN',
        'timesReferred',
        'createdDateTime',
        'createdUserGroup',
        'createdPCid',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp',
        'RollLevForApp_curr'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockCount::class;
    }

    public function stockCountListQuery($request, $input, $search = '', $grvLocation, $serviceLineSystemID)
    {

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $stockAdjustments = StockCount::whereIn('companySystemID', $subCompanies)
            ->with(['created_by', 'warehouse_by', 'segment_by']);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $stockAdjustments->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $stockAdjustments->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $stockAdjustments->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if (array_key_exists('location', $input)) {
            if ($input['location'] && !is_null($input['location'])) {
                $stockAdjustments->whereIn('location', $grvLocation);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $stockAdjustments->whereMonth('stockAdjustmentDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $stockAdjustments->whereYear('stockAdjustmentDate', '=', $input['year']);
            }
        }


        $stockAdjustments = $stockAdjustments->select(
            ['stockCountAutoID',
                'stockCountCode',
                'comment',
                'stockCountDate',
                'confirmedYN',
                'approved',
                'serviceLineSystemID',
                'documentSystemID',
                'confirmedByEmpSystemID',
                'createdUserSystemID',
                'confirmedDate',
                'createdDateTime',
                'refNo',
                'location',
                'refferedBackYN',
                'detailStatus'
            ]);



        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockAdjustments = $stockAdjustments->where(function ($query) use ($search) {
                $query->where('stockCountCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return $stockAdjustments;
    }


    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.doc_code')] = $val->stockCountCode;
                $data[$x][trans('custom.segment')] = $val->segment_by? $val->segment_by->ServiceLineDes : '';
                $data[$x][trans('custom.reference_no')] = $val->refNo;
                $data[$x][trans('custom.date')] = \Helper::dateFormat($val->stockCountDate);
                $data[$x][trans('custom.location')] = $val->warehouse_by? $val->warehouse_by->wareHouseDescription : '';
                $data[$x][trans('custom.comment')] = $val->comment;
                $data[$x][trans('custom.created_by')] = $val->created_by? $val->created_by->empName : '';
                $data[$x][trans('custom.created_at')] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x][trans('custom.confirmed_at')] = \Helper::convertDateWithTime($val->confirmedDate);
                $data[$x][trans('custom.approved_at')] = \Helper::convertDateWithTime($val->approvedDate);
                $data[$x][trans('custom.status')] = StatusService::getStatus($val->CancelledYN, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }

    public function getAudit($id){
        return  $this->with(['created_by','confirmed_by','modified_by','warehouse_by','company','details.uom','approved_by' => function ($query) {
            $query->with(['employee' =>  function($q){
                $q->with(['details.designation']);
            }])
                ->where('documentSystemID',97);
        },'audit_trial.modified_by'])
            ->findWithoutFail($id);
    }

    public function validateProductsForStockCount($input, $items)
    {
        $itemCodeSystems = $items->pluck('itemCodeSystem')->toArray();

        $checkGrvs = GRVDetails::selectRaw('grvDetailsID, grvAutoID, grvAutoID as documentID, itemDescription, itemPrimaryCode, itemCode')
                               ->whereIn('itemCode', $itemCodeSystems)
                               ->with(['master' => function ($query) use ($input) {
                                   $query->selectRaw('grvAutoID, grvPrimaryCode as documentCode, documentSystemID')
                                         ->where('companySystemID', $input['companySystemID'])
                                         ->where('grvLocation', $input['location'])
                                         ->where('approved', '!=', -1);
                               }])
                               ->whereHas('master', function ($query) use ($input) {
                                    $query->where('companySystemID', $input['companySystemID'])
                                          ->where('grvLocation', $input['location'])
                                          ->where('approved', '!=', -1);
                               })
                               ->get();

        $skipIds = [];
        if (count($checkGrvs)) {
            $skipIds[] = $checkGrvs->pluck('itemCode');
        }

        $checkMaterialIssues = ItemIssueDetails::selectRaw('itemIssueDetailID, itemIssueAutoID, itemIssueAutoID as documentID, itemDescription, itemPrimaryCode, itemCodeSystem')
                                               ->whereIn('itemCodeSystem', $itemCodeSystems)
                                               ->with(['master' => function ($query) use ($input) {
                                                   $query->selectRaw('itemIssueAutoID, itemIssueCode as documentCode, documentSystemID')
                                                         ->where('companySystemID', $input['companySystemID'])
                                                         ->where('wareHouseFrom', $input['location'])
                                                         ->where('approved', '!=', -1);
                                               }])
                                               ->whereHas('master', function ($query) use ($input) {
                                                    $query->where('companySystemID', $input['companySystemID'])
                                                          ->where('wareHouseFrom', $input['location'])
                                                          ->where('approved', '!=', -1);
                                               })
                                               ->get();

        if (count($checkMaterialIssues)) {
            $skipIds[] = $checkMaterialIssues->pluck('itemCodeSystem');
        }


        $checkMaterialReturn = ItemReturnDetails::selectRaw('itemReturnDetailID, itemReturnAutoID, itemReturnAutoID as documentID, itemDescription, itemPrimaryCode, itemCodeSystem')
                                               ->whereIn('itemCodeSystem', $itemCodeSystems)
                                               ->with(['master' => function ($query) use ($input) {
                                                   $query->selectRaw('itemReturnAutoID, itemReturnCode as documentCode, documentSystemID')
                                                         ->where('companySystemID', $input['companySystemID'])
                                                         ->where('wareHouseLocation', $input['location'])
                                                         ->where('approved', '!=', -1);
                                               }])
                                               ->whereHas('master', function ($query) use ($input) {
                                                    $query->where('companySystemID', $input['companySystemID'])
                                                          ->where('wareHouseLocation', $input['location'])
                                                          ->where('approved', '!=', -1);
                                               })
                                               ->get();

        if (count($checkMaterialReturn)) {
            $skipIds[] = $checkMaterialReturn->pluck('itemCodeSystem');
        }


        $checkStockTransfer = StockTransferDetails::selectRaw('stockTransferDetailsID, stockTransferAutoID, stockTransferAutoID as documentID, itemDescription, itemPrimaryCode, itemCodeSystem')
                                               ->whereIn('itemCodeSystem', $itemCodeSystems)
                                               ->with(['master' => function ($query) use ($input) {
                                                   $query->selectRaw('stockTransferAutoID, stockTransferCode as documentCode, documentSystemID')
                                                         ->where('companySystemID', $input['companySystemID'])
                                                         ->where(function($query) use ($input) {
                                                            $query->where('locationTo', $input['location'])
                                                                  ->orWhere('locationFrom', $input['location']);
                                                         })
                                                         ->where('approved', '!=', -1);
                                               }])
                                               ->whereHas('master', function ($query) use ($input) {
                                                    $query->where('companySystemID', $input['companySystemID'])
                                                          ->where(function($query) use ($input) {
                                                            $query->where('locationTo', $input['location'])
                                                                  ->orWhere('locationFrom', $input['location']);
                                                          })
                                                          ->where('approved', '!=', -1);
                                               })
                                               ->get();

        if (count($checkStockTransfer)) {
            $skipIds[] = $checkStockTransfer->pluck('itemCodeSystem');
        }


        $checkStockRecive = StockReceiveDetails::selectRaw('stockReceiveDetailsID, stockReceiveAutoID, stockReceiveAutoID as documentID, itemDescription, itemPrimaryCode, itemCodeSystem')
                                               ->whereIn('itemCodeSystem', $itemCodeSystems)
                                               ->with(['master' => function ($query) use ($input) {
                                                   $query->selectRaw('stockReceiveAutoID, stockReceiveCode as documentCode, documentSystemID')
                                                         ->where('companySystemID', $input['companySystemID'])
                                                         ->where(function($query) use ($input) {
                                                            $query->where('locationTo', $input['location'])
                                                                  ->orWhere('locationFrom', $input['location']);
                                                         })
                                                         ->where('approved', '!=', -1);
                                               }])
                                               ->whereHas('master', function ($query) use ($input) {
                                                    $query->where('companySystemID', $input['companySystemID'])
                                                          ->where(function($query) use ($input) {
                                                            $query->where('locationTo', $input['location'])
                                                                  ->orWhere('locationFrom', $input['location']);
                                                         })
                                                          ->where('approved', '!=', -1);
                                               })
                                               ->get();

        if (count($checkStockRecive)) {
            $skipIds[] = $checkStockRecive->pluck('itemCodeSystem');
        }


        $checkStockAdj = StockAdjustmentDetails::selectRaw('stockAdjustmentDetailsAutoID, stockAdjustmentAutoID, stockAdjustmentAutoID as documentID, itemDescription, itemPrimaryCode, itemCodeSystem')
                                               ->whereIn('itemCodeSystem', $itemCodeSystems)
                                               ->with(['master' => function ($query) use ($input) {
                                                   $query->selectRaw('stockAdjustmentAutoID, stockAdjustmentCode as documentCode, documentSystemID')
                                                         ->where('companySystemID', $input['companySystemID'])
                                                         ->where('location', $input['location'])
                                                         ->where('approved', '!=', -1);
                                               }])
                                               ->whereHas('master', function ($query) use ($input) {
                                                    $query->where('companySystemID', $input['companySystemID'])
                                                          ->where('location', $input['location'])
                                                          ->where('approved', '!=', -1);
                                               })
                                               ->get();

        if (count($checkStockAdj)) {
            $skipIds[] = $checkStockAdj->pluck('itemCodeSystem');
        }


        $checkPR = PurchaseReturnDetails::selectRaw('purhasereturnDetailID, purhaseReturnAutoID, purhaseReturnAutoID as documentID, itemDescription, itemPrimaryCode, itemCode')
                                               ->whereIn('itemCode', $itemCodeSystems)
                                               ->with(['master' => function ($query) use ($input) {
                                                   $query->selectRaw('purhaseReturnAutoID, purchaseReturnCode as documentCode, documentSystemID')
                                                         ->where('companySystemID', $input['companySystemID'])
                                                         ->where('purchaseReturnLocation', $input['location'])
                                                         ->where('approved', '!=', -1);
                                               }])
                                               ->whereHas('master', function ($query) use ($input) {
                                                    $query->where('companySystemID', $input['companySystemID'])
                                                          ->where('purchaseReturnLocation', $input['location'])
                                                          ->where('approved', '!=', -1);
                                               })
                                               ->get();

        if (count($checkPR)) {
            $skipIds[] = $checkPR->pluck('itemCode');
        }

        $checkDeliveryOrder = DeliveryOrderDetail::selectRaw('deliveryOrderDetailID, deliveryOrderID, deliveryOrderID as documentID, itemDescription, itemPrimaryCode, itemCodeSystem')
                                               ->whereIn('itemCodeSystem', $itemCodeSystems)
                                               ->with(['master' => function ($query) use ($input) {
                                                   $query->selectRaw('deliveryOrderID, deliveryOrderCode as documentCode, documentSystemID')
                                                         ->where('companySystemID', $input['companySystemID'])
                                                         ->where('wareHouseSystemCode', $input['location'])
                                                         ->where('approvedYN', '!=', -1);
                                               }])
                                               ->whereHas('master', function ($query) use ($input) {
                                                    $query->where('companySystemID', $input['companySystemID'])
                                                          ->where('wareHouseSystemCode', $input['location'])
                                                          ->where('approvedYN', '!=', -1);
                                               })
                                               ->get();

        if (count($checkDeliveryOrder)) {
            $skipIds[] = $checkDeliveryOrder->pluck('itemCodeSystem');
        }

        $checkCustomerInvoice = CustomerInvoiceItemDetails::selectRaw('customerItemDetailID, custInvoiceDirectAutoID, custInvoiceDirectAutoID as documentID, itemDescription, itemPrimaryCode, itemCodeSystem')
                                               ->whereIn('itemCodeSystem', $itemCodeSystems)
                                               ->with(['master' => function ($query) use ($input) {
                                                   $query->selectRaw('custInvoiceDirectAutoID, bookingInvCode as documentCode, documentSystemiD as documentSystemID')
                                                         ->where('companySystemID', $input['companySystemID'])
                                                         ->where('wareHouseSystemCode', $input['location'])
                                                         ->where('approved', '!=', -1);
                                               }])
                                               ->whereHas('master', function ($query) use ($input) {
                                                    $query->where('companySystemID', $input['companySystemID'])
                                                          ->where('wareHouseSystemCode', $input['location'])
                                                          ->where('approved', '!=', -1);
                                               })
                                               ->get();

        if (count($checkCustomerInvoice)) {
            $skipIds[] = $checkCustomerInvoice->pluck('itemCodeSystem');
        }
        

       $checkInventoryReClassification = InventoryReclassificationDetail::selectRaw('inventoryReclassificationDetailID, inventoryreclassificationID, inventoryreclassificationID as documentID, itemDescription, itemPrimaryCode, itemSystemCode')
                                               ->whereIn('itemSystemCode', $itemCodeSystems)
                                               ->with(['master' => function ($query) use ($input) {
                                                   $query->selectRaw('inventoryreclassificationID, documentCode as documentCode, documentSystemID')
                                                         ->where('companySystemID', $input['companySystemID'])
                                                         ->where('wareHouseSystemCode', $input['location'])
                                                         ->where('approved', '!=', -1);
                                               }])
                                               ->whereHas('master', function ($query) use ($input) {
                                                    $query->where('companySystemID', $input['companySystemID'])
                                                          ->where('wareHouseSystemCode', $input['location'])
                                                          ->where('approved', '!=', -1);
                                               })
                                               ->get();

        if (count($checkInventoryReClassification)) {
            $skipIds[] = $checkInventoryReClassification->pluck('itemSystemCode');
        }


        $stockCountDetails = StockCountDetail::selectRaw('stockCountDetailsAutoID, stockCountAutoID, stockCountAutoID as documentID, itemDescription, itemPrimaryCode, itemCodeSystem')
                                               ->whereIn('itemCodeSystem', $itemCodeSystems)
                                               ->with(['master' => function ($query) use ($input) {
                                                   $query->selectRaw('stockCountAutoID, stockCountCode as documentCode, documentSystemID')
                                                         ->where('companySystemID', $input['companySystemID'])
                                                         ->where('location', $input['location'])
                                                         ->where('approved', '!=', -1);
                                               }])
                                               ->whereHas('master', function ($query) use ($input) {
                                                    $query->where('companySystemID', $input['companySystemID'])
                                                          ->where('location', $input['location'])
                                                          ->where('approved', '!=', -1);
                                               })
                                               ->get();

        if (count($stockCountDetails)) {
            $skipIds[] = $stockCountDetails->pluck('itemCodeSystem');
        }

        $skipFinalIDs = [];
        foreach ($skipIds as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $skipFinalIDs[] = $value1;
            }
        }
        

        $skipItemIds = array_unique($skipFinalIDs);

        $usedItems = $checkInventoryReClassification->merge($checkCustomerInvoice)->merge($checkDeliveryOrder)->merge($checkPR)->merge($checkStockAdj)->merge($checkStockRecive)->merge($checkStockTransfer)->merge($checkMaterialReturn)->merge($checkMaterialIssues)->merge($checkGrvs)->merge($stockCountDetails);


        return ['usedItems' => $usedItems, 'skipItemIds' => $skipItemIds];
    }
}
