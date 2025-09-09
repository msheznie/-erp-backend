<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\AddAttachmentAPIRequest;
use App\Http\Requests\API\CompanyValidateAPIRequest;
use App\Http\Requests\API\CreateContractMasterAPIRequest;
use App\Http\Requests\API\CreateTenderMasterAPIRequest;
use App\Http\Requests\API\UpdateTenderMasterAPIRequest;
use App\Http\Requests\API\ViewContractAPIRequest;
use App\Http\Requests\DeleteAttachmentAPIRequest;
use App\Http\Requests\SRM\UpdateTenderCalendarDaysRequest;
use App\Models\BankAccount;
use App\Models\BankMaster;
use App\Models\CalendarDates;
use App\Models\CalendarDatesDetail;
use App\Models\CalendarDatesDetailEditLog;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\Employee;
use App\Models\ProcumentActivityEditLog;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\Models\SrmBudgetItem;
use App\Models\SrmDepartmentMaster;
use App\Models\SrmTenderBudgetItem;
use App\Models\SRMTenderCalendarLog;
use App\Models\SrmTenderDepartment;
use App\Models\SupplierRegistrationLink;
use App\Models\SupplierTenderNegotiation;
use App\Models\SystemConfigurationAttributes;
use App\Models\TenderBidNegotiation;
use App\Models\TenderCustomEmail;
use App\Models\TenderDepartmentEditLog;
use App\Models\TenderMasterReferred;
use App\Models\TenderNegotiation;
use App\Models\EmployeesDepartment;
use App\Models\EnvelopType;
use App\Models\EvaluationCriteriaDetails;
use App\Models\EvaluationType;
use App\Models\PricingScheduleMaster;
use App\Models\ProcumentActivity;
use App\Models\ScheduleBidFormatDetails;
use App\Models\SupplierCategory;
use App\Models\SupplierCategoryMaster;
use App\Models\TenderBoqItems;
use App\Models\TenderMainWorks;
use App\Models\TenderMaster;
use App\Models\TenderNegotiationArea;
use App\Models\TenderProcurementCategory;
use App\Models\TenderPurchaseRequest;
use App\Models\TenderPurchaseRequestEditLog;
use App\Models\TenderSiteVisitDates;
use App\Models\TenderType;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Models\YesNoSelection;
use App\Models\SrmTenderMasterEditLog;
use App\Repositories\TenderMasterRepository;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\
git;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailForQueuing;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Models\TenderDocumentTypeAssign;
use App\Models\TenderDocumentTypes;
use App\Models\TenderSupplierAssignee;
use App\Repositories\SupplierRegistrationLinkRepository;
use App\Models\PricingScheduleDetail;
use App\Models\TenderMasterSupplier;
use App\Models\BidEvaluationSelection;
use App\Models\BidSubmissionMaster;
use App\Models\BidSubmissionDetail;
use App\Models\CommercialBidRankingItems;
use App\Repositories\CommercialBidRankingItemsRepository;
use App\Models\BidBoq;
use App\Models\BidMainWork;
use App\Repositories\TenderFinalBidsRepository;
use App\Models\TenderFinalBids;
use App\Models\DocumentModifyRequest;
use App\Models\TenderCirculars;
use App\Models\CircularAmendments;
use App\Repositories\DocumentModifyRequestRepository;
use App\helper\email;
use App\Services\SrmDocumentModifyService;
use App\Services\SrmTenderEditAmendService;

/**
 * Class TenderMasterController
 * @package App\Http\Controllers\API
 */

class TenderMasterAPIController extends AppBaseController
{
    /** @var  TenderMasterRepository */
    private $tenderMasterRepository;
    private $registrationLinkRepository;
    private $commercialBidRankingItemsRepository;
    private $tenderFinalBidsRepository;
    private $documentModifyRequestRepository;
    private $documentModifyService;
    private $srmTenderEditAmendService;
    public function __construct(DocumentModifyRequestRepository $documentModifyRequestRepo, TenderFinalBidsRepository $tenderFinalBidsRepo, CommercialBidRankingItemsRepository $commercialBidRankingItemsRepo, TenderMasterRepository $tenderMasterRepo, SupplierRegistrationLinkRepository $registrationLinkRepository, SrmDocumentModifyService $documentModifyService, SrmTenderEditAmendService $srmTenderEditAmendService)
    {
        $this->tenderMasterRepository = $tenderMasterRepo;
        $this->registrationLinkRepository = $registrationLinkRepository;
        $this->commercialBidRankingItemsRepository = $commercialBidRankingItemsRepo;
        $this->tenderFinalBidsRepository = $tenderFinalBidsRepo;
        $this->documentModifyRequestRepository = $documentModifyRequestRepo;
        $this->documentModifyService = $documentModifyService;
        $this->srmTenderEditAmendService = $srmTenderEditAmendService;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderMasters",
     *      summary="Get a listing of the TenderMasters.",
     *      tags={"TenderMaster"},
     *      description="Get all TenderMasters",
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
     *                  @SWG\Items(ref="#/definitions/TenderMaster")
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
        $this->tenderMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderMasters = $this->tenderMasterRepository->all();

        return $this->sendResponse($tenderMasters->toArray(), 'Tender Masters retrieved successfully');
    }

    /**
     * @param CreateTenderMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderMasters",
     *      summary="Store a newly created TenderMaster in storage",
     *      tags={"TenderMaster"},
     *      description="Store TenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderMaster")
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
     *                  ref="#/definitions/TenderMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderMasterAPIRequest $request)
    {
        $input = $request->all();

        $tenderMaster = $this->tenderMasterRepository->create($input);

        return $this->sendResponse($tenderMaster->toArray(), 'Tender Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderMasters/{id}",
     *      summary="Display the specified TenderMaster",
     *      tags={"TenderMaster"},
     *      description="Get TenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMaster",
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
     *                  ref="#/definitions/TenderMaster"
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
        /** @var TenderMaster $tenderMaster */
        $tenderMaster = $this->tenderMasterRepository->withCount(['criteriaDetails',
            'criteriaDetails AS go_no_go_count' => function ($query) {
                $query->where('critera_type_id', 1);
            },
            'criteriaDetails AS technical_count' => function ($query) {
                $query->where('critera_type_id', 2);
            }
        ])->findWithoutFail($id);

        if (empty($tenderMaster)) {
            return $this->sendError('Tender Master not found');
        }

        return $this->sendResponse($tenderMaster->toArray(), 'Tender Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTenderMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderMasters/{id}",
     *      summary="Update the specified TenderMaster in storage",
     *      tags={"TenderMaster"},
     *      description="Update TenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderMaster")
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
     *                  ref="#/definitions/TenderMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderMaster $tenderMaster */
        $tenderMaster = $this->tenderMasterRepository->findWithoutFail($id);

        if (empty($tenderMaster)) {
            return $this->sendError('Tender Master not found');
        }

        $tenderMaster = $this->tenderMasterRepository->update($input, $id);

        return $this->sendResponse($tenderMaster->toArray(), 'TenderMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderMasters/{id}",
     *      summary="Remove the specified TenderMaster from storage",
     *      tags={"TenderMaster"},
     *      description="Delete TenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMaster",
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
        /** @var TenderMaster $tenderMaster */
        $tenderMaster = $this->tenderMasterRepository->findWithoutFail($id);

        if (empty($tenderMaster)) {
            return $this->sendError('Tender Master not found');
        }

        $tenderMaster->delete();

        return $this->sendSuccess('Tender Master deleted successfully');
    }

    public function getTenderMasterList(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];



        $tenderMaster = TenderMaster::with(['tender_type', 'envelop_type', 'currency','approvedRejectStatus'=>function($q) use ($companyId, $input){
            $q->select('documentSystemCode','status')
                ->where('companySystemID', $companyId)
                ->where('documentSystemID', isset($input['rfx']) && $input['rfx'] ? 113 : 108);
        }])->where('company_id', $companyId);

        $filters = $this->getFilterData($input);

        if ($filters['currencyId'] && count($filters['currencyId']) > 0) {
            $tenderMaster->whereIn('currency_id', $filters['currencyId']);
        }

        if ($filters['selection']) {
            $tenderMaster->where('tender_type_id', $filters['selection']);
        }

        if ($filters['envelope']) {
            $tenderMaster->where('envelop_type_id', $filters['envelope']);
        }

        if ($filters['published']) {
            $ids = array_column($filters['published'], 'id');
            $tenderMaster->whereIn('published_yn', $ids);
        }

        if ($filters['rfxType']) {
            $tenderMaster->where('document_type', $filters['rfxType']);
        }

        if ($filters['status']) {
            $ids = array_column($filters['status'], 'id');
            $tenderMaster->where(function ($query) use ($ids) {
                if (in_array(1, $ids)) {
                    $query->orWhere(function ($query) {
                        $query->where('confirmed_yn', 0)
                            ->where('approved', 0)
                            ->where('refferedBackYN', 0);
                    });
                }

                if (in_array(2, $ids)) {
                    $query->orWhere(function ($query) {
                        $query->where('confirmed_yn', 1)
                            ->where('approved', 0)
                            ->where('refferedBackYN', 0);
                    });
                }

                if (in_array(3, $ids)) {
                    $query->orWhere(function ($query) {
                        $query->where('confirmed_yn', 1)
                            ->where('approved', -1)
                            ->where('refferedBackYN', 0);
                    });
                }

                if (in_array(4, $ids)) {
                    $query->orWhere(function ($query) {
                        $query->where('confirmed_yn', 1)
                            ->where('approved', 0)
                            ->where('refferedBackYN', -1);
                    });

                    $query->WhereDoesntHave('approvedRejectStatus');
                }

                if (in_array(5, $ids)) {
                    $query->orWhere(function ($query) {
                        $query->where('confirmed_yn', 1)
                            ->where('approved', 0)
                            ->where('refferedBackYN', -1);
                    });

                    $query->whereHas('approvedRejectStatus');
                }
            });
        }

        if (isset($input['rfx']) && $input['rfx']) {
            $tenderMaster = $tenderMaster->where('document_type', '!=', 0);
        } else {
            $tenderMaster = $tenderMaster->where('document_type', 0);
        }

