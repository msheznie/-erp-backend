<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateErpBudgetAdditionAPIRequest;
use App\Http\Requests\API\UpdateErpBudgetAdditionAPIRequest;
use App\Models\BudgetMaster;
use App\Models\BudgetTransferForm;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Models\ReportTemplateDetails;
use App\Models\CompanyPolicyMaster;
use App\Models\BudgetReviewTransferAddition;
use App\Models\DocumentMaster;
use App\Models\ErpBudgetAddition;
use App\Models\Months;
use App\Models\ErpBudgetAdditionDetail;
use App\Models\ReportTemplate;
use App\Models\ReportTemplateLinks;
use App\Models\SegmentMaster;
use App\Models\CompanyFinanceYear;
use App\Models\Year;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\ErpBudgetAdditionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use App\helper\BudgetConsumptionService;
use App\Models\BudgetAdditionDetailRefferedBack;
use App\Models\BudgetAdditionRefferedBack;
use App\Models\CompanyDocumentAttachment;
use App\Models\DocumentApproved;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Traits\AuditTrial;
use Carbon\Carbon;

/**
 * Class ErpBudgetAdditionController
 *
 * @package App\Http\Controllers\API
 */
class ErpBudgetAdditionAPIController extends AppBaseController
{
    /** @var  ErpBudgetAdditionRepository */
    private $erpBudgetAdditionRepository;

    public function __construct(ErpBudgetAdditionRepository $erpBudgetAdditionRepo)
    {
        $this->erpBudgetAdditionRepository = $erpBudgetAdditionRepo;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpBudgetAdditions",
     *      summary="Get a listing of the ErpBudgetAdditions.",
     *      tags={"ErpBudgetAddition"},
     *      description="Get all ErpBudgetAdditions",
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
     *                  @SWG\Items(ref="#/definitions/ErpBudgetAddition")
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

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approvedYN', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $budgetTransfer = $this->erpBudgetAdditionRepository->budgetAdditionFormListQuery($request, $input, $search);

        return \DataTables::of($budgetTransfer)
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

    /**
     * @param CreateErpBudgetAdditionAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Post(
     *      path="/erpBudgetAdditions",
     *      summary="Store a newly created ErpBudgetAddition in storage",
     *      tags={"ErpBudgetAddition"},
     *      description="Store ErpBudgetAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpBudgetAddition that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpBudgetAddition")
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
     *                  ref="#/definitions/ErpBudgetAddition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateErpBudgetAdditionAPIRequest $request)
    {


        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['createdDate'] = now();
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');

        $validator = \Validator::make($input, [
            'companyFinanceYearID' => 'required|numeric|min:1',
            'comments' => 'required',
            'templatesMasterAutoID' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $companyFinanceYear = CompanyFinanceYear::find($input['companyFinanceYearID']);
        if (empty($companyFinanceYear)) {
            return $this->sendError('Selected financial year is not found.', 500);
        }

        $input['year'] = Carbon::parse($companyFinanceYear->bigginingDate)->format('Y');

        $input['documentSystemID'] = 102;
        $input['documentID'] = 'BDA';

        $lastSerial = ErpBudgetAddition::where('companySystemID', $input['companySystemID'])
            ->orderBy('id', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company')]), 500);
        }

        $input['companyID'] = $company->CompanyID;
        $input['serialNo'] = $lastSerialNumber;
        $input['RollLevForApp_curr'] = 1;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        if ($documentMaster) {
            $code = ($company->CompanyID . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['additionVoucherNo'] = $code;
        }

        $budgetTransferForms = $this->erpBudgetAdditionRepository->create($input);

        return $this->sendResponse($budgetTransferForms->toArray(), 'Budget Addition Form saved successfully');
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpBudgetAdditions/{id}",
     *      summary="Display the specified ErpBudgetAddition",
     *      tags={"ErpBudgetAddition"},
     *      description="Get ErpBudgetAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpBudgetAddition",
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
     *                  ref="#/definitions/ErpBudgetAddition"
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
        /** @var ErpBudgetAddition $erpBudgetAddition */
        $erpBudgetAddition = $this->erpBudgetAdditionRepository->fetchBudgetData($id);

        if (empty($erpBudgetAddition)) {
            return $this->sendError('Erp Budget Addition not found');
        }

        return $this->sendResponse($erpBudgetAddition, 'Erp Budget Addition retrieved successfully');
    }

