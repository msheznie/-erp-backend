<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFixedAssetMasterAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetMasterAPIRequest;
use App\Models\AssetFinanceCategory;
use App\Models\AssetType;
use App\Models\Company;
use App\Models\DepartmentMaster;
use App\Models\FixedAssetCategory;
use App\Models\FixedAssetCategorySub;
use App\Models\FixedAssetMaster;
use App\Models\GRVDetails;
use App\Models\Location;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\FixedAssetCostRepository;
use App\Repositories\FixedAssetMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FixedAssetMasterController
 * @package App\Http\Controllers\API
 */
class FixedAssetMasterAPIController extends AppBaseController
{
    /** @var  FixedAssetMasterRepository */
    private $fixedAssetMasterRepository;
    private $fixedAssetCostRepository;

    public function __construct(FixedAssetMasterRepository $fixedAssetMasterRepo, FixedAssetCostRepository $fixedAssetCostRepo)
    {
        $this->fixedAssetMasterRepository = $fixedAssetMasterRepo;
        $this->fixedAssetCostRepository = $fixedAssetCostRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetMasters",
     *      summary="Get a listing of the FixedAssetMasters.",
     *      tags={"FixedAssetMaster"},
     *      description="Get all FixedAssetMasters",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/FixedAssetMaster")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->fixedAssetMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetMasters = $this->fixedAssetMasterRepository->all();

        return $this->sendResponse($fixedAssetMasters->toArray(), 'Fixed Asset Masters retrieved successfully');
    }

