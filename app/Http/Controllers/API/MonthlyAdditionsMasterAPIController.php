<?php
/**
 * =============================================
 * -- File Name : MonthlyAdditionsMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Monthly Additions Master
 * -- Author : Mohamed Fayas
 * -- Create date : 07 - November 2018
 * -- Description : This file contains the all CRUD for Monthly Additions Master
 * -- REVISION HISTORY
 * -- Date: 07-November 2018 By: Fayas Description: Added new functions named as getMonthlyAdditionsByCompany(),getMonthlyAdditionFormData()
 * -- Date: 08-November 2018 By: Fayas Description: Added new functions named as getMonthlyAdditionAudit(),monthlyAdditionReopen()
 */
namespace App\Http\Controllers\API;

use App\helper\email;
use App\helper\Helper;
use App\Http\Requests\API\CreateMonthlyAdditionsMasterAPIRequest;
use App\Http\Requests\API\UpdateMonthlyAdditionsMasterAPIRequest;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\EmployeesDepartment;
use App\Models\EmploymentType;
use App\Models\ExpenseClaimType;
use App\Models\MonthlyAdditionsMaster;
use App\Models\PeriodMaster;
use App\Models\SalaryProcessEmploymentTypes;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\MonthlyAdditionDetailRepository;
use App\Repositories\MonthlyAdditionsMasterRepository;
use App\Traits\AuditTrial;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MonthlyAdditionsMasterController
 * @package App\Http\Controllers\API
 */
class MonthlyAdditionsMasterAPIController extends AppBaseController
{
    /** @var  MonthlyAdditionsMasterRepository */
    private $monthlyAdditionsMasterRepository;
    private $monthlyAdditionDetailRepository;

    public function __construct(MonthlyAdditionsMasterRepository $monthlyAdditionsMasterRepo,MonthlyAdditionDetailRepository $monthlyAdditionDetailRepo)
    {
        $this->monthlyAdditionsMasterRepository = $monthlyAdditionsMasterRepo;
        $this->monthlyAdditionDetailRepository = $monthlyAdditionDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/monthlyAdditionsMasters",
     *      summary="Get a listing of the MonthlyAdditionsMasters.",
     *      tags={"MonthlyAdditionsMaster"},
     *      description="Get all MonthlyAdditionsMasters",
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
     *                  @SWG\Items(ref="#/definitions/MonthlyAdditionsMaster")
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
        $this->monthlyAdditionsMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->monthlyAdditionsMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $monthlyAdditionsMasters = $this->monthlyAdditionsMasterRepository->all();

        return $this->sendResponse($monthlyAdditionsMasters->toArray(), trans('custom.monthly_additions_masters_retrieved_successfully'));
    }

