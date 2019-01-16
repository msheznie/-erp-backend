<?php
/**
 * =============================================
 * -- File Name : ItemMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Item Master
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Item Master
 * -- REVISION HISTORY
 * -- Date: 14-March 2018 By: Fayas Description: Added new functions named as getAllItemsMaster(),getItemMasterFormData(),
 * updateItemMaster(),getAssignedCompaniesByItem()
 * -- Date: 03-April 2018 By: Mubashir Description: Added a new function getAllItemsMasterApproval() to display items to be approved
 * -- Date: 10-April 2018 By: Fayas Description: Added a new function itemMasterBulkCreate().
 * -- Date: 05-June 2018 By: Mubashir Description: Modified getAllItemsMaster() to handle filters from local storage
 * -- Date: 17-July 2018 By: Fayas Description: Added new functions named as getItemMasterAudit()
 * -- Date: 30-October 2018 By: Fayas Description: Added new functions named as exportItemMaster()
 * -- Date: 14-December 2018 By: Fayas Description: Added new functions named as itemReferBack()
 * -- Date: 11-January 2019 By: Fayas Description: Added new functions named as getPosItemSearch()
 */


namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemMasterAPIRequest;
use App\Http\Requests\API\UpdateItemMasterAPIRequest;
use App\Models\DocumentApproved;
use App\Models\DocumentReferedHistory;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemMaster;
use App\Models\Company;
use App\Models\FinanceItemCategoryMaster;
use App\Models\FinanceItemCategorySub;
use App\Models\DocumentMaster;
use App\Models\ItemAssigned;
use App\Models\ItemMasterRefferedBack;
use App\Models\Unit;
use App\Models\WarehouseBinLocation;
use App\Models\YesNoSelection;
use App\Repositories\ItemMasterRepository;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class ItemMasterController
 * @package App\Http\Controllers\API
 */
class ItemMasterAPIController extends AppBaseController
{
    /** @var  ItemMasterRepository */
    private $itemMasterRepository;
    private $userRepository;

    public function __construct(ItemMasterRepository $itemMasterRepo, UserRepository $userRepo)
    {
        $this->itemMasterRepository = $itemMasterRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the ItemMaster.
     * GET|HEAD /itemMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->itemMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->itemMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemMasters = $this->itemMasterRepository->all();

        return $this->sendResponse($itemMasters->toArray(), 'Item Masters retrieved successfully');
    }

    /**
     * Item Master Bulk Create.
     * POST|HEAD /itemMasterBulkCreate
     *
     * @param Request $request
     * @return Response
     */
    public function itemMasterBulkCreate(Request $request)
    {

        $input = $request->all();
        //$input = $this->convertArrayToValue($input);
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];

        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();

        if (empty($company)) {
            return $this->sendError('Primary Company not found');
        }

        $document = DocumentMaster::where('documentID', 'ITMM')->first();

        $financeCategoryMaster = FinanceItemCategoryMaster::where('itemCategoryID', $input['financeCategoryMaster'])->first();

        if (empty($financeCategoryMaster)) {
            return $this->sendError('Finance Item Category not found');
        }

