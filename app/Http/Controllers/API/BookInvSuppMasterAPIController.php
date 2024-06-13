<?php
/**
 * =============================================
 * -- File Name : BookInvSuppMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  BookInvSuppMaster
 * -- Author : Mohamed Nazir
 * -- Create date : 08 - August 2018
 * -- Description : This file contains the all CRUD for Purchase Order
 * -- REVISION HISTORY
 * -- Date: 08-August 2018 By: Nazir Description: Added new function getInvoiceMasterRecord(),
 * -- Date: 24-August 2018 By: Nazir Description: Added new function getInvoiceMasterView(),
 * -- Date: 24-August 2018 By: Nazir Description: Added new function getInvoiceSupplierTypeBase(),
 * -- Date: 06-September 2018 By: Nazir Description: Added new function supplierInvoiceReopen(),
 * -- Date: 06-September 2018 By: Nazir Description: Added new function getInvoiceMasterApproval(),
 * -- Date: 06-September 2018 By: Nazir Description: Added new function getApprovedInvoiceForCurrentUser(),
 * -- Date: 06-September 2018 By: Nazir Description: Added new function approveSupplierInvoice(),
 * -- Date: 06-September 2018 By: Nazir Description: Added new function rejectSupplierInvoice(),
 * -- Date: 11-September 2018 By: Nazir Description: Added new function saveSupplierInvoiceTaxDetails(),
 * -- Date: 11-September 2018 By: Nazir Description: Added new function supplierInvoiceTaxTotal(),
 * -- Date: 12-September 2018 By: Nazir Description: Added new function printSupplierInvoice(),
 * -- Date: 12-September 2018 By: Nazir Description: Added new function getSupplierInvoiceStatusHistory(),
 * -- Date: 28-September 2018 By: Nazir Description: Added new function getSupplierInvoiceAmend(),
 * -- Date: 17-October 2018 By: Nazir Description: Added new function supplierInvoiceTaxPercentage(),
 * -- Date: 20-December 2018 By: Nazir Description: Added new function amendSupplierInvoiceReview(),
 * -- Date: 08-January 2019 By: Nazir Description: Added new function checkPaymentStatusSIPrint(),
 * -- Date: 05-February 2019 By: Nazir Description: Added new function clearSupplierInvoiceNo(),
 */

namespace App\Http\Controllers\API;

use App\helper\CustomValidation;
use App\helper\Helper;
use App\helper\SupplierInvoice;
use App\helper\TaxService;
use App\Http\Requests\API\CreateBookInvSuppMasterAPIRequest;
use App\Http\Requests\API\UpdateBookInvSuppMasterAPIRequest;
use App\Models\AccountsPayableLedger;
use App\Models\BudgetConsumedData;
use App\Models\ChartOfAccount;
use App\Models\CustomerMaster;
use App\Models\EmployeeLedger;
use App\Models\SupplierInvoiceDirectItem;
use App\Models\BookInvSuppDet;
use App\Models\BookInvSuppDetRefferedBack;
use App\Models\MonthlyDeclarationsTypes;
use App\Models\BookInvSuppMaster;
use App\Models\BookInvSuppMasterRefferedBack;
use App\Models\SupplierInvoiceItemDetail;
use App\Models\SystemGlCodeScenario;
use App\Models\TaxVatCategories;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ExpenseEmployeeAllocation;
use App\Models\CompanyDocumentAttachment;
use App\Models\TaxLedger;
use App\Models\TaxLedgerDetail;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\PoAdvancePayment;
use App\Models\CurrencyMaster;
use App\Models\CustomerInvoice;
use App\Models\DirectInvoiceDetails;
use App\Models\DirectInvoiceDetailsRefferedBack;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\Employee;
use App\Models\GeneralLedger;
use App\Models\GRVDetails;
use App\Models\MatchDocumentMaster;
use App\Models\Months;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\ProcumentOrder;
use App\Models\ErpProjectMaster;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\Company;
use App\Models\WarehouseMaster;
use App\Models\SupplierCurrency;
use App\Models\SupplierMaster;
use App\Models\Taxdetail;
use App\Models\TaxMaster;
use App\Models\UnbilledGrvGroupBy;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\ExpenseAssetAllocation;
use App\Repositories\BookInvSuppMasterRepository;
use App\Repositories\SupplierInvoiceItemDetailRepository;
use App\Repositories\ExpenseAssetAllocationRepository;
use App\Services\ChartOfAccountValidationService;
use App\Traits\AuditTrial;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Response;
use App\Models\SupplierBlock;
use App\Services\ValidateDocumentAmend;
use DateTime;
use App\Models\Tax;
/**
 * Class BookInvSuppMasterController
 * @package App\Http\Controllers\API
 */
class BookInvSuppMasterAPIController extends AppBaseController
{
    /** @var  BookInvSuppMasterRepository */
    private $bookInvSuppMasterRepository;
    private $userRepository;
    private $supplierInvoiceItemDetailRepository;
    private $expenseAssetAllocationRepository;

    public function __construct(BookInvSuppMasterRepository $bookInvSuppMasterRepo, UserRepository $userRepo, SupplierInvoiceItemDetailRepository $supplierInvoiceItemDetailRepo, ExpenseAssetAllocationRepository $expenseAssetAllocationRepo)
    {
        $this->bookInvSuppMasterRepository = $bookInvSuppMasterRepo;
        $this->userRepository = $userRepo;
        $this->supplierInvoiceItemDetailRepository = $supplierInvoiceItemDetailRepo;
        $this->expenseAssetAllocationRepository = $expenseAssetAllocationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bookInvSuppMasters",
     *      summary="Get a listing of the BookInvSuppMasters.",
     *      tags={"BookInvSuppMaster"},
     *      description="Get all BookInvSuppMasters",
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
     *                  @SWG\Items(ref="#/definitions/BookInvSuppMaster")
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
        $this->bookInvSuppMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->bookInvSuppMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bookInvSuppMasters = $this->bookInvSuppMasterRepository->all();

        return $this->sendResponse($bookInvSuppMasters->toArray(), 'Supplier Invoice Masters retrieved successfully');
    }

    /**
     * @param CreateBookInvSuppMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bookInvSuppMasters",
     *      summary="Store a newly created BookInvSuppMaster in storage",
     *      tags={"BookInvSuppMaster"},
     *      description="Store BookInvSuppMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BookInvSuppMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BookInvSuppMaster")
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
     *                  ref="#/definitions/BookInvSuppMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */

  
    public function store(CreateBookInvSuppMasterAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $alreadyAdded = BookInvSuppMaster::where('supplierInvoiceNo', $input['supplierInvoiceNo'])
                                        ->when($input['documentType'] != 4, function($query) use ($input) {
                                            $query->where('supplierID', $input['supplierID']);
                                        })
                                        ->when($input['documentType'] == 4, function($query) use ($input) {
                                            $query->where('employeeID', $input['employeeID']);
                                        })
                                        ->first();

        if ($alreadyAdded) {
            return $this->sendError("Entered supplier invoice number was already used ($alreadyAdded->bookingInvCode). Please check again", 500);
        }

        if(isset($input['custInvoiceDirectAutoID'])){
            $alreadyUsed = BookInvSuppMaster::where('custInvoiceDirectAutoID', $input['custInvoiceDirectAutoID'])->first();
            if ($alreadyUsed) {
                return $this->sendError("Entered customer invoice number was already used in ($alreadyUsed->bookingInvCode). Please check again", 500);
            }
        }
        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
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

        if (isset($input['bookingDate'])) {
            if ($input['bookingDate']) {
                $input['bookingDate'] = new Carbon($input['bookingDate']);
            }
        }

        if (isset($input['supplierInvoiceDate'])) {
            if ($input['supplierInvoiceDate']) {
                $input['supplierInvoiceDate'] = new Carbon($input['supplierInvoiceDate']);
            }
        }

        $documentDate = $input['bookingDate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Document date is not within the financial period!');
        }

        if (!isset($input['supplierID'])) {
            $input['supplierID'] = null;
        }

        if(isset($input['supplierID'])){
            $supplierId = $request['supplierID'];
            $supplier = SupplierMaster::where('supplierCodeSystem', '=', $supplierId)
                ->first();
            if($supplier){
                $input['retentionPercentage'] = $supplier->retentionPercentage;
            }
        }


