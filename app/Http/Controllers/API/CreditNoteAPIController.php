<?php
/**
 * =============================================
 * -- File Name : CreditNoteAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  CreditNote
 * -- Author : Mohamed Sahmy
 * -- Create date : 08 - September 2018
 * -- Description : This file contains the all CRUD for Credit Note
 * -- REVISION HISTORY
 * -- Date: 26-November 2018 By: Nazir Description: Added new function amendCreditNote(),
 * -- Date: 11-January 2019 By: Muabashir Description: Added new function approvalPreCheckCreditNote(),
 * -- Date: 23-January 2019 By: Nazir Store function, update function issues fixed and modified,
 * -- Date: 13-June 2019 By: Fayas Description: Added new function amendCreditNoteReview(),
 */

namespace App\Http\Controllers\API;

use App\helper\TaxService;
use App\Http\Requests\API\CreateCreditNoteAPIRequest;
use App\Http\Requests\API\UpdateCreditNoteAPIRequest;
use App\Models\AccountsReceivableLedger;
use App\Models\CompanyPolicyMaster;
use App\Models\CreditNote;
use App\Models\ChartOfAccountsAssigned;
use App\Models\CreditNoteDetails;
use App\Models\CreditNoteDetailsRefferdback;
use App\Models\CreditNoteReferredback;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DebitNote;
use App\Models\DocumentReferedHistory;
use App\Models\GeneralLedger;
use App\Models\MatchDocumentMaster;
use App\Models\Taxdetail;
use App\Models\TaxLedger;
use App\Models\TaxLedgerDetail;
use App\Models\YesNoSelectionForMinus;
use App\Models\YesNoSelection;
use App\Models\Months;
use App\Models\ErpDocumentTemplate;
use App\Models\CustomerAssigned;
use App\Models\DocumentMaster;
use App\Models\ModuleAssigned;
use App\Models\DocumentApproved;
use App\Models\EmployeesDepartment;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyFinancePeriod;
use App\Models\CustomerMaster;
use App\Models\Company;
use App\Models\ErpProjectMaster;
use App\Models\SegmentMaster;
use App\Repositories\VatReturnFillingMasterRepository;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use App\Models\CustomerCurrency;
use App\Repositories\CreditNoteRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Jobs\CreateCreditNote;
use App\Models\DocumentSystemMapping;
use App\Services\API\CreditNoteAPIService;
use App\Services\GeneralLedgerService;
use App\Services\ValidateDocumentAmend;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class CreditNoteController
 * @package App\Http\Controllers\API
 */
class CreditNoteAPIController extends AppBaseController
{
    /** @var  CreditNoteRepository */
    private $creditNoteRepository;
    private $vatReturnFillingMasterRepo;

    public function __construct(CreditNoteRepository $creditNoteRepo, VatReturnFillingMasterRepository $vatReturnFillingMasterRepo)
    {
        $this->creditNoteRepository = $creditNoteRepo;
        $this->vatReturnFillingMasterRepo = $vatReturnFillingMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/creditNotes",
     *      summary="Get a listing of the CreditNotes.",
     *      tags={"CreditNote"},
     *      description="Get all CreditNotes",
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
     *                  @SWG\Items(ref="#/definitions/CreditNote")
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
        $this->creditNoteRepository->pushCriteria(new RequestCriteria($request));
        $this->creditNoteRepository->pushCriteria(new LimitOffsetCriteria($request));
        $creditNotes = $this->creditNoteRepository->all();

        return $this->sendResponse($creditNotes->toArray(), trans('custom.credit_notes_retrieved_successfully'));
    }

    /**
     * @param CreateCreditNoteAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/creditNotes",
     *      summary="Store a newly created CreditNote in storage",
     *      tags={"CreditNote"},
     *      description="Store CreditNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CreditNote that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreditNote")
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
     *                  ref="#/definitions/CreditNote"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCreditNoteAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('companyFinancePeriodID', 'companyFinanceYearID', 'currencyID', 'customerCurrencyID'));
        $company = Company::select('CompanyID')->where('companySystemID', $input['companySystemID'])->first();
        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();

        if (!\Helper::validateCurrencyRate($input['companySystemID'], $input['customerCurrencyID'])) {
            return $this->sendError(
                trans('custom.currency_exchange_rate_must_be_greater_than_zero'),
                500
            );
        }

        if (isset($input['debitNoteAutoID'])) {
            $alreadyUsed = CreditNote::where('debitNoteAutoID', $input['debitNoteAutoID'])
                ->first();

            if ($alreadyUsed) {
                return $this->sendError(trans('custom.debit_note_already_used', ['creditNoteCode' => $alreadyUsed->creditNoteCode]), 500);
            }
        }

        $curentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
        if ($input['creditNoteDate'] > $curentDate) {
            return $this->sendError(trans('custom.document_date_cannot_be_greater_than_current_date'), 500);
        }

        $companyfinanceyear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        $lastSerial = CreditNote::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        if ($companyfinanceyear) {
            $startYear = $companyfinanceyear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];

            $input['FYBiggin'] = $companyfinanceyear->bigginingDate;
            $input['FYEnd'] = $companyfinanceyear->endingDate;
        } else {
            $finYear = date("Y");
        }

        $input['companyID'] = $company->CompanyID;
        $input['documentSystemiD'] = 19;
        $input['documentID'] = 'CN';
        $input['serialNo'] = $lastSerialNumber;
        $input['FYPeriodDateFrom'] = $companyfinanceperiod->dateFrom;
        $input['FYPeriodDateTo'] = $companyfinanceperiod->dateTo;
        $input['creditNoteDate'] = Carbon::parse($input['creditNoteDate'])->format('Y-m-d') . ' 00:00:00';
        $input['customerGLCodeSystemID'] = $customer->custGLAccountSystemID;
        $input['customerGLCode'] = $customer->custGLaccount;
        $input['documentType'] = 12;

        $documentDate = $input['creditNoteDate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError(trans('custom.document_date_not_within_financial_period'));
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['customerCurrencyID'], $input['customerCurrencyID'], 0);

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['localCurrencyID'] = $company->localCurrencyID;
            $input['companyReportingCurrencyID'] = $company->reportingCurrency;
            $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        }

        $creditNoteCode = ($company->CompanyID . '\\' . $finYear . '\\' . 'CN' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
        $input['creditNoteCode'] = $creditNoteCode;

        $input['customerCurrencyER'] = 1;
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdPcID'] = getenv('COMPUTERNAME');
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');

        $creditNotes = $this->creditNoteRepository->create($input);

        return $this->sendResponse($creditNotes->toArray(), trans('custom.credit_note_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/creditNotes/{id}",
     *      summary="Display the specified CreditNote",
     *      tags={"CreditNote"},
     *      description="Get CreditNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNote",
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
     *                  ref="#/definitions/CreditNote"
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
        /** @var CreditNote $creditNote */
        $creditNote = $this->creditNoteRepository->with(['currency', 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'debitNote' => function ($query) {
            $query->select('debitNoteAutoID', 'debitNoteCode');
        },'customer'])->findWithoutFail($id);

        if (empty($creditNote)) {
            return $this->sendError(trans('custom.credit_note_not_found'));
        }