    /**
     * @param CreateFixedAssetMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetMasters",
     *      summary="Store a newly created FixedAssetMaster in storage",
     *      tags={"FixedAssetMaster"},
     *      description="Store FixedAssetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetMaster")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/FixedAssetMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetMasterAPIRequest $request)
    {
        $input = $request->all();
        $assetSerialNoArr = $input['assetSerialNo'];
        $input = array_except($request->all(), 'assetSerialNo');
        $input = $this->convertArrayToValue($input);
        $input['assetSerialNo'] = $assetSerialNoArr;

        DB::beginTransaction();
        try {
            $messages = [
                'dateDEP.after_or_equal' => 'Depreciation Date cannot be less than Date aqquired',
            ];
            $validator = \Validator::make($request->all(), [
                'dateAQ' => 'required|date',
                'dateDEP' => 'required|date|after_or_equal:dateAQ',
            ],$messages);

            if ($validator->fails()) {//echo 'in';exit;
                return $this->sendError($validator->messages(), 422);
            }

            $grvDetailsID = $input['grvDetailsID'];
            $grvDetails = GRVDetails::with(['grv_master'])->find($grvDetailsID);
            if ($grvDetails) {

                $assetSerialNoCount = count($input['assetSerialNo']);

                $input['serviceLineSystemID'] = $grvDetails->grv_master->serviceLineSystemID;
                $segment = SegmentMaster::find($input['serviceLineSystemID']);
                if ($segment) {
                    $input['serviceLineCode'] = $segment->ServiceLineCode;
                }

                $company = Company::find($input['companySystemID']);
                if ($company) {
                    $input['companyID'] = $company->CompanyID;
                }

                $department = DepartmentMaster::find($input['departmentSystemID']);
                if ($department) {
                    $input['departmentID'] = $department->DepartmentID;
                }

                $input["documentSystemID"] = 22;
                $input["documentID"] = 'FA';

                $input['assetType'] = 1;
                $input['supplierIDRentedAsset'] = $grvDetails->grv_master->supplierID;

                if (isset($input['dateAQ'])) {
                    if ($input['dateAQ']) {
                        $input['dateAQ'] = new Carbon($input['dateAQ']);
                    }
                }

                if (isset($input['dateDEP'])) {
                    if ($input['dateDEP']) {
                        $input['dateDEP'] = new Carbon($input['dateDEP']);
                    }
                }

                if (isset($input['lastVerifiedDate'])) {
                    if ($input['lastVerifiedDate']) {
                        $input['lastVerifiedDate'] = new Carbon($input['lastVerifiedDate']);
                    }
                }

                $lastSerialNumber = 1;
                $lastSerial = FixedAssetMaster::selectRaw('MAX(serialNo) as serialNo')->where('companySystemID', $input['companySystemID'])->first();
                if ($lastSerial) {
                    $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                }
                if ($grvDetails["noQty"]) {
                    $qtyRange = range(1, $grvDetails->noQty);
                    if ($qtyRange) {
                        foreach ($qtyRange as $key => $qty) {
                            $documentCode = ($input['companyID'] . '\\FA' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));
                            if($qty <= $assetSerialNoCount){
                                if($input['assetSerialNo'][$key]['faUnitSerialNo']) {
                                    $input["faUnitSerialNo"] = $input['assetSerialNo'][$key]['faUnitSerialNo'];
                                }
                            }
                            $input["serialNo"] = $lastSerialNumber;
                            $input['docOriginSystemCode'] = $grvDetails->grv_master->grvAutoID;
                            $input['docOrigin'] = $grvDetails->grv_master->grvPrimaryCode;
                            $input['docOriginDetailID'] = $grvDetailsID;
                            $input["itemCode"] = $grvDetails->itemCode;
                            $input["PARTNUMBER"] = $grvDetails->item_by->secondaryItemCode;
                            $input["faCode"] = $documentCode;
                            $input['createdPcID'] = gethostname();
                            $input['createdUserID'] = \Helper::getEmployeeID();
                            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                            $input["timestamp"] = date('Y-m-d H:i:s');
                            unset($input['grvDetailsID']);
                            $lastSerialNumber++;
                            $fixedAssetMasters = $this->fixedAssetMasterRepository->create($input);
                            $cost['originDocumentSystemCode'] = $grvDetails->grv_master->grvAutoID;
                            $cost['originDocumentID'] = $grvDetails->grv_master->grvPrimaryCode;
                            $cost['faID'] = $fixedAssetMasters['faID'];
                            $cost['itemCode'] = $input["itemCode"];
                            $cost['assetID'] = $fixedAssetMasters['faCode'];
                            $cost['assetDescription'] = $fixedAssetMasters['assetDescription'];
                            $cost['costDate'] = $input['dateAQ'];
                            $cost['localCurrencyID'] = $grvDetails->localCurrencyID;
                            $cost['localAmount'] = $grvDetails->landingCost_LocalCur;
                            $cost['rptCurrencyID'] = $grvDetails->companyReportingCurrencyID;
                            $cost['rptAmount'] = $grvDetails->landingCost_RptCur;
                            $assetCostMastger = $this->fixedAssetCostRepository->create($cost);
                        }
                    }
                }
            }
            $assetAllocated = GRVDetails::where('grvDetailsID', $grvDetailsID)->update(['assetAllocationDoneYN' => -1]);
            DB::commit();
            return $this->sendResponse([], 'Fixed Asset Master saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetMasters/{id}",
     *      summary="Display the specified FixedAssetMaster",
     *      tags={"FixedAssetMaster"},
     *      description="Get FixedAssetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/FixedAssetMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var FixedAssetMaster $fixedAssetMaster */
        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }

        return $this->sendResponse($fixedAssetMaster->toArray(), 'Fixed Asset Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetMasters/{id}",
     *      summary="Update the specified FixedAssetMaster in storage",
     *      tags={"FixedAssetMaster"},
     *      description="Update FixedAssetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetMaster")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/FixedAssetMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var FixedAssetMaster $fixedAssetMaster */
        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }

        $fixedAssetMaster = $this->fixedAssetMasterRepository->update($input, $id);

        return $this->sendResponse($fixedAssetMaster->toArray(), 'FixedAssetMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetMasters/{id}",
     *      summary="Remove the specified FixedAssetMaster from storage",
     *      tags={"FixedAssetMaster"},
     *      description="Delete FixedAssetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var FixedAssetMaster $fixedAssetMaster */
        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }

        $fixedAssetMaster->delete();

        return $this->sendResponse($id, 'Fixed Asset Master deleted successfully');
    }


    public function getAllocationFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $companyCurrency = \Helper::companyCurrency($companyId);

        $department = DepartmentMaster::showInCombo()->get();

        $serviceline = SegmentMaster::isActive()->ofCompany($subCompanies)->get();

        $assetType = AssetType::all();

        $assetFinanceCategory = AssetFinanceCategory::all();

        $fixedAssetCategory = FixedAssetCategory::ofCompany($subCompanies)->get();

        $supplier = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName"))
            ->whereIN('companySystemID', $subCompanies)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $location = Location::all();

        $output = array(
            'financialYears' => $financialYears,
            'companyFinanceYear' => $companyFinanceYear,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'companyCurrency' => $companyCurrency,
            'department' => $department,
            'serviceline' => $serviceline,
            'assetType' => $assetType,
            'assetFinanceCategory' => $assetFinanceCategory,
            'fixedAssetCategory' => $fixedAssetCategory,
            'supplier' => $supplier,
            'location' => $location,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getFixedAssetSubCat(Request $request)
    {
        $subCategory = FixedAssetCategorySub::ofCompany([$request->companySystemID])->byFaCatID($request->faCatID)->get();
        return $this->sendResponse($subCategory, 'Record retrieved successfully');
    }

    public function getFinanceGLCode(Request $request)
    {
        $subCategory = AssetFinanceCategory::with(['costaccount', 'accdepaccount', 'depaccount', 'disaccount'])->find($request->faFinanceCatID);
        return $this->sendResponse($subCategory, 'Record retrieved successfully');
    }

    public function getFAGrvDetailsByID(Request $request)
    {
        $subCategory = GRVDetails::with(['grv_master'])->find($request->grvDetailsID);
        return $this->sendResponse($subCategory, 'Record retrieved successfully');
    }


    public function getAllAllocationByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('cancelYN', 'confirmedYN', 'approved'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $assetAllocation = GRVDetails::with(['grv_master', 'item_by', 'localcurrency', 'rptcurrency'])->whereHas('item_by', function ($q) {
            $q->where('financeCategoryMaster', 3);
            $q->whereIN('financeCategorySub', [16, 162, 164, 166]);
        })->whereHas('grv_master', function ($q) {
            $q->where('grvConfirmedYN', 1);
            $q->where('approved', -1);
        })->whereIN('companySystemID', $subCompanies)->where('assetAllocationDoneYN', 0);

        if (array_key_exists('cancelYN', $input)) {
            if (($input['cancelYN'] == 0 || $input['cancelYN'] == -1) && !is_null($input['cancelYN'])) {
                $assetAllocation->where('cancelYN', $input['cancelYN']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $assetAllocation->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $assetAllocation->where('approved', $input['approved']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetAllocation = $assetAllocation->where(function ($query) use ($search) {
                $query->where('grvPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetAllocation)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('grvDetailsID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

}
