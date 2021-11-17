<?php
/**
 * =============================================
 * -- File Name : ItemAssignedAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Item Assigned
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Item Assigned
 * -- Date: 6-September 2018 By: Fayas Description: Added new functions named as getAllAssignedItemsByCompany(),exportItemAssignedByCompanyReport()
 * -- Date: 20 - January 2019 By: Fayas Description: Added new functions named as getAllNonPosItemsByCompany(),savePullItemsFromInventory()
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemAssignedAPIRequest;
use App\Http\Requests\API\UpdateItemAssignedAPIRequest;
use App\Models\ItemAssigned;
use App\Models\Company;
use App\Models\ItemMaster;
use App\Models\CompanyFinanceYear;
use App\Models\PurchaseRequest;
use App\Models\CompanyDocumentAttachment;
use App\Models\SegmentMaster;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\CompanyPolicyMaster;
use App\Models\ProcumentOrder;
use App\Models\ErpItemLedger;
use App\Models\GRVDetails;
use App\Models\PurchaseOrderDetails;
use App\helper\Helper;
use App\Repositories\ItemAssignedRepository;
use App\Repositories\ItemMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use App\Repositories\PurchaseRequestRepository;
/**
 * Class ItemAssignedController
 * @package App\Http\Controllers\API
 */
class ItemAssignedAPIController extends AppBaseController
{
    /** @var  ItemAssignedRepository */
    private $itemAssignedRepository;
    private $itemMasterRepository;
    private $userRepository;
    private $purchaseRequestRepository;
    public function __construct(PurchaseRequestRepository $purchaseRequestRepo,UserRepository $userRepo,ItemAssignedRepository $itemAssignedRepo,ItemMasterRepository $itemMasterRepo)
    {
        $this->itemAssignedRepository = $itemAssignedRepo;
        $this->itemMasterRepository = $itemMasterRepo;
        $this->userRepository = $userRepo;
        $this->purchaseRequestRepository = $purchaseRequestRepo;
    }

    /**
     * Display a listing of the ItemAssigned.
     * GET|HEAD /itemAssigneds
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->itemAssignedRepository->pushCriteria(new RequestCriteria($request));
        $this->itemAssignedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemAssigneds = $this->itemAssignedRepository->all();

        return $this->sendResponse($itemAssigneds->toArray(), 'Item Assigneds retrieved successfully');
    }

    /**
     * Store a newly created ItemAssigned in storage.
     * POST /itemAssigneds
     *
     * @param CreateItemAssignedAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateItemAssignedAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input,['finance_sub_category']);
        unset($input['company']);
        unset($input['final_approved_by']);

        $input = $this->convertArrayToValue($input);

        $itemId = isset($input['itemCodeSystem'])?$input['itemCodeSystem']:0;

        $itemMaster = ItemMaster::find($itemId);

        if (empty($itemMaster)) {
          return $this->sendError('Item master not found.',500);
        }


        if (array_key_exists("idItemAssigned", $input)) {
            $itemAssigneds = ItemAssigned::where('idItemAssigned', $input['idItemAssigned'])->first();
            if ($input['isAssigned'] == 1 || $input['isAssigned'] == true) {
                $input['isAssigned'] = -1;
            }

            if($input['isAssigned'] == -1 && $itemAssigneds->isAssigned == 0 && ($itemMaster->isActive == 0 || $itemMaster->itemApprovedYN == 0 )){
                return $this->sendError('Master data is deactivated. Cannot activate or assign.',500);
            }

            if($input['isActive'] == 1 && $itemAssigneds->isActive == 0 && ($itemMaster->isActive == 0 || $itemMaster->itemApprovedYN == 0)){
                return $this->sendError('Master data is deactivated. Cannot activate or assign.',500);
            }
            $itemAssigneds->isActive = $input['isActive'];
            $itemAssigneds->isAssigned = $input['isAssigned'];
            $itemAssigneds->itemMovementCategory = $input['itemMovementCategory'];
            $itemAssigneds->save();
        } else {
            
            $validatorResult = \Helper::checkCompanyForMasters($input['companySystemID'], $itemId, 'item');
            if (!$validatorResult['success']) {
                return $this->sendError($validatorResult['message']);
            }

            if ($itemMaster->isActive == 0 || $itemMaster->itemApprovedYN == 0) {
                return $this->sendError('Master data is deactivated. Cannot activate or assign.',500);
            }

            $company = Company::where('companySystemID', $input['companySystemID'])->first();
            $input['wacValueReportingCurrencyID'] = $company->reportingCurrency;
            $input['wacValueLocalCurrencyID'] = $company->localCurrencyID;
            $input['companyID'] = $company->CompanyID;
            $input['isActive'] = 1;
            $input['isAssigned'] = -1;
            $input['itemPrimaryCode'] = $input['primaryCode'];
            $input['itemUnitOfMeasure'] = $input['unit'];
            $itemAssigneds = $this->itemAssignedRepository->create($input);
        }

        return $this->sendResponse($itemAssigneds->toArray(), 'Item Assigned saved successfully');
    }

    /**
     * Display the specified ItemAssigned.
     * GET|HEAD /itemAssigneds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ItemAssigned $itemAssigned */
        $itemAssigned = $this->itemAssignedRepository->findWithoutFail($id);