        // check rcm activation
        if (isset($input['documentType']) && $input['documentType'] == 1 && isset($input['preCheck']) && $input['preCheck'] &&  !Helper::isLocalSupplier($input['supplierID'], $input['companySystemID'])) {
            $company = Company::where('companySystemID', $input['companySystemID'])->first();
            if (!empty($company) && $company->vatRegisteredYN == 1) {
                return $this->sendError('Do you want to activate Reverse Charge Mechanism for this Invoice', 500, array('type' => 'rcm_confirm'));
            }
        }

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];
        $input['documentSystemID'] = '11';
        $input['documentID'] = 'SI';

        $lastSerial = BookInvSuppMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], 0);

        //var_dump($companyCurrencyConversion);
        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
            $input['vatRegisteredYN'] = $company->vatRegisteredYN;
            $input['localCurrencyID'] = $company->localCurrencyID;
            $input['companyReportingCurrencyID'] = $company->reportingCurrency;
            $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        }

        $input['serialNo'] = $lastSerialNumber;
        $input['supplierTransactionCurrencyER'] = 1;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        $companyfinanceyear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($companyfinanceyear) {
            $startYear = $companyfinanceyear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];

            $input['FYBiggin'] = $companyfinanceyear->bigginingDate;
            $input['FYEnd'] = $companyfinanceyear->endingDate;
        } else {
            $finYear = date("Y");
        }

        if ($documentMaster) {
            $bookingInvCode = ($company->CompanyID . '\\' . $finYear . '\\' . 'BSI' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['bookingInvCode'] = $bookingInvCode;
        }

        if ($input['documentType'] != 4) {
            // adding supplier grv details
            $supplierAssignedDetail = SupplierAssigned::select('liabilityAccountSysemID',
                'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount','VATPercentage')
                ->where('supplierCodeSytem', $input['supplierID'])
                ->where('companySystemID', $input['companySystemID'])
                ->first();

            $input['isLocalSupplier'] = Helper::isLocalSupplier($input['supplierID'], $input['companySystemID']);

            if ($supplierAssignedDetail) {
                $input['supplierVATEligible'] = $supplierAssignedDetail->vatEligible;
                $input['supplierGLCodeSystemID'] = $supplierAssignedDetail->liabilityAccountSysemID;
                $input['supplierGLCode'] = $supplierAssignedDetail->liabilityAccount;
                $input['UnbilledGRVAccountSystemID'] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
                $input['UnbilledGRVAccount'] = $supplierAssignedDetail->UnbilledGRVAccount;
                $input['VATPercentage'] = $supplierAssignedDetail->VATPercentage;
            }
        } else {
            $checkEmployeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "employee-control-account");

            if (is_null($checkEmployeeControlAccount)) {
                return $this->sendError('Please configure Employee control account for this company', 500);
            }

            $input['employeeControlAcID'] = $checkEmployeeControlAccount;
        }


        $bookInvSuppMasters = $this->bookInvSuppMasterRepository->create($input);

        return $this->sendResponse($bookInvSuppMasters->toArray(), 'Supplier Invoice created successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bookInvSuppMasters/{id}",
     *      summary="Display the specified BookInvSuppMaster",
     *      tags={"BookInvSuppMaster"},
     *      description="Get BookInvSuppMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppMaster",
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
     *                  ref="#/definitions/BookInvSuppMaster"
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
        /** @var BookInvSuppMaster $bookInvSuppMaster */
        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->with(['created_by', 'confirmed_by', 'company', 'financeperiod_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'financeyear_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        },'supplier' => function($query){
            $query->with('tax')->selectRaw('CONCAT(primarySupplierCode," | ",supplierName) as supplierName,supplierCodeSystem,vatPercentage,retentionPercentage,whtApplicableYN,whtType');
        },'employee' => function($query){
            $query->selectRaw('CONCAT(empID," | ",empName) as employeeName,employeeSystemID');
        },'transactioncurrency'=> function($query){
            $query->selectRaw('CONCAT(CurrencyCode," | ",CurrencyName) as CurrencyName,currencyID');
        },'direct_customer_invoice' => function($query) {
            $query->select('custInvoiceDirectAutoID','bookingInvCode');
        }])->findWithoutFail($id);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }

        return $this->sendResponse($bookInvSuppMaster->toArray(), 'Supplier Invoice retrieved successfully');
    }

    public function unitCostValidation(Request $request)
    {
        $input = $request->all();
        $id = $input['bookingSuppMasInvAutoID'];
        $isUnitCostZeroValidate = false;

        if ($input['documentType'][0] == 3) {

            $checkUnitCostValidation = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $id)
                                                ->where(function ($q) {
                                                    $q->where('unitCost', '=', 0);
                                                })
                                                ->count();
            if ($checkUnitCostValidation > 0) {
                $isUnitCostZeroValidate = true;
            }
        }

        return $this->sendResponse($isUnitCostZeroValidate, 'Record retrieved successfully');
    }


    /**
     * @param int $id
     * @param UpdateBookInvSuppMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bookInvSuppMasters/{id}",
     *      summary="Update the specified BookInvSuppMaster in storage",
     *      tags={"BookInvSuppMaster"},
     *      description="Update BookInvSuppMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BookInvSuppMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BookInvSuppMaster")
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
     *                  ref="#/definitions/BookInvSuppMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBookInvSuppMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'financeperiod_by', 'financeyear_by', 'supplier','employee',
            'confirmedByEmpID', 'confirmedDate', 'company', 'confirmed_by', 'confirmedByEmpSystemID','transactioncurrency','direct_customer_invoice']);
        
        if (isset($input['directItems'])) {
            $directItems = $input['directItems'];
        }

        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();

        if(empty($input['retentionAmount'])){
            $input['retentionAmount'] = 0;
        }

        if(empty($input['retentionPercentage'])){
            $input['retentionPercentage'] = 0;
        }

        /** @var BookInvSuppMaster $bookInvSuppMaster */
        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->findWithoutFail($id);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }

        $supplier_id = $input['supplierID'];
        $supplierMaster = SupplierMaster::where('supplierCodeSystem',$supplier_id)->first();



        if ($input['supplierID'] != $bookInvSuppMaster->supplierID && $input['documentType'] != 4) {
            $input['isLocalSupplier'] = Helper::isLocalSupplier($input['supplierID'], $input['companySystemID']);
        }

        $customValidation = CustomValidation::validation(11,$bookInvSuppMaster,2,$input);
        if (!$customValidation["success"]) {
            return $this->sendError($customValidation["message"],500, array('type' => 'already_confirmed'));
        }

        $documentCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($bookInvSuppMaster->supplierTransactionCurrencyID);

        $alreadyAdded = BookInvSuppMaster::where('supplierInvoiceNo', $input['supplierInvoiceNo'])
                                        ->when($input['documentType'] != 4, function($query) use ($input) {
                                            $query->where('supplierID', $input['supplierID']);
                                        })
                                        ->when($input['documentType'] == 4, function($query) use ($input) {
                                            $query->where('employeeID', $input['employeeID']);
                                        })
                                        ->where('bookingSuppMasInvAutoID', '<>', $id)
                                        ->first();

        if ($alreadyAdded) {
            return $this->sendError("Entered supplier invoice number was already used ($alreadyAdded->bookingInvCode). Please check again", 500);
        }

        if(isset($input['custInvoiceDirectAutoID'])){
            $alreadyUsed = BookInvSuppMaster::where('custInvoiceDirectAutoID', $input['custInvoiceDirectAutoID'])
                ->where('bookingSuppMasInvAutoID', '<>', $id)
                ->first();

            if ($alreadyUsed) {
                return $this->sendError("Entered customer invoice number was already used in ($alreadyUsed->bookingInvCode). Please check again", 500);
            }
        }

        if ($input['documentType'] != 4) {
            $supplierAssignedDetail = SupplierAssigned::select('liabilityAccountSysemID', 'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount','VATPercentage')
                ->where('supplierCodeSytem', $input['supplierID'])
                ->where('companySystemID', $input['companySystemID'])
                ->first();

            if ($supplierAssignedDetail) {
                $input['supplierGLCodeSystemID'] = $supplierAssignedDetail->liabilityAccountSysemID;
                $input['supplierGLCode'] = $supplierAssignedDetail->liabilityAccount;
                $input['UnbilledGRVAccountSystemID'] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
                $input['UnbilledGRVAccount'] = $supplierAssignedDetail->UnbilledGRVAccount;
                if ($input['supplierID'] != $bookInvSuppMaster->supplierID) {
                    $input['VATPercentage'] = $supplierAssignedDetail->VATPercentage;
                }
            }
        } else {
            $checkEmployeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "employee-control-account");

            if (is_null($checkEmployeeControlAccount)) {
                return $this->sendError('Please configure Employee control account for this company', 500);
            }

            $input['employeeControlAcID'] = $checkEmployeeControlAccount;
        }


        if (isset($input['bookingDate']) && $input['bookingDate']) {
            $input['bookingDate'] = new Carbon($input['bookingDate']);
        }

        if (isset($input['supplierInvoiceDate']) && $input['supplierInvoiceDate']) {
            $input['supplierInvoiceDate'] = new Carbon($input['supplierInvoiceDate']);
        }

        if (isset($input['retentionDueDate']) && $input['retentionDueDate']) {
            $input['retentionDueDate'] = new Carbon($input['retentionDueDate']);
        }

        // calculating header total
        $directAmountTrans = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
            ->sum('DIAmount');

        $directAmountLocal = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
            ->sum('localAmount');

        $directAmountReport = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
            ->sum('comRptAmount');

        $detailTaxSumTrans = Taxdetail::where('documentSystemCode', $bookInvSuppMaster->bookingSuppMasInvAutoID)
            ->where('documentSystemID', 11)
            ->sum('amount');

        $detailTaxSumLocal = Taxdetail::where('documentSystemCode', $bookInvSuppMaster->bookingSuppMasInvAutoID)
            ->where('documentSystemID', 11)
            ->sum('localAmount');

        $detailTaxSumReport = Taxdetail::where('documentSystemCode', $bookInvSuppMaster->bookingSuppMasInvAutoID)
            ->where('documentSystemID', 11)
            ->sum('rptAmount');

        $bookingAmountTrans = 0;
        $bookingAmountLocal = 0;
        $bookingAmountRpt = 0;
        if ($input['documentType'] == 0 || $input['documentType'] == 2) {
            $input['rcmActivated'] = 0;
            $grvAmountTransaction = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                ->sum('totTransactionAmount');
            $grvAmountLocal = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                ->sum('totLocalAmount');
            $grvAmountReport = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                ->sum('totRptAmount');

            $bookingAmountTrans = $grvAmountTransaction + $directAmountTrans + $detailTaxSumTrans;
            $bookingAmountLocal = $grvAmountLocal + $directAmountLocal + $detailTaxSumLocal;
            $bookingAmountRpt = $grvAmountReport + $directAmountReport + $detailTaxSumReport;

            $input['bookingAmountTrans'] = \Helper::roundValue($bookingAmountTrans);
            $input['bookingAmountLocal'] = \Helper::roundValue($bookingAmountLocal);
            $input['bookingAmountRpt'] = \Helper::roundValue($bookingAmountRpt);

        } else if ($input['documentType'] == 3) {
            $grvAmountTransaction = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $id)
                ->sum('netAmount');
            $grvAmountLocal = SupplierInvoiceDirectItem::selectRaw('SUM(VATAmount * noQty) as VATAmount')->where('bookingSuppMasInvAutoID', $id)
                ->first();

            $totatlDirectItemTrans = $grvAmountTransaction + (isset($grvAmountLocal->VATAmount) ? $grvAmountLocal->VATAmount : 0);

            $currencyConversionDire = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $totatlDirectItemTrans);

          
            $bookingAmountTrans = $totatlDirectItemTrans + $directAmountTrans + $detailTaxSumTrans;
            $bookingAmountLocal = $currencyConversionDire['localAmount'] + $directAmountLocal + $detailTaxSumLocal;
            $bookingAmountRpt = $currencyConversionDire['reportingAmount'] + $directAmountReport + $detailTaxSumReport;

            $input['bookingAmountTrans'] = \Helper::roundValue($bookingAmountTrans);
            $input['bookingAmountLocal'] = \Helper::roundValue($bookingAmountLocal);
            $input['bookingAmountRpt'] = \Helper::roundValue($bookingAmountRpt);

        } else {

            $bookingAmountTrans = $directAmountTrans + $detailTaxSumTrans;
            $bookingAmountLocal = $directAmountLocal + $detailTaxSumLocal;
            $bookingAmountRpt = $directAmountReport + $detailTaxSumReport;

            $input['bookingAmountTrans'] = \Helper::roundValue($bookingAmountTrans);
            $input['bookingAmountLocal'] = \Helper::roundValue($bookingAmountLocal);
            $input['bookingAmountRpt'] = \Helper::roundValue($bookingAmountRpt);
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

        $documentDate = $input['bookingDate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Document date is not within the selected financial period !', 500);
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], 0);

        $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();
        $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;

        if($policy == false || $input['documentType'] != 1) {
            if ($companyCurrencyConversion) {
                $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
            }
        }



        if(isset($input['retentionPercentage'])){
            if($input['retentionPercentage'] > 100){
                return $this->sendError('Retention Percentage cannot be greater than 100%');
            }
        }

        if ($bookInvSuppMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {


            if(($input['isSupplierBlocked']) && ($bookInvSuppMaster->documentType == 0 ||$bookInvSuppMaster->documentType == 2) )
            {
       
                $validatorResult = \Helper::checkBlockSuppliers($input['bookingDate'],$supplier_id);
                if (!$validatorResult['success']) {              
                     return $this->sendError('The selected supplier has been blocked. Are you sure you want to proceed ?', 500,['type' => 'blockSupplier']);
    
                }
            }


            $validator = \Validator::make($input, [
                'companyFinancePeriodID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'bookingDate' => 'required',
                'supplierInvoiceDate' => 'required',
                'supplierInvoiceNo' => 'required',
                'supplierTransactionCurrencyID' => 'required|numeric|min:1',
                'comments' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            if ($input['documentType'] == 4) {
                $validatorSupp = \Validator::make($input, [
                    'employeeID' => 'required|numeric|min:0',
                ]);
            } else {
                $validatorSupp = \Validator::make($input, [
                    'supplierID' => 'required|numeric|min:0',
                ]);
            }

            
            if ($validatorSupp->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            /*
            * GWL-713
             * documentType == 0  -   invoice type - PO
            *  check policy 11 - Allow multiple GRV in One Invoice
            * if policy 11 is 1 allow to add multiple different PO's
            * if policy 11 is 0 do not allow multiple different PO's
             */
            if($input['documentType'] == 0){

                $policy = CompanyPolicyMaster::where('companyPolicyCategoryID', 11)
                    ->where('companySystemID', $bookInvSuppMaster->companySystemID)
                    ->first();

                if(empty($policy) || (!empty($policy) && !$policy->isYesNO)) {

                    $details = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)->get();
                    if(count($details)){

                        $poIdArray = $details->pluck('purchaseOrderID')->toArray();
                        if (count(array_unique($poIdArray)) > 1) {
                            return $this->sendError('Multiple PO\'s cannot be added. Different PO found on saved details.');
                        }
                    }

                }
            }

            if ($input['documentType'] != 4){
                if($input['retentionDueDate'] == null && $input['retentionAmount'] > 0){
                    return $this->sendError('Due Date cannot be null as retention amount is greater than zero', 500);
                }



            if ($input['documentType'] == 1 || $input['documentType'] == 4) {
                $vatTrans = TaxService::processDirectSupplierInvoiceVAT($input['bookingSuppMasInvAutoID'], $input['documentSystemID']);
                $input['retentionVatAmount'] = $vatTrans['masterVATTrans'] *  $input['retentionPercentage'] / 100;
            }

            if ($input['documentType'] == 0) {
                    $vatTrans = TaxService::processPoBasedSupllierInvoiceVAT($input['bookingSuppMasInvAutoID']);
                    if(!is_numeric($input['retentionPercentage']))
                    {
                        $input['retentionPercentage'] = 0;
                    }
                    
                    $input['retentionVatAmount'] = $vatTrans['totalVAT'] *  $input['retentionPercentage'] / 100;
            }
            if ($input['documentType'] == 3) {
                    $vatTrans = TaxService::processSupplierInvoiceItemsVAT($input['bookingSuppMasInvAutoID']);
                    $input['retentionVatAmount'] = $vatTrans['masterVATTrans'] *  $input['retentionPercentage'] / 100;
            }

            }

            if ($input['documentType'] != 4 && $input['retentionAmount'] > 0) {

                $slug = "retention-control-account";
                $isConfigured = SystemGlCodeScenario::where('slug',$slug)->first();
                $companyID = isset($bookInvSuppMaster->companySystemID) ? $bookInvSuppMaster->companySystemID: null;
                $isDetailConfigured = ($isConfigured) ? SystemGlCodeScenarioDetail::where('systemGLScenarioID', $isConfigured->id)->where('companySystemID', $companyID)->first() : null;
                if($isConfigured && $isDetailConfigured) {
                    if ($isConfigured->isActive != 1 || $isDetailConfigured->chartOfAccountSystemID == null || $isDetailConfigured->chartOfAccountSystemID == 0) {
                        return $this->sendError('Chart of account is not configured for retention control account', 500);
                    }
                    $isChartOfAccountConfigured = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $isDetailConfigured->chartOfAccountSystemID)->where('companySystemID', $isDetailConfigured->companySystemID)->first();
                    if($isChartOfAccountConfigured){
                        if ($isChartOfAccountConfigured->isActive != 1 || $isChartOfAccountConfigured->chartOfAccountSystemID == null || $isChartOfAccountConfigured->isAssigned != -1 || $isChartOfAccountConfigured->chartOfAccountSystemID == 0 || $isChartOfAccountConfigured->companySystemID == 0 || $isChartOfAccountConfigured->companySystemID == null) {
                            return $this->sendError('Chart of account is not configured for retention control account', 500);
                        }
                    }
                    else{
                        return $this->sendError('Chart of account is not configured for retention control account', 500);
                    }
                }
                else{
                    return $this->sendError('Chart of account is not configured for retention control account', 500);
                }
            }

            $checkItems = 0;

            if ($input['documentType'] == 1 || $input['documentType'] == 4) {
                $checkItems = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
                    ->count();
                if ($checkItems == 0) {
                    return $this->sendError('Every Supplier Invoice should have at least one item', 500);
                }

                $employeeInvoice = CompanyPolicyMaster::where('companyPolicyCategoryID', 68)
                                    ->where('companySystemID', $bookInvSuppMaster->companySystemID)
                                    ->first();

                $employeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($bookInvSuppMaster->companySystemID, null, "employee-control-account");

                $companyData = Company::find($bookInvSuppMaster->companySystemID);

                if ($employeeInvoice && $employeeInvoice->isYesNO == 1 && $companyData && $companyData->isHrmsIntergrated && ($employeeControlAccount > 0)) {
                    $employeeControlRelatedAc = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
                                                                   ->where('chartOfAccountSystemID', $employeeControlAccount)
                                                                   ->get();


                    foreach ($employeeControlRelatedAc as $key => $value) {
                        $detailTotalOfLine = $value->netAmount + $value->VATAmount;

                        $allocatedSum = ExpenseEmployeeAllocation::where('documentDetailID', $value['directInvoiceDetailsID'])
                                                                          ->where('documentSystemID', $bookInvSuppMaster->documentSystemID)
                                                                          ->sum('amount');
                        if ($input['documentType'] != 4){
                            if ($allocatedSum != $detailTotalOfLine) {
                                return $this->sendError("Please allocate the full amount of ".$value->glCode." - ".$value->glCodeDes);
                            }
                        }


                        if ($bookInvSuppMaster->createMonthlyDeduction && (is_null($value->deductionType) || $value->deductionType == 0)) {
                            return $this->sendError("Please set deduction Type for ".$value->glCode." - ".$value->glCodeDes);
                        }
                    }

                }

            } 



            if ($checkItems > 0) {
                $checkQuantity = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
                    ->where(function ($q) {
                        $q->where('DIAmount', '<=', 0)
                            ->orWhereNull('localAmount', '<=', 0)
                            ->orWhereNull('comRptAmount', '<=', 0)
                            ->orWhereNull('DIAmount')
                            ->orWhereNull('localAmount')
                            ->orWhereNull('comRptAmount');
                    })
                    ->count();
                if ($checkQuantity > 0) {
                    return $this->sendError('Amount should be greater than 0 for every items', 500);
                }
            }

            if ($input['documentType'] == 0 || $input['documentType'] == 2) {

                $checkGRVItems = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                    ->count();
                if ($checkGRVItems == 0) {
                    return $this->sendError('Every Supplier Invoice should have at least one item', 500);
                }

                $checkGRVQuantity = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                                                    ->where(function ($q) {
                                                        $q->where('supplierInvoAmount', '<=', 0)
                                                            ->orWhereNull('totLocalAmount', '<=', 0)
                                                            ->orWhereNull('totRptAmount', '<=', 0)
                                                            ->orWhereNull('totTransactionAmount')
                                                            ->orWhereNull('totLocalAmount')
                                                            ->orWhereNull('totRptAmount');
                                                    })
                                                    ->whereHas('unbilled_grv', function($query){
                                                        $query->whereNull('purhaseReturnAutoID');
                                                    })
                                                    ->count();
                if ($checkGRVQuantity > 0) {
                    return $this->sendError('Amount should be greater than 0 for every items', 500);
                }

                //updating unbilled grv table all flags
                $getBookglDetailUnbilled = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                    ->get();
                if ($getBookglDetailUnbilled) {
                    foreach ($getBookglDetailUnbilled as $row) {

                        $unbilledSumData = UnbilledGrvGroupBy::find($row['unbilledgrvAutoID']);

                        if (is_null($unbilledSumData->purhaseReturnAutoID)) {
                            $getTotal = BookInvSuppDet::where('unbilledgrvAutoID', $row['unbilledgrvAutoID'])
                                ->sum('totTransactionAmount');

                            if ((round($unbilledSumData->totTransactionAmount, $documentCurrencyDecimalPlace) == round($getTotal, $documentCurrencyDecimalPlace)) || ($getTotal > $unbilledSumData->totTransactionAmount)) {

                                $unbilledSumData->selectedForBooking = -1;
                                $unbilledSumData->fullyBooked = 2;
                            } else {
                                $unbilledSumData->selectedForBooking = 0;
                                $unbilledSumData->fullyBooked = 1;
                            }
                            $unbilledSumData->save();
                        }
                    }
                }

            } else if ($input['documentType'] == 3) {

                $checkGRVItems = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $id)
                    ->count();
                if ($checkGRVItems == 0) {
                    return $this->sendError('Every Supplier Invoice should have at least one item', 500);
                }

                $checkGRVQuantity = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $id)
                                                    ->where(function ($q) {
                                                        $q->where('noQty', '<=', 0);
                                                    })
                                                    ->count();
                if ($checkGRVQuantity > 0) {
                    return $this->sendError('No of qty should be greater than 0 for every items', 500);
                }

                $dirItemDetails = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $id)
                                                            ->get();

                if (TaxService::checkPOVATEligible($input['supplierVATEligible'], $input['vatRegisteredYN'])) {
                    if (!empty($dirItemDetails)) {
                        foreach ($dirItemDetails as $itemDiscont) {
                            $calculateItemDiscount = 0;
                            $calculateItemDiscount = $itemDiscont['unitCost'] - $itemDiscont['discountAmount'];

                            if (!$input['vatRegisteredYN']) {
                                $calculateItemDiscount = $calculateItemDiscount + $itemDiscont['VATAmount'];
                            } else {
                                $checkVATCategory = TaxVatCategories::with(['type'])->find($itemDiscont['vatSubCategoryID']);
                                if ($checkVATCategory) {
                                    if (isset($checkVATCategory->type->id) && $checkVATCategory->type->id == 1 && $itemDiscont['exempt_vat_portion'] > 0 && $itemDiscont['VATAmount'] > 0) {
                                       $exemptVAT = $itemDiscont['VATAmount'] * ($itemDiscont['exempt_vat_portion'] / 100);

                                       $calculateItemDiscount = $calculateItemDiscount + $exemptVAT;
                                    } else if (isset($checkVATCategory->type->id) && $checkVATCategory->type->id == 3) {
                                        $calculateItemDiscount = $calculateItemDiscount + $itemDiscont['VATAmount'];
                                    }
                                }
                            }

                            // $calculateItemTax = (($itemDiscont['VATPercentage'] / 100) * $calculateItemDiscount) + $calculateItemDiscount;
                            $vatLineAmount = $itemDiscont['VATAmount']; //($calculateItemTax - $calculateItemDiscount);

                            $currencyConversion = \Helper::currencyConversion($itemDiscont['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $calculateItemDiscount);

                            $currencyConversionForLineAmount = \Helper::currencyConversion($itemDiscont['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $vatLineAmount);

                            $currencyConversionLineDefault = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $calculateItemDiscount);


                            SupplierInvoiceDirectItem::where('id', $itemDiscont['id'])
                                ->update([
                                    'costPerUnitLocalCur' => \Helper::roundValue($currencyConversion['localAmount']),
                                    'costPerUnitSupDefaultCur' => \Helper::roundValue($currencyConversionLineDefault['documentAmount']),
                                    'costPerUnitSupTransCur' => \Helper::roundValue($calculateItemDiscount),
                                    'costPerUnitComRptCur' => \Helper::roundValue($currencyConversion['reportingAmount']),
                                    'VATPercentage' => $itemDiscont['VATPercentage'],
                                    'VATAmount' => \Helper::roundValue($vatLineAmount),
                                    'VATAmountLocal' => \Helper::roundValue($currencyConversionForLineAmount['localAmount']),
                                    'VATAmountRpt' => \Helper::roundValue($currencyConversionForLineAmount['reportingAmount'])
                                ]);
                        }
                    }
                } else {
                    if (!empty($dirItemDetails)) {
                        foreach ($dirItemDetails as $itemDiscont) {
                            $calculateItemDiscount = $itemDiscont['unitCost'] - $itemDiscont['discountAmount'];

                            $currencyConversion = \Helper::currencyConversion(
                                $itemDiscont['companySystemID'],
                                $input['supplierTransactionCurrencyID'],
                                $input['supplierTransactionCurrencyID'],
                                $calculateItemDiscount
                            );

                            $currencyConversionLineDefault = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $calculateItemDiscount);

                            $vatLineAmount = 0;
                            $vatAmountLocal = 0;
                            $vatAmountRpt = 0;
                            if (isset($input['rcmActivated']) && $input['rcmActivated']) {
                                $vatLineAmount = $itemDiscont['VATAmount'];
                                $currencyConversionForLineAmount = \Helper::currencyConversion($itemDiscont['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $vatLineAmount);
                                $vatLineAmount =  \Helper::roundValue($vatLineAmount);
                                $vatAmountLocal = \Helper::roundValue($currencyConversionForLineAmount['localAmount']);
                                $vatAmountRpt = \Helper::roundValue($currencyConversionForLineAmount['reportingAmount']);
                            }


                            SupplierInvoiceDirectItem::where('id', $itemDiscont['id'])
                                ->update([
                                    'costPerUnitLocalCur' => \Helper::roundValue($currencyConversion['localAmount']),
                                    'costPerUnitSupDefaultCur' => \Helper::roundValue($currencyConversionLineDefault['documentAmount']),
                                    'costPerUnitSupTransCur' => \Helper::roundValue($calculateItemDiscount),
                                    'costPerUnitComRptCur' => \Helper::roundValue($currencyConversion['reportingAmount']),
                                    'VATAmount' => $vatLineAmount,
                                    'VATAmountLocal' => $vatAmountLocal,
                                    'VATAmountRpt' => $vatAmountRpt
                                ]);
                        }
                    }
                }
            }

            //checking Supplier Invoice amount is greater than UnbilledGRV Amount validations
            if ($input['documentType'] == 0 || $input['documentType'] == 2) {
                $checktotalExceed = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                    ->with(['grvmaster'])
                    ->get();
                if ($checktotalExceed) {
                    foreach ($checktotalExceed as $exc) {

                        $unbilledGRTotal = UnbilledGrvGroupBy::where('grvAutoID', $exc->grvAutoID)
                            ->where('supplierID', $exc->supplierID)
                            ->sum('totTransactionAmount');

                        $checkPreTotal = BookInvSuppDet::where('grvAutoID', $exc->grvAutoID)
                            ->where('supplierID', $exc->supplierID)
                            ->sum('totTransactionAmount');

                        if (round($checkPreTotal, $documentCurrencyDecimalPlace) > round($unbilledGRTotal, $documentCurrencyDecimalPlace)) {
                            return $this->sendError('Supplier Invoice amount is greater than Unbilled GRV amount. Total Invoice amount is '. round($checkPreTotal, $documentCurrencyDecimalPlace) .'and Total Unbilled GRV amount is '. round($unbilledGRTotal, $documentCurrencyDecimalPlace) , 500);
                        }
                    }
                }
            }


            //checking Supplier Invoice amount is greater than GRV Amount validations
            if ($input['documentType'] == 0 || $input['documentType'] == 2) {
                $checktotalExceed = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                    ->with(['grvmaster'])
                    ->get();
                if ($checktotalExceed) {
                    $company = Company::where('companySystemID', $input['companySystemID'])->first();
                    $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $input['supplierID'])
                        ->where('companySystemID', $input['companySystemID'])
                        ->first();
                    $valEligible = false;
                    $rcmActivate = TaxService::isSupplierInvoiceRcmActivated($id);
                    if (($company->vatRegisteredYN == 1  || $supplierAssignedDetail->vatEligible == 1) && !$rcmActivate) {
                        $valEligible = true;
                    }
                    foreach ($checktotalExceed as $exc) {
                        $grvDetailSum = GRVDetails::select(DB::raw('COALESCE(SUM(landingCost_RptCur * noQty),0) as total, SUM(VATAmountRpt*noQty) as transVATAmount'))
                            ->where('grvAutoID', $exc->grvAutoID)
                            ->first();

                        $logisticVATTotal = PoAdvancePayment::where('grvAutoID', $exc->grvAutoID)
                                                            ->sum('VATAmountRpt');


                        $checkPreTotal = BookInvSuppDet::where('grvAutoID', $exc->grvAutoID)
                            ->sum('totRptAmount');
                        if (!$valEligible) {
                            $grvDetailTotal = $grvDetailSum['total'];
                        } else {
                            $grvDetailTotal = $grvDetailSum['total'] + $grvDetailSum['transVATAmount'] + $logisticVATTotal;
                        }
                        if (round($checkPreTotal, $documentCurrencyDecimalPlace) > round($grvDetailTotal, $documentCurrencyDecimalPlace)) {
                            return $this->sendError('Supplier Invoice amount is greater than GRV amount. Total Invoice amount(Reporting Currency) is '.round($checkPreTotal, $documentCurrencyDecimalPlace). ' And Total GRV amount(Reporting Currency) is '. round($grvDetailTotal, $documentCurrencyDecimalPlace), 500);
                        }
                    }
                }
            }


            if ($input['documentType'] == 0) {
                //updating PO Master invoicedBooked flag
                $getPoRecords = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                    ->groupBy('purchaseOrderID')
                    ->get();

                if ($getPoRecords) {
                    foreach ($getPoRecords as $row) {

                        $poMasterTableTotal = ProcumentOrder::find($row['purchaseOrderID']);

                        $getTotal = BookInvSuppDet::where('purchaseOrderID', $row['purchaseOrderID'])
                            ->sum('totTransactionAmount');

                        if (round($poMasterTableTotal->poTotalSupplierTransactionCurrency, $documentCurrencyDecimalPlace) == round($getTotal, $documentCurrencyDecimalPlace)) {
                            $poMasterTableTotal->invoicedBooked = 2;
                        } else if (round($poMasterTableTotal->poTotalSupplierTransactionCurrency, $documentCurrencyDecimalPlace) <= round($getTotal, $documentCurrencyDecimalPlace)) {
                            $poMasterTableTotal->invoicedBooked = 2;
                        } else if ($getTotal != 0) {
                            $poMasterTableTotal->invoicedBooked = 1;
                        } else if ($getTotal == 0) {
                            $poMasterTableTotal->invoicedBooked = 0;
                        }
                        $poMasterTableTotal->save();
                    }
                }
            }

            $directInvoiceDetails = DirectInvoiceDetails::where('directInvoiceAutoID', $id)->get();

            $finalError = array('amount_zero' => array(),
                'amount_neg' => array(),
                'required_serviceLine' => array(),
                'active_serviceLine' => array(),
                'cannot_add_revenue' => array()
            );
            $error_count = 0;

            //Supplier Direct Invoice
            if ($input['documentType'] == 1) {

                foreach ($directInvoiceDetails as $item) {

                    $chartOfAccount = ChartOfAccountsAssigned::select('controlAccountsSystemID')->where('chartOfAccountSystemID', $item->chartOfAccountSystemID)->first();

                    if ($chartOfAccount->controlAccountsSystemID == 1) {
                        array_push($finalError['cannot_add_revenue'], $item->glCode);
                        $error_count++;
                    }
                }
            }

            foreach ($directInvoiceDetails as $item) {
                $updateItem = DirectInvoiceDetails::find($item['directInvoiceDetailsID']);

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

                $companyCurrencyConversion = \Helper::currencyConversion($updateItem->companySystemID, $updateItem->DIAmountCurrency, $updateItem->DIAmountCurrency, $updateItem->DIAmount);

                if (isset($policy->isYesNO) && $policy->isYesNO != 1) {
                    $input['localAmount'] = $companyCurrencyConversion['localAmount'];
                    $input['comRptAmount'] = $companyCurrencyConversion['reportingAmount'];
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $input['comRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                }

                $updateItem->save();

                if ($updateItem->DIAmount == 0 || $updateItem->localAmount == 0 || $updateItem->comRptAmount == 0) {
                    array_push($finalError['amount_zero'], $updateItem->serviceLineCode);
                    $error_count++;
                }
                if ($updateItem->DIAmount < 0 || $updateItem->localAmount < 0 || $updateItem->comRptAmount < 0) {
                    array_push($finalError['amount_neg'], $updateItem->serviceLineCode);
                    $error_count++;
                }
            }

            if($input['documentType'] == 1 || $input['documentType'] == 4) {
                $directInvoiceItems = $directItems;
                foreach ($directInvoiceItems as $directInvoiceItem) {
                    $allocatedItems = ExpenseAssetAllocation::where('documentDetailID',$directInvoiceItem['directInvoiceDetailsID'])->where('documentSystemCode',$directInvoiceItem['directInvoiceAutoID'])->get();
                    $total = 0;
                    foreach($allocatedItems as $allocatedItem) {
                        $total += $allocatedItem->amount;
                        if(isset($directInvoiceItem['netAmount']) && $directInvoiceItem['netAmount'] < $total) {
                            return $this->sendError("Detail amount cannot be less than allocated amount.",500);
                        }
                    }
                }
            }


            $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
            }

            $input['RollLevForApp_curr'] = 1;

            unset($input['confirmedYN']);
            unset($input['confirmedByEmpSystemID']);
            unset($input['confirmedByEmpID']);
            unset($input['confirmedByName']);
            unset($input['confirmedDate']);

            if ($input['documentType'] == 0 || $input['documentType'] == 2) {
                $grvAmountTransaction = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                    ->sum('totTransactionAmount');
                $bookingAmountTrans = $grvAmountTransaction + $directAmountTrans + $detailTaxSumTrans;
            } else if ($input['documentType'] == 3) {
                $grvAmountTransaction = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $id)
                    ->sum('netAmount');

                $grvAmountTransactionVAT = SupplierInvoiceDirectItem::selectRaw('SUM(VATAmount * noQty) as VATAmount')->where('bookingSuppMasInvAutoID', $id)
                ->first();


                $bookingAmountTrans = $grvAmountTransaction + (isset($grvAmountTransactionVAT->VATAmount) ? $grvAmountTransactionVAT->VATAmount : 0) + $directAmountTrans + $detailTaxSumTrans;
            } else {
                $bookingAmountTrans = $directAmountTrans + $detailTaxSumTrans;
            }

            if ($input['bookingAmountTrans'] != \Helper::roundValue($bookingAmountTrans)) {
                return $this->sendError('Cannot confirm. Supplier Invoice Master and Detail shows a difference in total.',500);
            }

            //check tax configuration if tax added
            if($detailTaxSumTrans > 0 ){
                if(empty(TaxService::getInputVATGLAccount($input["companySystemID"]))){
                    return $this->sendError('Cannot confirm. Input VAT GL Account not configured.', 500);
                }

                $inputVATGL = TaxService::getInputVATGLAccount($input["companySystemID"]);

                $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->inputVatGLAccountAutoID, $input["companySystemID"]);

                if (!$checkAssignedStatus) {
                    return $this->sendError('Cannot confirm. Input VAT GL Account not assigned to company.', 500);
                }

                //if rcm activated
                if($input['documentType'] == 1 && isset($input['rcmActivated']) && $input['rcmActivated']){
                    if(empty(TaxService::getInputVATTransferGLAccount($input["companySystemID"]))){
                        // return $this->sendError('Cannot confirm. Input VAT Transfer GL Account not configured.', 500);
                    }else if(empty(TaxService::getOutputVATGLAccount($input["companySystemID"]))){
                        return $this->sendError('Cannot confirm. Output VAT GL Account not configured.', 500);

                    }else  if(empty(TaxService::getOutputVATTransferGLAccount($input["companySystemID"]))){
                        // return $this->sendError('Cannot confirm. Output VAT Transfer GL Account not configured.', 500);
                    }
                    
                    $inputVATGL = TaxService::getOutputVATGLAccount($input["companySystemID"]);

                    $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->outputVatGLAccountAutoID, $input["companySystemID"]);

                    if (!$checkAssignedStatus) {
                        return $this->sendError('Cannot confirm. Output VAT GL Account not assigned to company.', 500);
                    }
                }
            }

            if($input['documentType'] == 0 || $input['documentType'] == 2 || $input['documentType'] == 3){
                $vatTotal = ($input['documentType'] == 0 || $input['documentType'] == 2) ? BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)->sum('VATAmount') : SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $id)->sum('VATAmount');
                if($vatTotal > 0){
                    if(empty(TaxService::getInputVATGLAccount($input["companySystemID"]))){
                        return $this->sendError('Cannot confirm. Input VAT GL Account not configured.', 500);
                    }else if( empty(TaxService::getInputVATTransferGLAccount($input["companySystemID"]))){
                        return $this->sendError('Cannot confirm. Input VAT Transfer GL Account not configured.', 500);
                    }

                    $inputVATGL = TaxService::getInputVATGLAccount($input["companySystemID"]);

                    $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->inputVatGLAccountAutoID, $input["companySystemID"]);

                    if (!$checkAssignedStatus) {
                        return $this->sendError('Cannot confirm. Input VAT GL Account not assigned to company.', 500);
                    }

                    $inputVATGL = TaxService::getInputVATTransferGLAccount($input["companySystemID"]);

                    $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->inputVatTransferGLAccountAutoID, $input["companySystemID"]);

                    if (!$checkAssignedStatus) {
                        return $this->sendError('Cannot confirm. Input VAT Transfer GL Account not assigned to company.', 500);
                    }

                    if (TaxService::isSupplierInvoiceRcmActivated($id)) {
                        if(empty(TaxService::getOutputVATGLAccount($input["companySystemID"]))){
                            return $this->sendError('Cannot confirm. Output VAT GL Account not configured.', 500);
                        }else  if(empty(TaxService::getOutputVATTransferGLAccount($input["companySystemID"]))){
                            return $this->sendError('Cannot confirm. Output VAT Transfer GL Account not configured.', 500);
                        }

                        $inputVATGL = TaxService::getOutputVATGLAccount($input["companySystemID"]);

                        $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->outputVatGLAccountAutoID, $input["companySystemID"]);

                        if (!$checkAssignedStatus) {
                            return $this->sendError('Cannot confirm. Output VAT GL Account not assigned to company.', 500);
                        }

                        $inputVATGL = TaxService::getOutputVATTransferGLAccount($input["companySystemID"]);

                        $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->outputVatTransferGLAccountAutoID, $input["companySystemID"]);

                        if (!$checkAssignedStatus) {
                            return $this->sendError('Cannot confirm. Output VAT Transfer GL Account not assigned to company.', 500);
                        }
                    }
                }
            }

            if ($input['documentType'] == 0 || $input['documentType'] == 2) {
                $this->supplierInvoiceItemDetailRepository->updateSupplierInvoiceItemDetail($id);
            }

            if ($input['documentType'] == 1 || $input['documentType'] == 3 || $input['documentType'] == 4) {
                $object = new ChartOfAccountValidationService();
                $result = $object->checkChartOfAccountStatus($input["documentSystemID"], $id, $input["companySystemID"]);

                if (isset($result) && !empty($result["accountCodes"])) {
                    return $this->sendError($result["errorMsg"]);
                }
            }

            $params = array(
                'autoID' => $id,
                'company' => $input["companySystemID"],
                'document' => $input["documentSystemID"],
                'segment' => 0,
                'category' => 0,
                'amount' => $input['bookingAmountTrans']
            );
            $confirm = \Helper::confirmDocument($params);

            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"]);
            }

        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

