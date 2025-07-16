<?php

namespace App\Repositories;

use App\Helper\Helper;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\DocumentModifyRequest;
use App\Models\TenderMaster;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use InfyOm\Generator\Common\BaseRepository;
use App\Repositories\SrmTenderMasterEditLogRepository;
use App\Repositories\DocumentAttachmentsEditLogRepository;
use App\Repositories\CalendarDatesDetailEditLogRepository;
use App\Repositories\CircularAmendmentsEditLogRepository;
use App\Repositories\EvaluationCriteriaDetailsEditLogRepository;
use App\Repositories\PricingScheduleDetailEditLogRepository;
use App\Repositories\ProcumentActivityEditLogRepository;
use App\Repositories\ScheduleBidFormatDetailsLogRepository;
use App\Repositories\SrmTenderBidEmployeeDetailsEditLogRepository;
use App\Repositories\TenderBoqItemsEditLogRepository;
use App\Repositories\TenderCircularsEditLogRepository;
use App\Repositories\TenderDocumentTypeAssignLogRepository;
use App\Repositories\SrmTenderUserAccessEditLogRepository;
use App\Repositories\TenderPurchaseRequestEditLogRepository;
use App\Repositories\TenderBudgetItemEditLogRepository;
use App\Repositories\TenderDepartmentEditLogRepository;
use App\Repositories\TenderSupplierAssigneeEditLogRepository;
use App\Services\SrmDocumentModifyService;
use App\Services\SrmTenderEditAmendService;
use Illuminate\Http\Request;

/**
 * Class DocumentModifyRequestRepository
 * @package App\Repositories
 * @version March 21, 2023, 3:13 pm +04
 *
 * @method DocumentModifyRequest findWithoutFail($id, $columns = ['*'])
 * @method DocumentModifyRequest find($id, $columns = ['*'])
 * @method DocumentModifyRequest first($columns = ['*'])
 */
class DocumentModifyRequestRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'approved',
        'approved_by_user_system_id',
        'approved_date',
        'companySystemID',
        'document_master_id',
        'documentSystemCode',
        'rejected',
        'rejected_by_user_system_id',
        'rejected_date',
        'requested_date',
        'requested_document_master_id',
        'requested_employeeSystemID',
        'RollLevForApp_curr',
        'status',
        'type',
        'version'
    ];

    protected $srmTenderMasterEditLogRepository;
    protected $documentAttachmentsEditLogRepository;
    protected $calendarDatesDetailsEditLogRepository;
    protected $circularAmendmentsEditLogRepository;
    protected $evaluationCriteriaDetailsEditLogRepository;
    protected $pricingScheduleDetailEditLogRepository;
    protected $procumentActivityEditLogRepository;
    protected $scheduleBidFormatDetailsLogRepository;
    protected $tenderBidEmployeeDetailsEditLogRepository;
    protected $tenderBoqItemsEditLogRepository;
    protected $tenderCircularsEditLogRepository;
    protected $tenderDocumentTypeAssignLogRepository;
    protected $srmTenderUserAccessEditLogRepository;
    protected $tenderPurchaseRequestEditLogRepository;
    protected $tenderBudgetItemEditLogRepository;
    protected $tenderDepartmentEditLogRepository;
    protected $srmDocumentModifyService;
    protected $tenderSupplierAssigneeEditLogRepository;
    protected $pricingScheduleMasterEditLogRepository;
    protected $srmTenderEditAmendService;
    public function __construct(
        SrmTenderMasterEditLogRepository $srmTenderMasterEditLogRepository,
        Application $app,
        DocumentAttachmentsEditLogRepository $documentAttachmentsEditLogRepository,
        CalendarDatesDetailEditLogRepository $calendarDatesDetailsEditLogRepository,
        CircularAmendmentsEditLogRepository $circularAmendmentsEditLogRepo,
        EvaluationCriteriaDetailsEditLogRepository $evaluationCriteriaDetailsEditLogRepo,
        PricingScheduleDetailEditLogRepository $pricingScheduleDetailEditLogRepo,
        ProcumentActivityEditLogRepository $procumentActivityEditLogRepo,
        ScheduleBidFormatDetailsLogRepository $scheduleBidFormatDetailsLogRepo,
        SrmTenderBidEmployeeDetailsEditLogRepository $tenderBidEmployeeDetailsEditLogRepo,
        TenderBoqItemsEditLogRepository $tenderBoqItemsEditLogRepo,
        TenderCircularsEditLogRepository $tenderCircularsEditLogRepo,
        TenderDocumentTypeAssignLogRepository $tenderDocumentTypeAssignLogRepo,
        SrmTenderUserAccessEditLogRepository $srmTenderUserAccessEditLogRepo,
        TenderPurchaseRequestEditLogRepository $tenderPurchaseRequestEditLogRepo,
        TenderBudgetItemEditLogRepository $tenderBudgetItemEditLogRepo,
        TenderDepartmentEditLogRepository $tenderDepartmentEditLogRepo,
        SrmDocumentModifyService $documentModifyService,
        TenderSupplierAssigneeEditLogRepository $tenderSupplierAssigneeEditLogRepo,
        PricingScheduleMasterEditLogRepository $pricingScheduleMasterEditLogRepo,
        SrmTenderEditAmendService $srmTenderEditAmendService
    ){
        parent::__construct($app);
        $this->srmTenderMasterEditLogRepository = $srmTenderMasterEditLogRepository;
        $this->documentAttachmentsEditLogRepository = $documentAttachmentsEditLogRepository;
        $this->calendarDatesDetailsEditLogRepository = $calendarDatesDetailsEditLogRepository;
        $this->circularAmendmentsEditLogRepository = $circularAmendmentsEditLogRepo;
        $this->evaluationCriteriaDetailsEditLogRepository = $evaluationCriteriaDetailsEditLogRepo;
        $this->pricingScheduleDetailEditLogRepository = $pricingScheduleDetailEditLogRepo;
        $this->procumentActivityEditLogRepository = $procumentActivityEditLogRepo;
        $this->scheduleBidFormatDetailsLogRepository = $scheduleBidFormatDetailsLogRepo;
        $this->tenderBidEmployeeDetailsEditLogRepository = $tenderBidEmployeeDetailsEditLogRepo;
        $this->tenderBoqItemsEditLogRepository = $tenderBoqItemsEditLogRepo;
        $this->tenderCircularsEditLogRepository = $tenderCircularsEditLogRepo;
        $this->tenderDocumentTypeAssignLogRepository = $tenderDocumentTypeAssignLogRepo;
        $this->srmTenderUserAccessEditLogRepository = $srmTenderUserAccessEditLogRepo;
        $this->tenderPurchaseRequestEditLogRepository = $tenderPurchaseRequestEditLogRepo;
        $this->tenderBudgetItemEditLogRepository = $tenderBudgetItemEditLogRepo;
        $this->tenderDepartmentEditLogRepository = $tenderDepartmentEditLogRepo;
        $this->srmDocumentModifyService = $documentModifyService;
        $this->tenderSupplierAssigneeEditLogRepository = $tenderSupplierAssigneeEditLogRepo;
        $this->pricingScheduleMasterEditLogRepository = $pricingScheduleMasterEditLogRepo;
        $this->srmTenderEditAmendService = $srmTenderEditAmendService;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentModifyRequest::class;
    }

    public function createEditAmendLog($tenderMaster, $version_id, $version_exists){
        try{
            return self::saveEditAmendLogData($tenderMaster, $version_id, $version_exists);
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }

    public function saveEditAmendLogData($tenderMaster, $version_id, $version_exists){
        try{
            if(!$version_exists){
                $this->srmTenderMasterEditLogRepository->saveTenderMasterHistory($tenderMaster, null);
                $this->tenderBidEmployeeDetailsEditLogRepository->saveTenderBidEmployeeDetailHistory($tenderMaster['id'], null);
                $this->srmTenderUserAccessEditLogRepository->saveTenderUserAccessHistory($tenderMaster['id'], null);
                $this->procumentActivityEditLogRepository->saveProcurementActivityHistory($tenderMaster['id']);
                $this->tenderPurchaseRequestEditLogRepository->saveTenderPurchaseRequestHistory($tenderMaster['id']);
                $this->tenderBudgetItemEditLogRepository->saveTenderBudgetItemHistory($tenderMaster['id'], null);
                $this->tenderDepartmentEditLogRepository->saveTenderDepartmentHistory($tenderMaster['id'], null);
                $this->calendarDatesDetailsEditLogRepository->saveCalendarDateDetailHistory($tenderMaster['id'], null);
                $this->tenderDocumentTypeAssignLogRepository->saveTenderDocumentTypeAssign($tenderMaster['id'], null);
                $this->documentAttachmentsEditLogRepository->saveDocumentAttachments($tenderMaster['id'], $tenderMaster['document_system_id']);
                $this->tenderSupplierAssigneeEditLogRepository->saveTenderSupplierAssignee($tenderMaster['id']);
                $this->pricingScheduleMasterEditLogRepository->saveTenderPricingScheduleMasters($tenderMaster['id']);
                $this->tenderCircularsEditLogRepository->saveTenderCircularForAmd($tenderMaster['id']);
                $this->evaluationCriteriaDetailsEditLogRepository->saveEvacuationCriteriaDetails($tenderMaster['id']);
            }
            $this->srmTenderMasterEditLogRepository->saveTenderMasterHistory($tenderMaster, $version_id);
            $this->tenderBidEmployeeDetailsEditLogRepository->saveTenderBidEmployeeDetailHistory($tenderMaster['id'], $version_id);
            $this->srmTenderUserAccessEditLogRepository->saveTenderUserAccessHistory($tenderMaster['id'], $version_id);
            $this->procumentActivityEditLogRepository->saveProcurementActivityHistory($tenderMaster['id'], $version_id);
            $this->tenderPurchaseRequestEditLogRepository->saveTenderPurchaseRequestHistory($tenderMaster['id'], $version_id);
            $this->tenderBudgetItemEditLogRepository->saveTenderBudgetItemHistory($tenderMaster['id'], $version_id);
            $this->tenderDepartmentEditLogRepository->saveTenderDepartmentHistory($tenderMaster['id'], $version_id);
            $this->calendarDatesDetailsEditLogRepository->saveCalendarDateDetailHistory($tenderMaster['id'], $version_id);
            $this->tenderDocumentTypeAssignLogRepository->saveTenderDocumentTypeAssign($tenderMaster['id'], $version_id);
            $this->documentAttachmentsEditLogRepository->saveDocumentAttachments($tenderMaster['id'], $tenderMaster['document_system_id'], $version_id);
            $this->tenderSupplierAssigneeEditLogRepository->saveTenderSupplierAssignee($tenderMaster['id'],  $version_id);
            $this->pricingScheduleMasterEditLogRepository->saveTenderPricingScheduleMasters($tenderMaster['id'], $version_id);
            $this->tenderCircularsEditLogRepository->saveTenderCircularForAmd($tenderMaster['id'], $version_id);
            $this->evaluationCriteriaDetailsEditLogRepository->saveEvacuationCriteriaDetails($tenderMaster['id'], $version_id);

            return ['success' => true, 'message' => 'Success'];
        } catch (\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
    public function createEditAmendRequest(Request $request){
        $input = $request->all();
        try{
            return DB::transaction(function () use ($input) {
                $tenderID = $input['documentSystemCode'] ?? 0;
                $companySystemID = $input['companySystemID'] ?? 0;
                $tenderMaster = TenderMaster::getTenderMasterData($tenderID);
                if(empty($tenderMaster))
                {
                    return ['success' => false, 'message' => 'Tender master not found.'];
                }

                $insertData = self::prepareEditOrAmendSaveData($input, $tenderID, $companySystemID);
                $documentModifyRequest = DocumentModifyRequest::create($insertData);
                TenderMaster::where('id', $tenderID)->update(['tender_edit_version_id' => $documentModifyRequest['id']]);

                $params = [
                    'autoID' => $documentModifyRequest['id'],
                    'company' => $companySystemID,
                    'document' => $input["document_master_id"],
                    'reference_document_id' => $input["requested_document_master_id"],
                    'document_type' => $tenderMaster->document_type,
                    'amount' => $tenderMaster->estimated_value,
                    'tenderTypeId' => $tenderMaster->tender_type_id
                ];
                $confirm = Helper::confirmDocument($params);
                $title = $tenderMaster['document_system_id'] == 108 ? 'Tender' : 'RFX';

                if (!$confirm["success"]) {
                    return ['success' => false, 'message' => $confirm["message"]];
                }
                return ['success' => true, 'message' => $title. ' modify request sent successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $exception->getMessage()];
        }
    }
    public function approveDocumentEditAmendRequest(Request $request){
        try {
            $input = $request->all();
            $reference_document_id = $input['reference_document_id'] ?? 0;
            if($reference_document_id == 0){
                return ['success' => false, 'message' => 'Reference Document ID is required'];
            }

            $tenderMaster = TenderMaster::find($input['id']);
            if(empty($tenderMaster)){
                return ['success' => false, 'message' => 'Tender master not found'];
            }

            return DB::transaction(function () use ($input, $reference_document_id, $tenderMaster) {
                $approve = Helper::approveDocument($input);
                if (!$approve["success"]){
                    return ['success' => false, 'message' => $approve["message"]];
                }

                if ($input['document_system_id'] == 117 && $approve['data'] && $approve['data']['numberOfLevels'] == $approve['data']['currentLevel']) {
                    $logResult = self::createEditAmendLogAfterApproval($input, $tenderMaster);
                    if(!$logResult['success']){
                        return ['success' => true, 'message' => $logResult["message"]];
                    }
                }

                if ($input['document_system_id'] == 118 && $approve['data'] && $approve['data']['numberOfLevels'] == $approve['data']['currentLevel']) {
                    $this->srmDocumentModifyService->cloneHistoryToMasterTable($input['id'], $input['reference_document_id']);
                }
                return ['success' => true, 'message' => $approve["message"]];
            });

        } catch (\Exception $exception){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $exception->getMessage()];
        }
    }
    private function createEditAmendLogAfterApproval($input, $tenderMaster){
        $documentRequestData = DocumentModifyRequest::getTenderModifyRequest($input['id']);
        $versionExists = $documentRequestData->version > 1;
        $versionID = $documentRequestData->id;

        return self::createEditAmendLog($tenderMaster, $versionID, $versionExists);
    }
    private function prepareEditOrAmendSaveData($input, $tenderID, $companySystemID){
        $is_version_exit = DocumentModifyRequest::getLatestTenderDocumentRequest($tenderID);
        $version = ($is_version_exit->version ?? 0) + 1;
        $document_master_id = $input['document_master_id'];
        $companyID = Company::getComanyCode($companySystemID);

        $documentMaster = DocumentMaster::getDocumentData($document_master_id);
        $lastSerialNumber = DocumentModifyRequest::getNewSerialNumber($companySystemID);
        $code = ($companyID . '/' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

        $input['version'] = $version;
        $input['requested_employeeSystemID'] =\Helper::getEmployeeSystemID();
        $input['requested_date'] = now();
        $input['RollLevForApp_curr'] = 1;
        $input['code'] = $code;
        $input['serial_number'] = $lastSerialNumber;
        $input['modify_type'] = 1;
        return $input;
    }

    public function getEditOrAmendHistory(Request $request): array {
        $input = $request->all();

        $validate = self::checkValidRequestParams($input);
        if(!$validate['success']){
            return $validate;
        }

        $tenderID = $input['tenderID'];
        $requestID = $input['requestID'];

        $tenderMaster = TenderMaster::getTenderMasterData($tenderID);
        if(empty($tenderMaster)){
            return ['success' => false, 'message' => 'Tender master not found'];
        }

        $requestMaster = DocumentModifyRequest::getDocumentModifyData($requestID);
        if(empty($requestMaster)){
            return ['success' => false, 'message' => 'Document modify request not found'];
        }

        $allChanges = $this->srmTenderEditAmendService->getHistoryData($tenderID, $requestID);
        return ['success' => true, 'message' => 'Data retrieved successfully', 'data' => $allChanges];
    }
    public function checkValidRequestParams($input): array{
        $validator = Validator::make($input, [
            'tenderID' => 'required',
            'requestID' => 'required',
        ], [

            'tenderID.required' => 'Tender Master ID is required',
            'requestID.required' => 'Document Request ID is required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => implode(', ', $validator->errors()->all())];
        }
        return ['success' => true, 'message' => 'Validation check success'];
    }
}
