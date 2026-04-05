<?php
/**
 * =============================================
 * -- File Name : ApprovalLevelAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Approval Setup
 * -- Author : Mubashir
 * -- Create date : 23 - April 2018
 * -- Description : This file contains the all CRUD for Approval Level
 * -- REVISION HISTORY
 * --
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateApprovalLevelAPIRequest;
use App\Http\Requests\API\UpdateApprovalLevelAPIRequest;
use App\Models\ApprovalLevel;
use App\Models\ApprovalRole;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\FinanceItemCategoryMaster;
use App\Models\FinanceItemCategorySub;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\SegmentMaster;
use App\Models\CompanyDocumentAttachment;
use App\Models\TenderType;
use App\Models\WorkflowConfiguration;
use App\Models\YesNoSelectionForMinus;
use App\Models\DocumentApproved;
use App\Repositories\ApprovalLevelRepository;
use App\Services\ApprovalLevelService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Log;
use App\helper\Helper;
use App\Models\PvApprovalTypeSetup;

/**
 * Class ApprovalLevelController
 * @package App\Http\Controllers\API
 */
class ApprovalLevelAPIController extends AppBaseController
{
    /** @var  ApprovalLevelRepository */
    private $approvalLevelRepository;

    /** @var  ApprovalLevelService */
    private $approvalLevelService;

    public function __construct(ApprovalLevelRepository $approvalLevelRepo, ApprovalLevelService $approvalLevelService)
    {
        $this->approvalLevelRepository = $approvalLevelRepo;
        $this->approvalLevelService = $approvalLevelService;
    }

    /**
     * Display a listing of the ApprovalLevel.
     * GET|HEAD /approvalLevels
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->approvalLevelRepository->pushCriteria(new RequestCriteria($request));
        $this->approvalLevelRepository->pushCriteria(new LimitOffsetCriteria($request));
        $approvalLevels = $this->approvalLevelRepository->all();

        return $this->sendResponse($approvalLevels->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.approval_levels')]));
    }

    /**
     * Store a newly created ApprovalLevel in storage.
     * POST /approvalLevels
     *
     * @param CreateApprovalLevelAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateApprovalLevelAPIRequest $request)
    {
        $input = $request->all();
        $approvalLevel = "";
        $input = $this->convertArrayToValue($input);

        if (isset($input['documentSystemID']) && ($input['documentSystemID'] == 3)) {
            $grvSubcategoryCheck = $this->approvalLevelService->validateGrvSubcategoryRequired(
                (int) ($input['companySystemID'] ?? 0),
                (int) ($input['documentSystemID'] ?? 0),
                $input
            );

            if (!$grvSubcategoryCheck['valid']) {
                return $this->sendError($grvSubcategoryCheck['message'], 422);
            }
        }

        $validationExcludedDocuments = [1, 4];
        if(isset($input['documentSystemID']) && !in_array($input['documentSystemID'], $validationExcludedDocuments)){
            $approvalLevelValidation = $this->approvalLevelValidation($input);

            if (!$approvalLevelValidation['status']) {
                return $this->sendError($approvalLevelValidation['message'], 500);
            }
        }

        $companyID = Company::where('companySystemID', $input["companySystemID"])->first();
        $input["companyID"] = $companyID->CompanyID;

        $documentID = DocumentMaster::where('documentSystemID', $input["documentSystemID"])->first();
        $input["documentID"] = $documentID->documentID;
        $input["departmentID"] = $documentID->departmentID;
        $input["departmentSystemID"] = $documentID->departmentSystemID;

        if (isset($request->serviceLineSystemID)) {
            $ServiceLineCode = SegmentMaster::where('serviceLineSystemID', $input["serviceLineSystemID"])->first();
            $input["serviceLineCode"] = $ServiceLineCode->ServiceLineCode;
        }

        if (isset($request->tenderTypeId)) {
            $tenderType = TenderType::getTenderTypeData($input['tenderTypeId']);
            $input["tenderTypeCode"] =  $input['tenderTypeId'] == -1 ? 'General' : $tenderType->name;
        }

        if(isset($input['isCategoryWiseApproval']) && $input['isCategoryWiseApproval']){
            $input['isCategoryWiseApproval'] = -1;
        }

        if (isset($input['documentSystemID']) && (int) $input['documentSystemID'] === 3 && empty($input['subcategoryID'])) {
            $input['subcategoryID'] = null;
        }

        if (isset($input['documentSystemID']) && $input['documentSystemID'] == 4) {
            $input['serviceLineWise'] = 0;
            $input['serviceLineSystemID'] = null;
            $input['serviceLineCode'] = null;
            
            $input['isCategoryWiseApproval'] = 0;
            $input['isDelegation'] = 0;
            $input['prTypeWise'] = 0;
        }

        if (isset($request->approvalLevelID)) {
            $id = $request->approvalLevelID;
            $approvalLevel = $this->approvalLevelRepository->findWithoutFail($id);

            if (empty($approvalLevel)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.approval_levels')]));
            }
            $approvalLevel = $this->approvalLevelRepository->update($input, $id);

        } else {
            $approvalLevel = $this->approvalLevelRepository->create($input);
        }

        return $this->sendResponse($approvalLevel->toArray(), trans('custom.save', ['attribute' => trans('custom.approval_levels')]));
    }

    /**
     * Display the specified ApprovalLevel.
     * GET|HEAD /approvalLevels/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ApprovalLevel $approvalLevel */
        $approvalLevel = $this->approvalLevelRepository->findWithoutFail($id);

