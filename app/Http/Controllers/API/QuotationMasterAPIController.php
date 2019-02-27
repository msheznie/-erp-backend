<?php
/**
 * =============================================
 * -- File Name : QuotationMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  QuotationMaster
 * -- Author : Mohamed Nazir
 * -- Create date : 22 - January 2019
 * -- Description : This file contains the all CRUD for Sales Quotation Master
 * -- REVISION HISTORY
 * -- Date: 23-January 2019 By: Nazir Description: Added new function getSalesQuotationFormData(),
 * -- Date: 23-January 2019 By: Nazir Description: Added new function getAllSalesQuotation(),
 * -- Date: 24-January 2019 By: Nazir Description: Added new function getItemsForSalesQuotation(),
 * -- Date: 25-January 2019 By: Nazir Description: Added new function getSalesQuotationApprovals(),
 * -- Date: 25-January 2019 By: Nazir Description: Added new function getApprovedSalesQuotationForUser(),
 * -- Date: 25-January 2019 By: Nazir Description: Added new function approveSalesQuotation(),
 * -- Date: 25-January 2019 By: Nazir Description: Added new function rejectSalesQuotation(),
 * -- Date: 25-January 2019 By: Nazir Description: Added new function getSalesQuotationMasterRecord(),
 * -- Date: 25-January 2019 By: Nazir Description: Added new function getSalesQuotationPrintPDF(),
 * -- Date: 29-January 2019 By: Nazir Description: Added new function salesQuotationReopen(),
 * -- Date: 29-January 2019 By: Nazir Description: Added new function salesQuotationVersionCreate(),
 * -- Date: 03-February 2019 By: Nazir Description: Added new function salesQuotationAmend(),
 * -- Date: 05-February 2019 By: Nazir Description: Added new function salesQuotationAudit(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuotationMasterAPIRequest;
use App\Http\Requests\API\UpdateQuotationMasterAPIRequest;
use App\Models\CompanyDocumentAttachment;
use App\Models\CurrencyMaster;
use App\Models\CustomerAssigned;
use App\Models\CustomerMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\ItemAssigned;
use App\Models\Months;
use App\Models\QuotationDetails;
use App\Models\QuotationDetailsRefferedback;
use App\Models\QuotationMaster;
use App\Models\QuotationMasterRefferedback;
use App\Models\QuotationMasterVersion;
use App\Models\QuotationVersionDetails;
use App\Models\SalesPersonMaster;
use App\Models\YesNoSelection;
use App\Models\Company;
use App\Models\YesNoSelectionForMinus;
use App\Models\ChartOfAccount;
use App\Models\customercurrency;
use App\Repositories\QuotationMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Response;

/**
 * Class QuotationMasterController
 * @package App\Http\Controllers\API
 */
class QuotationMasterAPIController extends AppBaseController
{
    /** @var  QuotationMasterRepository */
    private $quotationMasterRepository;

    public function __construct(QuotationMasterRepository $quotationMasterRepo)
    {
        $this->quotationMasterRepository = $quotationMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationMasters",
     *      summary="Get a listing of the QuotationMasters.",
     *      tags={"QuotationMaster"},
     *      description="Get all QuotationMasters",
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
     *                  @SWG\Items(ref="#/definitions/QuotationMaster")
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
        $this->quotationMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->quotationMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $quotationMasters = $this->quotationMasterRepository->all();

        return $this->sendResponse($quotationMasters->toArray(), 'Quotation Masters retrieved successfully');
    }