    /**
     * @param CreateMonthlyAdditionsMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/monthlyAdditionsMasters",
     *      summary="Store a newly created MonthlyAdditionsMaster in storage",
     *      tags={"MonthlyAdditionsMaster"},
     *      description="Store MonthlyAdditionsMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MonthlyAdditionsMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MonthlyAdditionsMaster")
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
     *                  ref="#/definitions/MonthlyAdditionsMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMonthlyAdditionsMasterAPIRequest $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        $input['createdpc'] = gethostname();
        //$input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'currency' => 'required|numeric|min:1',
            'empType' => 'required|numeric|min:1',
            'processPeriod' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['documentSystemID'] = 28;
        $input['documentID'] = 'MA';

        $company = Company::where('companySystemID', $input['companySystemID'])
            ->with(['localcurrency', 'reportingcurrency'])
            ->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.company_not_found'), 500);
        }
        $input['CompanyID'] = $company->CompanyID;

        $processPeriod = PeriodMaster::find($input['processPeriod']);

        if (empty($company)) {
            return $this->sendError(trans('custom.month_not_found'), 500);
        }

        $salaryProcessCheck = SalaryProcessEmploymentTypes::where('companySystemID', $input['companySystemID'])
            ->where('empType', $input['empType'])
            //->where('periodID', $input['processPeriod'])
            ->whereHas('salary_process', function ($q) use ($input) {
                $q->where('Currency', $input['currency']);
            })
            ->max('periodID');

        if (!$salaryProcessCheck) {
            $salaryProcessCheck = -1;
        }

        if ($salaryProcessCheck > $input['processPeriod']) {
            return $this->sendError('Salary has been processed for selected month.' . $salaryProcessCheck, 500);
        }

        $input['dateMA'] = $processPeriod->endDate;

        $currencyRate = \Helper::currencyConversion($input['companySystemID'], $input['currency'], $input['currency'], 0);

        $input['localCurrencyID'] = $company->localCurrencyID;
        $input['rptCurrencyID'] = $company->reportingCurrency;

        $input['localCurrencyExchangeRate'] = $currencyRate['trasToLocER'];
        $input['rptCurrencyExchangeRate'] = $currencyRate['trasToRptER'];

        $lastSerial = MonthlyAdditionsMaster::where('companySystemID', $input['companySystemID'])
            ->max('serialNo');

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial) + 1;
        }

        $input['serialNo'] = $lastSerialNumber;
        $input['RollLevForApp_curr'] = 1;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        if ($documentMaster) {
            $code = ($company->CompanyID . '/HR/' . $input['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['monthlyAdditionsCode'] = $code;
        }

        $input['expenseClaimAdditionYN'] = 1;

        $monthlyAdditionsMasters = $this->monthlyAdditionsMasterRepository->create($input);

        return $this->sendResponse($monthlyAdditionsMasters->toArray(), trans('custom.monthly_addition_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/monthlyAdditionsMasters/{id}",
     *      summary="Display the specified MonthlyAdditionsMaster",
     *      tags={"MonthlyAdditionsMaster"},
     *      description="Get MonthlyAdditionsMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MonthlyAdditionsMaster",
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
     *                  ref="#/definitions/MonthlyAdditionsMaster"
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
        /** @var MonthlyAdditionsMaster $monthlyAdditionsMaster */
        $monthlyAdditionsMaster = $this->monthlyAdditionsMasterRepository
            ->with(['employment_type', 'currency_by', 'confirmed_by'])
            ->findWithoutFail($id);

        if (empty($monthlyAdditionsMaster)) {
            return $this->sendError(trans('custom.monthly_additions_master_not_found'));
        }

