<?php

namespace App\Services\Inventory;

use App\helper\Helper;
use App\helper\TaxService;
use App\Models\ChartOfAccountsAssigned;
use App\Models\CompanyDocumentAttachment;
use App\Models\FinanceItemCategorySub;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\ItemAssigned;
use App\Models\SegmentMaster;
use App\Models\WarehouseMaster;
use App\Repositories\GRVDetailsRepository;
use App\Repositories\GRVMasterRepository;
use App\Repositories\UserRepository;
use App\Services\CompanyDocumentAttachmentService;
use App\Services\Procurement\CategoryValidationService;
use App\Services\Validation\CommonValidationService;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

class GRVService
{
    private $grv;
    private $userRepository;
    private $commonValidationService;
    private $GRVDetailsRepository;
    private $GRVMasterRepository;
    public function __construct(
        UserRepository $userRepository, 
        CommonValidationService $commonValidationService, 
        GRVDetailsRepository $GRVDetailsRepository,
        GRVMasterRepository $GRVMasterRepository,
    )
    {
        $this->grv = new GRVMaster();
        $this->userRepository = $userRepository;
        $this->commonValidationService = $commonValidationService;
        $this->GRVDetailsRepository = $GRVDetailsRepository;
        $this->GRVMasterRepository = $GRVMasterRepository;
    }