        return $this->sendResponse($creditNote->toArray(), trans('custom.credit_note_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateCreditNoteAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/creditNotes/{id}",
     *      summary="Update the specified CreditNote in storage",
     *      tags={"CreditNote"},
     *      description="Update CreditNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNote",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CreditNote that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreditNote")
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
     *                  ref="#/definitions/CreditNote"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCreditNoteAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('companyFinancePeriodID', 'confirmedYN', 'companyFinanceYearID', 'customerID', 'secondaryLogoCompanySystemID', 'customerCurrencyID', 'projectID'));

        $input = array_except($input, array('finance_period_by', 'finance_year_by', 'currency', 'createdDateAndTime',
            'confirmedByEmpSystemID', 'confirmedByEmpID', 'confirmedByName', 'confirmedDate','customer'));

        /** @var CreditNote $creditNote */
        $creditNote = $this->creditNoteRepository->findWithoutFail($id);
        if (empty($creditNote)) {
            return $this->sendError(trans('custom.credit_note_not_found'), 500);
        }

        if(empty($input['projectID'])){
            $input['projectID'] = null;
        }

        if (isset($input['debitNoteAutoID'])) {
            $alreadyUsed = CreditNote::where('debitNoteAutoID', $input['debitNoteAutoID'])
                ->where('creditNoteAutoID', '<>', $id)
                ->first();

            if ($alreadyUsed) {
                return $this->sendError(trans('custom.debit_note_already_used', ['creditNoteCode' => $alreadyUsed->creditNoteCode]), 500);
            }
        }

        $detail = CreditNoteDetails::where('creditNoteAutoID', $id)->get();

        $input['departmentSystemID'] = 4;

        /*financial Year check*/
        $companyFinanceYearCheck = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYearCheck["success"]) {
            return $this->sendError($companyFinanceYearCheck["message"], 500);
        }
        /*financial Period check*/
        $companyFinancePeriodCheck = \Helper::companyFinancePeriodCheck($input);
        if (!$companyFinancePeriodCheck["success"]) {
            return $this->sendError($companyFinancePeriodCheck["message"], 500);
        }

        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $input['FYPeriodDateFrom'] = $companyfinanceperiod->dateFrom;
        $input['FYPeriodDateTo'] = $companyfinanceperiod->dateTo;


        if(isset($input['customerCurrencyID']) && isset($input['companySystemID'])){
            $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['customerCurrencyID'], $input['customerCurrencyID'], 0);
            $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
                ->where('companyPolicyCategoryID', 67)
                ->where('isYesNO', 1)
                ->first();
            $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;

