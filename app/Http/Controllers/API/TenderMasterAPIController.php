<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateTenderMasterAPIRequest;
use App\Http\Requests\API\UpdateTenderMasterAPIRequest;
use App\Models\BankAccount;
use App\Models\BankMaster;
use App\Models\CalendarDates;
use App\Models\CalendarDatesDetail;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\EmployeesDepartment;
use App\Models\EnvelopType;
use App\Models\EvaluationCriteriaDetails;
use App\Models\EvaluationType;
use App\Models\PricingScheduleMaster;
use App\Models\ProcumentActivity;
use App\Models\ScheduleBidFormatDetails;
use App\Models\TenderBoqItems;
use App\Models\TenderMainWorks;
use App\Models\TenderMaster;
use App\Models\TenderProcurementCategory;
use App\Models\TenderSiteVisitDates;
use App\Models\TenderType;
use App\Models\YesNoSelection;
use App\Repositories\TenderMasterRepository;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailForQueuing;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Models\TenderSupplierAssignee;
use App\Repositories\SupplierRegistrationLinkRepository;

/**
 * Class TenderMasterController
 * @package App\Http\Controllers\API
 */

class TenderMasterAPIController extends AppBaseController
{
    /** @var  TenderMasterRepository */
    private $tenderMasterRepository;
    private $registrationLinkRepository;
    public function __construct(TenderMasterRepository $tenderMasterRepo, SupplierRegistrationLinkRepository $registrationLinkRepository)
    {
        $this->tenderMasterRepository = $tenderMasterRepo;
        $this->registrationLinkRepository = $registrationLinkRepository;
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
        $tenderMaster = $this->tenderMasterRepository->findWithoutFail($id);

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



        $tenderMaster = TenderMaster::with(['tender_type', 'envelop_type', 'currency'])->where('company_id', $companyId);

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

        if (isset($input['tenderMasterId'])) {
            $tenderMaster = TenderMaster::where('id', $input['tenderMasterId'])->first();

            if (!empty($tenderMaster['procument_cat_id'])) {
                $category = TenderProcurementCategory::where('id', $tenderMaster['procument_cat_id'])->first();
            } else {
                $category['is_active'] = 1;
            }
        }

        $employee = Helper::getEmployeeInfo();
        $company = Helper::companyCurrency($employee->empCompanySystemID);


        $data['tenderType'] = TenderType::get();
        $data['yesNoSelection'] = YesNoSelection::all();
        $data['envelopType'] = EnvelopType::get();
        $data['currency'] = CurrencyMaster::get();
        $data['evaluationTypes'] = EvaluationType::get();
        $data['bank'] = BankMaster::get();
        $data['currentDate'] = now();
        $data['defaultCurrency'] = $company;
        $data['procurementCategory'] = TenderProcurementCategory::where('level', 0)->where('is_active', 1)->get();

        if (isset($input['tenderMasterId'])) {
            if ($tenderMaster['confirmed_yn'] == 1 && $category['is_active'] == 0) {
                $data['procurementCategory'][] = $category;
            }
        }

        return $data;
    }