    /**
     * @param int                               $id
     * @param UpdateErpBudgetAdditionAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Put(
     *      path="/erpBudgetAdditions/{id}",
     *      summary="Update the specified ErpBudgetAddition in storage",
     *      tags={"ErpBudgetAddition"},
     *      description="Update ErpBudgetAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpBudgetAddition",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpBudgetAddition that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpBudgetAddition")
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
     *                  ref="#/definitions/ErpBudgetAddition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateErpBudgetAdditionAPIRequest $request)
    {

        $input = $request->only(['confirmedYN']);
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN'));
        $input['comments'] = $request->get('comments');

        $erpBudgetAddition = $this->erpBudgetAdditionRepository->findWithoutFail($id);

        if (empty($erpBudgetAddition)) {
            return $this->sendError('Erp Budget Addition not found');
        }
        $employee = \Helper::getEmployeeInfo();
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;


        
        if ($erpBudgetAddition->confirmedYN == 0 && $input['confirmedYN'] == 1) {
            $debitNoteDetails = ErpBudgetAdditionDetail::where('budgetAdditionFormAutoID', $id)->get();
            $checkBudgetFromReview = $this->validateBudgetFormReview($id, $erpBudgetAddition, $debitNoteDetails);
            if (!$checkBudgetFromReview['status']) {
                return $this->sendError("You cannot confirm this document.", 500, array('type' => 'confirm_error_budget_review', 'data' => $checkBudgetFromReview['message']));
            }

            $params = array(
                'autoID' => $id,
                'company' => $erpBudgetAddition->companySystemID,
                'document' => $erpBudgetAddition->documentSystemID,
                'segment' => 0,
                'category' => 0,
                'amount' => 0
            );
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }


        $erpBudgetAddition = $this->erpBudgetAdditionRepository->update($input, $id);

        return $this->sendReponseWithDetails($erpBudgetAddition->toArray(), 'Budget Addition updated successfully',1,$confirm['data'] ?? null);
    }

    public function validateBudgetFormReview($budgetTransferFormAutoID, $budgetTransferForm, $details)
    {
        $checkFormReview = BudgetReviewTransferAddition::where('budgetTransferAdditionID', $budgetTransferFormAutoID)
            ->where('budgetTransferType', 2)
            ->with(['purchase_order', 'purchase_request'])
            ->get();

        $currency = \Helper::companyCurrency($budgetTransferForm->companySystemID);

        $consumptionData = [];
        $consumptionDataWithPoPr = [];
        if (count($checkFormReview) > 0) {
            $checkFormReview = $checkFormReview->toArray();
            foreach ($checkFormReview as $key => $value) {
                $res = BudgetConsumptionService::getConsumptionData($value['documentSystemID'], $value['documentSystemCode']);

                if ($res['status'] && isset($res['data']) && count($res['data']) > 0) {
                    foreach ($res['data'] as $key1 => $value1) {
                        $consumptionData[] = $value1;
                        $temp['budgetData'] = $value1;
                        if (in_array($value['documentSystemID'], [2, 5, 52])) {
                            $temp['docCode'] = $value['purchase_order']['purchaseOrderCode'];
                        } else {
                            $temp['docCode'] = $value['purchase_request']['purchaseRequestCode'];
                        }

                        $consumptionDataWithPoPr[] = $temp;
                    }
                }
            }

            $checkBudgetBasedOnGL = CompanyPolicyMaster::where('companyPolicyCategoryID', 55)
                ->where('companySystemID', $budgetTransferForm->companySystemID)
                ->first();

            $departmentWiseCheckBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 33)
                ->where('companySystemID', $budgetTransferForm->companySystemID)
                ->first();

            $departmentWiseCheckBudgetPolicy = false;
            if ($departmentWiseCheckBudget && $departmentWiseCheckBudget->isYesNO == 1) {
                $departmentWiseCheckBudgetPolicy = true;
            }


            $errorMasg = [];
            if ($checkBudgetBasedOnGL && $checkBudgetBasedOnGL->isYesNO == 0) {
                if ($departmentWiseCheckBudgetPolicy) {
                    $groupByDetail = collect($consumptionData)->groupBy(function ($item, $key) {
                        return $item['templateDetailID'] . $item['serviceLineSystemID'];
                    });
                } else {
                    $groupByDetail = collect($consumptionData)->groupBy('templateDetailID')->all();
                }
                foreach ($groupByDetail as $key => $value) {
                    $budgetAmountToUnBlock = abs($value[0]['availableAmount']);
                    if ($budgetAmountToUnBlock > 0) {
                        $templateDetailData = ReportTemplateDetails::find($value[0]['templateDetailID']);
                        $documents = $this->getDocumentsForErrorMessageOfTransferConfirm($value[0]['templateDetailID'], $consumptionDataWithPoPr);

                        $transferedAmount = ErpBudgetAdditionDetail::where('templateDetailID', $value[0]['templateDetailID'])
                            ->when($departmentWiseCheckBudgetPolicy == true, function ($query) use ($value) {
                                $query->where('serviceLineSystemID', $value[0]['serviceLineSystemID']);
                            })
                            ->sum('adjustmentAmountRpt');

                        // return $budgetAmountToUnBlock;
                        $differentAmount = $budgetAmountToUnBlock - $transferedAmount;
                        $roundedDiffAmound = round($differentAmount, $currency->reportingcurrency->DecimalPlaces);
                        if ($transferedAmount > 0 && $roundedDiffAmound > 0) {
                            if ($departmentWiseCheckBudgetPolicy) {
                                $errorMasg[] = $templateDetailData->description . " of " . $value[0]['serviceLine'] . " segment need " . $currency->reportingcurrency->CurrencyCode . " " . number_format($differentAmount, $currency->reportingcurrency->DecimalPlaces) . " to unblock the documents " . $documents;
                            } else {
                                $errorMasg[] = $templateDetailData->description . " need " . $currency->reportingcurrency->CurrencyCode . " " . number_format($differentAmount, $currency->reportingcurrency->DecimalPlaces) . " to unblock the documents " . $documents;
                            }
                        } else if ($transferedAmount == 0) {
                            if ($departmentWiseCheckBudgetPolicy) {
                                $errorMasg[] = $templateDetailData->description . " of " . $value[0]['serviceLine'] . " segment need " . $currency->reportingcurrency->CurrencyCode . " " . number_format($budgetAmountToUnBlock, $currency->reportingcurrency->DecimalPlaces) . " to unblock the documents " . $documents;
                            } else {
                                $errorMasg[] = $templateDetailData->description . " need " . $currency->reportingcurrency->CurrencyCode . " " . number_format($budgetAmountToUnBlock, $currency->reportingcurrency->DecimalPlaces) . " to unblock the documents " . $documents;
                            }
                        }
                    }
                }
            } else {
                if ($departmentWiseCheckBudgetPolicy) {
                    $groupByDetail = collect($consumptionData)->groupBy(function ($item, $key) {
                        return $item['templateDetailID'] . $item['serviceLineSystemID'];
                    });
                } else {
                    $groupByDetail = collect($consumptionData)->groupBy('templateDetailID')->all();
                }
                foreach ($groupByDetail as $key => $value) {
                    $budgetAmountToUnBlock = abs($value[0]['availableAmount']);
                    if ($budgetAmountToUnBlock > 0) {
                        $templateDetailData = ChartOfAccount::find($value[0]['templateDetailID']);
                        $documents = $this->getDocumentsForErrorMessageOfTransferConfirm($value[0]['templateDetailID'], $consumptionDataWithPoPr);

                        $transferedAmount = ErpBudgetAdditionDetail::where('chartOfAccountSystemID', $value[0]['templateDetailID'])
                            ->when($departmentWiseCheckBudgetPolicy == true, function ($query) use ($value) {
                                $query->where('serviceLineSystemID', $value[0]['serviceLineSystemID']);
                            })
                            ->sum('adjustmentAmountRpt');

                        // return $budgetAmountToUnBlock;
                        $differentAmount = $budgetAmountToUnBlock - $transferedAmount;
                        $roundedDiffAmound = round($differentAmount, $currency->reportingcurrency->DecimalPlaces);
                        if ($transferedAmount > 0 && $roundedDiffAmound > 0) {
                            if ($departmentWiseCheckBudgetPolicy) {
                                $errorMasg[] = $templateDetailData->AccountCode . " - " . $templateDetailData->AccountDescription . " of " . $value[0]['serviceLine'] . " segment need " . $currency->reportingcurrency->CurrencyCode . " " . number_format($differentAmount, $currency->reportingcurrency->DecimalPlaces) . " to unblock the documents " . $documents;
                            } else {
                                $errorMasg[] = $templateDetailData->AccountCode . " - " . $templateDetailData->AccountDescription . " need " . $currency->reportingcurrency->CurrencyCode . " " . number_format($differentAmount, $currency->reportingcurrency->DecimalPlaces) . " to unblock the documents " . $documents;
                            }
                        } else if ($transferedAmount == 0) {
                            if ($departmentWiseCheckBudgetPolicy) {
                                $errorMasg[] = $templateDetailData->AccountCode . " - " . $templateDetailData->AccountDescription . " of " . $value[0]['serviceLine'] . " segment need " . $currency->reportingcurrency->CurrencyCode . " " . number_format($budgetAmountToUnBlock, $currency->reportingcurrency->DecimalPlaces) . " to unblock the documents " . $documents;
                            } else {
                                $errorMasg[] = $templateDetailData->AccountCode . " - " . $templateDetailData->AccountDescription . " need " . $currency->reportingcurrency->CurrencyCode . " " . number_format($budgetAmountToUnBlock, $currency->reportingcurrency->DecimalPlaces) . " to unblock the documents " . $documents;
                            }
                        }
                    }
                }
            }

            if (count($errorMasg) > 0) {
                return ['status' => false, 'message' => $errorMasg];
            }

            return ['status' => true];
        } else {
            return ['status' => true];
        }
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @SWG\Delete(
     *      path="/erpBudgetAdditions/{id}",
     *      summary="Remove the specified ErpBudgetAddition from storage",
     *      tags={"ErpBudgetAddition"},
     *      description="Delete ErpBudgetAddition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpBudgetAddition",
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
        /** @var ErpBudgetAddition $erpBudgetAddition */
        $erpBudgetAddition = $this->erpBudgetAdditionRepository->findWithoutFail($id);

