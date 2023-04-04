<?php

namespace App\Services;

use App\Models\ProcumentOrder;
use App\Models\ItemAssigned;
use App\Models\PurchaseOrderDetails;
use App\Models\ProcumentOrderDetail;
use App\Models\CompanyPolicyMaster;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\SupplierAssigned;
use App\Models\User;
use App\Models\AssetFinanceCategory;
use Illuminate\Support\Facades\DB;
use App\helper\Helper;
use Response;
use Illuminate\Support\Facades\Auth;
use App\helper\TaxService;
use App\Models\ErpItemLedger;
use App\Models\GRVDetails;
use App\Models\MaterielRequest;
use App\Models\MaterielRequestDetails;
use App\Repositories\MaterielRequestDetailsRepository;

class MaterialRequestService
{
    public static function validateMaterialRequestItem($itemCode, $companySystemID, $RequestID)
    {

        $allowItemToTypePolicy = false;
        $itemNotound = false;
        $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 54)
                                            ->where('companySystemID', $companySystemID)
                                            ->first();

        if ($allowItemToType) {
            $allowItemToTypePolicy = true;
        }


        $item = ItemAssigned::where('itemCodeSystem', $itemCode)
                            ->where('companySystemID', $companySystemID)
                            ->first();

        if (empty($item)) {
            if (!$allowItemToTypePolicy) {
                return ['status' => false, 'message' => 'Item not found'];
            } else {
                $itemNotound = true;
            }
        }

        $materielRequest = MaterielRequest::where('RequestID', $RequestID)->first();


        if (empty($materielRequest)) {
            return ['status' => false, 'message' => 'Materiel Request Details not found'];

        }

        if($materielRequest->ClosedYN == -1){
                return ['status' => false, 'message' => 'This Materiel Request already closed. You can not add.'];
        }

        if($materielRequest->approved == -1){
                return ['status' => false, 'message' => 'This Materiel Request fully approved. You can not add.'];
        }


        
        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
        ->where('mainItemCategoryID', $item->financeCategoryMaster)
        ->where('itemCategorySubID', $item->financeCategorySub)
        ->first();

        if (empty($financeItemCategorySubAssigned)) {
                return ['status' => false, 'message' => 'Finance Category not found'];
        }

        if ($item->financeCategoryMaster == 1) {

            $alreadyAdded = MaterielRequest::where('RequestID', $RequestID)
                ->whereHas('details', function ($query) use ($item) {
                    $query->where('itemCode', $item->itemCodeSystem);
                })
                ->first();

            if ($alreadyAdded) {
                return ['status' => false, 'message' => 'Selected item is already added. Please check again'];

            }
        }

        return ['status' => true];

    }

    public static function saveMaterialRequestItem($itemCode, $companySystemID, $RequestID, $empID, $employeeSystemID)
    {
        $allowItemToTypePolicy = false;
        $itemNotound = false;
        $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 54)
                                            ->where('companySystemID', $companySystemID)
                                            ->first();

        if ($allowItemToType) {
            $allowItemToTypePolicy = true;
        }


        if ($allowItemToTypePolicy) {
            $input['itemCode'] = isset($input['itemCode']['id']) ? $input['itemCode']['id'] : $itemCode;
        }

        $item = ItemAssigned::where('itemCodeSystem', $itemCode)
                            ->where('companySystemID', $companySystemID)
                            ->first();

        if (empty($item)) {
            if (!$allowItemToTypePolicy) {
                $itemNotound = false;
            } else {
                $itemNotound = true;
            }
        }

        $materielRequest = MaterielRequest::where('RequestID', $RequestID)->first();

        $input['qtyIssuedDefaultMeasure'] = 0;
        $input['RequestID'] = $RequestID;
        if (!$itemNotound) {
            $input['itemCode'] = $item->itemCodeSystem;
            $input['itemDescription'] = $item->itemDescription;
            $input['partNumber'] = $item->secondaryItemCode;
            $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
            $input['itemFinanceCategorySubID'] = $item->financeCategorySub;
            $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
            $input['unitOfMeasureIssued'] = $item->itemUnitOfMeasure;
            if($item->maximunQty){
                $input['maxQty'] = $item->maximunQty;
            }else{
                $input['maxQty'] = 0;
            }

            if($item->minimumQty){
                $input['minQty'] = $item->minimumQty;
            }else{
                $input['minQty'] = 0;
            }

            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
            ->where('mainItemCategoryID', $item->financeCategoryMaster)
            ->where('itemCategorySubID', $item->financeCategorySub)
            ->first();

            $input['financeGLcodebBS']  = $financeItemCategorySubAssigned->financeGLcodebBS;
            $input['financeGLcodePL']   = $financeItemCategorySubAssigned->financeGLcodePL;
            $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;


             $poQty = PurchaseOrderDetails::whereHas('order' , function ($query) use ($companySystemID,$materielRequest) {
                                                $query->where('companySystemID', $companySystemID)
                                                    ->where('poLocation', $materielRequest->location)
                                                    ->where('approved', -1)
                                                    ->where('poCancelledYN', 0);
                                         })
                                        ->where('itemCode', $input['itemCode'])
                                        ->groupBy('erp_purchaseorderdetails.companySystemID',
                                            'erp_purchaseorderdetails.itemCode')
                                        ->select(
                                            [
                                                'erp_purchaseorderdetails.companySystemID',
                                                'erp_purchaseorderdetails.itemCode',
                                                'erp_purchaseorderdetails.itemPrimaryCode'
                                            ]
                                        )
                                        ->sum('noQty');

            $quantityInHand = ErpItemLedger::where('itemSystemCode', $input['itemCode'])
                                    ->where('companySystemID', $companySystemID)
                                    ->groupBy('itemSystemCode')
                                    ->sum('inOutQty');

            $grvQty = GRVDetails::whereHas('grv_master' , function ($query) use ($companySystemID,$materielRequest) {
                                $query->where('companySystemID', $companySystemID)
                                    ->where('grvTypeID', 2)
                                    ->groupBy('erp_grvmaster.companySystemID');
                                 })
                                ->where('itemCode', $input['itemCode'])
                                ->groupBy('erp_grvdetails.itemCode')
                                ->select(
                                    [
                                        'erp_grvdetails.companySystemID',
                                        'erp_grvdetails.itemCode'
                                    ])
                                ->sum('noQty');

            $quantityOnOrder = $poQty - $grvQty;
            $input['quantityOnOrder'] = $quantityOnOrder;
            $input['quantityInHand']  = $quantityInHand;

        } else {
            $input['itemDescription'] = $input['itemCode'];
            $input['itemCode'] = null;
            $input['partNumber'] = null;
            $input['itemFinanceCategoryID'] = null;
            $input['itemFinanceCategorySubID'] = null;
            $input['unitOfMeasure'] = null;
            $input['unitOfMeasureIssued'] = null;
            $input['maxQty'] = 0;
            $input['minQty'] = 0;
            $input['quantityOnOrder'] = 0;
            $input['quantityInHand'] = 0;

        }

        $input['estimatedCost'] = 0;
        $input['quantityRequested'] = 0;
        
        $input['ClosedYN'] = 0;
        $input['selectedForIssue'] = 0;
        $input['comments'] = null;
        $input['convertionMeasureVal'] = 1;

        $input['allowCreatePR']      = 0;
        $input['selectedToCreatePR'] = 0;


        $materielRequestDetails = MaterielRequestDetails::create($input);
    }

}