        if (empty($approvalLevel)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.approval_levels')]));
        }

        if (!empty($approvalLevel) && ($approvalLevel->documentSystemID == 4) && ($approvalLevel->pvTypeWise == 1)) {
            $pvApprovalTypes = PvApprovalTypeSetup::where('company_system_id', $approvalLevel->companySystemID)->get();

            $approvalLevel->pvTypeBaseApprovals = $pvApprovalTypes;
        }

        return $this->sendResponse($approvalLevel->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.approval_levels')]));
    }

    /**
     * Update the specified ApprovalLevel in storage.
     * PUT/PATCH /approvalLevels/{id}
     *
     * @param  int $id
     * @param UpdateApprovalLevelAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateApprovalLevelAPIRequest $request)
    {
        /** @var ApprovalLevel $approvalLevel */
        $approvalLevel = $this->approvalLevelRepository->findWithoutFail($id);

        if (empty($approvalLevel)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.approval_levels')]));
        }

        $editGuard = $this->approvalLevelService->validateEditAllowed((int) $id);
        if (!$editGuard['allowed']) {
            return $this->sendError($editGuard['message'], 500);
        }

        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $companyID = Company::where('companySystemID', $input["companySystemID"])->first();
        $input["companyID"] = $companyID->CompanyID;

        $documentID = DocumentMaster::where('documentSystemID', $input["documentSystemID"])->first();
        $input["documentID"] = $documentID->documentID;
        $input["departmentID"] = $documentID->departmentID;
        $input["departmentSystemID"] = $documentID->departmentSystemID;

        if (isset($request->serviceLineSystemID)) {
            $ServiceLineCode = SegmentMaster::where('serviceLineSystemID', $input["serviceLineSystemID"])->first();
            $input["serviceLineCode"] = $ServiceLineCode->ServiceLineCode;
        }


        if (isset($request->tenderTypeId)) {
            $tenderType = TenderType::getTenderTypeData($request->tenderTypeId);
            $input["tenderTypeCode"] = $input['tenderTypeId'] == -1 ? 'General' : $tenderType->name;
        }

        $grvSubcategoryCheck = $this->approvalLevelService->validateGrvSubcategoryRequired(
            (int) ($input['companySystemID'] ?? 0),
            (int) ($input['documentSystemID'] ?? 0),
            $input
        );
        if (!$grvSubcategoryCheck['valid']) {
            return $this->sendError($grvSubcategoryCheck['message'], 422);
        }

        if (isset($input['documentSystemID']) && (int) $input['documentSystemID'] === 3 && empty($input['subcategoryID'])) {
            $input['subcategoryID'] = null;
        }

        $approvalLevel = $this->approvalLevelRepository->update($input, $id);