        $runningSerialOrder = $financeCategoryMaster->lastSerialOrder;
        $code = $financeCategoryMaster->itemCodeDef;
        $count = $financeCategoryMaster->numberOfDigits;
        $createdItems = array();
        DB::beginTransaction();
        try {
            foreach ($input['items'] as $item) {

                $partNo = isset($item['secondaryItemCode']) ? $item['secondaryItemCode'] : '';

                $messages = array('secondaryItemCode.unique' => 'Mfg. Part No ' . $partNo . ' already exists');
                $validator = \Validator::make((array)$item, ['secondaryItemCode' => 'unique:itemmaster'], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                $runningSerialOrder = $runningSerialOrder + 1;
                $primaryCode = $code . str_pad($runningSerialOrder, $count, '0', STR_PAD_LEFT);

                $item['runningSerialOrder'] = $runningSerialOrder;
                $item['primaryCode'] = $primaryCode;
                $item['primaryItemCode'] = $code;

                $financeCategorySub = FinanceItemCategorySub::where('itemCategorySubID', $item['financeCategorySub'])->first();

                if (empty($financeCategorySub)) {
                    return $this->sendError('Finance Item Sub Category not found');
                }

                if ($document) {
                    $item['documentSystemID'] = $document->documentSystemID;
                    $item['documentID'] = $document->documentID;
                }

                $item['isActive'] = 1;
                $item['createdPcID'] = gethostname();
                $item['createdUserID'] = $empId;
                $item['itemShortDescription'] = $item['itemDescription'];
                $item['primaryCompanyID'] = $company->CompanyID;
                $item['primaryCompanySystemID'] = $input['primaryCompanySystemID'];
                $item['financeCategoryMaster'] = $input['financeCategoryMaster'];

                $itemMaster = $this->itemMasterRepository->create($item);

                if ($input['itemConfirmedYN'] == true) {
                    $params = array('autoID' => $itemMaster->itemCodeSystem, 'company' => $item["primaryCompanySystemID"], 'document' => $item["documentSystemID"]);
                    $confirm = \Helper::confirmDocument($params);
                    if (!$confirm["success"]) {
                        return $this->sendError($confirm["message"], 500);
                    }
                }
                array_push($createdItems, $itemMaster);
            }

            $financeCategoryMaster->lastSerialOrder = $runningSerialOrder;
            $financeCategoryMaster->modifiedPc = gethostname();
            $financeCategoryMaster->modifiedUser = $empId;
            $financeCategoryMaster->save();

            DB::commit();

            return $this->sendResponse($createdItems, 'Item Master saved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
        }

    }

    /**
     * Display a listing of the ItemMaster.
     * POST /getAllItemsMaster
     *
     * @param Request $request
     * @return Response
     */

    public function getAllItemsMaster(Request $request)
    {

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $search = $request->input('search.value');
        $itemMasters = ($this->getAllItemsQry($input, $search));

        return \DataTables::eloquent($itemMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemCodeSystem', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
        ///return $this->sendResponse($itemMasters->toArray(), 'Item Masters retrieved successfully');*/
    }

    public function exportItemMaster(Request $request)
    {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $search = $request->input('search.value');
        $items = ($this->getAllItemsQry($input, $search))->orderBy('itemCodeSystem', $sort)->get();
        $type = $request->get('type');
        if ($items) {
            $x = 0;
            foreach ($items as $val) {
                $data[$x]['Item Code'] = $val['primaryCode'];
                $data[$x]['Part Number'] = $val['secondaryItemCode'];
                $data[$x]['Item Description'] = $val['itemDescription'];

                if ($val['unit_by']) {
                    $data[$x]['UOM'] = $val['unit_by']['UnitShortCode'];
                } else {
                    $data[$x]['UOM'] = '-';
                }

                if ($val['financeMainCategory']) {
                    $data[$x]['Category'] = $val['financeMainCategory']['categoryDescription'];
                } else {
                    $data[$x]['Category'] = '-';
                }

                if ($val['financeSubCategory']) {
                    $data[$x]['Sub Category'] = $val['financeSubCategory']['categoryDescription'];
                    $data[$x]['Gl Code'] = $val['financeSubCategory']['financeGLcodePL'];
                } else {
                    $data[$x]['Sub Category'] = '-';
                    $data[$x]['Gl Code'] = '-';
                }
                $x++;
            }
        } else {
            $data = array();
        }

        \Excel::create('item_master', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        ///return $this->sendResponse($itemMasters->toArray(), 'Item Masters retrieved successfully');*/
    }

    public function getAllItemsQry($request, $search)
    {

        $input = $request;
        $input = $this->convertArrayToSelectedValue($input, array('financeCategoryMaster', 'financeCategorySub', 'isActive', 'itemApprovedYN', 'itemConfirmedYN'));

        $companyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $itemMasters = ItemMaster::with(['unit', 'unit_by', 'financeMainCategory', 'financeSubCategory']);
        //->whereIn('primaryCompanySystemID',$childCompanies);

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

        if (array_key_exists('itemConfirmedYN', $input)) {
            if (($input['itemConfirmedYN'] == 0 || $input['itemConfirmedYN'] == 1) && !is_null($input['itemConfirmedYN'])) {
                $itemMasters->where('itemConfirmedYN', $input['itemConfirmedYN']);
            }
        }

        if ($search) {
            $itemMasters = $itemMasters->where(function ($query) use ($search) {
                $query->where('primaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }

        return $itemMasters;
    }

    /**
     * Display items from ItemMaster for approval.
     * POST /getAllItemsMasterApproval
     *
     * @param Request $request
     * @return Response
     */

    public function getAllItemsMasterApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request->selectedCompanyID;

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companyID = \Helper::getGroupCompany($companyId);
        } else {
            $companyID = [$companyId];
        }


        $empID = \Helper::getEmployeeSystemID();
        $search = $request->input('search.value');
        $itemMasters = DB::table('erp_documentapproved')->select('itemmaster.*', 'erp_documentapproved.documentApprovedID', 'financeitemcategorymaster.categoryDescription as financeitemcategorydescription', 'financeitemcategorysub.categoryDescription as financeitemcategorysubdescription', 'units.UnitShortCode', 'rollLevelOrder', 'financeGLcodePL', 'approvalLevelID', 'documentSystemCode')->join('employeesdepartments', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID')
                ->where('employeesdepartments.documentSystemID', 57)
                ->whereIn('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID);
        })
            ->join('itemmaster', function ($query) use ($companyID, $empID, $search) {
                $query->on('itemCodeSystem', '=', 'documentSystemCode')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->whereIn('itemmaster.primaryCompanySystemID', $companyID)
                    ->where('itemApprovedYN', 0)
                    ->when($search != "", function ($q) use ($search) {
                        $q->where(function ($query) use ($search) {
                            $query->where('primaryCode', 'LIKE', "%{$search}%")
                                ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%")
                                ->orWhere('itemDescription', 'LIKE', "%{$search}%");
                        });
                    });
            })
            ->leftJoin('units', 'UnitID', '=', 'unit')
            ->leftJoin('financeitemcategorymaster', 'itemCategoryID', '=', 'financeCategoryMaster')
            ->leftJoin('financeitemcategorysub', 'itemCategorySubID', '=', 'financeCategorySub')
            ->where('erp_documentapproved.approvedYN', 0)
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 57)
            ->whereIn('erp_documentapproved.companySystemID', $companyID);

        return \DataTables::of($itemMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    /**
     * get form data for Item Master.
     * GET /getItemMasterFormData
     *
     * @param Request $request
     * @return Response
     */
    public function getItemMasterFormData(Request $request)
    {

        $selectedCompanyId = $request['selectedCompanyId'];

        $masterCompany = Company::where("companySystemID", $selectedCompanyId)->first();

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            //$subCompanies = \Helper::getGroupCompany($selectedCompanyId);
            $subCompanies = \Helper::getSubCompaniesByGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        /**  Companies by group  Drop Down */
        $companiesByGroup = Company::whereIn("companySystemID", $subCompanies)->get();

        /** all Company  Drop Down */
        $allCompanies = Company::whereIn("companySystemID", $subCompanies)->get();

        /** all FinanceItemCategoryMaster Drop Down */
        $itemCategory = FinanceItemCategoryMaster::all();

        /** all FinanceItemCategorySub Drop Down */
        $itemCategorySub = FinanceItemCategorySub::all();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $units = Unit::all();

        $wareHouseBinLocations = [];
        if (isset($request['warehouseSystemCode'])) {
            $wareHouseBinLocations = WarehouseBinLocation::where('companySystemID', $selectedCompanyId)
                ->where('wareHouseSystemCode', $request['warehouseSystemCode'])
                ->get();
        }


        $output = array('companiesByGroup' => $companiesByGroup,
            'allCompanies' => $allCompanies,
            'financeItemCategoryMaster' => $itemCategory,
            'financeItemCategorySub' => $itemCategorySub,
            'yesNoSelection' => $yesNoSelection,
            'units' => $units,
            'wareHouseBinLocations' => $wareHouseBinLocations
        );

        return $this->sendResponse($output, 'Record retrieved successfully');

    }

    /**
     * Store a newly created ItemMaster in storage.
     * POST /itemMasters
     *
     * @param CreateItemMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateItemMasterAPIRequest $request)
    {


        $input = $request->all();

        $partNo = isset($input['secondaryItemCode']) ? $input['secondaryItemCode'] : '';

        $messages = array('secondaryItemCode.unique' => 'Mfg. Part No ' . $partNo . ' already exists');
        $validator = \Validator::make($input, ['secondaryItemCode' => 'unique:itemmaster'], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $empId = $user->employee['empID'];
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $empId;

        $financeCategoryMaster = FinanceItemCategoryMaster::where('itemCategoryID', $input['financeCategoryMaster'])->first();

        $runningSerialOrder = $financeCategoryMaster->lastSerialOrder + 1;
        $code = $financeCategoryMaster->itemCodeDef;
        $count = $financeCategoryMaster->numberOfDigits;
        $primaryCode = $code . str_pad($runningSerialOrder, $count, '0', STR_PAD_LEFT);
        $input['runningSerialOrder'] = $runningSerialOrder;
        $input['primaryCode'] = $primaryCode;
        $input['primaryItemCode'] = $code;
        $financeCategorySub = FinanceItemCategorySub::where('itemCategorySubID', $input['financeCategorySub'])->first();
        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();
        $input['primaryCompanyID'] = $company->CompanyID;
        $document = DocumentMaster::where('documentID', 'ITMM')->first();
        $input['documentSystemID'] = $document->documentSystemID;
        $input['documentID'] = $document->documentID;
        $input['isActive'] = 1;

        $itemMasters = $this->itemMasterRepository->create($input);

        $financeCategoryMaster->lastSerialOrder = $runningSerialOrder;
        $financeCategoryMaster->modifiedPc = gethostname();
        $financeCategoryMaster->modifiedUser = $empId;
        $financeCategoryMaster->save();

        return $this->sendResponse($itemMasters->toArray(), 'Item Master saved successfully');
    }

    /**
     * Update the specified ItemMaster in storage.
     * PUT/PATCH /updateItemMaster
     *
     * @param Request $request
     *
     * @return Response
     */
    public function updateItemMaster(Request $request)
    {
        $input = $request->all();
        $partNo = isset($input['secondaryItemCode']) ? $input['secondaryItemCode'] : '';
        $messages = array('secondaryItemCode.unique' => 'Mfg. Part No ' . $partNo . ' already exists');
        $validator = \Validator::make($input, [
            'secondaryItemCode' => Rule::unique('itemmaster')->ignore($input['itemCodeSystem'], 'itemCodeSystem')
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $empId;
        $empName = $user->employee['empName'];

        $id = $input['itemCodeSystem'];


        unset($input['final_approved_by']);
        $itemMaster = ItemMaster::where("itemCodeSystem", $id)->first();

        if (empty($itemMaster)) {
            return $this->sendError('Item Master not found');
        }


        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();

        if ($company) {
            $input['primaryCompanyID'] = $company->CompanyID;
        }


        foreach ($input as $key => $value) {
            if (is_array($input[$key])) {
                if (count($input[$key]) > 0) {
                    $input[$key] = $input[$key][0];
                } else {
                    $input[$key] = 0;
                }
            }
        }
        if ($itemMaster->itemConfirmedYN == 0 && $input['itemConfirmedYN'] == 1) {

            $validator = \Validator::make($input, [
                'primaryCompanySystemID' => 'required|numeric|min:1',
                'financeCategoryMaster' => 'required|numeric|min:1',
                'financeCategorySub' => 'required|numeric|min:1',
                'secondaryItemCode' => 'required',
                'unit' => 'required|numeric|min:1'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $checkSubCategory = FinanceItemcategorySubAssigned::where('mainItemCategoryID', $input['financeCategoryMaster'])
                ->where('itemCategorySubID', $input['financeCategorySub'])
                ->where('companySystemID', $input['primaryCompanySystemID'])
                ->first();

            if (empty($checkSubCategory)) {
                return $this->sendError('The Finance Sub Category field is required.', 500);
            }

            $params = array('autoID' => $id, 'company' => $input["primaryCompanySystemID"], 'document' => $input["documentSystemID"]);
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }
        foreach ($input as $key => $value) {
            $itemMaster->$key = $value;
        }

        $itemMaster->save();
        return $this->sendResponse($itemMaster->toArray(), 'Itemmaster updated successfully');

    }

    /**
     * Display all assigned itemAssigned for specific Item Master.
     * GET|HEAD /getAssignedCompaniesByItem}
     *
     * @param  int itemCodeSystem
     *
     * @return Response
     */
    public function getAssignedCompaniesByItem(Request $request)
    {

        $itemId = $request['itemCodeSystem'];
        $item = ItemMaster::where('itemCodeSystem', '=', $itemId)->first();

        if ($item) {
            $itemCompanies = ItemAssigned::where('itemCodeSystem', $itemId)->with(['company'])
                ->orderBy('idItemAssigned', 'DESC')
                ->get();
        } else {
            $itemCompanies = [];
        }

        return $this->sendResponse($itemCompanies, 'Companies retrieved successfully');

    }


    /**
     * Display the specified ItemMaster.
     * GET|HEAD /itemMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ItemMaster $itemMaster */
        //$itemMaster = $this->itemMasterRepository->findWithoutFail($id);
        $itemMaster = ItemMaster::where("itemCodeSystem", $id)->with(['finalApprovedBy'])->first();


        if (empty($itemMaster)) {
            return $this->sendError('Item Master not found');
        }

        return $this->sendResponse($itemMaster->toArray(), 'Item Master retrieved successfully');
    }

    /**
     * Update the specified ItemMaster in storage.
     * PUT/PATCH /itemMasters/{id}
     *
     * @param  int $id
     * @param UpdateItemMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateItemMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemMaster $itemMaster */
        $itemMaster = $this->itemMasterRepository->findWithoutFail($id);

        if (empty($itemMaster)) {
            return $this->sendError('Item Master not found');
        }

        $itemMaster = $this->itemMasterRepository->update($input, $id);

        return $this->sendResponse($itemMaster->toArray(), 'ItemMaster updated successfully');
    }

    /**
     * Remove the specified ItemMaster from storage.
     * DELETE /itemMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ItemMaster $itemMaster */
        $itemMaster = $this->itemMasterRepository->findWithoutFail($id);

        if (empty($itemMaster)) {
            return $this->sendError('Item Master not found');
        }

        $itemMaster->delete();

        return $this->sendResponse($id, 'Item Master deleted successfully');
    }


    public function approveItem(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectItem(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }

    }

    /**
     * Display the specified Item Master Audit.
     * GET|HEAD /getItemMasterAudit
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getItemMasterAudit(Request $request)
    {
        $id = $request->get('id');

        $itemMaster = $this->itemMasterRepository
            ->with(['created_by', 'confirmed_by', 'modified_by', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->where('documentSystemID', 57);
            }])
            ->findWithoutFail($id);

        if (empty($itemMaster)) {
            return $this->sendError('Item Master not found');
        }

        return $this->sendResponse($itemMaster->toArray(), 'Item Master retrieved successfully');
    }

    public function getAllFixedAssetItems(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyID'];

        $items = ItemAssigned::where('companySystemID', $companyId)
            ->where('financeCategoryMaster', 3)
            ->where('isActive', 1)
            ->select(['itemPrimaryCode', 'itemDescription', 'idItemAssigned', 'secondaryItemCode', 'itemCodeSystem']);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');

    }

    public function itemReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $item = $this->itemMasterRepository->find($id);
        if (empty($item)) {
            return $this->sendError('Item Master not found');
        }

        if ($item->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this item');
        }

        $itemArray = $item->toArray();

        $storeHistory = ItemMasterRefferedBack::insert($itemArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $item->primaryCompanySystemID)
            ->where('documentSystemID', $item->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $item->timesReferred;
            }
        }

        $documentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentRefereedHistory = DocumentReferedHistory::insert($documentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $item->primaryCompanySystemID)
            ->where('documentSystemID', $item->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $updateArray = ['refferedBackYN' => 0, 'itemConfirmedYN' => 0, 'itemConfirmedByEMPSystemID' => null,
                'itemConfirmedByEMPID' => null, 'itemConfirmedByEMPName' => null, 'itemConfirmedDate' => null, 'RollLevForApp_curr' => 1];

            $this->itemMasterRepository->update($updateArray, $id);
        }

        return $this->sendResponse($item->toArray(), 'Item Master Amend successfully');
    }


    public function getPosItemSearch(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];
        $items = ItemAssigned::where('companySystemID', $companyId)
                                ->where('financeCategoryMaster', 1)
                                ->with(['unit'])
                                ->select(['itemPrimaryCode', 'itemDescription','itemCodeSystem','idItemAssigned', 'secondaryItemCode','itemUnitOfMeasure']);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(10)->get();
        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }
}