        if (empty($erpBudgetAddition)) {
            return $this->sendError('Erp Budget Addition not found');
        }

        $erpBudgetAddition->delete();

        return $this->sendSuccess('Erp Budget Addition deleted successfully');
    }

    public function getBudgetAdditionFormData(Request $request)
    {
        $companyId = $request['companyId'];
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = Year::orderBy('year', 'desc')->get();

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);

        $financeYears = CompanyFinanceYear::selectRaw('DATE_FORMAT(bigginingDate,"%d %M %Y") as bigginingDate, DATE_FORMAT(endingDate,"%d %M %Y") as endingDate, companyFinanceYearID')->orderBy('companyFinanceYearID', 'desc')->where('companySystemID', $companyId)->get();


        $segments = SegmentMaster::where("companySystemID", $companyId)
            ->approved()->withAssigned($companyId)
            ->where('isActive', 1)->get();

        if (count($companyFinanceYear) > 0) {
            $startYear = $companyFinanceYear[0]['financeYear'];
            $finYearExp = explode('/', (explode('|', $startYear))[0]);
            $financeYear = (int)$finYearExp[2];
        } else {
            $financeYear = date("Y");
        }


        $budgetMasters = BudgetMaster::where([
            'companySystemID' => $companyId,
            'Year' => $financeYear
        ])->groupBy('templateMasterID')
            ->get();

        $templateIds = collect($budgetMasters)->pluck('templateMasterID')->toArray();

        $masterTemplates = ReportTemplate::whereIn('companyReportTemplateID', $templateIds)->get();

        $output = [
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'financeYears' => $financeYears,
            'companyFinanceYear' => $companyFinanceYear,
            'segments' => $segments,
            'masterTemplates' => $masterTemplates,
            'financeYear' => $financeYear
        ];

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function getTemplatesDetailsByBudgetAddition(Request $request)
    {

        $id = $request->get('id');

        $budgetAdditionMaster = ErpBudgetAddition::find($id);

        if (empty($budgetAdditionMaster)) {
            return $this->sendError('Budget Addition not found');
        }

        $template_details = ReportTemplate::find($budgetAdditionMaster->templatesMasterAutoID)
            ->details()->where([
                'isFinalLevel' => 1,
                'companySystemID' => $budgetAdditionMaster->companySystemID
            ])->get();
        if (empty($template_details)) {
            return $this->sendError('Templates not found');
        }

        return $this->sendResponse($template_details, 'Templates Details retrieved successfully');
    }
    public function getAllGLCodesByBudgetAddition(Request $request){
        $id = $request->get('id');

        $budgetAdditionMaster = ErpBudgetAddition::find($id);

        if (empty($budgetAdditionMaster)) {
            return $this->sendError('Budget Addition not found');
        }

        $templateMaster = ReportTemplate::find($budgetAdditionMaster->templatesMasterAutoID);

        if (empty($templateMaster)) {
            return $this->sendError('Templates Master not found');
        }

        $details = ReportTemplateDetails::where('companyReportTemplateID', $budgetAdditionMaster->templatesMasterAutoID)
            ->where('isFinalLevel', 1)
            ->get();
        $detIDs = collect($details)->pluck('detID')->toArray();
        $templateMasterID = collect($details)->pluck('companyReportTemplateID')->toArray();

        $glData = ReportTemplateLinks::whereNotNull('glAutoID')->whereIn('templateMasterID', $templateMasterID)->whereIn('templateDetailID',$detIDs)->get();

        $glIds = collect($glData)->pluck('glAutoID')->toArray();

        $glCodes = ChartOfAccountsAssigned::where('companySystemID', $request->get('companySystemID'))->whereIn('chartOfAccountSystemID', $glIds)
            ->get(['chartOfAccountSystemID', 'AccountCode', 'AccountDescription', 'controlAccounts']);

        return $this->sendResponse($glCodes, 'GL Codes retrieved successfully');
    }

    public function getTemplateByGLCodeByBudgetAddition(Request $request) {

        $id = $request->get('id');
        $glCodeID = $request->get('glCodeID');

        $companySystemID = $request->get('companySystemID');

        $budgetAdditionMaster = ErpBudgetAddition::find($id);

        if (empty($budgetAdditionMaster)) {
            return $this->sendError('Budget Addition not found');
        }

        $templateMaster = ReportTemplate::find($budgetAdditionMaster->templatesMasterAutoID);

        if (empty($templateMaster)) {
            return $this->sendError('Templates Master not found');
        }

        $details = ReportTemplateDetails::where('companyReportTemplateID', $budgetAdditionMaster->templatesMasterAutoID)
            ->where('isFinalLevel', 1)
            ->get();

        $detIDs = collect($details)->pluck('detID')->toArray();
        $templateMasterID = collect($details)->pluck('companyReportTemplateID')->toArray();

        $glData = ReportTemplateLinks::where('glAutoID',$glCodeID)->whereIn('templateMasterID', $templateMasterID)->whereIn('templateDetailID',$detIDs)->get();

        $templateDetail = ReportTemplateDetails::where('detID', $glData[0]->templateDetailID)->where('companyReportTemplateID',$glData[0]->templateMasterID)->get();

        return $this->sendResponse($templateDetail, 'Template Description retrieved successfully');
    }

    public function getBudgetAdditionApprovalByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approvedYN', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $debitNotes = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'erp_budgetaddition.*',
                'employees.empName As confirmed_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode'
            )
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $query->whereIn('employeesdepartments.documentSystemID', [102])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_budgetaddition', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_budgetaddition.companySystemID', $companyId)
                    ->where('erp_budgetaddition.approvedYN', 0)
                    ->where('erp_budgetaddition.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'confirmedByEmpSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [102])
            ->where('erp_documentapproved.companySystemID', $companyId);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $debitNotes = $debitNotes->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $debitNotes = $debitNotes->where('approvedYN', $input['approvedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $debitNotes = $debitNotes->whereMonth('createdDateTime', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $debitNotes = $debitNotes->whereYear('createdDateTime', '=', $input['year']);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $debitNotes = $debitNotes->where(function ($query) use ($search) {
                $query->where('additionVoucherNo', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'like', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $debitNotes = [];
        }

        return \DataTables::of($debitNotes)
            ->addColumn('Actions', 'Actions', "Actions")
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

    public function getBudgetAdditionApprovedByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approvedYN', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $debitNotes = DB::table('erp_documentapproved')
            ->select(
                'erp_budgetaddition.*',
                'employees.empName As confirmed_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode'
            )
            ->join('erp_budgetaddition', function ($query) use ($companyId) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                    ->where('erp_budgetaddition.companySystemID', $companyId)
                    ->where('erp_budgetaddition.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'confirmedByEmpSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [102])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $debitNotes = $debitNotes->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $debitNotes = $debitNotes->where('approvedYN', $input['approvedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $debitNotes = $debitNotes->whereMonth('createdDateTime', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $debitNotes = $debitNotes->whereYear('createdDateTime', '=', $input['year']);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $debitNotes = $debitNotes->where(function ($query) use ($search) {
                $query->where('additionVoucherNo', 'LIKE', "%$search%")
                    ->orWhere('comments', 'like', "%$search%");
            });
        }

        return \DataTables::of($debitNotes)
            ->addColumn('Actions', 'Actions', "Actions")
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

    public function getDocumentsForErrorMessageOfTransferConfirm($templateDetailID, $consumptionDataWithPoPr)
    {
        $docs = [];
        $comment = "";
        $addedArray = [];
        foreach ($consumptionDataWithPoPr as $key => $value) {
            if ($value['budgetData']['templateDetailID'] == $templateDetailID) {
                if (!in_array($value['docCode'], $addedArray)) {
                    $comment .= (($comment == "") ? " " : ", ") . $value['docCode'];
                    $addedArray[] = $value['docCode'];
                }
            }
        }

        return $comment;
    }

    public function budgetAdditionReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];
        $budgetAddition = $this->erpBudgetAdditionRepository->findWithoutFail($id);
        $emails = array();
        if (empty($budgetAddition)) {
            return $this->sendError('Budget Addition not found');
        }

        if ($budgetAddition->approvedYN == -1) {
            return $this->sendError('You cannot reopen this Budget Addition it is already fully approved');
        }

        if ($budgetAddition->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this Budget Addition it is already partially approved');
        }

        if ($budgetAddition->confirmedYN == 0) {
            return $this->sendError('You cannot reopen this Budget Addition, it is not confirmed');
        }

        $updateInput = [
            'confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
            'confirmedByEmpName' => null, 'confirmedDate' => null, 'RollLevForApp_curr' => 1
        ];

        $this->erpBudgetAdditionRepository->update($updateInput, $id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $budgetAddition->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $budgetAddition->additionVoucherNo . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $budgetAddition->additionVoucherNo;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $budgetAddition->companySystemID)
            ->where('documentSystemCode', $budgetAddition->id)
            ->where('documentSystemID', $budgetAddition->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $budgetAddition->companySystemID)
                    ->where('documentSystemID', $budgetAddition->documentSystemID)
                    ->first();

                if (empty($companyDocument)) {
                    return ['success' => false, 'message' => trans('custom.policy_not_found_for_this_document')];
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

        DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $budgetAddition->companySystemID)
            ->where('documentSystemID', $budgetAddition->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($budgetAddition->documentSystemID, $id, $input['reopenComments'], 'Reopened');

        return $this->sendResponse($budgetAddition->toArray(), 'Budget Addition reopened successfully');
    }
    public function getBudgetAdditionAudit(Request $request)
    {
        $id = $request->get('id');
        $budgetAddition = $this->erpBudgetAdditionRepository->getAudit($id);

        if (empty($budgetAddition)) {
            return $this->sendError('Budget Addition not found');
        }

        return $this->sendResponse($budgetAddition, 'Budget Addition audit detailed retrived');
    }
    public function amendBudgetAddition(Request $request)
    {
        $input =  $request->all();
        $budgetAdditionID = $input['budgetAdditionID'];
        $budgetAdditionMasterData = ErpBudgetAddition::find($budgetAdditionID);

        if (empty($budgetAdditionMasterData)) {
            return $this->sendError('Budget Addition not found');
        }

        if ($budgetAdditionMasterData->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this budget addition');
        }

        $budgetAdditionArray = $budgetAdditionMasterData->toArray();
        $storeBudgetAdditionHistory = BudgetAdditionRefferedBack::insert($budgetAdditionArray);

        $budgetAdditionDetailRec = ErpBudgetAdditionDetail::where('budgetAdditionFormAutoID', $budgetAdditionID)->get();

        if (!empty($budgetAdditionDetailRec)) {
            foreach ($budgetAdditionDetailRec as $budgetAdds) {
                $budgetAdds['timesReferred'] = $budgetAdditionMasterData->timesReferred;
            }
        }

        $budgetAdditionDetailArray = $budgetAdditionDetailRec->toArray();
        $storeAssetTransferDetailHistory = BudgetAdditionDetailRefferedBack::insert($budgetAdditionDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $budgetAdditionID)
            ->where('companySystemID', $budgetAdditionMasterData->companySystemID)
            ->where('documentSystemID', $budgetAdditionMasterData->documentSystemID)
            ->get();


        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $budgetAdditionMasterData->timesReferred;
            }
        }
        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $budgetAdditionID)
            ->where('companySystemID', $budgetAdditionMasterData->companySystemID)
            ->where('documentSystemID', $budgetAdditionMasterData->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $budgetAdditionMasterData->refferedBackYN = 0;
            $budgetAdditionMasterData->confirmedYN = 0;
            $budgetAdditionMasterData->confirmedDate = null;
            $budgetAdditionMasterData->confirmedByEmpSystemID = null;
            $budgetAdditionMasterData->confirmedByEmpID = null;
            $budgetAdditionMasterData->confirmedByEmpName = null;
            $budgetAdditionMasterData->RollLevForApp_curr = 1;
            $budgetAdditionMasterData->save();
        }
        return $this->sendResponse($budgetAdditionMasterData->toArray(), 'Budget Addition amend successfully');
    }
}
