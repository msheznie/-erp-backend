<?php
/**
 * =============================================
 * -- File Name : DebitNoteAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  DebitNote
 * -- Author : Mohamed Nazir
 * -- Create date : 16 - August 2018
 * -- Description : This file contains the all CRUD for Debit Note
 * -- REVISION HISTORY
 * -- Date: 08-August 2018 By: Nazir Description: Added new function getDebitNoteMasterRecord()
 * -- Date: 04-September 2018 By: Fayas Description: Added new function getAllDebitNotes(),getDebitNoteFormData()
 * -- Date: 05-September 2018 By: Fayas Description: Added new function getDebitNoteApprovedByUser(),getDebitNoteApprovalByUser()
 *                ,debitNoteReopen(),printDebitNote()
 * -- Date: 08-October 2018 By: Nazir Description: Added new function getDebitNotePaymentStatusHistory()
 * -- Date: 30-November 2018 By: Nazir Description: Added new function amendDebitNote()
 * -- Date: 23-December 2018 By: Nazir Description: Added new function amendDebitNoteReview(),
 * -- Date: 08-January 2019 By: Nazir Description: Added new function checkPaymentStatusDNPrint(),
 * -- Date: 11-January 2019 By: Mubashir Description: Added new function approvalPreCheckDebitNote()
 * -- Date: 6-February 2019 By: Fayas Description: Added new function exportDebitNotesByCompany()
 */
namespace App\Http\Controllers\API;

use App\helper\TaxService;
use App\Http\Requests\API\CreateDebitNoteAPIRequest;
use App\Http\Requests\API\UpdateDebitNoteAPIRequest;
use App\Models\AccountsPayableLedger;
use App\Models\BudgetConsumedData;
use App\Models\ChartOfAccount;
use App\Models\EmployeeLedger;
use App\Models\Company;
use App\Models\ChartOfAccountsAssigned;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\DebitNote;
use App\Models\DebitNoteDetails;
use App\Models\DebitNoteDetailsRefferedback;
use App\Models\ErpDocumentTemplate;
use App\Models\DebitNoteMasterRefferedback;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\GeneralLedger;
use App\Models\MatchDocumentMaster;
use App\Models\Months;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\ProcumentOrder;
use App\Models\SegmentMaster;
use App\Models\ErpProjectMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Models\Taxdetail;
use App\Models\TaxLedger;
use App\Models\TaxLedgerDetail;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\SystemGlCodeScenario;
use App\Repositories\DebitNoteRepository;
use App\Repositories\VatReturnFillingMasterRepository;
use App\Services\ChartOfAccountValidationService;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\CreateExcel;
use App\Models\Employee;
use App\Models\CurrencyMaster;
use App\helper\Helper;
use App\Models\SupplierBlock;
use App\Services\GeneralLedgerService;
use App\Services\ValidateDocumentAmend;

/**
 * Class DebitNoteController
 * @package App\Http\Controllers\API
 */
class DebitNoteAPIController extends AppBaseController
{
    /** @var  DebitNoteRepository */
    private $debitNoteRepository;
    private $vatReturnFillingMasterRepo;

    public function __construct(DebitNoteRepository $debitNoteRepo,VatReturnFillingMasterRepository $vatReturnFillingMasterRepo)
    {
        $this->debitNoteRepository = $debitNoteRepo;
        $this->vatReturnFillingMasterRepo = $vatReturnFillingMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/debitNotes",
     *      summary="Get a listing of the DebitNotes.",
     *      tags={"DebitNote"},
     *      description="Get all DebitNotes",
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
     *                  @SWG\Items(ref="#/definitions/DebitNote")
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
        $this->debitNoteRepository->pushCriteria(new RequestCriteria($request));
        $this->debitNoteRepository->pushCriteria(new LimitOffsetCriteria($request));
        $debitNotes = $this->debitNoteRepository->all();

        return $this->sendResponse($debitNotes->toArray(), trans('custom.debit_notes_retrieved_successfully'));
    }

    /**
     * @param CreateDebitNoteAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/debitNotes",
     *      summary="Store a newly created DebitNote in storage",
     *      tags={"DebitNote"},
     *      description="Store DebitNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DebitNote that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DebitNote")
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
     *                  ref="#/definitions/DebitNote"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDebitNoteAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        if (!\Helper::validateCurrencyRate($input['companySystemID'], $input['supplierTransactionCurrencyID'])) {
            return $this->sendError(
                'Currency exchange rate to local and reporting currency must be greater than zero.',
                500
            );
        }

        $employee = \Helper::getEmployeeInfo();

        $type =  $input['type'];
        $company_id = $input['companySystemID'];


        if($type == 2)
        {
                $is_valid = true;
                               $slug = "employee-control-account";
                               $emp_control_acc = SystemGlCodeScenario::where('slug',$slug)->where('isActive',1)->with(['company_scenario'=>function($query) use($company_id){
                                $query->where('companySystemID',$company_id);
                               }])->first();

                               if(isset($emp_control_acc))
                               {
                                if(isset($emp_control_acc->company_scenario))
                                {

                                    if(isset($emp_control_acc->company_scenario->chartOfAccountSystemID))
                                    {
                                        $ChartOfAccountsAssigned = ChartOfAccountsAssigned::where('chartOfAccountSystemID',$emp_control_acc->company_scenario->chartOfAccountSystemID)
                                            ->where('companySystemID',$company_id)->where('isAssigned',-1)->first();
                                            if(!isset($ChartOfAccountsAssigned))
                                            {
                                                $is_valid = false;
                                            }

                                    }
                                    else
                                    {
                                        $is_valid = false;
                                    }
                              
                                }
                                else
                                {
                                    $is_valid = false;
                                }
                               }
                               else
                               {
                                $is_valid = false;
                               }
                              

                             

            if(!($is_valid))
            {
                return $this->sendError('Employee Control Account not Configured !', 500);
            }
            
        }

    
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
        } else {
            $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
            $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 1;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else {
            $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
        }
        unset($inputParam);

        if (isset($input['invoiceNumber']) && !empty($input['invoiceNumber'])) {
            if($type == 1)
            {
                $alreadyAdded = DebitNote::where('invoiceNumber', $input['invoiceNumber'])
                ->where('supplierID', $input['supplierID'])
                ->first();
            }
            else if($type == 2)
            {
                $alreadyAdded = DebitNote::where('invoiceNumber', $input['invoiceNumber'])
                ->where('empID', $input['empID'])
                ->first();
            }
      

            if ($alreadyAdded) {
                return $this->sendError("Entered invoice number was already used ($alreadyAdded->debitNoteCode). Please check again", 500);
            }
        }

        if (isset($input['lcPayment']) && $input['lcPayment'] == 1 && empty($input['lcDocCode'])) {
            return $this->sendError("LC Doc Code is required", 500);
        }
       
        $validator = \Validator::make($input, [
            'companyFinancePeriodID' => 'required|numeric|min:1',
            'companyFinanceYearID' => 'required|numeric|min:1',
            'debitNoteDate' => 'required',
            'supplierID' => ['required_if:type,1|numeric|min:1'],
            'empID' => ['required_if:type,2|numeric|min:1'],
            'supplierTransactionCurrencyID' => 'required|numeric|min:1',
            'comments' => 'required',
        ],
        [
            'empID.required_if' => 'Please select an employee',
            'supplierID.required_if' => 'Please select a supplier',
        ]);
      
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
 
        if (isset($input['debitNoteDate'])) {
            if ($input['debitNoteDate']) {
                $input['debitNoteDate'] = new Carbon($input['debitNoteDate']);
            }
        }
        $documentDate = $input['debitNoteDate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];
        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Document date is not within the selected financial period !', 500);
        }

        $input['documentSystemID'] = 15;
        $input['documentID'] = 'DN';

        $lastSerial = DebitNote::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.company_not_found'), 500);
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], 0);

        $input['supplierTransactionCurrencyER'] = 1;
        $input['companyID'] = $company->CompanyID;
        $input['companyReportingCurrencyID'] = $company->reportingCurrency;
        $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
        $input['localCurrencyID'] = $company->localCurrencyID;
        $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

        $input['serialNo'] = $lastSerialNumber;
        $input['RollLevForApp_curr'] = 1;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        $companyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();
        if ($companyFinanceYear) {
            $startYear = $companyFinanceYear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];
        } else {
            $finYear = date("Y");
        }

        // adding supplier grv details
        if($type == 1)
        {
            $supplierAssignedDetail = SupplierAssigned::select('liabilityAccountSysemID', 'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount')
            ->where('supplierCodeSytem', $input['supplierID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

            if ($supplierAssignedDetail) {
                $input["supplierGLCodeSystemID"] = $supplierAssignedDetail->liabilityAccountSysemID;
                $input["supplierGLCode"] = $supplierAssignedDetail->liabilityAccount;
                $input["liabilityAccountSysemID"] = $supplierAssignedDetail->liabilityAccountSysemID;
                $input["liabilityAccount"] = $supplierAssignedDetail->liabilityAccount;
                $input["UnbilledGRVAccountSystemID"] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
                $input["UnbilledGRVAccount"] = $supplierAssignedDetail->UnbilledGRVAccount;
            }

        }
        else if($type == 2)
        {

            
            $emp_control_acc = SystemGlCodeScenarioDetail::where('systemGlScenarioID',12)->where('companySystemID',$company_id)->first();
            if(isset($emp_control_acc))
            {
                $emp_chart_acc = $emp_control_acc->chartOfAccountSystemID;
                if(!empty($emp_chart_acc) && $emp_chart_acc != null)
                {
                    $input["empControlAccount"] = $emp_chart_acc;
                }
            }

          
        }

        if ($documentMaster) {
            $code = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['debitNoteCode'] = $code;
        }
        
        $debitNotes = $this->debitNoteRepository->create($input);

        return $this->sendResponse($debitNotes->toArray(), trans('custom.debit_note_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/debitNotes/{id}",
     *      summary="Display the specified DebitNote",
     *      tags={"DebitNote"},
     *      description="Get DebitNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNote",
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
     *                  ref="#/definitions/DebitNote"
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
        /** @var DebitNote $debitNote */
        $debitNote = $this->debitNoteRepository->with(['confirmed_by', 'created_by', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'supplier' => function($query){
            $query->selectRaw('CONCAT(primarySupplierCode," | ",supplierName) as supplierName,supplierCodeSystem');
        }, 'employee' => function($query){
            $query->selectRaw('CONCAT(empID," | ",empName) as employeeName,employeeSystemID');
        },'transactioncurrency'=> function($query){
            $query->selectRaw('CONCAT(CurrencyCode," | ",CurrencyName) as CurrencyName,DecimalPlaces,currencyID');
        },'vrfDocument' => function($query) {
            $query->where('masterDocumentTypeID',15);
        }])->findWithoutFail($id);