    public function validateGRV($input)
    {

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $this->commonValidationService->validateCompany($input);

        $this->commonValidationService->validateFinanicalYear($input);

        $input = $this->commonValidationService->validateFinancialPeriod(collect($input)->merge(['departmentSystemID'=>10])->toArray());

        if(!isset($input['grvDate']))
            throw new \Exception("GRV date not found");

        if(!isset($input['stampDate']))
            throw new \Exception("Stamp date not found");

        if(!isset($input['grvLocation']))
            throw new \Exception("Location not found");


        $warehouse = WarehouseMaster::where("wareHouseSystemCode", $input['grvLocation'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if(empty($warehouse))
            throw new \Exception("Location not found");


        if ($warehouse->manufacturingYN == 1) {
            if (is_null($warehouse->WIPGLCode)) {
                throw new \Exception('Please assigned WIP GLCode for this warehouse');
            } else {
                $checkGLIsAssigned = ChartOfAccountsAssigned::checkCOAAssignedStatus($warehouse->WIPGLCode, $input['companySystemID']);
                if (empty($checkGLIsAssigned)) {
                    throw new \Exception('Assigned WIP GL Code is not assigned to this company!');
                }
            }
        }

        $input['grvDate'] = new Carbon($input['grvDate']);
        $input['stampDate'] = new Carbon(($input['stampDate']));

        if($input['grvDate']->greaterThan(Carbon::now()))
            throw new \Exception("GRV date can not be greater than current date");

        if($input['stampDate']->greaterThan(Carbon::now()))
            throw new \Exception("Stamp date can not be greater than current date");


    }

    public function validateGRVItem($itemCodeSystem, $companySystemID, $grvAutoID)
    {
        $grvMaster = $this->GRVMasterRepository->findWithoutFail($grvAutoID);
        if (empty($grvMaster)) {
            return ['status' => false, 'message' => trans('custom.grv_master_not_found')];
        }

        if ($grvMaster->serviceLineSystemID) {
            $checkDepartmentActive = SegmentMaster::find($grvMaster->serviceLineSystemID);
            if (empty($checkDepartmentActive)) {
                return ['status' => false, 'message' => trans('custom.department_not_found')];
            }
            if ($checkDepartmentActive->isActive == 0) {
                return ['status' => false, 'message' => trans('custom.please_select_active_department')];
            }
        } else {
            return ['status' => false, 'message' => trans('custom.please_select_department')];
        }

        if ($grvMaster->grvLocation) {
            $checkWarehouseActive = WarehouseMaster::find($grvMaster->grvLocation);
            if (empty($checkWarehouseActive)) {
                return ['status' => false, 'message' => trans('custom.warehouse_not_found')];
            }
            if ($checkWarehouseActive->isActive == 0) {
                return ['status' => false, 'message' => trans('custom.please_select_active_warehouse')];
            }
        } else {
            return ['status' => false, 'message' => trans('custom.please_select_warehouse')];
        }
        
        $itemAssign = ItemAssigned::with(['item_master'])->where('itemCodeSystem', $itemCodeSystem)->where('companySystemID', $companySystemID)->first();
        if (empty($itemAssign)) {
            return ['status' => false, 'message' => trans('custom.item_not_assigned')];
        }

        if (CategoryValidationService::shouldEnforceSingleCategory($grvMaster->companySystemID, 3)) {
            $grvDetailExistSameItem = GRVDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                ->where('grvAutoID', $grvAutoID)
                ->first();

            if ($grvDetailExistSameItem && $itemAssign->financeCategoryMaster != $grvDetailExistSameItem['itemFinanceCategoryID']) {
                return ['status' => false, 'message' => CategoryValidationService::getCategoryRestrictionMessage($grvMaster->companySystemID, 3)];
            }
        }

        $docConfigGrv = CompanyDocumentAttachment::where('companySystemID', $grvMaster->companySystemID)->where('documentSystemID', 3)->first();
        if ($docConfigGrv && CompanyDocumentAttachmentService::isApprovalEnabled($docConfigGrv->isSubcategoryApproval ?? 0)) {
            $grvDetailExistSub = GRVDetails::select(DB::raw('DISTINCT(itemFinanceCategorySubID) as itemFinanceCategorySubID'))
                ->where('grvAutoID', $grvAutoID)
                ->whereNotNull('itemFinanceCategorySubID')
                ->first();
            if ($grvDetailExistSub && $itemAssign->financeCategorySub && (int) $itemAssign->financeCategorySub !== (int) $grvDetailExistSub['itemFinanceCategorySubID']) {
                return ['status' => false, 'message' => trans('custom.grv_multiple_subcategories')];
            }
        }

        $item = ItemAssigned::where('itemCodeSystem', $itemAssign->itemCodeSystem)
            ->where('companySystemID', $companySystemID)
            ->first();
        //checking if item is inventory item cannot be added more than one
        $grvDetailExistSameItem = GRVDetails::select(DB::raw('itemCode'))
            ->where('grvAutoID', $grvAutoID)
            ->where('itemCode', $itemAssign->itemCodeSystem)
            ->first();

        if($grvMaster->grvTypeID == 1) {
            if ($item->financeCategoryMaster == 1) {
                if ($grvDetailExistSameItem) {
                    return ['status' => false, 'message' => trans('custom.selected_item_is_already_added_from_the_same_grv')];
                }
            }
        }
          if($grvMaster->grvTypeID != 1) {
            if ($grvDetailExistSameItem) {
                return ['status' => false, 'message' => trans('custom.selected_item_is_already_added_from_the_same_grv')];
            }
        }
        
        return ['status' => true, 'message' => 'GRV Item validated successfully'];
    }

    public function storeGRVItem($itemCodeSystem, $companySystemID, $grvAutoID)
    {
        $user = Helper::getEmployeeInfo();
        $unitCost = 0;
        $noQty = 1;
        $empID = $user->empID;
        $employeeSystemID = $user->employeeSystemID;

        $grvMaster = $this->GRVMasterRepository->findWithoutFail($grvAutoID);

        $itemAssigned = ItemAssigned::where('itemCodeSystem', $itemCodeSystem)
        ->where('companySystemID', $companySystemID)
        ->where('isAssigned', -1)
        ->where('isActive', 1)
        ->with(['item_master'])
        ->first();;
        
        $financeCategorySub = FinanceItemCategorySub::find($itemAssigned->financeCategorySub);
        $currency = Helper::convertAmountToLocalRpt($grvMaster->documentSystemID, $grvMaster->grvAutoID, $unitCost);

        // checking the qty request is matching with sum total
        $GRVDetail_arr['grvAutoID'] = $grvMaster->grvAutoID;
        $GRVDetail_arr['companySystemID'] = $companySystemID;
        $GRVDetail_arr['companyID'] = $grvMaster->companyID;
        $GRVDetail_arr['serviceLineCode'] = $grvMaster->serviceLineCode;
        $GRVDetail_arr['purchaseOrderMastertID'] = 0;
        $GRVDetail_arr['purchaseOrderDetailsID'] = 0;
        $GRVDetail_arr['itemCode'] = $itemAssigned->itemCodeSystem;
        $GRVDetail_arr['trackingType'] = optional($itemAssigned->item_master)->trackingType;
        $GRVDetail_arr['itemPrimaryCode'] = $itemAssigned->itemPrimaryCode;
        $GRVDetail_arr['itemDescription'] = $itemAssigned->itemDescription;
        $GRVDetail_arr['itemFinanceCategoryID'] = $itemAssigned->financeCategoryMaster;
        $GRVDetail_arr['itemFinanceCategorySubID'] = $itemAssigned->financeCategorySub;
        $GRVDetail_arr['financeGLcodebBSSystemID'] = $financeCategorySub->financeGLcodebBSSystemID;
        $GRVDetail_arr['financeGLcodebBS'] = $financeCategorySub->financeGLcodebBS;
        $GRVDetail_arr['financeGLcodePLSystemID'] = $financeCategorySub->financeGLcodePLSystemID;
        $GRVDetail_arr['financeGLcodePL'] = $financeCategorySub->financeGLcodePL;
        $GRVDetail_arr['includePLForGRVYN'] = $financeCategorySub->includePLForGRVYN;
        $GRVDetail_arr['supplierPartNumber'] = $itemAssigned->secondaryItemCode;
        $GRVDetail_arr['unitOfMeasure'] = $itemAssigned->itemUnitOfMeasure;
        $GRVDetail_arr['wasteQty'] = 0;
        $GRVDetail_arr['noQty'] = $noQty;
        $GRVDetail_arr['prvRecievedQty'] = 0;
        $GRVDetail_arr['poQty'] = 0;
        $GRVDetail_arr['unitCost'] = $unitCost;
        $GRVDetail_arr['discountPercentage'] = 0;
        $GRVDetail_arr['discountAmount'] = 0;
        $GRVDetail_arr['netAmount'] = $unitCost * $noQty;
        $GRVDetail_arr['comment'] = null;
        $GRVDetail_arr['supplierDefaultCurrencyID'] = $grvMaster->supplierDefaultCurrencyID;
        $GRVDetail_arr['supplierDefaultER'] = $grvMaster->supplierDefaultER;
        $GRVDetail_arr['supplierItemCurrencyID'] = $grvMaster->supplierTransactionCurrencyID;
        $GRVDetail_arr['foreignToLocalER'] = $grvMaster->supplierTransactionER;
        $GRVDetail_arr['companyReportingCurrencyID'] = $grvMaster->companyReportingCurrencyID;
        $GRVDetail_arr['companyReportingER'] = $grvMaster->companyReportingER;
        $GRVDetail_arr['localCurrencyID'] = $grvMaster->localCurrencyID;
        $GRVDetail_arr['localCurrencyER'] = $grvMaster->localCurrencyER;
        $GRVDetail_arr['addonDistCost'] = 0;
        $GRVDetail_arr['GRVcostPerUnitLocalCur'] = Helper::roundValue($currency['localAmount']);
        $GRVDetail_arr['GRVcostPerUnitSupDefaultCur'] = Helper::roundValue($currency['defaultAmount']);
        $GRVDetail_arr['GRVcostPerUnitSupTransCur'] = Helper::roundValue($unitCost);
        $GRVDetail_arr['GRVcostPerUnitComRptCur'] = Helper::roundValue($currency['reportingAmount']);
        $GRVDetail_arr['landingCost_LocalCur'] = Helper::roundValue($currency['localAmount']);
        $GRVDetail_arr['landingCost_TransCur'] = Helper::roundValue($unitCost);
        $GRVDetail_arr['landingCost_RptCur'] = Helper::roundValue($currency['reportingAmount']);
        $GRVDetail_arr['vatRegisteredYN'] = 0;
        $GRVDetail_arr['supplierVATEligible'] = 0;
        $GRVDetail_arr['VATPercentage'] = 0;
        $GRVDetail_arr['VATAmount'] = 0;
        $GRVDetail_arr['VATAmountLocal'] = 0;
        $GRVDetail_arr['VATAmountRpt'] = 0;
        $GRVDetail_arr['logisticsAvailable'] = 0;
        $GRVDetail_arr['createdPcID'] = gethostname();
        $GRVDetail_arr['createdUserID'] = $empID;
        $GRVDetail_arr['createdUserSystemID'] = $employeeSystemID;

        if ($grvMaster->vatRegisteredYN) {
            $vatDetails = TaxService::getVATDetailsByItem($grvMaster->companySystemID, $GRVDetail_arr['itemCode'], $grvMaster->supplierID);
            $GRVDetail_arr['VATPercentage'] = $vatDetails['percentage'];
            $GRVDetail_arr['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
            $GRVDetail_arr['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
            $GRVDetail_arr['VATAmount'] = 0;
            $GRVDetail_arr['VATAmountLocal'] = 0;
            $GRVDetail_arr['VATAmountRpt'] = 0;
        }

        $this->GRVDetailsRepository->create($GRVDetail_arr);
    }
}