//        $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
//            ->where('companyPolicyCategoryID', 67)
//            ->where('isYesNO', 1)
//            ->first();
//        $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;
//
//        if($BookInvSuppMaster->documentType == 1 && $policy == true){
//            $input['localCurrencyER' ]    = $BookInvSuppMaster->localCurrencyER;
//            $input['comRptCurrencyER']    = $BookInvSuppMaster->companyReportingER;
//        }
//        if($BookInvSuppMaster->documentType != 1 || $policy == false){
//            $input['localCurrencyER' ]    = $companyCurrencyConversion['trasToLocER'];
//            $input['comRptCurrencyER']    = $companyCurrencyConversion['trasToRptER'];
//        }

        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->update($input, $id);

        SupplierInvoice::updateMaster($id);

        return $this->sendResponse($bookInvSuppMaster->toArray(), 'Supplier Invoice updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bookInvSuppMasters/{id}",
     *      summary="Remove the specified BookInvSuppMaster from storage",
     *      tags={"BookInvSuppMaster"},
     *      description="Delete BookInvSuppMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppMaster",
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
    public function updateCurrency($id, UpdateBookInvSuppMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'financeperiod_by', 'financeyear_by', 'supplier',
            'confirmedByEmpID', 'confirmedDate', 'company', 'confirmed_by', 'confirmedByEmpSystemID','transactioncurrency','direct_customer_invoice','employee']);
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        /** @var BookInvSuppMaster $bookInvSuppMaster */
        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->findWithoutFail($id);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }

        if ($input['supplierID'] != $bookInvSuppMaster->supplierID) {
            $input['isLocalSupplier'] = Helper::isLocalSupplier($input['supplierID'], $input['companySystemID']);
        }

        $customValidation = CustomValidation::validation(11,$bookInvSuppMaster,2,$input);
        if (!$customValidation["success"]) {
            return $this->sendError($customValidation["message"],500, array('type' => 'already_confirmed'));
        }

        $documentCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($bookInvSuppMaster->supplierTransactionCurrencyID);

        $alreadyAdded = BookInvSuppMaster::where('supplierInvoiceNo', $input['supplierInvoiceNo'])
            ->where('supplierID', $input['supplierID'])
            ->where('bookingSuppMasInvAutoID', '<>', $id)
            ->first();

        if ($alreadyAdded) {
            return $this->sendError("Entered supplier invoice number was already used ($alreadyAdded->bookingInvCode). Please check again", 500);
        }

        if(isset($input['custInvoiceDirectAutoID'])){
            $alreadyUsed = BookInvSuppMaster::where('custInvoiceDirectAutoID', $input['custInvoiceDirectAutoID'])
                ->where('bookingSuppMasInvAutoID', '<>', $id)
                ->first();

            if ($alreadyUsed) {
                return $this->sendError("Entered customer invoice number was already used in ($alreadyUsed->bookingInvCode). Please check again", 500);
            }
        }

        $supplierAssignedDetail = SupplierAssigned::select('liabilityAccountSysemID', 'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount','VATPercentage')
            ->where('supplierCodeSytem', $input['supplierID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($supplierAssignedDetail) {
            $input['supplierGLCodeSystemID'] = $supplierAssignedDetail->liabilityAccountSysemID;
            $input['supplierGLCode'] = $supplierAssignedDetail->liabilityAccount;
            $input['UnbilledGRVAccountSystemID'] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
            $input['UnbilledGRVAccount'] = $supplierAssignedDetail->UnbilledGRVAccount;
            if ($input['supplierID'] != $bookInvSuppMaster->supplierID) {
                $input['VATPercentage'] = $supplierAssignedDetail->VATPercentage;
            }
        }

        if (isset($input['bookingDate']) && $input['bookingDate']) {
            $input['bookingDate'] = new Carbon($input['bookingDate']);
        }

        if (isset($input['supplierInvoiceDate']) && $input['supplierInvoiceDate']) {
            $input['supplierInvoiceDate'] = new Carbon($input['supplierInvoiceDate']);
        }

        // calculating header total
        $directAmountTrans = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
            ->sum('DIAmount');

        $directAmountLocal = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
            ->sum('localAmount');

        $directAmountReport = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
            ->sum('comRptAmount');

        $detailTaxSumTrans = Taxdetail::where('documentSystemCode', $bookInvSuppMaster->bookingSuppMasInvAutoID)
            ->where('documentSystemID', 11)
            ->sum('amount');

        $detailTaxSumLocal = Taxdetail::where('documentSystemCode', $bookInvSuppMaster->bookingSuppMasInvAutoID)
            ->where('documentSystemID', 11)
            ->sum('localAmount');

        $detailTaxSumReport = Taxdetail::where('documentSystemCode', $bookInvSuppMaster->bookingSuppMasInvAutoID)
            ->where('documentSystemID', 11)
            ->sum('rptAmount');

        $bookingAmountTrans = 0;
        $bookingAmountLocal = 0;
        $bookingAmountRpt = 0;
        if ($input['documentType'] == 0 || $input['documentType'] == 2) {
            $input['rcmActivated'] = 0;
            $grvAmountTransaction = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                ->sum('totTransactionAmount');
            $grvAmountLocal = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                ->sum('totLocalAmount');
            $grvAmountReport = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                ->sum('totRptAmount');

            $bookingAmountTrans = $grvAmountTransaction + $directAmountTrans + $detailTaxSumTrans;
            $bookingAmountLocal = $grvAmountLocal + $directAmountLocal + $detailTaxSumLocal;
            $bookingAmountRpt = $grvAmountReport + $directAmountReport + $detailTaxSumReport;

            $input['bookingAmountTrans'] = \Helper::roundValue($bookingAmountTrans);
            $input['bookingAmountLocal'] = \Helper::roundValue($bookingAmountLocal);
            $input['bookingAmountRpt'] = \Helper::roundValue($bookingAmountRpt);

        } else {

            $bookingAmountTrans = $directAmountTrans + $detailTaxSumTrans;
            $bookingAmountLocal = $directAmountLocal + $detailTaxSumLocal;
            $bookingAmountRpt = $directAmountReport + $detailTaxSumReport;

            $input['bookingAmountTrans'] = \Helper::roundValue($bookingAmountTrans);
            $input['bookingAmountLocal'] = \Helper::roundValue($bookingAmountLocal);
            $input['bookingAmountRpt'] = \Helper::roundValue($bookingAmountRpt);
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

        $documentDate = $input['bookingDate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Document date is not within the selected financial period !', 500);
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], 0);


            if ($companyCurrencyConversion) {
                $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
            }


        if ($bookInvSuppMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {


            $validator = \Validator::make($input, [
                'companyFinancePeriodID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'bookingDate' => 'required',
                'supplierInvoiceDate' => 'required',
                'supplierInvoiceNo' => 'required',
                'supplierID' => 'required|numeric|min:1',
                'supplierTransactionCurrencyID' => 'required|numeric|min:1',
                'comments' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            /*
            * GWL-713
             * documentType == 0  -   invoice type - PO
            *  check policy 11 - Allow multiple GRV in One Invoice
            * if policy 11 is 1 allow to add multiple different PO's
            * if policy 11 is 0 do not allow multiple different PO's
             */
            if($input['documentType'] == 0){

                $policy = CompanyPolicyMaster::where('companyPolicyCategoryID', 11)
                    ->where('companySystemID', $bookInvSuppMaster->companySystemID)
                    ->first();

                if(empty($policy) || (!empty($policy) && !$policy->isYesNO)) {

                    $details = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)->get();
                    if(count($details)){

                        $poIdArray = $details->pluck('purchaseOrderID')->toArray();
                        if (count(array_unique($poIdArray)) > 1) {
                            return $this->sendError('Multiple PO\'s cannot be added. Different PO found on saved details.');
                        }
                    }

                }
            }

            $checkItems = 0;
            if ($input['documentType'] == 1) {
                $checkItems = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
                    ->count();
                if ($checkItems == 0) {
                    return $this->sendError('Every Supplier Invoice should have at least one item', 500);
                }
            }

            if ($checkItems > 0) {
                $checkQuantity = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
                    ->where(function ($q) {
                        $q->where('DIAmount', '<=', 0)
                            ->orWhereNull('localAmount', '<=', 0)
                            ->orWhereNull('comRptAmount', '<=', 0)
                            ->orWhereNull('DIAmount')
                            ->orWhereNull('localAmount')
                            ->orWhereNull('comRptAmount');
                    })
                    ->count();
                if ($checkQuantity > 0) {
                    return $this->sendError('Amount should be greater than 0 for every items', 500);
                }
            }

            if ($input['documentType'] == 0 || $input['documentType'] == 2) {

                $checkGRVItems = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                    ->count();
                if ($checkGRVItems == 0) {
                    return $this->sendError('Every Supplier Invoice should have at least one item', 500);
                }

                $checkGRVQuantity = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                    ->where(function ($q) {
                        $q->where('supplierInvoAmount', '<=', 0)
                            ->orWhereNull('totLocalAmount', '<=', 0)
                            ->orWhereNull('totRptAmount', '<=', 0)
                            ->orWhereNull('totTransactionAmount')
                            ->orWhereNull('totLocalAmount')
                            ->orWhereNull('totRptAmount');
                    })
                    ->whereHas('unbilled_grv', function($query){
                        $query->whereNull('purhaseReturnAutoID');
                    })
                    ->count();
                if ($checkGRVQuantity > 0) {
                    return $this->sendError('Amount should be greater than 0 for every items', 500);
                }

                //updating unbilled grv table all flags
                $getBookglDetailUnbilled = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                    ->get();
                if ($getBookglDetailUnbilled) {
                    foreach ($getBookglDetailUnbilled as $row) {

                        $unbilledSumData = UnbilledGrvGroupBy::find($row['unbilledgrvAutoID']);

                        if (is_null($unbilledSumData->purhaseReturnAutoID)) {
                            $getTotal = BookInvSuppDet::where('unbilledgrvAutoID', $row['unbilledgrvAutoID'])
                                ->sum('totTransactionAmount');

                            if ((round($unbilledSumData->totTransactionAmount, $documentCurrencyDecimalPlace) == round($getTotal, $documentCurrencyDecimalPlace)) || ($getTotal > $unbilledSumData->totTransactionAmount)) {

                                $unbilledSumData->selectedForBooking = -1;
                                $unbilledSumData->fullyBooked = 2;
                            } else {
                                $unbilledSumData->selectedForBooking = 0;
                                $unbilledSumData->fullyBooked = 1;
                            }
                            $unbilledSumData->save();
                        }
                    }
                }

            }

            //checking Supplier Invoice amount is greater than UnbilledGRV Amount validations
            if ($input['documentType'] == 0 || $input['documentType'] == 2) {
                $checktotalExceed = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                    ->with(['grvmaster'])
                    ->get();
                if ($checktotalExceed) {
                    foreach ($checktotalExceed as $exc) {

                        $unbilledGRTotal = UnbilledGrvGroupBy::where('grvAutoID', $exc->grvAutoID)
                            ->where('supplierID', $exc->supplierID)
                            ->sum('totTransactionAmount');

                        $checkPreTotal = BookInvSuppDet::where('grvAutoID', $exc->grvAutoID)
                            ->where('supplierID', $exc->supplierID)
                            ->sum('totTransactionAmount');

                        if (round($checkPreTotal, $documentCurrencyDecimalPlace) > round($unbilledGRTotal, $documentCurrencyDecimalPlace)) {
                            return $this->sendError('Supplier Invoice amount is greater than Unbilled GRV amount. Total Invoice amount is '. round($checkPreTotal, $documentCurrencyDecimalPlace) .'and Total Unbilled GRV amount is '. round($unbilledGRTotal, $documentCurrencyDecimalPlace) , 500);
                        }
                    }
                }
            }

            //checking Supplier Invoice amount is greater than GRV Amount validations
            if ($input['documentType'] == 0 || $input['documentType'] == 2) {
                $checktotalExceed = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                    ->with(['grvmaster'])
                    ->get();
                if ($checktotalExceed) {
                    $company = Company::where('companySystemID', $input['companySystemID'])->first();
                    $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $input['supplierID'])
                        ->where('companySystemID', $input['companySystemID'])
                        ->first();
                    $valEligible = false;
                    $rcmActivate = TaxService::isSupplierInvoiceRcmActivated($id);
                    if (($company->vatRegisteredYN == 1  || $supplierAssignedDetail->vatEligible == 1) && !$rcmActivate) {
                        $valEligible = true;
                    }
                    foreach ($checktotalExceed as $exc) {
                        $grvDetailSum = GRVDetails::select(DB::raw('COALESCE(SUM(landingCost_TransCur * noQty),0) as total, SUM(VATAmount*noQty) as transVATAmount'))
                            ->where('grvAutoID', $exc->grvAutoID)
                            ->first();

                        $logisticVATTotal = PoAdvancePayment::where('grvAutoID', $exc->grvAutoID)
                            ->sum('VATAmount');


                        $checkPreTotal = BookInvSuppDet::where('grvAutoID', $exc->grvAutoID)
                            ->sum('totTransactionAmount');
                        if (!$valEligible) {
                            $grvDetailTotal = $grvDetailSum['total'];
                        } else {
                            $grvDetailTotal = $grvDetailSum['total'] + $grvDetailSum['transVATAmount'] + $logisticVATTotal;
                        }
                        if (round($checkPreTotal, $documentCurrencyDecimalPlace) > round($grvDetailTotal, $documentCurrencyDecimalPlace)) {
                            return $this->sendError('Supplier Invoice amount is greater than GRV amount. Total Invoice amount is '.round($checkPreTotal, $documentCurrencyDecimalPlace). ' And Total GRV amount is '. round($grvDetailTotal, $documentCurrencyDecimalPlace), 500);
                        }
                    }
                }
            }

            if ($input['documentType'] == 0) {
                //updating PO Master invoicedBooked flag
                $getPoRecords = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                    ->groupBy('purchaseOrderID')
                    ->get();

                if ($getPoRecords) {
                    foreach ($getPoRecords as $row) {

                        $poMasterTableTotal = ProcumentOrder::find($row['purchaseOrderID']);

                        $getTotal = BookInvSuppDet::where('purchaseOrderID', $row['purchaseOrderID'])
                            ->sum('totTransactionAmount');

                        if (round($poMasterTableTotal->poTotalSupplierTransactionCurrency, $documentCurrencyDecimalPlace) == round($getTotal, $documentCurrencyDecimalPlace)) {
                            $poMasterTableTotal->invoicedBooked = 2;
                        } else if (round($poMasterTableTotal->poTotalSupplierTransactionCurrency, $documentCurrencyDecimalPlace) <= round($getTotal, $documentCurrencyDecimalPlace)) {
                            $poMasterTableTotal->invoicedBooked = 2;
                        } else if ($getTotal != 0) {
                            $poMasterTableTotal->invoicedBooked = 1;
                        } else if ($getTotal == 0) {
                            $poMasterTableTotal->invoicedBooked = 0;
                        }
                        $poMasterTableTotal->save();
                    }
                }
            }

            $directInvoiceDetails = DirectInvoiceDetails::where('directInvoiceAutoID', $id)->get();

            $finalError = array('amount_zero' => array(),
                'amount_neg' => array(),
                'required_serviceLine' => array(),
                'active_serviceLine' => array(),
                'cannot_add_revenue' => array()
            );
            $error_count = 0;

            //Supplier Direct Invoice
            if ($input['documentType'] == 1) {

                foreach ($directInvoiceDetails as $item) {

                    $chartOfAccount = ChartOfAccountsAssigned::select('controlAccountsSystemID')->where('chartOfAccountSystemID', $item->chartOfAccountSystemID)->first();

                    if ($chartOfAccount->controlAccountsSystemID == 1) {
                        array_push($finalError['cannot_add_revenue'], $item->glCode);
                        $error_count++;
                    }
                }
            }

    

            foreach ($directInvoiceDetails as $item) {
                $updateItem = DirectInvoiceDetails::find($item['directInvoiceDetailsID']);

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

                $companyCurrencyConversion = \Helper::currencyConversion($updateItem->companySystemID, $updateItem->DIAmountCurrency, $updateItem->DIAmountCurrency, $updateItem->DIAmount);

                if (isset($policy->isYesNO) && $policy->isYesNO != 1) {
                    $input['localAmount'] = $companyCurrencyConversion['localAmount'];
                    $input['comRptAmount'] = $companyCurrencyConversion['reportingAmount'];
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $input['comRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                }

                $updateItem->save();

                if ($updateItem->DIAmount == 0 || $updateItem->localAmount == 0 || $updateItem->comRptAmount == 0) {
                    array_push($finalError['amount_zero'], $updateItem->serviceLineCode);
                    $error_count++;
                }
                if ($updateItem->DIAmount < 0 || $updateItem->localAmount < 0 || $updateItem->comRptAmount < 0) {
                    array_push($finalError['amount_neg'], $updateItem->serviceLineCode);
                    $error_count++;
                }
            }


            $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
            }

            $input['RollLevForApp_curr'] = 1;

            unset($input['confirmedYN']);
            unset($input['confirmedByEmpSystemID']);
            unset($input['confirmedByEmpID']);
            unset($input['confirmedByName']);
            unset($input['confirmedDate']);

            if ($input['documentType'] == 0 || $input['documentType'] == 2) {
                $grvAmountTransaction = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                    ->sum('totTransactionAmount');
                $bookingAmountTrans = $grvAmountTransaction + $directAmountTrans + $detailTaxSumTrans;
            } else {
                $bookingAmountTrans = $directAmountTrans + $detailTaxSumTrans;
            }

            if ($input['bookingAmountTrans'] != \Helper::roundValue($bookingAmountTrans)) {
                return $this->sendError('Cannot confirm. Supplier Invoice Master and Detail shows a difference in total.',500);
            }

            //check tax configuration if tax added
            if($detailTaxSumTrans > 0 ){
                if(empty(TaxService::getInputVATGLAccount($input["companySystemID"]))){
                    return $this->sendError('Cannot confirm. Input VAT GL Account not configured.', 500);
                }

                $inputVATGL = TaxService::getInputVATGLAccount($input["companySystemID"]);

                $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->inputVatGLAccountAutoID, $input["companySystemID"]);

                if (!$checkAssignedStatus) {
                    return $this->sendError('Cannot confirm. Input VAT GL Account not assigned to company.', 500);
                }

                //if rcm activated
                if($input['documentType'] == 1 && isset($input['rcmActivated']) && $input['rcmActivated']){
                    if(empty(TaxService::getInputVATTransferGLAccount($input["companySystemID"]))){
                        // return $this->sendError('Cannot confirm. Input VAT Transfer GL Account not configured.', 500);
                    }else if(empty(TaxService::getOutputVATGLAccount($input["companySystemID"]))){
                        return $this->sendError('Cannot confirm. Output VAT GL Account not configured.', 500);

                    }else  if(empty(TaxService::getOutputVATTransferGLAccount($input["companySystemID"]))){
                        // return $this->sendError('Cannot confirm. Output VAT Transfer GL Account not configured.', 500);
                    }

                    $inputVATGL = TaxService::getOutputVATGLAccount($input["companySystemID"]);

                    $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->outputVatGLAccountAutoID, $input["companySystemID"]);

                    if (!$checkAssignedStatus) {
                        return $this->sendError('Cannot confirm. Output VAT GL Account not assigned to company.', 500);
                    }
                }
            }

            if($input['documentType'] == 0 || $input['documentType'] == 2){
                $vatTotal = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
                    ->sum('VATAmount');
                if($vatTotal > 0){
                    if(empty(TaxService::getInputVATGLAccount($input["companySystemID"]))){
                        return $this->sendError('Cannot confirm. Input VAT GL Account not configured.', 500);
                    }else if( empty(TaxService::getInputVATTransferGLAccount($input["companySystemID"]))){
                        return $this->sendError('Cannot confirm. Input VAT Transfer GL Account not configured.', 500);
                    }

                    $inputVATGL = TaxService::getInputVATGLAccount($input["companySystemID"]);

                    $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->inputVatGLAccountAutoID, $input["companySystemID"]);

                    if (!$checkAssignedStatus) {
                        return $this->sendError('Cannot confirm. Input VAT GL Account not assigned to company.', 500);
                    }

                    $inputVATGL = TaxService::getInputVATTransferGLAccount($input["companySystemID"]);

                    $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->inputVatTransferGLAccountAutoID, $input["companySystemID"]);

                    if (!$checkAssignedStatus) {
                        return $this->sendError('Cannot confirm. Input VAT Transfer GL Account not assigned to company.', 500);
                    }

                    if (TaxService::isSupplierInvoiceRcmActivated($id)) {
                        if(empty(TaxService::getOutputVATGLAccount($input["companySystemID"]))){
                            return $this->sendError('Cannot confirm. Output VAT GL Account not configured.', 500);
                        }else  if(empty(TaxService::getOutputVATTransferGLAccount($input["companySystemID"]))){
                            return $this->sendError('Cannot confirm. Output VAT Transfer GL Account not configured.', 500);
                        }

                        $inputVATGL = TaxService::getOutputVATGLAccount($input["companySystemID"]);

                        $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->outputVatGLAccountAutoID, $input["companySystemID"]);

                        if (!$checkAssignedStatus) {
                            return $this->sendError('Cannot confirm. Output VAT GL Account not assigned to company.', 500);
                        }

                        $inputVATGL = TaxService::getOutputVATTransferGLAccount($input["companySystemID"]);

                        $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->outputVatTransferGLAccountAutoID, $input["companySystemID"]);

                        if (!$checkAssignedStatus) {
                            return $this->sendError('Cannot confirm. Output VAT Transfer GL Account not assigned to company.', 500);
                        }
                    }
                }
            }

            if ($input['documentType'] == 0 || $input['documentType'] == 2) {
                $this->supplierInvoiceItemDetailRepository->updateSupplierInvoiceItemDetail($id);
            }

            $params = array(
                'autoID' => $id,
                'company' => $input["companySystemID"],
                'document' => $input["documentSystemID"],
                'segment' => 0,
                'category' => 0,
                'amount' => $input['bookingAmountTrans']
            );
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"]);
            }

        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

