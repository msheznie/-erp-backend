<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateAssetVerificationAPIRequest;
use App\Http\Requests\API\UpdateAssetVerificationAPIRequest;
use App\Models\AssetFinanceCategory;
use App\Models\AssetVerification;
use App\Models\AssetVerificationDetail;
use App\Models\BookInvSuppMaster;
use App\Models\Company;
use App\Models\DepartmentMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\ERPAssetVerificationDetailReferredback;
use App\Models\ERPAssetVerificationReferredback;
use App\Models\FixedAssetCategory;
use App\Models\FixedAssetMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\AssetVerificationRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class AssetVerificationController
 *
 * @package App\Http\Controllers\API
 */
class AssetVerificationAPIController extends AppBaseController
{
    /** @var  AssetVerificationRepository */
    private $assetVerificationRepository;

    public function __construct(AssetVerificationRepository $assetVerificationRepo)
    {
        $this->assetVerificationRepository = $assetVerificationRepo;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetVerifications",
     *      summary="Get a listing of the AssetVerifications.",
     *      tags={"AssetVerification"},
     *      description="Get all AssetVerifications",
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
     *                  @SWG\Items(ref="#/definitions/AssetVerification")
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
        $input = $request->all();
        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }


        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $assetVerifications = AssetVerification::whereIN('companySystemID', $subCompanies);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $assetVerifications->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $assetVerifications->where('approved', $input['approved']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetVerifications = $assetVerifications->where(function ($query) use ($search) {
                $query->where('narration', 'LIKE', "%{$search}%")
                    ->orWhere('verficationCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetVerifications)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * @param CreateAssetVerificationAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetVerifications",
     *      summary="Store a newly created AssetVerification in storage",
     *      tags={"AssetVerification"},
     *      description="Store AssetVerification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetVerification that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetVerification")
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
     *                  ref="#/definitions/AssetVerification"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetVerificationAPIRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();

            $documentMaster = DocumentMaster::find($input['documentSystemID']);
            if ($documentMaster) {
                $input['documentID'] = $documentMaster->documentID;
            }

            $company = Company::find($input['companySystemID']);
            if ($company) {
                $input['companyID'] = $company->CompanyID;
            }

            $lastSerial = AssetVerification::where('companySystemID', $input['companySystemID'])
                ->orderBy('serialNo', 'desc')
                ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            $documentCode = ($company->CompanyID . '\\' . $documentMaster->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

            $input['verficationCode'] = $documentCode;
            $input['documentDate'] = new Carbon($input['documentDate']);
            $input['serialNo'] = $lastSerialNumber;
            $input['createdPcID'] = gethostname();
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();


            $assetVerification = $this->assetVerificationRepository->create($input);
            DB::commit();
            return $this->sendResponse($assetVerification->toArray(), 'Asset Verification saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception);
        }
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetVerifications/{id}",
     *      summary="Display the specified AssetVerification",
     *      tags={"AssetVerification"},
     *      description="Get AssetVerification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetVerification",
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
     *                  ref="#/definitions/AssetVerification"
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
        $assetVerification = AssetVerification::with('assetVerificationDetail:id,verification_id,faID')->where('id', $id)->first();

        if (empty($assetVerification)) {
            return $this->sendError('Asset Verification not found');
        }

        return $this->sendResponse($assetVerification, 'Asset Verification retrieved successfully.');
    }

    /**
     * @param int                               $id
     * @param UpdateAssetVerificationAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetVerifications/{id}",
     *      summary="Update the specified AssetVerification in storage",
     *      tags={"AssetVerification"},
     *      description="Update AssetVerification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetVerification",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetVerification that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetVerification")
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
     *                  ref="#/definitions/AssetVerification"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateAssetVerificationAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $assetVerification = $this->assetVerificationRepository->findWithoutFail($id);


        if (empty($assetVerification)) {
            return $this->sendError('Asset verification Master not found');
        }


        if ($assetVerification->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $isDetailsExists = AssetVerificationDetail::where('verification_id', $id)->exists();
            if (!$isDetailsExists) {
                return $this->sendError('Asset verification details not found');
            }

            $params = [
                'autoID' => $id,
                'company' => $assetVerification->companySystemID,
                'document' => $assetVerification->documentSystemID
            ];

            $confirm = \Helper::confirmDocument($params);

            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500, ['type' => 'confirm']);
            }
        }
        $assetVerification = $this->assetVerificationRepository->update($input, $id);


        return $this->sendReponseWithDetails($assetVerification->toArray(), 'Asset verification updated successfully',1,$confirm['data'] ?? null);
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetVerifications/{id}",
     *      summary="Remove the specified AssetVerification from storage",
     *      tags={"AssetVerification"},
     *      description="Delete AssetVerification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetVerification",
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
        /** @var AssetVerification $assetVerification */
        $assetVerification = $this->assetVerificationRepository->findWithoutFail($id);

        if ($assetVerification['approved']) {
            return $this->sendError('You can\'t remove this asset');
        }

        if (empty($assetVerification)) {
            return $this->sendError('Asset Verification not found');
        }

        $assetVerification->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.asset_verification_master')]));
    }

    public function getVerificationFormData(Request $request)
    {

        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        $yesNoSelection = YesNoSelection::all();

        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();
        $department = DepartmentMaster::showInCombo()->get();
        $assetFinanceCategory = AssetFinanceCategory::all();
        $fixedAssetCategory = FixedAssetCategory::ofCompany($subCompanies)->get();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'department' => $department,
            'assetFinanceCategory' => $assetFinanceCategory,
            'fixedAssetCategory' => $fixedAssetCategory,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getVerificationApprovalByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyID'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');

        $assetVerification = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'erp_fa_asset_verification.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode'
            )
            ->join('erp_fa_asset_verification', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                    ->where('erp_fa_asset_verification.companySystemID', $companyId)
                    ->where('erp_fa_asset_verification.approved', 0)
                    ->where('erp_fa_asset_verification.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [99])
            ->where('erp_documentapproved.companySystemID', $companyId);


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetVerification = $assetVerification->where(function ($query) use ($search) {
                $query->where('narration', 'LIKE', "%{$search}%")
                    ->orWhere('verficationCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($assetVerification)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getVerificationApprovedByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyID'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $assetVerification = DB::table('erp_documentapproved')
            ->select(
                'erp_fa_asset_verification.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode'
            )
            ->join('erp_fa_asset_verification', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                    ->where('erp_fa_asset_verification.companySystemID', $companyId)
                    ->where('erp_fa_asset_verification.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [99])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetVerification = $assetVerification->where(function ($query) use ($search) {
                $query->where('narration', 'LIKE', "%{$search}%")
                    ->orWhere('verficationCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($assetVerification)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAllCostingByCompanyForVerification(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('cancelYN', 'confirmedYN', 'approved', 'auditCategory', 'mainCategory', 'subCategory'));
        $isDeleted = (isset($input['is_deleted']) && $input['is_deleted'] == 1) ? 1 : 0;

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

        $assetCositng = FixedAssetMaster::with(['category_by', 'sub_category_by', 'finance_category'])
            ->ofCompany($subCompanies)->where('approved', -1);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $assetCositng->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('mainCategory', $input)) {
            if ($input['mainCategory']) {
                $assetCositng->where('faCatID', $input['mainCategory']);
            }
        }

        if (array_key_exists('department', $input)) {
            if ($input['department']) {
                $assetCositng->where('departmentSystemID', $input['department']);
            }
        }

        if (array_key_exists('subCategory', $input)) {
            if ($input['subCategory']) {
                $assetCositng->where('faSubCatID', $input['subCategory']);
            }
        }

        if (array_key_exists('auditCategory', $input)) {
            if ($input['auditCategory']) {
                $assetCositng->where('AUDITCATOGARY', $input['auditCategory']);
            }
        }

        // get only deleted
        if ($isDeleted == 1) {
            $assetCositng->onlyTrashed();
        }


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCositng = $assetCositng->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%")
                    ->orWhere('assetDescription', 'LIKE', "%{$search}%")
                    ->orWhere('docOrigin', 'LIKE', "%{$search}%")
                    ->orWhere('faUnitSerialNo', 'LIKE', "%{$search}%");
            });
        }

        $verification_id = $input['verificationId'];
        return \DataTables::eloquent($assetCositng)
            ->addColumn('Actions', function ($asset) use ($verification_id) {
                return AssetVerificationDetail::where('faID', $asset->faID)->where('verification_id', $verification_id)->exists();
            })
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
    public function amendAssetVerification(Request $request)
    {
        $input = $request->all();
        $assetVerificationAutoID = $input['assetVerificationID'];

        $assetVerificationMasterData = AssetVerification::find($assetVerificationAutoID);
        if (empty($assetVerificationMasterData)) {
            return $this->sendError('Asset Verification not found');
        }

        if ($assetVerificationMasterData->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this asset verification');
        }
        $assetVerificationArray = $assetVerificationMasterData->toArray();
        $storeAssetVerificationHistory = ERPAssetVerificationReferredback::insert($assetVerificationArray);

        $assetVerificationDetailRec = AssetVerificationDetail::where('verification_id', $assetVerificationAutoID)->get();

        if (!empty($assetVerificationDetailRec)) {
            foreach ($assetVerificationDetailRec as $assetVery) {
                $assetVery['timesReferred'] = $assetVerificationMasterData->timesReferred;
            }
        } 

        $assetVerificationDetailArray = $assetVerificationDetailRec->toArray(); 
        $storeAssetTransferDetailHistory = ERPAssetVerificationDetailReferredback::insert($assetVerificationDetailArray);


        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $assetVerificationAutoID)
            ->where('companySystemID', $assetVerificationMasterData->companySystemID)
            ->where('documentSystemID', $assetVerificationMasterData->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $assetVerificationMasterData->timesReferred;
            }
        }
        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $assetVerificationAutoID)
            ->where('companySystemID',  $assetVerificationMasterData->companySystemID)
            ->where('documentSystemID', $assetVerificationMasterData->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $assetVerificationMasterData->refferedBackYN = 0;
            $assetVerificationMasterData->confirmedYN = 0;
            $assetVerificationMasterData->confirmedByEmpSystemID = null;
            $assetVerificationMasterData->confirmedByName = null;
            $assetVerificationMasterData->confirmedByEmpID = null;
            $assetVerificationMasterData->confirmedDate = null;
            $assetVerificationMasterData->RollLevForApp_curr = 1;
            $assetVerificationMasterData->save();
        } 

        return $this->sendResponse($assetVerificationMasterData->toArray(), 'Asset Verification amend successfully');
    }
}