        return $this->sendResponse($approvalLevel->toArray(), trans('custom.update', ['attribute' => trans('custom.approval_levels')]));
    }

    /**
     * Remove the specified ApprovalLevel from storage.
     * DELETE /approvalLevels/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ApprovalLevel $approvalLevel */
        $approvalLevel = $this->approvalLevelRepository->findWithoutFail($id);

        if (empty($approvalLevel)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.approval_levels')]));
        }

        $documentApproved = DocumentApproved::where('approvalLevelID',$id )
                                            ->where('approvedYN',0)
                                            ->where('rejectedYN',0)
                                            ->get();
        
        if(count($documentApproved) > 0){
            return $this->sendError(trans('custom.cannot_delete_approval_level_following_documents_a'),500 ,$documentApproved->Toarray());
        }

        $approvalLevel->approvalRole()->delete();

        $approvalLevel->update(['is_deleted' => 1 ,'isActive' => 0]);

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.approval_levels')]));
    }


    public function getGroupApprovalLevelDatatable(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('selectedCompanyID'));
        return $this->approvalLevelRepository->getGroupApprovalLevelDatatable($input);
    }

    public function getGroupFilterData(Request $request)
    {
        /** all Company  Drop Down */
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if (Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $groupCompany = Company::whereIN("companySystemID", $companiesByGroup)->get();

        /** all document Drop Down */
        $document = Helper::getAllDocuments();

        /** all finance category Drop Down */
        $financeCategory = FinanceItemCategoryMaster::all();

        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $workflows = WorkflowConfiguration::where('isActive', 1)->where('companySystemID', $selectedCompanyId)->get();


        $output = array('company' => $groupCompany,
            'document' => $document,
            'financeCategory' => $financeCategory,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'workflows' => $workflows,
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function getCompanyServiceLine(Request $request)
    {
        /** all Service line  Drop Down */
        $selectedCompanyId = $request['companySystemID'];
        $serviceline = Helper::getCompanyServiceline($selectedCompanyId);
        $output = array('serviceline' => $serviceline
        );
        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function getSubcategoriesByCategory(Request $request)
    {
        $companySystemID = (int) $request->input('companySystemID');
        $categoryID = (int) $request->input('categoryID');

        if ($companySystemID <= 0 || $categoryID <= 0) {
            return $this->sendResponse([], trans('custom.retrieve', ['attribute' => trans('custom.record')]));
        }

        $subcategories = $this->approvalLevelService->getAssignedActiveSubcategories($companySystemID, $categoryID);

        return $this->sendResponse($subcategories, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function approvalLevelValidation($input, $activate = false)
    {
        $checkDuplicateApproval = ApprovalLevel::where('companySystemID', $input["companySystemID"])
            ->where('documentSystemID', $input['documentSystemID'])
            ->when(isset($input['isCategoryWiseApproval']) && ($input['isCategoryWiseApproval'] || $input['isCategoryWiseApproval'] == -1), function($query) use ($input){
                $query->where('isCategoryWiseApproval', -1)
                    ->where('categoryID', $input['categoryID'] ?? null);
                if (isset($input['documentSystemID']) && (int) $input['documentSystemID'] === 3) {
                    if (!empty($input['subcategoryID'])) {
                        $query->where('subcategoryID', $input['subcategoryID']);
                    } else {
                        $query->whereNull('subcategoryID');
                    }
                }
            })
            ->when((isset($input['isCategoryWiseApproval']) && !$input['isCategoryWiseApproval']) || !isset($input['isCategoryWiseApproval']), function($query) {
                $query->where('isCategoryWiseApproval', 0)
                    ->whereNull('categoryID')
                    ->whereNull('subcategoryID');
            })
            ->when(isset($input['serviceLineSystemID']) && isset($input['serviceLineWise']) && $input['serviceLineWise'], function($query) use ($input){
                $query->where('serviceLineWise', 1)
                    ->where('serviceLineSystemID', $input['serviceLineSystemID']);
            })
            ->when((isset($input['serviceLineWise']) && !$input['serviceLineWise']) || !isset($input['serviceLineWise']), function($query) {
                $query->where('serviceLineWise', 0)
                    ->whereNull('serviceLineSystemID');
            })
            ->when(isset($input['valueWise']) && $input['valueWise'], function($query) use ($input){
                $query->where('valueWise', 1)
                    ->where('valueFrom', $input['valueFrom'])
                    ->where('valueTo', $input['valueTo']);
            })
            ->when((isset($input['valueWise']) && !$input['valueWise']) || !isset($input['valueWise']), function($query) {
                $query->where('valueWise', 0)
                    ->where('valueFrom', 0)
                    ->where('valueTo', 0);
            })
            ->when(isset($input['tenderTypeId']) && $input['tenderTypeId'] == -1, function ($query) {
                $query->where(function ($query) {
                    $query->where('tenderTypeId', -1)
                        ->orWhereNull('tenderTypeId');
                });
            })
            ->when(array_key_exists('tenderTypeId', $input) && $input['tenderTypeId'] != -1 && !is_null($input['tenderTypeId']), function ($query) use ($input) {
                $query->where('tenderTypeId', $input['tenderTypeId']);
            })
            ->when(!array_key_exists('tenderTypeId', $input) || is_null($input['tenderTypeId']), function ($query) {
                $query->where(function ($query) {
                    $query->where('tenderTypeId', -1)
                        ->orWhereNull('tenderTypeId');
                });
            })
            ->when(isset($input['approvalLevelID']), function($query) use ($input) {
                $query->where('approvalLevelID', '!=', $input['approvalLevelID']);
            })
            ->when(isset($input['workflow']), function ($query) use ($input) {
                $query->where(function ($query) use ($input) {
                    $query->where('workflow', $input['workflow']);
                });
            })
            ->when($activate && (isset($input['documentSystemID']) && $input['documentSystemID'] == 1) && (isset($input['prTypeWise']) && $input['prTypeWise']), function ($query) use ($input) {
                $query->where('prType', $input['prType']);
            })
            ->where('isActive', -1)
            ->first();

        if ($checkDuplicateApproval) {
            return ['status' => false, 'message' => trans('custom.approval_level_already_exists')];
        }

        $checkPreviousLevels = ApprovalLevel::where('companySystemID', $input["companySystemID"])
            ->where('documentSystemID', $input['documentSystemID'])
            ->where(function($query) {
                $query->where('isCategoryWiseApproval', -1)
                    ->orWhere('serviceLineWise', 1)
                    ->orWhere('valueWise', 1);
            })
            ->where('isActive', -1)
            ->first();

        $action = $activate ? "active/inactive" : "create";

        if (($checkPreviousLevels && $checkPreviousLevels->isCategoryWiseApproval == -1) && ((isset($input['isCategoryWiseApproval']) && !$input['isCategoryWiseApproval']) || !isset($input['isCategoryWiseApproval']))) {
            return ['status' => false, 'message' => trans('custom.approval_level_category_wise_exists', ['action' => $action])];
        }

        if (($checkPreviousLevels && $checkPreviousLevels->serviceLineWise) && ((isset($input['serviceLineWise']) && !$input['serviceLineWise']) || !isset($input['serviceLineWise']))) {
            return ['status' => false, 'message' => trans('custom.approval_level_segment_wise_exists', ['action' => $action])];
        }

        if (($checkPreviousLevels && $checkPreviousLevels->valueWise) && ((isset($input['valueWise']) && !$input['valueWise']) || !isset($input['valueWise']))) {
            return ['status' => false, 'message' => trans('custom.approval_level_value_wise_exists', ['action' => $action])];
        }


        if ($activate) {
            $documentConf = CompanyDocumentAttachment::where('companySystemID', $input["companySystemID"])
                ->where('documentSystemID', $input['documentSystemID'])
                ->first();

            if ($documentConf) {
                $isCategoryWiseApproval = isset($input['isCategoryWiseApproval']) && ($input['isCategoryWiseApproval'] || $input['isCategoryWiseApproval'] == -1) ? -1 : 0;
                $serviceLineWise = isset($input['serviceLineWise']) && ($input['serviceLineWise'] || $input['serviceLineWise'] == 1) ? -1 : 0;
                $valueWise = isset($input['valueWise']) && ($input['valueWise'] || $input['valueWise'] == 1) ? -1 : 0;
                $prTypeWise = isset($input['prTypeWise']) && ($input['prTypeWise'] || $input['prTypeWise'] == 1) ? -1 : 0;
                $docSubcategoryApproval = \App\Services\CompanyDocumentAttachmentService::isApprovalEnabled($documentConf->isSubcategoryApproval ?? 0);

                if (($isCategoryWiseApproval != $documentConf->isCategoryApproval) || ($serviceLineWise != $documentConf->isServiceLineApproval) || ($valueWise != $documentConf->isAmountApproval) || ($prTypeWise != $documentConf->isPRTypeApproval)) {
                    return ['status' => false, 'message' => trans('custom.approval_level_criteria_differ')];
                }
                if ($input['documentSystemID'] == 3 && $docSubcategoryApproval && empty($input['subcategoryID'])) {
                    return ['status' => false, 'message' => trans('custom.approval_level_criteria_differ')];
                }
            }
        }

        return ['status' => true];

    }


    public function activateApprovalLevel(Request $request)
    {
        $approvalLevel = $this->approvalLevelRepository->findWithoutFail($request->approvalLevelID);

        if (empty($approvalLevel)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.approval_levels')]));
        }

        if ($request->isActive && (int) $approvalLevel->documentSystemID === 3 && !empty($approvalLevel->subcategoryID)) {
            $documentConf = CompanyDocumentAttachment::where('companySystemID', $approvalLevel->companySystemID)
                ->where('documentSystemID', $approvalLevel->documentSystemID)
                ->first();
            $docSubcategoryApproval = $documentConf
                ? \App\Services\CompanyDocumentAttachmentService::isApprovalEnabled($documentConf->isSubcategoryApproval ?? 0)
                : false;

            if (!$docSubcategoryApproval) {
                return $this->sendError(trans('custom.subcategory_approval_disabled_enable_before_activate'), 500);
            }
        }

        if ($approvalLevel->documentSystemID == 4) {
            if ($request->isActive) {
                $pvActivationValidation = $this->approvalLevelService->validatePaymentVoucherActivation($approvalLevel, $request);
                if (!$pvActivationValidation['status']) {
                    return $this->sendError($pvActivationValidation['message'], 500);
                }
            }
        } 
        else {
            $approvalLevelValidation = $this->approvalLevelValidation($approvalLevel->toArray(), true);

            if (!$approvalLevelValidation['status']) {
                return $this->sendError($approvalLevelValidation['message'], 500);
            }
        }

        if ($request->isActive) {
            $approvalLevel->isActive = -1;
        } else {
            $approvalLevel->isActive = 0;
        }
        $approvalLevel->save();

        $approvalRole = "";
        if ($approvalLevel->isActive) {
            $isExist = ApprovalRole::where('approvalLevelID', $request->approvalLevelID)->exists();
            if (!$isExist) {
                $approvalRollMaster = [];
                if ($approvalLevel) {
                    for ($i = 1; $i <= $approvalLevel->noOfLevels; $i++) {
                        $approvalRollMaster[] = array('rollDescription' => $approvalLevel->levelDescription . ' ' . $i, 'documentSystemID' => $approvalLevel->documentSystemID, 'documentID' => $approvalLevel->documentID, 'companySystemID' => $approvalLevel->companySystemID, 'companyID' => $approvalLevel->companyID, 'departmentSystemID' => $approvalLevel->departmentSystemID, 'departmentID' => $approvalLevel->departmentID, 'serviceLineSystemID' => $approvalLevel->serviceLineSystemID, 'serviceLineID' => $approvalLevel->serviceLineCode, 'rollLevel' => $i, 'approvalLevelID' => $approvalLevel->approvalLevelID);
                    }
                    ApprovalRole::insert($approvalRollMaster);
                }
            }
        }
        $approvalRole = ApprovalRole::with(['approval_group','company' => function ($query) {
            // $query->select('CompanyName');
        }, 'department' => function ($query) {
            //$query->select('DepartmentDescription');
        }, 'document' => function ($query) {
            //$query->select('documentDescription');
        }, 'serviceline' => function ($query) {
            //$query->select('ServiceLineDes');
        }])->where('approvalLevelID', $request->approvalLevelID)->orderBy('rollLevel', 'asc')->get();

        return $this->sendResponse($approvalRole->toArray(), trans('custom.update', ['attribute' => trans('custom.record')]));

    }

}