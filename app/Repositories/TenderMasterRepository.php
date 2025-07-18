<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Helpers\General;
use App\Models\BankMaster;
use App\Http\Controllers\API\TenderBidEmployeeDetails;
use App\Models\BidSubmissionDetail;
use App\Models\CalendarDates;
use App\Models\CalendarDatesDetail;
use App\Models\CalendarDatesDetailEditLog;
use App\Models\CircularAmendments;
use App\Models\CircularAmendmentsEditLog;
use App\Models\CodeConfigurations;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\ContractMaster;
use App\Models\ContractSettingDetail;
use App\Models\ContractSettingMaster;
use App\Models\ContractStatusHistory;
use App\Models\ContractTypes;
use App\Models\ContractTypeSections;
use App\Models\ContractUserAssign;
use App\Models\ContractUserGroup;
use App\Models\ContractUserGroupAssignedUser;
use App\Models\ContractUsers;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentAttachments;
use App\Models\DocumentMaster;
use App\Models\DocumentModifyRequest;
use App\Models\EnvelopType;
use App\Models\EvaluationCriteriaDetails;
use App\Models\EvaluationCriteriaDetailsEditLog;
use App\Models\EvaluationType;
use App\Models\PricingScheduleDetail;
use App\Models\PricingScheduleDetailEditLog;
use App\Models\PricingScheduleMaster;
use App\Models\PricingScheduleMasterEditLog;
use App\Models\ProcumentActivity;
use App\Models\ProcumentActivityEditLog;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use App\Models\ScheduleBidFormatDetails;
use App\Models\ScheduleBidFormatDetailsLog;
use App\Models\SrmBudgetItem;
use App\Models\SrmDepartmentMaster;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Models\SrmTenderBidEmployeeDetailsEditLog;
use App\Models\SrmTenderBudgetItem;
use App\Models\SRMTenderCalendarLog;
use App\Models\SrmTenderDepartment;
use App\Models\SrmTenderMasterEditLog;
use App\Models\SRMTenderPaymentProof;
use App\Models\SRMTenderTechnicalEvaluationAttachment;
use App\Models\SupplierRegistrationLink;
use App\Models\SupplierAssigned;
use App\Models\TenderBoqItems;
use App\Models\TenderBoqItemsEditLog;
use App\Models\TenderBudgetItemEditLog;
use App\Models\TenderCirculars;
use App\Models\TenderCircularsEditLog;
use App\Models\TenderDepartmentEditLog;
use App\Models\TenderDocumentTypeAssign;
use App\Models\TenderDocumentTypeAssignLog;
use App\Models\TenderDocumentTypes;
use App\Models\TenderMaster;
use App\Models\TenderMasterSupplier;
use App\Models\TenderProcurementCategory;
use App\Models\TenderPurchaseRequest;
use App\Models\TenderPurchaseRequestEditLog;
use App\Models\TenderSiteVisitDateEditLog;
use App\Models\TenderSiteVisitDates;
use App\Models\TenderSupplierAssignee;
use App\Models\TenderSupplierAssigneeEditLog;
use App\Models\TenderType;
use App\Models\YesNoSelection;
use App\Services\GeneralService;
use App\Services\SRMService;
use App\Utilities\ContractManagementUtils;
use Carbon\Carbon;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Services\SrmDocumentModifyService;
use mysql_xdevapi\Exception;

/**
 * Class TenderMasterRepository
 * @package App\Repositories
 * @version March 10, 2022, 1:54 pm +04
 *
 * @method TenderMaster findWithoutFail($id, $columns = ['*'])
 * @method TenderMaster find($id, $columns = ['*'])
 * @method TenderMaster first($columns = ['*'])
 */
class TenderMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'title_sec_lang',
        'description',
        'description_sec_lang',
        'tender_type_id',
        'currency_id',
        'envelop_type_id',
        'procument_cat_id',
        'procument_sub_cat_id',
        'evaluation_type_id',
        'estimated_value',
        'allocated_budget',
        'budget_document',
        'tender_document_fee',
        'bank_id',
        'bank_account_id',
        'document_sales_start_date',
        'document_sales_end_date',
        'pre_bid_clarification_start_date',
        'pre_bid_clarification_end_date',
        'pre_bid_clarification_method',
        'site_visit_date',
        'bid_submission_opening_date',
        'bid_submission_closing_date',
        'created_by',
        'updated_by',
        'deleted_by',
        'company_id',
        'bid_opening_date',
        'bid_opening_end_date',
        'technical_bid_opening_date',
        'technical_bid_closing_date',
        'commerical_bid_opening_date',
        'commerical_bid_closing_date',
        'is_negotiation_started',
        'negotiation_published'

    ];

    protected $srmDocumentModifyService;
    public function __construct(
        Application $app,
        SrmDocumentModifyService $srmDocumentModifyService
    ){
        parent::__construct($app);
        $this->srmDocumentModifyService = $srmDocumentModifyService;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderMaster::class;
    }

    public function getTenderFilterData(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];

        $currency = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $selection = TenderType::select('id', 'name')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            });

        $envelope = EnvelopType::select('id','name')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            });

        $published =  array(
            array('value'=> 0 , 'label'=> 'Not Published'),
            array('value'=> 1 , 'label'=> 'Published'),
        );

        $tenderNegotiationStatus =  array(
            array('value'=> 1 , 'label'=> 'Negotiation Not Started'),
            array('value'=> 2 , 'label'=> 'Negotiation Started'),
            array('value'=> 3 , 'label'=> 'Negotiation Completed'),
        );

        $status =array(
            array('value'=> 1 , 'label'=> 'Not Confirmed'),
            array('value'=> 2 , 'label'=> 'Pending Approval'),
            array('value'=> 3 , 'label'=> 'Fully Approved'),
            array('value'=> 4 , 'label'=> 'Referred Back'),
            array('value'=> 5 , 'label'=> 'Rejected'),
        );

        $rfxTypes = array(
            array('value'=> 1 , 'label'=> 'RFQ'),
            array('value'=> 2 , 'label'=> 'RFI'),
            array('value'=> 3 , 'label'=> 'RFP'),
        );

        $gonogo = array(
            array('value'=> 1 , 'label'=> 'Not Completed'),
            array('value'=> 2 , 'label'=> 'Completed'),
        );

        $technical = array(
            array('value'=> 0 , 'label'=> 'Not Completed'),
            array('value'=> 1 , 'label'=> 'Completed'),
        );

        $stage = array(
            array('value'=> 1 , 'label'=> 'Single Stage'),
            array('value'=> 2 , 'label'=> 'Two Stage'),
        );

        $commercial = array(
            array('value'=> 0 , 'label'=> 'Not Completed'),
            array('value'=> 1 , 'label'=> 'Completed'),
        );

        $data = array(
            'currency' => $currency,
            'selection' => $selection,
            'envelope' => $envelope,
            'published' => $published,
            'status' => $status,
            'rfxTypes' => $rfxTypes,
            'technical' => $technical,
            'gonogo' => $gonogo,
            'stage' => $stage,
            'commercial' => $commercial,
            'tenderNegotiationStatus' => $tenderNegotiationStatus
        );

        return $data;
    }

    public function getTenderPr(Request $request){
        $input = $request->all();
        $tenderId = $input['tenderId'];
        $companyId = $input['companyId'];

        $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tenderId);
        $editOrAmend = $requestData['enableRequestChange'] ?? false;
        $versionID = $requestData['versionID'];

        return PurchaseRequest::getPurchaseRequestForTender($tenderId, $companyId, $versionID, $editOrAmend);

    }

    public function getPurchaseRequestDetails(Request $request)
    {
        $purchaseRequestID = $request->input('purchaseRequestID');
        $tender_id = $request->input('tenderId');
        $main_work_id = $request->input('main_work_id');

        $purchaseRequestIDToCheck = $purchaseRequestID;
        $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tender_id);
        $editOrEnable = $requestData['enableRequestChange'] ?? false;
        $versionID = $requestData['versionID'] ?? 0;

        $result = $editOrEnable ?
            TenderBoqItemsEditLog::checkPRAlreadyAdded($tender_id, $purchaseRequestIDToCheck, $main_work_id, $versionID) :
            TenderBoqItems::checkPRAlreadyAdded($tender_id, $purchaseRequestIDToCheck, $main_work_id);

        if ($result) {
            return [
                'success' => false,
                'message' => 'Line items are already added',
                'data' => ''
            ];
        }

        $pr = PurchaseRequestDetails::where('purchaseRequestID', $purchaseRequestID);

        $pr = $pr->with(['uom' , 'purchase_request'])->get()
            ->transform(function ($data) {
                return $this->prDetailFormat($data);
            });

        $result['prDetail'] = $pr;
        return [
            'success' => true,
            'message' => 'PR Details Retrieved',
            'data' => $result
        ];
    }

    public function prDetailFormat($data)
    {
        return [
            'purchaseRequestID' => $data['purchaseRequestID'],
            'purchaseRequestCode' => $data['purchase_request']['purchaseRequestCode'],
            'purchaseRequestDetailsID' => $data['purchaseRequestDetailsID'],
            'itemPrimaryCode' => $data['itemPrimaryCode'],
            'itemDescription' => $data['itemDescription'],
            'noQty' => $data['quantityRequested'],
            'unitID' => $data['uom']['UnitID'],
            'unitShortCode' => $data['uom']['UnitShortCode'],
            'item_id' => $data['itemCode']
        ];
    }

    public static function getTenderDidOpeningDates($tenderId, $companyId)
    {
        $current_date = Carbon::now();
        $tender = TenderMaster::getTenderDidOpeningDates($tenderId, $companyId);

        if (!$tender) {
            return [
                'error' => 'Tender not found.',
            ];
        }

        $opening_date_comp = $tender->stage === 1 ? $tender->bid_opening_date : $tender->technical_bid_opening_date;
        $opening_date_comp_end = $tender->stage === 1 ? $tender->bid_opening_end_date : $tender->technical_bid_closing_date;
        return $current_date->gt($opening_date_comp) && ($opening_date_comp_end === null || $opening_date_comp_end->gt($current_date));
    }


    public static function getTenderPOData($tenderId, $companyId)
    {
        return TenderMaster::getTenderPOData($tenderId, $companyId);
    }
    public function getPaymentProofDocumentApproval($request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empId = \Helper::getEmployeeSystemID();
        $tenderPaymentProof =  SRMTenderPaymentProof::getTenderPaymentReview($companyId,$empId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $tenderPaymentProof = $tenderPaymentProof->where(function($query) use ($search) {
                $query->where('tm.title', 'LIKE', "%{$search}%")
                    ->orWhere('tm.tender_code', 'LIKE', "%{$search}%");
            });
        }
        return \DataTables::of($tenderPaymentProof)
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

    public function getSupplierWiseProofNotApproved($request)
    {
        $input = $request->all();
        $empId = \Helper::getEmployeeSystemID();
        $companyId = $input['companyId'];
        $tenderUuid = $input['uuid'];
        $tenderData = TenderMaster::getTenderByUuid($tenderUuid);


        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }


        $supplierPaymentProof = SRMTenderPaymentProof::getSupplierWiseDataNotApproved($companyId,$empId,$tenderData['id']);
        return \DataTables::of($supplierPaymentProof)
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

    public function approveSupplierWiseTender($request)
    {
        $input = $request->all();
        $data = $this->prepareDocumentData($input);

        unset($data['approvedComments']);
        $data['approvedComments'] = ($input['approvedComments']) ?? null;

        $approve = \Helper::approveDocument($data);

        if ($approve['data'] && $approve['data']['numberOfLevels'] == $approve['data']['currentLevel']) {
            $this->purchaseTender($request);
        }

        return ['success' => $approve["success"], 'message' => $approve["message"], 'data'=> $approve];
    }

    public function purchaseTender($request)
    {
        $input = $request->all();
        $getPaymentProofDocument = SRMTenderPaymentProof::getPaymentProofDataByUuid($input['uuid']);
        $result = DB::transaction(function () use ($getPaymentProofDocument) {
            $data = [
                'tender_master_id' => $getPaymentProofDocument['tender_id'],
                'purchased_date' =>  Carbon::parse(now())->format('Y-m-d H:i:s'),
                'purchased_by' => $getPaymentProofDocument['srm_supplier_id'],
                'created_by' => $getPaymentProofDocument['srm_supplier_id']
            ];
            TenderMasterSupplier::create($data);
        });
    }

    public function rejectSupplierWiseTender($request)
    {
        $input = $request->all();
        $data = DocumentApproved::getDocumentApprovedData($input['documentApCode']);
        $data = $this->prepareDocumentData($input);
        unset($data['rejectedComments']);
        $data['rejectedComments'] = ($input['rejectedComments']) ?? null;

        $approve = \Helper::rejectDocument($data);

        if($approve['success'])
        {
            SRMService::reopenPaymentProof($input['uuid']);
        }

        return ['success' => $approve["success"], 'message' => $approve["message"], 'data'=> $approve];
    }

    protected function prepareDocumentData($input)
    {
        $documentData = DocumentApproved::getDocumentApprovedData($input['documentApCode']);
        $paymentProofData = SRMTenderPaymentProof::getPaymentProofDataByUuid($input['uuid']);
        $tenderData = TenderMaster::getTenderDidOpeningDates(
            $paymentProofData['tender_id'] ?? null,
            $paymentProofData['company_id'] ?? null
        );

        $documentData->tenderCode = $tenderData->tender_code ?? null;
        $documentData->tenderTitle = $tenderData->title ?? null;
        $documentData->supplierName = $input['supplierName'] ?? null;

        return $documentData;
    }

    public function getSupplierWiseProofApproved($request)
    {
        $input = $request->all();
        $empId = \Helper::getEmployeeSystemID();
        $companyId = $input['companyId'];
        $tenderUuid = $input['uuid'];
        $tenderData = TenderMaster::getTenderByUuid($tenderUuid);


        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }


        $supplierPaymentProof = SRMTenderPaymentProof::getSupplierWiseDataApproved($companyId,$empId,$tenderData['id']);
        return \DataTables::of($supplierPaymentProof)
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

    public function updateTenderCalendarDays($request)
    {
        try
        {
            $input = $request->all();
            $tenderData = TenderMaster::getTenderByUuid($input['tenderCode']);
            $isTender = $input['isTender'];


            if(empty($tenderData)){
                return ['success' => false, 'message' => 'Tender data not found'];
            }

            $formattedDatesAndTime = $this->getFormattedDatesAndTime($input,$tenderData);
            $validationResult = $this->validateTenderDates($formattedDatesAndTime,$tenderData,$isTender, $input);
            if (!$validationResult['success']) {
                return $validationResult;
            }

            $updatedData = $this->processTenderUpdate($formattedDatesAndTime, $tenderData,$input);

            $title = ($isTender == 1) ? "Tender" : "RFX";

            return [
                'success' => true,
                'message' => $title . ' calendar days updated successfully.',
                'data' => $updatedData
            ];

        }
        catch(\Exception $e)
        {
            return ['success' => false, 'message' => $e->getMessage(),];
        }
    }

    private function validateTenderDates($data,$tenderData,$isTender,$input)
    {
        $documentSalesStartDate = $data['documentSalesStartDate'];
        $documentSalesEndDate = $data['documentSalesEndDate'];
        $submissionClosingDate = $data['submissionClosingDate'];
        $submissionOpeningDate = $data['submissionOpeningDate'];
        $bidOpeningStartDate = $data['bidOpeningStartDate'];
        $bidOpeningEndDate = $data['bidOpeningEndDate'];

        $technicalStartDate = $data['technicalStartDate'];
        $technicalEndDate = $data['technicalEndDate'];
        $commercialStartDate = $data['commercialStartDate'];
        $commercialEndDate = $data['commercialEndDate'];

        $preBidClarificationStartDate = $data['preBidClarificationStartDate'];
        $preBidClarificationEndDate = $data['preBidClarificationEndDate'];
        $siteVisitStartDate = $data['siteVisitStartDate'];
        $siteVisitEndDate = $data['siteVisitEndDate'];

        if (!empty($input['calendarDates'])) {
            foreach ($input['calendarDates'] as $calDate) {
                $isDefault = $calDate['is_default'] ?? 0;
                if ($isDefault == 0) {
                    $keyBase = $calDate['calendar_date'];
                    $startDate = $this->parseDateTime($calDate, 'from_date', 'from_time');
                    $endDate = $this->parseDateTime($calDate, 'to_date', 'to_time');

                    $calendarDateTimes[$keyBase . 'StartDate'] = $startDate;
                    $calendarDateTimes[$keyBase . 'EndDate'] = $endDate;

                    if (!empty($startDate) && !empty($endDate) && $startDate > $endDate) {
                        return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time'];
                    }
                }
            }
        }

        if ($submissionOpeningDate > $submissionClosingDate) {
            return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time  for Bid Submission'];
        }

        if ((isset($documentSalesStartDate) && isset($documentSalesEndDate)) && (($documentSalesStartDate > $documentSalesEndDate))) {
            return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time  for Document Sales'];
        }

        if (!is_null($documentSalesStartDate) && $documentSalesStartDate > $submissionOpeningDate) {
            return ['success' => false, 'message' => 'Bid submission from date and time should greater than document sales from date and time'];
        }

        if($tenderData['stage'] == 1)
        {

            if (!is_null($bidOpeningStartDate) && $submissionClosingDate >= $bidOpeningStartDate) {
                return ['success' => false, 'message' => 'Bid Opening from date and time should greater than bid submission to date and time'];
            }

            if (!is_null($bidOpeningStartDate) && !is_null($bidOpeningEndDate) && $bidOpeningStartDate > $bidOpeningEndDate) {
                return ['success' => false, 'message' => 'Bid Opening to date and time should greater than bid opening from date and time'];
            }
        }


        if($tenderData['stage'] == 2)
        {
            if (!is_null($technicalStartDate) && $submissionClosingDate > $technicalStartDate) {
                return ['success' => false, 'message' => 'Technical Bid Opening from date and time should greater than bid submission to date and time'];
            }
            if (!is_null($technicalStartDate) && !is_null($commercialStartDate) && $technicalStartDate > $commercialStartDate) {
                return ['success' => false, 'message' => 'Commercial Bid Opening from date and time should be greater than technical bid from date and time'];
            }

            if (!is_null($commercialEndDate) && !is_null($commercialStartDate) && $commercialStartDate > $commercialEndDate) {
                return ['success' => false, 'message' => 'Commercial Bid Opening to date and time should greater than commercial bid opening from date and time'];
            }

            if (!is_null($technicalEndDate) && !is_null($commercialStartDate) && ($technicalEndDate >= $commercialStartDate)) {
                return ['success' => false, 'message' => 'Commercial Bid Opening from date and time should be greater than technical bid to date and time'];
            }

            if (!empty($technicalStartDate) && !empty($technicalEndDate && $technicalStartDate > $technicalEndDate)) {
                return ['success' => false, 'message' => 'Technical Bid Opening to date and time should greater than Technical Bid Opening from date and time'];
            }
        }

        if ((!is_null($preBidClarificationStartDate) && !is_null($preBidClarificationEndDate)) && (($preBidClarificationStartDate > $preBidClarificationEndDate))) {
            return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time  for Pre-bid Clarification'];
        }

        if ((!is_null($siteVisitStartDate) && !is_null($siteVisitEndDate)) && (($siteVisitStartDate > $siteVisitEndDate))) {
            return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time  for Site Visit'];
        }

        if (!is_null($preBidClarificationStartDate) && !is_null($documentSalesStartDate) && ($documentSalesStartDate > $preBidClarificationStartDate)) {
            return ['success' => false, 'message' => 'Pre-bid Clarification from date and time should greater than document sale from date and time'];
        }

        if (!is_null($preBidClarificationStartDate) && ($preBidClarificationStartDate > $submissionClosingDate)) {
            return ['success' => false, 'message' => 'Pre-bid Clarification from date and time should less than bid submission to date and time'];
        }

        if (!is_null($preBidClarificationEndDate) && ($preBidClarificationEndDate >= $submissionClosingDate)) {
            return ['success' => false, 'message' => 'Pre-bid Clarification to date and time should less than bid submission to date and time'];
        }

        if ((!is_null($preBidClarificationStartDate) && !is_null($preBidClarificationEndDate)) && (($preBidClarificationStartDate > $preBidClarificationEndDate))) {
            return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time  for Pre-bid Clarification'];
        }

        if ((!is_null($siteVisitStartDate) && !is_null($siteVisitEndDate)) && (($siteVisitStartDate > $siteVisitEndDate))) {
            return ['success' => false, 'message' => 'From date and time cannot be greater than the To date and time  for Site Visit'];
        }

        if (!is_null($preBidClarificationStartDate) && !is_null($documentSalesStartDate) && ($documentSalesStartDate > $preBidClarificationStartDate)) {
            return ['success' => false, 'message' => 'Pre-bid Clarification from date and time should greater than document sale from date and time'];
        }

        if (!is_null($preBidClarificationStartDate) && ($preBidClarificationStartDate > $submissionClosingDate)) {
            return ['success' => false, 'message' => 'Pre-bid Clarification from date and time should less than bid submission to date and time'];
        }

        if (!is_null($preBidClarificationEndDate) && ($preBidClarificationEndDate >= $submissionClosingDate)) {
            return ['success' => false, 'message' => 'Pre-bid Clarification to date and time should less than bid submission to date and time'];
        }

        return ['success' => true];
    }
    private function getFormattedDatesAndTime($input, $tenderData)
    {
        return [
            'documentSalesStartDate' => $this->parseDateTime($input, 'documentSalesStartDate', 'documentSalesStartTime'),
            'documentSalesEndDate' => $this->parseDateTime($input, 'documentSalesEndDate', 'documentSalesEndTime'),
            'submissionClosingDate' => $this->parseDateTime($input, 'submissionClosingDate', 'bidSubmissionClosingTime'),
            'submissionOpeningDate' => $this->parseDateTime($input, 'submissionOpeningDate', 'bidSubmissionOpeningTime'),
            'bidOpeningStartDate' => $this->parseDateTime($input, 'bidOpeningStartDate', 'bidOpeningStarDateTime'),
            'bidOpeningEndDate' => $this->parseDateTime($input, 'bidOpeningEndDate', 'bidOpeningEndDateTime'),
            'technicalStartDate' => $this->parseDateTime($input, 'technicalBidOpeningStartDate', 'technicalBidOpeningStarDateTime'),
            'technicalEndDate' => $this->parseDateTime($input, 'technicalBidOpeningEndDate', 'technicalBidOpeningEndDateTime'),
            'commercialStartDate' => $this->parseDateTime($input, 'commercialBidOpeningStartDate', 'commercialBidOpeningStarDateTime'),
            'commercialEndDate' => $this->parseDateTime($input, 'commercialBidOpeningEndDate', 'commercialBidOpeningEndDateTime'),
            'preBidClarificationStartDate' => $this->parseDateTime($input, 'preBidClarificationStartDate', 'preBidClarificationStartTime'),
            'preBidClarificationEndDate' => $this->parseDateTime($input, 'preBidClarificationEndDate', 'preBidClarificationEndTime'),
            'siteVisitStartDate' => $this->parseDateTime($input, 'siteVisitStartDate', 'siteVisitStartTime'),
            'siteVisitEndDate' => $this->parseDateTime($input, 'siteVisitEndDate', 'siteVisitEndTime')
        ];
    }
    private function parseDateTime($input, $dateKey, $timeKey)
    {
        if (!empty($input[$dateKey]) && !empty($input[$timeKey])) {
            $dateTime = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                Carbon::parse($input[$dateKey])->format('Y-m-d') . ' ' . Carbon::parse($input[$timeKey])->format('H:i:s')
            );
            return $dateTime;
        }
        return null;
    }
    private function processTenderUpdate($formattedDatesAndTime, $tenderData,$input)
    {
        try {
            DB::transaction(function () use ($formattedDatesAndTime, $tenderData, $input) {
                $tenderMaster = TenderMaster::find($tenderData['id']);
                $calendarLog = $this->insertCalendarLog($formattedDatesAndTime, $tenderData, $input);
                $data = [
                    'document_sales_start_date' => $formattedDatesAndTime['documentSalesStartDate'] ?? null,
                    'document_sales_end_date' => $formattedDatesAndTime['documentSalesEndDate'] ?? null,
                    'bid_submission_opening_date' => $formattedDatesAndTime['submissionOpeningDate'] ?? null,
                    'bid_submission_closing_date' => $formattedDatesAndTime['submissionClosingDate'] ?? null,
                    'bid_opening_date' => $formattedDatesAndTime['bidOpeningStartDate'] ?? null,
                    'bid_opening_end_date' => $formattedDatesAndTime['bidOpeningEndDate'] ?? null,
                    'technical_bid_opening_date' => $formattedDatesAndTime['technicalStartDate'] ?? null,
                    'technical_bid_closing_date' => $formattedDatesAndTime['technicalEndDate'] ?? null,
                    'commerical_bid_opening_date' => $formattedDatesAndTime['commercialStartDate'] ?? null,
                    'commerical_bid_closing_date' => $formattedDatesAndTime['commercialEndDate'] ?? null,
                    'pre_bid_clarification_start_date' => $formattedDatesAndTime['preBidClarificationStartDate'] ?? null,
                    'pre_bid_clarification_end_date' => $formattedDatesAndTime['preBidClarificationEndDate'] ?? null,
                    'site_visit_date' => $formattedDatesAndTime['siteVisitStartDate'] ?? null,
                    'site_visit_end_date' => $formattedDatesAndTime['siteVisitEndDate'] ?? null,
                ];

                $tenderMaster->update($data);

                $calendarDateMap = CalendarDates::calendarDateMap($input['calendarDates']);

                $defaultDateMappings = [
                    1 => ['start' => 'preBidClarificationStartDate', 'end' => 'preBidClarificationEndDate'],
                    2 => ['start' => 'siteVisitStartDate', 'end' => 'siteVisitEndDate'],
                ];

                foreach ($input['calendarDates'] as $calDate) {

                    $calenderDateDetails = $calendarDateMap[$calDate['id']] ?? null;
                    if (!$calenderDateDetails) {
                        continue;
                    }

                    $dates = [
                        'from_date' => $this->parseDateTime($calDate, 'from_date', 'from_time'),
                        'to_date' => $this->parseDateTime($calDate, 'to_date', 'to_time'),
                    ];

                    CalendarDatesDetail::updateCalendarDates($tenderData['id'],$tenderData['company_id'],
                        $calDate['id'], $dates);

                    if (isset($defaultDateMappings[$calenderDateDetails->is_default])) {
                        $map = $defaultDateMappings[$calenderDateDetails->is_default];

                        $dates = [
                            'from_date' => $formattedDatesAndTime[$map['start']] ?? null,
                            'to_date'   => $formattedDatesAndTime[$map['end']] ?? null,
                        ];

                        CalendarDatesDetail::updateCalendarDates($tenderData['id'],$tenderData['company_id'],
                            $calenderDateDetails['id'], $dates);
                    }
                }
            });

            return [
                'success' => true,
                'message' => 'Tender calendar days updated successfully.',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getTenderCalendarValidation($request)
    {
        $input = $request->all();
        $tenderData = TenderMaster::getTenderByUuid($input['tenderCode']);
        $tenderBidEmployeeDetails = SrmTenderBidEmployeeDetails::getTendetBidEmployeeDetails($tenderData['id']);
        $currentDate = date('Y-m-d H:i:s');
        $currentDateFormatted = Carbon::createFromFormat('Y-m-d H:i:s', $currentDate);
        $formatedDates = $this->getFormattedDatesAndTime($input, $tenderData);
        $isCommDateDisable = false;
        $isDateDisable = false;

        if ($tenderData['stage'] == 1) {
            $bidOpeningStartDate = $formatedDates['bidOpeningStartDate'];
            $bidOpeningEndDate = $formatedDates['bidOpeningEndDate'];
        } else if ($tenderData['stage'] == 2) {
            $bidOpeningStartDate = $formatedDates['technicalStartDate'];
            $bidOpeningEndDate = $formatedDates['technicalEndDate'];

            $commercialStartDate = $formatedDates['commercialStartDate'];
            $commercialEndDate = $formatedDates['commercialEndDate'];

            $result1 = $currentDateFormatted->gt($commercialStartDate);
            if ($commercialEndDate == null) {
                $result2 = true;
            } else {
                $result2 = $commercialEndDate->gt($currentDateFormatted);
            }

            if ($result1 && $result2) {
                $isCommDateDisable = true;
            }

        }

        $technicalBidOpened = $currentDateFormatted->gt($bidOpeningStartDate);

        if ($bidOpeningEndDate == null) {
            $result4 = true;
        } else {
            $result4 = $bidOpeningEndDate->gt($currentDateFormatted);
        }

        if ($technicalBidOpened && $result4) {
            $isDateDisable = true;
        }

        $isEmpApprovalActive = $currentDateFormatted->gte($formatedDates['submissionClosingDate']);
        $minApprovalBidOpening = $tenderData['min_approval_bid_opening'];
        $employeeApprovalCount = $tenderBidEmployeeDetails->count();

        $technicalBidValidation = (($technicalBidOpened || $employeeApprovalCount >= $minApprovalBidOpening) || $isEmpApprovalActive);

        $disable = $technicalBidValidation ? false : true;
        $disableCommercial = $technicalBidValidation ? false : true;
        $commercialBidValidation = (!$isCommDateDisable || $minApprovalBidOpening > $employeeApprovalCount);

        $comActive = (!$commercialBidValidation && !$disable) ? true : false;


        return [
            'technicalBidValidation'=> $disable,
            'commercialBidValidation' => $comActive,
            'isTecDisable' => $technicalBidOpened
        ] ;
    }

    public function getCalendarDateAuditLogs($request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $tenderData = TenderMaster::getTenderByUuid($input['tenderCode']);
        if (empty($tenderData)) {
            return ['success' => false, 'message' => 'Tender not found'];
        }
        $calendarDataAuditLog = SRMTenderCalendarLog::getCalenderDatesEditLogs($tenderData['id'],
            $tenderData['company_id'],$input['sort'],$input['isGrouped']);

        return \DataTables::of($calendarDataAuditLog)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function insertCalendarLog($formattedDatesAndTime, $tenderData, $input)
    {
        return DB::transaction(function () use ($formattedDatesAndTime, $tenderData, $input) {

            $calendarDatesExists = SRMTenderCalendarLog::checkCalendarDatesExists(
                $tenderData['id'], $tenderData['company_id']);

            $sort = $calendarDatesExists['sort'] ? $calendarDatesExists['sort'] + 1 : 1;


            $logData = [];
            $baseData = [
                'tender_id' => $tenderData['id'],
                'company_id' => $tenderData['company_id'],
                'created_by' => Helper::getEmployeeSystemID(),
                'created_at' => Helper::currentDateTime(),
                'narration' => $input['comment'],
                'sort' => $sort,
            ];

            $fields = $this->getFieldMappings();

            foreach ($fields as $newField => $info) {
                $oldValue = isset($tenderData[$info['oldField']]) ? $tenderData[$info['oldField']] : null;
                $newValue = isset($formattedDatesAndTime[$newField]) ? $formattedDatesAndTime[$newField] : null;

                $this->compareAndLogChange($logData, $baseData, $info['descriptionDate'], $oldValue, $newValue, 'date');
                $this->compareAndLogChange($logData, $baseData, $info['descriptionTime'], $oldValue, $newValue, 'time');
            }

            if (!empty($input['calendarDates'])) {
                foreach ($input['calendarDates'] as $calDate) {
                    if (($calDate['is_default'] ?? 1) == 0) {
                        $keyBase = $calDate['calendar_date'];

                        $startDateTime = $this->parseDateTime($calDate, 'from_date', 'from_time');
                        $endDateTime   = $this->parseDateTime($calDate, 'to_date', 'to_time');

                        $calendarDate = CalendarDatesDetail::getCalendarDateDetail($tenderData['id'],
                            $tenderData['company_id'], $calDate['id']);

                        if ($calendarDate) {
                            $this->compareAndLogChange($logData, $baseData, "{$keyBase} from date", $calendarDate->from_date, $startDateTime, 'date');
                            $this->compareAndLogChange($logData, $baseData, "{$keyBase} from time", $calendarDate->from_date, $startDateTime, 'time');
                            $this->compareAndLogChange($logData, $baseData, "{$keyBase} to date", $calendarDate->to_date, $endDateTime, 'date');
                            $this->compareAndLogChange($logData, $baseData, "{$keyBase} to time", $calendarDate->to_date, $endDateTime, 'time');
                        }
                    }
                }
            }

            if (!empty($logData)) {
                SRMTenderCalendarLog::insert($logData);
            }
        });
    }


    private function compareAndLogChange(array &$logData, array $baseData, string $fieldDesc, $oldValue, $newValue, string $type)
    {
        $old = $oldValue ? Carbon::parse($oldValue) : null;
        $new = $newValue ? Carbon::parse($newValue) : null;

        $oldFormatted = $old ? $old->format($type === 'date' ? 'Y-m-d' : 'H:i:s') : null;
        $newFormatted = $new ? $new->format($type === 'date' ? 'Y-m-d' : 'H:i:s') : null;

        if ($oldFormatted !== $newFormatted) {
            $logData[] = array_merge($baseData, [
                'filed_description' => $fieldDesc,
                'old_value' => $oldFormatted,
                'new_value' => $newFormatted,
            ]);
        }
    }


    private function getFieldMappings(): array
    {
        return [
            'documentSalesStartDate' => [
                'oldField' => 'document_sales_start_date',
                'descriptionDate' => 'Document Sales from date',
                'descriptionTime' => 'Document Sales from time',
            ],
            'documentSalesEndDate' => [
                'oldField' => 'document_sales_end_date',
                'descriptionDate' => 'Document Sales to date',
                'descriptionTime' => 'Document Sales to time',
            ],
            'submissionOpeningDate' => [
                'oldField' => 'bid_submission_opening_date',
                'descriptionDate' => 'Bid Submission from date',
                'descriptionTime' => 'Bid Submission from time',
            ],
            'submissionClosingDate' => [
                'oldField' => 'bid_submission_closing_date',
                'descriptionDate' => 'Bid Submission to date',
                'descriptionTime' => 'Bid Submission to time',
            ],
            'bidOpeningStartDate' => [
                'oldField' => 'bid_opening_date',
                'descriptionDate' => 'Bid Opening from date',
                'descriptionTime' => 'Bid Opening from time',
            ],

            'bidOpeningEndDate' => [
                'oldField' => 'bid_opening_end_date',
                'descriptionDate' => 'Bid Opening to date',
                'descriptionTime' => 'Bid Opening to time',
            ],

            'technicalStartDate' => [
                'oldField' => 'technical_bid_opening_date',
                'descriptionDate' => 'Technical bid opening from date',
                'descriptionTime' => 'Technical bid opening from time',
            ],

            'technicalEndDate' => [
                'oldField' => 'technical_bid_closing_date',
                'descriptionDate' => 'Technical bid opening to date',
                'descriptionTime' => 'Technical bid opening  to time',
            ],

            'commercialStartDate' => [
                'oldField' => 'commerical_bid_opening_date',
                'descriptionDate' => 'Commercial bid opening from date',
                'descriptionTime' => 'Commercial bid opening from time',
            ],

            'commercialEndDate' => [
                'oldField' => 'commerical_bid_closing_date',
                'descriptionDate' => 'Commercial bid opening to date',
                'descriptionTime' => 'Commercial bid opening to time',
            ],

            'preBidClarificationStartDate' => [
                'oldField' => 'pre_bid_clarification_start_date',
                'descriptionDate' => 'Pre Bid Clarification from date',
                'descriptionTime' => 'Pre Bid Clarification from time',
            ],

            'preBidClarificationEndDate' => [
                'oldField' => 'pre_bid_clarification_end_date',
                'descriptionDate' => 'Pre Bid Clarification to date',
                'descriptionTime' => 'Pre Bid Clarification to time',
            ],

            'siteVisitStartDate' => [
                'oldField' => 'site_visit_date',
                'descriptionDate' => 'Site Visit from date',
                'descriptionTime' => 'Site Visit from time',
            ],

            'siteVisitEndDate' => [
                'oldField' => 'site_visit_end_date',
                'descriptionDate' => 'Site Visit to date',
                'descriptionTime' => 'Site Visit to time',
            ],
        ];
    }

    public function getContractTypes($companySystemID)
    {
        $contractTypes = ContractTypes::getContractTypes($companySystemID);
        return $contractTypes;
    }

    public function createContract($request)
    {
        $input = $request->all();
        $companySystemId = $input['companySystemID'];

        $tenderData = TenderMaster::getTenderByUuid($input['tenderId']);
        if (empty($tenderData)) {
            return ['success' => false, 'message' => 'Tender not found'];
        }

        $contractType = ContractTypes::getContractTypeId($input['contractType']);
        if (empty($contractType)) {
            return ['success' => false, 'message' => 'Contract Type not found'];
        }

        $supplierId = SupplierRegistrationLink::getSupplierMasterId($input['supplierId'], $companySystemId);
        $checkSupplierExists = ContractUsers::checkSupplierExists(
            $supplierId['supplier_master_id'], 1, $companySystemId, 1);

        if (empty($checkSupplierExists)) {
            return ['success' => false, 'message' => 'The supplier does not exist in the contract management masters.
             Please add the supplier to the contract management master'];
        }

        try {
            DB::transaction(function () use ($input, $companySystemId, $tenderData, $contractType) {

                $contractCodeData = $this->generateContractCode($companySystemId);
                $insertArray = [];

                $insertArray = [
                    'contractCode' => $contractCodeData['contractCode'],
                    'title' => $input['title'],
                    'serial_no' => $contractCodeData['lastSerialNumber'],
                    'contractType' => $contractType["contract_typeId"],
                    'counterParty' => $contractType["cmCounterParty_id"],
                    'uuid' => bin2hex(random_bytes(16)),
                    'documentMasterId' => 123,
                    'companySystemID' => $companySystemId,
                    'tender_id' => $tenderData['id'],
                    'created_by' => Helper::getEmployeeSystemID(),
                    'created_at' => Carbon::now()
                ];

                $contractMaster = ContractMaster::create($insertArray);

                if($contractMaster)
                {
                    $data = [
                        'contract_id' => $contractMaster['id'] ?? null,
                    ];

                    TenderMaster::where('uuid',$input['tenderId'])->update($data);

                    $contractMasterId = $contractMaster->id;
                    $this->insertHistoryStatus($contractMasterId, 0,$companySystemId);

                    $contractTypeSections = ContractTypeSections::getContractTypeSections($contractType["contract_typeId"], $companySystemId);

                    foreach ($contractTypeSections as $contractTypeSection) {

                        $contractSettingMasterArray = [
                            'uuid' => bin2hex(random_bytes(16)),
                            'contractId' => $contractMasterId,
                            'contractTypeSectionId' => $contractTypeSection['ct_sectionId'],
                            'isActive' => 0
                        ];
                        ContractSettingMaster::create($contractSettingMasterArray);
                    }

                    $contractTypeSectionDetail = ContractSettingMaster::getContractTypeSectionDetail($contractMasterId);

                    $contractSettingDetailArray = [];
                    $i = 0;

                    foreach ($contractTypeSectionDetail as $contractSectionDetail)
                    {
                        $sectionDetails = $contractSectionDetail['contractTypeSection']['contractSectionWithTypes']['sectionDetail'];

                        foreach ($sectionDetails as $sectionDetail)
                        {
                            $sectionDetailId = $sectionDetail['id'];
                            $contractSettingDetailArray[$i] = [
                                'uuid' => bin2hex(random_bytes(16)),
                                'settingMasterId' => $contractSectionDetail['id'],
                                'sectionDetailId' => $sectionDetailId,
                                'isActive' => 0,
                                'contractId' => $contractMasterId,
                                'created_at' => Carbon::now(),
                            ];
                            $i++;
                        }
                    }

                    ContractSettingDetail::insert($contractSettingDetailArray);

                    $this->assignDefaultUserForContract($contractMasterId, $companySystemId);
                }
            });

            return [
                'success' => true,
                'message' => 'Contract Master Created successfully.',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function generateContractCode($companySystemID)
    {
        $lastSerialNumber = ContractMaster::where('companySystemID', $companySystemID)
            ->max('serial_no');

        $lastSerialNumber = $lastSerialNumber ? intval($lastSerialNumber) + 1 : 1;
        $codePattern = CodeConfigurations::getDocumentCodePattern($companySystemID, 1);
        if($codePattern)
        {
            $companyId = Helper::getCompanyById($companySystemID);
            $contractCode = self::generateDocumentCode($codePattern, $companyId, $lastSerialNumber);
        } else
        {
            $contractCode =  self::generateCode($lastSerialNumber,'CO',4);
        }

        return ['contractCode' => $contractCode, 'lastSerialNumber' => $lastSerialNumber];

    }

    public function generateDocumentCode($pattern, $companyId, $serialNumber = 1)
    {
        $year = Carbon::now()->year;
        $serialNumberFormatted = str_pad($serialNumber, 4, '0', STR_PAD_LEFT);

        preg_match_all('/#_([A-Za-z0-9]+)\b/', $pattern, $matches);
        $prefixes = $matches[1] ?? [];

        $replacements = [
            '#Company ID' => $companyId,
            '#Year' => $year,
            '#SN' => $serialNumberFormatted,
            '#/' => '/',
            '#-' => '-'
        ];

        $prefixReplacements = array();
        foreach ($prefixes as $prefix) {
            $prefixReplacements["#_{$prefix}"] = $prefix;
        }
        $replacements = array_merge($replacements, $prefixReplacements);
        return  strtr($pattern, $replacements);
    }

    public static function generateCode($lastSerialNumber, $documentCode, $length=4) : string
    {
        return $documentCode . str_pad($lastSerialNumber, $length, '0', STR_PAD_LEFT);
    }

    public function viewContract($input)
    {
        $companySystemId = $input['companySystemId'];
        $contractId = $input['contractId'];
        $contractUuid = ContractMaster::getContractUuid($companySystemId, $contractId);

        if (env('IS_MULTI_TENANCY') == true) {
            $url = $_SERVER['HTTP_HOST'];
            $getProtocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
            $protocol = $getProtocol . "://";

            $url_array = explode('.', $url);
            $subDomain = $url_array[0];

            $tenantDomain = explode('-', $subDomain);
            $tenantPrefix = $tenantDomain[0];

            $newSubDomain = $tenantPrefix . '-cms-' . $tenantDomain[2];

            $redirectUrl = str_replace($subDomain, $newSubDomain, $url);

            $redirectUrlNew = rtrim($redirectUrl, '/') . '/contracts/edit/';
        }

        return [
            'contractUrl' => $protocol . $redirectUrlNew . $contractUuid['uuid'],
        ];
    }

    public function addAttachment($request){

        $input = $request->all();
        $companySystemID = $input['companySystemID'];
        $documentSystemID = $input['documentSystemID'];

        $tenderData = TenderMaster::getTenderByUuid($input['tenderId']);
        if (empty($tenderData)) {
            return ['success' => false, 'message' => 'Tender not found'];
        }
        $documentSystemCode = $tenderData['id'];

        $documentMaster = DocumentMaster::getDocumentData($documentSystemID);
        if ($documentMaster) {
            $documentID = $documentMaster->documentID;
        }

        $companyID = Company::getComanyCode($companySystemID);

        try {
            DB::transaction(function () use ($input, $companySystemID, $documentSystemID, $documentSystemCode,
                $documentID, $companyID) {

                if (isset($input['Attachment']) && !empty($input['Attachment'])) {

                    $getAttachmentData = self::getAttachmentData($input['Attachment'], $companySystemID,
                        $documentSystemID, $documentSystemCode, $documentID, $companyID);

                    DocumentAttachments::create($getAttachmentData);
                }

                $evaluationData = SRMTenderTechnicalEvaluationAttachment::getEvaluationData(
                    $companySystemID,$documentSystemCode);

                $data = [
                    'comment' => $input['comment'],
                ];

                if ($evaluationData) {
                    $data += [
                        'updated_by' =>  Helper::getEmployeeSystemID(),
                    ];

                    SRMTenderTechnicalEvaluationAttachment::where('uuid', $evaluationData['uuid'])->update($data);

                } else {

                    $data += [
                        'uuid' => self::generateUuid(),
                        'comment' => $input['comment'],
                        'document_system_id' => $documentSystemID,
                        'document_id' => $documentID,
                        'tender_id' => $documentSystemCode,
                        'company_id' =>  $companySystemID,
                        'created_by' =>  Helper::getEmployeeSystemID(),
                    ];

                    if (isset($input['comment']) && !empty($input['comment'])) {

                        SRMTenderTechnicalEvaluationAttachment::create($data);
                    }
                }
            });

            return [
                'success' => true,
                'message' => 'Technical Evaluation Attachment Created successfully.',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public static function generateUuid($length=16) : string
    {
        return bin2hex(random_bytes($length));
    }

    public static function blockExtensions(){
        return [
            'ace', 'ade', 'adp', 'ani', 'app', 'asp', 'aspx', 'asx', 'bas', 'bat', 'cla', 'cer', 'chm', 'cmd',
            'cnt', 'com', 'cpl', 'crt', 'csh', 'class', 'der', 'docm', 'exe', 'fxp', 'gadget', 'hlp', 'hpj', 'hta',
            'htc', 'inf', 'ins', 'isp', 'its', 'jar', 'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq',
            'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt', 'mdw', 'mdz', 'mht', 'mhtml',
            'msc', 'msh', 'msh1', 'msh1xml', 'msh2', 'msh2xml', 'mshxml', 'msi', 'msp', 'mst', 'ops', 'osd',
            'ocx', 'pl', 'pcd', 'pif', 'plg', 'prf', 'prg', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2',
            'pst', 'reg', 'scf', 'scr', 'sct', 'shb', 'shs', 'tmp', 'url', 'vb', 'vbe', 'vbp', 'vbs', 'vsmacros',
            'vss', 'vst', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xml', 'xbap', 'xnk', 'php'
        ];
    }

    public function getAttachmentData($attachment, $companySystemID, $documentSystemID, $documentSystemCode,
                                      $documentID, $companyID) {
        if (!empty($attachment) && isset($attachment['file'])) {

            $extension = $attachment['fileType'];
            $blockExtensions = self::blockExtensions();
            if (in_array($extension, $blockExtensions)) {
                return $this->sendError('This type of file not allow to upload.', 500);
            }

            if (isset($attachment['sizeInKbs'])) {
                if ($attachment['sizeInKbs'] > 2097152) {
                    return $this->sendError("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.", 500);
                }
            }

            $file = $attachment['file'];
            $decodeFile = base64_decode($file);

            $attch = time() . '_TechnicalEvaluationAttachment.' . $extension;
            $path = $companySystemID . '/TechnicalEvaluation/' . $attch;
            $myFileName = $companyID . '_' . time() . '_TechnicalEvaluation.' . $extension;

            Storage::disk(Helper::policyWiseDisk($companySystemID, 'public'))->put($path, $decodeFile);

            $data = [
                'companySystemID' => $companySystemID,
                'companyID' =>  $companyID,
                'documentSystemID' => $documentSystemID,
                'documentID' => $documentID,
                'documentSystemCode' => $documentSystemCode,
                'attachmentDescription' => 'Tender Technical Evaluation Attachment',
                'path' => $path,
                'originalFileName' => $attachment['originalFileName'],
                'myFileName' => $myFileName,
                'attachmentType' => 11,
                'sizeInKbs' => $attachment['sizeInKbs'],
                'isUploaded' => 1,
            ];

            return $data;
        }
    }

    public function deleteAttachment($request) {

        $input = $request->all();
        $companySystemID = $input['companySystemID'];
        $attachmentId = $input['attachmentId'];

        $attachment = DocumentAttachments::documentAttachmentById($attachmentId);
        if (!$attachment) {
            return ['success' => false, 'message' => 'Attachment not found.'];
        }

        $path = $attachment->path;
        $disk = Helper::policyWiseDisk($companySystemID, 'public');

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }

        $attachment->delete();

        return [
            'success' => true,
            'message' => 'Attachment Deleted successfully.',
        ];
    }

    public static function insertHistoryStatus($contractId, $status, $companySystemID, $contractHistoryId = null,
                                               $systemUser=false)
    {
        $insert = [
            'contract_id' => $contractId,
            'status' => $status,
            'company_id' => $companySystemID,
            'created_at' => Carbon::now()
        ];

        if($systemUser)
        {
            $insert['system_user'] = 1;
        }else
            $insert['created_by'] = Helper::getEmployeeSystemID();

        if ($contractHistoryId!=null)
        {
            $insert['contract_history_id'] = $contractHistoryId;
        }

        ContractStatusHistory::create($insert);
    }

    private function assignDefaultUserForContract($contractId, $companySystemID)
    {
        $defaultUserIds = ContractUserGroup::getDefaultUserIds($companySystemID);

        $userIdsAssignedUserGroup = ContractUserGroupAssignedUser::getUserIdsAssignedUserGroup($defaultUserIds);
        foreach ($userIdsAssignedUserGroup as $user)
        {
            $userGroupId = $user['userGroupId'];
            $userId = $user['contractUserId'];
            $existingRecord = ContractUserAssign::isExistingRecord($contractId, $userGroupId, $userId);

            if (!$existingRecord)
            {
                $input = [
                    'uuid' => bin2hex(random_bytes(16)),
                    'contractId' => $contractId,
                    'userGroupId' => $userGroupId,
                    'userId' => $userId,
                    'status' => 1,
                    'createdBy' => Helper::getEmployeeSystemID(),
                    'updated_at' => null
                ];

                ContractUserAssign::create($input);
            }
        }
    }

    public function getTenderTypeData($request)
    {
        $input = $request->all();
        $tenderTypes =  TenderType::getTenderTypeData();

        $additionalRecord = [
            'id' => -1,
            'name' => 'General'
        ];

        return collect([$additionalRecord])->merge($tenderTypes);


    }

    public function checkAssignSuppliers($companyId, $id, $rfq, $checkRecordExist, $editOrAmend, $versionID)
    {
        $assignSupplier =  $editOrAmend ?
            TenderSupplierAssigneeEditLog::getAssignSupplierCount($companyId, $id, $versionID) :
            TenderSupplierAssignee::getAssignSupplierCount($companyId, $id);

        if($checkRecordExist) {
            if($assignSupplier == 0){
                return [
                    'success' => false,
                    'message' => 'At least one supplier should be added'
                ];
            }
        } else {
            $type = $rfq ? 'RFX' : 'Tender';
            if ($assignSupplier != 1) {
                return [
                    'success' => false,
                    'message' => 'Single Sourcing ' .$type. ' allows only one supplier. Please remove
                                         additional suppliers before confirming'];
            }
        }
        return ['success' => true];
    }

    public function checkRankingEmptyRecords($request)
    {
        $input = $request->all();
        $tenderId = $input['tenderId'];
        $isEmptyRecordsExists = BidSubmissionDetail::BidSubmissionEmpty($tenderId);
        if($isEmptyRecordsExists )
        {
            DB::transaction(function () use ($tenderId) {
                BidSubmissionDetail::where('tender_id', $tenderId)
                    ->update(['technical_ranking' => null]);
            });
        }

    }

    public function validateTechnicalEvaluationCriteria($tenderId, $level, $criteriaType)
    {
        $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tenderId);

        $criteriaDetails = $requestData['enableRequestChange']
            ? EvaluationCriteriaDetailsEditLog::getEvaluationCriteriaDetailsLog($tenderId, $level, $criteriaType, $requestData['versionID'])
            : EvaluationCriteriaDetails::getEvaluationCriteriaDetails($tenderId, $level, $criteriaType);

        if ($criteriaDetails->exists()) {
            $totalWeightage = $criteriaDetails->sum('weightage');
            if ($totalWeightage != 100) {
                return [
                    'success' => false,
                    'message' => 'Total of the Technical Evaluation Criteria percentage should be equal to 100'
                ];
            }
        }

        return ['success' => true];
    }

    public function loadTenderSubActivity($input){
        $tenderMasterID = $input['tenderMasterId'];
        $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tenderMasterID);
        $editOrAmend =  $requestData['enableRequestChange'];
        $tenderHistoryData = $requestData['tenderMasterHistory'];
        $companySystemID = $input['companySystemID'];

        $tenderMaster = $editOrAmend ? $tenderHistoryData : TenderMaster::getTenderMasterData($tenderMasterID);
        $data['procurementSubCategory'] = [];
        if ($input['procument_cat_id'] > 0) {
            $data['procurementSubCategory'] = TenderProcurementCategory::getTenderProcurementCat($input['procument_cat_id']);
        }


        $activity = $editOrAmend ? ProcumentActivityEditLog::getTenderProcurements($tenderMasterID, $companySystemID, $requestData['versionID']) :
            ProcumentActivity::getProcumentActivityForAmd($input['tenderMasterId']);

        if ($tenderMaster['confirmed_yn'] == 1 && count($activity) > 0) {
            foreach ($activity as $vl) {
                $category = TenderProcurementCategory::getTenderProcurementCatDrop($vl['category_id']);
                if ($category['is_active'] == 0) {
                    $data['procurementSubCategory'][] = $category;
                }
            }
        }
        return $data;
    }
    public function getBudgetItemTotalAmount($input){
        $tenderMasterId = $input['tenderMasterId'];
        $companySystemID = $input['companySystemID'];
        $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tenderMasterId);
        // Get the budget amount for each item in the idList
        return collect($input['idList'])->map(function($itemId) use ($tenderMasterId, $companySystemID, $requestData) {
            $existingItem = $requestData['enableRequestChange'] ?
                TenderBudgetItemEditLog::getExistingBudgetItem($itemId, $tenderMasterId, $requestData['versionID']) :
                SrmTenderBudgetItem::getExistingBudgetItem($itemId, $tenderMasterId);

            if ($existingItem) {
                return $existingItem->budget_amount;
            } else {
                $budgetItem = SrmBudgetItem::getSrmBudgetItem($itemId, $companySystemID);
                return $budgetItem ? $budgetItem->budget_amount : 0;
            }
        })->sum();
    }
    public function getTenderMasterData($input){
        $tenderMasterId = $input['tenderMasterId'];
        $companySystemID = $input['companySystemID'];
        $isTender = isset ($input['isTender']) && $input['isTender'] ? 108 : 113;
        $editOrAmendRequest = false;

        $tenderMaster = TenderMaster::getEditTenderMasterData($tenderMasterId, $companySystemID, $isTender);
        $data['master'] = $tenderMaster;

        $checkHasTenderRequest = DocumentModifyRequest::getTenderModifyRequest($tenderMasterId);
        $checkEditAmendCondition = [];
        if ($data['master']['published_yn'] == 1) {
            $checkEditAmendCondition = $this->srmDocumentModifyService->checkConditions($tenderMasterId, $data['master']);
        }

        $hasOpeningOrClosingCheck = ($checkEditAmendCondition['checkOpeningDate'] ?? false) || ($checkEditAmendCondition['checkClosingDate'] ?? false);
        $hasValidTenderRequest = !empty($checkHasTenderRequest)
            && $checkHasTenderRequest->status == 1
            && $checkHasTenderRequest->approved != 0
            && $checkHasTenderRequest->confirmation_approved != -1;

        if ($hasOpeningOrClosingCheck && $hasValidTenderRequest) {
            $data['master'] = SrmTenderMasterEditLog::getEditTenderMasterData($tenderMasterId, $companySystemID, $isTender);
            $editOrAmendRequest = true;
        }

        $versionID = $data['master']['version_id'] ?? 0;
        $data['version_id'] = $versionID;

        $activity = $editOrAmendRequest ?
            ProcumentActivityEditLog::getTenderProcurements($tenderMasterId, $companySystemID, $versionID) :
            ProcumentActivity::getTenderProcurements($tenderMasterId, $companySystemID);

        $act = array();
        if (!empty($activity)) {
            foreach ($activity as $vl) {
                $dt['id'] = $vl['tender_procurement_category']['id'];
                $dt['itemName'] = $vl['tender_procurement_category']['code'] . ' | ' . $vl['tender_procurement_category']['description'];
                array_push($act, $dt);
            }
        }

        $data['master']['comment'] = SRMTenderCalendarLog::getNarration($input['tenderMasterId'], $companySystemID);

        $showEditIcon = false;
        if ($data['master']['commercial_ranking_line_item_status'] == 0  && $data['master']['is_negotiation_started'] == 0 && $data['master']['approved'] == -1) {
            $showEditIcon = true;
        }

        $data['master']['canEdit'] = $showEditIcon;
        $data['activity'] = $act;

        $qryAll = CalendarDates::getCalendarDateDatesQry($editOrAmendRequest, $tenderMasterId, $companySystemID, $versionID);

        if ($data['master']['published_yn'] == 1) {
            $serviceResp = $this->srmDocumentModifyService->getDocumentModifyRequestForms($tenderMasterId, $tenderMaster);
            $data = array_merge($data, $serviceResp);
        } else {
            $data['conditions'] = null;
            $data['changesRequestStatus'] = null;
            $data['requestType'] = null;
            $data['editable'] = true;
            $data['amendment'] = true;
            $data['enableChangeRequest'] = false;
            $data['requestedToEditAmend'] = false;
            $data['confirmedEditRequest'] = false;
        }

        if($data['enableChangeRequest'] || $data['confirmedEditRequest']){
            unset($data['master']['confirmed_by']);
            $data['master']['confirmed_date'] = null;
        }

        $calenderDataDetails = $editOrAmendRequest ?
            CalendarDatesDetailEditLog::getCalenderDateDetailEdit($tenderMasterId, $versionID, $companySystemID) :
            CalendarDatesDetail::getCalenderDateDetailEdit($tenderMasterId, $companySystemID);

        $dataArray = array();
        $i = 0;

        foreach ($calenderDataDetails as $calenderDataDetail) {

            $fromDate = $calenderDataDetail->from_date;
            $toDate = $calenderDataDetail->to_date;
            $calenderDate = CalendarDates::find($calenderDataDetail->calendar_date_id);

            $dataArray[$i]['id'] = $calenderDate->id;
            $dataArray[$i]['calendar_date'] = $calenderDate->calendar_date;
            $dataArray[$i]['is_default'] = $calenderDate->is_default;
            $dataArray[$i]['company_id'] = $calenderDataDetail->company_id;
            $dataArray[$i]['from_date'] = $fromDate->format('Y-m-d H:i:s');
            $dataArray[$i]['to_date'] = isset($toDate) ? $toDate->format('Y-m-d H:i:s') : null;
            $dataArray[$i]['from_time'] = $calenderDataDetail->from_time;
            $dataArray[$i]['to_time'] = isset($toDate) ? $calenderDataDetail->to_time : null;
            $i++;
        }

        $data['calendarDates'] = collect($dataArray);
        $data['calendarDatesAll'] =  DB::select($qryAll);


        $documentTypes = $editOrAmendRequest ?
            TenderDocumentTypeAssignLog::getTenderDocumentTypeAssign($tenderMasterId, $versionID) :
            TenderDocumentTypeAssign::getTenderDocumentTypeAssign($tenderMasterId);

        $docTypeArr = array();
        if (!empty($documentTypes)) {
            foreach ($documentTypes as $vl) {
                $dt['id'] = $vl['document_type']['id'];
                $dt['itemName'] = $vl['document_type']['document_type'];
                array_push($docTypeArr, $dt);
            }
        }
        $data['documentTypes'] = $docTypeArr;

        // Get Purchase Request Data
        $data['purchaseRequest'] = PurchaseRequest::getPurchaseRequestData($companySystemID, $tenderMasterId, $data['master']['document_type']);


        // Get Tender Purchase Request Data
        $tenderPurchaseRequestList = $editOrAmendRequest ?
            TenderPurchaseRequestEditLog::getTenderPurchaseForEdit($tenderMasterId, $versionID) :
            TenderPurchaseRequest::getTenderPurchaseForEdit($tenderMasterId);

        $data['tenderPurchaseRequestList'] = $tenderPurchaseRequestList;

        // Get the data from srm_tender_budget_items if it exists, otherwise from srm_budget_items
        $tableName = $editOrAmendRequest
            ? 'srm_tender_budget_items_edit_log'
            : 'srm_tender_budget_items';

        $data['srmBudgetItem'] = SrmTenderBudgetItem::getTenderBudgetItems($tableName, $tenderMasterId, $companySystemID, $editOrAmendRequest, $versionID);

        $srmBudgetItemList = SrmBudgetItem::getSrmBudgetItemList($tenderMasterId, $companySystemID, $editOrAmendRequest, $versionID);

        $data['srmBudgetItemList'] = $srmBudgetItemList;

        // Get Department Master Data
        $departmentMaster = SrmDepartmentMaster::where('company_id', $companySystemID)->where('is_active', 1)->get();
        $data['departmentMaster'] = $departmentMaster;

        // Get Tender Department
        $tenderdepartment = $editOrAmendRequest ?
            TenderDepartmentEditLog::getTenderDepartmentLogList($tenderMasterId, $versionID) :
            SrmTenderDepartment::getTenderDepartmentList($tenderMasterId);

        $data['tenderdepartment'] = $tenderdepartment;


        //check prebid Clarification Added
        $prebidclarificationDateId = CalendarDates::getDefaultCalendarDate(1);
        $prebidclarificationDateCount = $editOrAmendRequest ?
            CalendarDatesDetailEditLog::getCalenderDateDetailEdit($tenderMasterId, $versionID, $companySystemID, $prebidclarificationDateId->id) :
            CalendarDatesDetail::getCalenderDateDetailEdit($tenderMasterId, $companySystemID, $prebidclarificationDateId->id);

        //check Site Visit Date Added
        $siteVisitDateId = CalendarDates::getDefaultCalendarDate(2);
        $siteVisitDateCount =$editOrAmendRequest ?
            CalendarDatesDetailEditLog::getCalenderDateDetailEdit($tenderMasterId, $versionID, $companySystemID, $siteVisitDateId->id) :
            CalendarDatesDetail::getCalenderDateDetailEdit($tenderMasterId, $companySystemID, $siteVisitDateId->id);


        $data['hasPreBidClarifications'] = $prebidclarificationDateCount;
        $data['prebidclarificationDateId'] = $prebidclarificationDateId->id;
        $data['hasSiteVisitDate'] = $siteVisitDateCount;
        $data['siteVisitDateId'] = $siteVisitDateId->id;
        return $data;
    }
    public function removeCalendarDates(Request $request){
        return DB::transaction(function () use ($request)
        {

            $is_default = $request->input('is_default') ?? 0;
            $calenderDateTypeId = $request->input('calenderDateTypeId') ?? 0;
            $tenderMasterId = $request->input('tenderMasterId') ?? 0;
            $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tenderMasterId);
            $amdID = $requestData['enableRequestChange'] && !empty($requestData['tenderMasterHistory']) ?
                $requestData['tenderMasterHistory']['amd_id'] : 0;

            if($is_default != 0){
                $calendarDates = CalendarDates::getDefaultCalendarDate($is_default);
                $calenderDateTypeId = $calendarDates->id;

                $tenderMaster = $requestData['enableRequestChange'] ?
                    SrmTenderMasterEditLog::find($amdID) : TenderMaster::find($tenderMasterId);

                if($is_default == 1){
                    $tenderMaster->pre_bid_clarification_start_date = null;
                    $tenderMaster->pre_bid_clarification_end_date = null;
                    $tenderMaster->save();
                }

                if($is_default == 2){
                    $tenderMaster->site_visit_date = null;
                    $tenderMaster->site_visit_end_date = null;
                    $tenderMaster->save();
                }
            }
            $companyID = $tenderMaster->company_id;
            $calendarDatesDetail = $requestData['enableRequestChange'] ?
                CalendarDatesDetailEditLog::getCalenderDateDetailEdit($tenderMasterId, $requestData['versionID'], $companyID, $calenderDateTypeId) :
                CalendarDatesDetail::getCalenderDateDetailEdit($tenderMasterId, $companyID, $calenderDateTypeId);

            if (empty($calendarDatesDetail)) {
                return [
                    'success' => false,
                    'message' => 'Calendar Date Type not found'
                ];
            }
            $calendarDateDetail = $requestData['enableRequestChange'] ?
                CalendarDatesDetailEditLog::getCalendarDateDetailForAmd($tenderMasterId, $companyID, $calenderDateTypeId, $requestData['versionID']) :
                CalendarDatesDetail::getCalendarDateDetail($tenderMasterId, $companyID, $calenderDateTypeId);

            if($requestData['enableRequestChange']){
                $calendarDateDetail->is_deleted = 1;
                $calendarDateDetail->save();
            } else {
                $calendarDateDetail->delete();
            }
            return ['success' => true, 'message' => 'Successfully Deleted'];
        });
    }
    public function deleteCalenderDetails($id, $company_id, $requestData)
    {
        try {
            return DB::transaction(function () use ($id, $company_id, $requestData){
                $details = $requestData['enableRequestChange'] ?
                    CalendarDatesDetailEditLog::getCalenderDateDetailEdit($id, $requestData['versionID'], $company_id, 0) :
                    CalendarDatesDetail::getCalenderDateDetailEdit($id, $company_id, 0);

                foreach ($details as $val) {
                    $calender = $requestData['enableRequestChange'] ?
                        CalendarDatesDetailEditLog::find($val->amd_id) :
                        CalendarDatesDetail::find($val->id);
                    if($calender){
                        if($requestData['enableRequestChange']){
                            $calender->is_deleted = 1;
                            $calender->save();
                        } else {
                            $calender->delete();
                        }
                    }
                }
                return ['success' => true, 'message' => 'Successfully Deleted'];
            });
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function checkTenderBidEmployeesAdded($tenderMasterID, $editOrAmend, $amdID, $versionID){
        $tenderMaster = $editOrAmend ? SrmTenderMasterEditLog::find($amdID) : TenderMaster::find($tenderMasterID);
        if(empty($tenderMaster)) {
            return ['success' => false, 'message' => "Tender not found"];
        }
        $tenderBidEmployee = $editOrAmend ?
            SrmTenderBidEmployeeDetailsEditLog::getTenderBidEmployeesAmd($tenderMasterID, $versionID) :
            SrmTenderBidEmployeeDetails::getTenderBidEmployees($tenderMasterID);
        if(count($tenderBidEmployee) < $tenderMaster->min_approval_bid_opening){
            return ['success' => false, 'message' => "Atleast " . $tenderMaster->min_approval_bid_opening . " employee should selected"];
        }
        return ['success' => true, 'message' => "Success"];
    }

    public function getTenderExistData($tenderID, $editOrAmend, $versionID){
        return $editOrAmend ? SrmTenderMasterEditLog::tenderMasterHistory($tenderID, $versionID) :
            TenderMaster::getTenderMasterData($tenderID);
    }
    public function updateTenderMaster($updateData, $tenderMasterID, $editOrAmend, $versionID){
        try{
            return DB::transaction( function () use ($updateData, $tenderMasterID, $editOrAmend, $versionID) {
                if($editOrAmend){
                    SrmTenderMasterEditLog::where('id', $tenderMasterID)->where('version_id', $versionID)->update($updateData);
                } else {
                    TenderMaster::where('id', $tenderMasterID)->update($updateData);
                }
                return ['success' => true, 'message' => 'Updated successfully'];
            });
        } catch (\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
    public function updateProcurementActivity($procurementActivity, $tenderID, $companyID, $employee, $editOrAmend, $versionID){
        try{
            return DB::transaction( function () use ($procurementActivity, $tenderID, $companyID, $employee, $editOrAmend, $versionID){
                if(!empty($procurementActivity) && count($procurementActivity) > 0){
                    $deleteExistData = self::deleteProcurementActivity($tenderID, $companyID, $editOrAmend, $versionID);
                    if(!$deleteExistData['success']){
                        return $deleteExistData;
                    }
                    foreach ($procurementActivity as $vl) {
                        $activity['tender_id'] = $tenderID;
                        $activity['category_id'] = $vl['id'];
                        $activity['company_id'] = $companyID;
                        $activity['created_at'] = Carbon::now();
                        if($editOrAmend){
                            $activity['id'] = null;
                            $activity['version_id'] = $versionID;
                            $activity['level_no'] = 1;
                            ProcumentActivityEditLog::create($activity);
                        } else {
                            $activity['created_by'] = $employee->employeeSystemID;
                            ProcumentActivity::create($activity);
                        }
                    }
                    return ['success' => true, 'message' => 'Procurement Activity created successfully'];
                } else {
                    return self::deleteProcurementActivity($tenderID, $companyID, $editOrAmend, $versionID);
                }
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
    public function deleteProcurementActivity($tenderID, $companyID, $editOrAmend, $versionID)
    {
        try {
            return DB::transaction( function () use ($tenderID, $companyID, $editOrAmend, $versionID){
                $proActivity = $editOrAmend ?
                    ProcumentActivityEditLog::getTenderProcurements($tenderID, $companyID, $versionID) :
                    ProcumentActivity::getTenderProcurements($tenderID, $companyID);

                $proActivity->each(function($record) use($editOrAmend, $versionID){
                    if($editOrAmend){
                        $record->is_deleted = 1;
                        $record->save();
                    } else {
                        $record->delete();
                    }
                });
                return ['success' => true, 'message' => 'Successfully Deleted'];
            });

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function addTenderSiteVisitDate($tenderMasterID, $companyID, $site_visit_date, $employee, $versionID, $editOrAmend){
        try{
            return DB::transaction(function () use ($tenderMasterID, $companyID, $site_visit_date, $employee, $versionID, $editOrAmend) {
                $site = [
                    'tender_id' => $tenderMasterID,
                    'date' => $site_visit_date,
                    'company_id' => $companyID,
                    'created_by' => $employee->employeeSystemID
                ];

                if($editOrAmend){
                    $site['id'] = null;
                    $site['version_id'] = $versionID;
                    $site['level_no'] = 1;
                    TenderSiteVisitDateEditLog::create($site);
                } else {
                    TenderSiteVisitDates::create($site);
                }
                return ['success' => true, 'message' => 'Site visit date created successfully'];
            });
        } catch (\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
    public function tenderPurchaseRequestUpdate($tenderPurchaseRequestData, $tenderID, $companyID, $editOrAmend, $versionID){
        try{
            return DB::transaction(function () use ($tenderPurchaseRequestData, $tenderID, $companyID, $editOrAmend, $versionID) {
                if ($editOrAmend) {
                    TenderPurchaseRequestEditLog::where('tender_id', $tenderID)
                        ->where('version_id', $versionID)
                        ->where('is_deleted', 0)
                        ->update(['is_deleted' => 1]);
                } else {
                    TenderPurchaseRequest::where('tender_id', $tenderID)->delete();
                }
                foreach ($tenderPurchaseRequestData as $pr) {

                    $data = [
                        'tender_id' => $tenderID,
                        'purchase_request_id' => $pr['id'],
                        'company_id' => $companyID,
                    ];
                    if($editOrAmend){
                        $data['id'] = null;
                        $data['version_id'] = $versionID;
                        $data['level_no'] = 1;
                        TenderPurchaseRequestEditLog::create($data);
                    } else{
                        TenderPurchaseRequest::create($data);
                    }
                }
                return ['success' => true, 'message' => 'Tender purchase created successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
    public function updateTenderBudgetItems($budgetItemList, $tenderID, $companyID, $editOrAmend, $versionID){
        try{
            return DB::transaction(function () use ($budgetItemList, $tenderID, $companyID, $editOrAmend, $versionID) {
                if(!empty($budgetItemList)){
                    $existingItems = $editOrAmend ?
                        TenderBudgetItemEditLog::getExistingTenderBudgetItemList($tenderID, $versionID)->pluck('item_id')->toArray() :
                        SrmTenderBudgetItem::getTenderBudgetItemForAmd($tenderID)->pluck('item_id')->toArray();
                    $itemsToDelete = array_diff($existingItems, array_column($budgetItemList, 'id'));

                    $editOrAmend ?
                        TenderBudgetItemEditLog::where('tender_id', $tenderID)->where('version_id', $versionID)->where('is_deleted', 0)->whereIn('item_id', $itemsToDelete)->update(['is_deleted' => 1]) :
                        SrmTenderBudgetItem::where('tender_id', $tenderID)->whereIn('item_id', $itemsToDelete)->delete();

                    foreach ($budgetItemList as $pr) {
                        $existingBudgetItem = $editOrAmend ?
                            TenderBudgetItemEditLog::getExistingBudgetItem($pr['id'], $tenderID, $versionID) :
                            SrmTenderBudgetItem::getExistingBudgetItem($pr['id'], $tenderID);

                        if ($existingBudgetItem) {
                            $budget_amount = $existingBudgetItem->budget_amount;
                        } else {
                            $srmBudgetItem = SrmBudgetItem::getSrmBudgetItem($pr['id'], $companyID);
                            $budget_amount = $srmBudgetItem ? $srmBudgetItem->budget_amount : 0;
                        }

                        $data = [
                            'item_id' => $pr['id'],
                            'tender_id' => $tenderID,
                            'budget_amount' => $budget_amount,
                            'created_at' => now()
                        ];
                        if($editOrAmend){
                            $data['id'] = null;
                            $data['version_id'] = $versionID;
                            $data['level_no'] = 1;
                            TenderBudgetItemEditLog::updateOrCreate(
                                [
                                    'item_id' => $pr['id'],
                                    'tender_id' => $tenderID,
                                    'version_id' => $versionID,
                                    'is_deleted' => 0
                                ], $data);

                        } else {
                            SrmTenderBudgetItem::updateOrCreate(['item_id' => $pr['id'], 'tender_id' => $tenderID], $data);
                        }
                    }
                }
                return ['success' => true, 'message' => 'Tender budget item created successfully'];
            });
        } catch (\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
    public function updateTenderDepartments($departmentMaster, $company_id, $tenderID, $editOrAmend, $versionID){
        try{
            return DB::transaction(function () use ($departmentMaster, $company_id, $tenderID, $editOrAmend, $versionID) {
                $getInactiveDepartments = SrmDepartmentMaster::getDepartmentMaster($company_id);

                $convertedArray = [];
                foreach ($getInactiveDepartments as $item) {
                    $convertedArray[] = [
                        'id' => $item['id'],
                        'itemName' => $item['description'],
                    ];
                }

                $id1 = array_column($departmentMaster, 'id');
                $id2 = array_column($convertedArray, 'id');
                $commonIds = array_intersect($id1, $id2);

                if (!empty($commonIds)) {
                    return ['success' => false, 'message' => 'Selected Department is currently deactivated in Masters. Please activate it or remove it from your selection to proceed.'];
                } else {
                    $existDepartmentMaster = $editOrAmend ?
                        TenderDepartmentEditLog::getTenderDepartmentEditLog($tenderID, $versionID) :
                        SrmTenderDepartment::getTenderDepartmentEditLog($tenderID);
                    $departmentMasterCount = count($existDepartmentMaster);

                    if ($departmentMasterCount > 0 && count($departmentMaster) > 0){
                        $editOrAmend ?
                            TenderDepartmentEditLog::where('tender_id', $tenderID)->where('version_id', $versionID)->where('is_deleted', 0)->update(['is_deleted' => 1]) :
                            SrmTenderDepartment::where('tender_id', $tenderID)->delete();
                    }
                    foreach ($departmentMaster as $dm) {
                        $data = [
                            'tender_id' => $tenderID,
                            'department_id' => $dm['id'],
                            'company_id' => $company_id,
                        ];
                        if($editOrAmend){
                            $data['id'] = null;
                            $data['version_id'] = $versionID;
                            $data['level_no'] = 1;
                            TenderDepartmentEditLog::create($data);
                        } else {
                            SrmTenderDepartment::create($data);
                        }
                    }
                }
                return ['success' => true, 'message' => 'Tender department created successfully'];
            });
        } catch (\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
    public function getCalendarDateDetailsData($tenderID, $editOrAmend, $versionID){
        return $editOrAmend ?
            CalendarDatesDetailEditLog::getCalendarDateDetailsRecord($tenderID, $versionID) :
            CalendarDatesDetail::getCalendarDateDetailsRecord($tenderID);
    }

    public function getTenderDropdowns($input){
        $isRequestForEdit = false;
        $requestConfirmed = false;
        $versionID = 0;
        $tenderMasterID = $input['tenderMasterId'] ?? 0;

        if($tenderMasterID > 0) {
            $requestData = DocumentModifyRequest::getTenderModifyRequest($input['tenderMasterId']);
            if(!empty($requestData)){
                if($requestData->status == 1 && $requestData->approved != 0 && $requestData->confirmation_approved != -1){
                    $isRequestForEdit = true;
                }
                if($requestData->modify_type == 2 && $requestData->confirmation_approved != -1){
                    $requestConfirmed = true;
                }
            }

            $tenderMaster = self::getTenderExistData($tenderMasterID, $isRequestForEdit, 0);
            $versionID = $tenderMaster['version_id'] ?? 0;
            $category['is_active'] = 1;
            if(!empty($tenderMaster['procument_cat_id'])){
                $category = TenderProcurementCategory::getTenderProcurementCatDrop($tenderMaster['procument_cat_id']);
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
        $data['procurementCategory'] = TenderProcurementCategory::getAllProcurementCategory();

        if($tenderMasterID > 0){
            $assignedDocsArray = $isRequestForEdit
                ? TenderDocumentTypeAssignLog::getAssignedDocs($tenderMasterID, $versionID)->pluck('document_type_id')->toArray()
                : TenderDocumentTypeAssign::getTenderDocumentTypeAssign($tenderMasterID)->pluck('document_type_id')->toArray();

            $notInArray = array_merge([1, 2], $assignedDocsArray);
            $data['documentTypes'] = TenderDocumentTypes::getFilteredDocumentTypes(
                $notInArray,
                $employee->empCompanySystemID,
                $tenderMaster['published_yn'] === 1,
                $isRequestForEdit,
                $requestConfirmed
            );

            if ($tenderMaster['confirmed_yn'] == 1 && $category['is_active'] == 0) {
                $data['procurementCategory'][] = $category;
            }
        }
        return $data;
    }
    public function updateTenderStrategy($input){
        try{
            return DB::transaction(function () use ($input) {
                $tenderMasterID = $input['id'];
                $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tenderMasterID);
                $employee = Helper::getEmployeeInfo();

                $tenderBidEmployee = $requestData['enableRequestChange'] ?
                    SrmTenderBidEmployeeDetailsEditLog::getTenderBidEmployeesAmd($tenderMasterID, $requestData['versionID']) :
                    SrmTenderBidEmployeeDetails::getTenderBidEmployees($tenderMasterID);

                if (($input['min_approval_bid_opening'] != 0)) {
                    if (count($tenderBidEmployee) < $input['min_approval_bid_opening']) {
                        return ['status' => false, 'message' => "Atleast " . $input['min_approval_bid_opening'] . " employee should selected"];
                    }
                }

                $data['tender_type_id'] = $input['tender_type_id'];
                $data['envelop_type_id'] = (empty($input['envelop_type_id'])) ? 0 : $input['envelop_type_id'];
                $data['evaluation_type_id'] = $input['evaluation_type_id'];
                $data['stage'] = $input['stage'];
                $data['no_of_alternative_solutions'] = $input['no_of_alternative_solutions'];
                $data['commercial_weightage'] = $input['commercial_weightage'];
                $data['technical_weightage'] = $input['technical_weightage'];
                $data['is_active_go_no_go'] = $input['is_active_go_no_go'] ?? 0;
                $data['technical_passing_weightage'] = $input['technical_passing_weightage'];
                $data['commercial_passing_weightage'] = $input['commercial_passing_weightage'];
                $data['min_approval_bid_opening'] = $input['min_approval_bid_opening'];
                $updateTender = self::updateTenderMaster($data, $tenderMasterID, $requestData['enableRequestChange'], $requestData['versionID']);

                if ($updateTender['success'] && !$requestData['enableRequestChange']) {
                    if (isset($input['document_types'])) {
                        if (count($input['document_types']) > 0) {
                            TenderDocumentTypeAssign::where('tender_id', $input['id'])->where('company_id', $input['company_id'])->delete();
                            foreach ($input['document_types'] as $vl) {
                                $docTypeAssign['tender_id'] = $input['id'];
                                $docTypeAssign['document_type_id'] = $vl['id'];
                                $docTypeAssign['company_id'] = $input['company_id'];
                                $docTypeAssign['created_by'] = $employee->employeeSystemID;
                                TenderDocumentTypeAssign::create($docTypeAssign);
                            }
                        } else {
                            TenderDocumentTypeAssign::where('tender_id', $input['id'])->where('company_id', $input['company_id'])->delete();
                        }
                    }
                }
                return ['success' => true, 'message' => 'Updated successfully'];
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $ex->getMessage()];
        }
    }
    public function saveSupplierAssigned($input){
        try{
            return DB::transaction(function () use ($input) {
                $companySystemId = $input['companySystemID'];
                $pullList = $input['pullList'];
                $removedSuppliersId = $input['removedSuppliersId'];
                $selectAll = $input['selectAll'];
                $tenderId = $input['tenderId'];
                $employee = Helper::getEmployeeInfo();
                $data = [];
                $insertSupplierAssignee = false;

                $validation = self::checkTenderSupplierAssigneeValid($input);
                if(!$validation['success']){
                    return $validation;
                }

                $tenderMaster = TenderMaster::find($tenderId);
                if(empty($tenderMaster)){
                    return ['success' => false, 'message' => 'Tender not found'];
                }

                $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tenderId);
                $editOrAmend = $requestData['enableRequestChange'] ?? false;
                $versionID = $requestData['versionID'] ?? 0;

                if (!empty($pullList)) {
                    if ($tenderMaster['tender_type_id'] != 3 && $selectAll == true) {
                        $deleteData = self::deleteTenderSupplierAssignee($tenderId, $editOrAmend, $versionID);
                        if(!$deleteData['success']){
                            return $deleteData;
                        }

                        $pullList = SupplierAssigned::tenderAssignSuppliersForCreation(
                            $tenderId, $removedSuppliersId, $companySystemId, $editOrAmend, $versionID
                        );
                    }

                    foreach ($pullList as $key => $val) {
                        $data[$key] = [
                            'tender_master_id' => $tenderId,
                            'supplier_assigned_id' => $val,
                            'created_by' => $employee->employeeSystemID,
                            'company_id' => $companySystemId,
                            'created_at' => Helper::currentDateTime()
                        ];
                        if($editOrAmend){
                            $data[$key]['id'] = null;
                            $data[$key]['version_id'] = $versionID;
                            $data[$key]['level_no'] = 1;
                        }
                    }

                    $insertSupplierAssignee = $editOrAmend ?
                        TenderSupplierAssigneeEditLog::insert($data) :
                        TenderSupplierAssignee::insert($data);
                }
                if ($insertSupplierAssignee) {
                    return ['success' => true, 'message' => 'New supplier(s) added'];
                } else {
                    return ['success' => false, 'message' => 'Insertion failed', 'code' => 422];
                }
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    private function deleteTenderSupplierAssignee($tenderID, $editOrAmend, $versionID){
        try {
            return DB::transaction(function () use ($tenderID, $editOrAmend, $versionID) {
                if($editOrAmend){
                    TenderSupplierAssigneeEditLog::where('version_id', $versionID)
                        ->where('is_deleted', 0)
                        ->where('tender_master_id', $tenderID)
                        ->whereNotNull('supplier_assigned_id')
                        ->where('mail_sent', 0)
                        ->update(['is_deleted' => 1]);

                } else {
                    TenderSupplierAssignee::where('tender_master_id', $tenderID)
                        ->whereNotNull('supplier_assigned_id')->where('mail_sent', 0)
                        ->delete();
                }
                return ['success' => true, 'message' => 'Record(s) deleted successfully.'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
    private function checkTenderSupplierAssigneeValid($input){
        $messages = array(
            'pullList.required'   => 'Please select the supplier(s).',
        );

        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'pullList' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return ['success' => false, 'message' => $validator->messages(), 'code' => 422];
        }
        return ['success' => true, 'message' => 'Validation check done successfully'];
    }
    public function getSupplierAssignedList(Request $request){
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $companyId = $input['companyId'];
        $tenderMasterId =  $input['tenderMasterId'];

        $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tenderMasterId);
        $versionID = $requestData['versionID'] ?? 0;
        $enableRequestChange = $versionID > 0;

        $qry = $enableRequestChange ?
            TenderSupplierAssigneeEditLog::getSupplierAssignedListQry($companyId, $tenderMasterId, $requestData['versionID']) :
            TenderSupplierAssignee::getSupplierAssignedListQry($companyId, $tenderMasterId);

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
    public function getSupplierList(Request $request){
        $input = $request->all();
        $selectedCategoryIds = array();
        $selectedCompanyId = $input['companyId'];
        $tenderMasterId = $input['tenderMasterId'];
        $selectedCategories = $input['selectedCategories'];

        foreach ($selectedCategories as $selectedCategory) {
            $selectedCategoryIds[] = $selectedCategory['id'];
        }

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tenderMasterId);
        $qry = SupplierAssigned::getTenderAssignedSupplierQry(
            $selectedCategoryIds, $tenderMasterId, $selectedCompanyId, $requestData['enableRequestChange'], $requestData['versionID']
        );

        if(sizeof($selectedCategoryIds) != 0) {
            $qry = $qry->whereHas('businessCategoryAssigned', function ($query) use ($selectedCategoryIds) {
                $query->whereIn('supCategoryMasterID', $selectedCategoryIds);
            });
        }

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
    public function checkPricingScheduleMasterValidation($tenderID, $editOrAmend, $versionID){
        $schedule = $editOrAmend ?
            PricingScheduleMasterEditLog::getTenderScheduleMaster($tenderID, $versionID, 'first') :
            PricingScheduleMaster::getTenderScheduleMaster($tenderID, 'first');

        if (empty($schedule)) {
            return ['success' => false, 'message' => 'At least one work schedule should be added'];
        }
        $scheduleAll = $editOrAmend ?
            PricingScheduleMasterEditLog::getTenderScheduleMaster($tenderID, $versionID, 'get') :
            PricingScheduleMaster::getTenderScheduleMaster($tenderID, 'get');

        foreach ($scheduleAll as $val) {
            $scheduleDetail = $editOrAmend ?
                PricingScheduleDetailEditLog::getTenderPricingSchedule($tenderID, $val['amd_id'], $versionID) :
                PricingScheduleDetail::getTenderPricingSchedule($tenderID, $val['id']);

            if ($scheduleDetail->count() > 0) {
                $scheduleDetails = $scheduleDetail->get();
                foreach ($scheduleDetails as $shed) {
                    $scheduleDetailInfo = $editOrAmend ?
                        ScheduleBidFormatDetailsLog::checkScheduleBidFormatDetailExists($val['amd_id'], $shed['amd_id'], $versionID) :
                        ScheduleBidFormatDetails::checkScheduleBidFormatExists($val['id'], $shed['id']);

                    if (empty($scheduleDetailInfo)) {
                        return ['success' => false, 'message' => 'All work schedules should be completed'];
                    }
                }
            }
            $mainwork = $editOrAmend ?
                PricingScheduleDetailEditLog::getPricingScheduleMainWork($tenderID, $val['amd_id'], $versionID) :
                PricingScheduleDetail::getPricingScheduleMainWork($tenderID, $val['id']);

            if ($mainwork->count() > 0) {
                $mainworks = $mainwork->get();
                foreach ($mainworks as $main) {
                    if (count($main->tender_boq_items) == 0) {
                        return ['success' => false, 'message' => 'BOQ enabled main works should have at least one BOQ item'];
                    }
                }
            }
        }
        return ['success' => true, 'message' => 'Pricing Schedule Valid'];
    }
    public function checkTenderCircularValidation($tenderMasterID, $versionID){

        $tenderCircular = TenderCircularsEditLog::getTenderCirculars($tenderMasterID, $versionID);
        if(count($tenderCircular) == 0)
        {
            return ['success' => false, 'message' => 'Please attach a circular to confirm amended changes'];
        }
        $circularIDs = $tenderCircular->pluck('amd_id');
        foreach($circularIDs as $id)
        {
            $circularAmends = CircularAmendmentsEditLog::getAllCircularAmendments($id, $versionID);
            if (count($circularAmends) == 0) {
                return ['success' => false, 'message' => 'Please attach at least one amendment to a circular'];
            }
        }
        return ['success' => true, 'message' => 'Valid Circular'];
    }
    public function checkEvaluationCriteriaValid($tenderMasterID, $versionID, $editOrAmend, $is_active_go_no_go){
        $parentsWithoutSubLevels = EvaluationCriteriaDetails::getCriteriaWithoutChildren($tenderMasterID, $versionID, $editOrAmend, true);
        $subLevelsWithoutFurtherSubLevels = EvaluationCriteriaDetails::getCriteriaWithoutChildren($tenderMasterID, $versionID, $editOrAmend, false);


        if (!$parentsWithoutSubLevels->isEmpty()) {
            return ['success' => false, 'message' => 'If there is no child Technical Evaluation criteria, parent Technical Evaluation criteria should be marked as Final'];
        }
        if (!$subLevelsWithoutFurtherSubLevels->isEmpty()) {
            return ['success' => false, 'message' => 'At least one Criteria should be marked as Final under a parent Technical Evaluation Criteria'];
        }

        if (($is_active_go_no_go == 1) || $is_active_go_no_go == true) {
            $goNoGo = $editOrAmend ?
                EvaluationCriteriaDetailsEditLog::where('tender_id', $tenderMasterID)->where('critera_type_id', 1)->where('tender_version_id', $versionID)->where('is_deleted', 0)->first():
                EvaluationCriteriaDetails::where('tender_id', $tenderMasterID)->where('critera_type_id', 1)->first();
            if (empty($goNoGo)) {
                return ['success' => false, 'message' => 'At least one Go/No Go criteria should be added'];
            }
        }
        return ['success' => true, 'message' => 'Valid Go No Go and Technical Evaluation'];
    }
    public function getEvaluationCriteriaForTenderConfirm($tenderMasterID, $editOrAmend, $versionID){
        try{
            $evaluationData = $editOrAmend ?
                EvaluationCriteriaDetailsEditLog::getEvaluationCriteriaDetailsList($tenderMasterID, 2, $versionID) :
                EvaluationCriteriaDetails::getEvaluationCriteriaDetailsList($tenderMasterID, 2);

            return ['success' => true, 'message' => 'Data retrieved successfully', 'data' => $evaluationData];
        } catch(\Exception $exception){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $exception->getMessage()];
        }
    }
}