        if (empty($debitNote)) {
            return $this->sendError(trans('custom.debit_note_not_found'));
        }

        return $this->sendResponse($debitNote->toArray(), trans('custom.debit_note_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateDebitNoteAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/debitNotes/{id}",
     *      summary="Update the specified DebitNote in storage",
     *      tags={"DebitNote"},
     *      description="Update DebitNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNote",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DebitNote that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DebitNote")
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
     *                  ref="#/definitions/DebitNote"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDebitNoteAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'finance_period_by', 'finance_year_by', 'supplier', 'transactioncurrency',
            'confirmedByEmpID', 'confirmedDate', 'confirmed_by', 'confirmedByEmpSystemID','employee']);

        $input = $this->convertArrayToValue($input);

        if (!\Helper::validateCurrencyRate($input['companySystemID'], $input['supplierTransactionCurrencyID'])) {
            return $this->sendError(
                'Currency exchange rate to local and reporting currency must be greater than zero.',
                500
            );
        }
        
        /** @var DebitNote $debitNote */
        $debitNote = $this->debitNoteRepository->findWithoutFail($id);

        if(empty($input['projectID'])){
            $input['projectID'] = null;
        }
        
        $type =  $input['type'];
        $supplier_id = $input['supplierID'];
        $supplierMaster = SupplierMaster::where('supplierCodeSystem',$supplier_id)->first();
        
        if($type == 2)
        {   

            $company_id = $debitNote->companySystemID;
            $is_valid = true;

            $slug = "employee-control-account";
            $emp_control_acc = SystemGlCodeScenario::where('slug',$slug)->where('isActive',1)->with(['company_scenario'=>function($query) use($company_id){
             $query->where('companySystemID',$company_id);
            }])->first();

            if(isset($emp_control_acc))
            {
             if(isset($emp_control_acc->company_scenario))
             {

                 if(isset($emp_control_acc->company_scenario->chartOfAccountSystemID))
                 {
                     $ChartOfAccountsAssigned = ChartOfAccountsAssigned::where('chartOfAccountSystemID',$emp_control_acc->company_scenario->chartOfAccountSystemID)
                         ->where('companySystemID',$company_id)->where('isAssigned',-1)->first();
                         if(!isset($ChartOfAccountsAssigned))
                         {
                             $is_valid = false;
                         }

                 }
                 else
                 {
                     $is_valid = false;
                 }
                
            }
                    else
                    {
                        $is_valid = false;
                    }
            }
                    else
                    {
                    $is_valid = false;
                    }
           

          

                    if(!($is_valid))
                    {
                    return $this->sendError('Employee Control Account not Configured !', 500);
                    }


        }

        if (empty($debitNote)) {
            return $this->sendError(trans('custom.debit_note_not_found'));
        }

        if ($debitNote->confirmedYN == 1) {
            return $this->sendError(trans('custom.this_document_already_confirmed_you_cannot_edit'), 500);
        }

        if(isset($input['supplierTransactionCurrencyID']) && $input['supplierTransactionCurrencyID'] != $debitNote->supplierTransactionCurrencyID){

            $detailsCount = $debitNoteDetails = DebitNoteDetails::where('debitNoteAutoID', $id)->count();

            if($detailsCount > 0){
                return $this->sendError(trans('custom.you_cannot_change_the_currencyif_you_want_to_chang'), 500);
            }
        }


        if (isset($input['invoiceNumber']) && !empty($input['invoiceNumber'])) {

            if($type == 1)
            {
                $alreadyAdded = DebitNote::where('invoiceNumber', $input['invoiceNumber'])
                ->where('supplierID', $input['supplierID'])
                ->where('debitNoteAutoID', '<>', $id)
                ->first();
            }
            else if($type == 2)
            {
                $alreadyAdded = DebitNote::where('invoiceNumber', $input['invoiceNumber'])
                ->where('empID', $input['empID'])
                ->where('debitNoteAutoID', '<>', $id)
                ->first();
            }

       

            if ($alreadyAdded) {
                return $this->sendError("Entered invoice number was already used ($alreadyAdded->debitNoteCode). Please check again", 500);
            }
        }


        if (isset($input['lcPayment']) && $input['lcPayment'] == 1 && empty($input['lcDocCode'])) {
            return $this->sendError("LC Doc Code is required", 500);
        }

        // adding supplier grv details