        if (empty($itemAssigned)) {
            return $this->sendError('Item Assigned not found');
        }

        return $this->sendResponse($itemAssigned->toArray(), 'Item Assigned retrieved successfully');
    }

    /**
     * Update the specified ItemAssigned in storage.
     * PUT/PATCH /itemAssigneds/{id}
     *
     * @param  int $id
     * @param UpdateItemAssignedAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateItemAssignedAPIRequest $request)
    {
        $input = array_except($request->all(), ['unit', 'financeMainCategory', 'financeSubCategory', 'local_currency', 'rpt_currency']);
        $input = $this->convertArrayToSelectedValue($input,['itemMovementCategory']);

        /** @var ItemAssigned $itemAssigned */
        $itemAssigned = $this->itemAssignedRepository->findWithoutFail($id);

        if (empty($itemAssigned)) {
            return $this->sendError('Item not found');
        }

        $updateColumns = ['minimumQty', 'maximunQty', 'rolQuantity','itemMovementCategory','roQuantity'];

        $rules = [];

        if ($itemAssigned->isPOSItem == 1) {
            $updateColumns = array_merge($updateColumns, ['sellingCost', 'barcode']);
            $rules = ['sellingCost' => 'required|numeric|min:0.001'];
        }

        $updateColumns = array_only($input, $updateColumns);

        $validator = \Validator::make($updateColumns, $rules);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $itemAssigned = $this->itemAssignedRepository->update($updateColumns, $id);

        return $this->sendResponse($itemAssigned->toArray(), 'Item updated successfully');
    }

    /**
     * Remove the specified ItemAssigned from storage.
     * DELETE /itemAssigneds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ItemAssigned $itemAssigned */
        $itemAssigned = $this->itemAssignedRepository->findWithoutFail($id);

        if (empty($itemAssigned)) {
            return $this->sendError('Item Assigned not found');
        }

        $itemAssigned->delete();

        return $this->sendResponse($id, 'Item Assigned deleted successfully');
    }

    /**
     * Display a listing of the Items by company.
     * POST /getAllAssignedItemsByCompany
     *
     * @param Request $request
     * @return Response
     */

    public function getAllAssignedItemsByCompany(Request $request)
    {

        $input = $request->all();
        $itemMasters = $this->getAssignedItemsByCompanyQry($input);
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $data = \DataTables::eloquent($itemMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('idItemAssigned', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addColumn('current', function ($row) {
                $data = array('companySystemID' => $row->companySystemID,
                    'itemCodeSystem' => $row->itemCodeSystem,
                    'wareHouseId' => null);
                $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);

                $array = array('local' => $itemCurrentCostAndQty['wacValueLocal'],
                    'rpt' => $itemCurrentCostAndQty['wacValueReporting'],
                    'stock' => $itemCurrentCostAndQty['currentStockQty']);
                return $array;
            })
            ->make(true);
        return $data;
        ///return $this->sendResponse($itemMasters->toArray(), 'Item Masters retrieved successfully');*/
    }


    public function exportItemAssignedByCompanyReport(Request $request)
    {
        $input = $request->all();
        $data = array();
        $output = ($this->getAssignedItemsByCompanyQry($input))->orderBy('idItemAssigned', 'DES')->get();
        $output = $this->getCurrentCostAndQty($output);
        $type = $request->type;
        if (!empty($output)) {
            $x = 0;
            foreach ($output as $value) {
                $data[$x]['Item code'] = $value->itemPrimaryCode;
                $data[$x]['Mfg No'] = $value->secondaryItemCode;
                $data[$x]['Item Description'] = $value->itemDescription;

                if ($value->unit) {
                    $data[$x]['Unit'] = $value->unit->UnitShortCode;
                } else {
                    $data[$x]['Unit'] = '';
                }

                if ($value->financeMainCategory) {
                    $data[$x]['Main Category'] = $value->financeMainCategory->categoryDescription;
                } else {
                    $data[$x]['Main Category'] = '';
                }

                if ($value->financeSubCategory) {
                    $data[$x]['Sub Category'] = $value->financeSubCategory->categoryDescription;
                    $data[$x]['Finance BS Code'] = $value->financeSubCategory->financeGLcodebBS;
                    $data[$x]['Finance PL Code'] = $value->financeSubCategory->financeGLcodePL;
                } else {
                    $data[$x]['Sub Category'] = '';
                    $data[$x]['Finance BS Code'] = '';
                    $data[$x]['Finance PL Code'] = '';
                }

                $data[$x]['Min Qty'] = round($value->minimumQty, 2);
                $data[$x]['MAx Qty'] = round($value->maximunQty, 2);
                $data[$x]['Order level'] = $value->rolQuantity;
                $data[$x]['Total Qty'] = round($value->totalQty, 2);
                $localDecimal = 3;
                $rptDecimal = 2;
                if ($value->local_currency) {
                    $localDecimal = $value->local_currency->DecimalPlaces;
                }
                if ($value->rpt_currency) {
                    $rptDecimal = $value->rpt_currency->DecimalPlaces;
                }

                $data[$x]['WAC Value Local'] = round($value->wacValueLocal, $localDecimal);
                $data[$x]['WAC Value Rpt'] = round($value->wacValueReporting, $rptDecimal);
                $data[$x]['Category'] = $value->itemMovementCategory;
                $status = "Not Active";
                if ($value->isActive == 1) {
                    $status = "Active Only";
                }

                $data[$x]['Status'] = $status;
                $x++;
            }
        }

         \Excel::create('items_by_company', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');
    }

    public function getCurrentCostAndQty($array)
    {
        foreach ($array as $item) {
            $data = array('companySystemID' => $item->companySystemID,
                'itemCodeSystem' => $item->itemCodeSystem,
                'wareHouseId' => null);
            $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);
            $item->totalQty = $itemCurrentCostAndQty['currentStockQty'];
            $item->wacValueLocal = $itemCurrentCostAndQty['wacValueLocal'];
            $item->wacValueReporting = $itemCurrentCostAndQty['wacValueReporting'];
        }

        return $array;
    }

    public function getAssignedItemsByCompanyQry($request)
    {
        $input = $request;
        $input = $this->convertArrayToSelectedValue($input, array('financeCategoryMaster', 'financeCategorySub', 'isActive'));

        $companyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $itemMasters = ItemAssigned::with(['unit', 'financeMainCategory', 'financeSubCategory', 'local_currency', 'rpt_currency'])
            ->whereIn('companySystemID', $childCompanies)
            ->where('financeCategoryMaster', 1);

        if (array_key_exists('isPOSItem', $input)) {
            if ($input['isPOSItem'] > 0 && !is_null($input['isPOSItem'])) {
                $itemMasters->where('isPOSItem', 1);
            }
        }

        if (array_key_exists('financeCategoryMaster', $input)) {
            if ($input['financeCategoryMaster'] > 0 && !is_null($input['financeCategoryMaster'])) {
                $itemMasters->where('financeCategoryMaster', $input['financeCategoryMaster']);
            }
        }

        if (array_key_exists('financeCategorySub', $input)) {
            if ($input['financeCategorySub'] > 0 && !is_null($input['financeCategorySub'])) {
                $itemMasters->where('financeCategorySub', $input['financeCategorySub']);
            }
        }

        if (array_key_exists('isActive', $input)) {
            if (($input['isActive'] == 0 || $input['isActive'] == 1) && !is_null($input['isActive'])) {
                $itemMasters->where('isActive', $input['isActive']);
            }
        }
        if (array_key_exists('itemApprovedYN', $input)) {
            if (($input['itemApprovedYN'] == 0 || $input['itemApprovedYN'] == 1) && !is_null($input['itemApprovedYN'])) {
                $itemMasters->where('itemApprovedYN', $input['itemApprovedYN']);
            }
        }

        $search = $input['search']['value'];
        if ($search) {
            $itemMasters = $itemMasters->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }
        return $itemMasters;
    }

    public function getAllNonPosItemsByCompany(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('financeCategoryMaster', 'financeCategorySub', 'isActive'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $companyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $itemMasters = ItemAssigned::with(['unit', 'financeMainCategory', 'financeSubCategory', 'local_currency', 'rpt_currency'])
            ->whereIn('companySystemID', $childCompanies)
            ->where('financeCategoryMaster', 1)
            ->where('isAssigned', -1)
            ->where('isActive', 1)
            ->where('isPOSItem', 0);


        if (array_key_exists('financeCategorySub', $input)) {
            if ($input['financeCategorySub'] > 0 && !is_null($input['financeCategorySub'])) {
                $itemMasters->where('financeCategorySub', $input['financeCategorySub']);
            }
        }

        $search = $input['search']['value'];
        if ($search) {
            $itemMasters = $itemMasters->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }


        $data = \DataTables::eloquent($itemMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('idItemAssigned', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
        return $data;
    }

    public function savePullItemsFromInventory(Request $request)
    {

        $input = $request->all();

        $messages = array(
            'pullList.required'   => 'Select the items.',
        );

        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'pullList' => 'required'
        ],$messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $isGroup = \Helper::checkIsCompanyGroup($input['companySystemID']);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($input['companySystemID']);
        } else {
            $childCompanies = [$input['companySystemID']];
        }

        if (isset($input['isCheck']) && $input['isCheck']) {
            $itemMasters = ItemAssigned::whereIn('companySystemID', $childCompanies)
                ->where('financeCategoryMaster', 1)
                ->where('isAssigned', -1)
                ->where('isActive', 1)
                ->where('isPOSItem', 0);

            if (isset($input['appliedFilter'])) {
                if (array_key_exists('financeCategorySub', $input)) {
                    if ($input['financeCategorySub'] > 0 && !is_null($input['appliedFilter']['financeCategorySub'])) {
                        $itemMasters->where('financeCategorySub', $input['appliedFilter']['financeCategorySub']);
                    }
                }
                $search = $input['appliedFilter']['search']['value'];
                if ($search) {
                    $itemMasters = $itemMasters->where(function ($query) use ($search) {
                        $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                            ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%")
                            ->orWhere('itemDescription', 'LIKE', "%{$search}%");
                    });
                }
            }

            $input['pullList'] = collect($itemMasters->get())->pluck('idItemAssigned')->toArray();
        }

        foreach ($input['pullList'] as $id) {
            $itemAssigned = $this->itemAssignedRepository->findWithoutFail($id);
            if (!empty($itemAssigned)) {
                $this->itemAssignedRepository->update(['isPOSItem' => 1], $id);
                $itemMaster = $this->itemMasterRepository->findWithoutFail($itemAssigned->itemCodeSystem);
                if (!empty($itemMaster)) {
                    $this->itemMasterRepository->update(['isPOSItem' => 1], $itemAssigned->itemCodeSystem);
                }
            }
        }

        return $this->sendResponse($input['pullList'], 'Successfully pulled items from inventory');

    }

    public function getItemsByMainCategoryAndSubCategory(Request $request)
    {
        $input = $request->all();

        $isGroup = \Helper::checkIsCompanyGroup($input['selectedCompanyId']);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($input['selectedCompanyId']);
        } else {
            $childCompanies = [$input['selectedCompanyId']];
        }

        $mainCategoryIds = (isset($input['mainCategory'])) ? collect($input['mainCategory'])->pluck('id') : [];
        $subCategoryIds = (isset($input['subCategory'])) ? collect($input['subCategory'])->pluck('id') : [];


        $itemMasters = ItemAssigned::whereIn('companySystemID', $childCompanies)
                                    ->where('isAssigned', -1)
                                    ->where('isActive', 1)
                                    ->when(sizeof($mainCategoryIds) > 0, function($query) use ($mainCategoryIds) {
                                        $query->whereIn('financeCategoryMaster', $mainCategoryIds);
                                    })
                                    ->when(sizeof($subCategoryIds) > 0, function($query) use ($subCategoryIds) {
                                        $query->whereIn('financeCategorySub', $subCategoryIds);
                                    })
                                    ->get();


        return $this->sendResponse($itemMasters, 'Successfully items retrieved');

    }


    public function reOrderTest()
    {   

        $companies = Company::where('isActive',true)->pluck('companySystemID');
        $details = [];
        foreach($companies as $companyID)
        {
            $records = DB::table("itemassigned")
            ->selectRaw("itemDescription, itemCodeSystem, rolQuantity, IFNULL(ledger.INoutQty,0) as INoutQty,itemPrimaryCode,secondaryItemCode")
            ->join(DB::raw('(SELECT itemSystemCode, SUM(inOutQty) as INoutQty FROM erp_itemledger WHERE companySystemID = ' . $companyID . ' GROUP BY itemSystemCode) as ledger'), function ($query) {
                $query->on('itemassigned.itemCodeSystem', '=', 'ledger.itemSystemCode');
            })
            ->where('companySystemID', '=', $companyID)
            ->where('financeCategoryMaster', '=', 1)
            ->where('isActive', '=', 1)
           // ->where('roQuantity', '>', 0)
            ->whereRaw('ledger.INoutQty <= rolQuantity')->get();



            //purchase request start


            $company = Company::where('companySystemID', $companyID)->first();

            $request_data['PRRequestedDate'] = now();

            $id = Auth::id();
            $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
    
            $request_data['createdPcID'] = gethostname();
            $request_data['createdUserID'] = $user->employee['empID'];
            $request_data['createdUserSystemID'] = $user->employee['employeeSystemID'];
            
            $currency = (isset($company)) ? $company->localCurrencyID : 0;
            $request_data['currency'] = $currency;
            $companyFinanceYear = \Helper::companyFinanceYear($companyID);
                            
            $budger_year_id = 1;
            if(isset($companyFinanceYear))
            {
                $budger_year_id = $companyFinanceYear[0]->companyFinanceYearID;
            }           
            $request_data['budgetYearID'] = $budger_year_id; 
            $request_data['prBelongsYearID'] = $budger_year_id;  
            
            $budget_year = '';
            $checkCompanyFinanceYear = CompanyFinanceYear::find($budger_year_id);
            if ($checkCompanyFinanceYear) {
                $budget_year = Carbon::parse($checkCompanyFinanceYear->bigginingDate)->format('Y');
                $request_data['budgetYear'] = Carbon::parse($checkCompanyFinanceYear->bigginingDate)->format('Y');
                $request_data['prBelongsYear'] = Carbon::parse($checkCompanyFinanceYear->bigginingDate)->format('Y');
            }

            $request_data['documentSystemID'] = 1;  
            $request_data['companySystemID'] = $companyID;        
            $serivice_line_id = 1;
            $request_data['serviceLineSystemID'] = $serivice_line_id;     
            $segment = SegmentMaster::where('serviceLineSystemID', $serivice_line_id)->first();
            if ($segment) {
                $serivice_line_code = $segment->ServiceLineCode;
                $request_data['serviceLineCode'] = $segment->ServiceLineCode;
            }

            $request_data['serviceLineSystemID'] = 1;  
            $request_data['comments'] = 'System Auto-Generated - Auto Re-order Purchase';  
            $request_data['location'] = 1;  
            $request_data['priority'] = 1;  

            $request_data['departmentID'] = 'PROC';  
            $doc_id = 'PR';
            $request_data['documentID'] = $doc_id;
         

            $lastSerial = PurchaseRequest::where('companySystemID', $companyID)
            ->where('documentSystemID', 1)
            ->orderBy('purchaseRequestID', 'desc')
            ->first();
       
            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
            }
            
            $company_id = '';
            if ($company) {
                $company_id = $company->CompanyID;
                $request_data['companyID'] = $company->CompanyID;
            }
         
            $dep_id = 'PROC';
            $code = str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT);
            $request_data['purchaseRequestCode'] = $company_id . '\\' . $dep_id . '\\' . $serivice_line_code . '\\' . $doc_id . $code;
            
            $request_data['serialNumber'] = $lastSerialNumber;
             $companyDocumentAttachment = CompanyDocumentAttachment::where('companySystemID', $companyID)
                ->where('documentSystemID', 1)
                ->first();

            if ($companyDocumentAttachment) {
                $request_data['docRefNo'] = $companyDocumentAttachment->docRefNumber;
            }

            $new_purchaseRequests = $this->purchaseRequestRepository->create($request_data);
            //purchase request end

            // item details echekc start

            $succes_item = 0;
            $valid_items = [];


                    
    

            foreach($records as $itemVal)
            {   
                
           
           
                $is_failed= false;

                $allowItemToTypePolicy = false;
                $itemNotound = false;
                $companySystemID = $companyID;
                

          

                $request_data_details['itemCode'] = $itemVal->itemCodeSystem;
                $item = ItemAssigned::where('itemCodeSystem', $itemVal->itemCodeSystem)
                ->where('companySystemID', $companySystemID)
                ->first();
    

          
                if (empty($item)) {
                    if (!$allowItemToTypePolicy) {
                        //return $this->sendError('Item not found');
                        $is_failed= true;
                       // continue;
                    } else {
                        $itemNotound = true;
                    }
                }

           
                // $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $itemVal->purchaseRequestID)
                // ->first();

        

                $request_data_details['budgetYear'] = $budget_year;
                $request_data_details['itemPrimaryCode'] = $itemVal->itemPrimaryCode;
                $request_data_details['itemDescription'] = $itemVal->itemDescription;
                $request_data_details['partNumber'] = $itemVal->itemPrimaryCode;
                // $request_data_details['itemFinanceCategoryID'] = $itemVal->itemFinanceCategoryID;
                // $request_data_details['itemFinanceCategorySubID'] = $itemVal->itemFinanceCategorySubID;

        
                //start
        
                if (!$itemNotound) {

                    
             
                    $currencyConversion = \Helper::currencyConversion($companySystemID, $item->wacValueLocalCurrencyID, $currency, $item->wacValueLocal);
                    
                  
             
                    $request_data_details['estimatedCost'] =$currencyConversion['documentAmount'];
                    $request_data_details['companySystemID'] = $item->companySystemID;
                    $request_data_details['companyID'] = $item->companyID;
                    $request_data_details['unitOfMeasure'] = $item->itemUnitOfMeasure;
                    $request_data_details['maxQty'] = $item->maximunQty;
                    $request_data_details['minQty'] = $item->minimumQty;
                    

                 
                    $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
                        ->where('mainItemCategoryID', $item->financeCategoryMaster)
                        ->where('itemCategorySubID', $item->financeCategorySub)
                        ->first();
        
                    if (empty($financeItemCategorySubAssigned)) {
                        $is_failed= true;
                        //continue;
                       // return $this->sendError('Finance category not assigned for the selected item.');
                    }
              
                    $request_data_details['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                    $request_data_details['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                
                    if ($item->financeCategoryMaster == 3) {
                        $assetCategory = AssetFinanceCategory::find($item->faFinanceCatID);
                        if (!$assetCategory) {
                            $is_failed= true;
                           // continue;
                            //return $this->sendError('Asset category not assigned for the selected item.');
                        }
                        $request_data_details['financeGLcodePLSystemID'] = $assetCategory->COSTGLCODESystemID;
                        $request_data_details['financeGLcodePL'] = $assetCategory->COSTGLCODE;
                    } else {
                        $request_data_details['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                        $request_data_details['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                    }
                    
                    $request_data_details['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
                    

                    $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                            ->where('companySystemID', $companySystemID)
                            ->first();

                     
                    if ($allowFinanceCategory) {
                        $policy = $allowFinanceCategory->isYesNO;
        
                        if ($policy == 0) {
                            if ($new_purchaseRequests->financeCategory == null || $new_purchaseRequests->financeCategory == 0) {
                                $is_failed= true;
                                //continue;
                               // return $this->sendError('Category is not found.', 500);
                            }
        
                            //checking if item category is same or not
                            $pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                                ->where('purchaseRequestID', $new_purchaseRequests->purchaseRequestID)
                                ->first();
        
                            if ($pRDetailExistSameItem) {
                                if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                                    $is_failed= true;
                                   // continue;
                                   // return $this->sendError('You cannot add different category item', 500);
                                }
                            }
                        }
                    }
           
                 
                      // check policy 18
        
                    $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
                        ->where('companySystemID', $companySystemID)
                        ->first();
              
                    if ($allowPendingApproval && $item->financeCategoryMaster == 1) {
                        
                  
                        if ($allowPendingApproval->isYesNO == 0) {
        
                            $checkWhether = PurchaseRequest::where('purchaseRequestID', '!=', $new_purchaseRequests->purchaseRequestID)
                                ->where('companySystemID', $companySystemID)
                                ->where('serviceLineSystemID', $new_purchaseRequests->serviceLineSystemID)
                                ->select([
                                    'erp_purchaserequest.purchaseRequestID',
                                    'erp_purchaserequest.companySystemID',
                                    'erp_purchaserequest.serviceLineCode',
                                    'erp_purchaserequest.purchaseRequestCode',
                                    'erp_purchaserequest.PRConfirmedYN',
                                    'erp_purchaserequest.approved',
                                    'erp_purchaserequest.cancelledYN'
                                ])
                                ->groupBy(
                                    'erp_purchaserequest.purchaseRequestID',
                                    'erp_purchaserequest.companySystemID',
                                    'erp_purchaserequest.serviceLineCode',
                                    'erp_purchaserequest.purchaseRequestCode',
                                    'erp_purchaserequest.PRConfirmedYN',
                                    'erp_purchaserequest.approved',
                                    'erp_purchaserequest.cancelledYN'
                                );

                         
                                
                            $anyPendingApproval = $checkWhether->whereHas('details', function ($query) use ($companySystemID, $new_purchaseRequests, $item) {
                                $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                               ->where('manuallyClosed', 0);
                          
                            })
                                ->where('approved', 0)
                                ->where('cancelledYN', 0)
                                ->first();
                            /* approved=0 And cancelledYN=0*/

                        
        
                            if (!empty($anyPendingApproval)) {
                                $is_failed= true;
                               // continue;
                                //return $this->sendError("There is a purchase request (" . $anyPendingApproval->purchaseRequestCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                            }
                            
                     
                        
                            $anyApprovedPRButPONotProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $new_purchaseRequests->purchaseRequestID)
                                ->where('companySystemID', $companySystemID)
                                ->where('serviceLineSystemID', $new_purchaseRequests->serviceLineSystemID)
                                ->select([
                                    'erp_purchaserequest.purchaseRequestID',
                                    'erp_purchaserequest.companySystemID',
                                    'erp_purchaserequest.serviceLineCode',
                                    'erp_purchaserequest.purchaseRequestCode',
                                    'erp_purchaserequest.PRConfirmedYN',
                                    'erp_purchaserequest.approved',
                                    'erp_purchaserequest.cancelledYN'
                                ])
                                ->groupBy(
                                    'erp_purchaserequest.purchaseRequestID',
                                    'erp_purchaserequest.companySystemID',
                                    'erp_purchaserequest.serviceLineCode',
                                    'erp_purchaserequest.purchaseRequestCode',
                                    'erp_purchaserequest.PRConfirmedYN',
                                    'erp_purchaserequest.approved',
                                    'erp_purchaserequest.cancelledYN'
                                )
                                ->whereHas('details', function ($query) use ($companySystemID, $new_purchaseRequests, $item) {
                                    $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                        ->where('selectedForPO', 0)
                                        ->where('prClosedYN', 0)
                                        ->where('fullyOrdered', 0)
                                        ->where('manuallyClosed', 0);
        
                                })
                                ->where('approved', -1)
                                ->where('cancelledYN', 0)
                                ->first();
                            /* approved=-1 And cancelledYN=0 And selectedForPO=0 And prClosedYN=0 And fullyOrdered=0*/
                            // return $this->sendResponse($anyApprovedPRButPONotProcessed, 'successfully export');
                            // die();
                            if (!empty($anyApprovedPRButPONotProcessed)) {
                                $is_failed= true;
                                //continue;
                               // return $this->sendError("There is a purchase request (" . $anyApprovedPRButPONotProcessed->purchaseRequestCode . ") approved hense PO is not processed for the item you are trying to add. Please check again", 500);
                            }
                         
                            $anyApprovedPRButPOPartiallyProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $new_purchaseRequests->purchaseRequestID)
                                ->where('companySystemID', $companySystemID)
                                ->where('serviceLineSystemID', $new_purchaseRequests->serviceLineSystemID)
                                ->select([
                                    'erp_purchaserequest.purchaseRequestID',
                                    'erp_purchaserequest.companySystemID',
                                    'erp_purchaserequest.serviceLineCode',
                                    'erp_purchaserequest.purchaseRequestCode',
                                    'erp_purchaserequest.PRConfirmedYN',
                                    'erp_purchaserequest.approved',
                                    'erp_purchaserequest.cancelledYN'
                                ])
                                ->groupBy(
                                    'erp_purchaserequest.purchaseRequestID',
                                    'erp_purchaserequest.companySystemID',
                                    'erp_purchaserequest.serviceLineCode',
                                    'erp_purchaserequest.purchaseRequestCode',
                                    'erp_purchaserequest.PRConfirmedYN',
                                    'erp_purchaserequest.approved',
                                    'erp_purchaserequest.cancelledYN'
                                )->whereHas('details', function ($query) use ($companySystemID, $new_purchaseRequests, $item) {
                                    $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                        ->where('selectedForPO', 0)
                                        ->where('prClosedYN', 0)
                                        ->where('fullyOrdered', 1)
                                        ->where('manuallyClosed', 0);
                                
                                })
                                ->where('approved', -1)
                                ->where('cancelledYN', 0)
                                ->first();
                            /* approved=-1 And cancelledYN=0 And selectedForPO=0 And prClosedYN=0 And fullyOrdered=1*/
        
                            if (!empty($anyApprovedPRButPOPartiallyProcessed)) {
                                $is_failed= true;
                               // continue;
                               // return $this->sendError("There is a purchase request (" . $anyApprovedPRButPOPartiallyProcessed->purchaseRequestCode . ") approved and PO is partially processed for the item you are trying to add. Please check again", 500);
                            }
        
                            /* PO check*/
                       
                            $checkPOPending = ProcumentOrder::where('companySystemID', $companySystemID)
                                ->where('serviceLineSystemID', $new_purchaseRequests->serviceLineSystemID)
                                ->whereHas('detail', function ($query) use ($item) {
                                    $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                           ->where('manuallyClosed', 0);
                                })
                                ->where('approved', 0)
                                ->where('poCancelledYN', 0)
                                ->first();
                             
                       
                            if (!empty($checkPOPending)) {
                                $is_failed= true;
                               // continue;
                               // return $this->sendError("There is a purchase order (" . $checkPOPending->purchaseOrderCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                            }
                            /* PO --> approved=-1 And cancelledYN=0 */
                          
                        }
                    }
              
              
                    $group_companies = Helper::getSimilarGroupCompanies($companySystemID);
                    $poQty = PurchaseOrderDetails::whereHas('order', function ($query) use ($group_companies) {
                        $query->whereIn('companySystemID', $group_companies)
                            ->where('approved', -1)
                            ->where('poType_N', '!=',5)// poType_N = 5 =>work order
                            ->where('poCancelledYN', 0)
                            ->where('manuallyClosed', 0);
                         })
                        ->where('itemCode', $itemVal->itemCodeSystem)
                        ->where('manuallyClosed',0)
                        ->groupBy('erp_purchaseorderdetails.itemCode')
                        ->select(
                            [
                                'erp_purchaseorderdetails.companySystemID',
                                'erp_purchaseorderdetails.itemCode',
                                'erp_purchaseorderdetails.itemPrimaryCode'
                            ]
                        )
                        ->sum('noQty');

                    
                                
                    $quantityInHand = ErpItemLedger::where('itemSystemCode', $itemVal->itemCodeSystem)
                        ->where('companySystemID', $companySystemID)
                        ->groupBy('itemSystemCode')
                        ->sum('inOutQty');
        
                    $grvQty = GRVDetails::whereHas('grv_master', function ($query) use ($group_companies) {
                        $query->whereIn('companySystemID', $group_companies)
                            ->where('grvTypeID', 2)
                            ->where('approved', -1)
                            ->groupBy('erp_grvmaster.companySystemID');
                    })->whereHas('po_detail', function ($query){
                        $query->where('manuallyClosed',0)
                        ->whereHas('order', function ($query){
                            $query->where('manuallyClosed',0);
                        });
                    })
                        ->where('itemCode', $itemVal->itemCodeSystem)
                        ->groupBy('erp_grvdetails.itemCode')
                        ->select(
                            [
                                'erp_grvdetails.companySystemID',
                                'erp_grvdetails.itemCode'
                            ])
                        ->sum('noQty');
        
                    $quantityOnOrder = $poQty - $grvQty;
                    $request_data_details['poQuantity'] = $poQty;
                    $request_data_details['quantityOnOrder'] = $quantityOnOrder;
                    $request_data_details['quantityInHand'] = $quantityInHand;
               
         
        
                } else {
                    $request_data_details['estimatedCost'] = 0;
                    $request_data_details['companySystemID'] = $companySystemID;
                    $request_data_details['companyID'] = $new_purchaseRequests->companyID;
                    $request_data_details['unitOfMeasure'] = null;
                    $request_data_details['maxQty'] = 0;
                    $request_data_details['minQty'] = 0;
                    $request_data_details['poQuantity'] = 0;
                    $request_data_details['quantityOnOrder'] = 0;
                    $request_data_details['quantityInHand'] = 0;
                    $request_data_details['itemCode'] = null;
                }

                return $this->sendResponse($request_data_details, 'Purchase ');
                die();
         
                $request_data_details['quantityRequested'] = 2;  
                $request_data_details['totalCost'] = 30;  
                $request_data_details['comments'] = 'generated pr';  
                $request_data_details['itemCategoryID'] = 0;
                $request_data_details['isMRPulled'] = $itemVal->isMRPulled;  
           

                if(!$is_failed)
                {
                    $succes_item++;
                    array_push($valid_items,$request_data_details);
                   // 
                }
               
          
          
            }


                //end


            return $this->sendResponse($records, 'Successfully items retrieved');
            die();

            array_push($details,$records);
            

           // ->whereRaw('ledger.INoutQty <= rolQuantity')->get();
        }





        return $this->sendResponse($details, 'Successfully items retrieved');

    }
}