    /**
     * @param CreateQuotationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/quotationMasters",
     *      summary="Store a newly created QuotationMaster in storage",
     *      tags={"QuotationMaster"},
     *      description="Store QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationMaster")
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
     *                  ref="#/definitions/QuotationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateQuotationMasterAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        if (isset($input['documentDate'])) {
            if ($input['documentDate']) {
                $input['documentDate'] = new Carbon($input['documentDate']);
            }
        }

        if (isset($input['documentExpDate'])) {
            if ($input['documentExpDate']) {
                $input['documentExpDate'] = new Carbon($input['documentExpDate']);
            }
        }

        if ($input['documentExpDate'] < $input['documentDate']) {

            return $this->sendError('Document expiry date cannot be less than document date!');
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        if ($documentMaster) {
            $input['documentID'] = $documentMaster->documentID;
        }

        $customerData = CustomerMaster::where('customerCodeSystem', $input['customerSystemCode'])->first();

        if ($customerData) {
            $input['customerCode'] = $customerData->CutomerCode;
            $input['customerName'] = $customerData->CustomerName;
            $input['customerAddress'] = $customerData->customerAddress1;
            //$input['customerTelephone'] = $customerData->CutomerCode;
            //$input['customerFax'] = $customerData->CutomerCode;
            //$input['customerEmail'] = $customerData->CutomerCode;
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['transactionCurrencyID'], $input['transactionCurrencyID'], 0);

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
            $input['companyLocalCurrencyID'] = $company->localCurrencyID;
            $input['companyLocalExchangeRate'] = $companyCurrencyConversion['trasToLocER'];
            $input['companyReportingCurrencyID'] = $company->reportingCurrency;
            $input['companyReportingExchangeRate'] = $companyCurrencyConversion['trasToRptER'];

        }

        //updating transaction currency details
        $transactionCurrencyData = CurrencyMaster::where('currencyID', $input['transactionCurrencyID'])->first();
        if ($transactionCurrencyData) {
            $input['transactionCurrency'] = $transactionCurrencyData->CurrencyCode;
            $input['transactionExchangeRate'] = 1;
            $input['transactionCurrencyDecimalPlaces'] = $transactionCurrencyData->DecimalPlaces;
        }

        //updating local currency details
        $localCurrencyData = CurrencyMaster::where('currencyID', $input['companyLocalCurrencyID'])->first();
        if ($localCurrencyData) {
            $input['companyLocalCurrency'] = $localCurrencyData->CurrencyCode;
            $input['companyLocalCurrencyDecimalPlaces'] = $localCurrencyData->DecimalPlaces;
        }

        //updating reporting currency details
        $reportingCurrencyData = CurrencyMaster::where('currencyID', $input['companyLocalCurrencyID'])->first();
        if ($reportingCurrencyData) {
            $input['companyReportingCurrency'] = $reportingCurrencyData->CurrencyCode;
            $input['companyReportingCurrencyDecimalPlaces'] = $reportingCurrencyData->DecimalPlaces;
        }

        //updating customer GL update
        $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerSystemCode'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();
        if ($customerGLCodeUpdate) {

            $chartOfAccountData = ChartOfAccount::where('chartOfAccountSystemID', $customerGLCodeUpdate->custGLAccountSystemID)->first();
            if ($chartOfAccountData) {
                $input['customerReceivableAutoID'] = $chartOfAccountData->chartOfAccountSystemID;
                $input['customerReceivableGLAccount'] = $chartOfAccountData->AccountCode;
                $input['customerReceivableDescription'] = $chartOfAccountData->AccountDescription;
                $input['customerReceivableType'] = $chartOfAccountData->controlAccounts;
            }

        }

        $customerCurrency = customercurrency::where('customerCodeSystem', $input['customerSystemCode'])->where('isDefault', -1)->first();
        if ($customerCurrency) {

            $customerCurrencyMasterData = CurrencyMaster::where('currencyID', $customerCurrency->currencyID)->first();

            $input['customerCurrencyID'] = $customerCurrency->currencyID;
            $input['customerCurrency'] = $customerCurrencyMasterData->CurrencyCode;
            $input['customerCurrencyDecimalPlaces'] = $customerCurrencyMasterData->DecimalPlaces;

            //updating customer currency exchange rate
            $currencyConversionCustomerDefault = \Helper::currencyConversion($input['companySystemID'], $input['transactionCurrencyID'], $customerCurrency->currencyID, 0);

            if ($currencyConversionCustomerDefault) {
                $input['customerCurrencyExchangeRate'] = $currencyConversionCustomerDefault['transToDocER'];
            }
        }

        // creating document code
        $lastSerial = QuotationMaster::where('companySystemID', $input['companySystemID'])
            ->where('documentSystemID', $input['documentSystemID'])
            ->orderBy('quotationMasterID', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
        }

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        if ($documentMaster) {
            $quotationCode = ($company->CompanyID . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['quotationCode'] = $quotationCode;
        }

        $input['serialNumber'] = $lastSerialNumber;

        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserName'] = $employee->empName;

        $quotationMasters = $this->quotationMasterRepository->create($input);

        return $this->sendResponse($quotationMasters->toArray(), 'Quotation Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationMasters/{id}",
     *      summary="Display the specified QuotationMaster",
     *      tags={"QuotationMaster"},
     *      description="Get QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMaster",
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
     *                  ref="#/definitions/QuotationMaster"
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
        /** @var QuotationMaster $quotationMaster */
        $quotationMaster = $this->quotationMasterRepository->with(['created_by', 'confirmed_by'])->findWithoutFail($id);

