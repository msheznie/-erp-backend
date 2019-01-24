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
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuotationMasterAPIRequest;
use App\Http\Requests\API\UpdateQuotationMasterAPIRequest;
use App\Models\CurrencyMaster;
use App\Models\CustomerAssigned;
use App\Models\CustomerMaster;
use App\Models\DocumentMaster;
use App\Models\ItemAssigned;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
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

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        if($documentMaster){
            $input['documentID'] = $documentMaster->documentID;
        }

        $customerData = CustomerMaster::where('customerCodeSystem', $input['customerSystemCode'])->first();

        if($customerData){
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
        if($transactionCurrencyData){
            $input['transactionCurrency'] = $transactionCurrencyData->CurrencyCode;
            $input['transactionExchangeRate'] = 1;
            $input['transactionCurrencyDecimalPlaces'] = $transactionCurrencyData->DecimalPlaces;
        }

        //updating local currency details
        $localCurrencyData = CurrencyMaster::where('currencyID', $input['companyLocalCurrencyID'])->first();
        if($localCurrencyData){
            $input['companyLocalCurrency'] = $localCurrencyData->CurrencyCode;
            $input['companyLocalCurrencyDecimalPlaces'] = $localCurrencyData->DecimalPlaces;
        }

        //updating reporting currency details
        $reportingCurrencyData = CurrencyMaster::where('currencyID', $input['companyLocalCurrencyID'])->first();
        if($reportingCurrencyData){
            $input['companyReportingCurrency'] = $reportingCurrencyData->CurrencyCode;
            $input['companyReportingCurrencyDecimalPlaces'] = $reportingCurrencyData->DecimalPlaces;
        }

        //updating customer GL update
        $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerSystemCode'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();
        if($customerGLCodeUpdate){

            $chartOfAccountData = ChartOfAccount::where('chartOfAccountSystemID', $customerGLCodeUpdate->custGLAccountSystemID)->first();
            if($chartOfAccountData){
                $input['customerReceivableAutoID'] = $chartOfAccountData->chartOfAccountSystemID;
                $input['customerReceivableGLAccount'] = $chartOfAccountData->AccountCode;
                $input['customerReceivableDescription'] = $chartOfAccountData->AccountDescription;
                $input['customerReceivableType'] = $chartOfAccountData->controlAccounts;
            }

        }

        $customerCurrency = customercurrency::where('customerCodeSystem', $input['customerSystemCode'])->where('isDefault', -1)->first();
        if($customerCurrency){

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
            ->orderBy('quotationMasterID', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
        }

        $quotationCode = ($company->CompanyID . '\\' . 'QUO' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
        $input['quotationCode'] = $quotationCode;
        $input['serialNumber'] = $lastSerialNumber;

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
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

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

        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        /** @var QuotationMaster $quotationMaster */
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMaster)) {
            return $this->sendError('Quotation Master not found');
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

        $customerData = CustomerMaster::where('customerCodeSystem', $input['customerSystemCode'])->first();

        if($customerData){
            $input['customerCode'] = $customerData->CutomerCode;
            $input['customerName'] = $customerData->CustomerName;
            $input['customerAddress'] = $customerData->customerAddress1;
            //$input['customerTelephone'] = $customerData->CutomerCode;
            //$input['customerFax'] = $customerData->CutomerCode;
            //$input['customerEmail'] = $customerData->CutomerCode;
        }

        //updating transaction currency details
        $transactionCurrencyData = CurrencyMaster::where('currencyID', $input['transactionCurrencyID'])->first();
        if($transactionCurrencyData){
            $input['transactionCurrency'] = $transactionCurrencyData->CurrencyCode;
            $input['transactionExchangeRate'] = 1;
            $input['transactionCurrencyDecimalPlaces'] = $transactionCurrencyData->DecimalPlaces;
        }


        //updating customer GL update
        $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerSystemCode'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();
        if($customerGLCodeUpdate){

            $chartOfAccountData = ChartOfAccount::where('chartOfAccountSystemID', $customerGLCodeUpdate->custGLAccountSystemID)->first();
            if($chartOfAccountData){
                $input['customerReceivableAutoID'] = $chartOfAccountData->chartOfAccountSystemID;
                $input['customerReceivableGLAccount'] = $chartOfAccountData->AccountCode;
                $input['customerReceivableDescription'] = $chartOfAccountData->AccountDescription;
                $input['customerReceivableType'] = $chartOfAccountData->controlAccounts;
            }

        }

        $customerCurrency = customercurrency::where('customerCodeSystem', $input['customerSystemCode'])->where('isDefault', -1)->first();
        if($customerCurrency){

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


        $input['modifiedDateTime'] = Carbon::now();
        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserName'] = $employee->empName;

        $quotationMaster = $this->quotationMasterRepository->update($input, $id);

        return $this->sendResponse($quotationMaster->toArray(), 'Quotation master updated successfully');
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

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
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

        $quotationMaster = QuotationMaster::whereIn('companySystemID', $childCompanies);

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

        $items = ItemAssigned::where('companySystemID', $companySystemID);
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

}