        $search = $request->input('search.value');
        if ($search) {
            $tenderMaster = $tenderMaster->where(function ($query) use ($search) {
                $query->orWhereHas('tender_type', function ($query1) use ($search) {
                    $query1->where('name', 'LIKE', "%{$search}%");
                });
                $query->orWhereHas('envelop_type', function ($query1) use ($search) {
                    $query1->where('name', 'LIKE', "%{$search}%");
                });
                $query->orWhereHas('currency', function ($query1) use ($search) {
                    $query1->where('CurrencyName', 'LIKE', "%{$search}%");
                    $query1->orWhere('CurrencyCode', 'LIKE', "%{$search}%");
                });
                $query->orWhere('description', 'LIKE', "%{$search}%");
                $query->orWhere('title', 'LIKE', "%{$search}%");
                $query->orWhere('tender_code', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($tenderMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getTenderDropDowns(Request $request)
    {
        $input = $request->all();
        return $this->tenderMasterRepository->getTenderDropdowns($input);
    }

    public function createTender(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('currency_id', 'tender_type_id'));
        $employee = \Helper::getEmployeeInfo();
        if (isset($input['rfx']) && $input['rfx']) {
            $exist = TenderMaster::where('title', $input['title'])->where('company_id', $input['companySystemID'])->where('document_type', '!=', 0)->first();
        } else {
            $exist = TenderMaster::where('title', $input['title'])->where('company_id', $input['companySystemID'])->where('document_type', 0)->first();
        }

        if (!empty($exist)) {
            if (isset($input['rfx']) && $input['rfx']) {
                return ['success' => false, 'message' => trans('srm_tender_rfx.rfx_title_cannot_be_duplicated')];
            } else {
                return ['success' => false, 'message' => trans('srm_tender_rfx.tender_title_cannot_be_duplicated')];
            }
        }
        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        $documentMaster = DocumentMaster::where('documentSystemID', 108)->first();
        $lastSerial = TenderMaster::where('company_id', $input['companySystemID'])
            ->where('document_system_id', 108)
            ->orderBy('id', 'desc')
            ->first();
        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serial_number) + 1;
        }

        $tenderCode = ($company->CompanyID . '/' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
        $document_system_id = 108;

        if (isset($input['rfx']) && $input['rfx']) {
            $lastSerialNumber = 1;
            $documentMaster = DocumentMaster::where('documentSystemID', 113)->first();
            $lastSerial = TenderMaster::where('company_id', $input['companySystemID'])
                ->where('document_system_id', 113)
                ->orderBy('id', 'desc')
                ->first();

            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serial_number) + 1;
            }

            $tenderCode =  ($company->CompanyID . '/' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $document_system_id = 113;
        }

        DB::beginTransaction();
        try {
            $data['uuid'] = Helper::generateSRMUuid(16);
            $data['currency_id'] = isset($input['currency_id']) ? $input['currency_id'] : null;
            $data['description'] = isset($input['description']) ? $input['description'] : null;
            $data['envelop_type_id'] = isset($input['envelop_type_id']) ? $input['envelop_type_id'] : null;
            $data['tender_type_id'] = $input['tender_type_id'];
            $data['title'] = $input['title'];
            $data['document_system_id'] = $document_system_id;
            $data['document_id'] = $documentMaster['documentID'];
            $data['company_id'] = $input['companySystemID'];
            $data['created_by'] = $employee->employeeSystemID;
            $data['tender_document_fee'] = null;
            $data['tender_code'] = $tenderCode;
            $data['serial_number'] = $lastSerialNumber;
            $data['document_type'] = isset($input['rfx']) ? $input['document_type'] : 0;
            $data['isDelegation'] = $input['isDelegation'] ?? 0;
            $result = TenderMaster::create($data);

            if ($result) {
                DB::commit();
                return ['success' => true, 'message' => 'Successfully saved', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function deleteTenderMaster(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $data['deleted_by'] = $employee->employeeSystemID;
            $data['deleted_at'] = now();
            $result = TenderMaster::where('id', $input['id'])->update($data);
            if ($result) {
                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }
    public function getTenderMasterData(Request $request)
    {
        $input = $request->all();
        return $this->tenderMasterRepository->getTenderMasterData($input);
    }

    public function loadTenderSubCategory(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($request->all(), array('procument_cat_id'));
        $tenderMaster = TenderMaster::where('id', $input['tenderMasterId'])->first();

        if (!empty($tenderMaster['procument_sub_cat_id'])) {
            $category = TenderProcurementCategory::where('id', $tenderMaster['procument_sub_cat_id'])->first();
        } else {
            $category['is_active'] = 1;
        }
        if ($input['procument_cat_id'] > 0) {
            $data['procurementSubCategory'] = TenderProcurementCategory::where('parent_id', $input['procument_cat_id'])->where('is_active', 1)->get();
        } else {
            $data['procurementSubCategory'] = array();
        }


        if ($tenderMaster['confirmed_yn'] == 1 && $category['is_active'] == 0) {
            $data['procurementSubCategory'][] = $category;
        }

        return $data;
    }

    public function loadTenderBankAccount(Request $request)
    {
        $input = $request->all();
        $data['bankAccountDrop'] = array();
        if (!empty($input['bank_id'])) {
            $data['bankAccountDrop'] = BankAccount::where('bankmasterAutoID', $input['bank_id'])->where('companySystemID', $input['companySystemID'])->get();
        }


        return $data;
    }

    public function updateTender(Request $request)
    {
        $input = $this->convertArrayToSelectedValue($request->all(), array(
            'bank_account_id', 'bank_id', 'currency_id', 'currency_id', 'procument_cat_id',
            'procument_sub_cat_id', 'tender_type_id', 'envelop_type_id', 'evaluation_type_id'
        ));

        $requestData = $this->documentModifyService->checkForEditOrAmendRequest($input['id']);
        $amdID = $input['amd_id'] ?? 0;
        $editOrAmend = $requestData['enableRequestChange'] ?? false;
        $versionID = $requestData['versionID'] ?? 0;
        $tenderMaster = $this->tenderMasterRepository->getTenderExistData($input['id'], $editOrAmend, $versionID);
        $checkMinApprovalBidOpening = $this->tenderMasterRepository->checkTenderBidEmployeesAdded($input['id'], $editOrAmend, $amdID, $versionID);

        if (!$checkMinApprovalBidOpening['success']) {
            return ['status' => false, 'message' => $checkMinApprovalBidOpening['message']];
        }

        if ($input['addCalendarDates'] === 0) {
            $resValidate = $this->validateTenderHeader($input);
            if (!$resValidate['status']) {
                return $this->sendError($resValidate['message'], 422);
            }
        } else {
            return $this->updateCalenderDates($input);
        }

        $rfq = false;

        if (isset($input['rfq'])) {
            $rfq = ($input['rfq'] == true) ? true : false;
        }

        $condition = $this->validateDates($input['id'], $rfq, $editOrAmend, $versionID);
        if(isset($condition['success']) && $condition['success'] == false){
            return $this->validateDates($input['id'], $rfq, $editOrAmend, $versionID);
        }

        $documentSystemID = $rfq ? 113 : 108;

        $site_visit_date = null;
        $technical_bid_opening_time = null;
        $technical_bid_opening_date = null;
        $commerical_bid_opening_time = null;
        $commerical_bid_opening_date = null;
        $bid_opeing_end_time = null;
        $bid_opeing_end_date = null;
        $technical_bid_closing_date = null;
        $technical_bid_closing_time = null;
        $commerical_bid_closing_date = null;
        $commerical_bid_closing_time = null;
        $bid_opeing_end_date = null;
        $bid_opeing_end_time = null;
        $site_visit_time = null;
        $site_visit_end_date = null;
        $site_visit_end_time = null;
        $bid_opening_time = null;
        $bid_opeing_end_time = null;
        $bid_opening_date = null;
        $document_sales_start_date = null;
        $document_sales_end_date = null;
        $pre_bid_clarification_start_date = null;
        $pre_bid_clarification_end_date = null;
        $bankId = (empty($input['bank_id'])) ? 0 : $input['bank_id'];

        if (isset($input['document_sales_start_date'])) {
            $document_sales_start_time = ($input['document_sales_start_time']) ? new Carbon($input['document_sales_start_time']) : null;
            $document_sales_start_date = new Carbon($input['document_sales_start_date']);
            $document_sales_start_date = ($input['document_sales_start_time']) ? $document_sales_start_date->format('Y-m-d') . ' ' . $document_sales_start_time->format('H:i:s') : $document_sales_start_date->format('Y-m-d');
        }

        if (isset($input['document_sales_end_time'])) {
            $document_sales_end_time =  ($input['document_sales_end_time']) ?  new Carbon($input['document_sales_end_time']) : null;
            $document_sales_end_date = new Carbon($input['document_sales_end_date']);
            $document_sales_end_date = ($input['document_sales_end_time']) ? $document_sales_end_date->format('Y-m-d') . ' ' . $document_sales_end_time->format('H:i:s') : $document_sales_end_date->format('Y-m-d');
        }

        if (isset($input['bid_submission_opening_time'])) {
            $bid_submission_opening_time =  ($input['bid_submission_opening_time']) ? new Carbon($input['bid_submission_opening_time']) : null;
            $bid_submission_opening_date = new Carbon($input['bid_submission_opening_date']);
            $bid_submission_opening_date = ($input['bid_submission_opening_time']) ? $bid_submission_opening_date->format('Y-m-d') . ' ' . $bid_submission_opening_time->format('H:i:s') : $bid_submission_opening_date->format('Y-m-d');
        }

        if (isset($input['bid_submission_closing_time'])) {
            $bid_submission_closing_time =  ($input['bid_submission_closing_time']) ? new Carbon($input['bid_submission_closing_time']) : null;
            $bid_submission_closing_date = new Carbon($input['bid_submission_closing_date']);
            $bid_submission_closing_date = ($input['bid_submission_closing_time']) ? $bid_submission_closing_date->format('Y-m-d') . ' ' . $bid_submission_closing_time->format('H:i:s') : $bid_submission_closing_date->format('Y-m-d');
        }

        if (isset($input['pre_bid_clarification_start_time'])) {
            $pre_bid_clarification_start_time =  ($input['pre_bid_clarification_start_time']) ? new Carbon($input['pre_bid_clarification_start_time']) : null;
            $pre_bid_clarification_start_date = new Carbon($input['pre_bid_clarification_start_date']);
            $pre_bid_clarification_start_date = ($input['pre_bid_clarification_start_time']) ? $pre_bid_clarification_start_date->format('Y-m-d') . ' ' . $pre_bid_clarification_start_time->format('H:i:s') : $pre_bid_clarification_start_date->format('Y-m-d');
        }

        if (isset($input['pre_bid_clarification_end_time'])) {
            $pre_bid_clarification_end_time =  ($input['pre_bid_clarification_end_time']) ?  new Carbon($input['pre_bid_clarification_end_time']) : null;
            $pre_bid_clarification_end_date = new Carbon($input['pre_bid_clarification_end_date']);
            $pre_bid_clarification_end_date = ($input['pre_bid_clarification_end_time']) ?  $pre_bid_clarification_end_date->format('Y-m-d') . ' ' . $pre_bid_clarification_end_time->format('H:i:s') : $pre_bid_clarification_end_date->format('Y-m-d');
        }

        if ($input['site_visit_date']) {
            $site_visit_time = ($input['site_visit_start_time']) ?  new Carbon($input['site_visit_start_time']) : null;
            $site_visit_date = new Carbon($input['site_visit_date']);
            $site_visit_date =  ($input['site_visit_start_time']) ?  $site_visit_date->format('Y-m-d') . ' ' . $site_visit_time->format('H:i:s') : $site_visit_date->format('Y-m-d');
        }

        if ($input['site_visit_end_date']) {
            $site_visit_end_time = ($input['site_visit_end_time']) ? new Carbon($input['site_visit_end_time']) : null;
            $site_visit_end_date = new Carbon($input['site_visit_end_date']);
            $site_visit_end_date = ($input['site_visit_end_time']) ? $site_visit_end_date->format('Y-m-d') . ' ' . $site_visit_end_time->format('H:i:s') : $site_visit_end_date->format('Y-m-d');
        }

        $currenctDate = Carbon::now();

        // vaidation lists
        if (!isset($input['document_sales_start_time'])) {
            if (isset($input['document_sales_start_date']) && $rfq) {
                return ['success' => false, 'message' => 'Document sales from time is required'];
            } elseif (!$rfq) {
                return ['success' => false, 'message' => 'Document sales from time is required'];
            }
        }

        if (!isset($input['document_sales_end_time'])) {
            if (isset($input['document_sales_end_date']) && $rfq) {
                return ['success' => false, 'message' => 'Document sales to time is required'];
            } elseif (!$rfq) {
                return ['success' => false, 'message' => 'Document sales to time is required'];
            }
        }

        if ((isset($document_sales_start_date) && isset($document_sales_end_date)) && (($document_sales_start_date > $document_sales_end_date))) {
            return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time  for Document Sales'];
        }

        if ((isset($pre_bid_clarification_start_date) && isset($pre_bid_clarification_end_date)) && (($pre_bid_clarification_start_date > $pre_bid_clarification_end_date))) {
            return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time  for Pre-bid Clarification'];
        }

        if (($site_visit_date > $site_visit_end_date)) {
            if (isset($input['site_visit_date']) && isset($input['site_visit_end_date'])) {
                return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time  for Site Visit'];
            } elseif (!$rfq) {
                return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time  for Site Visit'];
            }
        }

        if (!isset($input['bid_submission_opening_time'])) {
            return ['success' => false, 'message' => 'Bid submission from time is required'];
        }


        if (isset($input['pre_bid_clarification_start_date']) && !isset($input['pre_bid_clarification_start_time'])) {
            return ['success' => false, 'message' => 'Pre-bid Clarification from time is required'];
        }

        if (isset($input['pre_bid_clarification_end_date']) && !isset($input['pre_bid_clarification_end_time'])) {
            return ['success' => false, 'message' => 'Pre-bid Clarification to time is required'];
        }

        if (isset($input['site_visit_date']) && !isset($input['site_visit_start_time'])) {
            return ['success' => false, 'message' => 'Site Visit from time is required'];
        }

        if (isset($input['site_visit_end_date']) && !isset($input['site_visit_end_time'])) {
            return ['success' => false, 'message' => 'Site Visit to time is required'];
        }

        if (!isset($input['bid_submission_closing_time'])) {
            return ['success' => false, 'message' => 'Bid submission to time is required'];
        }

        if ($bid_submission_opening_date > $bid_submission_closing_date) {
            return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time  for Bid Submission'];
        }

        if((isset($input['isProcessCompleteBeforeClosing']) && $input['isProcessCompleteBeforeClosing'] != 1)){
            if (isset($document_sales_start_date) && $document_sales_start_date < $currenctDate || isset($bid_submission_opening_date) && $bid_submission_opening_date < $currenctDate || isset($pre_bid_clarification_start_date) && $pre_bid_clarification_start_date < $currenctDate || isset($site_visit_date) && $site_visit_date < $currenctDate) {
                return ['success' => false, 'message' => 'All the date and time should greater than current date and time'];
            }
        }

        if (is_null($bid_submission_closing_date)) {
            $bid_sub_date = $bid_submission_opening_date;
        } else {
            $bid_sub_date = $bid_submission_closing_date;
        }

        if (isset($document_sales_start_date) && isset($pre_bid_clarification_start_date) && ($document_sales_start_date > $pre_bid_clarification_start_date)) {
            return ['success' => false, 'message' => 'Pre-bid Clarification from date and time should greater than document sale from date and time'];
        }

        if (isset($pre_bid_clarification_start_date) && ($pre_bid_clarification_start_date > $bid_submission_closing_date)) {
            return ['success' => false, 'message' => 'Pre-bid Clarification from date and time should less than bid submission to date and time'];
        }

        if (isset($pre_bid_clarification_start_date) && ($pre_bid_clarification_end_date >= $bid_submission_closing_date)) {
            return ['success' => false, 'message' => 'Pre-bid Clarification to date and time should less than bid submission to date and time'];
        }

        if (!$rfq && $document_sales_start_date > $bid_submission_opening_date) {
            return ['success' => false, 'message' => 'Bid submission from date and time should greater than document sales from date and time'];
        }

        if (!is_null($input['stage']) || $input['stage'] != 0) {

            if ($input['stage'][0] == 1 || $input['stage'] == 1) {

                if (isset($input['bid_opening_date_time'])) {
                    $bid_opening_time =  ($input['bid_opening_date_time']) ?  new Carbon($input['bid_opening_date_time']) : null;
                    $bid_opening_date = new Carbon($input['bid_opening_date']);
                    $bid_opening_date = ($input['bid_opening_date_time']) ? $bid_opening_date->format('Y-m-d') . ' ' . $bid_opening_time->format('H:i:s') : $bid_opening_date->format('Y-m-d');
                }

                if ((isset($input['bid_opening_end_date']))) {
                    $bid_opeing_end_time = (isset($input['bid_opening_end_date_time'])) ? new Carbon($input['bid_opening_end_date_time']) : null;
                    $bid_opeing_end_date = (isset($input['bid_opening_end_date'])) ? new Carbon($input['bid_opening_end_date']) : null;
                    $bid_opeing_end_date = (isset($input['bid_opening_end_date_time'])) ? $bid_opeing_end_date->format('Y-m-d') . ' ' . $bid_opeing_end_time->format('H:i:s') : $bid_opeing_end_date->format('Y-m-d');
                } else {
                    $bid_opeing_end_date = null;
                    $bid_opeing_end_time = null;
                }


                if (is_null($input['bid_submission_opening_date'])) {
                    return ['success' => false, 'message' => 'Bid Submission date cannot be empty'];
                }

                if (is_null($input['bid_opening_date_time'])) {
                    if ($input['bid_opening_date'] && $rfq) {
                        return ['success' => false, 'message' => 'Bid Opening Time cannot be empty'];
                    } elseif (!$rfq) {
                        return ['success' => false, 'message' => 'Bid Opening Time cannot be empty'];
                    }
                }


                if (is_null($bid_submission_closing_date)) {
                    if ($bid_sub_date > $bid_opening_date) {
                        return ['success' => false, 'message' => 'Bid Opening from date and time should greater than bid submission from date and time'];
                    }
                } else {

                    if ($bid_sub_date >= $bid_opening_date) {
                        if (isset($bid_opening_date) && $rfq) {
                            return ['success' => false, 'message' => 'Bid Opening from date and time should greater than bid submission to date and time'];
                        } elseif (!$rfq) {
                            return ['success' => false, 'message' => 'Bid Opening from date and time should greater than bid submission to date and time'];
                        }
                    }
                }


                if (isset($bid_opeing_end_date)) {
                    if (is_null($input['bid_opening_end_date_time'])) {
                        return ['success' => false, 'message' => 'Bid Opening to time cannot be empty'];
                    }

                    if ($bid_opening_date > $bid_opeing_end_date) {
                        return ['success' => false, 'message' => 'Bid Opening to date and time should greater than bid opening from date and time'];
                    }
                }
            }



            if ($input['stage'][0] == 2 || $input['stage'] == 2) {

                if (is_null($input['technical_bid_opening_date']) && !$rfq) {
                    return ['success' => false, 'message' => 'Technical Bid Opening from date cannot be empty'];
                }

                if (is_null($input['technical_bid_opening_date_time']) && isset($input['technical_bid_opening_date'])) {
                    if ($rfq && isset($input['technical_bid_opening_date'])) {
                        return ['success' => false, 'message' => 'Technical Bid Opening from time cannot be empty'];
                    } elseif (!$rfq) {
                        return ['success' => false, 'message' => 'Technical Bid Opening from time cannot be empty'];
                    }
                }

                if (isset($input['technical_bid_opening_date'])) {
                    $technical_bid_opening_time = ($input['technical_bid_opening_date_time']) ? new Carbon($input['technical_bid_opening_date_time']) : null;
                    $technical_bid_opening_date = new Carbon($input['technical_bid_opening_date']);
                    $technical_bid_opening_date = ($input['technical_bid_opening_date_time']) ? $technical_bid_opening_date->format('Y-m-d') . ' ' . $technical_bid_opening_time->format('H:i:s') : $technical_bid_opening_date->format('Y-m-d');
                }

                if (isset($input['technical_bid_closing_date'])) {
                    if (is_null($input['technical_bid_closing_date_time'])) {
                        return ['success' => false, 'message' => 'Technical bid opening to time cannot be empty'];
                    }

                    $technical_bid_closing_time = (isset($input['technical_bid_closing_date_time'])) ? new Carbon($input['technical_bid_closing_date_time']) : null;
                    $technical_bid_closing_date = (isset($input['technical_bid_closing_date'])) ? new Carbon($input['technical_bid_closing_date']) : null;
                    $technical_bid_closing_date = (isset($input['technical_bid_closing_date_time'])) ? $technical_bid_closing_date->format('Y-m-d') . ' ' . $technical_bid_closing_time->format('H:i:s') : $technical_bid_closing_date->format('Y-m-d');
                } else {
                    $technical_bid_closing_date = null;
                    $technical_bid_closing_time = null;
                }

                if (isset($input['commerical_bid_opening_date'])) {
                    $commerical_bid_opening_time = ($input['commerical_bid_opening_date_time']) ? new Carbon($input['commerical_bid_opening_date_time']) : null;
                    $commerical_bid_opening_date = new Carbon($input['commerical_bid_opening_date']);
                    $commerical_bid_opening_date = ($input['commerical_bid_opening_date_time']) ? $commerical_bid_opening_date->format('Y-m-d') . ' ' . $commerical_bid_opening_time->format('H:i:s') : $commerical_bid_opening_date->format('Y-m-d');

                    if (is_null($input['commerical_bid_opening_date_time']) && $rfq) {
                        return ['success' => false, 'message' => 'Commercial Bid Opening from time cannot be empty'];
                    }
                }

                if (isset($input['commerical_bid_closing_date'])) {
                    if (!(isset($input['commerical_bid_closing_date_time']))) {
                        return ['success' => false, 'message' => 'Commercial Bid Opening to time cannot be empty'];
                    }

                    $commerical_bid_closing_time = (isset($input['commerical_bid_closing_date_time'])) ? new Carbon($input['commerical_bid_closing_date_time']) : null;
                    $commerical_bid_closing_date = (isset($input['commerical_bid_closing_date'])) ? new Carbon($input['commerical_bid_closing_date']) : null;
                    $commerical_bid_closing_date = (isset($input['commerical_bid_closing_date_time'])) ? $commerical_bid_closing_date->format('Y-m-d') . ' ' . $commerical_bid_closing_time->format('H:i:s') : $commerical_bid_closing_date->format('Y-m-d');
                } else {
                    $commerical_bid_closing_date = null;
                    $commerical_bid_closing_time = null;
                }

                if (!is_null($commerical_bid_closing_date)) {
                    if ($commerical_bid_opening_date > $commerical_bid_closing_date) {
                        return ['success' => false, 'message' => 'Commercial Bid Opening to date and time should greater than commercial bid opening from date and time'];
                    }
                }

                if (is_null($input['technical_bid_opening_date_time'])) {
                    if ($rfq && isset($input['technical_bid_opening_date'])) {
                        return ['success' => false, 'message' => 'Technical Bid Opening Time cannot be empty'];
                    } elseif (!$rfq) {
                        return ['success' => false, 'message' => 'Technical Bid Opening Time cannot be empty'];
                    }
                } else {


                    if (is_null($bid_submission_closing_date)) {
                        if ($bid_sub_date > $technical_bid_opening_date) {
                            return ['success' => false, 'message' => 'Technical Bid Opening from date and time should greater than bid submission from date and time'];
                        }
                    } else {

                        if ($bid_sub_date > $technical_bid_opening_date) {
                            return ['success' => false, 'message' => 'Technical Bid Opening from date and time should greater than bid submission to date and time'];
                        }
                    }


                    if (is_null($input['commerical_bid_opening_date_time']) && !$rfq) {
                        return ['success' => false, 'message' => 'Commercial Bid Opening Time cannot be empty'];
                    }

                    if (is_null($technical_bid_closing_date)) {
                        if ($technical_bid_opening_date > $commerical_bid_opening_date && !$rfq) {
                            return ['success' => false, 'message' => 'Commercial Bid Opening from date and time should be greater than technical bid from date and time'];
                        } elseif (!is_null($input['commerical_bid_opening_date_time']) && ($technical_bid_opening_date > $commerical_bid_opening_date) && $rfq) {
                            return ['success' => false, 'message' => 'Commercial Bid Opening from date and time should be greater than technical bid from date and time'];
                        }
                    } else {
                        if (!$rfq && ($technical_bid_closing_date >= $commerical_bid_opening_date)) {
                            return ['success' => false, 'message' => 'Commercial Bid Opening from date and time should be greater than technical bid to date and time'];
                        } elseif ($rfq && !is_null($input['commerical_bid_opening_date_time']) && !is_null($input['technical_bid_opening_date_time']) && ($technical_bid_closing_date > $commerical_bid_opening_date)) {
                            return ['success' => false, 'message' => 'Commercial Bid Opening from date and time should be greater than technical bid to date and time'];
                        }
                    }
                }


                if (!empty($technical_bid_opening_date) && !empty($technical_bid_closing_date)) {
                    if ($technical_bid_opening_date > $technical_bid_closing_date) {
                        return ['success' => false, 'message' => 'Technical Bid Opening to date and time should greater than Technical Bid Opening from date and time'];
                    }
                }
            }


        }

        if ($rfq) {
            $existTndr = TenderMaster::where('title', $input['title'])->where('id', '!=', $input['id'])->where('company_id', $input['companySystemID'])->where('document_type', '!=', 0)->first();
        } else {
            $existTndr = TenderMaster::where('title', $input['title'])->where('id', '!=', $input['id'])->where('company_id', $input['companySystemID'])->where('document_type', 0)->first();
        }

        if (!empty($existTndr)) {
            if ($rfq) {
                return ['success' => false, 'message' => 'RFX title cannot be duplicated'];
            } else {
                return ['success' => false, 'message' => 'Tender title cannot be duplicated'];
            }
        }


        $employee = \Helper::getEmployeeInfo();
        $exist = $this->tenderMasterRepository->getTenderExistData($input['id'], $editOrAmend, $versionID);

        if (!isset($input['tender_document_fee'])) {
            $bankId = 0;
            $input['bank_account_id'] = null;
        }
        // Check Total Technical weightage
        $response = $this->tenderMasterRepository->validateTechnicalEvaluationCriteria($input['id'], 1, 2);

        if (!$response['success']) {
            return $response;
        }

        if($input['estimated_value'] > $input['allocated_budget'] ){
            return ['success' => false, 'message' => 'Estimated value cannot exceed the Allocated Budget Amount.'];
        }

        DB::beginTransaction();

        try {

            $data['title'] = $input['title'];
            $data['title_sec_lang'] = $input['title_sec_lang'];
            $data['description'] = $input['description'];
            $data['description_sec_lang'] = $input['description_sec_lang'];
            // $data['tender_type_id'] = $input['tender_type_id'];
            $data['currency_id'] = $input['currency_id'];
            // $data['envelop_type_id'] = $input['envelop_type_id'];
            $data['procument_cat_id'] = $input['procument_cat_id'];
            $data['procument_sub_cat_id'] = $input['procument_sub_cat_id'];
            // $data['evaluation_type_id'] = $input['evaluation_type_id'];
            $data['estimated_value'] = $input['estimated_value'];
            $data['allocated_budget'] = $input['allocated_budget'];
            $data['tender_document_fee'] = $input['tender_document_fee'];
            $data['bank_id'] = $bankId;
            $data['bank_account_id'] = $input['bank_account_id'];
            $data['document_sales_start_date'] = $document_sales_start_date;
            $data['document_sales_end_date'] = $document_sales_end_date;
            $data['pre_bid_clarification_start_date'] = $pre_bid_clarification_start_date;
            $data['pre_bid_clarification_end_date'] = $pre_bid_clarification_end_date;
            $data['pre_bid_clarification_method'] = $input['pre_bid_clarification_method'];
            $data['site_visit_date'] = $site_visit_date;
            $data['site_visit_end_date'] = $site_visit_end_date;
            $data['bid_submission_opening_date'] = $bid_submission_opening_date;
            $data['bid_submission_closing_date'] = $bid_submission_closing_date;
            $data['bid_opening_date'] = $bid_opening_date;
            $data['bid_opening_end_date'] = $bid_opeing_end_date;
            $data['technical_bid_opening_date'] = ($technical_bid_opening_date) ? $technical_bid_opening_date : null;
            $data['technical_bid_closing_date'] = ($technical_bid_closing_date) ? $technical_bid_closing_date : null;
            $data['commerical_bid_opening_date'] = ($commerical_bid_opening_date) ? $commerical_bid_opening_date : null;
            $data['commerical_bid_closing_date'] = ($commerical_bid_closing_date) ? $commerical_bid_closing_date : null;
            $data['updated_by'] = $employee->employeeSystemID;
            $data['show_technical_criteria'] = $input['show_technical_criteria'];

            $result = $this->tenderMasterRepository->updateTenderMaster($data, $input['id'], $editOrAmend, $versionID);
            if(!$result['success']){
                return $this->sendError($result['message'], 500);
            }

            if ($result) {

                $procurementActivity = $input['procument_activity'] ?? null;
                $addProcurementData = $this->tenderMasterRepository->updateProcurementActivity(
                    $procurementActivity, $input['id'], $input['company_id'], $employee, $editOrAmend, $versionID
                );
                if(!$addProcurementData['success']){
                    return $this->sendError($addProcurementData['message'], 500);
                }

                if ($exist['site_visit_date'] != $site_visit_date) {
                    $tenderSiteVisitDateUpdate = $this->tenderMasterRepository->addTenderSiteVisitDate(
                        $input['id'], $input['company_id'], $site_visit_date, $employee, $versionID, $editOrAmend
                    );
                    if(!$tenderSiteVisitDateUpdate['success']){
                        return $this->sendError($tenderSiteVisitDateUpdate['message'], 500);
                    }
                }

                if (isset($input['Attachment']) && !empty($input['Attachment'])) {
                    $attachment = $input['Attachment'];

                    if (!empty($attachment) && isset($attachment['file'])) {
                        $extension = $attachment['fileType'];
                        $allowExtensions = ['pdf', 'txt', 'xlsx', 'docx'];

                        if (!in_array($extension, $allowExtensions)) {
                            return $this->sendError('This type of file not allow to upload.', 500);
                        }

                        if (isset($attachment['size'])) {
                            if ($attachment['size'] > 2097152) {
                                return $this->sendError("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.", 500);
                            }
                        }

                        $file = $attachment['file'];
                        $decodeFile = base64_decode($file);

                        $attch = time() . '_TenderBudgetDocument.' . $extension;

                        $path = $input['company_id'] . '/TenderBudgetDocument/' . $attch;

                        Storage::disk(Helper::policyWiseDisk($input['company_id'], 'public'))->put($path, $decodeFile);

                        $att['budget_document'] = $path;
                        $tenderUpdated = $this->tenderMasterRepository->getTenderExistData($input['id'], $editOrAmend, $versionID);
                        $tenderUpdated->update($att);
                    }
                }
                if (isset($input['confirmed_yn'])) {
                    if ($input['confirmed_yn'] == 1) {

                        if (is_null($input['tender_type_id']) || $input['tender_type_id'] == 0) {
                            return ['success' => false, 'message' => 'Selection is required'];
                        }
                        if (is_null($input['envelop_type_id']) || $input['envelop_type_id'] == 0) {
                            return ['success' => false, 'message' => 'Envelop is required'];
                        }
                        if (is_null($input['evaluation_type_id']) || $input['evaluation_type_id'] == 0) {
                            return ['success' => false, 'message' => 'Evaluation is required'];
                        }
                        if (is_null($input['stage']) || $input['stage'] == 0) {
                            return ['success' => false, 'message' => 'Stage is required'];
                        }

                        if (isset($input['requestType']) && $input['requestType'] == 'Amend') {

                            $circulars = $this->tenderMasterRepository->checkTenderCircularValidation($input['id'], $versionID);
                            if(!$circulars['success']){
                                return ['success' => false, 'message' => $circulars['message']];
                            }

                        }
                        if(!$rfq){
                            $technicalExists = $this->tenderMasterRepository->checkTenderTechnicalCriteriaAdded($input['id'], $versionID, $editOrAmend);
                            if(!$technicalExists['success']){
                                return $technicalExists;
                            }
                        }

                        if($input['tender_type_id'] == 2 || $input['tender_type_id'] == 3){
                            $assignSupplierExists = $this->tenderMasterRepository->checkAssignSuppliers(
                                $input['company_id'], $input['id'], $rfq, true, $editOrAmend, $versionID
                            );
                            if (!$assignSupplierExists['success']) {
                                return $assignSupplierExists;
                            }
                        }

                        if($input['tender_type_id'] == 3){
                            $assignSupplier =  $this->tenderMasterRepository->checkAssignSuppliers(
                                $input['company_id'], $input['id'], $rfq, false, $editOrAmend, $versionID
                            );

                            if(!$assignSupplier['success']){
                                return $assignSupplier;
                            }
                        }

                        $criteriaValidation = $this->tenderMasterRepository->checkEvaluationCriteriaValid(
                            $input['id'], $versionID, $editOrAmend, $input['is_active_go_no_go']
                        );
                        if(!$criteriaValidation['success']){
                            return ['success' => false, 'message' => $criteriaValidation['message']];
                        }

                        $pricingSchedule = $this->tenderMasterRepository->checkPricingScheduleMasterValidation(
                            $input['id'], $editOrAmend, $versionID
                        );
                        if(!$pricingSchedule['success']){
                            return ['success' => false, 'message' => $pricingSchedule['message']];
                        }

                        if ($requestData['enableRequestChange']) {
                            $version = null;
                            $is_vsersion_exit = DocumentModifyRequest::where('documentSystemCode', $input['id'])->latest('id')->first();

                            $company = Company::where('companySystemID', $input['company_id'])->select('companySystemID', 'CompanyID')->first();
                            $documentMaster = DocumentMaster::where('documentSystemID', 118)->select('documentSystemID', 'documentID')->first();
                            $lastSerial = DocumentModifyRequest::where('companySystemID', $input['company_id'])
                                ->orderBy('id', 'desc')
                                ->select('id', 'serial_number')
                                ->first();
                            $lastSerialNumber = 1;
                            if ($lastSerial) {
                                $lastSerialNumber = intval($lastSerial->serial_number) + 1;
                            }

                            $code = ($company->CompanyID . '/' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));


                            $modifyData['companySystemID'] = $input['company_id'];
                            $modifyData['documentSystemCode'] = $input['id'];
                            $modifyData['version'] = $is_vsersion_exit->version;
                            $modifyData['document_master_id'] = 118;
                            $modifyData['requested_document_master_id'] = $documentSystemID;
                            $modifyData['type'] = $is_vsersion_exit->type;
                            $modifyData['status'] = 1;
                            $modifyData['serial_number'] = $lastSerialNumber;
                            $modifyData['requested_employeeSystemID'] = $is_vsersion_exit->requested_employeeSystemID;
                            $modifyData['requested_date'] = $is_vsersion_exit->requested_date;
                            $modifyData['RollLevForApp_curr'] = $is_vsersion_exit->RollLevForApp_curr;
                            $modifyData['approved'] = $is_vsersion_exit->approved;
                            $modifyData['requested'] = $is_vsersion_exit->requested;
                            $modifyData['approved_date'] = $is_vsersion_exit->approved_date;
                            $modifyData['approved_by_user_system_id'] = $is_vsersion_exit->approved_by_user_system_id;
                            $modifyData['requested_by_name'] = $is_vsersion_exit->requested_by_name;
                            $modifyData['description'] = $is_vsersion_exit->description;
                            $modifyData['code'] = $code;
                            $modifyData['serial_number'] = $lastSerialNumber;
                            $modifyData['confirmation_date'] = now();
                            $modifyData['modify_type'] = 2;
                            $documentModifyRequest = $this->documentModifyRequestRepository->create($modifyData);

                            $versionUpdate['tender_edit_confirm_id'] = $documentModifyRequest['id'];
                            TenderMaster::where('id', $input['id'])->update($versionUpdate);

                            $params = array('autoID' => $documentModifyRequest['id'], 'company' => $input["company_id"], 'document' => 118, 'reference_document_id' => $documentSystemID, 'tender_title' => $input['title'], 'tender_description' => $input['description'], 'document_type' => $tenderMaster->document_type, 'amount' => $tenderMaster->estimated_value, 'tenderTypeId' => $tenderMaster->tender_type_id);
                        } else {
                            $params = array('autoID' => $input['id'], 'company' => $input["company_id"], 'document' => $input["document_system_id"], 'tender_title' => $input['title'], 'tender_description' => $input['description'], 'document_type' => $tenderMaster->document_type, 'amount' => $tenderMaster->estimated_value , 'tenderTypeId' => $tenderMaster->tender_type_id);
                        }


                        $confirm = Helper::confirmDocument($params);
                        if (!$confirm["success"]) {
                            return ['success' => false, 'message' => $confirm["message"]];
                        } else {
                            $tenderMaster->confirmed_yn = 1;
                            $tenderMaster->confirmed_date = now();
                            $tenderMaster->confirmed_by_emp_system_id = $employee->employeeSystemID;
                            $tenderMaster->save();
                        }

                        $getTechCriteria = $this->tenderMasterRepository->getEvaluationCriteriaForTenderConfirm(
                            $input['id'], $editOrAmend, $versionID
                        );
                        if(!$getTechCriteria['success']){
                            return $this->sendError($getTechCriteria['message']);
                        }
                        $techCriteria = $getTechCriteria['data'];

                        if (!empty($techCriteria)) {
                            foreach ($techCriteria as $level1) {
                                if (count($level1['child']) > 0) {
                                    $weightage2 = 0;
                                    foreach ($level1['child'] as $level2) {
                                        $weightage2 += $level2['weightage'];
                                        if (count($level2['child']) > 0) {
                                            $weightage3 = 0;
                                            foreach ($level2['child'] as $level3) {
                                                $weightage3 += $level3['weightage'];
                                                if (count($level3['child']) > 0) {
                                                    $weightage4 = 0;
                                                    foreach ($level3['child'] as $level4) {
                                                        $weightage4 += $level4['weightage'];
                                                    }
                                                    if ($level3['weightage'] != $weightage4) {
                                                        return ['success' => false, 'message' => 'Total child criteria weightage of ' . $level3['description'] . ' is not equal to the parent criteria weightage'];
                                                    }
                                                }
                                            }
                                            if ($level2['weightage'] != $weightage3) {
                                                return ['success' => false, 'message' => 'Total child criteria weightage of ' . $level2['description'] . ' is not equal to the parent criteria weightage'];
                                            }
                                        }
                                    }
                                    if ($level1['weightage'] != $weightage2) {
                                        return ['success' => false, 'message' => 'Total child criteria weightage of ' . $level1['description'] . ' is not equal to the parent criteria weightage'];
                                    }
                                }
                            }
                        }
                    }
                }

                $tenderPurchaseRequest = $input['purchaseRequest'] ?? [];
                $tenderPurchaseRequestUpdate = $this->tenderMasterRepository->tenderPurchaseRequestUpdate(
                    $tenderPurchaseRequest, $input['id'], $input['company_id'], $editOrAmend, $versionID
                );
                if(!$tenderPurchaseRequestUpdate['success']){
                    return $this->sendError($tenderPurchaseRequestUpdate['message'], 500);
                }

                $budgetItemList = $input['srmBudgetItem'] ?? [];
                $updateTenderBudget = $this->tenderMasterRepository->updateTenderBudgetItems(
                    $budgetItemList, $input['id'], $input['company_id'], $editOrAmend, $versionID
                );
                if(!$updateTenderBudget['success']){
                    return $this->sendError($updateTenderBudget['message'], 500);
                }

                $departmentMaster = $input['departmentMaster'] ?? [];
                $createDepartment = $this->tenderMasterRepository->updateTenderDepartments(
                    $departmentMaster, $input['company_id'], $input['id'], $editOrAmend, $versionID
                );
                if(!$createDepartment['success']){
                    return $this->sendError($createDepartment['message'], 500);
                }

                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated', 'data' => $input['addCalendarDates']];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    private function validateDates($id, $rfq, $editOrAmend, $versionID){
        $currentDate = Carbon::now();
        $calendarDates = $this->tenderMasterRepository->getCalendarDateDetailsData($id, $editOrAmend, $versionID);

        foreach ($calendarDates as $calDate) {
            $fromTime = ($calDate['from_time']) ? new Carbon($calDate['from_time']) : null;
            $toTime = ($calDate['to_time']) ? new Carbon($calDate['to_time']) : null;

            if (empty($fromTime)) {
                return ['success' => false, 'message' => 'From time cannot be empty'];
            }

            if (empty($toTime)) {
                if (!empty($calDate['to_date']) && $rfq) {
                    return ['success' => false, 'message' => 'To time cannot be empty'];
                } elseif (!$rfq) {
                    return ['success' => false, 'message' => 'To time cannot be empty'];
                }
            }

            if (!empty($calDate['from_date'])) {
                $frm_date = new Carbon($calDate['from_date']);
                $frm_date = ($calDate['from_time']) ? $frm_date->format('Y-m-d') . ' ' . $fromTime->format('H:i:s') : $frm_date->format('Y-m-d');
            } else {
                $frm_date = null;
            }
            if (!empty($calDate['to_date'])) {
                $to_date = new Carbon($calDate['to_date']);
                $to_date = ($calDate['to_time']) ? $to_date->format('Y-m-d') . ' ' . $toTime->format('H:i:s') : $to_date->format('Y-m-d');
            } else {
                $to_date = null;
            }
            if (!empty($to_date) && empty($frm_date)) {
                return ['success' => false, 'message' => 'From date cannot be empty'];
            }

            if (!empty($frm_date) && empty($to_date)) {
                if (!empty($toTime) && $rfq) {
                    return ['success' => false, 'message' => 'To date cannot be empty'];
                } elseif (!$rfq) {
                    return ['success' => false, 'message' => 'To date cannot be empty'];
                }
            }

            if (!empty($frm_date) && !empty($to_date)) {
                if ($frm_date > $to_date) {
                    return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time'];
                }
            }

            if (!empty($frm_date)) {
                if ($frm_date < $currentDate) {
                    return ['success' => false, 'message' => 'From date and time should greater than current date and time'];
                }
            }

            if (!empty($to_date)) {
                if ($to_date < $currentDate) {
                    return ['success' => false, 'message' => 'To date and time should greater than current date and time'];
                }
            }

            if (!empty($to_date) || !empty($frm_date)) {
                if (($frm_date > $to_date) && !empty($to_date) && $rfq) {
                    return ['success' => false, 'message' => 'From date and time should greater than to date and time'];
                } elseif ($frm_date > $to_date && !$rfq) {
                    return ['success' => false, 'message' => 'From date and time should greater than to date and time'];
                }
            }
        }
    }

    public function updateCalenderDates($input)
    {
        $rfq = false;
        if (isset($input['rfq'])) {
            $rfq = ($input['rfq'] == true) ? true : false;
        }

        $requestData = $this->documentModifyService->checkForEditOrAmendRequest($input['id']);
        $editOrAmend = $requestData['enableRequestChange'] ?? false;
        $versionID = $requestData['versionID'] ?? 0;
        $amd_id = $requestData['tenderMasterHistory']['amd_id'] ?? 0;

        if($editOrAmend && empty($requestData['tenderMasterHistory'])){
            return ['success' => false, 'message' => 'Tender history data not found'];
        }

        $currenctDate = Carbon::now();
        if (isset($input['calendarDates'])) {
            if (count($input['calendarDates']) > 0) {
                $lastCalDate = end($input['calendarDates']);
                foreach ($input['calendarDates'] as $calDate) {
                    if ($calDate === $lastCalDate) {
                        $fromTime = ($calDate['from_time']) ? new Carbon($calDate['from_time']) : null;
                        $toTime = ($calDate['to_time']) ? new Carbon($calDate['to_time']) : null;

                        if (empty($fromTime)) {
                            return ['success' => false, 'message' => 'From time cannot be empty'];
                        }

                        if (empty($toTime)) {
                            if (!empty($calDate['to_date']) && $rfq) {
                                return ['success' => false, 'message' => 'To time cannot be empty'];
                            } elseif (!$rfq) {
                                return ['success' => false, 'message' => 'To time cannot be empty'];
                            }
                        }

                        if (!empty($calDate['from_date'])) {
                            $frm_date = new Carbon($calDate['from_date']);
                            $frm_date = ($calDate['from_time']) ? $frm_date->format('Y-m-d') . ' ' . $fromTime->format('H:i:s') : $frm_date->format('Y-m-d');
                        } else {
                            $frm_date = null;
                        }
                        if (!empty($calDate['to_date'])) {
                            $to_date = new Carbon($calDate['to_date']);
                            $to_date = ($calDate['to_time']) ? $to_date->format('Y-m-d') . ' ' . $toTime->format('H:i:s') : $to_date->format('Y-m-d');
                        } else {
                            $to_date = null;
                        }
                        if (!empty($to_date) && empty($frm_date)) {
                            return ['success' => false, 'message' => 'From date cannot be empty'];
                        }

                        if (!empty($frm_date) && empty($to_date)) {
                            if (!empty($toTime) && $rfq) {
                                return ['success' => false, 'message' => 'To date cannot be empty'];
                            } elseif (!$rfq) {
                                return ['success' => false, 'message' => 'To date cannot be empty'];
                            }
                        }

                        if (!empty($frm_date) && !empty($to_date)) {
                            if ($frm_date > $to_date) {
                                return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time'];
                            }
                        }

                        if (!empty($frm_date)) {
                            if ($frm_date < $currenctDate) {
                                return ['success' => false, 'message' => 'From date and time should greater than current date and time'];
                            }
                        }

                        if (!empty($to_date)) {
                            if ($to_date < $currenctDate) {
                                return ['success' => false, 'message' => 'To date and time should greater than current date and time'];
                            }
                        }


                        if (!empty($to_date) || !empty($frm_date)) {
                            if (($frm_date > $to_date) && !empty($to_date) && $rfq) {
                                return ['success' => false, 'message' => 'From date and time should greater than to date and time'];
                            } elseif ($frm_date > $to_date && !$rfq) {
                                return ['success' => false, 'message' => 'From date and time should greater than to date and time'];
                            }
                        }
                    }
                }


                $calenderDetails = $this->tenderMasterRepository->deleteCalenderDetails($input['id'], $input['company_id'], $requestData);
                if (!$calenderDetails['success']) {
                    return $this->sendError($calenderDetails['message'], 500);
                }

                foreach ($input['calendarDates'] as $calDate) {

                    $calenderDateDetails = CalendarDates::find($calDate['id']);

                    if (isset($input['document_sales_start_date'])) {
                        $document_sales_start_time = ($input['document_sales_start_time']) ? new Carbon($input['document_sales_start_time']) : null;
                        $document_sales_start_date = new Carbon($input['document_sales_start_date']);
                        $document_sales_start_date = ($input['document_sales_start_time']) ? $document_sales_start_date->format('Y-m-d') . ' ' . $document_sales_start_time->format('H:i:s') : $document_sales_start_date->format('Y-m-d');
                    }


                    if (isset($input['bid_submission_closing_time'])) {
                        $bid_submission_closing_time =  ($input['bid_submission_closing_time']) ? new Carbon($input['bid_submission_closing_time']) : null;
                        $bid_submission_closing_date = new Carbon($input['bid_submission_closing_date']);
                        $bid_submission_closing_date = ($input['bid_submission_closing_time']) ? $bid_submission_closing_date->format('Y-m-d') . ' ' . $bid_submission_closing_time->format('H:i:s') : $bid_submission_closing_date->format('Y-m-d');
                    }


                    $fromTime = new Carbon($calDate['from_time']);
                    $frm_date = new Carbon($calDate['from_date']);
                    $frm_date = ($calDate['from_time']) ? $frm_date->format('Y-m-d') . ' ' . $fromTime->format('H:i:s') : $frm_date->format('Y-m-d');
                    $to_date = null;
                    if (!empty($calDate['to_date'])) {
                        $toTime = new Carbon($calDate['to_time']);
                        $to_date = new Carbon($calDate['to_date']);
                        $to_date = ($calDate['to_time']) ? $to_date->format('Y-m-d') . ' ' . $toTime->format('H:i:s') : $to_date->format('Y-m-d');
                    }

                    $calDt['tender_id'] = $input['id'];
                    $calDt['calendar_date_id'] = $calDate['id'];
                    $calDt['from_date'] = $frm_date;
                    $calDt['to_date'] = $to_date;
                    $calDt['company_id'] = $input['company_id'];
                    $calDt['created_at'] = Carbon::now();
                    if($editOrAmend){
                        $calDt['id'] = null;
                        $calDt['level_no'] = 1;
                        $calDt['version_id'] = $versionID;
                        CalendarDatesDetailEditLog::create($calDt);
                    } else {
                        CalendarDatesDetail::create($calDt);
                    }

                    $tenderMaster = $editOrAmend ? SrmTenderMasterEditLog::find($amd_id) : TenderMaster::find($input['id']);
                    if($calenderDateDetails->is_default == 1){
                        $tenderMaster->pre_bid_clarification_start_date = $frm_date;
                        $tenderMaster->pre_bid_clarification_end_date = $to_date;
                        $tenderMaster->save();
                    }

                    if($calenderDateDetails->is_default == 2){
                        $tenderMaster->site_visit_date = $frm_date;
                        $tenderMaster->site_visit_end_date = $to_date;
                        $tenderMaster->save();
                    }

                }
                return ['success' => true, 'message' => 'Successfully updated', 'data' => $input['addCalendarDates']];
            } else {
                return $this->tenderMasterRepository->deleteCalenderDetails($input['id'], $input['company_id'], $requestData);
            }
        } else {
            return $this->tenderMasterRepository->deleteCalenderDetails($input['id'], $input['company_id'], $requestData);
        }
    }

    public function validateTenderHeader($input)
    {


        $messages = [
            'title.required' => 'Title is required.',
            'currency_id.required' => 'Currency is required.',
            'pre_bid_clarification_method.required' => 'Pre-bid Clarifications Method.',
            'bid_submission_opening_date.required' => 'Bid Submission From Date.',
            'tender_type_id.required' => 'Type is required.',
            'evaluation_type_id.required' => 'Evaluation Type is required.',
            'stage.required' => 'Stage is required.',
            'no_of_alternative_solutions.required' => 'Number of Alternative solutions is required.',
            'commercial_weightage.required' => 'Commercial Criteria Weightage is required.',
            'technical_weightage.required' => 'Technical Criteria Weightage is required.',
        ];

        $validator = \Validator::make($input, [
            'title' => 'required',
            'currency_id' => 'required',
            'bid_submission_opening_date' => 'required',
            'tender_type_id' => 'required',
            'evaluation_type_id' => 'required',
            'stage' => 'required',
            'no_of_alternative_solutions' => 'required',
            'commercial_weightage' => 'required',
            'technical_weightage' => 'required',

        ], $messages);

        if (!isset($input['rfq'])) {
            $messages = [
                'estimated_value.required' => 'Estimated Value is required.',
                'bank_id.required' => 'Bank is required.',
                'bank_account_id.required' => 'Bank Account is required.',
                'document_sales_start_date.required' => 'Document Sales From Date is required.',
                'document_sales_end_date.required' => 'Document Sales To Date is required.',
                //'pre_bid_clarification_start_date.required' => 'Pre-bid Clarification From Date.',
                //'pre_bid_clarification_end_date.required' => 'Pre-bid Clarification To Date.',
                'bid_submission_closing_date.required' => 'Bid Submission To Date.',
                // 'site_visit_date.required' => 'Site Visit From Date.',
                // 'site_visit_end_date.required' => 'Site Visit To Date.',
                'envelop_type_id.required' => 'Envelop Type is required.',
                'stage.required' => 'Stage is required.',
            ];
            $validator = \Validator::make($input, [
                'estimated_value' => 'required',
                'bank_id' =>  'required_with:tender_document_fee',
                'bank_account_id' => 'required_with:tender_document_fee',
                'document_sales_start_date' => 'required',
                'document_sales_end_date' => 'required',
                //'pre_bid_clarification_start_date' => 'required',
                //'pre_bid_clarification_end_date' => 'required',
                'pre_bid_clarification_method' => 'required',
                'bid_submission_closing_date' => 'required',
                //'site_visit_date' => 'required',
                //'site_visit_end_date' => 'required',
                'envelop_type_id' => 'required',
                'evaluation_type_id' => 'required',
            ], $messages);
        }

        if ($validator->fails()) {
            return ['status' => false, 'message' => $validator->messages()];
        }

        if ($input['evaluation_type_id'] == 0) {
            return ['status' => false, 'message' => 'Evaluation Type is required.'];
        }

        return ['status' => true, 'message' => "success"];
    }

    public function getFaqFormData(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companySystemID'];
        $data['tenders'] = TenderMaster::where('company_id', $companyId)
            ->where('published_yn', 1)
            ->where('company_id', $companyId)
            ->where('pre_bid_clarification_method', '!=', 0)
            ->where('closed_yn', '!=', 1)
            ->get();
        return $data;
    }

    public function getTenderMasterApproval(Request $request)
    {
        $input = $request->all();
        $rfx = false;

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();
        if (isset($input['rfx'])) {
            $rfx = $input['rfx'];
        }

        $poMasters = DB::table('erp_documentapproved')->select(
            'srm_tender_master.id',
            'srm_tender_master.tender_code',
            'srm_tender_master.document_system_id',
            'srm_tender_master.title',
            'srm_tender_master.description',
            'srm_tender_master.estimated_value',
            'srm_tender_master.bid_submission_opening_date',
            'srm_tender_master.bid_submission_closing_date',
            'srm_tender_master.created_at',
            'srm_tender_master.confirmed_date',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.CurrencyCode',
            'erp_documentapproved.approvalLevelID',
            'erp_documentapproved.documentSystemCode',
            'employees.empName As created_user'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $rfx) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                /*->on('erp_documentapproved.departmentSystemID', '=', 'employeesdepartments.departmentSystemID')*/
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            if ($rfx) {
                $query->where('employeesdepartments.documentSystemID', 113);
            } else {
                $query->where('employeesdepartments.documentSystemID', 108);
            }
            $query->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('srm_tender_master', function ($query) use ($companyID, $empID, $rfx) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('srm_tender_master.company_id', $companyID)
                ->where('srm_tender_master.approved', 0)
                ->where('srm_tender_master.confirmed_yn', 1);
            if ($rfx) {
                $query->where('srm_tender_master.document_type', '!=', 0);
            }
        });

        if ($rfx) {
            $poMasters = $poMasters->where('erp_documentapproved.approvedYN', 0)
                ->join('currencymaster', 'currency_id', '=', 'currencyID')
                ->join('employees', 'created_by', 'employees.employeeSystemID')
                ->where('erp_documentapproved.rejectedYN', 0)
                ->where('erp_documentapproved.documentSystemID', 113)
                ->where('erp_documentapproved.companySystemID', $companyID);
        } else {
            $poMasters = $poMasters->where('erp_documentapproved.approvedYN', 0)
                ->join('currencymaster', 'currency_id', '=', 'currencyID')
                ->join('employees', 'created_by', 'employees.employeeSystemID')
                ->where('erp_documentapproved.rejectedYN', 0)
                ->where('erp_documentapproved.documentSystemID', 108)
                ->where('erp_documentapproved.companySystemID', $companyID);
        }


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $poMasters = $poMasters->where(function ($query) use ($search) {
                $query->where('tender_code', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $purchaseRequests = [];
        }

        return \DataTables::of($poMasters)
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
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    public function approveTender(Request $request)
    {

        $approve = \Helper::approveDocument($request);

        if (!$approve["success"]) {

            return $this->sendError($approve["message"]);
        } else {

            return $this->sendResponse(array(), $approve["message"]);
        }
    }

    public function rejectTender(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            if($request['documentSystemID'] == 118){
                $deleteLogRecords = $this->srmTenderEditAmendService->rejectDocumentRequestChanges($request['id']);
                if(!$deleteLogRecords['success']){
                    return $this->sendError($deleteLogRecords["message"]);
                }
            }
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    public function getTenderMasterFullApproved(Request $request)
    {
        $input = $request->all();
        $rfx = false;
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();
        if (isset($input['rfx'])) {
            $rfx = $input['rfx'];
        }


        $poMasters = DB::table('erp_documentapproved')->select(
            'srm_tender_master.id',
            'srm_tender_master.tender_code',
            'srm_tender_master.document_system_id',
            'srm_tender_master.title',
            'srm_tender_master.description',
            'srm_tender_master.estimated_value',
            'srm_tender_master.bid_submission_opening_date',
            'srm_tender_master.bid_submission_closing_date',
            'srm_tender_master.created_at',
            'srm_tender_master.confirmed_date',
            'erp_documentapproved.approvedComments',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.CurrencyCode',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $rfx) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                /*->on('erp_documentapproved.departmentSystemID', '=', 'employeesdepartments.departmentSystemID')*/
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

            if ($rfx) {
                $query->where('employeesdepartments.documentSystemID', 113);
            } else {
                $query->where('employeesdepartments.documentSystemID', 108);
            }

            $query->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('srm_tender_master', function ($query) use ($companyID, $empID, $rfx) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('srm_tender_master.company_id', $companyID)
                ->where('srm_tender_master.approved', -1)
                ->where('srm_tender_master.confirmed_yn', 1);
            if ($rfx) {
                $query->where('srm_tender_master.document_type', '!=', 0);
            }
        });
        if ($rfx) {
            $poMasters = $poMasters->where('erp_documentapproved.approvedYN', -1)
                ->join('currencymaster', 'currency_id', '=', 'currencyID')
                ->join('employees', 'created_by', 'employees.employeeSystemID')
                ->where('erp_documentapproved.documentSystemID', 113)
                ->where('erp_documentapproved.companySystemID', $companyID);
        } else {
            $poMasters = $poMasters->where('erp_documentapproved.approvedYN', -1)
                ->join('currencymaster', 'currency_id', '=', 'currencyID')
                ->join('employees', 'created_by', 'employees.employeeSystemID')
                ->where('erp_documentapproved.documentSystemID', 108)
                ->where('erp_documentapproved.companySystemID', $companyID);
        }


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $poMasters = $poMasters->where(function ($query) use ($search) {
                $query->where('tender_code', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $purchaseRequests = [];
        }

        return \DataTables::of($poMasters)
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
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    public function reOpenTender(Request $request)
    {
        $input = $request->all();

        $tenderMasterId = $input['tenderMasterId'];

        $tenderMaster = TenderMaster::find($tenderMasterId);
        $emails = array();
        if (empty($tenderMaster)) {
            return $this->sendError('Tender not found');
        }

        if ($tenderMaster->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this Tender it is already partially approved');
        }

        if ($tenderMaster->approved == -1) {
            return $this->sendError('You cannot reopen this Tender it is already fully approved');
        }

        if ($tenderMaster->confirmed_yn == 0) {
            return $this->sendError('You cannot reopen this Tender, it is not confirmed');
        }

        // updating fields

        $tenderMaster->confirmed_yn = 0;
        $tenderMaster->confirmed_by_emp_system_id = null;
        $tenderMaster->confirmed_by_name = null;
        $tenderMaster->confirmed_date = null;
        $tenderMaster->RollLevForApp_curr = 1;
        $tenderMaster->save();

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $tenderMaster->document_system_id)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $tenderMaster->tender_code . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $tenderMaster->tender_code;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $tenderMaster->company_id)
            ->where('documentSystemCode', $tenderMaster->id)
            ->where('documentSystemID', $tenderMaster->document_system_id)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $tenderMaster->company_id)
                    ->where('documentSystemID', $tenderMaster->document_system_id)
                    ->first();

                if (empty($companyDocument)) {
                    return ['success' => false, 'message' => 'Policy not found for this document'];
                }

                $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                    ->where('companySystemID', $documentApproval->companySystemID)
                    ->where('documentSystemID', $documentApproval->documentSystemID);

                if ($companyDocument['isServiceLineApproval'] == -1) {
                    $approvalList = $approvalList->where('ServiceLineSystemID', $documentApproval->serviceLineSystemID);
                }

                $approvalList = $approvalList
                    ->with(['employee'])
                    ->groupBy('employeeSystemID')
                    ->get();

                foreach ($approvalList as $da) {
                    if ($da->employee) {
                        $emails[] = array(
                            'empSystemID' => $da->employee->employeeSystemID,
                            'companySystemID' => $documentApproval->companySystemID,
                            'docSystemID' => $documentApproval->documentSystemID,
                            'alertMessage' => $subject,
                            'emailAlertMessage' => $body,
                            'docSystemCode' => $documentApproval->documentSystemCode
                        );
                    }
                }

                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    return ['success' => false, 'message' => $sendEmail["message"]];
                }
            }
        }

        DocumentApproved::where('documentSystemCode', $tenderMasterId)
            ->where('companySystemID', $tenderMaster->company_id)
            ->where('documentSystemID', $tenderMaster->document_system_id)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($tenderMaster->document_system_id, $tenderMasterId, $input['reopenComments'], 'Reopened');

        return $this->sendResponse($tenderMaster->toArray(), 'Tender reopened successfully');
    }

    public function tenderMasterPublish(Request $request)
    {
        $input = $request->all();
        $tenderTitle = $input['title'];
        $tenderDescription = $input['description'];
        $companyId = $input['company_id'];
        $tenderType = $input['tender_type_id'];
        $documentType = $input['document_type'];

        $apiKey = $request->input('api_key');
        $loginUrl = env('SRM_LINK');
        $urlArray = explode('/', $loginUrl);
        $urlArray = array_filter($urlArray);
        array_pop($urlArray);

        $urlString = implode('//', $urlArray) . '/';

        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $att['updated_by'] = $employee->employeeSystemID;
            $att['published_yn'] = 1;
            $att['published_at'] = Carbon::now();
            $result = TenderMaster::where('id', $input['id'])->update($att);

            if ($result) {
                DB::commit();
                if ($tenderType == 1 && $documentType == 0) {
                    $this->openTenderSupplierEmailInvitation($tenderTitle, $tenderDescription, $companyId, $urlString);
                }
                return ['success' => true, 'message' => 'Successfully Published'];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function openTenderSupplierEmailInvitation($tenderTitle, $tenderDescription, $companyId, $urlString){

        $getFullyApprovedSupplierList = SupplierRegistrationLink::join('supplierassigned', 'supplierCodeSytem', '=', 'supplier_master_id')
            ->whereNotNull('supplier_master_id')
            ->where('supplierassigned.companySystemID', $companyId)
            ->where('supplierassigned.isActive', 1)
            ->select('email')
            ->distinct()
            ->get();

        $fromName = \Helper::getEmailConfiguration('mail_name','GEARS');

        $file = array();

        foreach ($getFullyApprovedSupplierList as $SupplierList){

            $docType = 'Tender';
            $emailFormatted = email::emailAddressFormat($SupplierList['email']);

            $dataEmail['companySystemID'] = $companyId;
            $dataEmail['alertMessage'] = "Invitation for ".$docType." ";
            $dataEmail['empEmail'] = $emailFormatted;
            $body = "Dear Supplier," . "<br /><br />" . "
            We trust this message finds you well." . "<br /><br />" . "
            We are in the process of inviting reputable suppliers to participate in a ".$docType." for an upcoming project. Your company's outstanding reputation and capabilities have led us to extend this invitation to you." . "<br /><br />" . "
            If your company is interested in participating in the ".$docType." process, please click on the link below." . "<br /><br />" . "
            " . "<b>" . " ".$docType." Title :" . "</b> " . $tenderTitle . "<br /><br />" . "
            " . "<b>" . " ".$docType." Description :" . "</b> " . $tenderDescription . "<br /><br />" . "
            " . "<b>" . "Link :" . "</b> " . "<a href='" . $urlString . "'>" . $urlString . "</a><br /><br />" . "
            If you have any initial inquiries or require further information, feel free to reach out to us." . "<br /><br />" . "
            Thank you for considering this invitation. We look forward to the possibility of collaborating with your esteemed company." . "<br /><br />";
            $dataEmail['emailAlertMessage'] = $body;
            $sendEmail = \Email::sendEmailErp($dataEmail);
        }
    }

    public function loadTenderSubActivity(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($request->all(), array('procument_cat_id'));
        $tenderActivity = $this->tenderMasterRepository->loadTenderSubActivity($input);

        return $this->sendResponse($tenderActivity, 'Tender sub activity retrieved successfully');
    }
    public function sendSupplierInvitation(Request $request)
    {
        $companyName = "";
        $company = Company::find($request->input('company_id'));
        $email = email::emailAddressFormat($request->input('email'));
        if (isset($company->CompanyName)) {
            $companyName =  $company->CompanyName;
        }
        $token = md5(Carbon::now()->format('YmdHisu'));
        $apiKey = $request->input('api_key');

        $data['domain'] =  Helper::getDomainForSrmDocuments($request);
        $request->merge($data);

        $fromName = \Helper::getEmailConfiguration('mail_name','GEARS');

        $file = array();

        $isCreated = $this->registrationLinkRepository->save($request, $token);
        $loginUrl = env('SRM_LINK') . $token . '/' . $apiKey;
        if ($isCreated['status'] == true) {

            $dataEmail['companySystemID'] = $request->input('company_id');
            $dataEmail['alertMessage'] = "Registration Link";
            $dataEmail['empEmail'] = $email;
            $body = "Dear Supplier," . "<br /><br />" . " Please find the below link to register at " . $companyName . " supplier portal. It will expire in 48 hours. " . "<br /><br />" . "Click Here: " . "</b><a href='" . $loginUrl . "'>" . $loginUrl . "</a><br /><br />" . " Thank You" . "<br /><br /><b>";
            $dataEmail['emailAlertMessage'] = $body;
            $sendEmail = \Email::sendEmailErp($dataEmail);

            return $this->sendResponse($loginUrl, 'Supplier Registration Link Generated successfully');
        } else {
            return $this->sendError('Supplier Registration Link Generation Failed', 500);
        }
    }
    public function getSupplierList(Request $request)
    {
        return $this->tenderMasterRepository->getSupplierList($request);
    }
    public function saveSupplierAssigned(Request $request)
    {
        $input = $request->all();
        try{
            $response = $this->tenderMasterRepository->saveSupplierAssigned($input);
            if(!$response['success']){
                $code = $response['code'] ?? 500;
                return $this->sendError($response['message'], $code);
            } else {
                return $this->sendResponse([], $response['message']);
            }
        } catch (\Exception $exception) {
            return $this->sendError('Unexpected Error: ' . $exception->getMessage());
        }
    }
    public function getSupplierAssignedList(Request $request)
    {
        return $this->tenderMasterRepository->getSupplierAssignedList($request);
    }

    public function getSupplierCategoryList(Request $request)
    {
        try {
            return SupplierCategoryMaster::orderBy('categoryName', 'asc')
                ->get();
        } catch (\Exception $ex) {
            return [];
        }
    }
    public function updateTenderStrategy(Request $request)
    {
        try {
            $input = $this->convertArrayToSelectedValue($request->all(), array('tender_type_id', 'envelop_type_id', 'evaluation_type_id', 'stage'));
            $resValidate = $this->validateTenderStrategy($input);
            if (!$resValidate['status']) {
                return $this->sendError($resValidate['message'], 422);
            }
            $commercialWeightage = $input['commercial_weightage'];
            $technicalWeightage = $input['technical_weightage'];

            $total = ((int)$commercialWeightage + (int)$technicalWeightage);
            $employee = \Helper::getEmployeeInfo();
            if ($total != 100) {
                return ['status' => false, 'message' => 'The total Evaluation Criteria Weightage cannot be less than 100'];
            }

            if (!isset($input['rfx'])) {
                if ($input['commercial_weightage'] != 0 && ($input['commercial_passing_weightage'] == 0 || is_null($input['commercial_passing_weightage']))) {
                    return ['status' => false, 'message' => 'Commercial Passing Weightage is required'];
                }

                if ($input['technical_weightage'] != 0 && ($input['technical_passing_weightage'] == 0 || is_null($input['technical_passing_weightage']))) {
                    return ['status' => false, 'message' => 'Technical Passing Weightage is required'];
                }
            }

            return $this->tenderMasterRepository->updateTenderStrategy($input);

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e];
        }
    }

    public function validateTenderStrategy($input)
    {
        $messages = [
            'tender_type_id.required' => 'Type is required.',
            'evaluation_type_id.required' => 'Evaluation Type is required.',
            'stage.required' => 'Stage is required.',
            'no_of_alternative_solutions.required' => 'Number of Alternative solutions is required.',
            'commercial_weightage.required' => 'Commercial Criteria Weightage is required.',
            'technical_weightage.required' => 'Technical Criteria Weightage is required.'
        ];

        $validator = \Validator::make($input, [
            'tender_type_id' => 'required',
            'evaluation_type_id' => 'required',
            'stage' => 'required',
            'no_of_alternative_solutions' => 'required',
            'commercial_weightage' => 'required',
            'technical_weightage' => 'required'
        ], $messages);

        if (!isset($input['rfx'])) {
            $messages = [
                'envelop_type_id.required' => 'Envelop Type is required.',
                'commercial_passing_weightage.required' => 'Commercial Passing Weightage is required.',
                'technical_passing_weightage.required' => 'Technical Passing Weightage is required.'
            ];

            $validator = \Validator::make($input, [
                'commercial_passing_weightage' => 'required',
                'technical_passing_weightage' => 'required'
            ], $messages);
        }

        if ($validator->fails()) {
            return ['status' => false, 'message' => $validator->messages()];
        }

        if ($input['evaluation_type_id'] == 0) {
            return ['status' => false, 'message' => 'Evaluation Type is required.'];
        }

        return ['status' => true, 'message' => "success"];
    }

    public function removeCalenderDate(Request $request)
    {
        try {
            return $this->tenderMasterRepository->removeCalendarDates($request);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e];
        }
    }

    public function updateCalenderDate(Request $request)
    {
        $currentDate = Carbon::now();
        $employee = \Helper::getEmployeeInfo();

        $rfx = isset($request['rfq']) ? true : false;
        $fromTime = ($request['from_time']) ? new Carbon($request['from_time']) : null;
        $toTime = ($request['to_time']) ? new Carbon($request['to_time']) : null;
        $versionID = $request['versionID'] ?? 0;
        $editOrAmend = $versionID > 0;

        $calendarDateValid = $this->tenderMasterRepository->getCalendarDateData($request, $versionID, $editOrAmend);
        if(!$calendarDateValid['success']){
            return $this->sendError($calendarDateValid['message']);
        }
        $calendarDatesDetail = $calendarDateValid['data'];

        if (!isset($request['from_time'])) {
            $fromTime = new Carbon($calendarDatesDetail->from_time);
        }

        if (!isset($request['to_time'])) {
            $toTime = new Carbon($calendarDatesDetail->to_time);
        }


        if (isset($request['from_date'])) {
            $frm_date = new Carbon($request['from_date']);
            $frm_date = ($fromTime) ? $frm_date->format('Y-m-d') . ' ' . $fromTime->format('H:i:s') : $frm_date->format('Y-m-d');
            $data['from_date'] = $frm_date;
        }


        if (isset($request['to_date']) && !empty($request['to_date'])) {
            $to_date = new Carbon($request['to_date']);
            $to_date = ($toTime) ? $to_date->format('Y-m-d') . ' ' . $toTime->format('H:i:s') : $to_date->format('Y-m-d');
            $data['to_date'] = $to_date;
        } else {
            $to_date = null;
            $data['to_date'] = $to_date;
        }

        if (!empty($to_date) && empty($frm_date)) {
            return ['success' => false, 'message' => 'From date cannot be empty'];
        }
        if (!empty($frm_date) && empty($to_date) && !$rfx) {
            return ['success' => false, 'message' => 'To date cannot be empty'];
        }

        if (!empty($frm_date)) {
            if ($frm_date < $currentDate) {
                return ['success' => false, 'message' => 'From date and time should greater than current date and time'];
            }
        }

        if (!empty($to_date)) {
            if ($to_date < $currentDate) {
                return ['success' => false, 'message' => 'To date and time should greater than current date and time'];
            }
        }

        if (!empty($frm_date) && !empty($to_date)) {
            if ($frm_date > $to_date) {
                return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time'];
            }
        }

        $data['updated_at'] = Carbon::now();
        $data['updated_by'] = $employee->employeeSystemID;


        DB::beginTransaction();
        try {
            if (isset($request['time_changed']) && $request['time_changed']) {
                if ($calendarDatesDetail->from_time != $fromTime || $calendarDatesDetail->to_time != $toTime) {

                    $calenderDates =  $editOrAmend ?
                        CalendarDatesDetailEditLog::find($calendarDatesDetail->amd_id) :
                        CalendarDatesDetail::find($calendarDatesDetail->id);
                    $calenderDates->update($data);

                    $tenderMasterData = $this->tenderMasterRepository->getTenderData($request, $versionID, $editOrAmend);
                    if(!$tenderMasterData['success']){
                        return $this->sendError($tenderMasterData['message']);
                    }
                    $tenderMaster = $tenderMasterData['data'];

                    if($calendarDatesDetail->is_default == 1){
                        $tenderMaster->pre_bid_clarification_start_date = $data['from_date'];
                        $tenderMaster->pre_bid_clarification_end_date = $data['to_date'];
                        $tenderMaster->save();
                    }

                    if($calendarDatesDetail->is_default == 2){
                        $tenderMaster->site_visit_date = $data['from_date'];
                        $tenderMaster->site_visit_end_date = $data['to_date'];
                        $tenderMaster->save();
                    }

                    DB::commit();
                    return ['success' => true, 'message' => 'updated', 'data' => $calendarDatesDetail];
                }
            } else {
                $calenderDates =  $editOrAmend ?
                    CalendarDatesDetailEditLog::find($calendarDatesDetail->amd_id) :
                    CalendarDatesDetail::find($calendarDatesDetail->id);
                $calenderDates->update($data);

                $tenderMasterData = $this->tenderMasterRepository->getTenderData($request, $versionID, $editOrAmend);
                if(!$tenderMasterData['success']){
                    return $this->sendError($tenderMasterData['message']);
                }
                $tenderMaster = $tenderMasterData['data'];

                if($calendarDatesDetail->is_default == 1){
                    $tenderMaster->pre_bid_clarification_start_date = $data['from_date'];
                    $tenderMaster->pre_bid_clarification_end_date = $data['to_date'];
                    $tenderMaster->save();
                }

                if($calendarDatesDetail->is_default == 2){
                    $tenderMaster->site_visit_date = $data['from_date'];
                    $tenderMaster->site_visit_end_date = $data['to_date'];
                    $tenderMaster->save();
                }

                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated', 'data' => $calendarDatesDetail];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function getPurchasedTenderList(Request $request)
    {
        $input = $request->all();
        $userId = \Helper::getEmployeeSystemID();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $filters = $this->getFilterData($input);

        // $tenderMaster = TenderMasterSupplier::with(['tender_master'=>function($q){
        //     $q->with(['tender_type', 'envelop_type', 'currency']);
        // }])->get();

        $query = TenderMaster::with(['currency', 'srm_bid_submission_master', 'tender_type', 'envelop_type', 'srmTenderMasterSupplier',
            'tenderUserAccess'=> function ($q) use ($userId){
                $q->where('user_id',$userId)
                    ->where('module_id',1);
            }, 'tenderBidMinimumApproval'=> function ($q1) use ($userId) {
                $q1->where('emp_id',$userId);
            }])
            ->withCount(['criteriaDetails',
                'criteriaDetails AS go_no_go_count' => function ($query) {
                    $query->where('critera_type_id', 1);
                },
                'criteriaDetails AS technical_count' => function ($query) {
                    $query->where('critera_type_id', 2);
                }
            ])
            ->where(function ($query) use ($userId) {
                $query->whereHas('tenderUserAccess', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->where('module_id',1);
                })
                    ->orWhereHas('tenderBidMinimumApproval', function ($q1) use ($userId) {
                        $q1->where('emp_id', $userId);
                    });
            })
            ->whereHas('srmTenderMasterSupplier')->where('published_yn', 1)->where('company_id', $companyId);


        if ($filters['currencyId'] && count($filters['currencyId']) > 0) {
            $query->whereIn('currency_id', $filters['currencyId']);
        }

        if ($filters['selection']) {
            $query->where('tender_type_id', $filters['selection']);
        }

        if ($filters['envelope']) {
            $query->where('envelop_type_id', $filters['envelope']);
        }

        if ($filters['gonogo']) {
            $gonogo =  ($filters['gonogo'] == 1 ) ? 0 :1;
            $query->where('go_no_go_status', $gonogo);
        }

        if ($filters['technical']) {
            $ids = array_column($filters['technical'], 'id');
            $query->whereIn('technical_eval_status', $ids);
        }

        if ($filters['stage']) {
            $query->where('stage', $filters['stage']);
        }

        // return $this->sendResponse($query, 'Tender Masters retrieved successfully');

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('tender_code', 'LIKE', "%{$search}%");
                $query->orWhere('description', 'LIKE', "%{$search}%");
                $query->orWhere('description_sec_lang', 'LIKE', "%{$search}%");
                $query->orWhere('title', 'LIKE', "%{$search}%");
                $query->orWhere('title_sec_lang', 'LIKE', "%{$search}%");
                $query->orWhereHas('envelop_type', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
                $query->orWhereHas('currency', function ($query1) use ($search) {
                    $query1->where('CurrencyName', 'LIKE', "%{$search}%");
                    $query1->orWhere('CurrencyCode', 'LIKE', "%{$search}%");
                });
            });
        }

        if ($filters['technical']) {
            $query->having('technical_count', '>', 0);
        }

        return \DataTables::eloquent($query)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getPurchaseTenderMasterData(Request $request)
    {
        $input = $request->all();
        $tenderMasterId = $input['tenderMasterId'];
        $companySystemID = $input['companySystemID'];
        $is_date_disable = false;
        $is_comm_date_disable = false;
        $is_emp_approval_active = false;

        $data['master'] = TenderMaster::with(['procument_activity', 'confirmed_by', 'tender_type', 'envelop_type', 'evaluation_type'])
            ->withCount(['criteriaDetails',
                'criteriaDetails AS go_no_go_count' => function ($query) {
                    $query->where('critera_type_id', 1);
                },
                'criteriaDetails AS technical_count' => function ($query) {
                    $query->where('critera_type_id', 2);
                }
            ])->withCount(['DocumentAttachments'=>function($q){
                $q->where('envelopType',3);
            }])->where('id', $input['tenderMasterId'])->first();
        $activity = ProcumentActivity::with(['tender_procurement_category'])->where('tender_id', $input['tenderMasterId'])->where('company_id', $input['companySystemID'])->get();
        $act = array();
        if (!empty($activity)) {
            foreach ($activity as $vl) {
                $dt['id'] = $vl['tender_procurement_category']['id'];
                $dt['itemName'] = $vl['tender_procurement_category']['code'] . ' | ' . $vl['tender_procurement_category']['description'];
                array_push($act, $dt);
            }
        }
        $data['activity'] = $act;

        $qry = "SELECT
            srm_calendar_dates.id as id,
            srm_calendar_dates.calendar_date as calendar_date,
            srm_calendar_dates.company_id as company_id,
            srm_calendar_dates_detail.from_date as from_date,
            srm_calendar_dates_detail.to_date as to_date
        FROM
            srm_calendar_dates 
            INNER JOIN srm_calendar_dates_detail ON srm_calendar_dates_detail.calendar_date_id = srm_calendar_dates.id AND srm_calendar_dates_detail.tender_id = $tenderMasterId
        WHERE
            srm_calendar_dates.company_id = $companySystemID";

        $qryAll = "SELECT
            srm_calendar_dates.id as id,
            srm_calendar_dates.calendar_date as calendar_date,
            srm_calendar_dates.company_id as company_id,
            srm_calendar_dates_detail.from_date as from_date,
            srm_calendar_dates_detail.to_date as to_date
        FROM
            srm_calendar_dates 
            LEFT JOIN srm_calendar_dates_detail ON srm_calendar_dates_detail.calendar_date_id = srm_calendar_dates.id AND srm_calendar_dates_detail.tender_id = $tenderMasterId
        WHERE
            srm_calendar_dates.company_id = $companySystemID
            and ISNULL(srm_calendar_dates_detail.from_date)
            and ISNULL(srm_calendar_dates_detail.to_date)";


        $data['calendarDates'] = DB::select($qry);
        $data['calendarDatesAll'] = DB::select($qryAll);
        $current_date = date('Y-m-d H:i:s');
        $commercialDateCheckResult = false;
        $stage = $data['master']['stage'];
        $current_date2 = Carbon::createFromFormat('Y-m-d H:i:s', $current_date);

        if ($stage == 1) {
            $opening_date_comp = $data['master']['bid_opening_date'];
            $opening_date_comp_end = $data['master']['bid_opening_end_date'];
        } else if ($stage == 2) {
            $opening_date_comp = $data['master']['technical_bid_opening_date'];
            $opening_date_comp_end = $data['master']['technical_bid_closing_date'];

            $opening_commer_date_comp = $data['master']['commerical_bid_opening_date'];
            $closing_commer_date_comp = $data['master']['commerical_bid_closing_date'];

            $commercialDateCheckResult = $current_date2->gt($opening_commer_date_comp);
            if ($closing_commer_date_comp == null) {
                $result2 = true;
            } else {
                $result2 = $closing_commer_date_comp->gt($current_date2);
            }




            if ($commercialDateCheckResult && $result2) {
                $is_comm_date_disable = true;
            }
        }


        $result3 = $current_date2->gt($opening_date_comp);
        if ($opening_date_comp_end == null) {
            $result4 = true;
        } else {
            $result4 = $opening_date_comp_end->gt($current_date2);
        }

        if ($result3 && $result4) {
            $is_date_disable = true;
        }

        if(isset($data['master']) && isset($data['master']['bid_submission_closing_date'])) {
            $bidClosingDate  = Carbon::parse($data['master']['bid_submission_closing_date']);
            $currentDateTime = Carbon::now();
            if ($currentDateTime->gte($bidClosingDate)) {
                $is_emp_approval_active = true;
            }
        }


        $data['master']['disable_date'] = $is_date_disable;
        $data['master']['bid_opening_date_status'] = $result3;
        $data['master']['comm_opening_date_status'] = $commercialDateCheckResult;
        $data['master']['is_comm_date_disable'] = $is_comm_date_disable;
        $data['master']['is_emp_approval_active'] = $is_emp_approval_active;
        $data['master']['tender_bids'] = $this->getTenderBits($request);

        $documentTypes = TenderDocumentTypeAssign::with(['document_type'])->where('tender_id', $tenderMasterId)->get();
        $docTypeArr = array();
        if (!empty($documentTypes)) {
            foreach ($documentTypes as $vl) {
                $dt['id'] = $vl['document_type']['id'];
                $dt['itemName'] = $vl['document_type']['document_type'];
                array_push($docTypeArr, $dt);
            }
        }
        $data['documentTypes'] = $docTypeArr;
        $data['showSupplierPolicy'] = Helper::checkPolicy($companySystemID, 99);
        return $data;
    }

    private function getTenderBits(Request $request)
    {
        $input = $request->all();
        $companyId = $request['companySystemID'];
        $tenderId = $request['tenderMasterId'];
        $isNegotiation = $request['isNegotiation'];

        $request->merge([
            'tenderMasterId' => $tenderId,
            'companySystemID' => $companyId,
        ]);

        $tenderBidNegotiations = TenderBidNegotiation::select('bid_submission_master_id_new')
            ->where('tender_id', $tenderId)
            ->get();

        if ($tenderBidNegotiations->count() > 0) {
            $bidSubmissionMasterIds = $tenderBidNegotiations->pluck('bid_submission_master_id_new')->toArray();
        } else {
            $bidSubmissionMasterIds = [];
        }

        $query = BidSubmissionMaster::where('status', 1)->where('bidSubmittedYN', 1)->where('tender_id', $tenderId);

        if ($isNegotiation == 1) {
            $query = $query->whereIn('id', $bidSubmissionMasterIds);
        } else {
            $query = $query->whereNotIn('id', $bidSubmissionMasterIds);
        }
        return $query->count();
    }

    public function tenderCommiteApproveal(Request $request)
    {

        $input = $request->all();
        $tender_id = $input['tender_id'];
        $emp_id = $input['emp_id'];
        $comments = $input['comments'];
        $val = $input['data']['value'];
        $id = $input['id'];
        $type = $input['type'];



        DB::beginTransaction();
        try {
            if ($type == 2) {
                $data['status'] = $val;
                $data['remarks'] = $comments;
            } else if ($type == 1) {
                $data['commercial_eval_status'] = $val;
                $data['commercial_eval_remarks'] = $comments;
            } else if ($type == 3) {
                $data['tender_award_commite_mem_status'] = $val;
                $data['tender_award_commite_mem_comment'] = $comments;
            }

            $results = SrmTenderBidEmployeeDetails::where('emp_id', $emp_id)->where('tender_id', $tender_id)->where('emp_id', $emp_id)->update($data, $id);


            if ($type == 3) {
                $min_Approval = $input['min_approval'];

                $results = SrmTenderBidEmployeeDetails::where('tender_id', $tender_id)->where('tender_award_commite_mem_status', 1)->count();
                $pending = SrmTenderBidEmployeeDetails::where('tender_id', $tender_id)->where('tender_award_commite_mem_status', 0)->count();

                $need = $min_Approval - $results;
                $status = 0;
                if ($min_Approval <= $results) {
                    $status = 1;
                } else if ($need > $pending) {
                    $status = 2;
                }

                TenderMaster::where('id', $tender_id)->update(['award_commite_mem_status' => $status]);
            }

            DB::commit();
            return ['success' => true, 'message' => 'Successfully updated', 'data' => $results];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function tenderBidDocVerification(Request $request)
    {

        $input = $request->all();
        $id = $input['tender_id'];
        $isNegotiation = $input['isNegotiation'];
        $comments = isset($input['comments']) ? $input['comments'] : null;
        // $val = $input['type'];

        DB::beginTransaction();
        try {

            $bid_sub_data['doc_verifiy_by_emp'] = \Helper::getEmployeeSystemID();
            $bid_sub_data['doc_verifiy_date'] =  date('Y-m-d H:i:s');

            if($isNegotiation == 1){
                $bid_sub_data['negotiation_doc_verify_comment'] = $comments;
                $bid_sub_data['negotiation_doc_verify_status'] = 1;
            } else {
                $bid_sub_data['doc_verifiy_status'] = 1;
                $bid_sub_data['doc_verifiy_comment'] = $comments;
            }

            $results = TenderMaster::where('id', $id)->update($bid_sub_data, $id);

            DB::commit();
            return ['success' => true, 'message' => 'Successfully updated', 'data' => $results];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }

    public function getTenderTechniqalEvaluation(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $tenderId = $input['tenderMasterId'];
        $bid_id = $input['bid_id'];
        if ($id == null) {
            $master_data = null;
            $bid_master_ids = (array)$bid_id;
            $submission_master_data = BidSubmissionMaster::where('id', $bid_id)->first();
        } else {
            $master_data = BidEvaluationSelection::where('id', $id)->first();

            $bid_master_ids = json_decode(BidEvaluationSelection::where('id', $id)->pluck('bids')[0], true);
            $submission_master_data = null;
        }



        $data['bid_submissions'] = BidSubmissionMaster::with('SupplierRegistrationLink')->whereIn('id', $bid_master_ids)->get();

        $techniqal_wightage = TenderMaster::where('id', $tenderId)->select('id', 'technical_weightage', 'show_technical_criteria')->first();


        $data['criteriaDetail'] = EvaluationCriteriaDetails::with(['evaluation_criteria_score_config', 'tender_criteria_answer_type', 'bid_submission_detail1' => function ($q) use ($bid_master_ids) {
            $q->whereIn('bid_master_id', $bid_master_ids)->orderBy('bid_master_id')->with(['srm_bid_submission_master' => function ($q) {
                $q->select('id', 'technical_verify_status');
            }]);
        }, 'child' => function ($q) use ($bid_master_ids) {
            $q->with(['evaluation_criteria_score_config', 'tender_criteria_answer_type', 'bid_submission_detail1' => function ($q) use ($bid_master_ids) {
                $q->whereIn('bid_master_id', $bid_master_ids)->orderBy('bid_master_id')->with(['srm_bid_submission_master' => function ($q) {
                    $q->select('id', 'technical_verify_status');
                }]);
            }, 'child' => function ($q) use ($bid_master_ids) {
                $q->with(['evaluation_criteria_score_config', 'tender_criteria_answer_type', 'bid_submission_detail1' => function ($q) use ($bid_master_ids) {
                    $q->whereIn('bid_master_id', $bid_master_ids)->orderBy('bid_master_id')->with(['srm_bid_submission_master' => function ($q) {
                        $q->select('id', 'technical_verify_status');
                    }]);
                }, 'child' => function ($q) use ($bid_master_ids) {
                    $q->with(['evaluation_criteria_score_config', 'tender_criteria_answer_type', 'bid_submission_detail1' => function ($q) use ($bid_master_ids) {
                        $q->whereIn('bid_master_id', $bid_master_ids)->orderBy('bid_master_id')->with(['srm_bid_submission_master' => function ($q) {
                            $q->select('id', 'technical_verify_status');
                        }]);
                    }]);
                }]);
            }]);
        }])->where('tender_id', $tenderId)->where('level', 1)->where('critera_type_id', 2)->get();

        $wight = [];
        $percentage = [];


        foreach ($bid_master_ids as $ids) {
            $srm_score = BidSubmissionDetail::where('bid_master_id', $ids)->sum('result');
            $erp_score_score = BidSubmissionDetail::where('bid_master_id', $ids)->sum('eval_result');

            $result =  round(($srm_score / 100) * $techniqal_wightage->technical_weightage, 3);
            $eval_result = round(($erp_score_score / 100) * $techniqal_wightage->technical_weightage, 3);

            $temp['result'] = $result;
            $temp['eval_result'] = $eval_result;

            $updated['tech_weightage'] =  $eval_result;
            $output = BidSubmissionMaster::where('id', $ids)->update($updated);

            if ($techniqal_wightage->technical_weightage == 0) {
                $temp1['result_percentage'] = 0;
                $temp1['eval_result_percentage'] = 0;
            } else {
                $temp1['result_percentage'] = round((($result) / $techniqal_wightage->technical_weightage) * 100, 3);
                $temp1['eval_result_percentage'] = round((($eval_result) / $techniqal_wightage->technical_weightage) * 100, 3);
            }

            array_push($wight, $temp);
            array_push($percentage, $temp1);
        }


        $data['weightage'] = $wight;
        $data['percentage'] = $percentage;
        $data['master_data'] = $master_data;
        $data['submission_master_data'] = $submission_master_data;
        $data['show_technical_criteria'] = $techniqal_wightage->show_technical_criteria;
        return $this->sendResponse($data, 'Tender Masters retrieved successfully');
    }

    public function getCommercialBidTenderList(Request $request)
    {
        $input = $request->all();
        $userId = \Helper::getEmployeeSystemID();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];
        $isNegotiation = isset($input['isNegotiation']) ? $input['isNegotiation'] : null;

        $filters = $this->getFilterData($input);

        // $tenderMaster = TenderMasterSupplier::with(['tender_master'=>function($q){
        //     $q->with(['tender_type', 'envelop_type', 'currency']);
        // }])->get();

        $query = TenderMaster::with(['currency', 'srm_bid_submission_master', 'tender_type', 'envelop_type', 'srmTenderMasterSupplier',
            'tenderUserAccess'=> function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->where('module_id',2);
            }, 'tenderBidMinimumApproval'=> function ($q1) use ($userId) {
                $q1->where('emp_id',$userId);
            },  'tender_negotiation' => function ($q2) {
                $q2->select('srm_tender_master_id')
                    ->selectSub(function ($query) {
                        $query->from('tender_negotiations as tn')
                            ->selectRaw('MAX(version)')
                            ->whereColumn('tn.srm_tender_master_id', 'tender_negotiations.srm_tender_master_id');
                    }, 'version');
            }])
            ->withCount([
                'tenderBidMinimumApproval as approved_count' => function ($q) {
                    $q->where('status', 1);
                }
            ])
            ->whereHas('srmTenderMasterSupplier')->where('published_yn', 1)
            ->where('company_id', $companyId)
            ->where(function ($query) use ($userId) {
                $query->whereHas('tenderUserAccess', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->where('module_id',2);
                })->orWhereHas('tenderBidMinimumApproval', function ($q1) use ($userId) {
                    $q1->where('emp_id', $userId);
                });
            })
            ->having('approved_count', '>=', \DB::raw('min_approval_bid_opening'));

        if($isNegotiation == 1){
            $query->where('is_negotiation_started',1)
                ->where('negotiation_published',1);
            $query->withCount(['criteriaDetails',
                'srm_bid_submission_master AS commercial_eval_negotiation' => function ($query2) {
                    $query2->with(['TenderBidNegotiation' => function ($q){
                        $q->where('commercial_verify_status',0);
                    }])
                        ->whereHas('TenderBidNegotiation',function ($q2){
                            $q2->where('commercial_verify_status',0);
                        });
                }
            ]);
            if ($filters['commercial']) {
                $ids = array_column($filters['commercial'], 'id');
                $query->where(function ($query) use ($ids) {
                    if (in_array(1, $ids)) {
                        $query->orWhere(function ($query) {
                            $query->whereDoesntHave('srm_bid_submission_master', function ($q) {
                                $q->where('commercial_verify_status', 0)->whereHas('TenderBidNegotiation');
                            });
                        });
                    }

                    if (in_array(0, $ids)) {
                        $query->orWhere(function ($query) {
                            $query->whereHas('srm_bid_submission_master', function ($q) {
                                $q->where('commercial_verify_status', 0)->whereHas('TenderBidNegotiation');
                            });
                        });
                    }
                });
            }
        }

        if ($filters['currencyId'] && count($filters['currencyId']) > 0) {
            $query->whereIn('currency_id', $filters['currencyId']);
        }

        if ($filters['selection']) {
            $query->where('tender_type_id', $filters['selection']);
        }

        if ($filters['envelope']) {
            $query->where('envelop_type_id', $filters['envelope']);
        }

        if ($filters['stage']) {
            $query->where('stage', $filters['stage']);
        }

        if ($filters['commercial'] && $isNegotiation != 1) {
            $ids = array_column($filters['commercial'], 'id');
            $query->whereIn('commercial_verify_status', $ids);
        }

        // return $this->sendResponse($query, 'Tender Masters retrieved successfully');

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('tender_code', 'LIKE', "%{$search}%");
                $query->orWhere('description', 'LIKE', "%{$search}%");
                $query->orWhere('description_sec_lang', 'LIKE', "%{$search}%");
                $query->orWhere('title', 'LIKE', "%{$search}%");
                $query->orWhere('title_sec_lang', 'LIKE', "%{$search}%");
            });

            if($isNegotiation == 1){
                $query->orWhere('negotiation_code', 'LIKE', "%{$search}%");
            }
        }




        return \DataTables::eloquent($query)
            ->filter(function ($query) {
                $query->where(function ($q) {
                    $q->where('stage', '<>', 2)
                        ->orWhere(function ($subQuery) {
                            $subQuery->where('stage', '=', 2)
                                ->where('technical_eval_status', 1)
                                ->where('doc_verifiy_status', 1)
                                ->where('go_no_go_status', 1);
                        });
                });
            })
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getCommercialEval(Request $request)
    {
        $input = $request->all();

        $tenderId = $request->input('extraParams.tenderId');
        $bidMasterId = $request->input('extraParams.id');



        $data = PricingScheduleMaster::with(['tender_bid_format_master', 'bid_schedule' => function ($q) use ($bidMasterId) {
            $q->where('bid_master_id', $bidMasterId);
        }, 'pricing_shedule_details' => function ($q) use ($bidMasterId) {
            $q->with(['bid_main_work' => function ($q) use ($bidMasterId) {
                $q->where('bid_master_id', $bidMasterId);
            }, 'bid_format_detail' => function ($q) use ($bidMasterId) {
                $q->where('bid_master_id', $bidMasterId);
                $q->orWhere('bid_master_id', null);
            },
                'tender_bid_format_detail' => function ($q) {
                    $q->select('id', 'tender_id', 'label', 'field_type', 'finalTotalYn');
                    $q->where('finalTotalYn', 1);
                }

            ]);
        }])->where('tender_id', $tenderId)->get();



        return [
            'success' => true,
            'message' => 'Successfully Received',
            'data' =>  $data
        ];
    }

    public function getCommercialEvalBoq(Request $request)
    {
        $mainWorkId = $request->input('extraParams.mainWorkId');
        $bidMasterId = $request->input('extraParams.bidMasterId');
        $data = TenderBoqItems::with(['unit', 'bid_boq' => function ($q) use ($bidMasterId) {
            $q->where('bid_master_id', $bidMasterId);
        }])->where('main_work_id', $mainWorkId)->get();



        return [
            'success' => true,
            'message' => 'Successfully Received',
            'data' =>  $data
        ];
    }

    public function getEvalCompletedTenderList(Request $request)
    {
        $input = $request->all();
        $userId = \Helper::getEmployeeSystemID();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];
        $isNegotiation = $input['isNegotiation'];
        $filters = $this->getFilterData($input);

        $query = TenderMaster::with(['currency', 'srm_bid_submission_master', 'tender_type', 'envelop_type', 'srmTenderMasterSupplier','tenderUserAccess'=> function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->where('module_id',3);
        },'tenderBidMinimumApproval'=> function ($q1) use ($userId) {
            $q1->where('emp_id',$userId);
        }, 'tender_negotiation' => function ($q2) {
            $q2->select('srm_tender_master_id', 'id')
                ->selectSub(function ($query) {
                    $query->from('tender_negotiations as tn')
                        ->selectRaw('MAX(version)')
                        ->whereColumn('tn.srm_tender_master_id', 'tender_negotiations.srm_tender_master_id');
                }, 'version');
        }, 'tender_negotiation.tenderBidNegotiation'])
            ->where(function ($query) use ($userId) {
                $query->whereHas('tenderUserAccess', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->where('module_id',3);
                })
                    ->orWhereHas('tenderBidMinimumApproval', function ($q1) use ($userId) {
                        $q1->where('emp_id', $userId);
                    });
            })
            ->whereHas('srmTenderMasterSupplier')->where('published_yn', 1)
            ->where('commercial_verify_status', 1)
            ->where('company_id', $companyId)
            ->where('technical_eval_status', 1);

        if($isNegotiation == 1){
            $query = $query->where('negotiation_code', '!=', null);
            $type = gettype($filters['combinedRankingStatus']);
            $combinedRankingStatusArray = json_decode(json_encode($filters['combinedRankingStatus']), true);

            $query->where(function ($query) use ($combinedRankingStatusArray) {
                if (!in_array(0, $combinedRankingStatusArray) && !in_array(1, $combinedRankingStatusArray)) {
                    return;
                }
                if (in_array(0, $combinedRankingStatusArray)) {
                    $query->whereNull('negotiation_is_awarded');
                }

                if (in_array(1, $combinedRankingStatusArray)) {
                    $query->orWhere('negotiation_is_awarded', 1);
                }
            });

        } else {
            if ($filters['combinedRankingStatus'] && count($filters['combinedRankingStatus']) > 0) {
                $query->whereIn('is_awarded', $filters['combinedRankingStatus']);
            } else {
                $query->whereIn('is_awarded', [0 , 1]);
            }
        }

        if ($filters['currencyId'] && count($filters['currencyId']) > 0) {
            $query->whereIn('currency_id', $filters['currencyId']);
        }


        if ($filters['selection']) {
            $query->where('tender_type_id', $filters['selection']);
        }

        if ($filters['envelope']) {
            $query->where('envelop_type_id', $filters['envelope']);
        }

        if ($filters['stage']) {
            $query->where('stage', $filters['stage']);
        }


        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
                $query->orWhere('description_sec_lang', 'LIKE', "%{$search}%");
                $query->orWhere('title', 'LIKE', "%{$search}%");
                $query->orWhere('negotiation_code', 'LIKE', "%{$search}%");
                $query->orWhere('tender_code', 'LIKE', "%{$search}%");
                $query->orWhere('title_sec_lang', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($query)

            ->filter(function ($query) {
                $query->where(function ($q) {
                    $q->where('stage', '<>', 1)
                        ->orWhere(function ($subQuery) {
                            $subQuery->where('stage', '=', 1)
                                ->where('doc_verifiy_status', 1)
                                ->where('go_no_go_status', 1);
                        });
                });
            })
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getTechnicalRanking(Request $request)
    {
        $input = $request->all();
        $this->tenderMasterRepository->checkRankingEmptyRecords($request);
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];
        $tenderId = $request['tenderId'];
        $isNegotiation = $request['isNegotiation'];

        $tenderBidNegotiations = TenderNegotiation::tenderBidNegotiationList($tenderId, $isNegotiation);;

        if ($tenderBidNegotiations->count() > 0) {
            $bidSubmissionMasterIds = $tenderBidNegotiations->pluck('bid_submission_master_id_new')->toArray();
        } else {
            $bidSubmissionMasterIds = [];
        }
        $technicalCount =  $this->getTechnicalCount($tenderId);

        // Set Technical Ranking
        $getRankCount = BidSubmissionDetail::where('tender_id', $tenderId)
            ->where('technical_ranking', '!=', null);


        if ($isNegotiation == 1) {
            $getRankCount = $getRankCount->whereIn('bid_master_id', $bidSubmissionMasterIds);
        } else {
            $getRankCount = $getRankCount->whereNotIn('bid_master_id', $bidSubmissionMasterIds);
        }

        $getRankCount = $getRankCount->count();

        if($getRankCount == 0){
            $this->CreateStoreTechnicalRanking($tenderId, $bidSubmissionMasterIds, $isNegotiation, $tenderBidNegotiations);
        }

        if($technicalCount->technical_count > 0)
        {
            $query = BidSubmissionMaster::selectRaw("round(SUM((srm_bid_submission_detail.eval_result/100)*srm_tender_master.technical_weightage),3) as weightage,srm_bid_submission_master.id,srm_bid_submission_master.bidSubmittedDatetime,srm_bid_submission_master.tender_id,srm_supplier_registration_link.name,srm_bid_submission_detail.id as bid_id,srm_bid_submission_master.commercial_verify_status,srm_bid_submission_master.bidSubmissionCode,srm_tender_master.technical_passing_weightage as passing_weightage,srm_bid_submission_detail.technical_ranking")
                ->join('srm_supplier_registration_link', 'srm_supplier_registration_link.id', '=', 'srm_bid_submission_master.supplier_registration_id')
                ->join('srm_tender_master', 'srm_tender_master.id', '=', 'srm_bid_submission_master.tender_id')
                ->join('srm_bid_submission_detail', 'srm_bid_submission_detail.bid_master_id', '=', 'srm_bid_submission_master.id')
                ->join('srm_evaluation_criteria_details', 'srm_evaluation_criteria_details.id', '=', 'srm_bid_submission_detail.evaluation_detail_id')
                ->havingRaw('weightage >= passing_weightage')
                ->groupBy('srm_bid_submission_master.id')
                ->where('srm_bid_submission_master.status', 1)
                ->where('srm_bid_submission_master.bidSubmittedYN', 1)
                ->where('srm_bid_submission_master.doc_verifiy_status','!=',2)
                ->where('srm_bid_submission_master.commercial_verify_status', 1)
                ->where('srm_bid_submission_master.tender_id', $tenderId);

            if ($isNegotiation == 1) {
                $query = $query->whereIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
            } else {
                $query = $query->whereNotIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
            }

            $query = $query->orderBy('weightage', 'desc');
        }
        else
        {
            $query = BidSubmissionMaster::selectRaw("'' as weightage,srm_bid_submission_master.id,srm_bid_submission_master.bidSubmittedDatetime,srm_bid_submission_master.tender_id,srm_supplier_registration_link.name,'' as bid_id,srm_bid_submission_master.commercial_verify_status,srm_bid_submission_master.bidSubmissionCode,srm_tender_master.technical_passing_weightage as passing_weightage,'' as technical_ranking")

                ->join('srm_supplier_registration_link', 'srm_supplier_registration_link.id', '=', 'srm_bid_submission_master.supplier_registration_id')
                ->join('srm_tender_master', 'srm_tender_master.id', '=', 'srm_bid_submission_master.tender_id')
                ->groupBy('srm_bid_submission_master.id')
                ->where('srm_bid_submission_master.status', 1)
                ->where('srm_bid_submission_master.bidSubmittedYN', 1)
                ->where('srm_bid_submission_master.doc_verifiy_status','!=',2)
                ->where('srm_bid_submission_master.commercial_verify_status', 1)
                ->where('srm_bid_submission_master.tender_id', $tenderId);

            if ($isNegotiation == 1) {
                $query = $query->whereIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
            } else {
                $query = $query->whereNotIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
            }

            $query = $query->orderBy('weightage', 'desc');
        }




        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('bidSubmissionCode', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($query)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    private function CreateStoreTechnicalRanking($tenderId, $bidSubmissionMasterIds, $isNegotiation, $tenderBidNegotiations){
        //Get Negotiation Area
        if($isNegotiation == 1){
            $tenderBidNegotiations = TenderBidNegotiation::with(['tender_negotiation_area'])->select('tender_negotiation_id')
                ->where('tender_id', $tenderId)
                ->first();

            if (
                isset($tenderBidNegotiations->tender_negotiation_area) &&
                is_object($tenderBidNegotiations->tender_negotiation_area) &&
                ($tenderBidNegotiations->tender_negotiation_area->technical_evaluation == 0 ||
                    $tenderBidNegotiations->tender_negotiation_area->technical_evaluation == false)
            ) {
                return;
            }
        }
        $tenderFinalBids = BidSubmissionMaster::selectRaw("round(SUM((srm_bid_submission_detail.eval_result/100)*srm_tender_master.technical_weightage),3) as weightage, srm_tender_master.technical_passing_weightage as passing_weightage,srm_bid_submission_detail.id as srm_bid_submission_detail_id")
            ->join('srm_tender_master', 'srm_tender_master.id', '=', 'srm_bid_submission_master.tender_id')
            ->join('srm_bid_submission_detail', 'srm_bid_submission_detail.bid_master_id', '=', 'srm_bid_submission_master.id')
            ->havingRaw('weightage >= passing_weightage')
            ->groupBy('srm_bid_submission_master.id')
            ->where('srm_bid_submission_master.status', 1)
            ->where('srm_bid_submission_master.bidSubmittedYN', 1)
            ->where('srm_bid_submission_master.doc_verifiy_status','!=',2)
            ->where('srm_bid_submission_master.commercial_verify_status', 1);
        if ($isNegotiation == 1) {
            $tenderFinalBids = $tenderFinalBids->whereIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
        } else {
            $tenderFinalBids = $tenderFinalBids->whereNotIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
        }

        $tenderFinalBids = $tenderFinalBids->where('srm_bid_submission_master.tender_id', $tenderId)
            ->orderBy('weightage', 'desc')
            ->get();
        $weightage = null;
        $index1 = 1;
        foreach ($tenderFinalBids as $index => $record) {
            if ($index === 0) {
                $weightage = $record->weightage;
                $record->technical_ranking = $index1;
            } else {
                if ($weightage === $record->weightage) {
                    $record->technical_ranking = $index1;
                } else {
                    $weightage = $record->weightage;
                    $index1++;
                    $record->technical_ranking = $index1;
                }
            }
            // Update the record in the database with the calculated ranking
            BidSubmissionDetail::where('id', $record->srm_bid_submission_detail_id)
                ->update(['technical_ranking' => $record->technical_ranking]);
        }
    }

    public function getCommercialRanking(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];
        $tenderId = $request['tenderId'];
        $isNegotiation = $request['isNegotiation'];

        $tenderBidNegotiations = TenderNegotiation::tenderBidNegotiationList($tenderId, $isNegotiation);;

        if ($tenderBidNegotiations->count() > 0) {
            $bidSubmissionMasterIds = $tenderBidNegotiations->pluck('bid_submission_master_id_new')->toArray();
        } else {
            $bidSubmissionMasterIds = [];
        }

        $techniqal_wightage = TenderMaster::where('id', $tenderId)->select('id', 'technical_weightage', 'commercial_weightage')
            ->withCount(['criteriaDetails',
                'criteriaDetails AS go_no_go_count' => function ($query) {
                    $query->where('critera_type_id', 1);
                },
                'criteriaDetails AS technical_count' => function ($query) {
                    $query->where('critera_type_id', 2);
                }
            ])->first();


        $total_amount = 0;

        if($techniqal_wightage->technical_count == 0)
        {
            $query1 =  BidSubmissionMaster::selectRaw("'' as weightage,srm_bid_submission_master.id,srm_bid_submission_master.bidSubmittedDatetime,srm_tender_final_bids.commercial_ranking,srm_bid_submission_master.tender_id,srm_supplier_registration_link.name,'' as bid_id,srm_bid_submission_master.commercial_verify_status,srm_bid_submission_master.bidSubmissionCode,srm_tender_master.technical_passing_weightage as passing_weightage,srm_supplier_registration_link.id as supplier_id")
                ->join('srm_supplier_registration_link', 'srm_supplier_registration_link.id', '=', 'srm_bid_submission_master.supplier_registration_id')
                ->join('srm_tender_master', 'srm_tender_master.id', '=', 'srm_bid_submission_master.tender_id')
                ->join('srm_tender_final_bids', 'srm_tender_master.id', '=', 'srm_tender_final_bids.tender_id')
                ->groupBy('srm_bid_submission_master.id')->where('srm_bid_submission_master.status', 1)
                ->where('srm_bid_submission_master.bidSubmittedYN', 1)
                ->where('srm_bid_submission_master.tender_id', $tenderId);
            if ($isNegotiation == 1) {
                $query1 = $query1->whereIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
            } else {
                $query1 = $query1->whereNotIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
            }
            $query1 = $query1->where('srm_bid_submission_master.doc_verifiy_status', 1)->pluck('supplier_id')->toArray();
        }
        else
        {
            $query1 = BidSubmissionMaster::selectRaw("round(SUM((srm_bid_submission_detail.eval_result/100)*srm_tender_master.technical_weightage),3) as weightage, srm_bid_submission_master.id,srm_bid_submission_master.bidSubmittedDatetime,srm_bid_submission_master.tender_id,srm_supplier_registration_link.name,srm_bid_submission_detail.id as bid_id,srm_bid_submission_master.commercial_verify_status,srm_bid_submission_master.bidSubmissionCode,srm_tender_master.technical_passing_weightage as passing_weightage,srm_bid_submission_master.comm_weightage,srm_bid_submission_master.line_item_total,srm_supplier_registration_link.id as supplier_id")
                ->join('srm_supplier_registration_link', 'srm_supplier_registration_link.id', '=', 'srm_bid_submission_master.supplier_registration_id')
                ->join('srm_tender_master', 'srm_tender_master.id', '=', 'srm_bid_submission_master.tender_id')
                ->join('srm_bid_submission_detail', 'srm_bid_submission_detail.bid_master_id', '=', 'srm_bid_submission_master.id')
                ->join('srm_evaluation_criteria_details', 'srm_evaluation_criteria_details.id', '=', 'srm_bid_submission_detail.evaluation_detail_id')
                ->join('srm_bid_main_work', 'srm_bid_main_work.bid_master_id', '=', 'srm_bid_submission_master.id','left')
                ->havingRaw('weightage >= passing_weightage')
                ->groupBy('srm_bid_submission_master.id')
                ->where('srm_bid_submission_master.status', 1)->where('srm_bid_submission_master.bidSubmittedYN', 1);

            if ($isNegotiation == 1) {
                $query1 = $query1->whereIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
            } else {
                $query1 = $query1->whereNotIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
            }

            $query1 = $query1->where('srm_bid_submission_master.tender_id', $tenderId)->where('srm_bid_submission_master.commercial_verify_status', 1)
                ->orderBy('srm_bid_submission_master.comm_weightage', 'asc')->pluck('supplier_id')->toArray();
        }




        $query = TenderFinalBids::selectRaw('srm_tender_final_bids.commercial_ranking,srm_tender_final_bids.id,srm_tender_final_bids.status,srm_tender_final_bids.supplier_id,srm_tender_final_bids.com_weightage as weightage, srm_tender_final_bids.bid_id,srm_bid_submission_master.bidSubmittedDatetime,srm_supplier_registration_link.name,srm_bid_submission_master.bidSubmissionCode,srm_bid_submission_master.line_item_total')
            ->join('srm_bid_submission_master', 'srm_bid_submission_master.id', '=', 'srm_tender_final_bids.bid_id')
            ->join('srm_supplier_registration_link', 'srm_supplier_registration_link.id', '=', 'srm_bid_submission_master.supplier_registration_id')
            ->where('srm_tender_final_bids.tender_id', $tenderId);

        if ($isNegotiation == 1) {
            $query = $query->whereIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
        } else {
            $query = $query->whereNotIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
        }

        $query =  $query->orderBy('srm_tender_final_bids.com_weightage', 'desc');



        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('bidSubmissionCode', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($query)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->addColumn('selection', function ($row) use ($query1, $isNegotiation) {
                $count =  count(array_keys($query1, $row->supplier_id));
                if ($count == 1) {
                    return true;
                } else {
                    return false;
                }
            })
            ->addColumn('radio', function ($row) use ($query1) {

                return 1;
            })
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getBidItemSelection(Request $request)
    {

        $tenderId = $request['tenderMasterId'];
        $isNegotiation = $request['isNegotiation'];

        $bidMasterId = $this->getCommercialBids($tenderId, $isNegotiation);
        $data['bids'] = $bidMasterId;
        $items = $this->getPricingItems($bidMasterId, $tenderId);

        foreach ($items[0]->pricing_shedule_details as $key => $val) {

            if ($val->is_disabled == 1) {
                $val1 =  CommercialBidRankingItems::updateOrCreate(
                    ['bid_format_detail_id' => $val->id, 'tender_id' => $tenderId, 'value' => (float)$val->bid_format_detail->value, 'filed_type' => $val->field_type, 'is_disable' => 1],
                    ['bid_format_detail_id' => $val->id, 'tender_id' => $tenderId, 'value' => (float)$val->bid_format_detail->value, 'filed_type' => $val->field_type, 'is_disable' => 1]
                );

                PricingScheduleDetail::where('id', $val1->bid_format_detail_id)->where('tender_id', $tenderId)->update(['tender_ranking_line_item' => $val1->id]);
            } else if ($val->is_disabled == 0 && ($val->boq_applicable == 0 || $val->boq_applicable == 1)) {

                $count = count($val->tender_boq_items);
                if ($count > 0) {
                    $child_exist = 1;
                } else {
                    $child_exist = 0;
                }
                $val2 = CommercialBidRankingItems::updateOrCreate(
                    ['bid_format_detail_id' => $val->id, 'tender_id' => $tenderId, 'filed_type' => $val->field_type, 'is_main' => 1, 'is_child_exist' => $child_exist],
                    ['bid_format_detail_id' => $val->id, 'tender_id' => $tenderId, 'filed_type' => $val->field_type, 'is_main' => 1, 'is_child_exist' => $child_exist]
                );

                PricingScheduleDetail::where('id', $val2->bid_format_detail_id)->where('tender_id', $tenderId)->update(['tender_ranking_line_item' => $val2->id]);

                foreach ($val->tender_boq_items as $bid_works) {


                    $val3 = CommercialBidRankingItems::updateOrCreate(
                        ['bid_format_detail_id' => $bid_works->id, 'tender_id' => $tenderId, 'filed_type' => $val->field_type, 'is_main' => 0, 'is_child_exist' => 1, 'parent_id' => $val->id],
                        ['bid_format_detail_id' => $bid_works->id, 'tender_id' => $tenderId, 'filed_type' => $val->field_type, 'is_main' => 0, 'is_child_exist' => 1, 'parent_id' => $val->id]
                    );

                    TenderBoqItems::where('id', $val3->bid_format_detail_id)->update(['tender_ranking_line_item' => $val3->id]);
                }
            }
        }

        $line_item_values =  CommercialBidRankingItems::where('tender_id', $tenderId)->where('status', 1)->get();
        $this->updateLineItem($bidMasterId, $line_item_values, $tenderId);

        $data['bid_submissions'] = BidSubmissionMaster::with('SupplierRegistrationLink')->whereIn('id', $bidMasterId)->where('tender_id', $tenderId)->get();
        $items = $this->getPricingItems($bidMasterId, $tenderId);
        $data['items']  = $items;
        return $this->sendResponse($data, 'data retrieved successfully');
    }

    public function getPricingItems($bidMasterId, $tenderId)
    {
        if (!empty($bidMasterId)) {
            BidMainWork::deleteIncompleteBidMainWorkRecords($tenderId, $bidMasterId);
        }

        return PricingScheduleMaster::with(['tender_bid_format_master', 'pricing_shedule_details' => function ($q) use ($bidMasterId) {
            $q->with(['bid_main_works' => function ($q) use ($bidMasterId) {
                $q->whereIn('bid_master_id', $bidMasterId);
            }, 'bid_format_detail' => function ($q) use ($bidMasterId) {
                $q->whereIn('bid_master_id', $bidMasterId);
                $q->orWhere('bid_master_id', null);
            }, 'tender_boq_items' => function ($q) {
                $q->with(['bid_boqs', 'ranking_items']);
            }, 'ranking_items'])->whereNotIn('field_type', [4]);;
        }])->where('tender_id', $tenderId)->get();
    }

    public function updateBidLineItem(Request $request)
    {
        DB::beginTransaction();
        try {

            $tenderId = $request['tenderMasterId'];
            $id = $request['id'];
            $checked = $request['checked'];
            $rang_id = $request['rang_id'];
            $type = $request['type'];
            $isNegotiation = $request['isNegotiation'];

            if ($type == 1) {
                $update =  CommercialBidRankingItems::where('tender_id', $tenderId)->update(['status' => $checked]);
                TenderMaster::where('id', $tenderId)->update(['commercial_line_item_status' => $checked]);
            } else {
                $ranging =  CommercialBidRankingItems::find($rang_id);

                if ($ranging->is_child_exist == 1 && $ranging->is_main == 1) {
                    $update =  CommercialBidRankingItems::where('parent_id', $id)->update(['status' => $checked]);
                }
                CommercialBidRankingItems::updateOrCreate(
                    ['id' => $rang_id, 'bid_format_detail_id' => $id, 'tender_id' => $tenderId],
                    ['bid_format_detail_id' => $id, 'tender_id' => $tenderId, 'status' => $checked]
                );

                if ($ranging->is_main == 0) {
                    $count =  CommercialBidRankingItems::where('parent_id', $ranging->parent_id)->where('status', 1)->count();
                    if ($checked || $count == 0) {
                        $update =  CommercialBidRankingItems::where('bid_format_detail_id', $ranging->parent_id)->where('is_main', 1)->update(['status' => $checked]);
                    }
                }
            }

            $bidMasterId = $this->getCommercialBids($tenderId,$isNegotiation);

            $line_item_values =  CommercialBidRankingItems::where('tender_id', $tenderId)->where('status', 1)->get();
            $this->updateLineItem($bidMasterId, $line_item_values, $tenderId);

            DB::commit();
            return ['success' => true, 'message' => 'Successfully updated', 'data' => true];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function confirmCommBidLineItem(Request $request)
    {


        DB::beginTransaction();
        try {

            $tenderId = $request['tenderMasterId'];
            $isNegotiation = $request['isNegotiation'];
            $status = $request['commercial_ranking_line_item_status'];
            $bids = $request['bids'];

            $pricing_schedule = true;
            $technical_evaluation = true;

            //Get Negotiation Area
            if($isNegotiation == 1){
                $tenderBidNegotiations = TenderBidNegotiation::with(['tender_negotiation_area'])->select('tender_negotiation_id')
                    ->where('tender_id', $tenderId)
                    ->first();

                if($tenderBidNegotiations->tender_negotiation_area->pricing_schedule == 0 || $tenderBidNegotiations->tender_negotiation_area->pricing_schedule == false){
                    $pricing_schedule = false;
                }

                if($tenderBidNegotiations->tender_negotiation_area->technical_evaluation == 0 || $tenderBidNegotiations->tender_negotiation_area->technical_evaluation == false){
                    $technical_evaluation = false;
                }

            }

            $techniqal_wightage = TenderMaster::where('id', $tenderId)->select('id', 'technical_weightage', 'commercial_weightage')->first();

            if($isNegotiation == 1){
                $techniqal_wightage->negotiation_commercial_ranking_line_item_status = $status;
            }else {
                $techniqal_wightage->commercial_ranking_line_item_status = $status;
            }

            $techniqal_wightage->save();

            $minLineItemTotal = BidSubmissionMaster::whereIn('id', $bids)->min('line_item_total');

            $result = BidSubmissionMaster::whereIn('id', $bids)->select('id', 'line_item_total', 'tech_weightage', 'supplier_registration_id')->get();

            $supplier_ids = BidSubmissionMaster::whereIn('id', $bids)->pluck('supplier_registration_id')->toArray();


            foreach ($result as $key => $val) {
                $output = 0;

                $count =  count(array_keys($supplier_ids, $val->supplier_registration_id));

                $weightage = round(($minLineItemTotal / $val->line_item_total) * $techniqal_wightage->commercial_weightage, 3);

                if($isNegotiation == 1 && $pricing_schedule == false){
                    $weightage = 0;
                }

                if($isNegotiation == 1 && $technical_evaluation == false){
                    $val->tech_weightage = 0;
                }

                $results = BidSubmissionMaster::find($val->id)
                    ->update(['comm_weightage' => $weightage]);


                $total = round($val->tech_weightage + $weightage, 3);

                $results = BidSubmissionMaster::find($val->id)
                    ->update(['total_weightage' => $total]);

                $status_val = 0;
                if ($count == 1) {
                    $status_val = 1;
                }

                TenderFinalBids::updateOrCreate(
                    ['tender_id' => $tenderId, 'bid_id' => $val->id, 'supplier_id' => $val->supplier_registration_id],
                    ['tender_id' => $tenderId, 'bid_id' => $val->id, 'supplier_id' => $val->supplier_registration_id, 'com_weightage' => $weightage, 'tech_weightage' => $val->tech_weightage==null?0:$val->tech_weightage, 'total_weightage' => $total, 'status' => $status_val]
                );
            }

            $tenderBidNegotiations = TenderBidNegotiation::select('bid_submission_master_id_new')
                ->where('tender_id', $tenderId)
                ->get();

            if ($tenderBidNegotiations->count() > 0) {
                $bidSubmissionMasterIds = $tenderBidNegotiations->pluck('bid_submission_master_id_new')->toArray();
            } else {
                $bidSubmissionMasterIds = [];
            }

            // Create Commercial Ranking and update to table
            $getRankCount = TenderFinalBids::where('tender_id', $tenderId)
                ->where('commercial_ranking', '!=', null);
            if ($isNegotiation == 1) {
                $getRankCount = $getRankCount->whereIn('bid_id', $bidSubmissionMasterIds);
            } else {
                $getRankCount = $getRankCount->whereNotIn('bid_id', $bidSubmissionMasterIds);
            }

            $getRankCount = $getRankCount->count();

            if($getRankCount == 0){
                $tenderFinalBids = TenderFinalBids::select('id','com_weightage')
                    ->where('tender_id', $tenderId);

                if( $isNegotiation == 1){
                    $tenderFinalBids = $tenderFinalBids->whereIn('bid_id', $bidSubmissionMasterIds);
                } else {
                    $tenderFinalBids = $tenderFinalBids->whereNotIn('bid_id', $bidSubmissionMasterIds);
                }

                $tenderFinalBids = $tenderFinalBids->orderBy('com_weightage', 'desc')
                    ->get();

                $weightage = null;
                $index1 = 1;
                foreach ($tenderFinalBids as $index => $record) {
                    if ($index === 0) {
                        $weightage = $record->com_weightage;
                        $record->ranking = $index1;
                    } else {
                        if ($weightage === $record->com_weightage) {
                            $record->ranking = $index1;
                        } else {
                            $weightage = $record->com_weightage;
                            $index1++;
                            $record->ranking = $index1;
                        }
                    }

                    // Update the record in the database with the calculated ranking
                    TenderFinalBids::where('id', $record->id)->update(['commercial_ranking' => $record->ranking]);
                }
            }
            DB::commit();
            return ['success' => true, 'message' => 'Line items Successfully updated', 'data' => $results];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function confirmFinalCommercial(Request $request)
    {
        DB::beginTransaction();
        try {
            $inputs = $request['extraParams'];
            $tenderId = $inputs['tenderMasterId'];
            $isNegotiation = $inputs['isNegotiation'];
            $selected_suppliers = $inputs['suppliers'];
            $ids = $inputs['ids'];
            $comment = $inputs['comment'];
            $suppliers = TenderFinalBids::distinct('supplier_id')->where('tender_id', $tenderId)->where('status', 0)->pluck('supplier_id')->toArray();
            $is_equal = $this->array_equal($selected_suppliers, $suppliers);
            if (!$is_equal && $isNegotiation == 0) {
                return $this->sendError('Please select atleast one bid for each suppliers', 500);
            } else {
                TenderFinalBids::whereIn('id', $ids)->update(['status' => true]);
                if($isNegotiation == 1){
                    $update = ['negotiation_combined_ranking_status' => true, 'negotiation_commercial_ranking_comment' => $comment];
                } else {
                    $update = ['combined_ranking_status' => true, 'commercial_ranking_comment' => $comment];
                }
                TenderMaster::where('id', $tenderId)->update($update);
            }
            $tenderBidNegotiations = TenderBidNegotiation::select('bid_submission_master_id_new')
                ->where('tender_id', $tenderId)
                ->get();
            if ($tenderBidNegotiations->count() > 0) {
                $bidSubmissionMasterIds = $tenderBidNegotiations->pluck('bid_submission_master_id_new')->toArray();
            } else {
                $bidSubmissionMasterIds = [];
            }
            $getRankCount = TenderFinalBids::where('tender_id', $tenderId)
                ->where('combined_ranking', '!=', null)
                ->whereIn('id', $ids)
                ->count();
            if($getRankCount == 0){
                $tenderFinalBids = TenderFinalBids::select('id','total_weightage')
                    ->where('tender_id', $tenderId)
                    ->where('status', '!=', 0);
                if( $isNegotiation == 1){
                    $tenderFinalBids = $tenderFinalBids->whereIn('bid_id', $bidSubmissionMasterIds);
                } else {
                    $tenderFinalBids = $tenderFinalBids->whereNotIn('bid_id', $bidSubmissionMasterIds);
                }
                $tenderFinalBids = $tenderFinalBids->orderBy('total_weightage', 'desc')->get();
                $weightage = null;
                $index1 = 1;
                foreach ($tenderFinalBids as $index => $record) {
                    if ($index === 0) {
                        $weightage = $record->total_weightage;
                        $record->ranking = $index1;
                    } else {
                        if ($weightage === $record->total_weightage) {
                            $record->ranking = $index1;
                        } else {
                            $weightage = $record->total_weightage;
                            $index1++;
                            $record->ranking = $index1;
                        }
                    }
                    // Update the record in the database with the calculated ranking
                    TenderFinalBids::where('id', $record->id)->update(['combined_ranking' => $record->ranking]);
                }
            }
            DB::commit();
            return ['success' => true, 'message' => 'Successfully updated', 'data' => true];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }
    function array_equal($a, $b)
    {
        return (is_array($a)
            && is_array($b)
            && count($a) == count($b)
            && array_diff($a, $b) === array_diff($b, $a)
        );
    }

    function updateLineItem($bidMasterId, $line_item_values, $tenderId)
    {

        foreach ($bidMasterId as $key => $val) {

            $total = 0;


            foreach ($line_item_values as $item) {
                if ($item->is_disable == 0) {
                    if ($item->is_child_exist == 1 && $item->is_main == 0) {

                        $boq = BidBoq::where('boq_id', $item->bid_format_detail_id)->where('bid_master_id', $val)->where('main_works_id', $item->parent_id)->select('total_amount')->first();

                        if ($item->filed_type == 3) {
                            $total += $boq->total_amount / 100;
                        } else {
                            $total += $boq->total_amount;
                        }
                    } else if ($item->is_child_exist == 0 && $item->is_main == 1) {

                        $boq_mai = BidMainWork::where('main_works_id', $item->bid_format_detail_id)->where('bid_master_id', $val)->where('tender_id', $tenderId)->select('total_amount')->first();

                        if (isset($boq_mai)) {
                            if ($item->filed_type == 3) {
                                $total += $boq_mai->total_amount / 100;
                            } else {
                                $total += $boq_mai->total_amount;
                            }
                        }
                    }
                } else if ($item->is_disable == 1) {
                    if ($item->filed_type == 3) {
                        $total += $item->value / 100;
                    } else {
                        $total += $item->value;
                    }
                }
            }
            $results = BidSubmissionMaster::find($val)
                ->update(['line_item_total' => $total]);
        }
    }

    function getCommercialBids($tenderId, $isNegotiation)
    {
        $tender= TenderMaster::select('id')->withCount(['criteriaDetails',
            'criteriaDetails AS go_no_go_count' => function ($query) {
                $query->where('critera_type_id', 1);
            },
            'criteriaDetails AS technical_count' => function ($query) {
                $query->where('critera_type_id', 2);
            }
        ])->withCount(['DocumentAttachments'=>function($q){
            $q->where('envelopType',3);
        }])->where('id', $tenderId)->first();

        $tenderBidNegotiations = TenderNegotiation::tenderBidNegotiationList($tenderId, $isNegotiation);

        if ($tenderBidNegotiations->count() > 0) {
            $bidSubmissionMasterIds = $tenderBidNegotiations->pluck('bid_submission_master_id_new')->toArray();
        } else {
            $bidSubmissionMasterIds = [];
        }

        if($tender->technical_count == 0)
        {
            $query = BidSubmissionMaster::selectRaw("'' as weightage,srm_bid_submission_master.id,srm_bid_submission_master.bidSubmittedDatetime,srm_bid_submission_master.tender_id,srm_supplier_registration_link.name,'' as bid_id,srm_bid_submission_master.commercial_verify_status,srm_bid_submission_master.bidSubmissionCode,srm_tender_master.technical_passing_weightage as passing_weightage")
                ->join('srm_supplier_registration_link', 'srm_supplier_registration_link.id', '=', 'srm_bid_submission_master.supplier_registration_id')
                ->join('srm_tender_master', 'srm_tender_master.id', '=', 'srm_bid_submission_master.tender_id')
                ->groupBy('srm_bid_submission_master.id')->where('srm_bid_submission_master.status', 1)
                ->where('srm_bid_submission_master.bidSubmittedYN', 1)
                ->where('srm_bid_submission_master.tender_id', $tenderId);

            if ($isNegotiation == 1) {
                $query = $query->whereIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
            } else {
                $query = $query->whereNotIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
            }

            return $query->where('srm_bid_submission_master.doc_verifiy_status', 1)->pluck('id');
        }
        else
        {
            $query = BidSubmissionMaster::selectRaw("round(SUM((srm_bid_submission_detail.eval_result/100)*srm_tender_master.technical_weightage),3) as weightage,srm_bid_submission_master.id,srm_bid_submission_master.bidSubmittedDatetime,srm_bid_submission_master.tender_id,srm_bid_submission_detail.id as bid_id,srm_bid_submission_master.commercial_verify_status,srm_bid_submission_master.bidSubmissionCode,srm_tender_master.technical_passing_weightage as passing_weightage")
                ->join('srm_tender_master', 'srm_tender_master.id', '=', 'srm_bid_submission_master.tender_id')
                ->join('srm_bid_submission_detail', 'srm_bid_submission_detail.bid_master_id', '=', 'srm_bid_submission_master.id')
                ->havingRaw('weightage >= passing_weightage')
                ->groupBy('srm_bid_submission_master.id')
                ->where('srm_bid_submission_master.status', 1)->where('srm_bid_submission_master.bidSubmittedYN', 1)->where('srm_bid_submission_master.tender_id', $tenderId)->where('srm_bid_submission_master.commercial_verify_status', 1);

            if ($isNegotiation == 1) {
                $query = $query->whereIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
            } else {
                $query = $query->whereNotIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
            }

            return $query->orderBy('srm_bid_submission_master.id', 'asc')->pluck('id');
        }

    }

    public function getRankingCompletedTenderList(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];
        $filters = $this->getFilterData($input);

        $hasPolicyToCreatePOFromTender = Helper::checkPolicy($companyId, 97);
        $hasPolicyToInitiateContractFromTender = Helper::checkPolicy($companyId, 100);
        $hasPolicyToLoiLoa = Helper::checkPolicy($companyId, 101);

        $query = TenderMaster::with(['currency', 'srm_bid_submission_master', 'tender_type', 'envelop_type',
            'contract' => function ($q)
            {
                $q->select('id', 'contractCode');
            },
            'srmTenderMasterSupplier', 'srmTenderMasterSupplier.supplierDetails' => function ($s1) {
                $s1->select('id', 'name', 'uuid', 'email');
            }, 'ranking_supplier.bid_submission_master' => function ($rs) {
                $rs->select('id', 'bidSubmittedDatetime', 'line_item_total');
            },
            'srmTenderPo' => function ($q) {
                $q->select('id', 'tender_id', 'po_id')->with(['procument_order' => function ($po) {
                    $po->select('purchaseOrderID', 'purchaseOrderCode');
                }]);
            }])->whereHas('srmTenderMasterSupplier')->where('published_yn', 1)
            ->where('is_awarded', 1)->where('company_id', $companyId)->where(function ($query) {
                $query->where('negotiation_published', 0)
                    ->orWhere('is_negotiation_closed', 1);
            });

        if ($filters['tenderAwardingStatus'] && count($filters['tenderAwardingStatus']) > 0) {
            $query->whereIn('final_tender_awarded', $filters['tenderAwardingStatus']);
        }

        if ($filters['currencyId'] && count($filters['currencyId']) > 0) {
            $query->whereIn('currency_id', $filters['currencyId']);
        }

        if ($filters['selection']) {
            $ids = array_column($filters['selection'], 'id');
            $query->whereIn('tender_type_id', $ids);
        }

        if ($filters['envelope']) {
            $ids = array_column($filters['envelope'], 'id');
            $query->whereIn('envelop_type_id', $ids);
        }

        if ($filters['stage']) {
            $ids = array_column($filters['stage'], 'id');
            $query->whereIn('stage', $ids);
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
                $query->orWhere('description_sec_lang', 'LIKE', "%{$search}%");
                $query->orWhere('title', 'LIKE', "%{$search}%");
                $query->orWhere('title_sec_lang', 'LIKE', "%{$search}%");
                $query->orWhere('tender_code', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($query)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->addColumn('hasPolicyToCreatePOFromTender', $hasPolicyToCreatePOFromTender)
            ->addColumn('hasPolicyToInitiateContractFromTender', $hasPolicyToInitiateContractFromTender)
            ->addColumn('hasPolicyToLoiLoa', $hasPolicyToLoiLoa)
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAwardedFormData(Request $request)
    {
        $tenderId = $request['tenderMasterId'];

        $tenderBidNegotiations = TenderBidNegotiation::select('bid_submission_master_id_new')
            ->where('tender_id', $tenderId)
            ->get();

        if ($tenderBidNegotiations->count() > 0) {
            $bidSubmissionMasterIds = $tenderBidNegotiations->pluck('bid_submission_master_id_new')->toArray();
        } else {
            $bidSubmissionMasterIds = [];
        }

        $getNegotiationCode = TenderMaster::select('negotiation_code')->where('id', $tenderId)->first();

        $tender = TenderMaster::where('id', $tenderId)->with(['ranking_supplier' => function ($q) use($bidSubmissionMasterIds, $getNegotiationCode) {
            if($getNegotiationCode->negotiation_code != '' OR $getNegotiationCode->negotiation_code != null){
                $q->whereIn('bid_id', $bidSubmissionMasterIds);
            }
            $q->where('award', 1)->with([
                'supplier' => function ($supplierQuery) {
                    $supplierQuery->with([
                        'supplier' => function ($masterQuery) {
                            $masterQuery->select('supplierCodeSystem', 'approvedYN', 'supplierConfirmedYN', 'isActive');
                        }
                    ]);
                }
            ]);
        }])->first();

        return $this->sendResponse($tender, 'data retrieved successfully');
    }

    public function confirmFinalBidAwardComment(Request $request)
    {


        DB::beginTransaction();
        try {
            $tenderId = $request['tender_id'];
            $status = $request['final_tender_comment_status'];
            $comment = $request['final_tender_award_comment'];
            $emails = SrmTenderBidEmployeeDetails::where('tender_id', $tenderId)->with('employee')->get();

            $redirectUrl =  $this->checkDomain($tenderId);

            $tender = TenderMaster::find($tenderId);
            $tender->final_tender_award_comment = $comment;
            $tender->final_tender_comment_status = $status;
            $tender->save();

            foreach ($emails as $mail) {
                if(($mail->employee->discharegedYN == 0) && ($mail->employee->ActivationFlag == -1) && ($mail->employee->empLoginActive == 1) && ($mail->employee->empActive == 1)){
                    $name = $mail->employee->empFullName;
                    $documentType = ($tender->document_type == 0) ? 'Tender' : 'RFX';
                    $body = "Hi $name , <br><br> The $documentType $tender->tender_code has been available for the final employee committee approval for $documentType awarding. <br><br> <a href=$redirectUrl>Click here to approve</a> <br><br>Thank you.";
                    $dataEmail['empEmail'] = $mail->employee->empUserName;
                    $dataEmail['companySystemID'] = $request['companySystemID'];
                    $dataEmail['alertMessage'] = "Employee Committee Approval";
                    $dataEmail['emailAlertMessage'] = $body;
                    $sendEmail = \Email::sendEmailErp($dataEmail);
                }
            }

            DB::commit();
            return $this->sendResponse($tender, 'successfully confirmed');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
    }

    public static function checkDomain($id)
    {

        $redirectUrl =  env("APP_URL");
        $url = $_SERVER['HTTP_HOST'];
        if (env('IS_MULTI_TENANCY') == true) {


            $url_array = explode('.', $url);
            $subDomain = $url_array[0];

            //$tenantDomain = (isset(explode('-', $subDomain)[0])) ? explode('-', $subDomain)[0] : "";

            $search = '*';
            $redirectUrl = str_replace($search, $subDomain, $redirectUrl);
        }

        return $redirectUrl;
    }

    public function sendTenderAwardEmail(Request $request)
    {

        DB::beginTransaction();
        try {
            $tenderId = $request['tender_id'];

            // Get Negotiated Bid list
            $tenderBidNegotiations = TenderBidNegotiation::select('bid_submission_master_id_new')
                ->where('tender_id', $tenderId)
                ->get();

            if ($tenderBidNegotiations->count() > 0) {
                $bidSubmissionMasterIds = $tenderBidNegotiations->pluck('bid_submission_master_id_new')->toArray();
            } else {
                $bidSubmissionMasterIds = [];
            }

            $getNegotiationCode = TenderMaster::select('negotiation_code')->where('id', $tenderId)->first();

            $tender = TenderMaster::where('id', $tenderId)->with(['ranking_supplier' => function ($q) use($bidSubmissionMasterIds, $getNegotiationCode) {
                if($getNegotiationCode->negotiation_code != '' OR $getNegotiationCode->negotiation_code != null){
                    $q->whereIn('bid_id', $bidSubmissionMasterIds);
                }
                $q->where('award', 1)->with('supplier');
            }, 'company'])->first();


            $tender->final_tender_award_email = 1;
            $tender->save();

            //Get the Custom email Template
            $file = array();
            $tenderCustomEmail = TenderCustomEmail::getSupplierCustomEmailBody($tenderId, $tender->ranking_supplier->supplier->id, 'TAE');
            if ($tenderCustomEmail && $tenderCustomEmail->attachment) {
                $file[$tenderCustomEmail->attachment->originalFileName] = Helper::getFileUrlFromS3($tenderCustomEmail->attachment->path);
            }
            $name = $tender->ranking_supplier->supplier->name;
            $company = $tender->company->CompanyName;
            $currency = $tender->currency->CurrencyName;
            $bid_submision_date = \Carbon\Carbon::parse($tender->ranking_supplier->bid_submission_master->bidSubmittedDatetime)->format('d/m/Y');
            $finalcommercialprice = $tender->ranking_supplier->bid_submission_master->line_item_total;
            $documentType = $this->getDocumentType($tender->document_type);
            $dataEmail['ccEmail'] = [];
            $dataEmail['attachmentList'] = [];
            if ($tenderCustomEmail) {
                $body =  "<p>Hi " . $name . $tenderCustomEmail->email_body . $company . '</p>';
                $ccEmails = json_decode($tenderCustomEmail->cc_email, true);
            } else {
                $body = "Hi $name, <br><br> Based on your final revised proposal submitted on $bid_submision_date, we would like to inform you that we intend to award your company the $tender->tender_code | $tender->title $documentType for <b>$finalcommercialprice</b> $currency with all agreed conditions.
                    <br>We are looking forward to complete the tasks within the time frame that mentioned in the latest proposal. 
                    <br><br> Regards,<br>$company.";
            }
            $dataEmail['empEmail'] = $tender->ranking_supplier->supplier->email;
            $dataEmail['companySystemID'] = $tender->company_id;
            $dataEmail['alertMessage'] = ($tenderCustomEmail && $tenderCustomEmail->email_subject) ? $tenderCustomEmail->email_subject : "Letter of Awarding | $tender->tender_code | $tender->title";
            $dataEmail['emailAlertMessage'] = $body;

            if (!empty($ccEmails)) {
                $dataEmail['ccEmail'] = $ccEmails;
            }

            if (!empty($tenderCustomEmail->attachment)) {
                $dataEmail['attachmentList'] = $file;
            }

            $sendEmail = \Email::sendEmailSRM($dataEmail);

            $bidSubmittedSuppliers = BidSubmissionMaster::select('supplier_registration_id')
                ->where('tender_id', $tenderId)
                ->where('supplier_registration_id', '!=', $tender->ranking_supplier->supplier->id)
                ->groupBy('supplier_registration_id')
                ->get()
                ->pluck('supplier_registration_id')
                ->toArray();

            $supplierDetails = SupplierRegistrationLink::select('id', 'name', 'email')->whereIn('id', $bidSubmittedSuppliers)->get();

            if (sizeof($supplierDetails) > 0 && $tender->document_type === 0) {
                foreach ($supplierDetails as $bid) {
                    $name = $bid->name;
                    $company = $tender->company->CompanyName;
                    $documentType = $this->getDocumentType($tender->document_type);
                    $body = "Hi $name <br><br> Thank you for your participation in our tender process. We appreciate the effort and time you invested in your proposal. After careful consideration, we regret to inform you that your bid has not been selected for award.  <br><br>  We received several competitive proposals, making our decision a challenging one. We hope for future opportunities to collaborate. <br><br> Thank you once again for your interest in working with us. <br><br> Best Regards,<br>$company.";
                    $dataEmail['empEmail'] = $bid->email;
                    $dataEmail['companySystemID'] = $tender->company_id;
                    $dataEmail['alertMessage'] = "$documentType Regret";
                    $dataEmail['emailAlertMessage'] = $body;
                    $dataEmail['attachmentList'] = [];
                    $dataEmail['ccEmail'] = [];
                    $sendEmail = \Email::sendEmailErp($dataEmail);
                }
            }

            DB::commit();
            return $this->sendResponse($tender, 'Email Send successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
    }

    public function getTenderEditMasterApproval(Request $request)
    {
        $input = $request->all();
        $rfx = false;

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $documentID = $request->documentID;
        $rollOver = $documentID == 117?'RollLevForApp_curr':'confirmation_RollLevForApp_curr';
        $approved = $documentID == 117?'document_modify_request.approved':'document_modify_request.confirmation_approved';
        $versionId = $documentID == 117?'srm_tender_master.tender_edit_version_id':'srm_tender_master.tender_edit_confirm_id';
        $empID = \Helper::getEmployeeSystemID();
        if (isset($input['rfx'])) {
            $rfx = $input['rfx'];
        }

        $poMasters = DB::table('erp_documentapproved')->select(
            'srm_tender_master.id',
            'srm_tender_master.tender_code',
            'document_modify_request.document_master_id as document_system_id',
            'srm_tender_master.title',
            'srm_tender_master.description',
            'srm_tender_master.estimated_value',
            'srm_tender_master.bid_submission_opening_date',
            'srm_tender_master.bid_submission_closing_date',
            'srm_tender_master.created_at',
            'srm_tender_master.confirmed_date',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            // 'currencymaster.CurrencyCode',
            'approvalLevelID',
            'erp_documentapproved.documentSystemCode',
            'employees.empName As created_user',
            'document_modify_request.type',
            'document_modify_request.description as modifyRequestDescription'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $rfx) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                /*->on('erp_documentapproved.departmentSystemID', '=', 'employeesdepartments.departmentSystemID')*/
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            if ($rfx) {
                $query->where('employeesdepartments.documentSystemID', 113);
            } else {
                $query->where('employeesdepartments.documentSystemID', 108);
            }
            $query->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('document_modify_request', function ($query) use ($companyID, $empID, $rfx,$rollOver,$approved) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                ->on('erp_documentapproved.rollLevelOrder', '=', $rollOver)
                ->where('document_modify_request.companySystemID', $companyID)
                ->where($approved, 0);
        })->leftJoin('srm_tender_master', 'srm_tender_master.id', '=', 'document_modify_request.documentSystemCode');


        $poMasters = $poMasters->where('erp_documentapproved.approvedYN', 0)
            // ->join('currencymaster', 'currency_id', '=', 'currencyID')
            ->join('employees', 'document_modify_request.requested_employeeSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.documentSystemID', $documentID)
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.companySystemID', $companyID);


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $poMasters = $poMasters->where(function ($query) use ($search) {
                $query->where('tender_code', 'LIKE', "%{$search}%")
                    ->orWhere('srm_tender_master.description', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $purchaseRequests = [];
        }

        return \DataTables::of($poMasters)
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
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    public function getTenderEditMasterFullApproved(Request $request)
    {
        $input = $request->all();
        $rfx = false;

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $documentID = $request->documentID;
        $rollOver = $documentID == 117?'RollLevForApp_curr':'confirmation_RollLevForApp_curr';
        $approved = $documentID == 117?'document_modify_request.approved':'document_modify_request.confirmation_approved';
        $versionId = $documentID == 117?'srm_tender_master.tender_edit_version_id':'srm_tender_master.tender_edit_confirm_id';
        $empID = \Helper::getEmployeeSystemID();
        if (isset($input['rfx'])) {
            $rfx = $input['rfx'];
        }

        $poMasters = DB::table('erp_documentapproved')->select(
            'srm_tender_master.id',
            'srm_tender_master.tender_code',
            'document_modify_request.document_master_id as document_system_id',
            'srm_tender_master.title',
            'srm_tender_master.description',
            'srm_tender_master.estimated_value',
            'srm_tender_master.bid_submission_opening_date',
            'srm_tender_master.bid_submission_closing_date',
            'srm_tender_master.created_at',
            'srm_tender_master.confirmed_date',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            // 'currencymaster.CurrencyCode',
            'approvalLevelID',
            'erp_documentapproved.documentSystemCode',
            'employees.empName As created_user',
            'document_modify_request.type',
            'document_modify_request.description as modifyRequestDescription'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $rfx) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                /*->on('erp_documentapproved.departmentSystemID', '=', 'employeesdepartments.departmentSystemID')*/
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

            if ($rfx) {
                $query->where('employeesdepartments.documentSystemID', 113);
            } else {
                $query->where('employeesdepartments.documentSystemID', 108);
            }

            $query->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('document_modify_request', function ($query) use ($companyID, $empID, $rfx,$approved) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                ->where('document_modify_request.companySystemID', $companyID)
                ->where($approved, -1);
        })->leftJoin('srm_tender_master', 'srm_tender_master.id', '=', 'document_modify_request.documentSystemCode');


        $poMasters = $poMasters->where('erp_documentapproved.approvedYN', -1)
            // ->join('currencymaster', 'currency_id', '=', 'currencyID')
            ->join('employees', 'document_modify_request.requested_employeeSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.documentSystemID', $documentID)
            ->where('erp_documentapproved.companySystemID', $companyID);


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $poMasters = $poMasters->where(function ($query) use ($search) {
                $query->where('tender_code', 'LIKE', "%{$search}%")
                    ->orWhere('srm_tender_master.description', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $purchaseRequests = [];
        }

        return \DataTables::of($poMasters)
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
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    public function startTenderNegotiation(Request $request) {

        DB::beginTransaction();
        try {
            $tenderId = $request['tenderMasterId'];
            $tender = TenderMaster::where('id',$tenderId)->select('is_negotiation_started')->first();
            $tender->is_negotiation_started = 1;
            $tender->save();

            DB::commit();
            return $this->sendResponse($tender, 'Tender negotiation started successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
    }

    public function closeTenderNegotiation(Request $request) {

        DB::beginTransaction();
        try {
            $tenderId = $request['srm_tender_master_id'];
            TenderMaster::where('id', $tenderId)->update(['is_negotiation_closed' => 1, 'negotiation_is_awarded' => 1]);
            DB::commit();
            return $this->sendResponse('success', 'Tender negotiation closed successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
    }

    public function getNegotiationStartedTenderList(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $query = TenderNegotiation::select('srm_tender_master_id','status','approved_yn','confirmed_yn','comments','started_by','no_to_approve','currencyId','id')
            ->whereHas('tenderMaster', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->with(['area' => function ($query)  use ($input) {
                $query->select('pricing_schedule','technical_evaluation','tender_documents','id','tender_negotiation_id');
            },'tenderMaster' => function ($q) use ($input){
                $q->select('title', 'uuid', 'description','currency_id','envelop_type_id','tender_code','stage','bid_opening_date','technical_bid_opening_date','commerical_bid_opening_date','tender_type_id','id', 'is_negotiation_closed');
                $q->with(['currency' => function ($c) use ($input) {
                    $c->select('CurrencyName','currencyID','CurrencyCode');
                },'tender_type' => function ($t) {
                    $t->select('id','name','description');
                },'envelop_type' => function ($e) {
                    $e->select('id','name','description');
                }]);
            }]);

        if (array_key_exists('tenderNegotiationSatus', $input) && isset($input['tenderNegotiationSatus'])) {
            if ($input['tenderNegotiationSatus'] == 3) {
                $query->whereHas('tenderMaster', function ($q) {
                    $q->where('is_negotiation_closed', 1);
                });
            } else {
                $query->where('status', $input['tenderNegotiationSatus']);
                $query->whereHas('tenderMaster', function ($q) {
                    $q->where('is_negotiation_closed', 0);
                });
            }
        }


        if (array_key_exists('currencyId', $input) && isset($input['currencyId'])) {
            $query->whereIN('currencyId',collect($input['currencyId'])->pluck('id')->toArray())->select('srm_tender_master_id','status','approved_yn','id','confirmed_yn');

        }




        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($a) use ($search) {
                $a->orWhereHas('tenderMaster', function ($b) use ($search) {
                    $b->where('title', 'LIKE', "%{$search}%");
                    $b->orWhere('description', 'LIKE', "%{$search}%");
                    $b->orWhere('tender_code', 'LIKE', "%{$search}%");
                });
            });
        }


        return \DataTables::eloquent($query)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getTenderFilterData(Request $request){
        return $this->tenderMasterRepository->getTenderFilterData($request);
    }

    public function getFilterData($input)
    {
        $currencyId = !empty($input['filters']['currencyId']) ? $input['filters']['currencyId'] : null;
        $currencyId = (array)$currencyId;
        $currencyId = collect($currencyId)->pluck('id');

        $combinedRankingStatus = !empty($input['filters']['combined_ranking_status']) ? $input['filters']['combined_ranking_status'] : null;
        $combinedRankingStatus = (array)$combinedRankingStatus;
        $combinedRankingStatus = collect($combinedRankingStatus)->pluck('id');

        $tenderAwardingStatus = !empty($input['filters']['final_tender_awarded']) ? $input['filters']['final_tender_awarded'] : null;
        $tenderAwardingStatus = (array)$tenderAwardingStatus;
        $tenderAwardingStatus = collect($tenderAwardingStatus)->pluck('id');

        $selection = !empty($input['filters']['selection']) ? $input['filters']['selection'] : null;
        $envelope = !empty($input['filters']['envelope']) ? $input['filters']['envelope'] : null;
        $published = !empty($input['filters']['publish']) ? $input['filters']['publish']: null;
        $status = !empty($input['filters']['status']) ? $input['filters']['status']: null;
        $rfxType = !empty($input['filters']['type']) ? $input['filters']['type']: null;
        $gonogo = !empty($input['filters']['gonogo']) ? $input['filters']['gonogo']: null;
        $technical = !empty($input['filters']['technical']) ? $input['filters']['technical']: null;
        $stage = !empty($input['filters']['stage']) ? $input['filters']['stage']: null;
        $commercial = !empty($input['filters']['commercial']) ? $input['filters']['commercial']: null;
        $tenderNegotiationStatus = !empty($input['filters']['tenderNegotiationStatus']) ? $input['filters']['tenderNegotiationStatus']: null;

        $filters = [
            'currencyId' => $currencyId,
            'selection' => $selection,
            'envelope' => $envelope,
            'published' => $published,
            'status' => $status,
            'rfxType' => $rfxType,
            'gonogo' => $gonogo,
            'technical' => $technical,
            'stage' => $stage,
            'commercial' => $commercial,
            'tenderNegotiationStatus' => $tenderNegotiationStatus,
            'combinedRankingStatus' => $combinedRankingStatus,
            'tenderAwardingStatus' => $tenderAwardingStatus
        ];

        return $filters;
    }

    public function approveBidOpening(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $id = $input['id'];
            $data['technical_eval_status'] = 1;
            $data['go_no_go_status'] = 1;
            $data['doc_verifiy_status'] = 1;

            $bid_status['doc_verifiy_status'] = 1;

            TenderMaster::where('id', $id)->update($data);
            BidSubmissionMaster::where('tender_id', $id)->update($bid_status);
            DB::commit();
            return ['success' => true, 'message' => 'Successfully updated'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getTechnicalCount($tenderId){
        return TenderMaster::select('id')->withCount(['criteriaDetails',
            'criteriaDetails AS technical_count' => function ($query) {
                $query->where('critera_type_id', 2);
            }])->where('id', $tenderId)->first();
    }

    public function getDocumentType($documentType){
        switch($documentType){
            case 0 :
                return 'Tender';
                break;
            case 1:
                return 'Quotation';
                break;
            case 2:
                return 'Information';
                break;
            case 3:
                return 'Proposal';
                break;
                return 'Tender';
            default:
        }
    }

    public function getTenderPr(Request $request){
        return $this->tenderMasterRepository->getTenderPr($request);
    }

    public function getPurchaseRequestDetails(Request $request)
    {
        return $this->tenderMasterRepository->getPurchaseRequestDetails($request);
    }

    public function getTenderNegotiationList(Request $request)
    {
        $input = $request->all();
        $userId = \Helper::getEmployeeSystemID();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $filters = $this->getFilterData($input);

        $query = TenderMaster::with(['currency', 'tender_type', 'envelop_type', 'srmTenderMasterSupplier','tenderUserAccess'=> function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->where('module_id',1);
        },'tenderBidMinimumApproval'=> function ($q1) use ($userId) {
            $q1->where('emp_id',$userId);
        },   'tender_negotiation' => function ($q2) {
            $q2->select('srm_tender_master_id')
                ->selectSub(function ($query) {
                    $query->from('tender_negotiations as tn')
                        ->selectRaw('MAX(version)')
                        ->whereColumn('tn.srm_tender_master_id', 'tender_negotiations.srm_tender_master_id');
                }, 'version');
        }])
            ->where('is_negotiation_started',1)
            ->where('negotiation_published',1)
            ->where('company_id',$companyId)
            ->withCount(['criteriaDetails',
                'criteriaDetails AS go_no_go_count' => function ($query) {
                    $query->where('critera_type_id', 1);
                },
                'criteriaDetails AS technical_count' => function ($query) {
                    $query->where('critera_type_id', 2);
                },
                'srm_bid_submission_master AS technical_eval_negotiation' => function ($query2) {
                    $query2->with(['TenderBidNegotiation' => function ($q){
                        $q->where('technical_verify_status',0);
                    }])
                        ->whereHas('TenderBidNegotiation',function ($q2){
                            $q2->where('technical_verify_status',0);
                        });
                }
            ])
            ->where(function ($query) use ($userId) {
                $query->whereHas('tenderUserAccess', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->where('module_id',1);
                })
                    ->orWhereHas('tenderBidMinimumApproval', function ($q1) use ($userId) {
                        $q1->where('emp_id', $userId);
                    });
            })
            ->whereHas('srmTenderMasterSupplier')->where('published_yn', 1);


        if ($filters['currencyId'] && count($filters['currencyId']) > 0) {
            $query->whereIn('currency_id', $filters['currencyId']);
        }

        if ($filters['selection']) {
            $query->where('tender_type_id', $filters['selection']);
        }

        if ($filters['envelope']) {
            $query->where('envelop_type_id', $filters['envelope']);
        }

        if ($filters['gonogo']) {
            $gonogo =  ($filters['gonogo'] == 1 ) ? 0 :1;
            $query->where('go_no_go_status', $gonogo);
        }

        if ($filters['technical']) {
            $ids = array_column($filters['technical'], 'id');
            $query->where(function ($query) use ($ids) {
                if (in_array(1, $ids)) {
                    $query->orWhere(function ($query) {
                        $query->whereDoesntHave('srm_bid_submission_master', function ($q) {
                            $q->where('technical_verify_status', 0)->whereHas('TenderBidNegotiation');
                        });
                    });
                }

                if (in_array(0, $ids)) {
                    $query->orWhere(function ($query) {
                        $query->whereHas('srm_bid_submission_master', function ($q) {
                            $q->where('technical_verify_status', 0)->whereHas('TenderBidNegotiation');
                        });
                    });
                }
            });
        }

        if ($filters['stage']) {
            $query->where('stage', $filters['stage']);
        }

        // return $this->sendResponse($query, 'Tender Masters retrieved successfully');

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('tender_code', 'LIKE', "%{$search}%")
                    ->orWhere('negotiation_code', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('description_sec_lang', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%")
                    ->orWhere('title_sec_lang', 'LIKE', "%{$search}%")
                    ->orWhereHas('envelop_type', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('currency', function ($query1) use ($search) {
                        $query1->where('CurrencyName', 'LIKE', "%{$search}%");
                        $query1->orWhere('CurrencyCode', 'LIKE', "%{$search}%");
                    });
            });
        }


        return \DataTables::eloquent($query)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getTenderBidOpeningReport(Request $request)
    {
        $tenderId = $request->get('id');
        $employeeID = $request->get('userID');
        $companyId = $request->get('companySystemID');
        $isNegotiation = $request->get('isNegotiation');

        $tenderBidNegotiations = TenderBidNegotiation::select('bid_submission_master_id_new')
            ->where('tender_id', $tenderId)
            ->get();

        $bidSubmissionMasterIds = [];

        if ($tenderBidNegotiations->count() > 0) {
            $bidSubmissionMasterIds = $tenderBidNegotiations->pluck('bid_submission_master_id_new')->toArray();
        }

        $getNegotiationCode = TenderMaster::select('negotiation_code')->where('id', $tenderId)->first();

        $tenderMaster = TenderMaster::where('id', $tenderId)->with(['ranking_supplier' => function ($q) use($bidSubmissionMasterIds, $getNegotiationCode) {
            if($getNegotiationCode->negotiation_code != '' OR $getNegotiationCode->negotiation_code != null){
                $q->whereIn('bid_id', $bidSubmissionMasterIds);
            }
            $q->where('award', 1)->with('supplier');
        }])->first();

        // Get Bids Count
        $query = BidSubmissionMaster::where('status', 1)->where('bidSubmittedYN', 1)->where('tender_id', $tenderId);

        if ($isNegotiation == 1) {
            $query = $query->whereIn('id', $bidSubmissionMasterIds);
        } else {
            $query = $query->whereNotIn('id', $bidSubmissionMasterIds);
        }
        $tenderBids =  $query->count();

        $tenderBidsSupplierList = $this->getTenderBitsSupplierNameList($companyId, $tenderId, 1, $isNegotiation, $bidSubmissionMasterIds);
        $employeeDetails = SrmTenderBidEmployeeDetails::where('tender_id', $tenderId)->with('employee')->get();

        $company = Company::where('companySystemID', $tenderMaster->company_id)->first();

        $SrmTenderBidEmployeeDetails = SrmTenderBidEmployeeDetails::with('employee')
            ->where('tender_id', $tenderId)
            ->get();

        $employeeData = Employee::where('employeeSystemID', $employeeID)->first();

        $time = strtotime("now");
        $fileName = 'Minutes_of_Bid_Opening' . $time . '.pdf';
        $order = array('tenderMaster' => $tenderMaster, 'employeeDetails' => $employeeDetails, 'company' => $company, 'employeeData' => $employeeData, 'tenderBids' => $tenderBids,
            'isNegotiation' => $isNegotiation,
            'tenderBidsSupplierList' => $tenderBidsSupplierList,
            'SrmTenderBidEmployeeDetails' => $SrmTenderBidEmployeeDetails);
        $html = view('print.minutes_of_bid_opening_print', $order);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream($fileName);

    }

    public function getTenderBitsSupplierNameList($companyId, $tenderId, $loadSupplier, $isNegotiation, $bidSubmissionMasterIds)
    {
        $query = BidSubmissionMaster::with(['SupplierRegistrationLink', 'bidSubmissionDetail' => function($query){
            $query->whereHas('srm_evaluation_criteria_details.evaluation_criteria_type', function ($query) {
                $query->where('id', 1);
            });
        }])->withCount(['documents'=>function($q) use ($companyId){
            $q->where('companySystemID',$companyId)
                ->where('documentSystemID', 113)
                ->where('attachmentType',2)
                ->where('envelopType',3);
        }])->where('status', 1)->where('bidSubmittedYN', 1)->where('tender_id', $tenderId);

        if ($isNegotiation == 1) {
            $query = $query->whereIn('id', $bidSubmissionMasterIds);
        } else {
            $query = $query->whereNotIn('id', $bidSubmissionMasterIds);
        }

        if( isset($loadSupplier) && $loadSupplier ){
            $query = $query->groupBy('srm_bid_submission_master.supplier_registration_id');
        }

        return $query->get();

    }

    public function getTenderPurchaseList(Request $request)
    {
        $input = $request->all();
        $tenderMasterId = $input['tenderMasterId'];
        $type = isset($input['type']) ? 'count' : 'list';
        $isTender = isset($input['isTender']) ? $input['isTender'] : false;

        $negotiatedSuppliers = $this->getTenderNegotiations($tenderMasterId);
        $supplierMasterIds = $this->negotiatedSupplierIdList($negotiatedSuppliers) ;


        $documentType = ($isTender) ? [0] : [1,2,3];

        $tenderPurchaseList = TenderMasterSupplier::select('id','tender_master_id','purchased_by', 'purchased_date')
            ->with(['supplierDetails'])
            ->with(['tender_master' => function ($q) use ($tenderMasterId,$documentType){
                $q->select('id','title')
                    ->whereIn('document_type',$documentType);
            }])
            ->whereHas('supplierDetails', function($q2) use ($tenderMasterId,$documentType){
            })

            ->where('tender_master_id',$tenderMasterId);

        if($type == 'count'){
            return !empty($tenderPurchaseList->get()->count()) || ($tenderPurchaseList->count() > 0)  ? $tenderPurchaseList->get()->count() : 0;
        }

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $tenderPurchaseList = $tenderPurchaseList->whereHas('supplierDetails', function ($query) use ($search){
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        return \DataTables::of($tenderPurchaseList)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getTenderNegotiations($tenderMasterId){
        return TenderNegotiation::select('id','srm_tender_master_id')
            ->with(['SupplierTenderNegotiationList' => function ($q){
                $q->select('tender_negotiation_id','suppliermaster_id');
            }])
            ->where('srm_tender_master_id',$tenderMasterId)
            ->where('status',2)
            ->get();
    }

    public function negotiatedSupplierIdList($negotiatedSuppliers){
        return $negotiatedSuppliers->pluck('SupplierTenderNegotiationList')
            ->flatten()
            ->pluck('suppliermaster_id')
            ->toArray();
    }

    public function referBackTenderMaster(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $tenderMasterId = $input['tenderMasterId'];

            $tenderMaster = $this->tenderMasterRepository->findWithoutFail($tenderMasterId);
            if (empty($tenderMaster)) {
                return $this->sendError('Tender Master not found');
            }

            if ($tenderMaster->refferedBackYN != -1) {
                return $this->sendError('You cannot amend this document');
            }


            $tenderMasterArray = $tenderMaster->toArray();
            $tenderMasterArray = collect($tenderMasterArray)->except(['document_sales_start_time', 'document_sales_end_time','pre_bid_clarification_start_time'
                ,'pre_bid_clarification_end_time','site_visit_start_time','site_visit_end_time','bid_submission_opening_time','bid_submission_closing_time'
                ,'bid_opening_date_time','bid_opening_end_date_time','technical_bid_opening_date_time','technical_bid_closing_date_time'
                ,'commerical_bid_opening_date_time','commerical_bid_closing_date_time'])->toArray();

            // $storeTenderMasterHistory = TenderMasterReferred::insert($tenderMasterArray);

            $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $tenderMasterId)
                ->where('companySystemID', $tenderMaster->company_id)
                ->where('documentSystemID', $tenderMaster->document_system_id)
                ->get();

            if (!empty($fetchDocumentApproved)) {
                foreach ($fetchDocumentApproved as $DocumentApproved) {
                    $DocumentApproved['refTimes'] = $tenderMaster->timesReferred;
                }
            }

            $DocumentApprovedArray = $fetchDocumentApproved->toArray();

            $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

            $deleteApproval = DocumentApproved::where('documentSystemCode', $tenderMasterId)
                ->where('companySystemID', $tenderMaster->company_id)
                ->where('documentSystemID', $tenderMaster->document_system_id)
                ->delete();

            if ($deleteApproval) {
                $tenderMaster->refferedBackYN = 0;
                $tenderMaster->confirmed_yn = 0;
                $tenderMaster->confirmed_by_emp_system_id = null;
                $tenderMaster->confirmed_by_name = null;
                $tenderMaster->confirmed_date = null;
                $tenderMaster->RollLevForApp_curr = 1;
                $tenderMaster->save();
            }

            DB::commit();
            return $this->sendResponse($tenderMaster->toArray(), 'Tender amended successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getTenderAmendHistory(Request $request){
        $input = $request->all();

        $tenderAmendHistory = TenderMasterReferred::with(['currency', 'tender_type', 'envelop_type','createdBy'])
            ->where('id',$input['tenderMasterId'])
            ->get();

        return $this->sendResponse($tenderAmendHistory, 'Tender Master retrieved successfully');
    }
    public function getTenderRfxAudit(Request $request){
        $input = $request->all();

        $id = $input['id'];
        $documentId = $input['documentId'];

        $data['tenderMaster'] = $this->tenderMasterRepository
            ->with(['createdBy', 'confirmed_by', 'modifiedBy', 'approvedBy' => function ($query) use ($documentId) {
                $query->with('employee')
                    ->where('documentSystemID', $documentId);
            }])
            ->findWithoutFail($id);

        $data['docModifiyMaster'] = DocumentModifyRequest::select('id','description','refferedBackYN','approved')
            ->where('confirm',0)
            ->where('modify_type',1)
            ->where('documentSystemCode',$id)
            ->where('requested_document_master_id',$documentId)
            ->orderBy('id', 'desc')
            ->first();

        $data['docModifyApprovedData'] = DocumentApproved::select('documentApprovedID','companySystemID','documentSystemID','documentSystemCode','approvedComments',
            'employeeSystemID','approvedDate','rejectedYN','approvedYN','rejectedDate','rejectedComments')
            ->with(['employee' => function ($q){
                $q->select('employeeSystemID','empFullName');
            }])
            ->where('documentSystemCode',$data['docModifiyMaster']['id'])
            ->whereIn('documentSystemID',[117,118])
            ->get();


        $data['rejectedHistory'] = DocumentReferedHistory::select('documentReferedID','documentApprovedID','companySystemID','documentSystemID','documentSystemCode',
            'employeeSystemID','rejectedYN','rejectedDate','rejectedComments')
            ->with(['employee' => function ($q){
                $q->select('employeeSystemID','empFullName');
            }])
            ->where('documentSystemID',$documentId)
            ->where('documentSystemCode',$id)
            ->where('rejectedYN',-1)
            ->get();

        $data['modifyRequestList'] = DocumentModifyRequest::getModificationRequestList($input['id']);

        if (empty($data['tenderMaster'])) {
            return $this->sendError('Tender Master not found');
        }
        return $this->sendResponse($data, 'Tender Master retrieved successfully');
    }

    public function getCompanyTenderList(Request $request){
        $input = $request->all();
        $srmUrl = env("SRM_LINK");
        $tenderUrl = str_replace('/register/', '/tender-management/tenders/', $srmUrl);

        $search = isset($input['search']) ? $input['search'] : '';
        $data = TenderMaster::select('title','title_sec_lang','description','pre_bid_clarification_method','site_visit_date',
            'document_sales_start_date','document_sales_end_date','pre_bid_clarification_start_date','pre_bid_clarification_end_date',
            'pre_bid_clarification_end_date','bid_submission_opening_date','bid_submission_closing_date')
            ->selectRaw('CASE 
                    WHEN pre_bid_clarification_method = 1 THEN "Online"
                    WHEN pre_bid_clarification_method = 0 THEN "Offline"
                    WHEN pre_bid_clarification_method = 2 THEN "Both"
                    ELSE "-"
                END as clarification_method')
            ->where('document_type',0)
            ->where('tender_type_id',1)
            ->where('approved',-1)
            ->where('published_yn',1)
            ->orderBy('id','desc');


        if ($search) {
            $search = str_replace("\\", "\\\\\\\\", $search);
            $data->where(function ($q) use ($search) {
                $q->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('title_sec_lang','like', "%{$search}%")
                    ->orWhere('description','like', "%{$search}%")
                    ->orWhereRaw('CASE 
                                WHEN pre_bid_clarification_method = 1 THEN "Online"
                                WHEN pre_bid_clarification_method = 0 THEN "Offline"
                                WHEN pre_bid_clarification_method = 2 THEN "Both"
                                ELSE "-"
                            END like ?', ['%' . $search . '%']);
            });
        }

        $limit = isset($input['limit']) ? $input['limit'] : 5;
        if($data){
            $data = $data->paginate($limit);
        }


        $companyData = Company::select('CompanyName','logoPath','masterCompanySystemIDReorting', 'CompanyEmail')
            ->orderBy('companySystemID','asc')
            ->first();

        $tenderData['data'] = $data;
        $tenderData['srmLink'] = $tenderUrl;
        $tenderData['companyData'] = $companyData;

        return $tenderData;
    }

    public function getBudgetItemTotalAmount(Request $request){
        $input = $request->all();
        $record = $this->tenderMasterRepository->getBudgetItemTotalAmount($input);
        return $this->sendResponse($record, 'Budget item amount retrieved successfully');
    }


    public function getTenderPOData(Request $request)
    {
        try {
            $result = TenderMasterRepository::getTenderPOData($request['tenderUUID'], $request['companySystemID']);

            return $this->sendResponse($result, 'Success' );
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return $this->sendError($e->getMessage(), $statusCode);
        }
    }


    public function getPaymentProofDocumentApproval(Request $request)
    {
        try
        {
            $data = $this->tenderMasterRepository->getPaymentProofDocumentApproval($request);
            return $data;
        }
        catch(\Exception $e)
        {
            return $this->sendError('Unexpected Error: ' . $e->getMessage());
        }
    }

    public function getSupplierWiseProofNotApproved(Request $request)
    {
        try
        {
            $data = $this->tenderMasterRepository->getSupplierWiseProofNotApproved($request);
            return $data;
        }
        catch(\Exception $e)
        {
            return $this->sendError('Unexpected Error: ' . $e->getMessage());
        }
    }

    public function approveSupplierWiseTender(Request $request)
    {
        try
        {
            $data = $this->tenderMasterRepository->approveSupplierWiseTender($request);
            if(!$data['success']) {
                return $this->sendError($data['message']);
            }
            return $this->sendResponse($data, $data['message']);
        }
        catch(\Exception $e)
        {
            return $this->sendError('Unexpected Error: ' . $e->getMessage());
        }
    }

    public function rejectSupplierWiseTender(Request $request)
    {
        try
        {
            $data = $this->tenderMasterRepository->rejectSupplierWiseTender($request);
            if(!$data['success']) {
                return $this->sendError($data['message']);
            }
            return $this->sendResponse($data, $data['message']);
        }
        catch(\Exception $e)
        {
            return $this->sendError('Unexpected Error: ' . $e->getMessage());
        }
    }

    public function getSupplierWiseProofApproved(Request $request)
    {
        try
        {
            $data = $this->tenderMasterRepository->getSupplierWiseProofApproved($request);
            return $data;
        }
        catch(\Exception $e)
        {
            return $this->sendError('Unexpected Error: ' . $e->getMessage());
        }
    }

    public function updateTenderCalendarDays(UpdateTenderCalendarDaysRequest $request)
    {
        try
        {
            $data = $this->tenderMasterRepository->updateTenderCalendarDays($request);

            if(!$data['success']) {
                return $this->sendError($data['message']);
            }
            return $this->sendResponse($data, $data['message']);
        }
        catch(\Exception $e)
        {
            return $this->sendError('Unexpected Error: ' . $e->getMessage());
        }
    }

    public function getTenderCalendarValidation(Request $request)
    {
        try
        {
            $data = $this->tenderMasterRepository->getTenderCalendarValidation($request);
            return $data;
        }
        catch(\Exception $e)
        {
            return $this->sendError('Unexpected Error: ' . $e->getMessage());
        }
    }

    public function getCalendarDateAuditLogs(Request $request)
    {
        try
        {
            $data = $this->tenderMasterRepository->getCalendarDateAuditLogs($request);
            return $data;
        }
        catch(\Exception $e)
        {
            return $this->sendError('Unexpected Error: ' . $e->getMessage());
        }
    }

    public function getContractTypes(CompanyValidateAPIRequest $request)
    {
        try
        {
            $input = $request->all();
            $companySystemID = $input['companySystemId'];
            $contractTypes = $this->tenderMasterRepository->getContractTypes($companySystemID);
            return $contractTypes;
        }
        catch(\Exception $e)
        {
            return $this->sendError('Unexpected Error: ' . $e->getMessage());
        }
    }

    public function createContract(CreateContractMasterAPIRequest $request)
    {
        try
        {
            $data = $this->tenderMasterRepository->createContract($request);

            if(!$data['success']) {
                return $this->sendError($data['message']);
            }
            return $this->sendResponse($data, $data['message']);
        }
        catch(\Exception $e)
        {
            return $this->sendError('Unexpected Error: ' . $e->getMessage());
        }
    }

    public function viewContract(ViewContractAPIRequest $request)
    {
        try
        {
            $input = $request->all();
            $contractUrl = $this->tenderMasterRepository->viewContract($input);
            return $contractUrl;
        }
        catch(\Exception $e)
        {
            return $this->sendError('Unexpected Error: ' . $e->getMessage());
        }
    }

    public function addAttachment(AddAttachmentAPIRequest $request)
    {
        try
        {
            $data = $this->tenderMasterRepository->addAttachment($request);

            if(!$data['success']) {
                return $this->sendError($data['message']);
            }
            return $this->sendResponse($data, $data['message']);
        }
        catch(\Exception $e)
        {
            return $this->sendError('Unexpected Error: ' . $e->getMessage());
        }
    }

    public function deleteAttachment(DeleteAttachmentAPIRequest $request)
    {
        try
        {
            $data = $this->tenderMasterRepository->deleteAttachment($request);

            if(!$data['success']) {
                return $this->sendError($data['message']);
            }
            return $this->sendResponse($data, $data['message']);
        }
        catch(\Exception $e)
        {
            return $this->sendError('Unexpected Error: ' . $e->getMessage());
        }
    }

    public function getTenderTypeData(Request $request)
    {
        try
        {
            $data = $this->tenderMasterRepository->getTenderTypeData($request);
            return $data;
        }
        catch(\Exception $e)
        {
            return $this->sendError('Unexpected Error: ' . $e->getMessage());
        }
    }

}