        if (empty($quotationMaster)) {
            return $this->sendError('Quotation Master not found');
        }

        return $this->sendResponse($quotationMaster->toArray(), 'Quotation Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateQuotationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/quotationMasters/{id}",
     *      summary="Update the specified QuotationMaster in storage",
     *      tags={"QuotationMaster"},
     *      description="Update QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationMaster")
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
     *                  ref="#/definitions/QuotationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateQuotationMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'confirmedByEmpID', 'confirmedDate', 'company', 'confirmed_by', 'confirmedByEmpSystemID']);
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        $tempName = '';
        if ($input['documentSystemID'] == 67) {
            $tempName = 'quotation';
        } else if ($input['documentSystemID'] == 68) {
            $tempName = 'order';
        }

        /** @var QuotationMaster $quotationMaster */
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMaster)) {
            return $this->sendError('Sales ' . $tempName . ' not found');
        }

        if (isset($input['documentDate'])) {
            if ($input['documentDate']) {
                $input['documentDate'] = new Carbon($input['documentDate']);
            }
        }

        if (isset($input['documentExpDate'])) {
            if ($input['documentExpDate']) {
                $input['documentExpDate'] = new Carbon($input['documentExpDate']);
            }
        }

        if ($input['documentExpDate'] < $input['documentDate']) {

            return $this->sendError('Document expiry date cannot be less than document date!');
        }

        $customerData = CustomerMaster::where('customerCodeSystem', $input['customerSystemCode'])->first();

        if ($customerData) {
            $input['customerCode'] = $customerData->CutomerCode;
            $input['customerName'] = $customerData->CustomerName;
            $input['customerAddress'] = $customerData->customerAddress1;
            //$input['customerTelephone'] = $customerData->CutomerCode;
            //$input['customerFax'] = $customerData->CutomerCode;
            //$input['customerEmail'] = $customerData->CutomerCode;
        }

        //updating transaction currency details
        $transactionCurrencyData = CurrencyMaster::where('currencyID', $input['transactionCurrencyID'])->first();
        if ($transactionCurrencyData) {
            $input['transactionCurrency'] = $transactionCurrencyData->CurrencyCode;
            $input['transactionExchangeRate'] = 1;
            $input['transactionCurrencyDecimalPlaces'] = $transactionCurrencyData->DecimalPlaces;
        }


        //updating customer GL update
        $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerSystemCode'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();
        if ($customerGLCodeUpdate) {

            $chartOfAccountData = ChartOfAccount::where('chartOfAccountSystemID', $customerGLCodeUpdate->custGLAccountSystemID)->first();
            if ($chartOfAccountData) {
                $input['customerReceivableAutoID'] = $chartOfAccountData->chartOfAccountSystemID;
                $input['customerReceivableGLAccount'] = $chartOfAccountData->AccountCode;
                $input['customerReceivableDescription'] = $chartOfAccountData->AccountDescription;
                $input['customerReceivableType'] = $chartOfAccountData->controlAccounts;
            }

        }

        $customerCurrency = customercurrency::where('customerCodeSystem', $input['customerSystemCode'])->where('isDefault', -1)->first();
        if ($customerCurrency) {

            $customerCurrencyMasterData = CurrencyMaster::where('currencyID', $customerCurrency->currencyID)->first();

            $input['customerCurrencyID'] = $customerCurrency->currencyID;
            $input['customerCurrency'] = $customerCurrencyMasterData->CurrencyCode;
            $input['customerCurrencyDecimalPlaces'] = $customerCurrencyMasterData->DecimalPlaces;

            //updating customer currency exchange rate
            $currencyConversionCustomerDefault = \Helper::currencyConversion($input['companySystemID'], $input['transactionCurrencyID'], $customerCurrency->currencyID, 0);

            if ($currencyConversionCustomerDefault) {
                $input['customerCurrencyExchangeRate'] = $currencyConversionCustomerDefault['transToDocER'];
            }
        }

        // updating header amounts
        $totalAmount = QuotationDetails::selectRaw("COALESCE(SUM(transactionAmount),0) as totalTransactionAmount, COALESCE(SUM(companyLocalAmount),0) as totalLocalAmount, COALESCE(SUM(companyReportingAmount),0) as totalReportingAmount, COALESCE(SUM(customerAmount),0) as totalCustomerAmount")
            ->where('quotationMasterID', $id)->first();

        $input['transactionAmount'] = \Helper::roundValue($totalAmount->totalTransactionAmount);
        $input['companyLocalAmount'] = \Helper::roundValue($totalAmount->totalLocalAmount);
        $input['companyReportingAmount'] = \Helper::roundValue($totalAmount->totalReportingAmount);
        $input['customerCurrencyAmount'] = \Helper::roundValue($totalAmount->totalCustomerAmount);

        if ($quotationMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $validator = \Validator::make($input, [
                'documentDate' => 'required',
                'documentExpDate' => 'required',
                'customerSystemCode' => 'required|numeric|min:1',
                'transactionCurrencyID' => 'required|numeric|min:1'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $qoDetailExist = QuotationDetails::where('quotationMasterID', $id)
                ->count();

            if ($qoDetailExist == 0) {
                return $this->sendError('Sales ' . $tempName . ' cannot be confirmed without any details');
            }

            $checkQuantity = QuotationDetails::where('quotationMasterID', $id)
                ->where('requestedQty', '<', 0.1)
                ->count();

            if ($checkQuantity > 0) {
                return $this->sendError('Every item should have at least one minimum qty requested', 500);
            }

            if ($qoDetailExist > 0) {
                $checkAmount = QuotationDetails::where('quotationMasterID', $id)
                    ->where(function ($q) {
                        $q->where('transactionAmount', '<=', 0)
                            ->orWhereNull('companyLocalAmount', '<=', 0)
                            ->orWhereNull('companyReportingAmount', '<=', 0)
                            ->orWhereNull('transactionAmount')
                            ->orWhereNull('companyLocalAmount')
                            ->orWhereNull('companyReportingAmount');
                    })
                    ->count();
                if ($checkAmount > 0) {
                    return $this->sendError('Amount should be greater than 0 for every items', 500);
                }
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
                'document' => $input["documentSystemID"],
                'segment' => 0,
                'category' => 0,
                'amount' => $input['transactionAmount']
            );
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"]);
            }
        }
        $input['modifiedDateTime'] = Carbon::now();
        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserName'] = $employee->empName;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        $quotationMaster = $this->quotationMasterRepository->update($input, $id);

        return $this->sendResponse($quotationMaster->toArray(), 'Sales ' . $tempName . ' updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/quotationMasters/{id}",
     *      summary="Remove the specified QuotationMaster from storage",
     *      tags={"QuotationMaster"},
     *      description="Delete QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMaster",
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
        /** @var QuotationMaster $quotationMaster */
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMaster)) {
            return $this->sendError('Quotation Master not found');
        }

        $quotationMaster->delete();

        return $this->sendResponse($id, 'Quotation Master deleted successfully');
    }

    public function getSalesQuotationFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $customer = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName"))
            ->where('companySystemID', $subCompanies)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $salespersons = SalesPersonMaster::select(DB::raw("salesPersonID,CONCAT(SalesPersonCode, ' | ' ,SalesPersonName) as SalesPersonName"))
            ->where('companySystemID', $subCompanies)
            ->get();

        $years = QuotationMaster::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $month = Months::all();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'customer' => $customer,
            'salespersons' => $salespersons
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getAllSalesQuotation(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $quotationMaster = QuotationMaster::whereIn('companySystemID', $childCompanies)
            ->where('documentSystemID', $input['documentSystemID']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $quotationMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $quotationMaster->where('approvedYN', $input['approvedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $quotationMaster->whereMonth('documentDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $quotationMaster->whereYear('documentDate', '=', $input['year']);
            }
        }

        if (array_key_exists('customerSystemCode', $input)) {
            if ($input['customerSystemCode'] && !is_null($input['customerSystemCode'])) {
                $quotationMaster->where('customerSystemCode', $input['customerSystemCode']);
            }
        }

        if (array_key_exists('salesPersonID', $input)) {
            if ($input['salesPersonID'] && !is_null($input['salesPersonID'])) {
                $quotationMaster->where('salesPersonID', $input['salesPersonID']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $quotationMaster = $quotationMaster->where(function ($query) use ($search) {
                $query->where('quotationCode', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($quotationMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('quotationMasterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getItemsForSalesQuotation(Request $request)
    {
        $input = $request->all();

        $companySystemID = $input['companySystemID'];

        $items = ItemAssigned::where('companySystemID', $companySystemID)
            ->where('isActive', 1)
            ->where('isAssigned', -1);
        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }
        $items = $items
            ->take(20)
            ->get();

        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }

    public function getSalesQuotationApprovals(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $documentSystemID = $request->documentSystemID;
        $empID = \Helper::getEmployeeSystemID();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'erp_quotationmaster.quotationMasterID',
            'erp_quotationmaster.quotationCode',
            'erp_quotationmaster.documentSystemID',
            'erp_quotationmaster.referenceNo',
            'erp_quotationmaster.documentDate',
            'erp_quotationmaster.documentExpDate',
            'erp_quotationmaster.narration',
            'erp_quotationmaster.createdDateTime',
            'erp_quotationmaster.confirmedDate',
            'erp_quotationmaster.transactionAmount',
            'erp_quotationmaster.customerName',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            $query->where('employeesdepartments.documentSystemID', 67)
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID);
        })->join('erp_quotationmaster', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'quotationMasterID')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('erp_quotationmaster.companySystemID', $companyID)
                ->where('erp_quotationmaster.approvedYN', 0)
                ->where('erp_quotationmaster.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'transactionCurrencyID', 'currencymaster.currencyID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', $documentSystemID)
            ->where('erp_documentapproved.companySystemID', $companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('quotationCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('customerName', 'LIKE', "%{$search}%");
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

    public function getApprovedSalesQuotationForUser(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $documentSystemID = $request->documentSystemID;
        $empID = \Helper::getEmployeeSystemID();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'erp_quotationmaster.quotationMasterID',
            'erp_quotationmaster.quotationCode',
            'erp_quotationmaster.documentSystemID',
            'erp_quotationmaster.referenceNo',
            'erp_quotationmaster.documentDate',
            'erp_quotationmaster.documentExpDate',
            'erp_quotationmaster.narration',
            'erp_quotationmaster.createdDateTime',
            'erp_quotationmaster.confirmedDate',
            'erp_quotationmaster.transactionAmount',
            'erp_quotationmaster.approvedDate',
            'erp_quotationmaster.customerName',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('erp_quotationmaster', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'quotationMasterID')
                ->where('erp_quotationmaster.companySystemID', $companyID)
                ->where('erp_quotationmaster.approvedYN', -1)
                ->where('erp_quotationmaster.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'transactionCurrencyID', 'currencymaster.currencyID')
            ->where('erp_documentapproved.documentSystemID', 67)
            ->where('erp_documentapproved.companySystemID', $companyID)
            ->where('erp_documentapproved.documentSystemID', $documentSystemID)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('quotationCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('customerName', 'LIKE', "%{$search}%");
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

    public function approveSalesQuotation(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectSalesQuotation(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    public function getSalesQuotationMasterRecord(Request $request)
    {
        $input = $request->all();

        $output = QuotationMaster::where('quotationMasterID', $input['quotationMasterID'])->with(['approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 11);
        }, 'company', 'detail', 'confirmed_by', 'created_by', 'modified_by', 'sales_person'])->first();

        return $this->sendResponse($output, 'Data retrieved successfully');
    }

    public function getSalesQuotationPrintPDF(Request $request)
    {
        $id = $request->get('id');

        $quotationMasterData = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMasterData)) {
            return $this->sendError('Quotation Master not found');
        }

        $output = QuotationMaster::where('quotationMasterID', $id)->with(['approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 11);
        }, 'company', 'detail', 'confirmed_by', 'created_by', 'modified_by', 'sales_person'])->first();

        $netTotal = QuotationDetails::where('quotationMasterID', $id)
            ->sum('transactionAmount');

        $order = array(
            'masterdata' => $output,
            'netTotal' => $netTotal
        );

        $html = view('print.sales_quotation', $order);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream();
    }


    public function salesQuotationReopen(request $request)
    {
        $input = $request->all();
        $quotationMasterID = $input['quotationMasterID'];

        $quotationMasterData = QuotationMaster::find($quotationMasterID);
        $emails = array();
        if (empty($quotationMasterData)) {
            return $this->sendError('Quotation master not found');
        }

        if ($quotationMasterData->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this sales quotation it is already partially approved');
        }

        if ($quotationMasterData->approved == -1) {
            return $this->sendError('You cannot reopen this sales quotation it is already fully approved');
        }

        if ($quotationMasterData->confirmedYN == 0) {
            return $this->sendError('You cannot reopen this sales quotation, it is not confirmed');
        }

        // updating fields
        $quotationMasterData->confirmedYN = 0;
        $quotationMasterData->confirmedByEmpSystemID = null;
        $quotationMasterData->confirmedByEmpID = null;
        $quotationMasterData->confirmedByName = null;
        $quotationMasterData->confirmedDate = null;
        $quotationMasterData->RollLevForApp_curr = 1;
        $quotationMasterData->save();

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $quotationMasterData->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $quotationMasterData->quotationCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $quotationMasterData->quotationCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $quotationMasterData->companySystemID)
            ->where('documentSystemCode', $quotationMasterData->custInvoiceDirectAutoID)
            ->where('documentSystemID', $quotationMasterData->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $quotationMasterData->companySystemID)
                    ->where('documentSystemID', $quotationMasterData->documentSystemID)
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

        $deleteApproval = DocumentApproved::where('documentSystemCode', $quotationMasterID)
            ->where('companySystemID', $quotationMasterData->companySystemID)
            ->where('documentSystemID', $quotationMasterData->documentSystemID)
            ->delete();

        return $this->sendResponse('s', 'Sales quotation reopened successfully');

    }

    public function salesQuotationVersionCreate(Request $request)
    {
        $input = $request->all();

        $quotationMasterID = $input['quotationMasterID'];

        $employee = \Helper::getEmployeeInfo();
        $emails = array();
        $currentVersion = 0;

        $quotationMasterData = QuotationMaster::find($quotationMasterID);

        if (empty($quotationMasterData)) {
            return $this->sendError('Quotation master not found');
        }

        $quotationMasterArray = $quotationMasterData->toArray();

        $storeQuotationMasterVersion = QuotationMasterVersion::insert($quotationMasterArray);

        $fetchQuotationDetails = QuotationDetails::where('quotationMasterID', $quotationMasterID)
            ->get();

        if (!empty($fetchQuotationDetails)) {
            foreach ($fetchQuotationDetails as $bookDetail) {
                $bookDetail['versionNo'] = $quotationMasterData->versionNo;
            }
        }

        $quotationDetailsArray = $fetchQuotationDetails->toArray();

        $storeQuotationVersionDetails = QuotationVersionDetails::insert($quotationDetailsArray);

        // sending email to the relevant party

        $emailBody = '<p>' . $quotationMasterData->quotationCode . ' is being revised by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $emailSubject = $quotationMasterData->quotationCode . ' has been created new version';

        if ($quotationMasterData->confirmedYN == 1) {
            $emails[] = array('empSystemID' => $quotationMasterData->confirmedByEmpSystemID,
                'companySystemID' => $quotationMasterData->companySystemID,
                'docSystemID' => $quotationMasterData->documentSystemID,
                'alertMessage' => $emailSubject,
                'emailAlertMessage' => $emailBody,
                'docSystemCode' => $quotationMasterID);
        }

        $documentApproval = DocumentApproved::where('companySystemID', $quotationMasterData->companySystemID)
            ->where('documentSystemCode', $quotationMasterID)
            ->where('documentSystemID', $quotationMasterData->documentSystemID)
            ->get();

        foreach ($documentApproval as $da) {
            if ($da->approvedYN == -1) {
                $emails[] = array('empSystemID' => $da->employeeSystemID,
                    'companySystemID' => $quotationMasterData->companySystemID,
                    'docSystemID' => $quotationMasterData->documentSystemID,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody,
                    'docSystemCode' => $quotationMasterID);
            }
        }

        $sendEmail = \Email::sendEmail($emails);
        if (!$sendEmail["success"]) {
            return $this->sendError($sendEmail["message"], 500);
        }

        $deleteApproval = DocumentApproved::where('documentSystemCode', $quotationMasterID)
            ->where('companySystemID', $quotationMasterData->companySystemID)
            ->where('documentSystemID', $quotationMasterData->documentSystemID)
            ->delete();

        if ($quotationMasterData) {
            $currentVersion = $quotationMasterData->versionNo + 1;
        }

        if ($deleteApproval) {
            // updating fields
            $quotationMasterData->versionNo = $currentVersion;
            $quotationMasterData->confirmedYN = 0;
            $quotationMasterData->confirmedByEmpSystemID = null;
            $quotationMasterData->confirmedByEmpID = null;
            $quotationMasterData->confirmedByName = null;
            $quotationMasterData->confirmedDate = null;
            $quotationMasterData->RollLevForApp_curr = 1;

            $quotationMasterData->approvedYN = 0;
            $quotationMasterData->approvedEmpSystemID = null;
            $quotationMasterData->approvedbyEmpID = null;
            $quotationMasterData->approvedbyEmpName = null;
            $quotationMasterData->approvedDate = null;
            $quotationMasterData->save();
        }

        return $this->sendResponse($quotationMasterData->toArray(), 'Quotation version created successfully');
    }

    public function salesQuotationAmend(Request $request)
    {
        $input = $request->all();

        $quotationMasterID = $input['quotationMasterID'];

        $quotationMasterData = QuotationMaster::find($quotationMasterID);

        if (empty($quotationMasterData)) {
            return $this->sendError('Sales quotation not found');
        }

        if ($quotationMasterData->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this Sales quotation');
        }

        $salesQuotationArray = $quotationMasterData->toArray();

        $storeSalesQuotationHistory = QuotationMasterRefferedback::insert($salesQuotationArray);

        $fetchQuotationDetails = QuotationDetails::where('quotationMasterID', $quotationMasterID)
            ->get();

        if (!empty($fetchQuotationDetails)) {
            foreach ($fetchQuotationDetails as $bookDetail) {
                $bookDetail['timesReferred'] = $quotationMasterData->timesReferred;
            }
        }

        $salesQuotationDetailArray = $fetchQuotationDetails->toArray();

        $storeSalesQuotationDetailHistory = QuotationDetailsRefferedback::insert($salesQuotationDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $quotationMasterID)
            ->where('companySystemID', $quotationMasterData->companySystemID)
            ->where('documentSystemID', $quotationMasterData->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $quotationMasterData->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $quotationMasterID)
            ->where('companySystemID', $quotationMasterData->companySystemID)
            ->where('documentSystemID', $quotationMasterData->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $quotationMasterData->refferedBackYN = 0;
            $quotationMasterData->confirmedYN = 0;
            $quotationMasterData->confirmedByEmpSystemID = null;
            $quotationMasterData->confirmedByEmpID = null;
            $quotationMasterData->confirmedByName = null;
            $quotationMasterData->confirmedDate = null;
            $quotationMasterData->RollLevForApp_curr = 1;
            $quotationMasterData->save();
        }

        return $this->sendResponse($quotationMasterData->toArray(), 'Sales quotation amend successfully');
    }

    public function salesQuotationAudit(Request $request)
    {
        $input = $request->all();
        $quotationMasterID = $input['quotationMasterID'];
        $quotationMasterdata = $this->quotationMasterRepository->with(['created_by', 'confirmed_by', 'modified_by', 'approved_by' => function ($query) {
            $query->with('employee')
                ->whereIn('documentSystemID', [67, 68]);
        }, 'company'])->findWithoutFail($quotationMasterID);


        if (empty($quotationMasterdata)) {
            return $this->sendError('Sales quotation not found');
        }

        return $this->sendResponse($quotationMasterdata->toArray(), 'Sales quotation retrieved successfully');
    }


}