            if($policy == false) {
                if ($companyCurrencyConversion) {
                    $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                }
            }
        }
        if ($input['secondaryLogoCompanySystemID'] != $creditNote->secondaryLogoCompanySystemID) {
            if ($input['secondaryLogoCompanySystemID'] != '') {
                $company = Company::where('companySystemID', $input['secondaryLogoCompanySystemID'])->first();
                $input['secondaryLogoCompID'] = $company->CompanyID;
                $input['secondaryLogo'] = $company->logo_url;
            } else {
                $input['secondaryLogoCompID'] = NULL;
                $input['secondaryLogo'] = NULL;
            }

        }

        $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
        if ($customer) {
            $input['customerGLCode'] = $customer->custGLaccount;
            $input['customerGLCodeSystemID'] = $customer->custGLAccountSystemID;
        }

        // updating header amounts
        $totalAmount = CreditNoteDetails::selectRaw("COALESCE(SUM(creditAmount),0) as creditAmountTrans, 
                                                    COALESCE(SUM(localAmount),0) as creditAmountLocal, 
                                                    COALESCE(SUM(comRptAmount),0) as creditAmountRpt,
                                                    COALESCE(SUM(VATAmount),0) as VATAmount,
                                                    COALESCE(SUM(VATAmountLocal),0) as VATAmountLocal, 
                                                    COALESCE(SUM(VATAmountRpt),0) as VATAmountRpt,
                                                    COALESCE(SUM(netAmount),0) as netAmount,
                                                    COALESCE(SUM(netAmountLocal),0) as netAmountLocal, 
                                                    COALESCE(SUM(netAmountRpt),0) as netAmountRpt
                                                    ")
                                            ->where('creditNoteAutoID', $id)
                                            ->first();

        $input['creditAmountTrans'] = \Helper::roundValue($totalAmount->creditAmountTrans);
        $input['creditAmountLocal'] = \Helper::roundValue($totalAmount->creditAmountLocal);
        $input['creditAmountRpt'] = \Helper::roundValue($totalAmount->creditAmountRpt);


        $input['VATAmount'] = \Helper::roundValue($totalAmount->VATAmount);
        $input['VATAmountLocal'] = \Helper::roundValue($totalAmount->VATAmountLocal);
        $input['VATAmountRpt'] = \Helper::roundValue($totalAmount->VATAmountRpt);


        $input['netAmount'] = \Helper::roundValue($totalAmount->netAmount);
        $input['netAmountLocal'] = \Helper::roundValue($totalAmount->netAmountLocal);
        $input['netAmountRpt'] = \Helper::roundValue($totalAmount->netAmountRpt);

        $input['customerCurrencyER'] = 1;

        $_post['creditNoteDate'] = Carbon::parse($input['creditNoteDate'])->format('Y-m-d') . ' 00:00:00';
        $curentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
        if ($_post['creditNoteDate'] > $curentDate) {
            return $this->sendError(trans('custom.document_date_cannot_be_greater_than_current_date'), 500);
        }

        if ($creditNote->confirmedYN == 0 && $input['confirmedYN'] == 1) {
            $messages = [
                'customerCurrencyID.required' => trans('custom.currency_is_required'),
                'customerID.required' => trans('custom.customer_is_required'),
                'companyFinanceYearID.required' => trans('custom.financial_year_is_required'),
                'companyFinancePeriodID.required' => trans('custom.financial_period_is_required'),

            ];
            $validator = \Validator::make($input, [
                'customerCurrencyID' => 'required|numeric|min:1',
                'customerID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'companyFinancePeriodID' => 'required|numeric|min:1',

            ], $messages);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $documentDate = $input['creditNoteDate'];
            $monthBegin = $input['FYPeriodDateFrom'];
            $monthEnd = $input['FYPeriodDateTo'];
            if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
            } else {
                return $this->sendError(trans('custom.document_date_not_within_selected_financial_period'), 500);
            }

            if (count($detail) == 0) {
                return $this->sendError(trans('custom.you_cannot_confirm_credit_note_should_have_at_leas'), 500);
            }

            $detailValidation = CreditNoteDetails::selectRaw("IF ( serviceLineCode IS NULL OR serviceLineCode = '', null, 1 ) AS serviceLineCode,IF ( serviceLineSystemID IS NULL OR serviceLineSystemID = '' OR serviceLineSystemID = 0, null, 1 ) AS serviceLineSystemID, IF ( contractUID IS NULL OR contractUID = '' OR contractUID = 0, null, 1 ) AS contractUID,
                    IF ( creditAmount IS NULL OR creditAmount = '' OR creditAmount = 0, null, 1 ) AS creditAmount")->
            where('creditNoteAutoID', $id)
                ->where(function ($query) {

                    $query->whereRaw('serviceLineSystemID IS NULL OR serviceLineSystemID =""')
                        ->orwhereRaw('serviceLineCode IS NULL OR serviceLineCode =""')
                        ->orwhereRaw('contractUID IS NULL OR contractUID =""')
                        ->orwhereRaw('creditAmount IS NULL OR creditAmount =""');
                });

            $isOperationIntergrated = ModuleAssigned::where('moduleID', 3)->where('companySystemID', $creditNote->companySystemID)->exists();

            if (!empty($detailValidation->get()->toArray())) {
                foreach ($detailValidation->get()->toArray() as $item) {

                    $validations = [
                        'serviceLineSystemID' => 'required|numeric|min:1',
                        'serviceLineCode' => 'required|min:1',
                        'creditAmount' => 'required|numeric|min:1'
                    ];

                    if ($isOperationIntergrated) {
                        $validations['contractUID'] = 'required|numeric|min:1';
                    }

                    $validators = \Validator::make($item, $validations, [

                        'serviceLineSystemID.required' => trans('custom.department_is_required'),
                        'serviceLineCode.required' => trans('custom.segment_code_not_updated'),
                        'contractUID.required' => trans('custom.contract_no_is_required'),
                        'creditAmount.required' => trans('custom.amount_should_be_greater_than_zero'),

                    ]);
                    if ($validators->fails()) {
                        return $this->sendError($validators->messages(), 422);
                    }
                }
            }

            /*serviceline and contract validation*/
            $groupby = CreditNoteDetails::select('serviceLineSystemID')->where('creditNoteAutoID', $id)->groupBy('serviceLineSystemID')->get();
            $groupbycontract = CreditNoteDetails::select('contractUID')->where('creditNoteAutoID', $id)->groupBy('contractUID')->get();
            if(count($groupby) == 0) {
                return $this->sendError(trans('custom.credit_note_details_not_found_1'), 500);
            }

            Taxdetail::where('documentSystemCode', $id)
                ->where('documentSystemID', $input["documentSystemiD"])
                ->delete();

            // if VAT Applicable
            if(isset($input['isVATApplicable']) && $input['isVATApplicable'] && isset($input['VATAmount']) && $input['VATAmount'] > 0){

                if(empty(TaxService::getOutputVATGLAccount($input["companySystemID"]))) {
                    return $this->sendError(trans('custom.cannot_confirm_output_vat_gl_account_not_configure'), 500);
                }

                $outputChartOfAc = TaxService::getOutputVATGLAccount($input["companySystemID"]);

                $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($outputChartOfAc->outputVatGLAccountAutoID, $input["companySystemID"]);

                if (!$checkAssignedStatus) {
                    return $this->sendError(trans('custom.cannot_confirm_output_vat_gl_account_not_assigned_'), 500);
                }

                $taxDetail['companyID'] = $input['companyID'];
                $taxDetail['companySystemID'] = $input['companySystemID'];
                $taxDetail['documentID'] = $input['documentID'];
                $taxDetail['documentSystemID'] = $input['documentSystemiD'];
                $taxDetail['documentSystemCode'] = $id;
                $taxDetail['documentCode'] = $creditNote->creditNoteCode;
                $taxDetail['taxShortCode'] = '';
                $taxDetail['taxDescription'] = '';
                $taxDetail['taxPercent'] = $input['VATPercentage'];
                $taxDetail['payeeSystemCode'] = $input['customerID'];

                if(!empty($customer)) {
                    $taxDetail['payeeCode'] = $customer->CutomerCode;
                    $taxDetail['payeeName'] = $customer->CustomerName;
                }

                $taxDetail['amount'] = $input['VATAmount'];
                $taxDetail['localCurrencyER']  = $input['localCurrencyER'];
                $taxDetail['rptCurrencyER'] = $input['companyReportingER'];
                $taxDetail['localAmount'] = $input['VATAmountLocal'];
                $taxDetail['rptAmount'] = $input['VATAmountRpt'];
                $taxDetail['currency'] =  $input['customerCurrencyID'];
                $taxDetail['currencyER'] =  1;

                $taxDetail['localCurrencyID'] =  $creditNote->localCurrencyID;
                $taxDetail['rptCurrencyID'] =  $creditNote->companyReportingCurrencyID;
                $taxDetail['payeeDefaultCurrencyID'] =  $input['customerCurrencyID'];
                $taxDetail['payeeDefaultCurrencyER'] =  1;
                $taxDetail['payeeDefaultAmount'] =  $input['VATAmount'];

                Taxdetail::create($taxDetail);
            }

            $input['RollLevForApp_curr'] = 1;

            unset($input['confirmedYN']);
            unset($input['confirmedByEmpSystemID']);
            unset($input['confirmedByEmpID']);
            unset($input['confirmedByName']);
            unset($input['confirmedDate']);

            $params = array(
                'autoID' => $id,
                'company' => $input["companySystemID"],
                'document' => $input["documentSystemiD"],
                'segment' => 0,
                'category' => 0,
                'amount' => $input['creditAmountTrans']
            );
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"]);
            }

        }

        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');

        $creditNote = $this->creditNoteRepository->update($input, $id);

        return $this->sendReponseWithDetails($creditNote->toArray(), trans('custom.credit_note_updated_successfully'),1,$confirm['data'] ?? null);
    }

    public function updateCurrency($id, UpdateCreditNoteAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('companyFinancePeriodID', 'confirmedYN', 'companyFinanceYearID', 'customerID', 'secondaryLogoCompanySystemID', 'customerCurrencyID', 'projectID'));

        $input = array_except($input, array('finance_period_by', 'finance_year_by', 'currency', 'createdDateAndTime',
            'confirmedByEmpSystemID', 'confirmedByEmpID', 'confirmedByName', 'confirmedDate','customer'));

        if (!\Helper::validateCurrencyRate($input['companySystemID'], $input['customerCurrencyID'])) {
            return $this->sendError(
                trans('custom.currency_exchange_rate_must_be_greater_than_zero'),
                500
            );
        }

        /** @var CreditNote $creditNote */
        $creditNote = $this->creditNoteRepository->findWithoutFail($id);
        if (empty($creditNote)) {
            return $this->sendError(trans('custom.credit_note_not_found'), 500);
        }

        if (isset($input['debitNoteAutoID'])) {
            $alreadyUsed = CreditNote::where('debitNoteAutoID', $input['debitNoteAutoID'])
                ->where('creditNoteAutoID', '<>', $id)
                ->first();

            if ($alreadyUsed) {
                return $this->sendError(trans('custom.debit_note_already_used', ['creditNoteCode' => $alreadyUsed->creditNoteCode]), 500);
            }
        }

        $detail = CreditNoteDetails::where('creditNoteAutoID', $id)->get();

        $input['departmentSystemID'] = 4;

        /*financial Year check*/
        $companyFinanceYearCheck = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYearCheck["success"]) {
            return $this->sendError($companyFinanceYearCheck["message"], 500);
        }
        /*financial Period check*/
        $companyFinancePeriodCheck = \Helper::companyFinancePeriodCheck($input);
        if (!$companyFinancePeriodCheck["success"]) {
            return $this->sendError($companyFinancePeriodCheck["message"], 500);
        }

        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $input['FYPeriodDateFrom'] = $companyfinanceperiod->dateFrom;
        $input['FYPeriodDateTo'] = $companyfinanceperiod->dateTo;


        if(isset($input['customerCurrencyID']) && isset($input['companySystemID'])){
            $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['customerCurrencyID'], $input['customerCurrencyID'], 0);

                if ($companyCurrencyConversion) {
                    $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

                }
        }
        if ($input['secondaryLogoCompanySystemID'] != $creditNote->secondaryLogoCompanySystemID) {
            if ($input['secondaryLogoCompanySystemID'] != '') {
                $company = Company::where('companySystemID', $input['secondaryLogoCompanySystemID'])->first();
                $input['secondaryLogoCompID'] = $company->CompanyID;
                $input['secondaryLogo'] = $company->logo_url;
            } else {
                $input['secondaryLogoCompID'] = NULL;
                $input['secondaryLogo'] = NULL;
            }

        }

        $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
        if ($customer) {
            $input['customerGLCode'] = $customer->custGLaccount;
            $input['customerGLCodeSystemID'] = $customer->custGLAccountSystemID;
        }

        // updating header amounts
        $totalAmount = CreditNoteDetails::selectRaw("COALESCE(SUM(creditAmount),0) as creditAmountTrans, 
                                                    COALESCE(SUM(localAmount),0) as creditAmountLocal, 
                                                    COALESCE(SUM(comRptAmount),0) as creditAmountRpt,
                                                    COALESCE(SUM(VATAmount),0) as VATAmount,
                                                    COALESCE(SUM(VATAmountLocal),0) as VATAmountLocal, 
                                                    COALESCE(SUM(VATAmountRpt),0) as VATAmountRpt,
                                                    COALESCE(SUM(netAmount),0) as netAmount,
                                                    COALESCE(SUM(netAmountLocal),0) as netAmountLocal, 
                                                    COALESCE(SUM(netAmountRpt),0) as netAmountRpt
                                                    ")
            ->where('creditNoteAutoID', $id)
            ->first();

        $input['creditAmountTrans'] = \Helper::roundValue($totalAmount->creditAmountTrans);
        $input['creditAmountLocal'] = \Helper::roundValue($totalAmount->creditAmountLocal);
        $input['creditAmountRpt'] = \Helper::roundValue($totalAmount->creditAmountRpt);


        $input['VATAmount'] = \Helper::roundValue($totalAmount->VATAmount);
        $input['VATAmountLocal'] = \Helper::roundValue($totalAmount->VATAmountLocal);
        $input['VATAmountRpt'] = \Helper::roundValue($totalAmount->VATAmountRpt);


        $input['netAmount'] = \Helper::roundValue($totalAmount->netAmount);
        $input['netAmountLocal'] = \Helper::roundValue($totalAmount->netAmountLocal);
        $input['netAmountRpt'] = \Helper::roundValue($totalAmount->netAmountRpt);

        $input['customerCurrencyER'] = 1;

        $_post['creditNoteDate'] = Carbon::parse($input['creditNoteDate'])->format('Y-m-d') . ' 00:00:00';
        $curentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
        if ($_post['creditNoteDate'] > $curentDate) {
            return $this->sendError(trans('custom.document_date_cannot_be_greater_than_current_date'), 500);
        }

        if ($creditNote->confirmedYN == 0 && $input['confirmedYN'] == 1) {
            $messages = [
                'customerCurrencyID.required' => trans('custom.currency_is_required'),
                'customerID.required' => trans('custom.customer_is_required'),
                'companyFinanceYearID.required' => trans('custom.financial_year_is_required'),
                'companyFinancePeriodID.required' => trans('custom.financial_period_is_required'),

            ];
            $validator = \Validator::make($input, [
                'customerCurrencyID' => 'required|numeric|min:1',
                'customerID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'companyFinancePeriodID' => 'required|numeric|min:1',

            ], $messages);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $documentDate = $input['creditNoteDate'];
            $monthBegin = $input['FYPeriodDateFrom'];
            $monthEnd = $input['FYPeriodDateTo'];
            if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
            } else {
                return $this->sendError(trans('custom.document_date_not_within_selected_financial_period'), 500);
            }

            if (count($detail) == 0) {
                return $this->sendError(trans('custom.you_cannot_confirm_credit_note_should_have_at_leas'), 500);
            }

            $detailValidation = CreditNoteDetails::selectRaw("IF ( serviceLineCode IS NULL OR serviceLineCode = '', null, 1 ) AS serviceLineCode,IF ( serviceLineSystemID IS NULL OR serviceLineSystemID = '' OR serviceLineSystemID = 0, null, 1 ) AS serviceLineSystemID, IF ( contractUID IS NULL OR contractUID = '' OR contractUID = 0, null, 1 ) AS contractUID,
                    IF ( creditAmount IS NULL OR creditAmount = '' OR creditAmount = 0, null, 1 ) AS creditAmount")->
            where('creditNoteAutoID', $id)
                ->where(function ($query) {

                    $query->whereRaw('serviceLineSystemID IS NULL OR serviceLineSystemID =""')
                        ->orwhereRaw('serviceLineCode IS NULL OR serviceLineCode =""')
                        ->orwhereRaw('contractUID IS NULL OR contractUID =""')
                        ->orwhereRaw('creditAmount IS NULL OR creditAmount =""');
                });

            $isOperationIntergrated = ModuleAssigned::where('moduleID', 3)->where('companySystemID', $creditNote->companySystemID)->exists();

            if (!empty($detailValidation->get()->toArray())) {
                foreach ($detailValidation->get()->toArray() as $item) {

                    $validations = [
                        'serviceLineSystemID' => 'required|numeric|min:1',
                        'serviceLineCode' => 'required|min:1',
                        'creditAmount' => 'required|numeric|min:1'
                    ];

                    if ($isOperationIntergrated) {
                        $validations['contractUID'] = 'required|numeric|min:1';
                    }

                    $validators = \Validator::make($item, $validations, [

                        'serviceLineSystemID.required' => trans('custom.department_is_required'),
                        'serviceLineCode.required' => trans('custom.segment_code_not_updated'),
                        'contractUID.required' => trans('custom.contract_no_is_required'),
                        'creditAmount.required' => trans('custom.amount_should_be_greater_than_zero'),

                    ]);
                    if ($validators->fails()) {
                        return $this->sendError($validators->messages(), 422);
                    }
                }
            }

            /*serviceline and contract validation*/
            $groupby = CreditNoteDetails::select('serviceLineSystemID')->where('creditNoteAutoID', $id)->groupBy('serviceLineSystemID')->get();
            $groupbycontract = CreditNoteDetails::select('contractUID')->where('creditNoteAutoID', $id)->groupBy('contractUID')->get();
            if (count($groupby) != 0) {
                if (count($groupby) > 1 || count($groupbycontract) > 1) {
                    if ($isOperationIntergrated) {
                        return $this->sendError(trans('custom.you_cannot_continue_multiple_segment_or_contract_e'), 500);
                    } else {
                        return $this->sendError(trans('custom.you_cannot_continue_multiple_segment_exist_in_deta'), 500);
                    }
                }
            } else {
                return $this->sendError(trans('custom.credit_note_details_not_found_1'), 500);
            }

            Taxdetail::where('documentSystemCode', $id)
                ->where('documentSystemID', $input["documentSystemiD"])
                ->delete();

            // if VAT Applicable
            if(isset($input['isVATApplicable']) && $input['isVATApplicable'] && isset($input['VATAmount']) && $input['VATAmount'] > 0){

                if(empty(TaxService::getOutputVATGLAccount($input["companySystemID"]))) {
                    return $this->sendError(trans('custom.cannot_confirm_output_vat_gl_account_not_configure'), 500);
                }

                $outputChartOfAc = TaxService::getOutputVATGLAccount($input["companySystemID"]);

                $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($outputChartOfAc->outputVatGLAccountAutoID, $input["companySystemID"]);

                if (!$checkAssignedStatus) {
                    return $this->sendError(trans('custom.cannot_confirm_output_vat_gl_account_not_assigned_'), 500);
                }

                $taxDetail['companyID'] = $input['companyID'];
                $taxDetail['companySystemID'] = $input['companySystemID'];
                $taxDetail['documentID'] = $input['documentID'];
                $taxDetail['documentSystemID'] = $input['documentSystemiD'];
                $taxDetail['documentSystemCode'] = $id;
                $taxDetail['documentCode'] = $creditNote->creditNoteCode;
                $taxDetail['taxShortCode'] = '';
                $taxDetail['taxDescription'] = '';
                $taxDetail['taxPercent'] = $input['VATPercentage'];
                $taxDetail['payeeSystemCode'] = $input['customerID'];

                if(!empty($customer)) {
                    $taxDetail['payeeCode'] = $customer->CutomerCode;
                    $taxDetail['payeeName'] = $customer->CustomerName;
                }

                $taxDetail['amount'] = $input['VATAmount'];
                $taxDetail['localCurrencyER']  = $input['localCurrencyER'];
                $taxDetail['rptCurrencyER'] = $input['companyReportingER'];
                $taxDetail['localAmount'] = $input['VATAmountLocal'];
                $taxDetail['rptAmount'] = $input['VATAmountRpt'];
                $taxDetail['currency'] =  $input['customerCurrencyID'];
                $taxDetail['currencyER'] =  1;

                $taxDetail['localCurrencyID'] =  $creditNote->localCurrencyID;
                $taxDetail['rptCurrencyID'] =  $creditNote->companyReportingCurrencyID;
                $taxDetail['payeeDefaultCurrencyID'] =  $input['customerCurrencyID'];
                $taxDetail['payeeDefaultCurrencyER'] =  1;
                $taxDetail['payeeDefaultAmount'] =  $input['VATAmount'];

                Taxdetail::create($taxDetail);
            }

            $input['RollLevForApp_curr'] = 1;

            unset($input['confirmedYN']);
            unset($input['confirmedByEmpSystemID']);
            unset($input['confirmedByEmpID']);
            unset($input['confirmedByName']);
            unset($input['confirmedDate']);

            $params = array(
                'autoID' => $id,
                'company' => $input["companySystemID"],
                'document' => $input["documentSystemiD"],
                'segment' => 0,
                'category' => 0,
                'amount' => $input['creditAmountTrans']
            );
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"]);
            }

        }

        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');

        $creditNote = $this->creditNoteRepository->update($input, $id);

        return $this->sendReponseWithDetails($creditNote->toArray(), trans('custom.credit_note_updated_successfully'),1,$confirm['data'] ?? null);
    }
    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/creditNotes/{id}",
     *      summary="Remove the specified CreditNote from storage",
     *      tags={"CreditNote"},
     *      description="Delete CreditNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNote",
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
        /** @var CreditNote $creditNote */
        $creditNote = $this->creditNoteRepository->findWithoutFail($id);

        if (empty($creditNote)) {
            return $this->sendError(trans('custom.credit_note_not_found'));
        }

        $creditNote->delete();

        return $this->sendResponse($id, trans('custom.credit_note_deleted_successfully'));
    }

    public function creditNoteLocalUpdate($id,Request $request){

        $value = $request->data;
        $companyId = $request->companyId;
        $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();

        if (isset($policy->isYesNO) && $policy->isYesNO == 1) {

        $details = CreditNoteDetails::where('creditNoteAutoID',$id)->get();

        $masterINVID = CreditNote::findOrFail($id);
            $VATAmountLocal = \Helper::roundValue($masterINVID->VATAmount/$value);
            $netAmountLocal = \Helper::roundValue($masterINVID->netAmount/$value);
            $creditAmountLocal = \Helper::roundValue($masterINVID->creditAmountTrans/$value);

            $masterInvoiceArray = array('localCurrencyER'=>$value, 'VATAmountLocal'=>$VATAmountLocal, 'netAmountLocal'=>$netAmountLocal,  'creditAmountLocal' =>$creditAmountLocal);
        $masterINVID->update($masterInvoiceArray);

        foreach($details as $item){
            $localAmount = \Helper::roundValue($item->creditAmount / $value);
            $itemVATAmountLocal= \Helper::roundValue($item->VATAmount / $value);
            $itemNetAmountLocal= \Helper::roundValue($item->netAmount / $value);
            $directInvoiceDetailsArray = array('localCurrencyER'=>$value, 'localAmount'=>$localAmount,'VATAmountLocal'=>$itemVATAmountLocal, 'netAmountLocal'=>$itemNetAmountLocal);
            $updatedLocalER = CreditNoteDetails::findOrFail($item->creditNoteDetailsID);
            $updatedLocalER->update($directInvoiceDetailsArray);
        }

        return $this->sendResponse([$id,$value], trans('custom.update_local_er'));

        }
        else{
            return $this->sendError(trans('custom.policy_not_enabled'), 400);
        }
    }

    public function creditNoteReportingUpdate($id,Request $request){

        $value = $request->data;
        $companyId = $request->companyId;

        $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();
        if (isset($policy->isYesNO) && $policy->isYesNO == 1) {

        $details = CreditNoteDetails::where('creditNoteAutoID',$id)->get();

        $masterINVID = CreditNote::findOrFail($id);
        $VATAmountRpt = \Helper::roundValue($masterINVID->VATAmount/$value);
        $netAmountRpt = \Helper::roundValue($masterINVID->netAmount/$value);
        $creditAmountRpt = \Helper::roundValue($masterINVID->creditAmountTrans/$value);

            $masterInvoiceArray = array('companyReportingER'=>$value, 'VATAmountRpt'=>$VATAmountRpt,'netAmountRpt'=>$netAmountRpt, 'creditAmountRpt'=>$creditAmountRpt);
        $masterINVID->update($masterInvoiceArray);

        foreach($details as $item){
            $reportingAmount = \Helper::roundValue($item->creditAmount / $value);
            $itemVATAmountRpt = \Helper::roundValue($item->VATAmount / $value);
            $itemNetAmountRpt = \Helper::roundValue($item->netAmount / $value);
            $directInvoiceDetailsArray = array('comRptCurrencyER'=>$value, 'comRptAmount'=>$reportingAmount,'VATAmountRpt'=>$itemVATAmountRpt, 'netAmountRpt'=>$itemNetAmountRpt);
            $updatedLocalER = CreditNoteDetails::findOrFail($item->creditNoteDetailsID);
            $updatedLocalER->update($directInvoiceDetailsArray);
        }

        return $this->sendResponse($id, trans('custom.update_reporting_er'));
        }

        else{
            return $this->sendError(trans('custom.policy_not_enabled'), 400);
        }

    }

    public function getCreditNoteMasterRecord(Request $request)
    {
        $input = $request->all();

        $output = CreditNote::where('creditNoteAutoID', $input['creditNoteAutoID'])->with(['details' => function ($query) {
            $query->with('segment');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 19);
        }, 'company', 'currency', 'customer', 'confirmed_by', 'createduser'])->first();
        return $this->sendResponse($output, trans('custom.data_retrieved_successfully'));

    }

    public function getCreditNoteViewFormData(Request $request)
    {
        $input = $request->all();
        /*companySystemID*/
        $companySystemID = $input['companyId'];
        $type = $input['type']; /*value ['filter','create','getCurrency']*/
        switch ($type) {
            case 'filter':
                $output['yesNoSelectionForMinus'] = YesNoSelectionForMinus::all();
                $output['customer'] = CustomerAssigned::select('*')
                    ->whereHas('customer_master',function($q){
                        $q->where('isCustomerActive',1);
                    })   
                    ->where('companySystemID', $companySystemID)
                    ->where('isAssigned', '-1')
                    ->where('isActive', '1')
                    ->get();
                $output['yesNoSelection'] = YesNoSelection::all();
                $output['month'] = Months::all();
                $output['years'] = CreditNote::select(DB::raw("YEAR(creditNoteDate) as year"))
                    ->whereNotNull('creditNoteDate')
                    ->where('companySystemID', $companySystemID)
                    ->groupby('year')
                    ->orderby('year', 'desc')
                    ->get();
                
                $output['projects'] = ErpProjectMaster::where('companySystemID', $companySystemID)
                    ->get();
                break;
            case 'create':

                $output['customer'] = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName"))
                    ->whereHas('customer_master',function($q){
                        $q->where('isCustomerActive',1);
                    })    
                    ->where('companySystemID', $companySystemID)
                    ->where('isActive', 1)
                    ->where('isAssigned', -1)
                    ->get();

                $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                    array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));
                $output['companyFinanceYear'] = \Helper::companyFinanceYear($companySystemID, 1);
                $output['company'] = Company::select('CompanyName', 'CompanyID','vatRegisteredYN')->where('companySystemID', $companySystemID)->first();

                $output['isProjectBase'] = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
                ->where('companySystemID', $companySystemID)
                ->where('isYesNO', 1)
                ->exists();
    
                $output['projects'] = ErpProjectMaster::where('companySystemID', $companySystemID)
                                                ->get();

                break;
            case 'getCurrency':
                $customerID = $input['customerID'];
                $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
                break;
            case 'getCreateData':
                $customerID = $input['customerID'];
                $isVATEligible = TaxService::checkCompanyVATEligible($companySystemID);
                $output['percentage'] = 0;

                if ($isVATEligible) {
                    $defaultVAT = TaxService::getDefaultVAT($companySystemID, $customerID, 0);
                    $vatPercentage = $defaultVAT['percentage'];
                    $output['percentage'] = $vatPercentage;
                }
                $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
                break;
            case 'edit' :
                $id = $input['id'];
                $master = CreditNote::where('creditNoteAutoID', $id)->first();
                $output['company'] = Company::select('CompanyName', 'CompanyID','vatRegisteredYN')->where('companySystemID', $companySystemID)->first();

                if ($master->customerID != '') {
                    $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $master->customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
                } else {
                    $output['currencies'] = [];
                }
                $output['customer'] = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName"))
                    ->whereHas('customer_master',function($q){
                        $q->where('isCustomerActive',1);
                    })       
                    ->where('companySystemID', $companySystemID)
                    ->where('isActive', 1)
                    ->where('isAssigned', -1)
                    ->get();

                $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                    array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

                $output['companyFinanceYear'] = \Helper::companyFinanceYear($companySystemID, 1);
                $output['companyLogo'] = Company::select('companySystemID', 'CompanyID', 'CompanyName', 'companyLogo')->get();
                $output['yesNoSelection'] = YesNoSelection::all();
                $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companySystemID)->approved()->withAssigned($companySystemID)->get();
                
                $output['isProjectBase'] = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
                ->where('companySystemID', $companySystemID)
                ->where('isYesNO', 1)
                ->exists();
    
                $output['projects'] = ErpProjectMaster::where('companySystemID', $companySystemID)
                                                ->get();
                
                break;
            case 'editAmend' :
                $id = $input['id'];
                $master = CreditNoteReferredback::where('creditNoteRefferedBackAutoID', $id)->first();
                $output['company'] = Company::select('CompanyName', 'CompanyID')->where('companySystemID', $companySystemID)->first();

                if ($master->customerID != '') {
                    $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $master->customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
                } else {
                    $output['currencies'] = [];
                }
                $output['customer'] = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName,vatEligible,vatPercentage"))
                    ->whereHas('customer_master',function($q){
                        $q->where('isCustomerActive',1);
                    })       
                    ->where('companySystemID', $companySystemID)
                    ->where('isActive', 1)
                    ->where('isAssigned', -1)
                    ->get();
                $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                    array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

                $output['companyFinanceYear'] = \Helper::companyFinanceYear($companySystemID, 1);
                $output['companyLogo'] = Company::select('companySystemID', 'CompanyID', 'CompanyName', 'companyLogo')->get();
                $output['yesNoSelection'] = YesNoSelection::all();
                $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companySystemID)->approved()->withAssigned($companySystemID)->get();
        }

        $output['isOperationIntergrated'] = ModuleAssigned::where('moduleID', 3)->where('companySystemID', $companySystemID)->exists();
        return $this->sendResponse($output, 'Form data');
    }

    public function creditNoteMasterDataTable(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approved', 'year', 'customerID', 'projectID'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $customerID = $request['customerID'];
        $customerID = (array)$customerID;
        $customerID = collect($customerID)->pluck('id');

        $projectID = $request['projectID'];
        $projectID = (array)$projectID;
        $projectID = collect($projectID)->pluck('id');

        $search = $request->input('search.value');

        $master = $this->creditNoteRepository->creditNoteListQuery($request, $input, $search, $customerID, $projectID);

        return \DataTables::of($master)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('creditNoteAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function creditNoteReopen(request $request)
    {
        $input = $request->all();
        $creditNoteAutoID = $input['creditNoteAutoID'];

        $creditnote = CreditNote::find($creditNoteAutoID);
        $emails = array();
        if (empty($creditnote)) {
            return $this->sendError(trans('custom.credit_note_not_found_1'));
        }

        if ($creditnote->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_credit_note_it_is_already_p'));
        }

        if ($creditnote->approved == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_credit_note_it_is_already_f'));
        }

        if ($creditnote->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_credit_note_it_is_not_confi'));
        }

        // updating fields
        $creditnote->confirmedYN = 0;
        $creditnote->confirmedByEmpSystemID = null;
        $creditnote->confirmedByEmpID = null;
        $creditnote->confirmedByName = null;
        $creditnote->confirmedDate = null;
        $creditnote->RollLevForApp_curr = 1;
        $creditnote->save();

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $creditnote->documentSystemiD)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $creditnote->creditNoteCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $creditnote->creditNoteCode;

        $subject = $cancelDocNameSubject . ' ' . trans('email.is_reopened');

        $body = '<p>' . $cancelDocNameBody . ' ' . trans('email.is_reopened_by', ['empID' => $employee->empID, 'empName' => $employee->empFullName]) . '</p><p>' . trans('email.comment') . ' : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $creditnote->companySystemID)
            ->where('documentSystemCode', $creditnote->custInvoiceDirectAutoID)
            ->where('documentSystemID', $creditnote->documentSystemiD)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $creditnote->companySystemID)
                    ->where('documentSystemID', $creditnote->documentSystemID)
                    ->first();

                /*if (empty($companyDocument)) {
                    return ['success' => false, 'message' => 'Policy not found for this document'];
                }*/

                $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                    ->where('companySystemID', $documentApproval->companySystemID)
                    ->where('documentSystemID', $documentApproval->documentSystemID);

                /*  if ($companyDocument['isServiceLineApproval'] == -1) {
                      $approvalList = $approvalList->where('ServiceLineSystemID', $documentApproval->serviceLineSystemID);
                  }*/

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

        DocumentApproved::where('documentSystemCode', $creditNoteAutoID)
            ->where('companySystemID', $creditnote->companySystemID)
            ->where('documentSystemID', $creditnote->documentSystemiD)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($creditnote->documentSystemiD,$creditNoteAutoID,$input['reopenComments'],'Reopened');

        return $this->sendResponse('s', trans('custom.credit_note_reopened_successfully'));

    }

    public function creditNoteAudit(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $creditNote = $this->creditNoteRepository->with(['createduser', 'confirmed_by', 'modified_by', 'approved_by' => function ($query) {
            $query->with('employee')
                ->where('documentSystemID', 19);
        }, 'company', 'currency', 'companydocumentattachment_by' => function ($query) {
            $query->where('documentSystemID', 19);
        }, 'audit_trial.modified_by'])->findWithoutFail($id);


        if (empty($creditNote)) {
            return $this->sendError(trans('custom.good_receipt_voucher_not_found_1'));
        }

        return $this->sendResponse($creditNote->toArray(), trans('custom.credit_note_retrieved_successfully'));
    }

    public function printCreditNote(Request $request)
    {
        $id = $request->get('id');
        $lang = $request->get('lang', 'en');
        $creditNote = $this->creditNoteRepository->getAudit($id);

        if (empty($creditNote)) {
            return $this->sendError(trans('custom.credit_note_not_found_2'));
        }


        $creditNote->docRefNo = \Helper::getCompanyDocRefNo($creditNote->companySystemID, $creditNote->documentSystemiD);

        $array = array('request' => $creditNote, 'lang' => $lang);
        $time = strtotime("now");
        $fileName = 'credit_note_' . $id . '_' . $time . '.pdf';
        $printTemplate = ErpDocumentTemplate::with('printTemplate')
                                            ->where('companyID', $creditNote->companySystemID)
                                            ->where('documentID', 19)
                                            ->first();

        // Check if Arabic language for RTL support
        $isRTL = ($lang === 'ar');
        $direction = $isRTL ? 'rtl' : 'ltr';

        if ($printTemplate && $printTemplate->printTemplateID == 9) {
            $html = view('print.credit_note_template.credit_note_gulf', $array);
            $htmlFooter = view('print.credit_note_template.credit_note_gulf_footer', $array);

            // Configure mPDF for RTL support if Arabic
            $mpdfConfig = [
                'tempDir' => public_path('tmp'),
                'mode' => 'utf-8',
                'format' => 'A4-P',
                'setAutoTopMargin' => 'stretch',
                'autoMarginPadding' => -10
            ];

            if ($isRTL) {
                $mpdfConfig['direction'] = 'rtl';
            }

            $mpdf = new \Mpdf\Mpdf($mpdfConfig);
            $mpdf->AddPage('P');
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->SetHTMLFooter($htmlFooter);

            $mpdf->WriteHTML($html);
            return $mpdf->Output($fileName, 'I');
        } else {
            $html = view('print.credit_note', $array);

            // Configure mPDF for RTL support if Arabic
            $mpdfConfig = [
                'tempDir' => public_path('tmp'),
                'mode' => 'utf-8',
                'format' => 'A4',
                'setAutoTopMargin' => 'stretch',
                'autoMarginPadding' => -10
            ];

            if ($isRTL) {
                $mpdfConfig['direction'] = 'rtl';
            }

            $mpdf = new \Mpdf\Mpdf($mpdfConfig);
            $mpdf->AddPage('P');
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->WriteHTML($html);
            return $mpdf->Output($fileName, 'I');
        }
    }

    public function getCreditNoteApprovedByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseFrom', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $creditNote = DB::table('erp_documentapproved')
            ->select(
                'erp_creditnote.*',
                'employees.empName As created_emp',
                'currencymaster.DecimalPlaces As DecimalPlaces',
                'currencymaster.CurrencyCode As CurrencyCode',
                'customermaster.CustomerName',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_creditnote', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'creditNoteAutoID')
                    ->where('erp_creditnote.companySystemID', $companyId)
                    ->where('erp_creditnote.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('customermaster', 'customerCodeSystem', 'erp_creditnote.customerID')
            ->leftJoin('currencymaster', 'currencyID', 'erp_creditnote.customerCurrencyID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [19])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $creditNote = $creditNote->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $creditNote = $creditNote->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $creditNote = $creditNote->whereMonth('creditNoteDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $creditNote = $creditNote->whereYear('creditNoteDate', '=', $input['year']);
            }
        }


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $creditNote = $creditNote->where(function ($query) use ($search) {
                $query->where('creditNoteCode', 'LIKE', "%{$search}%");
                $query->orwhere('comments', 'LIKE', "%{$search}%");
                $query->orwhere('CustomerName', 'LIKE', "%{$search}%");

            });
        }

        return \DataTables::of($creditNote)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('creditNoteAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getCreditNoteApprovalByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approved', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $creditNote = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'erp_creditnote.*',
                'employees.empName As created_emp',
                'currencymaster.DecimalPlaces As DecimalPlaces',
                'currencymaster.CurrencyCode As CurrencyCode',
                'erp_documentapproved.documentApprovedID',
                'customermaster.CustomerName',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyId)
                    ->where('documentSystemID', 19)
                    ->first();

                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    //$query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }

                $query->whereIn('employeesdepartments.documentSystemID', [19])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_creditnote', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'creditNoteAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_creditnote.companySystemID', $companyId)
                    ->where('erp_creditnote.approved', 0)
                    ->where('erp_creditnote.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('customermaster', 'customerCodeSystem', 'erp_creditnote.customerID')
            ->leftJoin('currencymaster', 'currencyID', 'erp_creditnote.customerCurrencyID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [19])
            ->where('erp_documentapproved.companySystemID', $companyId);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $creditNote = $creditNote->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $creditNote = $creditNote->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $creditNote = $creditNote->whereMonth('creditNoteDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $creditNote = $creditNote->whereYear('creditNoteDate', '=', $input['year']);
            }
        }


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $creditNote = $creditNote->where(function ($query) use ($search) {
                $query->where('creditNoteCode', 'LIKE', "%{$search}%");
                $query->orwhere('comments', 'LIKE', "%{$search}%");
                $query->orwhere('CustomerName', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $creditNote = [];
        }

        return \DataTables::of($creditNote)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('creditNoteAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function creditNoteReceiptStatus(Request $request)
    {
        $input = $request->all();
        $creditnote = CreditNote::find($input['id']);

        $data = DB::select("SELECT
	erp_matchdocumentmaster.PayMasterAutoId as masterID,
	erp_matchdocumentmaster.companyID,
	erp_matchdocumentmaster.matchingDocCode as docCode,
	erp_matchdocumentmaster.matchingDocdate as docDate,
	erp_matchdocumentmaster.supplierTransCurrencyID as currencyID,
	currencymaster.CurrencyCode,
	erp_matchdocumentmaster.matchedAmount as amount,
	erp_matchdocumentmaster.matchingConfirmedYN as confirmedYN,
	erp_matchdocumentmaster.approved,
	currencymaster.DecimalPlaces 
FROM
	erp_matchdocumentmaster
	INNER JOIN currencymaster ON erp_matchdocumentmaster.supplierTransCurrencyID = currencymaster.currencyID 
WHERE
	erp_matchdocumentmaster.PayMasterAutoId = $creditnote->creditNoteAutoID 
	AND erp_matchdocumentmaster.companyID = '$creditnote->companyID'
	AND erp_matchdocumentmaster.documentID = '$creditnote->documentID' 
	UNION ALL
SELECT
	erp_customerreceivepayment.custReceivePaymentAutoID as masterID,
	erp_customerreceivepayment.companyID,
IF
	( erp_custreceivepaymentdet.matchingDocID = 0 OR erp_custreceivepaymentdet.matchingDocID IS NULL, erp_customerreceivepayment.custPaymentReceiveCode, erp_matchdocumentmaster.matchingDocCode ) AS docCode,
IF
	( erp_custreceivepaymentdet.matchingDocID = 0 OR erp_custreceivepaymentdet.matchingDocID IS NULL, erp_customerreceivepayment.custPaymentReceiveDate, erp_matchdocumentmaster.matchingDocdate ) AS docDate,
	erp_custreceivepaymentdet.custTransactionCurrencyID as  currencyID,
	currencymaster.CurrencyCode,
	erp_custreceivepaymentdet.receiveAmountTrans as amount,
	erp_customerreceivepayment.confirmedYN,
	erp_customerreceivepayment.approved,
	currencymaster.DecimalPlaces 
FROM
	erp_custreceivepaymentdet
	INNER JOIN currencymaster ON erp_custreceivepaymentdet.custTransactionCurrencyID = currencymaster.currencyID
	LEFT JOIN erp_customerreceivepayment ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
	LEFT JOIN erp_matchdocumentmaster ON erp_custreceivepaymentdet.matchingDocID = erp_matchdocumentmaster.matchDocumentMasterAutoID 
WHERE
	erp_customerreceivepayment.companyID ='$creditnote->companyID'
	AND erp_custreceivepaymentdet.addedDocumentID = '$creditnote->documentID'
	AND erp_custreceivepaymentdet.bookingInvCodeSystem = $creditnote->creditNoteAutoID ");
        return $this->sendResponse($data, trans('custom.credit_note_retrieved_successfully'));
    }

    public function amendCreditNote(Request $request)
    {
        $input = $request->all();

        $creditNoteAutoID = $input['creditNoteAutoID'];

        $creditNoteMasterData = CreditNote::find($creditNoteAutoID);

        if (empty($creditNoteMasterData)) {
            return $this->sendError(trans('custom.credit_note_not_found'));
        }

        if ($creditNoteMasterData->refferedBackYN != -1) {
            return $this->sendError(trans('custom.you_cannot_refer_back_this_credit_note'));
        }

        $creditNoteArray = $creditNoteMasterData->toArray();

        $storeCreditNoteHistory = CreditNoteReferredback::insert($creditNoteArray);

        $creditNoteDetailRec = CreditNoteDetails::where('creditNoteAutoID', $creditNoteAutoID)->get();

        if (!empty($creditNoteDetailRec)) {
            foreach ($creditNoteDetailRec as $bookDetail) {
                $bookDetail['timesReferred'] = $creditNoteMasterData->timesReferred;
            }
        }

        $creditNoteDetailArray = $creditNoteDetailRec->toArray();

        $storeCreditNoteDetailHistory = CreditNoteDetailsRefferdback::insert($creditNoteDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $creditNoteAutoID)
            ->where('companySystemID', $creditNoteMasterData->companySystemID)
            ->where('documentSystemID', $creditNoteMasterData->documentSystemiD)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $creditNoteMasterData->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $creditNoteAutoID)
            ->where('companySystemID', $creditNoteMasterData->companySystemID)
            ->where('documentSystemID', $creditNoteMasterData->documentSystemiD)
            ->delete();

        if ($deleteApproval) {
            $creditNoteMasterData->refferedBackYN = 0;
            $creditNoteMasterData->confirmedYN = 0;
            $creditNoteMasterData->confirmedByEmpSystemID = null;
            $creditNoteMasterData->confirmedByEmpID = null;
            $creditNoteMasterData->confirmedByName = null;
            $creditNoteMasterData->confirmedDate = null;
            $creditNoteMasterData->RollLevForApp_curr = 1;
            $creditNoteMasterData->save();
        }

        return $this->sendResponse($creditNoteMasterData->toArray(), trans('custom.credit_note_amend_successfully'));
    }

    public function approvalPreCheckCreditNote(Request $request)
    {
        $approve = \Helper::postedDatePromptInFinalApproval($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"], 500, ['type' => $approve["type"]]);
        } else {
            return $this->sendResponse(array('type' => $approve["type"]), $approve["message"]);
        }
    }


    public function amendCreditNoteReview(Request $request)
    {
        $input = $request->all();

        $id = $input['creditNoteAutoID'];

        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $masterData = CreditNote::find($id);

        if (empty($masterData)) {
            return $this->sendError(trans('custom.credit_note_not_found'));
        }

        if ($masterData->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_return_back_to_amend_this_credit_note_i'));
        }

        // checking document matched in receive payment
        $checkDetailExistMatch = CustomerReceivePaymentDetail::where('bookingInvCodeSystem', $id)
            ->where('companySystemID', $masterData->companySystemID)
            ->where('addedDocumentSystemID', $masterData->documentSystemiD)
            ->first();

        if ($checkDetailExistMatch) {
            return $this->sendError(trans('custom.cannot_return_back_to_amend_credit_note_is_added_t_1'));
        }

        // checking document matched in erp_matchdocumentmaster
        $checkDetailExistMatch = MatchDocumentMaster::where('PayMasterAutoId', $id)
            ->where('companySystemID', $masterData->companySystemID)
            ->where('documentSystemID', $masterData->documentSystemiD)
            ->first();

        if ($checkDetailExistMatch) {
            return $this->sendError(trans('custom.cannot_return_back_to_amend_credit_note_is_added_t'));
        }

        if($masterData->approved == -1) {
            $isAPIDocument = DocumentSystemMapping::where('documentId',$id)->where('documentSystemID',$masterData->documentSystemiD)->exists();
            if ($isAPIDocument){
                return $this->sendError(trans('custom.the_autogenerated_documents_cannot_be_amended'));
            }
        }

        $documentAutoId = $id;
        $documentSystemID = $masterData->documentSystemiD;

        $checkBalance = GeneralLedgerService::validateDebitCredit($documentSystemID, $documentAutoId);
        if (!$checkBalance['status']) {
            $allowValidateDocumentAmend = false;
        } else {
            $allowValidateDocumentAmend = true;
        }

        if($masterData->approved == -1){
            if($allowValidateDocumentAmend){
                $validatePendingGlPost = ValidateDocumentAmend::validatePendingGlPost($documentAutoId, $documentSystemID);
                if(isset($validatePendingGlPost['status']) && $validatePendingGlPost['status'] == false){
                    if(isset($validatePendingGlPost['message']) && $validatePendingGlPost['message']){
                        return $this->sendError($validatePendingGlPost['message']);
                    }
                }
            }

            $validateVatReturnFilling = ValidateDocumentAmend::validateVatReturnFilling($documentAutoId,$documentSystemID,$masterData->companySystemID);
            if(isset($validateVatReturnFilling['status']) && $validateVatReturnFilling['status'] == false){
                $errorMessage = "Credit Note " . $validateVatReturnFilling['message'];
                return $this->sendError($errorMessage);
            }
        }


        $emailBody = '<p>' . $masterData->creditNoteCode . ' ' . trans('email.has_been_returned_back_to_amend_by', ['empName' => $employee->empName]) . ' ' . trans('email.due_to_below_reason') . '.</p><p>' . trans('email.comment') . ' : ' . $input['returnComment'] . '</p>';
        $emailSubject = $masterData->creditNoteCode . ' ' . trans('email.has_been_returned_back_to_amend');

        DB::beginTransaction();
        try {

            //sending email to relevant party
            if ($masterData->confirmedYN == 1) {
                $emails[] = array('empSystemID' => $masterData->confirmedByEmpSystemID,
                    'companySystemID' => $masterData->companySystemID,
                    'docSystemID' => $masterData->documentSystemiD,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody,
                    'docSystemCode' => $id,
                    'docCode' => $masterData->creditNoteCode
                );
            }

            $documentApproval = DocumentApproved::where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemCode', $id)
                ->where('documentSystemID', $masterData->documentSystemiD)
                ->get();

            foreach ($documentApproval as $da) {
                if ($da->approvedYN == -1) {
                    $emails[] = array('empSystemID' => $da->employeeSystemID,
                        'companySystemID' => $masterData->companySystemID,
                        'docSystemID' => $masterData->documentSystemiD,
                        'alertMessage' => $emailSubject,
                        'emailAlertMessage' => $emailBody,
                        'docSystemCode' => $id,
                        'docCode' => $masterData->creditNoteCode
                    );
                }
            }

            $sendEmail = \Email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            //deleting from approval table
            $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemiD)
                ->delete();

            //deleting from general ledger table
            $deleteGLData = GeneralLedger::where('documentSystemCode', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemiD)
                ->delete();

            //deleting records from accounts receivable
            $deleteARData = AccountsReceivableLedger::where('documentCodeSystem', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemiD)
                ->delete();

            //deleting records from tax ledger
            TaxLedger::where('documentMasterAutoID', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemiD)
                ->delete();

            $taxLedgerDetails = TaxLedgerDetail::where('documentMasterAutoID', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemiD)
                ->get();

            $returnFilledDetailID = null;
            foreach ($taxLedgerDetails as $taxLedgerDetail) {
                if($taxLedgerDetail->returnFilledDetailID != null){
                    $returnFilledDetailID = $taxLedgerDetail->returnFilledDetailID;
                }
                $taxLedgerDetail->delete();
            }

            if($returnFilledDetailID != null){
                $this->vatReturnFillingMasterRepo->updateVatReturnFillingDetails($returnFilledDetailID);
            }

            // updating fields
            $masterData->confirmedYN = 0;
            $masterData->confirmedByEmpSystemID = null;
            $masterData->confirmedByEmpID = null;
            $masterData->confirmedByName = null;
            $masterData->confirmedDate = null;
            $masterData->RollLevForApp_curr = 1;

            $masterData->approved = 0;
            $masterData->approvedByUserSystemID = null;
            $masterData->approvedByUserID = null;
            $masterData->approvedDate = null;
            $masterData->postedDate = null;
            $masterData->save();

            AuditTrial::createAuditTrial($masterData->documentSystemiD, $id, $input['returnComment'], 'returned back to amend');

            DB::commit();
            return $this->sendResponse($masterData->toArray(), trans('custom.credit_note_amend_saved_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getFilteredDebitNote(Request $request)
    {
        $input = $request->all();
        $seachText = $input['seachText'];
        $seachText = str_replace("\\", "\\\\", $seachText);
        $debitNote = DebitNote::select('debitNoteAutoID', 'debitNoteCode')
            ->where('approved', -1)
            ->where('refferedBackYN', 0)
            ->where('debitNoteCode', 'LIKE', "%{$seachText}%")
            ->whereHas('company', function ($query) {
                $query->where('masterCompanySystemIDReorting', '<>', 35);
            })
            ->orderBy('debitNoteAutoID', 'desc')
            ->take(30)
            ->get()->toArray();
        return $this->sendResponse($debitNote, trans('custom.data_retrieved_successfully'));
    }

    public function createCreditNoteAPI(Request $request){

        $input = $request->all();
        $db = isset($request->db) ? $request->db : "";
        $authorization = $request->header('Authorization');

        $creditNotes = $input['credit_notes'] ?? null;

        if(is_array($creditNotes)) {
            $compId = $input['company_id'];
            $company = Company::where('companySystemID', $compId)->first();
            if (!$company) {
                return $this->sendError(trans('custom.company_details_not_found'));
            }

            // Get tracking parameters from ThirdPartyApiLogger middleware
            $externalReference = $request->get('external_reference');
            $tenantUuid = $request->get('tenant_uuid') ?? env('TENANT_UUID', 'local');

            CreateCreditNote::dispatch($input, $db, $request->api_external_key, $request->api_external_url, $authorization, $externalReference, $tenantUuid);

            return $this->sendResponse(['externalReference' => $externalReference],trans('custom.credit_note_request_queued_successfully'));
        }
        else {
            return $this->sendError(trans('custom.invalid_data_format'));
        }

    }

}
