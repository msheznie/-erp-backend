<?php

/**
 * =============================================
 * -- File Name : BudgetTransferFormAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Budget Transfer
 * -- Author : Mohamed Fayas
 * -- Create date : 18 - October 2018
 * -- Description : This file contains the all CRUD for Budget Transfer
 * -- REVISION HISTORY
 * -- Date: 18-October 2018 By: Fayas Description: Added new function getBudgetTransferMasterByCompany()
 * -- Date: 22-October 2018 By: Fayas Description: Added new function getBudgetTransferAudit(),budgetTransferReopen(),
 *                      getBudgetTransferApprovedByUser(),getBudgetTransferApprovalByUser()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetTransferFormAPIRequest;
use App\Http\Requests\API\UpdateBudgetTransferFormAPIRequest;
use App\Models\BudgetTransferForm;
use App\Models\ReportTemplateDetails;
use App\Models\ReportTemplate;
use App\Models\BudgetReviewTransferAddition;
use App\Models\BudgetTransferFormDetail;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentApproved;
use App\Models\ChartOfAccount;
use App\Models\DocumentMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\EmployeesDepartment;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\TemplatesMaster;
use App\Models\ErpBudgetAddition;
use App\Models\Year;
use App\Models\YesNoSelection;
use App\helper\BudgetConsumptionService;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\BudgetTransferFormRepository;
use App\Traits\AuditTrial;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\BudgetTransferFormDetailRefferedBack;
use App\Models\BudgetTransferFormRefferedBack;
use App\Models\DocumentReferedHistory;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;

/**
 * Class BudgetTransferFormController
 * @package App\Http\Controllers\API
 */

class BudgetTransferFormAPIController extends AppBaseController
{
    /** @var  BudgetTransferFormRepository */
    private $budgetTransferFormRepository;