        return $this->sendResponse($monthlyAdditionsMaster->toArray(), trans('custom.monthly_additions_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateMonthlyAdditionsMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/monthlyAdditionsMasters/{id}",
     *      summary="Update the specified MonthlyAdditionsMaster in storage",
     *      tags={"MonthlyAdditionsMaster"},
     *      description="Update MonthlyAdditionsMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MonthlyAdditionsMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MonthlyAdditionsMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MonthlyAdditionsMaster")
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
     *                  ref="#/definitions/MonthlyAdditionsMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMonthlyAdditionsMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['employment_type', 'currency_by', 'confirmed_by']);
        $input = $this->convertArrayToValue($input);

        /** @var MonthlyAdditionsMaster $monthlyAdditionsMaster */
        $monthlyAdditionsMaster = $this->monthlyAdditionsMasterRepository->findWithoutFail($id);

        if (empty($monthlyAdditionsMaster)) {
            return $this->sendError(trans('custom.monthly_additions_master_not_found'));
        }

        if ($monthlyAdditionsMaster->confirmedYN == 1) {
            return $this->sendError(trans('custom.this_document_already_confirmed_you_cannot_edit'), 500);
        }


        if ($monthlyAdditionsMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            unset($inputParam);
            $validator = \Validator::make($input, [
                'companySystemID' => 'required',
                'currency' => 'required|numeric|min:1',
                'empType' => 'required|numeric|min:1',
                'processPeriod' => 'required|numeric|min:1'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $checkItems = $this->monthlyAdditionDetailRepository->findWhere(['monthlyAdditionsMasterID' => $id]);

            if (count($checkItems) == 0) {
                return $this->sendError('Every monthly addition should have at least one item', 500);
            }

            $input['RollLevForApp_curr'] = 1;
            $params = array('autoID' => $id,
                'company' => $monthlyAdditionsMaster->companySystemID,
                'document' => $monthlyAdditionsMaster->documentSystemID,
                'segment' => 0,
                'category' => 0,
                'amount' => 0
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }
        $employee = \Helper::getEmployeeInfo();


        $updateInput = array(
            'modifiedpc' => gethostname(),
            'modifieduser' => $employee->empID,
            'modifiedUserSystemID' => $employee->employeeSystemID,
            'description' => $input['description']
        );

        $monthlyAdditionsMaster = $this->monthlyAdditionsMasterRepository->update($updateInput, $id);

        return $this->sendResponse($monthlyAdditionsMaster->toArray(), trans('custom.monthly_addition_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/monthlyAdditionsMasters/{id}",
     *      summary="Remove the specified MonthlyAdditionsMaster from storage",
     *      tags={"MonthlyAdditionsMaster"},
     *      description="Delete MonthlyAdditionsMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MonthlyAdditionsMaster",
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
        /** @var MonthlyAdditionsMaster $monthlyAdditionsMaster */
        $monthlyAdditionsMaster = $this->monthlyAdditionsMasterRepository->findWithoutFail($id);

        if (empty($monthlyAdditionsMaster)) {
            return $this->sendError(trans('custom.monthly_additions_master_not_found'));
        }

        $monthlyAdditionsMaster->delete();

        return $this->sendResponse($id, trans('custom.monthly_additions_master_deleted_successfully'));
    }

    public function getMonthlyAdditionsByCompany(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approvedYN'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $monthlyAdditions = $this->monthlyAdditionsMasterRepository->monthlyAdditionsListQuery($request, $input, $search);

        return \DataTables::of($monthlyAdditions)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('monthlyAdditionsMasterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getMonthlyAdditionFormData(Request $request)
    {
        $companyId = $request['companyId'];
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $company = Company::where('companySystemID', $companyId)->with(['localcurrency', 'reportingcurrency'])->first();
        $currencies = CurrencyMaster::all();
        $employmentTypes = EmploymentType::all();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'company' => $company,
            'currencies' => $currencies,
            'employmentTypes' => $employmentTypes
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function getProcessPeriods(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'companySystemID' => 'required|numeric|min:1',
            'currency' => 'required|numeric|min:1',
            'empType' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $maxPeriodID = SalaryProcessEmploymentTypes::where('companySystemID', $input['companySystemID'])
            ->where('empType', $input['empType'])
            ->whereHas('salary_process', function ($q) use ($input) {
                $q->where('Currency', $input['currency']);
            })
            ->max('periodID');
        if (!$maxPeriodID) {
            $maxPeriodID = 0;
        }

        $processPeriods = PeriodMaster::select(DB::raw("periodMasterID as value,CONCAT(periodMonth,' : ',DATE_FORMAT(startDate, '%d/%m/%Y'), ' - ' ,DATE_FORMAT(endDate, '%d/%m/%Y')) as label"))
            ->where('periodMasterID', '>', $maxPeriodID)->get();

        return $this->sendResponse($processPeriods, trans('custom.record_retrieved_successfully_1'));
    }

    public function getMonthlyAdditionAudit(Request $request)
    {
        $id = $request->get('id');
        $monthlyAddition = $this->monthlyAdditionsMasterRepository->getAudit($id);

        if (empty($monthlyAddition)) {
            return $this->sendError(trans('custom.monthly_addition_not_found'));
        }

        $monthlyAddition->docRefNo = \Helper::getCompanyDocRefNo($monthlyAddition->companySystemID, $monthlyAddition->documentSystemID);

        return $this->sendResponse($monthlyAddition->toArray(), trans('custom.monthly_addition_retrieved_successfully'));
    }

    public function monthlyAdditionReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];
        $monthlyAddition = $this->monthlyAdditionsMasterRepository->findWithoutFail($id);
        $emails = array();
        if (empty($monthlyAddition)) {
            return $this->sendError(trans('custom.monthly_addition_not_found'));
        }

        if ($monthlyAddition->approvedYN == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_monthly_addition_it_is_alre'));
        }

        if ($monthlyAddition->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_monthly_addition_it_is_alre_1'));
        }

        if ($monthlyAddition->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_monthly_addition_it_is_not_'));
        }

        $updateInput = ['confirmedYN' => 0,'confirmedByEmpSystemID' => null,'confirmedby' => null,
            'confirmedByName' => null, 'confirmedDate' => null,'RollLevForApp_curr' => 1];

        $this->monthlyAdditionsMasterRepository->update($updateInput,$id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $monthlyAddition->documentSystemID)->first();

        $cancelDocNameBody = $document->document_description_translated . ' <b>' . $monthlyAddition->monthlyAdditionsCode . '</b>';
        $cancelDocNameSubject = $document->document_description_translated . ' ' . $monthlyAddition->monthlyAdditionsCode;

        $subject = $cancelDocNameSubject . ' ' . trans('email.is_reopened');

        $body = '<p>' . $cancelDocNameBody . ' ' . trans('email.is_reopened_by', ['empID' => $employee->empID, 'empName' => $employee->empFullName]) . '</p><p>' . trans('email.comment') . ' : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $monthlyAddition->companySystemID)
            ->where('documentSystemCode', $monthlyAddition->monthlyAdditionsMasterID)
            ->where('documentSystemID', $monthlyAddition->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $monthlyAddition->companySystemID)
                    ->where('documentSystemID', $monthlyAddition->documentSystemID)
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

        DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $monthlyAddition->companySystemID)
            ->where('documentSystemID', $monthlyAddition->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($monthlyAddition->documentSystemID,$id,$input['reopenComments'],'Reopened');

        return $this->sendResponse($monthlyAddition->toArray(), trans('custom.monthly_addition_reopened_successfully'));
    }

    public function amendEcMonthlyAdditionReview(Request $request){

        $input = $request->all();

        $id = $input['monthlyAdditionsMasterID'];
        $employee = Helper::getEmployeeInfo();
        $emails = array();
        $masterData = MonthlyAdditionsMaster::find($id);
        $documentName = trans('email.monthly_additions');

        if (empty($masterData)) {
            return $this->sendError($documentName.' not found');
        }

        if ($masterData->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_return_back_to_amend_this').$documentName.', it is not confirmed');
        }

        $emailBody = '<p>' . $masterData->monthlyAdditionsCode . ' ' . trans('email.has_been_returned_back_to_amend_by', ['empName' => $employee->empName]) . ' ' . trans('email.due_to_below_reason') . '.</p><p>' . trans('email.comment') . ' : ' . $input['returnComment'] . '</p>';
        $emailSubject = $masterData->monthlyAdditionsCode . ' ' . trans('email.has_been_returned_back_to_amend');

        DB::beginTransaction();
        try {

            //sending email to relevant party
            if ($masterData->confirmedYN == 1) {
                $emails[] = array('empSystemID' => $masterData->confirmedByEmpSystemID,
                    'companySystemID' => $masterData->companySystemID,
                    'docSystemID' => $masterData->documentSystemID,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody,
                    'docSystemCode' => $id,
                    'docCode' => $masterData->monthlyAdditionsCode
                );
            }

            $documentApproval =  DocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemID)
                ->get();

            foreach ($documentApproval as $da) {
                if ($da->approvedYN == -1) {
                    $emails[] = array('empSystemID' => $da->employeeSystemID,
                        'companySystemID' => $masterData->companySystemID,
                        'docSystemID' => $masterData->documentSystemID,
                        'alertMessage' => $emailSubject,
                        'emailAlertMessage' => $emailBody,
                        'docSystemCode' => $masterData->monthlyAdditionsCode);
                }
            }

            $sendEmail = email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemID)
                ->delete();

            // updating fields
            $masterData->confirmedYN = 0 ;
            $masterData->confirmedByEmpSystemID = null;
            $masterData->confirmedby = null;
            $masterData->confirmedDate = null;

            $masterData->approvedYN = 0;
            $masterData->approvedByUserSystemID = null;
            $masterData->approvedby = null;
            $masterData->approvedDate = null;

            $masterData->save();

            AuditTrial::createAuditTrial($masterData->documentSystemID,$id,$input['returnComment'],'returned back to amend');

            DB::commit();
            return $this->sendResponse($masterData->toArray(), $documentName.' amend saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }
}
