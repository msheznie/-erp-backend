<?php

namespace App\Services;

use App\Models\CompanyPolicyMaster;
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GRVDetails;
use App\Models\ItemAssigned;
use App\Models\MaterielRequest;
use App\Models\MaterielRequestDetails;
use App\Models\PurchaseOrderDetails;
use App\Repositories\MaterielRequestDetailsRepository;


class MaterialRequestDetailService
{
    private $materielRequestDetailsRepository;

    public function __construct(MaterielRequestDetailsRepository $materielRequestDetailsRepository)
    {
        $this->materielRequestDetailsRepository = $materielRequestDetailsRepository;
    }

    public function storeMaterialRequestDetail($input) : Array {
        $companySystemID = $input['companySystemID'];
        $materielRequest = MaterielRequest::where('RequestID', $input['RequestID'])->first();

        if (empty($materielRequest))
            return['success' => false,'message' => 'Materiel Request Details not found'];

        if($materielRequest->ClosedYN == -1)
            return['success' => false,'message' => 'This Materiel Request already closed. You can not add.'];

        if($materielRequest->approved == -1)
            return['success' => false,'message' => 'This Materiel Request fully approved. You can not add.'];

        if($this->checkAllowItemToType($companySystemID))
            $input['itemCode'] = isset($input['itemCode']['id']) ? $input['itemCode']['id'] : $input['itemCode'];


        $checkItem = $this->checkItem($input['itemCode'],$companySystemID);

        if(!$checkItem['success'])
            return $checkItem;

        $item = $checkItem['data'];
        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
            ->where('mainItemCategoryID', $item->financeCategoryMaster)
            ->where('itemCategorySubID', $item->financeCategorySub)
            ->first();

        if (empty($financeItemCategorySubAssigned))
            return['success' => false,'message' => 'Finance Category not found'];

        if ($item->financeCategoryMaster == 1) {
            $alreadyAdded = MaterielRequest::where('RequestID', $input['RequestID'])
                ->whereHas('details', function ($query) use ($item) {
                    $query->where('itemCode', $item->itemCodeSystem);
                })
                ->first();
            if ($alreadyAdded)
                return['success' => false,'message' => 'Selected item is already added. Please check again'];

        }

        $materielRequestItemObj = $this->getMaterialRequestdetailObj($materielRequest,$item,$financeItemCategorySubAssigned,$companySystemID);
        $materielRequestDetails = $this->materielRequestDetailsRepository->create($materielRequestItemObj->toArray());

        return['success' => true,'message' => 'Materiel Request Details saved successfully' , 'data' => $materielRequestDetails->toArray()];

    }

    private function getMaterialRequestdetailObj($materielRequest,$item,$financeItemCategorySubAssigned,$companySystemID) : MaterielRequestDetails {

        $poQnty = $this->getPOQty($companySystemID,$materielRequest->location,$item->itemCode);
        $quantityInHand = $this->getQuantityInHand($companySystemID,$item->itemCode);
        $grvQty = $this->getGrvQty($companySystemID,$item->itemCode);

        $materielRequestItem = new MaterielRequestDetails();
        $materielRequestItem->RequestID = $materielRequest->RequestID;
        $materielRequestItem->itemCode = $item->itemCodeSystem;
        $materielRequestItem->itemDescription = $item->itemDescription;
        $materielRequestItem->partNumber = $item->secondaryItemCode;
        $materielRequestItem->itemFinanceCategoryID = $item->financeCategoryMaster;
        $materielRequestItem->itemFinanceCategorySubID = $item->financeCategorySub;
        $materielRequestItem->unitOfMeasure = $item->itemUnitOfMeasure;
        $materielRequestItem->unitOfMeasureIssued = $item->itemUnitOfMeasure;
        $materielRequestItem->maxQty = $item->maximunQty;
        $materielRequestItem->minQty = $item->minimumQty;
        $materielRequestItem->financeGLcodebBS = $financeItemCategorySubAssigned->financeGLcodebBS;
        $materielRequestItem->financeGLcodePL = $financeItemCategorySubAssigned->financeGLcodePL;
        $materielRequestItem->includePLForGRVYN = $financeItemCategorySubAssigned->includePLForGRVYN;
        $materielRequestItem->minQty = $item->minimumQty;
        $materielRequestItem->quantityInHand  = $quantityInHand;
        $materielRequestItem->quantityOnOrder =  ($poQnty-$grvQty);

        return $materielRequestItem;
    }

    private function getGrvQty($companySystemID,$itemCode):int {
        return GRVDetails::whereHas('grv_master' , function ($query) use ($companySystemID,$itemCode) {
            $query->where('companySystemID', $companySystemID)
                ->where('grvTypeID', 2)
                ->groupBy('erp_grvmaster.companySystemID');
        })
            ->where('itemCode', $itemCode)
            ->groupBy('erp_grvdetails.itemCode')
            ->select(
                [
                    'erp_grvdetails.companySystemID',
                    'erp_grvdetails.itemCode'
                ])
            ->sum('noQty');
    }


    private function getQuantityInHand($companySystemID,$itemCode):int {
        return ErpItemLedger::where('itemSystemCode', $itemCode)
            ->where('companySystemID', $companySystemID)
            ->groupBy('itemSystemCode')
            ->sum('inOutQty');
    }

    private function getPOQty($companySystemID,$location,$itemCode):int{
        return PurchaseOrderDetails::whereHas('order' , function ($query) use ($companySystemID,$location, $itemCode) {
            $query->where('companySystemID', $companySystemID)
                ->where('poLocation', $location)
                ->where('approved', -1)
                ->where('poCancelledYN', 0);
        })
            ->where('itemCode', $itemCode)
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
    }
    private function checkAllowItemToType($companySystemID):bool {
       $policy = CompanyPolicyMaster::where('companyPolicyCategoryID', 54)
            ->where('companySystemID', $companySystemID)
            ->first();

       if($policy)
            return true;
       return false;
    }

    private  function checkItem($itemCode, $companySystemID): Array {

        $item = ItemAssigned::where('itemCodeSystem', $itemCode)
            ->where('companySystemID', $companySystemID)
            ->first();

        if (empty($item)) {
            if (!$this->checkAllowItemToType($companySystemID)) {
                return ['success' => false,'message' => 'Item not found', 'data' => []];
            }
        }

        return ['success' => true , 'message'=>'Item Found' , 'data' => $item];
    }
}