        if($type == 1)
        {
            $supplierAssignedDetail = SupplierAssigned::select('liabilityAccountSysemID', 'liabilityAccount', 'UnbilledGRVAccountSystemID',
            'UnbilledGRVAccount','supplierName','primarySupplierCode')
            ->where('supplierCodeSytem', $input['supplierID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

            if ($supplierAssignedDetail) {
                $input["supplierGLCodeSystemID"] = $supplierAssignedDetail->liabilityAccountSysemID;
                $input["supplierGLCode"] = $supplierAssignedDetail->liabilityAccount;
                $input["liabilityAccountSysemID"] = $supplierAssignedDetail->liabilityAccountSysemID;
                $input["liabilityAccount"] = $supplierAssignedDetail->liabilityAccount;
                $input["UnbilledGRVAccountSystemID"] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
                $input["UnbilledGRVAccount"] = $supplierAssignedDetail->UnbilledGRVAccount;
                $input["empControlAccount"] = null;
                $input["empID"] = null;
            }
        }
        else if($type == 2)
        {

            
            $emp_control_acc = SystemGlCodeScenarioDetail::where('systemGlScenarioID',12)->where('companySystemID',$input['companySystemID'])->first();

         
            if(isset($emp_control_acc))
            {
                $emp_chart_acc = $emp_control_acc->chartOfAccountSystemID;
                if(!empty($emp_chart_acc) && $emp_chart_acc != null)
                {
                    $input["empControlAccount"] = $emp_chart_acc;
                    $input["supplierID"] = null;
                }
            }

          
        }
    

        if (isset($input['debitNoteDate'])) {
            if ($input['debitNoteDate']) {
                $input['debitNoteDate'] = new Carbon($input['debitNoteDate']);
            }
        }

        if (isset($input['supplierTransactionCurrencyID'])) {
            $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], 0);
            $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
                ->where('companyPolicyCategoryID', 67)
                ->where('isYesNO', 1)
                ->first();
            $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;

            if($policy == false) {
                $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
            }
        }

        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
        } else {
            $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
            $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 1;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else {
            $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
        }
        unset($inputParam);

        $documentDate = $input['debitNoteDate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];
        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            // return $this->sendError('Document date is not within the selected financial period !', 500);
        }

        if ($debitNote->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            if($type == 1 && ($input['isSupplierBlocked']))
            {
               
                $validatorResult = \Helper::checkBlockSuppliers($input['debitNoteDate'],$input['supplierID']);
                if (!$validatorResult['success']) {              
                     return $this->sendError('The selected supplier has been blocked. Are you sure you want to proceed ?', 500,['type' => 'blockSupplier']);
    
                }
            }

            $validator = \Validator::make($input, [
                'companyFinancePeriodID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'debitNoteDate' => 'required',
                'supplierID' => ['required_if:type,1|numeric|min:1'],
                'empID' => ['required_if:type,2|numeric|min:1'],
                'supplierTransactionCurrencyID' => 'required|numeric|min:1',
                'comments' => 'required',
            ],
            [
                'empID.required_if' => 'Please select an employee',
                'supplierID.required_if' => 'Please select a supplier',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $documentDate = $input['debitNoteDate'];
            $monthBegin = $input['FYPeriodDateFrom'];
            $monthEnd = $input['FYPeriodDateTo'];
            if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
            } else {
                return $this->sendError('Document date is not within the selected financial period !', 500);
            }

            $checkItems = DebitNoteDetails::where('debitNoteAutoID', $id)
                ->count();

            if ($checkItems == 0) {
                return $this->sendError('Every debit note should have at least one item', 500);
            }

            $checkQuantity = DebitNoteDetails::where('debitNoteAutoID', $id)
                ->where(function ($q) {
                    $q->where('debitAmount', '<=', 0)
                        ->orWhereNull('localAmount', '<=', 0)
                        ->orWhereNull('comRptAmount', '<=', 0)
                        ->orWhereNull('debitAmount')
                        ->orWhereNull('localAmount')
                        ->orWhereNull('comRptAmount');
                })
                ->count();
            if ($checkQuantity > 0) {
                return $this->sendError('Amount should be greater than 0 for every items', 500);
            }

            $debitNoteDetails = DebitNoteDetails::where('debitNoteAutoID', $id)->get();

            $finalError = array('amount_zero' => array(),
                'amount_neg' => array(),
                'required_serviceLine' => array(),
                'active_serviceLine' => array(),
            );
            $error_count = 0;

            foreach ($debitNoteDetails as $item) {
                $updateItem = DebitNoteDetails::find($item['debitNoteDetailsID']);

                if ($updateItem->serviceLineSystemID && !is_null($updateItem->serviceLineSystemID)) {

                    $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $updateItem->serviceLineSystemID)
                        ->where('isActive', 1)
                        ->first();
                    if (empty($checkDepartmentActive)) {
                        $updateItem->serviceLineSystemID = null;
                        $updateItem->serviceLineCode = null;
                        array_push($finalError['active_serviceLine'], $updateItem->glCode);
                        $error_count++;
                    }
                } else {
                    array_push($finalError['required_serviceLine'], $updateItem->glCode);
                    $error_count++;
                }

                $companyCurrencyConversion = \Helper::currencyConversion($updateItem->companySystemID, $updateItem->debitAmountCurrency, $updateItem->debitAmountCurrency, $updateItem->debitAmount);

                $companyId = $input['companySystemID'];

                $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
                    ->where('companyPolicyCategoryID', 67)
                    ->where('isYesNO', 1)
                    ->first();


                if (isset($policy->isYesNO) && $policy->isYesNO != 1) {

                    $input['localAmount'] = $companyCurrencyConversion['localAmount'];
                    $input['comRptAmount'] = $companyCurrencyConversion['reportingAmount'];
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $input['comRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                }

                $updateItem->save();

                if ($updateItem->debitAmount == 0 || $updateItem->localAmount == 0 || $updateItem->comRptAmount == 0) {
                    array_push($finalError['amount_zero'], $updateItem->itemPrimaryCode);
                    $error_count++;
                }
                if ($updateItem->debitAmount < 0 || $updateItem->localAmount < 0 || $updateItem->comRptAmount < 0) {
                    array_push($finalError['amount_neg'], $updateItem->itemPrimaryCode);
                    $error_count++;
                }
            }

            $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
            }

            $amount = DebitNoteDetails::where('debitNoteAutoID', $id)
                                       ->sum('debitAmount');

            $input['debitAmountTrans'] = $amount;

            $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $amount);



            if (isset($policy->isYesNO) && $policy->isYesNO != 1) {

                $input['debitAmountLocal'] = $companyCurrencyConversion['localAmount'];
                $input['debitAmountRpt'] = $companyCurrencyConversion['reportingAmount'];
                $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
            }

            $vatAmount = DebitNoteDetails::where('debitNoteAutoID', $id)
                ->sum('VATAmount');
            //vat amount currency conversion

            $input['VATAmount'] = $vatAmount;
            $VATCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $vatAmount);

            $input['VATAmountLocal'] = $VATCurrencyConversion['localAmount'];
            $input['VATAmountRpt'] = $VATCurrencyConversion['reportingAmount'];

            $totalNetAmount = DebitNoteDetails::where('debitNoteAutoID',$id)
                ->sum('netAmount');
            // total amount currency conversion

            $input['netAmount'] = $totalNetAmount;
            $totalCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $totalNetAmount);

            $input['netAmountLocal'] = $totalCurrencyConversion['localAmount'];
            $input['netAmountRpt']   = $totalCurrencyConversion['reportingAmount'];

            Taxdetail::where('documentSystemCode', $id)
                ->where('documentSystemID', $input["documentSystemID"])
                ->delete();

            // if VAT Applicable
            if(isset($input['isVATApplicable']) && $input['isVATApplicable'] && $vatAmount > 0){

                if(empty(TaxService::getInputVATGLAccount($input["companySystemID"]))) {
                    return $this->sendError(trans('custom.cannot_confirm_input_vat_gl_account_not_configured'), 500);
                }

                $outputChartOfAc = TaxService::getInputVATGLAccount($input["companySystemID"]);

                $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($outputChartOfAc->inputVatGLAccountAutoID, $input["companySystemID"]);

                if (!$checkAssignedStatus) {
                    return $this->sendError(trans('custom.cannot_confirm_input_vat_gl_account_not_assigned_t'), 500);
                }

                $taxDetail['companyID'] = $input['companyID'];
                $taxDetail['companySystemID'] = $input['companySystemID'];
                $taxDetail['documentID'] = $input['documentID'];
                $taxDetail['documentSystemID'] = $input['documentSystemID'];
                $taxDetail['documentSystemCode'] = $id;
                $taxDetail['documentCode'] = $debitNote->debitNoteCode;
                $taxDetail['taxShortCode'] = '';
                $taxDetail['taxDescription'] = '';
                $taxDetail['taxPercent'] = $input['VATPercentage'];
                $taxDetail['payeeSystemCode'] = $input['supplierID'];

                if(!empty($supplierAssignedDetail)) {
                    $taxDetail['payeeCode'] = $supplierAssignedDetail->primarySupplierCode;
                    $taxDetail['payeeName'] = $supplierAssignedDetail->supplierName;
                }

                 $taxDetail['amount'] = $vatAmount;

                $companyId = $input['companySystemID'];
                $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
                    ->where('companyPolicyCategoryID', 67)
                    ->where('isYesNO', 1)
                    ->first();

                $taxDetail['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $taxDetail['rptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                $taxDetail['localAmount'] = $VATCurrencyConversion['localAmount'];
                $taxDetail['rptAmount'] = $VATCurrencyConversion['reportingAmount'];

                if (isset($policy->isYesNO) && $policy->isYesNO == 1) {

                    $taxDetail['localCurrencyER'] = $debitNote->localCurrencyER;
                    $taxDetail['rptCurrencyER'] = $debitNote->companyReportingER;
                    $taxDetail['localAmount'] = $debitNote->VATAmountLocal;
                    $taxDetail['rptAmount'] = $debitNote->VATAmountRpt;
                }





                 $taxDetail['currency'] =  $input['supplierTransactionCurrencyID'];
                 $taxDetail['currencyER'] =  1;


                 $taxDetail['localCurrencyID'] =  $debitNote->localCurrencyID;
                 $taxDetail['rptCurrencyID'] =  $debitNote->companyReportingCurrencyID;
                 $taxDetail['payeeDefaultCurrencyID'] =  $input['supplierTransactionCurrencyID'];
                 $taxDetail['payeeDefaultCurrencyER'] =  1;
                 $taxDetail['payeeDefaultAmount'] =  $vatAmount;

                 Taxdetail::create($taxDetail);
            }

                $object = new ChartOfAccountValidationService();
                $result = $object->checkChartOfAccountStatus($input["documentSystemID"], $id, $input["companySystemID"]);


                if (isset($result) && !empty($result["accountCodes"])) {
                    return $this->sendError($result["errorMsg"]);
                }


            $input['RollLevForApp_curr'] = 1;
            $params = array('autoID' => $id,
                'company' => $debitNote->companySystemID,
                'document' => $debitNote->documentSystemID,
                'segment' => 0,
                'category' => 0,
                'amount' => $amount
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }
        $totalAmount = DebitNoteDetails::selectRaw("COALESCE(SUM(debitAmount),0) as debitAmountTrans, 
                                                    COALESCE(SUM(localAmount),0) as debitAmountLocal, 
                                                    COALESCE(SUM(comRptAmount),0) as debitAmountRpt,
                                                    COALESCE(SUM(VATAmount),0) as VATAmount,
                                                    COALESCE(SUM(VATAmountLocal),0) as VATAmountLocal, 
                                                    COALESCE(SUM(VATAmountRpt),0) as VATAmountRpt,
                                                    COALESCE(SUM(netAmount),0) as netAmount,
                                                    COALESCE(SUM(netAmountLocal),0) as netAmountLocal, 
                                                    COALESCE(SUM(netAmountRpt),0) as netAmountRpt
                                                    ")
            ->where('debitNoteAutoID', $id)
            ->first();

        $input['debitAmountTrans'] = \Helper::roundValue($totalAmount->debitAmountTrans);
        $input['debitAmountLocal'] = \Helper::roundValue($totalAmount->debitAmountLocal);
        $input['debitAmountRpt'] = \Helper::roundValue($totalAmount->debitAmountRpt);


        $input['VATAmount'] = \Helper::roundValue($totalAmount->VATAmount);
        $input['VATAmountLocal'] = \Helper::roundValue($totalAmount->VATAmountLocal);
        $input['VATAmountRpt'] = \Helper::roundValue($totalAmount->VATAmountRpt);


        $input['netAmount'] = \Helper::roundValue($totalAmount->netAmount);
        $input['netAmountLocal'] = \Helper::roundValue($totalAmount->netAmountLocal);
        $input['netAmountRpt'] = \Helper::roundValue($totalAmount->netAmountRpt);

        $employee = \Helper::getEmployeeInfo();

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        //var_dump($input);
        //exit();

        $debitNote = $this->debitNoteRepository->update($input, $id);

        return $this->sendReponseWithDetails($policy, trans('custom.debit_note_updated_successfully'),1,$confirm['data'] ?? null);
    }

    public function updateCurrency($id, UpdateDebitNoteAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'finance_period_by', 'finance_year_by', 'supplier', 'transactioncurrency',
            'confirmedByEmpID', 'confirmedDate', 'confirmed_by', 'confirmedByEmpSystemID','employee']);

        $input = $this->convertArrayToValue($input);

        if (!\Helper::validateCurrencyRate($input['companySystemID'], $input['supplierTransactionCurrencyID'])) {
            return $this->sendError(
                'Currency exchange rate to local and reporting currency must be greater than zero.',
                500
            );
        }

        /** @var DebitNote $debitNote */
        $debitNote = $this->debitNoteRepository->findWithoutFail($id);

        $type =  $input['type'];

        if (empty($debitNote)) {
            return $this->sendError(trans('custom.debit_note_not_found'));
        }

        if ($debitNote->confirmedYN == 1) {
            return $this->sendError(trans('custom.this_document_already_confirmed_you_cannot_edit'), 500);
        }

        if(isset($input['supplierTransactionCurrencyID']) && $input['supplierTransactionCurrencyID'] != $debitNote->supplierTransactionCurrencyID){

            $detailsCount = $debitNoteDetails = DebitNoteDetails::where('debitNoteAutoID', $id)->count();

            if($detailsCount > 0){
                return $this->sendError(trans('custom.you_cannot_change_the_currencyif_you_want_to_chang'), 500);
            }
        }


        if (isset($input['invoiceNumber']) && !empty($input['invoiceNumber'])) {
           


            if($type == 1)
            {
                $alreadyAdded = DebitNote::where('invoiceNumber', $input['invoiceNumber'])
                ->where('supplierID', $input['supplierID'])
                ->where('debitNoteAutoID', '<>', $id)
                ->first();
            }
            else if($type == 2)
            {
                $alreadyAdded = DebitNote::where('invoiceNumber', $input['invoiceNumber'])
                ->where('empID', $input['empID'])
                ->where('debitNoteAutoID', '<>', $id)
                ->first();
            }



            if ($alreadyAdded) {
                return $this->sendError("Entered invoice number was already used ($alreadyAdded->debitNoteCode). Please check again", 500);
            }
        }


        if (isset($input['lcPayment']) && $input['lcPayment'] == 1 && empty($input['lcDocCode'])) {
            return $this->sendError("LC Doc Code is required", 500);
        }

        // adding supplier grv details
        if($type == 1)
        {
            $supplierAssignedDetail = SupplierAssigned::select('liabilityAccountSysemID', 'liabilityAccount', 'UnbilledGRVAccountSystemID',
            'UnbilledGRVAccount','supplierName','primarySupplierCode')
            ->where('supplierCodeSytem', $input['supplierID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

            if ($supplierAssignedDetail) {
                $input["supplierGLCodeSystemID"] = $supplierAssignedDetail->liabilityAccountSysemID;
                $input["supplierGLCode"] = $supplierAssignedDetail->liabilityAccount;
                $input["liabilityAccountSysemID"] = $supplierAssignedDetail->liabilityAccountSysemID;
                $input["liabilityAccount"] = $supplierAssignedDetail->liabilityAccount;
                $input["UnbilledGRVAccountSystemID"] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
                $input["UnbilledGRVAccount"] = $supplierAssignedDetail->UnbilledGRVAccount;
                $input["empControlAccount"] = null;
                $input["empID"] = null;
            }
        }
        else if($type == 2)
        {

            
            $emp_control_acc = SystemGlCodeScenarioDetail::where('systemGlScenarioID',12)->where('companySystemID',$input['companySystemID'])->first();
            if(isset($emp_control_acc))
            {
                $emp_chart_acc = $emp_control_acc->chartOfAccountSystemID;
                if(!empty($emp_chart_acc) && $emp_chart_acc != null)
                {
                    $input["empControlAccount"] = $emp_chart_acc;
                    $input["supplierID"] = null;
                }
            }

          
        }

 

        if (isset($input['debitNoteDate'])) {
            if ($input['debitNoteDate']) {
                $input['debitNoteDate'] = new Carbon($input['debitNoteDate']);
            }
        }

        if (isset($input['supplierTransactionCurrencyID'])) {
            $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], 0);

            $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

        }

        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
        } else {
            $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
            $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 1;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else {
            $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
        }
        unset($inputParam);

        $documentDate = $input['debitNoteDate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];
        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            // return $this->sendError('Document date is not within the selected financial period !', 500);
        }

        if ($debitNote->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $validator = \Validator::make($input, [
                'companyFinancePeriodID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'debitNoteDate' => 'required',
                'supplierID' => ['required_if:type,1|numeric|min:1'],
                'empID' => ['required_if:type,2|numeric|min:1'],
                'supplierTransactionCurrencyID' => 'required|numeric|min:1',
                'comments' => 'required',
            ],
            [
                'empID.required_if' => 'Please select an employee',
                'supplierID.required_if' => 'Please select a supplier',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $documentDate = $input['debitNoteDate'];
            $monthBegin = $input['FYPeriodDateFrom'];
            $monthEnd = $input['FYPeriodDateTo'];
            if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
            } else {
                return $this->sendError('Document date is not within the selected financial period !', 500);
            }

            $checkItems = DebitNoteDetails::where('debitNoteAutoID', $id)
                ->count();

            if ($checkItems == 0) {
                return $this->sendError('Every debit note should have at least one item', 500);
            }

            $checkQuantity = DebitNoteDetails::where('debitNoteAutoID', $id)
                ->where(function ($q) {
                    $q->where('debitAmount', '<=', 0)
                        ->orWhereNull('localAmount', '<=', 0)
                        ->orWhereNull('comRptAmount', '<=', 0)
                        ->orWhereNull('debitAmount')
                        ->orWhereNull('localAmount')
                        ->orWhereNull('comRptAmount');
                })
                ->count();
            if ($checkQuantity > 0) {
                return $this->sendError('Amount should be greater than 0 for every items', 500);
            }

            $debitNoteDetails = DebitNoteDetails::where('debitNoteAutoID', $id)->get();

            $finalError = array('amount_zero' => array(),
                'amount_neg' => array(),
                'required_serviceLine' => array(),
                'active_serviceLine' => array(),
            );
            $error_count = 0;

            foreach ($debitNoteDetails as $item) {
                $updateItem = DebitNoteDetails::find($item['debitNoteDetailsID']);

                if ($updateItem->serviceLineSystemID && !is_null($updateItem->serviceLineSystemID)) {

                    $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $updateItem->serviceLineSystemID)
                        ->where('isActive', 1)
                        ->first();
                    if (empty($checkDepartmentActive)) {
                        $updateItem->serviceLineSystemID = null;
                        $updateItem->serviceLineCode = null;
                        array_push($finalError['active_serviceLine'], $updateItem->glCode);
                        $error_count++;
                    }
                } else {
                    array_push($finalError['required_serviceLine'], $updateItem->glCode);
                    $error_count++;
                }

                $companyCurrencyConversion = \Helper::currencyConversion($updateItem->companySystemID, $updateItem->debitAmountCurrency, $updateItem->debitAmountCurrency, $updateItem->debitAmount);




                $input['localAmount'] = $companyCurrencyConversion['localAmount'];
                $input['comRptAmount'] = $companyCurrencyConversion['reportingAmount'];
                $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $input['comRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];


                $updateItem->save();

                if ($updateItem->debitAmount == 0 || $updateItem->localAmount == 0 || $updateItem->comRptAmount == 0) {
                    array_push($finalError['amount_zero'], $updateItem->itemPrimaryCode);
                    $error_count++;
                }
                if ($updateItem->debitAmount < 0 || $updateItem->localAmount < 0 || $updateItem->comRptAmount < 0) {
                    array_push($finalError['amount_neg'], $updateItem->itemPrimaryCode);
                    $error_count++;
                }
            }

            $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
            }

            $amount = DebitNoteDetails::where('debitNoteAutoID', $id)
                ->sum('debitAmount');

            $input['debitAmountTrans'] = $amount;

            $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $amount);





            $input['debitAmountLocal'] = $companyCurrencyConversion['localAmount'];
            $input['debitAmountRpt'] = $companyCurrencyConversion['reportingAmount'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
            $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];


            $vatAmount = DebitNoteDetails::where('debitNoteAutoID', $id)
                ->sum('VATAmount');
            //vat amount currency conversion

            $input['VATAmount'] = $vatAmount;
            $VATCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $vatAmount);

            $input['VATAmountLocal'] = $VATCurrencyConversion['localAmount'];
            $input['VATAmountRpt'] = $VATCurrencyConversion['reportingAmount'];

            $totalNetAmount = DebitNoteDetails::where('debitNoteAutoID',$id)
                ->sum('netAmount');
            // total amount currency conversion

            $input['netAmount'] = $totalNetAmount;
            $totalCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $totalNetAmount);

            $input['netAmountLocal'] = $totalCurrencyConversion['localAmount'];
            $input['netAmountRpt']   = $totalCurrencyConversion['reportingAmount'];

            Taxdetail::where('documentSystemCode', $id)
                ->where('documentSystemID', $input["documentSystemID"])
                ->delete();

            // if VAT Applicable
            if(isset($input['isVATApplicable']) && $input['isVATApplicable'] && $vatAmount > 0){

                if(empty(TaxService::getInputVATGLAccount($input["companySystemID"]))) {
                    return $this->sendError(trans('custom.cannot_confirm_input_vat_gl_account_not_configured'), 500);
                }

                $outputChartOfAc = TaxService::getInputVATGLAccount($input["companySystemID"]);

                $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($outputChartOfAc->inputVatGLAccountAutoID, $input["companySystemID"]);

                if (!$checkAssignedStatus) {
                    return $this->sendError(trans('custom.cannot_confirm_input_vat_gl_account_not_assigned_t'), 500);
                }

                $taxDetail['companyID'] = $input['companyID'];
                $taxDetail['companySystemID'] = $input['companySystemID'];
                $taxDetail['documentID'] = $input['documentID'];
                $taxDetail['documentSystemID'] = $input['documentSystemID'];
                $taxDetail['documentSystemCode'] = $id;
                $taxDetail['documentCode'] = $debitNote->debitNoteCode;
                $taxDetail['taxShortCode'] = '';
                $taxDetail['taxDescription'] = '';
                $taxDetail['taxPercent'] = $input['VATPercentage'];
                $taxDetail['payeeSystemCode'] = $input['supplierID'];

                if(!empty($supplierAssignedDetail)) {
                    $taxDetail['payeeCode'] = $supplierAssignedDetail->primarySupplierCode;
                    $taxDetail['payeeName'] = $supplierAssignedDetail->supplierName;
                }

                $taxDetail['amount'] = $vatAmount;


                $taxDetail['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $taxDetail['rptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                $taxDetail['localAmount'] = $VATCurrencyConversion['localAmount'];
                $taxDetail['rptAmount'] = $VATCurrencyConversion['reportingAmount'];


                $taxDetail['currency'] =  $input['supplierTransactionCurrencyID'];
                $taxDetail['currencyER'] =  1;


                $taxDetail['localCurrencyID'] =  $debitNote->localCurrencyID;
                $taxDetail['rptCurrencyID'] =  $debitNote->companyReportingCurrencyID;
                $taxDetail['payeeDefaultCurrencyID'] =  $input['supplierTransactionCurrencyID'];
                $taxDetail['payeeDefaultCurrencyER'] =  1;
                $taxDetail['payeeDefaultAmount'] =  $vatAmount;

                Taxdetail::create($taxDetail);
            }

            $input['RollLevForApp_curr'] = 1;
            $params = array('autoID' => $id,
                'company' => $debitNote->companySystemID,
                'document' => $debitNote->documentSystemID,
                'segment' => 0,
                'category' => 0,
                'amount' => $amount
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        $employee = \Helper::getEmployeeInfo();

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        //var_dump($input);
        //exit();

        $debitNote = $this->debitNoteRepository->update($input, $id);

        return $this->sendReponseWithDetails($debitNote, trans('custom.debit_note_updated_successfully'),1,$confirm['data'] ?? null);

    }

    public function updateDebiteNoteType($id, UpdateDebitNoteAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'finance_period_by', 'finance_year_by', 'supplier', 'transactioncurrency',
            'confirmedByEmpID', 'confirmedDate', 'confirmed_by', 'confirmedByEmpSystemID','employee']);

        $input = $this->convertArrayToValue($input);

      
        /** @var DebitNote $debitNote */
        $debitNote = $this->debitNoteRepository->findWithoutFail($id);

        $type =  $input['type'];

        if($type == 1)
        {
            $details["empControlAccount"] = null;
            $details["empID"] = null;
        }
        else if($type == 2)
        {
            $details["supplierID"] = null;
        }

        if (empty($debitNote)) {
            return $this->sendError(trans('custom.debit_note_not_found'));
        }

        if ($debitNote->confirmedYN == 1) {
            return $this->sendError(trans('custom.this_document_already_confirmed_you_cannot_edit'), 500);
        }
        $details['type'] = $type;

        $debitNote = $this->debitNoteRepository->update($details, $id);

        return $this->sendResponse($debitNote, trans('custom.debit_note_updated_successfully'));

    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/debitNotes/{id}",
     *      summary="Remove the specified DebitNote from storage",
     *      tags={"DebitNote"},
     *      description="Delete DebitNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNote",
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
        /** @var DebitNote $debitNote */
        $debitNote = $this->debitNoteRepository->findWithoutFail($id);

        if (empty($debitNote)) {
            return $this->sendError(trans('custom.debit_note_not_found'));
        }

        $debitNote->delete();

        return $this->sendResponse($id, trans('custom.debit_note_deleted_successfully'));
    }

    public function debitNoteLocalUpdate($id,Request $request){

        $value = $request->data;
        $companyId = $request->companyId;

        $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();

        if (isset($policy->isYesNO) && $policy->isYesNO == 1) {
            $details = DebitNoteDetails::where('debitNoteAutoID',$id)->get();

            $masterINVID = DebitNote::findOrFail($id);
            $masterInvoiceArray = array('localCurrencyER'=>$value, 'VATAmountLocal'=>$masterINVID->VATAmount/$value, 'netAmountLocal'=>$masterINVID->netAmount/$value);
            $masterINVID->update($masterInvoiceArray);

            foreach($details as $item){
                $localAmount = $item->debitAmount / $value;
                $directInvoiceDetailsArray = array('localCurrencyER'=>$value, 'localAmount'=>$localAmount, 'VATAmountLocal'=>$item->VATAmount / $value, 'netAmountLocal'=>$item->netAmount / $value, 'debitAmountLocal' =>$masterINVID->debitAmountTrans/$value);
                $updatedLocalER = DebitNoteDetails::findOrFail($item->debitNoteDetailsID);
                $updatedLocalER->update($directInvoiceDetailsArray);
            }

            return $this->sendResponse([$id,$value], 'Update Local ER');
        }
        else{
            return $this->sendError('Policy not enabled', 400);
        }

    }

    public function debitNoteReportingUpdate($id,Request $request){

        $value = $request->data;
        $companyId = $request->companyId;

        $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();
        if (isset($policy->isYesNO) && $policy->isYesNO == 1) {
        $details = DebitNoteDetails::where('debitNoteAutoID',$id)->get();

        $masterINVID = DebitNote::findOrFail($id);
        $masterInvoiceArray = array('companyReportingER'=>$value, 'VATAmountRpt'=>$masterINVID->VATAmount/$value, 'netAmountRpt'=>$masterINVID->netAmount/$value, 'debitAmountRpt'=>$masterINVID->debitAmountTrans/$value);

        $masterINVID->update($masterInvoiceArray);

        foreach($details as $item){
            $reportingAmount = $item->debitAmount / $value;
            $directInvoiceDetailsArray = array('comRptCurrencyER'=>$value, 'comRptAmount'=>$reportingAmount, 'VATAmountRpt'=>$item->VATAmount / $value, 'netAmountRpt'=>$item->netAmount / $value);
            $updatedLocalER = DebitNoteDetails::findOrFail($item->debitNoteDetailsID);
            $updatedLocalER->update($directInvoiceDetailsArray);
        }
        return $this->sendResponse($id, 'Update Reporting ER');
        }
        else{
            return $this->sendError('Policy not enabled', 400);
        }
    }



    public function getDebitNoteMasterRecord(Request $request)
    {
        $id = $request->get('debitNoteAutoID');
        $debitNote = $this->debitNoteRepository->getAudit($id);

        if (empty($debitNote)) {
            return $this->sendError(trans('custom.debit_note_not_found'));
        }

        return $this->sendResponse($debitNote, trans('custom.data_retrieved_successfully'));
    }

    public function getAllDebitNotes(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approved', 'year', 'supplierID', 'projectID','type'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $supplierID = $request['supplierID'];
        $supplierID = (array)$supplierID;
        $supplierID = collect($supplierID)->pluck('id');

        $projectID = $request['projectID'];
        $projectID = (array)$projectID;
        $projectID = collect($projectID)->pluck('id');


        $search = $request->input('search.value');
        $debitNotes = $this->debitNotesByCompany($input, $search, $supplierID, $projectID);

        return \DataTables::of($debitNotes)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('debitNoteAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getDebitNoteFormData(Request $request)
    {
        $companyId = $request['companyId'];
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = DebitNote::select(DB::raw("YEAR(createdDateAndTime) as year"))
            ->whereNotNull('createdDateAndTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();
        $companyFinanceYear = \Helper::companyFinanceYear($companyId, 1);

        $suppliers = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName,vatEligible,vatPercentage"))
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

 

        $employees = Employee::selectRaw('empID, empName, employeeSystemID')->where('discharegedYN','<>', 2);
        if(Helper::checkHrmsIntergrated($companyId)){
            $employees = $employees->whereHas('hr_emp', function($q){
                $q->where('isDischarged', 0)->where('empConfirmedYN', 1);
            });
        }
        $employees = $employees->get();



        $currency = CurrencyMaster::all();

        $segments = SegmentMaster::where("companySystemID", $companyId)->approved()->withAssigned($companyId)
            ->where('isActive', 1)->get();

        $companyBasePO = ProcumentOrder::select(DB::raw("purchaseOrderID,purchaseOrderCode"))
            ->where('companySystemID', $companyId)
            ->where('poConfirmedYN', 1)
            ->where('poCancelledYN', 0)
            ->where('approved', -1)
            ->get();

        $isProject_base = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
            ->where('companySystemID', $companyId)
            ->where('isYesNO', 1)
            ->exists();

        $projects = ErpProjectMaster::where('companySystemID', $companyId)
                                        ->get();


          
        $debite_note_type = [["id"=>1,"name"=>"Supplier"],["id"=>2,"name"=>"Employee",]];                                

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'type' => $debite_note_type,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'companyFinanceYear' => $companyFinanceYear,
            'suppliers' => $suppliers,
            'employee' =>$employees,
            'segments' => $segments,
            'companyBasePO' => $companyBasePO,
            'isProjectBase' => $isProject_base,
            'projects' => $projects,
            'currency' => $currency,
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }


    /**
     * get Debit Note Approved By User
     * POST /getMaterielIssueApprovedByUser
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getDebitNoteApprovedByUser(Request $request)
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
        $debitNotes = DB::table('erp_documentapproved')
            ->select(
                'erp_debitnote.*',
                'employees.empName As created_emp',
                'doc_employees.empName As empFullName',
                'currencymaster.DecimalPlaces As DecimalPlaces',
                'currencymaster.CurrencyCode As CurrencyCode',
                'suppliermaster.supplierName As supplierName',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_debitnote', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'debitNoteAutoID')
                    ->where('erp_debitnote.companySystemID', $companyId)
                    ->where('erp_debitnote.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('employees as doc_employees', 'erp_debitnote.empID', 'doc_employees.employeeSystemID')
            ->leftJoin('currencymaster', 'supplierTransactionCurrencyID', 'currencymaster.currencyID')
            ->leftJoin('suppliermaster', 'supplierID', 'suppliermaster.supplierCodeSystem')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [15])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $debitNotes = $debitNotes->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $debitNotes = $debitNotes->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $debitNotes = $debitNotes->whereMonth('debitNoteDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $debitNotes = $debitNotes->whereYear('debitNoteDate', '=', $input['year']);
            }
        }


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $debitNotes = $debitNotes->where(function ($query) use ($search) {
                $query->where('debitNoteCode', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'like', "%{$search}%")
                    ->orWhere('doc_employees.empName', 'like', "%{$search}%");
            });
        }

        return \DataTables::of($debitNotes)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('debitNoteAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * get Debit Note Approval By User
     * POST /getDebitNoteApprovalByUser
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getDebitNoteApprovalByUser(Request $request)
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
        $debitNotes = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'erp_debitnote.*',
                'employees.empName As created_emp',
                'doc_employees.empName As empFullName',
                'currencymaster.DecimalPlaces As DecimalPlaces',
                'currencymaster.CurrencyCode As CurrencyCode',
                'suppliermaster.supplierName As supplierName',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyId)
                    ->where('documentSystemID', 15)
                    ->first();

                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    //$query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }

                $query->whereIn('employeesdepartments.documentSystemID', [15])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_debitnote', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'debitNoteAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_debitnote.companySystemID', $companyId)
                    ->where('erp_debitnote.approved', 0)
                    ->where('erp_debitnote.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('employees as doc_employees', 'erp_debitnote.empID', 'doc_employees.employeeSystemID')
            ->leftJoin('currencymaster', 'supplierTransactionCurrencyID', 'currencymaster.currencyID')
            ->leftJoin('suppliermaster', 'supplierID', 'suppliermaster.supplierCodeSystem')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [15])
            ->where('erp_documentapproved.companySystemID', $companyId);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $debitNotes = $debitNotes->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $debitNotes = $debitNotes->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $debitNotes = $debitNotes->whereMonth('debitNoteDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $debitNotes = $debitNotes->whereYear('debitNoteDate', '=', $input['year']);
            }
        }


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $debitNotes = $debitNotes->where(function ($query) use ($search) {
                $query->where('debitNoteCode', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'like', "%{$search}%")
                    ->orWhere('doc_employees.empName', 'like', "%{$search}%");
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
                        $query->orderBy('debitNoteAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function debitNoteReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['debitNoteAutoID'];
        $debitNote = $this->debitNoteRepository->findWithoutFail($id);
        $emails = array();
        if (empty($debitNote)) {
            return $this->sendError(trans('custom.debit_note_not_found'));
        }

        if ($debitNote->approved == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_debit_note_it_is_already_fu'));
        }

        if ($debitNote->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_debit_note_it_is_already_pa'));
        }

        if ($debitNote->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_debit_note_it_is_not_confir'));
        }

        $updateInput = ['confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
            'confirmedByName' => null, 'confirmedDate' => null, 'RollLevForApp_curr' => 1];

        $this->debitNoteRepository->update($updateInput, $id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $debitNote->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $debitNote->debitNoteCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $debitNote->debitNoteCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $debitNote->companySystemID)
            ->where('documentSystemCode', $debitNote->debitNoteAutoID)
            ->where('documentSystemID', $debitNote->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $debitNote->companySystemID)
                    ->where('documentSystemID', $debitNote->documentSystemID)
                    ->first();

                if (empty($companyDocument)) {
                    return ['success' => false, 'message' => 'Policy not found for this document'];
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
            ->where('companySystemID', $debitNote->companySystemID)
            ->where('documentSystemID', $debitNote->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($debitNote->documentSystemID,$id,$input['reopenComments'],'Reopened');

        return $this->sendResponse($debitNote->toArray(), trans('custom.debit_note_reopened_successfully'));
    }

    public function printDebitNote(Request $request)
    {
        $id = $request->get('debitNoteAutoID');
        $lang = $request->get('lang', 'en');
        $debitNote = $this->debitNoteRepository->getAudit($id);

        if (empty($debitNote)) {
            return $this->sendError(trans('custom.debit_note_not_found'));
        }

        $debitNote->docRefNo = \Helper::getCompanyDocRefNo($debitNote->companySystemID, $debitNote->documentSystemID);


        $totalAmount = DebitNoteDetails::where('debitNoteAutoID', $id)
            ->sum('debitAmount');
        $debitNote->totalAmount = $totalAmount;

        $totalVATAmount = DebitNoteDetails::where('debitNoteAutoID', $id)
            ->sum('VATAmount');
        $debitNote->totalVATAmount = $totalVATAmount;

        $totalNetAmount = DebitNoteDetails::where('debitNoteAutoID', $id)
            ->sum('netAmount');
        $debitNote->totalNetAmount = $totalNetAmount;


        $transDecimal = 2;
        $localDecimal = 3;
        $rptDecimal = 2;

        if ($debitNote->transactioncurrency) {
            $transDecimal = $debitNote->transactioncurrency->DecimalPlaces;
        }

        if ($debitNote->localcurrency) {
            $localDecimal = $debitNote->localcurrency->DecimalPlaces;
        }

        if ($debitNote->rptcurrency) {
            $rptDecimal = $debitNote->rptcurrency->DecimalPlaces;
        }
        $debitNote->transDecimal = $transDecimal;
        $debitNote->localDecimal = $localDecimal;
        $debitNote->rptDecimal = $rptDecimal;

        $array = array('entity' => $debitNote, 'lang' => $lang);
        $time = strtotime("now");
        $fileName = 'debit_note_' . $id . '_' . $time . '.pdf';

         $printTemplate = ErpDocumentTemplate::with('printTemplate')
                                            ->where('companyID', $debitNote->companySystemID)
                                            ->where('documentID', 15)
                                            ->first();

        // Check if Arabic language for RTL support
        $isRTL = ($lang === 'ar');
        $direction = $isRTL ? 'rtl' : 'ltr';

        if ($printTemplate && $printTemplate->printTemplateID == 10) {
            $html = view('print.debit_note_template.debit_note_gulf', $array);
            $htmlFooter = view('print.debit_note_template.debit_note_gulf_footer', $array);
            
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
            $html = view('print.debit_note', $array);
            
            // Configure mPDF for RTL support if Arabic
            $mpdfConfig = [
                'tempDir' => public_path('tmp'), 
                'mode' => 'utf-8', 
                'format' => 'A4-L', 
                'setAutoTopMargin' => 'stretch', 
                'autoMarginPadding' => -10
            ];
            
            if ($isRTL) {
                $mpdfConfig['direction'] = 'rtl';
            }
            
            $mpdf = new \Mpdf\Mpdf($mpdfConfig);
            $mpdf->AddPage('L');
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->WriteHTML($html);
            return $mpdf->Output($fileName, 'I');
        }
    }

    public function getDebitNotePaymentStatusHistory(Request $request)
    {
        $input = $request->all();

        $companySystemID = $input['companySystemID'];
        $debitNoteAutoID = $input['debitNoteAutoID'];

        $debitNoteMaster = DebitNote::find($debitNoteAutoID);
        if (empty($debitNoteMaster)) {
            return $this->sendError(trans('custom.debit_note_not_found'));
        }

        $detail = DB::select('SELECT
	erp_paysupplierinvoicemaster.PayMasterAutoId,
	erp_paysupplierinvoicemaster.companyID,
IF (
	erp_paysupplierinvoicedetail.matchingDocID = 0
	OR erp_paysupplierinvoicedetail.matchingDocID IS NULL,
	erp_paysupplierinvoicemaster.BPVcode,
	erp_matchdocumentmaster.matchingDocCode
) AS docCode,
IF (
	erp_paysupplierinvoicedetail.matchingDocID = 0
	OR erp_paysupplierinvoicedetail.matchingDocID IS NULL,
	erp_paysupplierinvoicemaster.BPVdate,
	erp_matchdocumentmaster.matchingDocdate
) AS docDate,
 erp_paysupplierinvoicedetail.supplierTransCurrencyID,
 currencymaster.CurrencyCode,
 currencymaster.DecimalPlaces,
 erp_paysupplierinvoicedetail.supplierPaymentAmount,
 erp_paysupplierinvoicemaster.confirmedYN,
 erp_paysupplierinvoicemaster.approved
FROM
	erp_paysupplierinvoicedetail
INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
LEFT JOIN erp_matchdocumentmaster ON erp_paysupplierinvoicedetail.matchingDocID = erp_matchdocumentmaster.matchDocumentMasterAutoID
INNER JOIN currencymaster ON erp_paysupplierinvoicedetail.supplierTransCurrencyID = currencymaster.currencyID
WHERE
	erp_paysupplierinvoicemaster.companySystemID = ' . $companySystemID . '
AND erp_paysupplierinvoicedetail.addedDocumentSystemID = ' . $debitNoteMaster->documentSystemID . '
AND erp_paysupplierinvoicedetail.bookingInvSystemCode = ' . $debitNoteAutoID . '
UNION ALL
	SELECT
		erp_matchdocumentmaster.PayMasterAutoId,
		erp_matchdocumentmaster.companyID,
		erp_matchdocumentmaster.matchingDocCode,
		erp_matchdocumentmaster.matchingDocdate,
		erp_matchdocumentmaster.supplierTransCurrencyID,
		currencymaster.CurrencyCode,
		currencymaster.DecimalPlaces,
		erp_matchdocumentmaster.matchedAmount,
		erp_matchdocumentmaster.matchingConfirmedYN,
		erp_matchdocumentmaster.approved
	FROM
		erp_matchdocumentmaster
	INNER JOIN currencymaster ON erp_matchdocumentmaster.supplierTransCurrencyID = currencymaster.currencyID
	WHERE
		erp_matchdocumentmaster.PayMasterAutoId = ' . $debitNoteAutoID . '
	AND erp_matchdocumentmaster.companySystemID = ' . $companySystemID . '
	AND erp_matchdocumentmaster.documentSystemID = ' . $debitNoteMaster->documentSystemID . '');

        return $this->sendResponse($detail, trans('custom.payment_status_retrieved_successfully'));
    }

    public function amendDebitNote(Request $request)
    {
        $input = $request->all();

        $debitNoteAutoID = $input['debitNoteAutoID'];

        $debitNoteMasterData = DebitNote::find($debitNoteAutoID);
        if (empty($debitNoteMasterData)) {
            return $this->sendError(trans('custom.debit_note_not_found'));
        }

        if ($debitNoteMasterData->refferedBackYN != -1) {
            return $this->sendError(trans('custom.you_cannot_refer_back_this_debit_note'));
        }

        $debitNoteArray = $debitNoteMasterData->toArray();

        $storeDebitNoteHistory = DebitNoteMasterRefferedback::insert($debitNoteArray);

        $debitNoteDetailRec = DebitNoteDetails::where('debitNoteAutoID', $debitNoteAutoID)->get();

        if (!empty($debitNoteDetailRec)) {
            foreach ($debitNoteDetailRec as $bookDetail) {
                $bookDetail['timesReferred'] = $debitNoteMasterData->timesReferred;
            }
        }

        $debitNoteDetailArray = $debitNoteDetailRec->toArray();

        $storeDebitNoteDetailHistory = DebitNoteDetailsRefferedback::insert($debitNoteDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $debitNoteAutoID)
            ->where('companySystemID', $debitNoteMasterData->companySystemID)
            ->where('documentSystemID', $debitNoteMasterData->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $debitNoteMasterData->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $debitNoteAutoID)
            ->where('companySystemID', $debitNoteMasterData->companySystemID)
            ->where('documentSystemID', $debitNoteMasterData->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $debitNoteMasterData->refferedBackYN = 0;
            $debitNoteMasterData->confirmedYN = 0;
            $debitNoteMasterData->confirmedByEmpSystemID = null;
            $debitNoteMasterData->confirmedByEmpID = null;
            $debitNoteMasterData->confirmedByName = null;
            $debitNoteMasterData->confirmedDate = null;
            $debitNoteMasterData->RollLevForApp_curr = 1;
            $debitNoteMasterData->save();
        }

        return $this->sendResponse($debitNoteMasterData->toArray(), trans('custom.debit_note_amend_successfully'));
    }

    public function amendDebitNoteReview(Request $request)
    {
        $input = $request->all();

        $debitNoteAutoID = $input['debitNoteAutoID'];

        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $debitNoteMasterData = DebitNote::find($debitNoteAutoID);

        if (empty($debitNoteMasterData)) {
            return $this->sendError(trans('custom.debit_note_not_found_1'));
        }

        if ($debitNoteMasterData->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_return_back_to_amend_this_debit_note_it'));
        }

        // checking document matched in payment
        $checkDetailExistMatch = PaySupplierInvoiceDetail::where('bookingInvSystemCode', $debitNoteAutoID)
            ->where('companySystemID', $debitNoteMasterData->companySystemID)
            ->where('addedDocumentSystemID', $debitNoteMasterData->documentSystemID)
            ->first();

        if ($checkDetailExistMatch) {
            return $this->sendError(trans('custom.cannot_return_back_to_amend_debit_note_is_added_to_1'));
        }

        // checking document matched in machmaster
        $checkDetailExistMatch = MatchDocumentMaster::where('PayMasterAutoId', $debitNoteAutoID)
            ->where('companySystemID', $debitNoteMasterData->companySystemID)
            ->where('documentSystemID', $debitNoteMasterData->documentSystemID)
            ->first();

        if ($checkDetailExistMatch) {
            return $this->sendError(trans('custom.cannot_return_back_to_amend_debit_note_is_added_to'));
        }


        $documentAutoId = $debitNoteAutoID;
        $documentSystemID = $debitNoteMasterData->documentSystemID;
        $checkBalance = GeneralLedgerService::validateDebitCredit($documentSystemID, $documentAutoId);
        if (!$checkBalance['status']) {
            $allowValidateDocumentAmend = false;
        } else {
            $allowValidateDocumentAmend = true;
        }

        if($debitNoteMasterData->approved == -1){
            if($allowValidateDocumentAmend){
                $validatePendingGlPost = ValidateDocumentAmend::validatePendingGlPost($documentAutoId,$documentSystemID);
                if(isset($validatePendingGlPost['status']) && $validatePendingGlPost['status'] == false){
                    if(isset($validatePendingGlPost['message']) && $validatePendingGlPost['message']){
                        return $this->sendError($validatePendingGlPost['message']);
                    }
                }
            }

            $validateVatReturnFilling = ValidateDocumentAmend::validateVatReturnFilling($documentAutoId,$documentSystemID,$debitNoteMasterData->companySystemID);
            if(isset($validateVatReturnFilling['status']) && $validateVatReturnFilling['status'] == false){
                $errorMessage = "Debit note " . $validateVatReturnFilling['message'];
                return $this->sendError($errorMessage);
            }
        }

        $emailBody = '<p>' . $debitNoteMasterData->debitNoteCode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $emailSubject = $debitNoteMasterData->debitNoteCode . ' has been return back to amend';

        DB::beginTransaction();
        try {

            //sending email to relevant party
            if ($debitNoteMasterData->confirmedYN == 1) {
                $emails[] = array('empSystemID' => $debitNoteMasterData->confirmedByEmpSystemID,
                    'companySystemID' => $debitNoteMasterData->companySystemID,
                    'docSystemID' => $debitNoteMasterData->documentSystemID,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody,
                    'docSystemCode' => $debitNoteMasterData->debitNoteCode);
            }

            $documentApproval = DocumentApproved::where('companySystemID', $debitNoteMasterData->companySystemID)
                ->where('documentSystemCode', $debitNoteAutoID)
                ->where('documentSystemID', $debitNoteMasterData->documentSystemID)
                ->get();

            foreach ($documentApproval as $da) {
                if ($da->approvedYN == -1) {
                    $emails[] = array('empSystemID' => $da->employeeSystemID,
                        'companySystemID' => $debitNoteMasterData->companySystemID,
                        'docSystemID' => $debitNoteMasterData->documentSystemID,
                        'alertMessage' => $emailSubject,
                        'emailAlertMessage' => $emailBody,
                        'docSystemCode' => $debitNoteMasterData->debitNoteCode);
                }
            }

            $sendEmail = \Email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            //deleting from approval table
            $deleteApproval = DocumentApproved::where('documentSystemCode', $debitNoteAutoID)
                ->where('companySystemID', $debitNoteMasterData->companySystemID)
                ->where('documentSystemID', $debitNoteMasterData->documentSystemID)
                ->delete();

            //deleting from general ledger table
            $deleteGLData = GeneralLedger::where('documentSystemCode', $debitNoteAutoID)
                ->where('companySystemID', $debitNoteMasterData->companySystemID)
                ->where('documentSystemID', $debitNoteMasterData->documentSystemID)
                ->delete();

            //deleting records from accounts payable
            $deleteAPData = AccountsPayableLedger::where('documentSystemCode', $debitNoteAutoID)
                ->where('companySystemID', $debitNoteMasterData->companySystemID)
                ->where('documentSystemID', $debitNoteMasterData->documentSystemID)
                ->delete();

            //deleting records from employee ledger
            $deleteELData = EmployeeLedger::where('documentSystemCode', $debitNoteAutoID)
                ->where('companySystemID', $debitNoteMasterData->companySystemID)
                ->where('documentSystemID', $debitNoteMasterData->documentSystemID)
                ->delete();

            //deleting records from tax ledger
            TaxLedger::where('documentMasterAutoID', $debitNoteAutoID)
                ->where('companySystemID', $debitNoteMasterData->companySystemID)
                ->where('documentSystemID', $debitNoteMasterData->documentSystemID)
                ->delete();

            $taxLedgerDetails = TaxLedgerDetail::where('documentMasterAutoID', $debitNoteAutoID)
                ->where('companySystemID', $debitNoteMasterData->companySystemID)
                ->where('documentSystemID', $debitNoteMasterData->documentSystemID)
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

            BudgetConsumedData::where('documentSystemCode', $debitNoteAutoID)
                ->where('companySystemID', $debitNoteMasterData->companySystemID)
                ->where('documentSystemID', $debitNoteMasterData->documentSystemID)
                ->delete();

            // updating fields
            $debitNoteMasterData->confirmedYN = 0;
            $debitNoteMasterData->confirmedByEmpSystemID = null;
            $debitNoteMasterData->confirmedByEmpID = null;
            $debitNoteMasterData->confirmedByName = null;
            $debitNoteMasterData->confirmedDate = null;
            $debitNoteMasterData->RollLevForApp_curr = 1;

            $debitNoteMasterData->approved = 0;
            $debitNoteMasterData->approvedByUserSystemID = null;
            $debitNoteMasterData->approvedByUserID = null;
            $debitNoteMasterData->approvedDate = null;
            $debitNoteMasterData->postedDate = null;
            $debitNoteMasterData->save();

            AuditTrial::createAuditTrial($debitNoteMasterData->documentSystemID,$debitNoteAutoID,$input['returnComment'],'returned back to amend');

            DB::commit();
            return $this->sendResponse($debitNoteMasterData->toArray(), trans('custom.debit_note_amend_saved_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function checkPaymentStatusDNPrint(Request $request)
    {

        $input = $request->all();

        $PayMasterAutoId = $input['PayMasterAutoId'];
        $companySystemID = $input['companySystemID'];
        $matchingDocCode = $input['matchingDocCode'];

        $printID = 0;

        $matchedAmount = MatchDocumentMaster::where('PayMasterAutoId', $PayMasterAutoId)
            ->where('companySystemID', $companySystemID)
            ->where('matchingDocCode', $matchingDocCode)
            ->first();

        if ($matchedAmount) {
            $printID = $matchedAmount->matchDocumentMasterAutoID;
        }

        return $this->sendResponse($printID, trans('custom.print_data_retrieved'));
    }

    public function approvalPreCheckDebitNote(Request $request)
    {
        $approve = \Helper::postedDatePromptInFinalApproval($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"], 500, ['type' => $approve["type"]]);
        } else {
            return $this->sendResponse(array('type' => $approve["type"]), $approve["message"]);
        }

    }

    public function exportDebitNotesByCompany(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approved', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $supplierID = $request['supplierID'];
        $supplierID = (array)$supplierID;
        $supplierID = collect($supplierID)->pluck('id');


        $projectID = $request['projectID'];
        $projectID = (array)$projectID;
        $projectID = collect($projectID)->pluck('id');
        
        $search = $request->input('search.value');
        $output = $this->debitNotesByCompany($input, $search, $supplierID, $projectID)->orderBy('debitNoteAutoID', $sort)->get();
        $data = array();
        $type = $request->docType;
        if (!empty($output)) {
            $x = 0;
            foreach ($output as $value) {
                $data[$x]['Debit Note Code'] = $value->debitNoteCode;

                if ($value->postedDate) {
                    $data[$x]['Posted Date'] = \Helper::convertDateWithTime($value->postedDate);
                } else {
                    $data[$x]['Posted Date'] = '';
                }

                $data[$x]['Narration'] = $value->comments;
                if ($value->supplier) {
                    $data[$x]['Supplier/Employee Code'] = $value->supplier->primarySupplierCode;
                    $data[$x]['Supplier/Employee Name'] = $value->supplier->supplierName;
                } else {
                    $data[$x]['Supplier/Employee Code'] = $value->employee->empID;;
                    $data[$x]['Supplier/Employee Name'] = $value->employee->empFullName;;
                }

                $decimalPlaces = 2;
                $localDecimalPlaces = 2;
                $rptDecimalPlaces = 2;

                if ($value->transactioncurrency) {
                    $data[$x]['Currency'] = $value->transactioncurrency->CurrencyCode;
                    $decimalPlaces = $value->transactioncurrency->DecimalPlaces;
                } else {
                    $data[$x]['Currency'] = '';
                }

                if ($value->localcurrency) {
                    $localDecimalPlaces = $value->localcurrency->DecimalPlaces;
                }

                if ($value->rptcurrency) {
                    $rptDecimalPlaces = $value->rptcurrency->DecimalPlaces;
                }

                $data[$x]['Amount'] = round($value->debitAmountTrans, $decimalPlaces);
                $data[$x]['Amount (Local)'] = round($value->debitAmountLocal, $localDecimalPlaces);
                $data[$x]['Amount (Rpt)'] = round($value->debitAmountRpt, $rptDecimalPlaces);

                if ($value->final_approved_by) {
                    $data[$x]['Approved By'] = $value->final_approved_by->empName;
                } else {
                    $data[$x]['Approved By'] = '';
                }

                if ($value->approvedDate) {
                    $data[$x]['Approved Date'] = \Helper::convertDateWithTime($value->approvedDate);
                } else {
                    $data[$x]['Approved Date'] = '';
                }

                $x++;
            }
        }
        $companyMaster = Company::find(isset($request->companyId)?$request->companyId: null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );

        $fileName = 'debit_note_by_company';
        $path = 'accounts-payable/debit_note_by_company/excel/';
        $basePath = CreateExcel::process($data,$request->docType,$fileName,$path,$detail_array);

        if($basePath == '')
        {
             return $this->sendError('Unable to export excel');
        }
        else
        {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }
    }


    private function debitNotesByCompany($request, $search, $supplierID, $projectID)
    {
        $input = $request;
        $selectedCompanyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $debitNotes = DebitNote::whereIn('companySystemID', $subCompanies)
            ->with('created_by', 'transactioncurrency', 'localcurrency', 'rptcurrency', 'supplier', 'final_approved_by', 'project','employee')
            ->where('documentSystemID', $input['documentId']);

        if (array_key_exists('supplierID', $input)) {
            if ($input['supplierID'] && !is_null($input['supplierID'])) {
                $debitNotes = $debitNotes->whereIn('supplierID', $supplierID);
            }
        }

        if (array_key_exists('projectID', $input)) {
            if ($input['projectID'] && !is_null($input['projectID'])) {
                $debitNotes = $debitNotes->whereIn('projectID', $projectID);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $debitNotes = $debitNotes->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('type', $input)) {
            if ($input['type'] && !is_null($input['type']) && $input['type'] > 0) {
                $debitNotes = $debitNotes->where('type', $input['type']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $debitNotes = $debitNotes->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $debitNotes = $debitNotes->whereMonth('debitNoteDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $debitNotes = $debitNotes->whereYear('debitNoteDate', '=', $input['year']);
            }
        }


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $debitNotes = $debitNotes->where(function ($query) use ($search) {
                $query->where('debitNoteCode', 'LIKE', "%{$search}%")
                    ->orWhere('invoiceNumber', 'LIKE', "%{$search}%")
                    ->orWhereHas('supplier', function ($query) use ($search) {
                        $query->where('supplierName', 'like', "%{$search}%")
                            ->orWhere('primarySupplierCode', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('employee', function ($query) use ($search) {
                        $query->where('empName', 'like', "%{$search}%")
                            ->orWhere('empID', 'LIKE', "%{$search}%");
                    });
            });
        }

        return $debitNotes;
    }
}