    public function createTender(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($request->all(), array('currency_id'));
        $employee = \Helper::getEmployeeInfo();
        $exist = TenderMaster::where('title', $input['title'])->where('company_id', $input['companySystemID'])->first();
        if (!empty($exist)) {
            return ['success' => false, 'message' => 'Tender title cannot be duplicated'];
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
        DB::beginTransaction();
        try {
            $data['currency_id'] = isset($input['currency_id']) ? $input['currency_id'] : null;
            $data['description'] = isset($input['description']) ? $input['description'] : null;
            $data['envelop_type_id'] = $input['envelop_type_id'];
            $data['tender_type_id'] = $input['tender_type_id'];
            $data['title'] = $input['title'];
            $data['document_system_id'] = 108;
            $data['document_id'] = $documentMaster['documentID'];
            $data['company_id'] = $input['companySystemID'];
            $data['created_by'] = $employee->employeeSystemID;
            $data['tender_code'] = $tenderCode;
            $data['serial_number'] = $lastSerialNumber;

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
        $tenderMasterId = $input['tenderMasterId'];
        $companySystemID = $input['companySystemID'];
        $data['master'] = TenderMaster::with(['procument_activity', 'confirmed_by'])->where('id', $input['tenderMasterId'])->first();
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

        $qry="SELECT
	srm_calendar_dates.id as id,
	srm_calendar_dates.calendar_date as calendar_date,
	srm_calendar_dates.company_id as company_id,
	srm_calendar_dates_detail.from_date as from_date,
	srm_calendar_dates_detail.to_date as to_date
FROM
	srm_calendar_dates 
	LEFT JOIN srm_calendar_dates_detail ON srm_calendar_dates_detail.calendar_date_id = srm_calendar_dates.id AND srm_calendar_dates_detail.tender_id = $tenderMasterId
WHERE
	srm_calendar_dates.company_id = $companySystemID";


        $data['calendarDates'] = DB::select($qry);

        return $data;
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
        $input = $this->convertArrayToSelectedValue($request->all(), array('bank_account_id', 'bank_id', 'currency_id', 'currency_id', 'envelop_type_id', 'evaluation_type_id', 'procument_cat_id', 'procument_sub_cat_id', 'tender_type_id'));


        $resValidate = $this->validateTenderHeader($input);

        if (!$resValidate['status']) {
            return $this->sendError($resValidate['message'], 422);
        }
        $document_sales_start_date = new Carbon($input['document_sales_start_date']);
        $document_sales_start_date = $document_sales_start_date->format('Y-m-d');

        $document_sales_end_date = new Carbon($input['document_sales_end_date']);
        $document_sales_end_date = $document_sales_end_date->format('Y-m-d');

        $bid_submission_opening_date = new Carbon($input['bid_submission_opening_date']);
        $bid_submission_opening_date = $bid_submission_opening_date->format('Y-m-d');

        $bid_submission_closing_date = new Carbon($input['bid_submission_closing_date']);
        $bid_submission_closing_date = $bid_submission_closing_date->format('Y-m-d');

        $pre_bid_clarification_start_date = new Carbon($input['pre_bid_clarification_start_date']);
        $pre_bid_clarification_start_date = $pre_bid_clarification_start_date->format('Y-m-d');

        $pre_bid_clarification_end_date = new Carbon($input['pre_bid_clarification_end_date']);
        $pre_bid_clarification_end_date = $pre_bid_clarification_end_date->format('Y-m-d');
        $site_visit_date = null;
        if ($input['site_visit_date']) {
            $site_visit_date = new Carbon($input['site_visit_date']);
            $site_visit_date = $site_visit_date->format('Y-m-d');
        }

        if ($input['site_visit_end_date']) {
            $site_visit_end_date = new Carbon($input['site_visit_end_date']);
            $site_visit_end_date = $site_visit_end_date->format('Y-m-d');
        }


        if ($document_sales_start_date > $document_sales_end_date) {
            return ['success' => false, 'message' => 'Document sales from date cannot be greater than Document sales to date'];
        }

        if ($pre_bid_clarification_start_date > $pre_bid_clarification_end_date) {
            return ['success' => false, 'message' => 'Pre-bid clarification from date cannot be greater than Pre-bid clarification to date'];
        }

        if ($bid_submission_opening_date > $bid_submission_closing_date) {
            return ['success' => false, 'message' => 'Bid submission from date cannot be greater than Bid submission to date'];
        }

        if ($site_visit_date > $site_visit_end_date) {
            return ['success' => false, 'message' => 'Site Visit from date cannot be greater than Site Visit to date'];
        }

        $existTndr = TenderMaster::where('title', $input['title'])->where('id', '!=', $input['id'])->where('company_id', $input['companySystemID'])->first();
        if (!empty($existTndr)) {
            return ['success' => false, 'message' => 'Tender title cannot be duplicated'];
        }

        $employee = \Helper::getEmployeeInfo();
        $exist = TenderMaster::where('id', $input['id'])->first();
        DB::beginTransaction();
        try {

            $data['title'] = $input['title'];
            $data['title_sec_lang'] = $input['title_sec_lang'];
            $data['description'] = $input['description'];
            $data['description_sec_lang'] = $input['description_sec_lang'];
            $data['tender_type_id'] = $input['tender_type_id'];
            $data['currency_id'] = $input['currency_id'];
            $data['envelop_type_id'] = $input['envelop_type_id'];
            $data['procument_cat_id'] = $input['procument_cat_id'];
            $data['procument_sub_cat_id'] = $input['procument_sub_cat_id'];
            $data['evaluation_type_id'] = $input['evaluation_type_id'];
            $data['estimated_value'] = $input['estimated_value'];
            $data['allocated_budget'] = $input['allocated_budget'];
            $data['tender_document_fee'] = $input['tender_document_fee'];
            $data['bank_id'] = $input['bank_id'];
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
            $data['updated_by'] = $employee->employeeSystemID;

            $result = TenderMaster::where('id', $input['id'])->update($data);

            if ($result) {
                if (isset($input['procument_activity'])) {
                    if (count($input['procument_activity']) > 0) {
                        ProcumentActivity::where('tender_id', $input['id'])->where('company_id', $input['company_id'])->delete();
                        foreach ($input['procument_activity'] as $vl) {
                            $activity['tender_id'] = $input['id'];
                            $activity['category_id'] = $vl['id'];
                            $activity['company_id'] = $input['company_id'];
                            $activity['created_by'] = $employee->employeeSystemID;

                            ProcumentActivity::create($activity);
                        }
                    } else {
                        ProcumentActivity::where('tender_id', $input['id'])->where('company_id', $input['company_id'])->delete();
                    }
                } else {
                    ProcumentActivity::where('tender_id', $input['id'])->where('company_id', $input['company_id'])->delete();
                }
                if (isset($input['calendarDates'])) {
                    if (count($input['calendarDates']) > 0) {
                        CalendarDatesDetail::where('tender_id', $input['id'])->where('company_id', $input['company_id'])->delete();
                        foreach ($input['calendarDates'] as $calDate) {
                            if (!empty($calDate['from_date'])) {
                                $frm_date = new Carbon($calDate['from_date']);
                                $frm_date = $frm_date->format('Y-m-d');
                            }else{
                                $frm_date = null;
                            }
                            if (!empty($calDate['to_date'])) {
                                $to_date = new Carbon($calDate['to_date']);
                                $to_date = $to_date->format('Y-m-d');
                            }else{
                                $to_date = null;
                            }
                            $calDt['tender_id'] = $input['id'];
                            $calDt['calendar_date_id'] = $calDate['id'];
                            $calDt['from_date'] = $frm_date;
                            $calDt['to_date'] = $to_date;
                            $calDt['company_id'] = $input['company_id'];
                            $calDt['created_by'] = $employee->employeeSystemID;

                            CalendarDatesDetail::create($calDt);
                        }
                    }else {
                        CalendarDatesDetail::where('tender_id', $input['id'])->where('company_id', $input['company_id'])->delete();
                    }
                }else {
                    CalendarDatesDetail::where('tender_id', $input['id'])->where('company_id', $input['company_id'])->delete();
                }


                if ($exist['site_visit_date'] != $site_visit_date) {
                    $site['tender_id'] = $input['id'];
                    $site['date'] = $site_visit_date;
                    $site['company_id'] = $input['company_id'];
                    $site['created_by'] = $employee->employeeSystemID;

                    TenderSiteVisitDates::create($site);
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
                        TenderMaster::where('id', $input['id'])->update($att);
                    }
                }

                if (isset($input['confirmed_yn'])) {
                    if ($input['confirmed_yn'] == 1) {
                        $technical = EvaluationCriteriaDetails::where('tender_id', $input['id'])->where('critera_type_id', 2)->first();
                        if (empty($technical)) {
                            return ['success' => false, 'message' => 'At least one technical criteria should be added'];
                        }
                        $schedule = PricingScheduleMaster::where('tender_id', $input['id'])->first();
                        if (empty($schedule)) {
                            return ['success' => false, 'message' => 'At least one work schedule should be added'];
                        }
                        $scheduleAll = PricingScheduleMaster::where('tender_id', $input['id'])->get();
                        foreach ($scheduleAll as $val) {
                            $mainwork = TenderMainWorks::where('tender_id', $input['id'])->where('schedule_id', $val['id'])->first();
                            $scheduleDetail = ScheduleBidFormatDetails::where('schedule_id', $val['id'])->first();
                            if (empty($scheduleDetail)) {
                                return ['success' => false, 'message' => 'All work schedules should be completed'];
                            }
                            if (empty($mainwork)) {
                                return ['success' => false, 'message' => 'Main works should be added in all work schedules'];
                            }
                        }

                        $mainWorkBoqApp = TenderMainWorks::with(['tender_bid_format_detail'])->where('tender_id', $input['id'])->get();
                        foreach ($mainWorkBoqApp as $vals) {
                            if ($vals['tender_bid_format_detail']['boq_applicable'] == 1) {
                                $boqItems = TenderBoqItems::where('main_work_id', $vals['id'])->first();
                                if (empty($boqItems)) {
                                    return ['success' => false, 'message' => 'BOQ enabled main works should have at least one BOQ item'];
                                }
                            }
                        }

                        $params = array('autoID' => $input['id'], 'company' => $input["company_id"], 'document' => $input["document_system_id"]);
                        $confirm = \Helper::confirmDocument($params);
                        if (!$confirm["success"]) {
                            return ['success' => false, 'message' => $confirm["message"]];
                        } else {
                            $dataC['confirmed_yn'] = 1;
                            $dataC['confirmed_date'] = now();
                            $dataC['confirmed_by_emp_system_id'] = $employee->employeeSystemID;

                            TenderMaster::where('id', $input['id'])->update($dataC);
                        }
                    }
                }


                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated'];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function validateTenderHeader($input)
    {
        $messages = [
            'title.required' => 'Title is required.',
            'tender_type_id.required' => 'Type is required.',
            'currency_id.required' => 'Currency is required.',
            'envelop_type_id.required' => 'Envelop Type is required.',
            'evaluation_type_id.required' => 'Evaluation Type is required.',
            'estimated_value.required' => 'Estimated Value is required.',
            'allocated_budget.required' => 'Allocated Budget is required.',
            'tender_document_fee.required' => 'Tender Document Fee is required.',
            'bank_id.required' => 'Bank is required.',
            'bank_account_id.required' => 'Bank Account is required.',
            'document_sales_start_date.required' => 'Document Sales From Date is required.',
            'document_sales_end_date.required' => 'Document Sales To Date is required.',
            'pre_bid_clarification_start_date.required' => 'Pre-bid Clarification From Date.',
            'pre_bid_clarification_end_date.required' => 'Pre-bid Clarification To Date.',
            'pre_bid_clarification_method.required' => 'Pre-bid Clarifications Method.',
            'bid_submission_opening_date.required' => 'Bid Submission From Date.',
            'bid_submission_closing_date.required' => 'Bid Submission To Date.',
            'site_visit_date.required' => 'Site Visit From Date.',
            'site_visit_end_date.required' => 'Site Visit To Date.'

        ];

        $validator = \Validator::make($input, [
            'title' => 'required',
            'tender_type_id' => 'required',
            'currency_id' => 'required',
            'envelop_type_id' => 'required',
            'evaluation_type_id' => 'required',
            'estimated_value' => 'required',
            'allocated_budget' => 'required',
            'tender_document_fee' => 'required',
            'bank_id' => 'required',
            'bank_account_id' => 'required',
            'document_sales_start_date' => 'required',
            'document_sales_end_date' => 'required',
            'pre_bid_clarification_start_date' => 'required',
            'pre_bid_clarification_end_date' => 'required',
            'pre_bid_clarification_method' => 'required',
            'bid_submission_opening_date' => 'required',
            'bid_submission_closing_date' => 'required',
            'site_visit_date' => 'required',
            'site_visit_end_date' => 'required',

        ], $messages);

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
            ->where('pre_bid_clarification_method', '!=', 0)
            ->where('closed_yn', '!=', 1)
            ->get();
        return $data;
    }

    public function getTenderMasterApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

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
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                /*->on('erp_documentapproved.departmentSystemID', '=', 'employeesdepartments.departmentSystemID')*/
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            $query->where('employeesdepartments.documentSystemID', 108)
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('srm_tender_master', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('srm_tender_master.company_id', $companyID)
                ->where('srm_tender_master.approved', 0)
                ->where('srm_tender_master.confirmed_yn', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->join('currencymaster', 'currency_id', '=', 'currencyID')
            ->join('employees', 'created_by', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 108)
            ->where('erp_documentapproved.companySystemID', $companyID);

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
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    public function getTenderMasterFullApproved(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

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
        )->join('employeesdepartments', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                /*->on('erp_documentapproved.departmentSystemID', '=', 'employeesdepartments.departmentSystemID')*/
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            $query->where('employeesdepartments.documentSystemID', 108)
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('srm_tender_master', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('srm_tender_master.company_id', $companyID)
                ->where('srm_tender_master.approved', -1)
                ->where('srm_tender_master.confirmed_yn', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->join('currencymaster', 'currency_id', '=', 'currencyID')
            ->join('employees', 'created_by', 'employees.employeeSystemID')
            ->where('erp_documentapproved.documentSystemID', 108)
            ->where('erp_documentapproved.companySystemID', $companyID);

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
        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $att['updated_by'] = $employee->employeeSystemID;
            $att['published_yn'] = 1;
            $result = TenderMaster::where('id', $input['id'])->update($att);

            if ($result) {
                DB::commit();
                return ['success' => true, 'message' => 'Successfully Published'];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function loadTenderSubActivity(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($request->all(), array('procument_cat_id'));
        $tenderMaster = TenderMaster::where('id', $input['tenderMasterId'])->first();
        if ($input['procument_cat_id'] > 0) {
            $data['procurementSubCategory'] = TenderProcurementCategory::where('parent_id', $input['procument_cat_id'])->where('is_active', 1)->get();
        } else {
            $data['procurementSubCategory'] = array();
        }


        $activity = ProcumentActivity::where('tender_id', $input['tenderMasterId'])->get();

        if ($tenderMaster['confirmed_yn'] == 1) {
            if (count($activity) > 0) {
                foreach ($activity as $vl) {
                    $category = TenderProcurementCategory::where('id', $vl['category_id'])->first();
                    if ($category['is_active'] == 0) {
                        $data['procurementSubCategory'][] = $category;
                    }
                }
            }
        }

        return $data;
    }
    public function sendSupplierInvitation(Request $request)
    {
        $companyName = "";
        $company = Company::find($request->input('company_id'));
        if (isset($company->CompanyName)) {
            $companyName =  $company->CompanyName;
        }
        $token = md5(Carbon::now()->format('YmdHisu'));
        $apiKey = $request->input('api_key');
        $isCreated = $this->registrationLinkRepository->save($request, $token);
        $loginUrl = env('SRM_LINK') . $token . '/' . $apiKey;
        if ($isCreated['status'] == true) { 
            Mail::to($request->input('email'))->send(new EmailForQueuing("Registration Link", "Dear Supplier," . "<br /><br />" . " Please find the below link to register at " . $companyName . " supplier portal. It will expire in 48 hours. " . "<br /><br />" . "Click Here: " . "</b><a href='" . $loginUrl . "'>" . $loginUrl . "</a><br /><br />" . " Thank You" . "<br /><br /><b>"));
            return $this->sendResponse($loginUrl, 'Supplier Registration Link Generated successfully');
        } else {
            return $this->sendError('Supplier Registration Link Generation Failed', 500);
        }
    }
    public function getSupplierList(Request $request)
    {
        $input = $request->all();
        $selectedCompanyId = $input['companyId'];
        $tenderMasterId = $input['tenderMasterId'];
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }


        $qry = SupplierAssigned::whereDoesntHave('tenderSupplierAssigned', function ($query) use ($tenderMasterId) {
            $query->where('tender_master_id', '=', $tenderMasterId);
        })
            ->where('companySystemID', $selectedCompanyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->where('supEmail', '!=', null)
            ->where('registrationNumber', '!=', null);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $qry = $qry->where(function ($query) use ($search) {
                $query->where('primarySupplierCode', 'LIKE', "%{$search}%")
                    ->orWhere('registrationNumber', 'LIKE', "%{$search}%")
                    ->orWhere('supEmail', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($qry)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
    public function saveSupplierAssigned(Request $request)
    {
        $input = $request->all();
        $companySystemId = $input['companySystemID'];
        $isCheck = $input['isCheck'];
        $pullList = $input['pullList'];
        $removedSuppliersId = $input['removedSuppliersId'];
        $selectAll = $input['selectAll'];
        $tenderId = $input['tenderId'];
        $employee = \Helper::getEmployeeInfo();
        $data = [];
        $insertSupplierAssignee = false;
        $messages = array(
            'pullList.required'   => 'Please select the supplier(s).',
        );

        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'pullList' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if (!empty($pullList)) { 
            if($selectAll == true){ 
                $pullList = [];
                TenderSupplierAssignee::where('tender_master_id', $tenderId)
                ->whereNotNull('supplier_assigned_id')
                ->delete();
                $suppilerAssigned = SupplierAssigned::whereDoesntHave('tenderSupplierAssigned', function ($query) use ($tenderId) {
                    $query->where('tender_master_id', '=', $tenderId);
                })
                ->whereNotIn('supplierAssignedID',$removedSuppliersId)
                ->where('companySystemID',$companySystemId) 
                ->where('isActive', 1)
                ->where('isAssigned', -1)
                ->where('supEmail', '!=', null)
                ->where('registrationNumber', '!=', null); 
                $pullList = collect($suppilerAssigned->get())->pluck('supplierAssignedID')->toArray();  
            } 

            foreach ($pullList as $key => $val) {
                $data[] = array(
                    'tender_master_id' => $tenderId,
                    'supplier_assigned_id' => $val,
                    'created_by' => $employee->employeeSystemID,
                    'company_id' => $companySystemId,
                    'created_at' => Helper::currentDateTime()
                );
            }
            $insertSupplierAssignee = TenderSupplierAssignee::insert($data);
        }
        if ($insertSupplierAssignee) {
            return $this->sendResponse([], 'New supplier(s) added');
        } else {
            return $this->sendError('Insertion faild', 422);
        }
    }
    public function getSupplierAssignedList(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $companyId = $input['companyId'];
        $tenderMasterId =  $input['tenderMasterId'];
        $qry = TenderSupplierAssignee::with(['supplierAssigned'])
            ->where('company_id', $companyId)
            ->where('tender_master_id', $tenderMasterId);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $qry = $qry->where(function ($query) use ($search) {
                $query->where('supplier_name', 'LIKE', "%{$search}%");
                $query->orWhere('supplier_email', 'LIKE', "%{$search}%");
                $query->orWhere('registration_number', 'LIKE', "%{$search}%"); 
                 $query->orWhereHas('supplierAssigned', function ($query1) use ($search) {
                    $query1->where('primarySupplierCode', 'LIKE', "%{$search}%");
                    $query1->orWhere('registrationNumber', 'LIKE', "%{$search}%");
                    $query1->orWhere('supEmail', 'LIKE', "%{$search}%");
                    $query1->orWhere('supplierName', 'LIKE', "%{$search}%");
                });
            });
        } 
        return \DataTables::eloquent($qry)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