//        $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
//            ->where('companyPolicyCategoryID', 67)
//            ->where('isYesNO', 1)
//            ->first();
//        $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;
//
//        if($BookInvSuppMaster->documentType == 1 && $policy == true){
//            $input['localCurrencyER' ]    = $BookInvSuppMaster->localCurrencyER;
//            $input['comRptCurrencyER']    = $BookInvSuppMaster->companyReportingER;
//        }
//        if($BookInvSuppMaster->documentType != 1 || $policy == false){
//            $input['localCurrencyER' ]    = $companyCurrencyConversion['trasToLocER'];
//            $input['comRptCurrencyER']    = $companyCurrencyConversion['trasToRptER'];
//        }


        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->update($input, $id);

        SupplierInvoice::updateMaster($id);

        return $this->sendResponse($bookInvSuppMaster->toArray(), 'Supplier Invoice updated successfully');
    }


    public function destroy($id)
    {
        /** @var BookInvSuppMaster $bookInvSuppMaster */
        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->findWithoutFail($id);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice Master not found');
        }

        $confirm = CustomValidation::validation(11,$bookInvSuppMaster,2,[]);
        if (!$confirm["success"]) {
            return $this->sendError($confirm["message"],500);
        }

        $bookInvSuppMaster->delete();

        return $this->sendResponse($id, 'Supplier Invoice Master deleted successfully');
    }

    public function updateLocalER($id,Request $request){

        $value = $request->data;
        $companyId = $request->companyId;
        $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();

        if (isset($policy->isYesNO) && $policy->isYesNO == 1) {

        $details = DirectInvoiceDetails::where('directInvoiceAutoID',$id)->get();

        $masterINVID = BookInvSuppMaster::findOrFail($id);
            $masterVATAmountLocal = \Helper::roundValue($masterINVID->VATAmount / $value);
            $masterNetAmountLocal = \Helper::roundValue($masterINVID->netAmount / $value);
            $bookingAmountLocal = \Helper::roundValue($masterINVID->bookingAmountTrans/$value);


            $masterInvoiceArray = array('localCurrencyER'=>$value, 'VATAmountLocal'=>$masterVATAmountLocal, 'netAmountLocal'=>$masterNetAmountLocal, 'bookingAmountLocal'=>$bookingAmountLocal);
        $masterINVID->update($masterInvoiceArray);

        foreach($details as $item){
            $localAmount = \Helper::roundValue($item->DIAmount / $value);
            $VATAmountLocal = \Helper::roundValue($item->VATAmount / $value);
            $netAmountLocal = \Helper::roundValue($item->netAmount / $value);

            $directInvoiceDetailsArray = array('localCurrencyER'=>$value, 'localAmount'=>$localAmount,'VATAmountLocal'=>$VATAmountLocal, 'netAmountLocal'=>$netAmountLocal);
            $updatedLocalER = DirectInvoiceDetails::findOrFail($item->directInvoiceDetailsID);
            $updatedLocalER->update($directInvoiceDetailsArray);
        }

        return $this->sendResponse([$id,$value], 'Update Local ER');
        }
        else{
            return $this->sendError('Policy not enabled', 400);
        }
    }

    public function updateReportingER($id,Request $request){
        $value = $request->data;
        $companyId = $request->companyId;
        $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();

        if (isset($policy->isYesNO) && $policy->isYesNO == 1) {

        $masterINVID = BookInvSuppMaster::findOrFail($id);
            $masterVATAmountRpt = \Helper::roundValue($masterINVID->VATAmount / $value);
            $masterNetAmountRpt = \Helper::roundValue($masterINVID->netAmount / $value);
            $bookingAmountRpt = \Helper::roundValue($masterINVID->bookingAmountTrans/$value);

            $masterInvoiceArray = array('companyReportingER'=>$value, 'VATAmountRpt'=>$masterVATAmountRpt, 'netAmountRpt'=>$masterNetAmountRpt, 'bookingAmountRpt'=>$bookingAmountRpt);
        $masterINVID->update($masterInvoiceArray);

        $details = DirectInvoiceDetails::where('directInvoiceAutoID',$id)->get();

        foreach($details as $item){
            $reportingAmount = \Helper::roundValue($item->DIAmount / $value);
            $itemVATAmountRpt = \Helper::roundValue($item->VATAmount / $value);
            $itemNetAmountRpt = \Helper::roundValue($item->netAmount / $value);
            $directInvoiceDetailsArray = array('comRptCurrencyER'=>$value, 'comRptAmount'=>$reportingAmount, 'VATAmountRpt'=>$itemVATAmountRpt, 'netAmountRpt'=>$itemNetAmountRpt);
            $updatedLocalER = DirectInvoiceDetails::findOrFail($item->directInvoiceDetailsID);
            $updatedLocalER->update($directInvoiceDetailsArray);
        }

        return $this->sendResponse($id, 'Update Reporting ER');
        }
        else{
            return $this->sendError('Policy not enabled', 400);
        }
    }

    public function getInvoiceMasterRecord(Request $request)
    {
        $input = $request->all();

        $output = BookInvSuppMaster::where('bookingSuppMasInvAutoID', $input['bookingSuppMasInvAutoID'])->with(['grvdetail' => function ($query) {
            $query->with('grvmaster');
        }, 'directdetail' => function ($query) {
            $query->with('project','segment');
        }, 'detail' => function ($query) {
            $query->with('grvmaster');
        }, 'item_details' => function ($query) {
            $query->with('unit');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 11);
        }, 'project','company', 'transactioncurrency', 'localcurrency', 'rptcurrency', 'supplier', 'suppliergrv', 'confirmed_by', 'created_by', 'modified_by', 'cancelled_by','audit_trial.modified_by', 'employee'])->first();

        $isProjectBase = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
        ->where('companySystemID', $output->companySystemID)
        ->where('isYesNO', 1)
        ->exists();

        $output['isProjectBase'] = $isProjectBase;

        return $this->sendResponse($output, 'Data retrieved successfully');
    }

    public function getInvoiceMasterFormData(Request $request)
    {
        $companyId = $request['companyId'];
        
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $taxMaster = DB::select('SELECT * FROM erp_taxmaster WHERE taxType = 2 AND companySystemID = ' . $companyId . '');

        $years = BookInvSuppMaster::select(DB::raw("YEAR(createdDateAndTime) as year"))
            ->whereNotNull('createdDateAndTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $supplier = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName"))
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"));
        $companyFinanceYear = $companyFinanceYear->where('companySystemID', $companyId);
        if (isset($request['type']) && ($request['type'] == 'add' || $request['type'] == 'edit')) {
            $companyFinanceYear = $companyFinanceYear->where('isActive', -1);
        }
        $companyFinanceYear = $companyFinanceYear->get();

        $segments = SegmentMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $segments = $segments->where('isActive', 1);
        }
        $segments = $segments->get();

        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $wareHouseLocation = $wareHouseLocation->get();

        $isVATEligible = TaxService::checkCompanyVATEligible($companyId);

        $assetAllocatePolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 61)
                                    ->where('companySystemID', $companyId)
                                    ->first();

        $directGRV = CompanyPolicyMaster::where('companyPolicyCategoryID', 30)
                                    ->where('companySystemID', $companyId)
                                    ->first();

        $employeeInvoice = CompanyPolicyMaster::where('companyPolicyCategoryID', 68)
                                    ->where('companySystemID', $companyId)
                                    ->first();

        $employeeAllocate = CompanyPolicyMaster::where('companyPolicyCategoryID', 90)
                            ->where('companySystemID', $companyId)
                            ->first();                            

        $employeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($companyId, null, "employee-control-account");

        $companyData = Company::find($companyId);


        $monthly_declarations_drop = MonthlyDeclarationsTypes::selectRaw("monthlyDeclarationID, monthlyDeclaration")
                    ->where('companyID', $companyId)->where('monthlyDeclarationType', 'D')->where('isPayrollCategory', 1)
                    ->get();

        $isProject_base = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
        ->where('companySystemID', $companyId)
        ->where('isYesNO', 1)
        ->exists();

        $projects = [];
        $projects = ErpProjectMaster::where('companySystemID', $companyId)
                                        ->get();
        $whtTypes = Tax::where('companySystemID',$companyId)->where('taxCategory',3)->where('isActive',1)->get();

  

        $output = array('yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'tax' => $taxMaster,
            'assetAllocatePolicy' => ($assetAllocatePolicy && $assetAllocatePolicy->isYesNO == 1) ? true : false,
            'directGRVPolicy' => ($directGRV && $directGRV->isYesNO == 1) ? true : false,
            'employeeInvoicePolicy' => ($employeeInvoice && $employeeInvoice->isYesNO == 1) ? true : false,
            'currencies' => $currencies,
            'financialYears' => $financialYears,
            'isHrmsIntergrated' => ($companyData) ? $companyData->isHrmsIntergrated : false,
            'wareHouseLocation' => $wareHouseLocation,
            'deduction_type_drop' => $monthly_declarations_drop,
            'suppliers' => $supplier,
            'companyFinanceYear' => $companyFinanceYear,
            'employeeControlAccount' => $employeeControlAccount,
            'segments' => $segments,
            'isVATEligible' => $isVATEligible,
            'isProjectBase' => $isProject_base,
            'projects' => $projects,
            'employeeAllocatePolicy' => ($employeeAllocate && $employeeAllocate->isYesNO == 1) ? true : false,
            'whtTypes' => $whtTypes
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getInvoiceSupplierTypeBase(Request $request)
    {
        $companyId = $request['companyId'];

        $supplierData = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName"));
        $supplierData = $supplierData->where('companySystemID', $companyId);
        if (isset($request['invoiceType']) && $request['invoiceType'] == 1) {
            $supplierData = $supplierData->where('isActive', 1);
        }
        $supplierData = $supplierData->where('isAssigned', -1);
        $supplierData = $supplierData->get();

        $employeeData = [];
        $currencies = [];
        if (isset($request['invoiceType']) && $request['invoiceType'] == 4) {
            $employeeData = Employee::selectRaw('empID, empName, employeeSystemID')->where('discharegedYN','<>', 2);
            if(Helper::checkHrmsIntergrated($companyId)){
                $employeeData = $employeeData->whereHas('hr_emp', function($q){
                    $q->where('isDischarged', 0)->where('empConfirmedYN', 1);
                });
            }
            $employeeData = $employeeData->get();

            $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
                                        ->get();
        }

        return $this->sendResponse(['supplierData' => $supplierData, 'employeeData' => $employeeData, 'currencies' => $currencies], 'Record retrieved successfully');
    }


    public function getInvoiceMasterView(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('cancelYN', 'confirmedYN', 'approved', 'month', 'year', 'supplierID', 'documentType', 'projectID'));
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
        
        $invMaster = $this->bookInvSuppMasterRepository->bookInvSuppListQuery($request, $input, $search, $supplierID, $projectID);

        return \DataTables::eloquent($invMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bookingSuppMasInvAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function supplierInvoiceReopen(Request $request)
    {
        $input = $request->all();

        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];

        $bookInvSuppMaster = BookInvSuppMaster::find($bookingSuppMasInvAutoID);
        $emails = array();
        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }

        if ($bookInvSuppMaster->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this Supplier Invoice it is already partially approved');
        }

        if ($bookInvSuppMaster->approved == -1) {
            return $this->sendError('You cannot reopen this Supplier Invoice it is already fully approved');
        }

        if ($bookInvSuppMaster->confirmedYN == 0) {
            return $this->sendError('You cannot reopen this Supplier Invoice, it is not confirmed');
        }

        // updating fields

        $bookInvSuppMaster->confirmedYN = 0;
        $bookInvSuppMaster->confirmedByEmpSystemID = null;
        $bookInvSuppMaster->confirmedByEmpID = null;
        $bookInvSuppMaster->confirmedByName = null;
        $bookInvSuppMaster->confirmedDate = null;
        $bookInvSuppMaster->RollLevForApp_curr = 1;
        $bookInvSuppMaster->save();

        // delete tax details
        $checkTaxExist = Taxdetail::where('documentSystemCode', $bookingSuppMasInvAutoID)
            ->where('companySystemID', $bookInvSuppMaster->companySystemID)
            ->where('documentSystemID', 11)
            ->first();

        if ($checkTaxExist) {
             Taxdetail::where('documentSystemCode', $bookingSuppMasInvAutoID)
                ->where('companySystemID', $bookInvSuppMaster->companySystemID)
                ->where('documentSystemID', 11)
                ->delete();
        }

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $bookInvSuppMaster->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $bookInvSuppMaster->bookingInvCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $bookInvSuppMaster->bookingInvCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $bookInvSuppMaster->companySystemID)
            ->where('documentSystemCode', $bookInvSuppMaster->bookingSuppMasInvAutoID)
            ->where('documentSystemID', $bookInvSuppMaster->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $bookInvSuppMaster->companySystemID)
                    ->where('documentSystemID', $bookInvSuppMaster->documentSystemID)
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


         DocumentApproved::where('documentSystemCode', $bookingSuppMasInvAutoID)
            ->where('companySystemID', $bookInvSuppMaster->companySystemID)
            ->where('documentSystemID', $bookInvSuppMaster->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($bookInvSuppMaster->documentSystemID,$bookingSuppMasInvAutoID,$input['reopenComments'],'Reopened','Pending Approval');

        return $this->sendResponse($bookInvSuppMaster->toArray(), 'Supplier Invoice reopened successfully');
    }

    public function getInvoiceMasterApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyID)
            ->where('documentSystemID', 11)
            ->first();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'employeesdepartments.approvalDeligated',
            'erp_bookinvsuppmaster.bookingSuppMasInvAutoID',
            'erp_bookinvsuppmaster.bookingInvCode',
            'erp_bookinvsuppmaster.documentSystemID',
            'erp_bookinvsuppmaster.secondaryRefNo',
            'erp_bookinvsuppmaster.bookingDate',
            'erp_bookinvsuppmaster.comments',
            'erp_bookinvsuppmaster.createdDateAndTime',
            'erp_bookinvsuppmaster.confirmedDate',
            'erp_bookinvsuppmaster.bookingAmountTrans',
            'erp_bookinvsuppmaster.documentType',
            'erp_bookinvsuppmaster.supplierInvoiceNo',
            'erp_bookinvsuppmaster.supplierInvoiceDate',
            'erp_bookinvsuppmaster.postedDate',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'suppliermaster.supplierName As supplierName',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user',
            'inv_emp.empName As employee_inv'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $serviceLinePolicy) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                $query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
            }
            $query->where('employeesdepartments.documentSystemID', 11)
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('erp_bookinvsuppmaster', function ($query) use ($companyID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'bookingSuppMasInvAutoID')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('erp_bookinvsuppmaster.companySystemID', $companyID)
                ->where('erp_bookinvsuppmaster.approved', 0)
                ->where('erp_bookinvsuppmaster.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'supplierTransactionCurrencyID', 'currencymaster.currencyID')
            ->leftJoin('suppliermaster', 'supplierID', 'suppliermaster.supplierCodeSystem')
            ->leftJoin('employees as inv_emp', 'erp_bookinvsuppmaster.employeeID', 'inv_emp.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 11)
            ->where('erp_documentapproved.companySystemID', $companyID)->groupBy('erp_bookinvsuppmaster.bookingSuppMasInvAutoID');

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('bookingInvCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $grvMasters = [];
        }

        return \DataTables::of($grvMasters)
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

    public function getApprovedInvoiceForCurrentUser(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'erp_bookinvsuppmaster.bookingSuppMasInvAutoID',
            'erp_bookinvsuppmaster.bookingInvCode',
            'erp_bookinvsuppmaster.documentSystemID',
            'erp_bookinvsuppmaster.secondaryRefNo',
            'erp_bookinvsuppmaster.bookingDate',
            'erp_bookinvsuppmaster.comments',
            'erp_bookinvsuppmaster.createdDateAndTime',
            'erp_bookinvsuppmaster.confirmedDate',
            'erp_bookinvsuppmaster.bookingAmountTrans',
            'erp_bookinvsuppmaster.documentType',
            'erp_bookinvsuppmaster.supplierInvoiceNo',
            'erp_bookinvsuppmaster.supplierInvoiceDate',
            'erp_bookinvsuppmaster.postedDate',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'suppliermaster.supplierName As supplierName',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('erp_bookinvsuppmaster', function ($query) use ($companyID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'bookingSuppMasInvAutoID')
                ->where('erp_bookinvsuppmaster.companySystemID', $companyID)
                ->where('erp_bookinvsuppmaster.approved', -1)
                ->where('erp_bookinvsuppmaster.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'supplierTransactionCurrencyID', 'currencymaster.currencyID')
            ->leftJoin('suppliermaster', 'supplierID', 'suppliermaster.supplierCodeSystem')
            ->where('erp_documentapproved.documentSystemID', 11)
            ->where('erp_documentapproved.companySystemID', $companyID)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('bookingInvCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($grvMasters)
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

    public function approveSupplierInvoice(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectSupplierInvoice(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    public function saveSupplierInvoiceTaxDetails(Request $request)
    {
        $input = $request->all();
        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];
        $percentage = $input['percentage'];

        $bookInvSuppMaster = BookInvSuppMaster::find($bookingSuppMasInvAutoID);
        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }

        if (!isset($input['taxMasterAutoID'])) {
            $input['taxMasterAutoID'] = 0;
            // return $this->sendError('Please Select a tax');
        }

        $taxMasterAutoID = $input['taxMasterAutoID'];

        if ($input['percentage'] == 0) {
            return $this->sendError('VAT percentage cannot be 0');
        }


        if ($bookInvSuppMaster->documentType == 0) {
            $invoiceDetail = BookInvSuppDet::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)->first();
            if (empty($invoiceDetail)) {
                return $this->sendResponse('e', 'Invoice details not found.');
            }
        }
        $decimal = \Helper::getCurrencyDecimalPlace($bookInvSuppMaster->supplierTransactionCurrencyID);
        if ($bookInvSuppMaster->documentType == 1) {
            $invoiceDetail = DirectInvoiceDetails::where('directInvoiceAutoID', $bookingSuppMasInvAutoID)->first();
            if (empty($invoiceDetail)) {
                return $this->sendResponse('e', 'Invoice Details not found.');
            }
        }

        $totalAmount = 0;
        $amount = DirectInvoiceDetails::where('directInvoiceAutoID', $bookingSuppMasInvAutoID)
            ->sum('DIAmount');

        if ($bookInvSuppMaster->documentType == 0) {
            $grvAmountTransaction = BookInvSuppDet::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                ->sum('totTransactionAmount');

            $totalAmount = $grvAmountTransaction + $amount;

        } else {
            $totalAmount = $amount;
        }

        $totalAmount = ($percentage / 100) * $totalAmount;

        /*$taxMaster = TaxMaster::where('taxType',2)
            ->where('companySystemID', $bookInvSuppMaster->companySystemID)
            ->first();

        if (empty($taxMaster)) {
            return $this->sendResponse('e', 'VAT Master not found');
        }*/

        $Taxdetail = Taxdetail::where('documentSystemCode', $bookingSuppMasInvAutoID)
            ->where('documentSystemID', 11)
            ->first();

        if (!empty($Taxdetail)) {
            return $this->sendResponse('e', 'VAT detail already exist');
        }

        $_post['taxMasterAutoID'] = $taxMasterAutoID;
        $_post['companySystemID'] = $bookInvSuppMaster->companySystemID;
        $_post['companyID'] = $bookInvSuppMaster->companyID;
        $_post['documentSystemID'] = 11;
        $_post['documentID'] = 'SI';
        $_post['documentSystemCode'] = $bookingSuppMasInvAutoID;
        $_post['documentCode'] = $bookInvSuppMaster->bookingInvCode;
        $_post['taxShortCode'] = ''; // $taxMaster->taxShortCode;
        $_post['taxDescription'] = ''; //$taxMaster->taxDescription;
        $_post['taxPercent'] = $percentage;
        $_post['payeeSystemCode'] = $bookInvSuppMaster->supplierID ; //$taxMaster->payeeSystemCode;
        $_post['currency'] = $bookInvSuppMaster->supplierTransactionCurrencyID;
        $_post['currencyER'] = $bookInvSuppMaster->supplierTransactionCurrencyER;
        $_post['amount'] = round($totalAmount, $decimal);
        $_post['payeeDefaultCurrencyID'] = $bookInvSuppMaster->supplierTransactionCurrencyID;
        $_post['payeeDefaultCurrencyER'] = $bookInvSuppMaster->supplierTransactionCurrencyER;
        $_post['payeeDefaultAmount'] = round($totalAmount, $decimal);
        $_post['localCurrencyID'] = $bookInvSuppMaster->localCurrencyID;
        $_post['localCurrencyER'] = $bookInvSuppMaster->localCurrencyER;
        $_post['rptCurrencyID'] = $bookInvSuppMaster->companyReportingCurrencyID;
        $_post['rptCurrencyER'] = $bookInvSuppMaster->companyReportingER;

        if ($_post['currency'] == $_post['rptCurrencyID']) {
            $MyRptAmount = $totalAmount;
        } else {
            if ($_post['rptCurrencyER'] > $_post['currencyER']) {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalAmount / $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalAmount * $_post['rptCurrencyER']);
                }
            } else {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalAmount * $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalAmount / $_post['rptCurrencyER']);
                }
            }
        }
        $_post["rptAmount"] = \Helper::roundValue($MyRptAmount);
        if ($_post['currency'] == $_post['localCurrencyID']) {
            $MyLocalAmount = $totalAmount;
        } else {
            if ($_post['localCurrencyER'] > $_post['currencyER']) {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalAmount / $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalAmount * $_post['localCurrencyER']);
                }
            } else {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalAmount * $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalAmount / $_post['localCurrencyER']);
                }
            }
        }
        $_post["localAmount"] = \Helper::roundValue($MyLocalAmount);

        DB::beginTransaction();
        try {
            Taxdetail::create($_post);
            DB::commit();
            return $this->sendResponse('s', 'Successfully Added');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('e', 'Error Occurred');
        }
    }

    public function supplierInvoiceTaxTotal(Request $request)
    {
        $input = $request->all();

        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];

        $detailTaxSum = Taxdetail::select(DB::raw('COALESCE(SUM(amount),0) as total'))
            ->where('documentSystemCode', $bookingSuppMasInvAutoID)
            ->where('documentSystemID', 11)
            ->first();

        return $this->sendResponse($detailTaxSum->toArray(), 'Data retrieved successfully');
    }


    public function printSupplierInvoice(Request $request)
    {
        $id = $request->get('bookingSuppMasInvAutoID');

        $bookInvSuppMaster = BookInvSuppMaster::find($id);
        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }

        $bookInvSuppMasterRecord = BookInvSuppMaster::where('bookingSuppMasInvAutoID', $id)->with(['grvdetail' => function ($query) {
            $query->with('grvmaster');
        }, 'directdetail' => function ($query) {
            $query->with('project','segment');
        }, 'detail' => function ($query) {
            $query->with('grvmaster');
        }, 'item_details' => function ($query) {
            $query->with('unit');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 11);
        }, 'project','company', 'transactioncurrency', 'localcurrency', 'rptcurrency', 'supplier', 'suppliergrv', 'confirmed_by', 'created_by', 'modified_by', 'cancelled_by', 'employee'])->first();

        if (empty($bookInvSuppMasterRecord)) {
            return $this->sendError('Supplier Invoice not found');
        }

        $refernaceDoc = \Helper::getCompanyDocRefNo($bookInvSuppMaster->companySystemID, $bookInvSuppMaster->documentSystemID);

        $transDecimal = 2;
        $localDecimal = 3;
        $rptDecimal = 2;

        if ($bookInvSuppMasterRecord->transactioncurrency) {
            $transDecimal = $bookInvSuppMasterRecord->transactioncurrency->DecimalPlaces;
        }

        if ($bookInvSuppMasterRecord->localcurrency) {
            $localDecimal = $bookInvSuppMasterRecord->localcurrency->DecimalPlaces;
        }

        if ($bookInvSuppMasterRecord->rptcurrency) {
            $rptDecimal = $bookInvSuppMasterRecord->rptcurrency->DecimalPlaces;
        }

        $directTotTra = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
            ->sum('DIAmount');

        $directTotVAT = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
            ->sum('VATAmount');

        $directTotNet = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
            ->sum('netAmount');

        $directTotLoc = DirectInvoiceDetails::where('directInvoiceAutoID', $id)
            ->sum('localAmount');

        $grvTotTra = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
            ->sum('totTransactionAmount');

        $grvTotLoc = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
            ->sum('totLocalAmount');

        $grvTotRpt = BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
            ->sum('totRptAmount');

        $isVATEligible = TaxService::checkCompanyVATEligible($bookInvSuppMaster->companySystemID);


        $directItemNetTotalLocal = 0;
        $directItemNetTotalTrans = 0;

        if ($bookInvSuppMasterRecord->documentType == 3) {
            $grvTotTra = SupplierInvoiceDirectItem::selectRaw('SUM(netAmount + (VATAmount * noQty)) as total')->where('bookingSuppMasInvAutoID', $id)->first()->total;
        }

        $isProjectBase = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
        ->where('companySystemID', $bookInvSuppMasterRecord->companySystemID)
        ->where('isYesNO', 1)
        ->exists();

        $order = array(
            'masterdata' => $bookInvSuppMasterRecord,
            'docRef' => $refernaceDoc,
            'transDecimal' => $transDecimal,
            'localDecimal' => $localDecimal,
            'rptDecimal' => $rptDecimal,
            'directTotTra' => $directTotTra,
            'directTotVAT' => $directTotVAT,
            'directTotNet' => $directTotNet,
            'directTotLoc' => $directTotLoc,
            'grvTotTra' => $grvTotTra,
            'grvTotLoc' => $grvTotLoc,
            'isVATEligible' => $isVATEligible,
            'isProjectBase' => $isProjectBase,
            'grvTotRpt' => $grvTotRpt
        );

        $time = strtotime("now");
        $fileName = 'supplier_invoice_' . $id . '_' . $time . '.pdf';
        $html = view('print.supplier_invoice', $order);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream($fileName);
    }

    public function supplierInvoiceCancel(Request $request)
    {
        $input = $request->all();

        $supInvoiceAutoID = $input['supInvoiceAutoID'];

        $suppInvoiceData = BookInvSuppMaster::find($supInvoiceAutoID);
        if (empty($suppInvoiceData)) {
            return $this->sendError('Supplier Invoice not found');
        }

        if ($suppInvoiceData->confirmedYN == 1) {
            return $this->sendError('You cannot cancel this customer invoice, this is already confirmed');
        }

        if ($suppInvoiceData->approved == -1) {
            return $this->sendError('You cannot cancel this customer invoice, this is already approved');
        }

        if ($suppInvoiceData->cancelYN == -1) {
            return $this->sendError('You cannot cancel this customer invoice, this is already cancelled');
        }

        $supplierDetail = BookInvSuppDet::where('bookingSuppMasInvAutoID', $supInvoiceAutoID)->get();

        $supplierDirectDetail = DirectInvoiceDetails::where('directInvoiceAutoID', $supInvoiceAutoID)->get();

        $supplierDirectItemDetail = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $supInvoiceAutoID)->get();

        if (count($supplierDetail) > 0 || count($supplierDirectDetail) > 0 || count($supplierDirectItemDetail) > 0) {
            return $this->sendError('You cannot cancel this supplier invoice, invoice details are exist');
        }

        $employee = \Helper::getEmployeeInfo();

        $suppInvoiceData->cancelYN = -1;
        $suppInvoiceData->cancelComment = $request['cancelComments'];
        $suppInvoiceData->cancelDate = NOW();
        $suppInvoiceData->canceledByEmpSystemID = \Helper::getEmployeeSystemID();
        $suppInvoiceData->canceledByEmpID = $employee->empID;
        $suppInvoiceData->canceledByEmpName = $employee->empFullName;
        $suppInvoiceData->supplierInvoiceNo = null;
        $suppInvoiceData->save();

        /*Audit entry*/

        AuditTrial::createAuditTrial($suppInvoiceData->documentSystemID,$supInvoiceAutoID,$request['cancelComments'],'Cancelled', 'Not Confirmed');

        return $this->sendResponse($suppInvoiceData->toArray(), 'Customer invoice cancelled successfully');
    }

    public function getSupplierInvoiceStatusHistory(Request $request)
    {
        $input = $request->all();

        $companySystemID = $input['companySystemID'];
        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];

        $detail = DB::select('SELECT
	erp_paysupplierinvoicedetail.payDetailAutoID,
	erp_paysupplierinvoicedetail.PayMasterAutoId,
	erp_paysupplierinvoicedetail.apAutoID,
	erp_paysupplierinvoicedetail.matchingDocID,
	erp_paysupplierinvoicedetail.companyID,
	erp_paysupplierinvoicedetail.companySystemID,
IF (
	erp_paysupplierinvoicedetail.matchingDocID = 0,
	erp_paysupplierinvoicemaster.BPVcode,
	erp_matchdocumentmaster.matchingDocCode
) AS docCode,

IF (
	erp_paysupplierinvoicedetail.matchingDocID = 0,
	erp_paysupplierinvoicemaster.BPVdate,
	erp_matchdocumentmaster.matchingDocdate
) AS docDate,

IF (
	erp_paysupplierinvoicedetail.matchingDocID = 0,
	erp_paysupplierinvoicemaster.BPVNarration,
	"Matching"
) AS docNarration,
 erp_paysupplierinvoicedetail.addedDocumentID,
 erp_paysupplierinvoicedetail.bookingInvSystemCode,
 erp_paysupplierinvoicedetail.bookingInvDocCode,
 erp_paysupplierinvoicedetail.bookingInvoiceDate,
 erp_paysupplierinvoicedetail.supplierCodeSystem,
 erp_paysupplierinvoicedetail.supplierInvoiceNo,
 suppliermaster.supplierName,
 erp_paysupplierinvoicedetail.supplierTransCurrencyID,
 erp_paysupplierinvoicedetail.supplierTransER,
 currencymaster.CurrencyCode AS SupTransCur,
 currencymaster.DecimalPlaces AS SupTransDec,
 erp_paysupplierinvoicedetail.supplierPaymentAmount AS MyTotTransactionAmount,
 erp_paysupplierinvoicedetail.supplierInvoiceAmount,
 erp_paysupplierinvoicedetail.supplierPaymentAmount,

IF (
	erp_paysupplierinvoicedetail.matchingDocID = 0,
	erp_paysupplierinvoicemaster.confirmedYN,
	erp_matchdocumentmaster.matchingConfirmedYN
) AS docConfirmed,

IF (
	erp_paysupplierinvoicedetail.matchingDocID = 0,
	erp_paysupplierinvoicemaster.approved,
	erp_matchdocumentmaster.matchingConfirmedYN
) AS docApproved
FROM
	erp_paysupplierinvoicedetail
LEFT JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
LEFT JOIN suppliermaster ON erp_paysupplierinvoicedetail.supplierCodeSystem = suppliermaster.supplierCodeSystem
LEFT JOIN currencymaster ON erp_paysupplierinvoicedetail.supplierTransCurrencyID = currencymaster.currencyID
LEFT JOIN erp_matchdocumentmaster ON erp_paysupplierinvoicedetail.matchingDocID = erp_matchdocumentmaster.matchDocumentMasterAutoID  WHERE bookingInvSystemCode = ' . $bookingSuppMasInvAutoID . ' AND erp_paysupplierinvoicedetail.companySystemID = ' . $companySystemID . ' ');

        return $this->sendResponse($detail, 'payment status retrieved successfully');
    }

    public function getSupplierInvoiceAmend(Request $request)
    {
        $input = $request->all();

        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];

        $bookInvSuppMaster = BookInvSuppMaster::find($bookingSuppMasInvAutoID);
        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }

        if ($bookInvSuppMaster->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this Supplier Invoice');
        }

        $supplierInvoiceArray = array_except($bookInvSuppMaster->toArray(), ['rcmAvailable', 'isVatEligible']);

        BookInvSuppMasterRefferedBack::insert($supplierInvoiceArray);

        $fetchBookInvoiceDetails = BookInvSuppDet::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
            ->get();

        if (!empty($fetchBookInvoiceDetails)) {
            foreach ($fetchBookInvoiceDetails as $bookDetail) {
                $bookDetail['timesReferred'] = $bookInvSuppMaster->timesReferred;
            }
        }

        $bookInvoiceDetailArray = $fetchBookInvoiceDetails->toArray();

        BookInvSuppDetRefferedBack::insert($bookInvoiceDetailArray);

        $fetchBookInvoiceDirectDetails = DirectInvoiceDetails::where('directInvoiceAutoID', $bookingSuppMasInvAutoID)
            ->get();

        if (!empty($fetchBookInvoiceDirectDetails)) {
            foreach ($fetchBookInvoiceDirectDetails as $bookDirectDetail) {
                $bookDirectDetail['timesReferred'] = $bookInvSuppMaster->timesReferred;
            }
        }

        $bookInvoiceDirectDetailArray = $fetchBookInvoiceDirectDetails->toArray();

        DirectInvoiceDetailsRefferedBack::insert($bookInvoiceDirectDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $bookingSuppMasInvAutoID)
            ->where('companySystemID', $bookInvSuppMaster->companySystemID)
            ->where('documentSystemID', $bookInvSuppMaster->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $bookInvSuppMaster->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $bookingSuppMasInvAutoID)
            ->where('companySystemID', $bookInvSuppMaster->companySystemID)
            ->where('documentSystemID', $bookInvSuppMaster->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $bookInvSuppMaster->refferedBackYN = 0;
            $bookInvSuppMaster->confirmedYN = 0;
            $bookInvSuppMaster->confirmedByEmpSystemID = null;
            $bookInvSuppMaster->confirmedByEmpID = null;
            $bookInvSuppMaster->confirmedByName = null;
            $bookInvSuppMaster->confirmedDate = null;
            $bookInvSuppMaster->RollLevForApp_curr = 1;
            $bookInvSuppMaster->save();
        }

        // delete tax details
        $checkTaxExist = Taxdetail::where('documentSystemCode', $bookingSuppMasInvAutoID)
            ->where('companySystemID', $bookInvSuppMaster->companySystemID)
            ->where('documentSystemID', 11)
            ->first();

        if ($checkTaxExist) {
             Taxdetail::where('documentSystemCode', $bookingSuppMasInvAutoID)
                ->where('companySystemID', $bookInvSuppMaster->companySystemID)
                ->where('documentSystemID', 11)
                ->delete();
        }

        return $this->sendResponse($bookInvSuppMaster->toArray(), 'Supplier Invoice Amend successfully');
    }

    public function supplierInvoiceTaxPercentage(Request $request)
    {
        $input = $request->all();

        $taxMasterAutoID = $input['taxMasterAutoID'];

        $taxMaster = DB::select('SELECT taxPercent FROM erp_taxmaster WHERE taxMasterAutoID = ' . $taxMasterAutoID . '');

        return $this->sendResponse($taxMaster, 'Data retrieved successfully');
    }

    public function amendSupplierInvoiceReview(Request $request)
    {
        $input = $request->all();

        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];

        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $bookInvSuppMasterData = BookInvSuppMaster::find($bookingSuppMasInvAutoID);

        if (empty($bookInvSuppMasterData)) {
            return $this->sendError('Supplier Invoice not found');
        }

        $documentAutoId = $bookingSuppMasInvAutoID;
        $documentSystemID = $bookInvSuppMasterData->documentSystemID;
        
        if($bookInvSuppMasterData->approved == -1) {
            $validateFinanceYear = ValidateDocumentAmend::validateFinanceYear($documentAutoId,$documentSystemID);
            if(isset($validateFinanceYear['status']) && $validateFinanceYear['status'] == false){
                if(isset($validateFinanceYear['message']) && $validateFinanceYear['message']){
                    return $this->sendError($validateFinanceYear['message']);
                }
            }
            
            $validateFinancePeriod = ValidateDocumentAmend::validateFinancePeriod($documentAutoId,$documentSystemID);
            if(isset($validateFinancePeriod['status']) && $validateFinancePeriod['status'] == false){
                if(isset($validateFinancePeriod['message']) && $validateFinancePeriod['message']){
                    return $this->sendError($validateFinancePeriod['message']);
                }
            }
    
            $validatePendingGlPost = ValidateDocumentAmend::validatePendingGlPost($documentAutoId,$documentSystemID);
            if(isset($validatePendingGlPost['status']) && $validatePendingGlPost['status'] == false){
                if(isset($validatePendingGlPost['message']) && $validatePendingGlPost['message']){
                    return $this->sendError($validatePendingGlPost['message']);
                }
            }
        }


        if ($bookInvSuppMasterData->confirmedYN == 0 && $bookInvSuppMasterData->cancelYN == 0) {
            return $this->sendError('You cannot return back to amend this Supplier Invoice, it is not confirmed');
        }

        // checking document matched in machmaster
        $checkDetailExistMatch = PaySupplierInvoiceDetail::where('bookingInvSystemCode', $bookingSuppMasInvAutoID)
            ->where('companySystemID', $bookInvSuppMasterData->companySystemID)
            ->where('addedDocumentSystemID', $bookInvSuppMasterData->documentSystemID)
            ->first();

        if ($checkDetailExistMatch) {
            return $this->sendError('Cannot return back to amend. Supplier Invoice is added to payment');
        }

        $emailBody = '<p>' . $bookInvSuppMasterData->bookingInvCode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $emailSubject = $bookInvSuppMasterData->bookingInvCode . ' has been return back to amend';

        DB::beginTransaction();
        try {

            //sending email to relevant party
            if ($bookInvSuppMasterData->confirmedYN == 1) {
                $emails[] = array('empSystemID' => $bookInvSuppMasterData->confirmedByEmpSystemID,
                    'companySystemID' => $bookInvSuppMasterData->companySystemID,
                    'docSystemID' => $bookInvSuppMasterData->documentSystemID,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody,
                    'docSystemCode' => $bookInvSuppMasterData->bookingSuppMasInvAutoID);
            }

            $documentApproval = DocumentApproved::where('companySystemID', $bookInvSuppMasterData->companySystemID)
                ->where('documentSystemCode', $bookingSuppMasInvAutoID)
                ->where('documentSystemID', $bookInvSuppMasterData->documentSystemID)
                ->get();

            foreach ($documentApproval as $da) {
                if ($da->approvedYN == -1) {
                    $emails[] = array('empSystemID' => $da->employeeSystemID,
                        'companySystemID' => $bookInvSuppMasterData->companySystemID,
                        'docSystemID' => $bookInvSuppMasterData->documentSystemID,
                        'alertMessage' => $emailSubject,
                        'emailAlertMessage' => $emailBody,
                        'docSystemCode' => $bookInvSuppMasterData->bookingSuppMasInvAutoID);
                }
            }

            $sendEmail = \Email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            //deleting from approval table
            DocumentApproved::where('documentSystemCode', $bookingSuppMasInvAutoID)
                ->where('companySystemID', $bookInvSuppMasterData->companySystemID)
                ->where('documentSystemID', $bookInvSuppMasterData->documentSystemID)
                ->delete();

            //deleting from general ledger table
            GeneralLedger::where('documentSystemCode', $bookingSuppMasInvAutoID)
                ->where('companySystemID', $bookInvSuppMasterData->companySystemID)
                ->where('documentSystemID', $bookInvSuppMasterData->documentSystemID)
                ->delete();

            //deleting records from accounts payable
            AccountsPayableLedger::where('documentSystemCode', $bookingSuppMasInvAutoID)
                ->where('companySystemID', $bookInvSuppMasterData->companySystemID)
                ->where('documentSystemID', $bookInvSuppMasterData->documentSystemID)
                ->delete();

             //deleting records from employee ledger
            EmployeeLedger::where('documentSystemCode', $bookingSuppMasInvAutoID)
                ->where('companySystemID', $bookInvSuppMasterData->companySystemID)
                ->where('documentSystemID', $bookInvSuppMasterData->documentSystemID)
                ->delete();

            //deleting from tax ledger table
            TaxLedger::where('documentMasterAutoID', $bookingSuppMasInvAutoID)
                ->where('companySystemID', $bookInvSuppMasterData->companySystemID)
                ->where('documentSystemID', $bookInvSuppMasterData->documentSystemID)
                ->delete();

            TaxLedgerDetail::where('documentMasterAutoID', $bookingSuppMasInvAutoID)
                ->where('companySystemID', $bookInvSuppMasterData->companySystemID)
                ->where('documentSystemID', $bookInvSuppMasterData->documentSystemID)
                ->delete();

            BudgetConsumedData::where('documentSystemCode', $bookingSuppMasInvAutoID)
                ->where('companySystemID', $bookInvSuppMasterData->companySystemID)
                ->where('documentSystemID', $bookInvSuppMasterData->documentSystemID)
                ->delete();

            if($bookInvSuppMasterData->cancelYN == -1){
                $oldStatus = 'Cancelled';
            } else {
                $oldStatus = 'Approved';
            }

            // updating fields
            $bookInvSuppMasterData->confirmedYN = 0;
            $bookInvSuppMasterData->confirmedByEmpSystemID = null;
            $bookInvSuppMasterData->confirmedByEmpID = null;
            $bookInvSuppMasterData->confirmedByName = null;
            $bookInvSuppMasterData->confirmedDate = null;
            $bookInvSuppMasterData->RollLevForApp_curr = 1;

            $bookInvSuppMasterData->approved = 0;
            $bookInvSuppMasterData->approvedByUserSystemID = null;
            $bookInvSuppMasterData->approvedByUserID = null;
            $bookInvSuppMasterData->approvedDate = null;
            $bookInvSuppMasterData->postedDate = null;

            $bookInvSuppMasterData->cancelYN = 0;
            $bookInvSuppMasterData->cancelComment = null;
            $bookInvSuppMasterData->cancelDate = null;
            $bookInvSuppMasterData->canceledByEmpSystemID = null;
            $bookInvSuppMasterData->canceledByEmpID = null;
            $bookInvSuppMasterData->canceledByEmpName = null;

            $bookInvSuppMasterData->save();

            AuditTrial::createAuditTrial($bookInvSuppMasterData->documentSystemID,$bookingSuppMasInvAutoID,$input['returnComment'],'returned back to amend', $oldStatus);

            $this->expenseAssetAllocationRepository->deleteExpenseAssetAllocation($bookingSuppMasInvAutoID, $bookInvSuppMasterData->documentSystemID);

            DB::commit();
            return $this->sendResponse($bookInvSuppMasterData->toArray(), 'Supplier Invoice amend saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function checkPaymentStatusSIPrint(Request $request)
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

        return $this->sendResponse($printID, 'Print data retrieved');
    }

    public function clearSupplierInvoiceNo(Request $request)
    {
        $input = $request->all();

        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];

        $bookInvSuppMaster = BookInvSuppMaster::find($bookingSuppMasInvAutoID);
        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }

        // updating fields
        $bookInvSuppMaster->supplierInvoiceNo = null;
        $bookInvSuppMaster->save();

        return $this->sendResponse($bookInvSuppMaster, 'Record updated successfully');
    }

    public function getFilteredDirectCustomerInvoice(Request $request)
    {
        $input = $request->all();
        $seachText = $input['seachText'];
        $seachText = str_replace("\\", "\\\\", $seachText);

        $directCustomerInvoices = CustomerInvoice::select('custInvoiceDirectAutoID','bookingInvCode')
                                        ->where('isPerforma',0)
                                        ->where('approved',-1)
                                        ->where('canceledYN',0)
                                        ->whereHas('company', function ($query) {
                                            $query->where('masterCompanySystemIDReorting','<>',35);
                                        })
                                        ->where('bookingInvCode', 'LIKE', "%{$seachText}%")
                                        ->orderBy('custInvoiceDirectAutoID', 'desc')
                                        ->take(30)
                                        ->get()->toArray();

        return $this->sendResponse($directCustomerInvoices, 'Data retrieved successfully');
    }


    public function getPurchaseOrdersLikedWithSi(Request $request)
    {
        $input = $request->all();

        $siData = BookInvSuppDet::where('bookingSuppMasInvAutoID', $input['invoiceID'])
                               ->groupBy('purchaseOrderID')
                               ->whereNotNull('purchaseOrderID')
                               ->get();

        $poIds = count($siData) > 0 ? $siData->pluck('purchaseOrderID') : [];

        $purchaseOrders = ProcumentOrder::selectRaw('purchaseOrderID as value, purchaseOrderCode as label')
                                        ->whereIn('purchaseOrderID', $poIds)
                                        ->get();


        return $this->sendResponse($purchaseOrders, 'Data retrieved successfully');
    }
}