    public function __construct(BudgetTransferFormRepository $budgetTransferFormRepo)
    {
        $this->budgetTransferFormRepository = $budgetTransferFormRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetTransferForms",
     *      summary="Get a listing of the BudgetTransferForms.",
     *      tags={"BudgetTransferForm"},
     *      description="Get all BudgetTransferForms",
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
     *                  @SWG\Items(ref="#/definitions/BudgetTransferForm")
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
        $this->budgetTransferFormRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetTransferFormRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetTransferForms = $this->budgetTransferFormRepository->all();

        return $this->sendResponse($budgetTransferForms->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.budget_transfer_form')]));
    }

    /**
     * @param CreateBudgetTransferFormAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetTransferForms",
     *      summary="Store a newly created BudgetTransferForm in storage",
     *      tags={"BudgetTransferForm"},
     *      description="Store BudgetTransferForm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetTransferForm that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetTransferForm")
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
     *                  ref="#/definitions/BudgetTransferForm"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetTransferFormAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['createdDate'] = now();

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

        $input['documentSystemID'] = 46;
        $input['documentID'] = 'BTN';

        $lastSerial = BudgetTransferForm::where('companySystemID', $input['companySystemID'])
            ->orderBy('budgetTransferFormAutoID', 'desc')
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
            $input['transferVoucherNo'] = $code;
        }

        $budgetTransferForms = $this->budgetTransferFormRepository->create($input);

        return $this->sendResponse($budgetTransferForms->toArray(), 'Budget Transfer Form saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetTransferForms/{id}",
     *      summary="Display the specified BudgetTransferForm",
     *      tags={"BudgetTransferForm"},
     *      description="Get BudgetTransferForm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferForm",
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
     *                  ref="#/definitions/BudgetTransferForm"
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
        /** @var BudgetTransferForm $budgetTransferForm */
        $budgetTransferForm = $this->budgetTransferFormRepository->with(['company.reportingcurrency', 'created_by', 'confirmed_by', 'from_reviews'])->findWithoutFail($id);

        if (empty($budgetTransferForm)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_transfer_form')]));
        }

        return $this->sendResponse($budgetTransferForm->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.budget_transfer_form')]));
    }

    /**
     * @param int $id
     * @param UpdateBudgetTransferFormAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetTransferForms/{id}",
     *      summary="Update the specified BudgetTransferForm in storage",
     *      tags={"BudgetTransferForm"},
     *      description="Update BudgetTransferForm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferForm",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetTransferForm that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetTransferForm")
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
     *                  ref="#/definitions/BudgetTransferForm"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetTransferFormAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmed_by', 'company']);
        $input = $this->convertArrayToValue($input);
        /** @var BudgetTransferForm $budgetTransferForm */
        $budgetTransferForm = $this->budgetTransferFormRepository->findWithoutFail($id);

        if (empty($budgetTransferForm)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_transfer_form')]));
        }

        $employee = \Helper::getEmployeeInfo();

        if ($budgetTransferForm->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $validator = \Validator::make($input, [
                'year' => 'required|numeric|min:1',
                'comments' => 'required',
                'templatesMasterAutoID' => 'required|numeric|min:1'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $checkItems = BudgetTransferFormDetail::where('budgetTransferFormAutoID', $id)
                ->count();
            if ($checkItems == 0) {
                return $this->sendError(trans('custom.every_budget_transfer_should_have_at_least_one_item'), 500);
            }

            $checkQuantity = BudgetTransferFormDetail::where('budgetTransferFormAutoID', $id)
                ->where(function ($q) {
                    $q->where('adjustmentAmountRpt', '<=', 0)
                        ->orWhereNull('adjustmentAmountLocal', '<=', 0)
                        ->orWhereNull('adjustmentAmountRpt')
                        ->orWhereNull('adjustmentAmountLocal');
                })
                ->count();
            if ($checkQuantity > 0) {
                return $this->sendError(trans('custom.amount_should_be_greater_than_0_for_every_items'), 500);
            }

            $debitNoteDetails = BudgetTransferFormDetail::where('budgetTransferFormAutoID', $id)->get();

            $finalError = array(
                'balance_check' => array(),
                'required_serviceLine_from' => array(),
                'active_serviceLine_from' => array(),
                'required_serviceLine_to' => array(),
                'active_serviceLine_to' => array(),
            );
            $error_count = 0;

            foreach ($debitNoteDetails as $item) {
                $updateItem = BudgetTransferFormDetail::find($item['budgetTransferFormDetailAutoID']);

                if ($updateItem->toServiceLineSystemID && !is_null($updateItem->toServiceLineSystemID)) {

                    $checkDepartmentActiveTo = SegmentMaster::where('serviceLineSystemID', $updateItem->serviceLineSystemID)
                        ->where('isActive', 1)
                        ->first();
                    if (empty($checkDepartmentActiveTo)) {
                        array_push($finalError['active_serviceLine_to'], $updateItem->glCode);
                        $error_count++;
                    }
                } else {
                    array_push($finalError['required_serviceLine_to'], $updateItem->glCode);
                    $error_count++;
                }
            }

            $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
            if ($error_count > 0) {
                // return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
            }

            $checkBudgetFromReview = $this->validateBudgetFormReview($id, $budgetTransferForm, $debitNoteDetails);
            if (!$checkBudgetFromReview['status']) {
                return $this->sendError("You cannot confirm this document.", 500, array('type' => 'confirm_error_budget_review', 'data' => $checkBudgetFromReview['message']));
            }

            $input['RollLevForApp_curr'] = 1;
            $params = array(
                'autoID' => $id,
                'company' => $budgetTransferForm->companySystemID,
                'document' => $budgetTransferForm->documentSystemID,
                'segment' => 0,
                'category' => 0,
                'amount' => 0
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        $budgetTransferForm = $this->budgetTransferFormRepository->update(array_only($input, ['comments', 'year', 'templatesMasterAutoID', 'modifiedPc', 'modifiedUser', 'modifiedUserSystemID']), $id);

        return $this->sendReponseWithDetails($budgetTransferForm->toArray(), trans('custom.update', ['attribute' => trans('custom.budget_transfer')]),1,$confirm['data'] ?? null);
    }

    public function validateBudgetFormReview($budgetTransferFormAutoID, $budgetTransferForm, $details)
    {
        $checkFormReview = BudgetReviewTransferAddition::where('budgetTransferAdditionID', $budgetTransferFormAutoID)
            ->where('budgetTransferType', 1)
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

                        $transferedAmount = BudgetTransferFormDetail::where('toTemplateDetailID', $value[0]['templateDetailID'])
                            ->when($departmentWiseCheckBudgetPolicy == true, function ($query) use ($value) {
                                $query->where('toServiceLineSystemID', $value[0]['serviceLineSystemID']);
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

                        $transferedAmount = BudgetTransferFormDetail::where('toChartOfAccountSystemID', $value[0]['templateDetailID'])
                            ->when($departmentWiseCheckBudgetPolicy == true, function ($query) use ($value) {
                                $query->where('toServiceLineSystemID', $value[0]['serviceLineSystemID']);
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
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetTransferForms/{id}",
     *      summary="Remove the specified BudgetTransferForm from storage",
     *      tags={"BudgetTransferForm"},
     *      description="Delete BudgetTransferForm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferForm",
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
        /** @var BudgetTransferForm $budgetTransferForm */
        $budgetTransferForm = $this->budgetTransferFormRepository->findWithoutFail($id);

        if (empty($budgetTransferForm)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_transfer_form')]));
        }

        $budgetTransferForm->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.budget_transfer_form')]));
    }

    public function getBudgetTransferMasterByCompany(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approvedYN', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $budgetTransfer = $this->budgetTransferFormRepository->budgetTransferFormListQuery($request, $input, $search);

        return \DataTables::of($budgetTransfer)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('budgetTransferFormAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getBudgetTransferFormData(Request $request)
    {
        $companyId = $request['companyId'];
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $yearsArray = Year::orderBy('year', 'desc')->get();
        $years = CompanyFinanceYear::selectRaw('DATE_FORMAT(bigginingDate,"%d %M %Y") as bigginingDate, DATE_FORMAT(endingDate,"%d %M %Y") as endingDate, companyFinanceYearID')->orderBy('companyFinanceYearID', 'desc')->where('companySystemID', $companyId)->get();


        $companyFinanceYear = \Helper::companyFinanceYear($companyId);

        $segments = SegmentMaster::where("companySystemID", $companyId)
            ->where('isActive', 1)->get();

        $masterTemplates = ReportTemplate::where('isActive', 1)
            ->where('companySystemID', $companyId)
            ->whereNotIn('reportID', [3, 4])
            ->get();


        if (count($companyFinanceYear) > 0) {
            $startYear = $companyFinanceYear[0]['financeYear'];
            $finYearExp = explode('/', (explode('|', $startYear))[0]);
            $financeYear = (int)$finYearExp[2];
        } else {
            $financeYear = date("Y");
        }

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'yearsArray' => $yearsArray,
            'companyFinanceYear' => $companyFinanceYear,
            'segments' => $segments,
            'masterTemplates' => $masterTemplates,
            'financeYear' => $financeYear
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function getBudgetTransferAudit(Request $request)
    {
        $id = $request->get('id');
        $budgetTransfer = $this->budgetTransferFormRepository->getAudit($id);

        if (empty($budgetTransfer)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_transfer')]));
        }

        return $this->sendResponse($budgetTransfer, trans('custom.retrieve', ['attribute' => trans('custom.budget_transfer_audit')]));
    }

    public function budgetTransferReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['budgetTransferFormAutoID'];
        $budgetTransfer = $this->budgetTransferFormRepository->findWithoutFail($id);
        $emails = array();
        if (empty($budgetTransfer)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_transfer')]));
        }

        if ($budgetTransfer->approvedYN == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_budget_transfer_it_is_already_fully_approved'));
        }

        if ($budgetTransfer->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_budget_transfer_it_is_already_partially_approved'));
        }

        if ($budgetTransfer->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_budget_transfer_it_is_not_confirmed'));
        }

        $updateInput = [
            'confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
            'confirmedByName' => null, 'confirmedDate' => null, 'RollLevForApp_curr' => 1
        ];

        $this->budgetTransferFormRepository->update($updateInput, $id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $budgetTransfer->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $budgetTransfer->transferVoucherNo . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $budgetTransfer->transferVoucherNo;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $budgetTransfer->companySystemID)
            ->where('documentSystemCode', $budgetTransfer->budgetTransferFormAutoID)
            ->where('documentSystemID', $budgetTransfer->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $budgetTransfer->companySystemID)
                    ->where('documentSystemID', $budgetTransfer->documentSystemID)
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
            ->where('companySystemID', $budgetTransfer->companySystemID)
            ->where('documentSystemID', $budgetTransfer->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($budgetTransfer->documentSystemID, $id, $input['reopenComments'], 'Reopened');

        return $this->sendResponse($budgetTransfer->toArray(), trans('custom.reopened', ['attribute' => trans('custom.budget_transfer')]));
    }


    public function getBudgetTransferApprovedByUser(Request $request)
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
                'erp_budgettransferform.*',
                'employees.empName As confirmed_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode'
            )
            ->join('erp_budgettransferform', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'budgetTransferFormAutoID')
                    ->where('erp_budgettransferform.companySystemID', $companyId)
                    ->where('erp_budgettransferform.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'confirmedByEmpSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [46])
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


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $debitNotes = $debitNotes->where(function ($query) use ($search) {
                $query->where('transferVoucherNo', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'like', "%{$search}%");
            });
        }

        return \DataTables::of($debitNotes)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('budgetTransferFormAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


    public function getBudgetTransferApprovalByUser(Request $request)
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
                'erp_budgettransferform.*',
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

                $query->whereIn('employeesdepartments.documentSystemID', [46])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_budgettransferform', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'budgetTransferFormAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_budgettransferform.companySystemID', $companyId)
                    ->where('erp_budgettransferform.approvedYN', 0)
                    ->where('erp_budgettransferform.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'confirmedByEmpSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [46])
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


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $debitNotes = $debitNotes->where(function ($query) use ($search) {
                $query->where('transferVoucherNo', 'LIKE', "%{$search}%")
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
                        $query->orderBy('budgetTransferFormAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function budgetTransferCreateFromReview(Request $request)
    {
        $input = $request->all();

        if (!isset($input['type']) || (isset($input['type']) && $input['type'] == "")) {
            return $this->sendError("Please choose budget create type", 500);
        }

        if ($input['type'] == 1) {
            return $this->createBudgetTransferFromReview($input);
        } else {
            return $this->createBudgetAdditionFromReview($input);
        }
    }

    public function createBudgetTransferFromReview($input)
    {
        $selectedPoPrs = collect($input['budgetReviews'])->where('selected', 1)->all();

        $budgetYears = array_unique(collect($selectedPoPrs)->pluck('budgetYear')->toArray());
        if (count($budgetYears) != 1) {
            return $this->sendError("Different Budget year cannot be selected", 500);
        }


        $consumptionData = [];
        $consumptionDataWithPoPr = [];
        foreach ($selectedPoPrs as $key => $value) {
            $res = BudgetConsumptionService::getConsumptionData($value['documentSystemID'], $value['documentSystemCode']);

            if ($res['status'] && isset($res['data']) && count($res['data']) > 0) {
                foreach ($res['data'] as $key1 => $value1) {
                    $consumptionData[] = $value1;
                    $temp['budgetData'] = $value1;
                    $temp['poData'] = $value;

                    $consumptionDataWithPoPr[] = $temp;
                }
            }
        }

        DB::beginTransaction();
        try {

            $templateIDs = array_unique(collect($consumptionData)->pluck('companyReportTemplateID')->toArray());
            if (count($templateIDs) == 0) {
                return $this->sendError("Budget not found for this document", 500);
            }

            $budgetTransferCods = "";
            foreach ($templateIDs as $key => $value) {
                $commentAndDoc = $this->getBudgetTransferComment($value, $consumptionDataWithPoPr, 1);

                $employee = \Helper::getEmployeeInfo();
                $saveData['createdPcID'] = gethostname();
                $saveData['createdUserID'] = $employee->empID;
                $saveData['createdUserSystemID'] = $employee->employeeSystemID;
                $saveData['createdDate'] = now();
                $saveData['year'] = $budgetYears[0];
                $saveData['companyFinanceYearID'] = CompanyFinanceYear::financeYearID($budgetYears[0], $input['companySystemID']);
                $saveData['comments'] = $commentAndDoc['comment'];
                $saveData['companySystemID'] = $input['companySystemID'];
                $saveData['templatesMasterAutoID'] = $value;
                $saveData['documentSystemID'] = 46;
                $saveData['documentID'] = 'BTN';
                $saveData['documentID'] = 'BTN';

                $lastSerial = BudgetTransferForm::where('companySystemID', $input['companySystemID'])
                    ->orderBy('budgetTransferFormAutoID', 'desc')
                    ->first();

                $lastSerialNumber = 1;
                if ($lastSerial) {
                    $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                }

                $company = Company::where('companySystemID', $input['companySystemID'])->first();

                if (empty($company)) {
                    return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company')]), 500);
                }

                $saveData['companyID'] = $company->CompanyID;
                $saveData['serialNo'] = $lastSerialNumber;
                $saveData['RollLevForApp_curr'] = 1;

                $documentMaster = DocumentMaster::where('documentSystemID', $saveData['documentSystemID'])->first();

                if ($documentMaster) {
                    $code = ($company->CompanyID . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                    $saveData['transferVoucherNo'] = $code;

                    $budgetTransferCods .= (($budgetTransferCods == "") ? "" : ", ") . $code;
                }

                $budgetTransferForms = $this->budgetTransferFormRepository->create($saveData);

                if ($budgetTransferForms) {
                    foreach ($commentAndDoc['docs'] as $key => $value) {
                        $createData = [
                            'budgetTransferAdditionID' => $budgetTransferForms->budgetTransferFormAutoID,
                            'budgetTransferType' => 1,
                            'documentSystemCode' => $value['documentSystemCode'],
                            'documentSystemID' => $value['documentSystemID']
                        ];

                        BudgetReviewTransferAddition::insert($createData);
                    }
                }
            }

            DB::commit();
            return $this->sendResponse([], " Budget transfer(s) " . $budgetTransferCods . " is/are created for selected documents");
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function createBudgetAdditionFromReview($input)
    {
        $selectedPoPrs = collect($input['budgetReviews'])->where('selected', 1)->all();

        $budgetYears = array_unique(collect($selectedPoPrs)->pluck('budgetYear')->toArray());
        if (count($budgetYears) != 1) {
            return $this->sendError("Different Budget year cannot be selected", 500);
        }


        $consumptionData = [];
        $consumptionDataWithPoPr = [];
        foreach ($selectedPoPrs as $key => $value) {
            $res = BudgetConsumptionService::getConsumptionData($value['documentSystemID'], $value['documentSystemCode']);

            if ($res['status'] && isset($res['data']) && count($res['data']) > 0) {
                foreach ($res['data'] as $key1 => $value1) {
                    $consumptionData[] = $value1;
                    $temp['budgetData'] = $value1;
                    $temp['poData'] = $value;

                    $consumptionDataWithPoPr[] = $temp;
                }
            }
        }

        DB::beginTransaction();
        try {

            $templateIDs = array_unique(collect($consumptionData)->pluck('companyReportTemplateID')->toArray());
            if (count($templateIDs) == 0) {
                return $this->sendError("Budget not found for this document", 500);
            }

            $budgetAdditionsCods = "";
            foreach ($templateIDs as $key => $value) {
                $commentAndDoc = $this->getBudgetTransferComment($value, $consumptionDataWithPoPr, 2);

                $employee = \Helper::getEmployeeInfo();
                $saveData['createdPcID'] = gethostname();
                $saveData['createdUserID'] = $employee->empID;
                $saveData['createdUserSystemID'] = $employee->employeeSystemID;
                $saveData['createdDate'] = now();
                $saveData['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
                $saveData['modifiedUser'] = \Helper::getEmployeeID();
                $saveData['modifiedPc'] = gethostname();
                $saveData['year'] = $budgetYears[0];
                $saveData['companyFinanceYearID'] = CompanyFinanceYear::financeYearID($budgetYears[0], $input['companySystemID']);
                $saveData['comments'] = $commentAndDoc['comment'];
                $saveData['companySystemID'] = $input['companySystemID'];
                $saveData['templatesMasterAutoID'] = $value;
                $saveData['documentSystemID'] = 102;
                $saveData['documentID'] = 'BDA';

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

                $saveData['companyID'] = $company->CompanyID;
                $saveData['serialNo'] = $lastSerialNumber;
                $saveData['RollLevForApp_curr'] = 1;

                $documentMaster = DocumentMaster::where('documentSystemID', $saveData['documentSystemID'])->first();

                if ($documentMaster) {
                    $code = ($company->CompanyID . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                    $saveData['additionVoucherNo'] = $code;

                    $budgetAdditionsCods .= (($budgetAdditionsCods == "") ? "" : ", ") . $code;
                }

                $budgetAddition = ErpBudgetAddition::create($saveData);

                if ($budgetAddition) {
                    foreach ($commentAndDoc['docs'] as $key => $value) {
                        $createData = [
                            'budgetTransferAdditionID' => $budgetAddition->id,
                            'budgetTransferType' => 2,
                            'documentSystemCode' => $value['documentSystemCode'],
                            'documentSystemID' => $value['documentSystemID']
                        ];

                        BudgetReviewTransferAddition::insert($createData);
                    }
                }
            }

            DB::commit();
            return $this->sendResponse([], " Budget addition(s) " . $budgetAdditionsCods . " is/are created for selected documents");
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getBudgetTransferComment($templatesMasterAutoID, $consumptionDataWithPoPr, $type)
    {
        $docs = [];
        $addedArray = [];
        $comment = "";
        foreach ($consumptionDataWithPoPr as $key => $value) {
            if ($value['budgetData']['companyReportTemplateID'] == $templatesMasterAutoID) {
                if (!in_array($value['poData']['documentCode'], $addedArray)) {
                    $docs[] = $value['poData'];
                    $addedArray[] = $value['poData']['documentCode'];
                    $comment .= (($comment == "") ? " " : ", ") . $value['poData']['documentCode'];
                }
            }
        }

        $docName = ($type == 1) ? "transfer" : "addition";

        return ['docs' => $docs, 'comment' => "Budget " . $docName . " created for " . $comment];
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

    public function amendBudgetTrasfer(Request $request)
    {
        $input =  $request->all();
        $budgetTransferID = $input['budgetTransferID'];

        $budgetTransferMasterData = BudgetTransferForm::find($budgetTransferID);

        if (empty($budgetTransferMasterData)) {
            return $this->sendError('Budget Transfer not found');
        }

        if ($budgetTransferMasterData->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this budget transfer');
        }

        $budgetTransferArray = $budgetTransferMasterData->toArray();
        $storeBudgetTransferHistory = BudgetTransferFormRefferedBack::insert($budgetTransferArray);
        $budgetTransferDetailRec = BudgetTransferFormDetail::where('budgetTransferFormAutoID', $budgetTransferID)->get();

        if (!empty($budgetTransferDetailRec)) {
            foreach ($budgetTransferDetailRec as $budgetTrans) {
                $budgetTrans['timesReferred'] = $budgetTransferMasterData->timesReferred;
            }
        }
        $budgetTransferDetailArray = $budgetTransferDetailRec->toArray();
        $storeAssetTransferDetailHistory = BudgetTransferFormDetailRefferedBack::insert($budgetTransferDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $budgetTransferID)
            ->where('companySystemID', $budgetTransferMasterData->companySystemID)
            ->where('documentSystemID', $budgetTransferMasterData->documentSystemID)
            ->get();


        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $budgetTransferMasterData->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $budgetTransferID)
            ->where('companySystemID', $budgetTransferMasterData->companySystemID)
            ->where('documentSystemID', $budgetTransferMasterData->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $budgetTransferMasterData->refferedBackYN = 0;
            $budgetTransferMasterData->confirmedYN = 0;
            $budgetTransferMasterData->confirmedDate = null;
            $budgetTransferMasterData->confirmedByEmpSystemID = null;
            $budgetTransferMasterData->confirmedByEmpID = null;
            $budgetTransferMasterData->confirmedByEmpName = null;
            $budgetTransferMasterData->RollLevForApp_curr = 1;
            $budgetTransferMasterData->save();
        }
        return $this->sendResponse($budgetTransferMasterData->toArray(), 'Budget Transfer amend successfully');
    }
}
