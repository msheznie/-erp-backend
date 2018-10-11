<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFixedAssetMasterAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetMasterAPIRequest;
use App\Models\AssetFinanceCategory;
use App\Models\AssetType;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\DepartmentMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\EmployeesDepartment;
use App\Models\FixedAssetCategory;
use App\Models\FixedAssetCategorySub;
use App\Models\FixedAssetCost;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetInsuranceDetail;
use App\Models\FixedAssetMaster;
use App\Models\GRVDetails;
use App\Models\InsurancePolicyType;
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
use Illuminate\Support\Facades\Storage;
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
        $itemImgaeArr = $input['itemImage'];
        $itemPicture = $input['itemPicture'];
        $input = array_except($request->all(), 'assetSerialNo', 'itemImage');
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
            ], $messages);

            if ($validator->fails()) {//echo 'in';exit;
                return $this->sendError($validator->messages(), 422);
            }

            if (isset($input['itemPicture'])) {
                if ($itemImgaeArr[0]['size'] > 31457280) {
                    return $this->sendError("Maximum allowed file size is 30 MB. Please upload lesser than 30 MB.", 500);
                }
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
                    if ($grvDetails->noQty < 1) {
                        $documentCode = ($input['companyID'] . '\\FA' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));

                        if ($input['assetSerialNo'][0]['faUnitSerialNo']) {
                            $input["faUnitSerialNo"] = $input['assetSerialNo'][0]['faUnitSerialNo'];
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
                        unset($input['itemPicture']);
                        $fixedAssetMasters = $this->fixedAssetMasterRepository->create($input);

                        if ($itemPicture) {
                            $decodeFile = base64_decode($itemImgaeArr[0]['file']);
                            $extension = $itemImgaeArr[0]['filetype'];
                            $data['itemPicture'] = $input['companyID'] . '_' . $input["documentID"] . '_' . $fixedAssetMasters['faID'] . '.' . $extension;

                            $path = $input["documentID"] . '/' . $fixedAssetMasters['faID'] . '/' . $data['itemPicture'];
                            $data['itemPath'] = $path;
                            Storage::disk('public')->put($path, $decodeFile);
                            $fixedAssetMasters = $this->fixedAssetMasterRepository->update($data, $fixedAssetMasters['faID']);
                        }

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
                    } else {
                        $qtyRange = range(1, $grvDetails->noQty);
                        if ($qtyRange) {
                            foreach ($qtyRange as $key => $qty) {
                                $documentCode = ($input['companyID'] . '\\FA' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));
                                if ($qty <= $assetSerialNoCount) {
                                    if ($input['assetSerialNo'][$key]['faUnitSerialNo']) {
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
                                unset($input['itemPicture']);
                                $lastSerialNumber++;
                                $fixedAssetMasters = $this->fixedAssetMasterRepository->create($input);

                                if ($itemPicture) {
                                    $decodeFile = base64_decode($itemImgaeArr[0]['file']);
                                    $extension = $itemImgaeArr[0]['filetype'];
                                    $data['itemPicture'] = $input['companyID'] . '_' . $input["documentID"] . '_' . $fixedAssetMasters['faID'] . '.' . $extension;

                                    $path = $input["documentID"] . '/' . $fixedAssetMasters['faID'] . '/' . $data['itemPicture'];
                                    $data['itemPath'] = $path;
                                    Storage::disk('public')->put($path, $decodeFile);
                                    $fixedAssetMasters = $this->fixedAssetMasterRepository->update($data, $fixedAssetMasters['faID']);
                                }

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
                    $assetAllocated = GRVDetails::where('grvDetailsID', $grvDetailsID)->update(['assetAllocationDoneYN' => -1]);
                    DB::commit();
                }
            }
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
        $itemImgaeArr = $input['itemImage'];
        $itemPicture = $input['itemPicture'];
        $input = array_except($request->all(), 'itemImage');
        $input = $this->convertArrayToValue($input);

        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }

        DB::beginTransaction();
        try {
            $messages = [
                'dateDEP.after_or_equal' => 'Depreciation Date cannot be less than Date aqquired',
            ];
            $validator = \Validator::make($request->all(), [
                'dateAQ' => 'required|date',
                'dateDEP' => 'required|date|after_or_equal:dateAQ',
            ], $messages);

            if ($validator->fails()) {//echo 'in';exit;
                return $this->sendError($validator->messages(), 422);
            }

            if (isset($input['itemPicture'])) {
                if ($itemImgaeArr[0]['size'] > 31457280) {
                    return $this->sendError("Maximum allowed file size is 30 MB. Please upload lesser than 30 MB.", 500);
                }
            }

            $department = DepartmentMaster::find($input['departmentSystemID']);
            if ($department) {
                $input['departmentID'] = $department->DepartmentID;
            }

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

            if ($fixedAssetMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {
                $params = array('autoID' => $id, 'company' => $fixedAssetMaster->companySystemID, 'document' => $fixedAssetMaster->documentSystemID, 'segment' => '', 'category' => '', 'amount' => 0);
                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"], 500, ['type' => 'confirm']);
                }
            }

            /** @var FixedAssetMaster $fixedAssetMaster */
            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = \Helper::getEmployeeID();
            $input["timestamp"] = date('Y-m-d H:i:s');
            unset($input['itemPicture']);

            $fixedAssetMaster = $this->fixedAssetMasterRepository->update($input, $id);

            if ($itemPicture) {
                $decodeFile = base64_decode($itemImgaeArr[0]['file']);
                $extension = $itemImgaeArr[0]['filetype'];
                $data['itemPicture'] = $input['companyID'] . '_' . $input["documentID"] . '_' . $fixedAssetMaster['faID'] . '.' . $extension;

                $path = $input["documentID"] . '/' . $fixedAssetMaster['faID'] . '/' . $data['itemPicture'];
                $data['itemPath'] = $path;
                Storage::disk('public')->put($path, $decodeFile);
                $fixedAssetMaster = $this->fixedAssetMasterRepository->update($data, $fixedAssetMaster['faID']);
            }

            DB::commit();
            return $this->sendResponse($fixedAssetMaster->toArray(), 'FixedAssetMaster updated successfully');

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }


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

        $insuranceType = InsurancePolicyType::all();

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
            'insuranceType' => $insuranceType,
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
        })->whereHas('localcurrency', function ($q) {
        })->whereHas('rptcurrency', function ($q) {
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

    public function getAllCostingByCompany(Request $request)
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

        $assetCositng = FixedAssetMaster::with(['category_by', 'sub_category_by'])->ofCompany($subCompanies);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $assetCositng->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $assetCositng->where('approved', $input['approved']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCositng = $assetCositng->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%")
                    ->orWhere('assetDescription', 'LIKE', "%{$search}%")
                    ->orWhere('COMMENTS', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetCositng)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('faID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAssetCostingByID($id)
    {
        /** @var FixedAssetMaster $fixedAssetMaster */
        $fixedAssetMaster = $this->fixedAssetMasterRepository->with('confirmed_by')->findWithoutFail($id);
        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }

        $fixedAssetCosting = FixedAssetCost::with(['localcurrency', 'rptcurrency'])->ofFixedAsset($id)->get();
        $groupedAsset = $this->fixedAssetMasterRepository->findWhere(['groupTO' => $id]);
        $depAsset = FixedAssetDepreciationPeriod::ofAsset($id)->get();
        $insurance = FixedAssetInsuranceDetail::with(['policy_by', 'location_by'])->ofAsset($id)->get();

        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }

        $output = ['fixedAssetMaster' => $fixedAssetMaster, 'fixedAssetCosting' => $fixedAssetCosting, 'groupedAsset' => $groupedAsset, 'depAsset' => $depAsset, 'insurance' => $insurance];

        return $this->sendResponse($output, 'Fixed Asset Master retrieved successfully');
    }

    function assetCostingReopen(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();

            $id = $input['faID'];
            $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);
            $emails = array();
            if (empty($fixedAssetMaster)) {
                return $this->sendError('Fixed Asset Master not found');
            }


            if ($fixedAssetMaster->approved == -1) {
                return $this->sendError('You cannot reopen this Asset costing it is already fully approved');
            }

            if ($fixedAssetMaster->RollLevForApp_curr > 1) {
                return $this->sendError('You cannot reopen this Asset costing it is already partially approved');
            }

            if ($fixedAssetMaster->confirmedYN == 0) {
                return $this->sendError('You cannot reopen this Asset costing, it is not confirmed');
            }

            $updateInput = ['confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
                'confirmedDate' => null, 'RollLevForApp_curr' => 1];

            $this->fixedAssetMasterRepository->update($updateInput, $id);

            $employee = \Helper::getEmployeeInfo();

            $document = DocumentMaster::where('documentSystemID', $fixedAssetMaster->documentSystemID)->first();

            $cancelDocNameBody = $document->documentDescription . ' <b>' . $fixedAssetMaster->faCode . '</b>';
            $cancelDocNameSubject = $document->documentDescription . ' ' . $fixedAssetMaster->faCode;

            $subject = $cancelDocNameSubject . ' is reopened';

            $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

            $documentApproval = DocumentApproved::where('companySystemID', $fixedAssetMaster->companySystemID)
                ->where('documentSystemCode', $fixedAssetMaster->faID)
                ->where('documentSystemID', $fixedAssetMaster->documentSystemID)
                ->where('rollLevelOrder', 1)
                ->first();

            if ($documentApproval) {
                if ($documentApproval->approvedYN == 0) {
                    $companyDocument = CompanyDocumentAttachment::where('companySystemID', $fixedAssetMaster->companySystemID)
                        ->where('documentSystemID', $fixedAssetMaster->documentSystemID)
                        ->first();

                    if (empty($companyDocument)) {
                        return ['success' => false, 'message' => 'Policy not found for this document'];
                    }

                    $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                        ->where('companySystemID', $documentApproval->companySystemID)
                        ->where('documentSystemID', $documentApproval->documentSystemID);

                    $approvalList = $approvalList
                        ->with(['employee'])
                        ->groupBy('employeeSystemID')
                        ->get();

                    foreach ($approvalList as $da) {
                        if ($da->employee) {
                            $emails[] = array('empSystemID' => $da->employee->employeeSystemID,
                                'companySystemID' => $documentApproval->companySystemID,
                                'docSystemID' => $documentApproval->documentSystemID,
                                'alertMessage' => $subject,
                                'emailAlertMessage' => $body,
                                'docSystemCode' => $documentApproval->documentSystemCode);
                        }
                    }

                    $sendEmail = \Email::sendEmail($emails);
                    if (!$sendEmail["success"]) {
                        return ['success' => false, 'message' => $sendEmail["message"]];
                    }
                }
            }

            $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $fixedAssetMaster->companySystemID)
                ->where('documentSystemID', $fixedAssetMaster->documentSystemID)
                ->delete();

            DB::commit();
            return $this->sendResponse($fixedAssetMaster->toArray(), 'Payment Voucher reopened successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    public function getCostingApprovalByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $assetCost = DB::table('erp_documentapproved')
            ->select(
                'erp_fa_asset_master.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode',
                'erp_fa_category.catDescription as catDescription',
                'erp_fa_categorysub.catDescription as subCatDescription'
            )
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $query->whereIn('employeesdepartments.documentSystemID', [22])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            })
            ->join('erp_fa_asset_master', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'faID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_fa_asset_master.companySystemID', $companyId)
                    ->where('erp_fa_asset_master.approved', 0)
                    ->where('erp_fa_asset_master.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('erp_fa_category', 'erp_fa_category.faCatID', 'erp_fa_asset_master.faCatID')
            ->leftJoin('erp_fa_categorysub', 'erp_fa_categorysub.faCatSubID', 'erp_fa_asset_master.faSubCatID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [22])
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCost = $assetCost->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%")
                    ->orWhere('assetDescription', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($assetCost)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('faID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getCostingApprovedByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $assetCost = DB::table('erp_documentapproved')
            ->select(
                'erp_fa_asset_master.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode',
                'erp_fa_category.catDescription as catDescription',
                'erp_fa_categorysub.catDescription as subCatDescription')
            ->join('erp_fa_asset_master', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'faID')
                    ->where('erp_fa_asset_master.companySystemID', $companyId)
                    ->where('erp_fa_asset_master.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('erp_fa_category', 'erp_fa_category.faCatID', 'erp_fa_asset_master.faCatID')
            ->leftJoin('erp_fa_categorysub', 'erp_fa_categorysub.faCatSubID', 'erp_fa_asset_master.faSubCatID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [22])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCost = $assetCost->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%")
                    ->orWhere('assetDescription', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($assetCost)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('faID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


}
