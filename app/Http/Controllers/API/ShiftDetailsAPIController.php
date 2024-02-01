<?php
/**
 * =============================================
 * -- File Name : ShiftDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Shift Details
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - January 2019
 * -- Description : This file contains the all CRUD for Shift Details
 * -- REVISION HISTORY
 * -- Date: 14-January 2018 By: Fayas Description: Added new functions named as getPosShiftDetails()
 */
namespace App\Http\Controllers\API;

use App\helper\CreateExcel;
use App\helper\inventory;
use App\helper\ItemTracking;
use App\helper\TaxService;
use App\Http\Requests\API\CreateShiftDetailsAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceDirectAPIRequest;
use App\Http\Requests\API\UpdateShiftDetailsAPIRequest;
use App\Jobs\BankLedgerInsert;
use App\Jobs\GeneralLedgerInsert;
use App\Jobs\ItemLedgerInsert;
use App\helper\Helper;
use App\Jobs\POSItemLedgerInsert;
use App\Jobs\TaxLedgerInsert;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\Counter;
use App\Models\CurrencyDenomination;
use App\Models\CustomerAssigned;
use App\Models\CustomerCatalogDetail;
use App\Models\CustomerCurrency;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerMaster;
use App\Models\DeliveryOrder;
use App\Models\DocumentApproved;
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GposInvoice;
use App\Models\GposPaymentGlConfigDetail;
use App\Models\ItemAssigned;
use App\Models\ItemIssueMaster;
use App\Models\ItemMaster;
use App\Models\OutletUsers;
use App\Models\POSBankGLEntries;
use App\Models\POSFinanceLog;
use App\Models\POSGLEntries;
use App\Models\POSInsufficientItems;
use App\Models\POSInvoiceSource;
use App\Models\POSItemGLEntries;
use App\Models\POSSOURCECustomerMaster;
use App\Models\POSSourceMenuSalesMaster;
use App\Models\POSSourcePaymentGlConfig;
use App\Models\POSSOURCEPaymentGlConfigDetail;
use App\Models\POSSourceSalesReturn;
use App\Models\POSSOURCEShiftDetails;
use App\Models\POSSOURCETaxMaster;
use App\Models\POSTaxGLEntries;
use App\Models\PurchaseReturn;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use App\Models\SegmentMaster;
use App\Models\ShiftDetails;
use App\Models\StockTransfer;
use App\Models\Taxdetail;
use App\Models\TaxMaster;
use App\Models\TaxVatCategories;
use App\Models\VatSubCategoryType;
use App\Models\WarehouseMaster;
use App\Repositories\CustomerInvoiceDirectRepository;
use App\Repositories\CustomerInvoiceItemDetailsRepository;
use App\Repositories\SalesReturnRepository;
use App\Repositories\ShiftDetailsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use DB;
use Response;

/**
 * Class ShiftDetailsController
 * @package App\Http\Controllers\API
 */

class ShiftDetailsAPIController extends AppBaseController
{
    /** @var  ShiftDetailsRepository */
    private $shiftDetailsRepository;
    private $customerInvoiceDirectRepository;


    public function __construct(ShiftDetailsRepository $shiftDetailsRepo, CustomerInvoiceDirectRepository $customerInvoiceDirectRepo, CustomerInvoiceItemDetailsRepository $customerInvoiceItemDetailsRepo, SalesReturnRepository $salesReturnRepo)
    {
        $this->shiftDetailsRepository = $shiftDetailsRepo;
        $this->customerInvoiceDirectRepository = $customerInvoiceDirectRepo;
        $this->customerInvoiceItemDetailsRepository = $customerInvoiceItemDetailsRepo;

        $this->salesReturnRepository = $salesReturnRepo;

    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/shiftDetails",
     *      summary="Get a listing of the ShiftDetails.",
     *      tags={"ShiftDetails"},
     *      description="Get all ShiftDetails",
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
     *                  @SWG\Items(ref="#/definitions/ShiftDetails")
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
        $this->shiftDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->shiftDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $shiftDetails = $this->shiftDetailsRepository->all();

        return $this->sendResponse($shiftDetails->toArray(), 'Shift Details retrieved successfully');
    }

    /**
     * @param CreateShiftDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/shiftDetails",
     *      summary="Store a newly created ShiftDetails in storage",
     *      tags={"ShiftDetails"},
     *      description="Store ShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ShiftDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ShiftDetails")
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
     *                  ref="#/definitions/ShiftDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateShiftDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $messages = array(
            'wareHouseID.required'   => 'The outlet field is required.'
        );

        $validator = \Validator::make($input, [
            'wareHouseID' => 'required|numeric|min:1',
            'counterID' => 'required|numeric|min:1',
            'companyID' => 'required'
        ],$messages);


        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        $employee = \Helper::getEmployeeInfo();

        $counterCheck = ShiftDetails::where('isClosed',0)
                                        ->where('wareHouseID',$input['wareHouseID'])
                                        ->where('counterID',$input['counterID'])
                                        ->with(['counter','user'])
                                        ->first();

        if(!empty($counterCheck)){
            return $this->sendError('Already a shift is going on with counter [ '.$counterCheck->counter->counterCode.' ] by ' .$counterCheck->user->empName,500);
        }

        $shift = ShiftDetails::where('isClosed',0)
                            ->where('empID',$employee->employeeSystemID)
                            ->where('wareHouseID',$input['wareHouseID'])
                            ->with(['counter','user'])
                            ->first();

        if(!empty($shift)){
            return $this->sendError('You cannot start new shift, Already a shift is going on with counter [ '.$shift->counter->counterCode.' ]',500);
        }

        $input['companyCode'] = \Helper::getCompanyById($input['companyID']);

        $company  = Company::with(['localcurrency','reportingcurrency'])->find($input['companyID']);
        if(empty($company)){
            return $this->sendError('Company not found');
        }

        $input['empID'] = $employee->employeeSystemID;
        $input['startTime'] = now();

        if(isset($input['startingBalance_transaction'])){
            $input['startingBalance_local'] = $input['startingBalance_transaction'];
        }else{
            $input['startingBalance_transaction'] = 0;
            $input['startingBalance_local'] = 0;
        }

        $input['transactionCurrencyID'] = $company->localCurrencyID;
        $input['companyLocalCurrencyID'] = $company->localCurrencyID;
        $input['companyReportingCurrencyID'] = $company->reportingCurrency;

        if($company->localcurrency){
            $input['transactionCurrencyDecimalPlaces'] = $company->localcurrency->DecimalPlaces;
            $input['companyLocalCurrencyDecimalPlaces'] = $company->localcurrency->DecimalPlaces;
            $input['transactionCurrency'] = $company->localcurrency->CurrencyCode;
            $input['companyLocalCurrency'] = $company->localcurrency->CurrencyCode;
        }

        if($company->reportingcurrency){
            $input['companyReportingCurrencyDecimalPlaces'] = $company->reportingcurrency->DecimalPlaces;
            $input['companyReportingCurrency'] = $company->reportingcurrency->CurrencyCode;
        }

        $currencyCon = \Helper::currencyConversion($input['companyID'],$input['transactionCurrencyID'],$input['transactionCurrencyID'],$input['startingBalance_transaction']);

        $input['startingBalance_reporting'] = round($currencyCon['reportingAmount'],$input['companyReportingCurrencyDecimalPlaces']);
        $input['transactionExchangeRate'] = $currencyCon['trasToLocER'];
        $input['companyLocalExchangeRate'] = $currencyCon['trasToLocER'];
        $input['companyReportingExchangeRate'] = $currencyCon['trasToRptER'];

        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['createdUserName'] = $employee->empName;

        $input['timestamp'] = now();

        $shiftDetails = $this->shiftDetailsRepository->create($input);
        //return $this->sendResponse($input, 'Shift Details saved successfully');
        return $this->sendResponse($shiftDetails->toArray(), 'Shift Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/shiftDetails/{id}",
     *      summary="Display the specified ShiftDetails",
     *      tags={"ShiftDetails"},
     *      description="Get ShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ShiftDetails",
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
     *                  ref="#/definitions/ShiftDetails"
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
        /** @var ShiftDetails $shiftDetails */
        $shiftDetails = $this->shiftDetailsRepository->findWithoutFail($id);

        if (empty($shiftDetails)) {
            return $this->sendError('Shift Details not found');
        }

        return $this->sendResponse($shiftDetails->toArray(), 'Shift Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateShiftDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/shiftDetails/{id}",
     *      summary="Update the specified ShiftDetails in storage",
     *      tags={"ShiftDetails"},
     *      description="Update ShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ShiftDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ShiftDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ShiftDetails")
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
     *                  ref="#/definitions/ShiftDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateShiftDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($request->all(), ['user', 'counter','outlet']);
        $input = $this->convertArrayToValue($input);

        $messages = array(
            'wareHouseID.required'   => 'The outlet field is required.'
        );

        /** @var ShiftDetails $shiftDetails */
        $shiftDetails = $this->shiftDetailsRepository->findWithoutFail($id);

        if (empty($shiftDetails)) {
            return $this->sendError('Shift not found');
        }


        $validator = \Validator::make($input, [
            'wareHouseID' => 'required|numeric|min:1',
            'counterID' => 'required|numeric|min:1',
            'companyID' => 'required',
            'endingBalance_transaction' => 'required|numeric|min:0.001'
        ],$messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }


        $checkHoldInvoices = GposInvoice::where('shiftID',$id)->where('isHold',1)->count();

        if($checkHoldInvoices > 0){
            return $this->sendError('Please close all the pending hold bills. '.$checkHoldInvoices.' bills found!');
        }

        $input['endingBalance_local'] =  $input['endingBalance_transaction'];


        $currencyConvert = \Helper::convertAmountToLocalRpt(207,$shiftDetails->shiftID,$input['endingBalance_transaction']);
        $input['endingBalance_reporting'] = round($currencyConvert['reportingAmount'],$shiftDetails->companyReportingCurrencyDecimalPlaces);

        $input['different_transaction'] = round(($input['endingBalance_transaction'] - $shiftDetails->startingBalance_transaction),$shiftDetails->transactionCurrencyDecimalPlaces);
        $input['different_local'] = round(($input['endingBalance_transaction'] - $shiftDetails->startingBalance_transaction),$shiftDetails->transactionCurrencyDecimalPlaces);
        $input['different_local_reporting'] = round(($input['endingBalance_reporting'] - $shiftDetails->startingBalance_reporting),$shiftDetails->companyReportingCurrencyDecimalPlaces);

        $input['endTime'] = now();
        $employee = \Helper::getEmployeeInfo();

        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        $input['modifiedUserName'] = $employee->empName;
        $input['timestamp'] = now();
        $shiftDetails = $this->shiftDetailsRepository->update($input, $id);
        return $this->sendResponse($shiftDetails->toArray(), 'ShiftDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/shiftDetails/{id}",
     *      summary="Remove the specified ShiftDetails from storage",
     *      tags={"ShiftDetails"},
     *      description="Delete ShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ShiftDetails",
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
        /** @var ShiftDetails $shiftDetails */
        $shiftDetails = $this->shiftDetailsRepository->findWithoutFail($id);

        if (empty($shiftDetails)) {
            return $this->sendError('Shift Details not found');
        }

        $shiftDetails->delete();

        return $this->sendResponse($id, 'Shift Details deleted successfully');
    }

    public function getPosSourceShiftDetails(Request $request) {

        $posTypeID = $request->posTypeID;
        $postedShifts = POSFinanceLog::groupBy('shiftId')->where('status', 2)->get();
        $postedShifts = collect($postedShifts)->pluck('shiftId');
        $posSourceShiftDetails = POSSOURCEShiftDetails::selectRaw('shiftID as value,CONCAT(startTime, " | " ,endTime, " - ", createdUserName) as label')->where('posType', $posTypeID)->whereNotIn('shiftID',$postedShifts)->get();


        $output = array(
            'posSourceShiftDetails' => $posSourceShiftDetails,
        );

        return $this->sendResponse($output, "Shift Details retrieved successfully");
    }

    public function getPosCustomerMasterDetails(Request $request) {

        $shiftID = $request->shiftID;
        $companySystemID = $request->companyId;
        $shiftDetailLabels = POSSOURCEShiftDetails::selectRaw('startTime ,endTime, createdUserName, posType')->where('shiftID', $shiftID)->first();

        if(empty($shiftDetailLabels)){
            return $this->sendError('Shift Details not found',500);
        }

        if($shiftDetailLabels->posType == 1) {
            $posCustomers = DB::table('pos_source_invoice')
                ->selectRaw('pos_source_customermaster.customerSystemCode, pos_source_customermaster.customerName, pos_source_customermaster.customerAutoID')
                ->join('pos_source_customermaster', 'pos_source_customermaster.customerAutoID', '=', 'pos_source_invoice.customerID')
                ->where('pos_source_invoice.shiftID', $shiftID)
                ->where('pos_source_invoice.isCreditSales', 1)
                ->where('pos_source_customermaster.erp_customer_master_id', 0)
                ->where('pos_source_customermaster.companyID', $companySystemID)
                ->get();
        } else{
            $posCustomers = DB::table('pos_source_menusalesmaster')
                ->selectRaw('pos_source_customermaster.customerSystemCode, pos_source_customermaster.customerName, pos_source_customermaster.customerAutoID')
                ->join('pos_source_customermaster', 'pos_source_customermaster.customerAutoID', '=', 'pos_source_menusalesmaster.customerID')
                ->where('pos_source_menusalesmaster.shiftID', $shiftID)
                ->where('pos_source_menusalesmaster.isCreditSales', 1)
                ->where('pos_source_customermaster.erp_customer_master_id', 0)
                ->where('pos_source_customermaster.companyID', $companySystemID)
                ->get();
        }


        $customers = CustomerAssigned::selectRaw('customerassigned.customerCodeSystem as value,CONCAT(customerassigned.CutomerCode, " | " ,customerassigned.CustomerName) as label')->where('companySystemID', $companySystemID)
            ->leftJoin('pos_source_customermaster', 'customerCodeSystem', '=', 'erp_customer_master_id')
            ->whereNull('pos_source_customermaster.erp_customer_master_id')
            ->get();


        $posTaxes = DB::table('pos_source_taxmaster')
            ->selectRaw('taxMasterAutoID, taxDescription, taxShortCode, taxType')
            ->whereIn('pos_source_taxmaster.taxType', [0,1])
            ->where('pos_source_taxmaster.erp_tax_master_id', 0)
            ->where('pos_source_taxmaster.companyID', $companySystemID)
            ->get();

        $taxesVat = TaxVatCategories::selectRaw('taxVatSubCategoriesAutoID as value, subCategoryDescription as label')
            ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'erp_tax_vat_sub_categories.taxMasterAutoID')
            ->where('erp_taxmaster_new.companySystemID', $companySystemID)
            ->where('erp_taxmaster_new.taxCategory', 2)
            ->where('erp_tax_vat_sub_categories.isActive', 1)
            ->get();

        $taxesOther = DB::table('erp_taxmaster_new')->selectRaw('taxMasterAutoID as value, taxDescription as label')
            ->where('erp_taxmaster_new.companySystemID', $companySystemID)
            ->where('erp_taxmaster_new.taxCategory', 1)
            ->where('erp_taxmaster_new.isActive', 1)
            ->get();

        $posPayments = DB::table('pos_source_paymentglconfigdetail')
            ->selectRaw('ID, description, GLCode')
            ->join('pos_source_paymentglconfigmaster', 'pos_source_paymentglconfigmaster.autoID', '=', 'pos_source_paymentglconfigdetail.paymentConfigMasterID')
            ->where('pos_source_paymentglconfigdetail.companyID', $companySystemID)
            ->where('pos_source_paymentglconfigdetail.erp_bank_acc_id', 0)
            ->get();
        $isAvailable = 0;

        foreach ($posPayments as $pt){
            $pt->dropOptions = BankAccount::selectRaw('bankAccountAutoID as value, CONCAT(bankShortCode, " | " ,AccountNo) as label')->where('companySystemID', $companySystemID)->where('chartOfAccountSystemID', $pt->GLCode)
                ->get();
            $pt->isDrop = 1;
            if($pt->dropOptions->isEmpty()) {
                $pt->isDrop = 0;
            }
            else {
                $isAvailable = 1;
            }
        }

        $posLog = DB::table('pos_finance_log')
            ->selectRaw('shiftId, status')
            ->where('pos_finance_log.shiftId', $shiftID)
            ->where('pos_finance_log.status', 1)
            ->first();

        $isPosInsufficient = 0;
        $posInsufficient = DB::table('pos_item_gl')
            ->selectRaw('*')
            ->join('pos_finance_log', 'pos_finance_log.shiftId', '=', 'pos_item_gl.shiftId')
            ->where('pos_item_gl.shiftId', $shiftID)
            ->where('pos_finance_log.status', 1)
            ->get();
        if(!$posInsufficient->isEmpty()) {
            $isPosInsufficient = 1;
        }

        $output = array(
            'shiftDetailLabels' => $shiftDetailLabels,
            'posCustomers' => $posCustomers,
            'customers' => $customers,
            'posTaxes' => $posTaxes,
            'taxesVat' => $taxesVat,
            'taxesOther' => $taxesOther,
            'posPayments' => $posPayments,
            'isAvailable' => $isAvailable,
            'posLog' => $posLog,
            'posInsufficient' => $posInsufficient,
            'isPosInsufficient' => $isPosInsufficient
        );

        return $this->sendResponse($output, "Shift Details retrieved successfully");
    }

    public function postPosCustomerMapping(Request $request) {

        $cusPOSId = $request->cusPOSId;
        $cusERPId = $request->cusERPId;

        $isExist = POSSOURCECustomerMaster::where('erp_customer_master_id', $cusERPId)->first();
        if(!empty($isExist)){
            return $this->sendError("ERP customer is already linked");
        }

        $output = POSSOURCECustomerMaster::where('customerAutoID', $cusPOSId)->update(['erp_customer_master_id' => $cusERPId]);

        return $this->sendResponse($output, "Shift Details retrieved successfully");
    }

    public function postPosTaxMapping(Request $request) {

        $taxPOSId = $request->taxPOSId;
        $taxERPId = $request->taxERPId;
        $taxType = $request->taxType;

        if($taxType == 0) {
            $tax = TaxVatCategories::find($taxERPId);
            if (empty($tax)) {
                return $this->sendError("No vat sub category found");
            }
            $output = POSSOURCETaxMaster::where('taxMasterAutoID', $taxPOSId)->update(['erp_tax_master_id' => $tax->taxMasterAutoID, 'erp_vat_sub_category' => $taxERPId]);
        } else {
            $output = POSSOURCETaxMaster::where('taxMasterAutoID', $taxPOSId)->update(['erp_tax_master_id' => $taxERPId, 'erp_vat_sub_category' => 0]);
        }

        return $this->sendResponse($output, "Shift Details retrieved successfully");
    }

    public function postPosPayMapping(Request $request) {

        $payPOSId = $request->payPOSId;
        $payERPId = $request->payERPId;
        $output = POSSOURCEPaymentGlConfigDetail::where('ID', $payPOSId)->update(['erp_bank_acc_id' => $payERPId]);

        return $this->sendResponse($output, "Shift Details retrieved successfully");
    }

    public function insufficientItems(Request $request){

        $shiftId = $request->shiftId;

        return $this->getAllInsufficientItems($shiftId);

    }

    public function exportInsufficientItems(Request $request){

        $shiftId = $request->shiftId;

        $items = POSInsufficientItems::where('shiftId', $shiftId)->get();
        $data = array();
        $x = 0;
        foreach ($items  as $val) {
            if($val->insufficientQty > 0) {
                $x++;
                $data[$x]['Item Code'] = $val->primaryCode;
                $data[$x]['Qty Needed'] = $val->qty;
                $data[$x]['Available Qty'] = $val->availableQty;
                $data[$x]['Insufficient Qty'] = $val->insufficientQty;
                $data[$x]['Warehouse'] = isset($val->warehouse->wareHouseDescription) ? $val->warehouse->wareHouseDescription : null;
            }
        }
        $shiftDetails = POSSOURCEShiftDetails::where('shiftID',$shiftId)->first();

        $companyMaster = Company::find(isset($shiftDetails->companyID)?$shiftDetails->companyID:null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );

        $fileName = 'Inventory Availability';
        $path = 'pos/sales_transaction/excel/';
        $type = 'xls';
        $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

        if($basePath == '')
        {
            return $this->sendError('Unable to export excel');
        }
        else
        {
            return $this->sendResponse($basePath, trans('custom.success_export'));
        }

    }


    public function getAllInsufficientItems($shiftId){
        $isInsufficient = 0;

        $shiftDetails = POSSOURCEShiftDetails::where('shiftID',$shiftId)->first();

        POSInsufficientItems::where('shiftId', $shiftId)->delete();

        $qtyArray = array();
        if($shiftDetails->posType == 1) {
            $invItemsPLBS = DB::table('pos_source_invoicedetail')
                ->selectRaw('pos_source_invoicedetail.qty * itemassigned.wacValueLocal as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub,  financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, itemmaster.financeCategoryMaster as categoryID, SUM(pos_source_invoicedetail.qty) as qty, itemassigned.wacValueLocal as price, pos_source_invoicedetail.UOMID as uom, pos_source_invoice.wareHouseAutoID as wareHouseID, financeitemcategorysub.includePLForGRVYN as glYN, warehousemaster.wareHouseDescription')
                ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                ->join('itemassigned', 'itemassigned.itemCodeSystem', '=', 'itemmaster.itemCodeSystem')
                ->join('warehousemaster', 'warehousemaster.wareHouseSystemCode', '=', 'pos_source_invoice.wareHouseAutoID')
                ->where('pos_source_invoice.shiftID', $shiftId)
                ->where('itemassigned.companySystemID', $shiftDetails->companyID)
                ->groupBy('itemID')
                ->get();
        } else if ($shiftDetails->posType == 2){
            $invItemsPLBS = DB::table('pos_source_menusalesitems')
                ->selectRaw('(pos_source_menusalesitemdetails.qty * itemassigned.wacValueLocal * pos_source_menusalesitems.qty) as amount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalesitemdetails.itemAutoID as itemID, pos_source_menusalesitems.revenueGLAutoID as glCode,  itemmaster.financeCategoryMaster as categoryID, financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, financeitemcategorysub.includePLForGRVYN as glYN, SUM(pos_source_menusalesitemdetails.qty * pos_source_menusalesitems.qty) as qty, SUM(pos_source_menusalesitemdetails.cost * pos_source_menusalesitems.qty) as price, pos_source_menusalesitemdetails.UOMID as uom, pos_source_menusalesmaster.wareHouseAutoID as wareHouseID')
                ->join('pos_source_menusalesmaster', 'pos_source_menusalesmaster.menuSalesID', '=', 'pos_source_menusalesitems.menuSalesID')
                ->join('pos_source_menusalesitemdetails', 'pos_source_menusalesitemdetails.menuSalesItemID', '=', 'pos_source_menusalesitems.menuSalesItemID')
                ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_menusalesitemdetails.itemAutoID')
                ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                ->join('itemassigned', 'itemassigned.itemCodeSystem', '=', 'itemmaster.itemCodeSystem')
                ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                ->where('itemassigned.companySystemID', $shiftDetails->companyID)
                ->groupBy('itemID')
                ->get();
            
        }
        
        foreach ($invItemsPLBS as $gl) {

                $itemArray[] = array(
                    'shiftId' => $gl->shiftId,
                    'itemAutoId' => $gl->itemID,
                    'uom' => $gl->uom,
                    'qty' => $gl->qty,
                    'wareHouseId' => $gl->wareHouseID
                );


                    POSInsufficientItems::insert($itemArray);

                $sumQty = ErpItemLedger::where('itemSystemCode', $gl->itemID)->where('companySystemID', $shiftDetails->companyID)->where('wareHouseSystemCode', $gl->wareHouseID)->sum('inOutQty');
                $item = ItemMaster::where('itemCodeSystem', $gl->itemID)->where('primaryCompanySystemID', $shiftDetails->companyID)->first();
                if ($item) {
                    if ($item->financeCategoryMaster == 1) {
                            $remQty = $gl->qty - $sumQty;
                            POSInsufficientItems::where('shiftId', $gl->shiftId)->where('itemAutoId', $gl->itemID)->update(['insufficientQty' => $remQty, 'availableQty' => $sumQty, 'primaryCode' => $item->primaryCode]);
                            $isInsufficient = 1;
                    }
                }
            }


        $isInsufficientExist = false;

        $qtyArray = POSInsufficientItems::with(['warehouse'])->where('shiftId', $shiftId)->where('insufficientQty', '>', 0)->get();

        $qtyArrayLength = count($qtyArray);

        if ($qtyArrayLength > 0) {
            $isInsufficientExist = true;
        }

        $data['output'] = $qtyArray;
        $data['isExist'] = $isInsufficientExist;
        return $this->sendResponse($data, "Insufficient quantities retrieved successfully");
     }

    public function postGLEntries(Request $request){
        
        $shiftId = $request->shiftId;

        $db = isset($request->db) ? $request->db : "";

        $shiftDetails = POSSOURCEShiftDetails::where('shiftID',$shiftId)->first();

        if($shiftDetails->posType == 1) {

            $logged_user = \Helper::getEmployeeSystemID();

            $masterData = ['documentSystemID' => 110, 'autoID' => $shiftId, 'companySystemID' => $shiftDetails->companyID, 'employeeSystemID' => $logged_user, 'companyID' => $shiftDetails->companyCode];

            \Illuminate\Support\Facades\DB::beginTransaction();

            try {

                $invoices = DB::table('pos_source_invoice')
                    ->selectRaw('pos_source_invoice.*')
                    ->where('pos_source_invoice.shiftID', $shiftId)
                    ->where('pos_source_invoice.isCreditSales', 1)
                    ->get();
                foreach ($invoices as $invoice) {

                    $companyFinanceYear = CompanyFinanceYear::where('bigginingDate', "<=", $invoice->invoiceDate)->where('endingDate', ">=", $invoice->invoiceDate)->where('companySystemID', $shiftDetails->companyID)->where('isActive', -1)->first();

                    $companyFinancePeriod = CompanyFinancePeriod::where('dateFrom', "<=", $invoice->invoiceDate)->where('dateTo', ">=", $invoice->invoiceDate)->where('companySystemID', $shiftDetails->companyID)->where('isActive', -1)->first();

                    if (!isset($companyFinancePeriod->companyFinancePeriodID) || is_null($companyFinancePeriod->companyFinancePeriodID)) {
                        return $this->sendError('Financial period is not found or inactive', 500);
                    }

                    if (!isset($companyFinancePeriod->companyFinanceYearID) || is_null($companyFinancePeriod->companyFinanceYearID)) {
                        return $this->sendError('Financial year is not found or inactive', 500);
                    }

                    $customerID = null;
                    $serviceLineSystemID = null;
                    $serviceLineCode = null;
                    $wareHouseID = null;
                    $customerID = POSSOURCECustomerMaster::where('customerAutoID', $invoice->customerID)->first();
                    if ($customerID) {
                        $customerID = $customerID->erp_customer_master_id;
                    }
                    $segments = DB::table('pos_source_shiftdetails')
                        ->selectRaw('pos_source_shiftdetails.segmentID as segmentID, serviceline.ServiceLineCode as serviceLineCode')
                        ->join('serviceline', 'serviceline.serviceLineSystemID', '=', 'pos_source_shiftdetails.segmentID')
                        ->where('pos_source_shiftdetails.shiftID', $shiftId)
                        ->first();

                    if ($segments) {
                        $serviceLineSystemID = $segments->segmentID;
                        $serviceLineCode = $segments->serviceLineCode;
                    }
                    $wareHouse = DB::table('pos_source_shiftdetails')
                        ->selectRaw('pos_source_shiftdetails.wareHouseID as wareHouseID')
                        ->where('pos_source_shiftdetails.shiftID', $shiftId)
                        ->first();
                    if ($wareHouse) {
                        $wareHouseID = $wareHouse->wareHouseID;
                    }

                    $companyCurrency = \Helper::companyCurrency($shiftDetails->companyID);

                    $input = ['bookingDate' => $invoice->invoiceDate, 'comments' => "Inv Created by GPOS System. Bill No: " . $invoice->invoiceCode, 'companyFinancePeriodID' => $companyFinancePeriod->companyFinancePeriodID, 'companyFinanceYearID' => $companyFinanceYear->companyFinanceYearID, 'companyID' => $shiftDetails->companyID, 'custTransactionCurrencyID' => $companyCurrency->localcurrency->currencyID, 'customerID' => $customerID, 'date_of_supply' => $invoice->invoiceDate, 'invoiceDueDate' => $invoice->invoiceDate, 'isPerforma' => 2, 'serviceLineSystemID' => $serviceLineSystemID, 'serviceLineCode' => $serviceLineCode, 'wareHouseSystemCode' => $wareHouseID, 'customerInvoiceNo' => $invoice->invoiceCode, 'bankAccountID' => 1, 'bankID' => 2];


                    if (isset($input['isPerforma']) && $input['isPerforma'] == 2) {
                        $wareHouse = isset($input['wareHouseSystemCode']) ? $input['wareHouseSystemCode'] : 0;
                        if (!$wareHouse) {
                            return $this->sendError('Please select a warehouse', 500);
                        }
                    }

                    if (!isset($input['custTransactionCurrencyID']) || (isset($input['custTransactionCurrencyID']) && ($input['custTransactionCurrencyID'] == 0 || $input['custTransactionCurrencyID'] == null))) {
                        return $this->sendError('Please select a currency', 500);
                    }
                    $companyFinanceYearID = $input['companyFinanceYearID'];

                    if (!isset($input['companyFinanceYearID']) || is_null($input['companyFinanceYearID'])) {
                        return $this->sendError('Financial year is not selected', 500);
                    }

                    if (!isset($input['companyFinancePeriodID']) || is_null($input['companyFinancePeriodID'])) {
                        return $this->sendError('Financial period is not selected', 500);
                    }

                    $company = Company::where('companySystemID', $input['companyID'])->first()->toArray();

                    $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $companyFinanceYearID)->first();
                    $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
                    $FYPeriodDateFrom = $companyfinanceperiod->dateFrom;
                    $FYPeriodDateTo = $companyfinanceperiod->dateTo;
                    $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
                    if (empty($customer)) {
                        return $this->sendError('Customer not found', 500);
                    }

                    /*exchange added*/
                    $input['custTransactionCurrencyER'] = $invoice->transactionExchangeRate;
                    $input['companyReportingCurrencyID'] = $invoice->companyReportingCurrencyID;
                    $input['companyReportingER'] = $invoice->companyReportingExchangeRate;
                    $input['localCurrencyID'] = $invoice->companyLocalCurrencyID;
                    $input['localCurrencyER'] = $invoice->companyLocalExchangeRate;

                    $bank = BankAssign::select('bankmasterAutoID')
                        ->where('companySystemID', $input['companyID'])
                        ->where('isDefault', -1)
                        ->first();


                    if (isset($input['isPerforma']) && ($input['isPerforma'] == 2 || $input['isPerforma'] == 3 || $input['isPerforma'] == 4 || $input['isPerforma'] == 5)) {
                        $serviceLine = isset($input['serviceLineSystemID']) ? $input['serviceLineSystemID'] : 0;
                        if (!$serviceLine) {
                            return $this->sendError('Please select a Segment', 500);
                        }
                        $segment = SegmentMaster::find($input['serviceLineSystemID']);
                        $input['serviceLineCode'] = isset($segment->ServiceLineCode) ? $segment->ServiceLineCode : null;
                    }

                    $lastSerial = CustomerInvoiceDirect::where('companySystemID', $input['companyID'])
                        ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                        ->orderBy('serialNo', 'desc')
                        ->first();

                    $lastSerialNumber = 1;
                    if ($lastSerial) {
                        $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                    }

                    $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));
                    $bookingInvCode = ($company['CompanyID'] . '\\' . $y . '\\INV' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

                    $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerID'])
                        ->where('companySystemID', $input['companyID'])
                        ->first();
                    if ($customerGLCodeUpdate) {
                        $input['customerVATEligible'] = $customerGLCodeUpdate->vatEligible;
                    }

                    $company = Company::where('companySystemID', $input['companyID'])->first();

                    if ($company) {
                        $input['vatRegisteredYN'] = $company->vatRegisteredYN;
                    }

                    $input['documentID'] = "INV";
                    $input['documentSystemiD'] = 20;
                    $input['bookingInvCode'] = $bookingInvCode;
                    $input['serialNo'] = $lastSerialNumber;
                    $input['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
                    $input['FYEnd'] = $CompanyFinanceYear->endingDate;
                    $input['FYPeriodDateFrom'] = $FYPeriodDateFrom;
                    $input['FYPeriodDateTo'] = $FYPeriodDateTo;
                    $input['invoiceDueDate'] = Carbon::parse($input['invoiceDueDate'])->format('Y-m-d') . ' 00:00:00';
                    $input['bookingDate'] = Carbon::parse($input['bookingDate'])->format('Y-m-d') . ' 00:00:00';
                    $input['date_of_supply'] = Carbon::parse($input['date_of_supply'])->format('Y-m-d') . ' 00:00:00';
                    $input['customerInvoiceDate'] = $input['bookingDate'];
                    $input['companySystemID'] = $input['companyID'];
                    $input['companyID'] = $company['CompanyID'];
                    $input['customerGLCode'] = $customer->custGLaccount;
                    $input['customerGLSystemID'] = $customer->custGLAccountSystemID;
                    $input['documentType'] = 11;
                    $input['isPOS'] = 1;
                    $input['createdUserID'] = \Helper::getEmployeeID();
                    $input['createdPcID'] = getenv('COMPUTERNAME');
                    $input['modifiedUser'] = \Helper::getEmployeeID();
                    $input['modifiedPc'] = getenv('COMPUTERNAME');
                    $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                    $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();


                    $curentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
                    if ($input['bookingDate'] > $curentDate) {
                        return $this->sendResponse('e', 'Document date cannot be greater than current date');
                    }

                    $customerInvoiceDirects = $this->customerInvoiceDirectRepository->create($input);


                    $items = DB::table('pos_source_invoicedetail')
                        ->selectRaw('pos_source_invoicedetail.*')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->where('pos_source_invoicedetail.invoiceID', $invoice->invoiceID)
                        ->get();

                    foreach ($items as $item) {
                        $input2 = ['customerCatalogDetailID' => 0, 'customerCatalogMasterID' => 0, 'itemCode' => $item->itemAutoID, 'qtyIssued' => $item->defaultQty, 'issueCostLocal' => $item->companyLocalAmount, 'issueCostRpt' => $item->companyReportingAmount, 'qtyIssuedDefaultMeasure' => $item->defaultQty, 'sellingCost' => $item->price, 'sellingCostAfterMargin' => $item->companyLocalAmount / $item->defaultQty, 'sellingCostAfterMarginLocal' => $item->companyLocalAmount / $item->defaultQty, 'sellingCostAfterMarginRpt' => $item->companyReportingAmount / $item->defaultQty, 'sellingTotal' => $item->companyLocalAmount, 'sellingCurrencyER' => $item->transactionExchangeRate, 'sellingCurrencyID' => $item->transactionCurrencyID, 'salesPrice' => $item->price, 'discountAmount' => $item->discountAmount];

                        $input2['companySystemID'] = $customerInvoiceDirects->companySystemID;
                        $input2['custInvoiceDirectAutoID'] = $customerInvoiceDirects->custInvoiceDirectAutoID;

                        $companySystemID = $input2['companySystemID'];

                        $itemAssigned = ItemAssigned::with(['item_master'])
                            ->where('itemCodeSystem', $item->itemAutoID)
                            ->where('companySystemID', $companySystemID)
                            ->first();
                        if (empty($itemAssigned)) {
                            return $this->sendError('Item not found');
                        }

                        $customerInvoiceDirect = CustomerInvoiceDirect::find($input2['custInvoiceDirectAutoID']);

                        if (empty($customerInvoiceDirect)) {
                            return $this->sendError('Customer Invoice Direct Not Found');
                        }

                        $input2['itemCodeSystem'] = $itemAssigned->itemCodeSystem;
                        $input2['itemPrimaryCode'] = $itemAssigned->itemPrimaryCode;
                        $input2['itemDescription'] = $itemAssigned->itemDescription;
                        $input2['itemUnitOfMeasure'] = $itemAssigned->itemUnitOfMeasure;

                        $input2['unitOfMeasureIssued'] = $itemAssigned->itemUnitOfMeasure;
                        $input2['trackingType'] = isset($itemAssigned->item_master->trackingType) ? $itemAssigned->item_master->trackingType : null;
                        $input2['convertionMeasureVal'] = 1;

                        if (!isset($input2['qtyIssued'])) {
                            $input2['qtyIssued'] = 0;
                            $input2['qtyIssuedDefaultMeasure'] = 0;
                        }

                        $input2['comments'] = '';
                        $input2['itemFinanceCategoryID'] = $itemAssigned->financeCategoryMaster;
                        $input2['itemFinanceCategorySubID'] = $itemAssigned->financeCategorySub;

                        $input2['localCurrencyID'] = $customerInvoiceDirect->localCurrencyID;
                        $input2['localCurrencyER'] = $customerInvoiceDirect->localCurrencyER;


                        $data = array('companySystemID' => $companySystemID,
                            'itemCodeSystem' => $input2['itemCodeSystem'],
                            'wareHouseId' => $customerInvoiceDirect->wareHouseSystemCode);

                        $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

                        $input2['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
                        $input2['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                        $input2['currentStockQtyInDamageReturn'] = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];


                        $input2['issueCostLocal'] = $item->totalCost / $item->defaultQty;
                        $input2['issueCostRpt'] = ($item->totalCost / $item->defaultQty) / $customerInvoiceDirect->companyReportingER;


                        $input2['issueCostLocalTotal'] = $item->totalCost;

                        $input2['reportingCurrencyID'] = $customerInvoiceDirect->companyReportingCurrencyID;
                        $input2['reportingCurrencyER'] = $customerInvoiceDirect->companyReportingER;

                        $input2['issueCostRptTotal'] = $item->totalCost / $customerInvoiceDirect->companyReportingER;
                        $input2['marginPercentage'] = 0;

                        $input2['sellingCurrencyID'] = $customerInvoiceDirect->custTransactionCurrencyID;
                        $input2['sellingCurrencyER'] = $customerInvoiceDirect->custTransactionCurrencyER;
                        if ((isset($input2['customerCatalogDetailID']) && $input2['customerCatalogDetailID'] > 0)) {
                            $catalogDetail = CustomerCatalogDetail::find($input2['customerCatalogDetailID']);

                            if (empty($catalogDetail)) {
                                return $this->sendError('Customer catalog Not Found');
                            }

                            if ($customerInvoiceDirect->custTransactionCurrencyID != $catalogDetail->localCurrencyID) {
                                $currencyConversion = Helper::currencyConversion($customerInvoiceDirect->companySystemID, $catalogDetail->localCurrencyID, $customerInvoiceDirect->custTransactionCurrencyID, $catalogDetail->localPrice);
                                if (!empty($currencyConversion)) {
                                    $catalogDetail->localPrice = $currencyConversion['documentAmount'];
                                }
                            }

                            $input2['part_no'] = $catalogDetail->partNo;
                        } else {
                            $input2['part_no'] = $itemAssigned->secondaryItemCode;
                        }
                        /*round to 7 decimals*/
                        $input2['issueCostLocal'] = Helper::roundValue($input2['issueCostLocal']);
                        $input2['issueCostLocalTotal'] = Helper::roundValue($input2['issueCostLocalTotal']);
                        $input2['issueCostRpt'] = Helper::roundValue($input2['issueCostRpt']);
                        $input2['issueCostRptTotal'] = Helper::roundValue($input2['issueCostRptTotal']);
                        $input2['sellingCost'] = Helper::roundValue($input2['sellingCost']);
                        $input2['sellingCostAfterMargin'] = Helper::roundValue($input2['sellingCostAfterMargin']);
                        $input2['sellingTotal'] = Helper::roundValue($input2['sellingTotal']);
                        $input2['sellingCostAfterMarginLocal'] = Helper::roundValue($input2['sellingCostAfterMarginLocal']);
                        $input2['sellingCostAfterMarginRpt'] = Helper::roundValue($input2['sellingCostAfterMarginRpt']);


                        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
                            ->where('mainItemCategoryID', $input2['itemFinanceCategoryID'])
                            ->where('itemCategorySubID', $input2['itemFinanceCategorySubID'])
                            ->first();
                        if (!empty($financeItemCategorySubAssigned)) {
                            $input2['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                            $input2['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                            $input2['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                            $input2['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                            $input2['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
                            $input2['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;
                            $input2['financeCogsGLcodePL'] = $financeItemCategorySubAssigned->financeCogsGLcodePL;
                            $input2['financeCogsGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeCogsGLcodePLSystemID;
                        } else {
                            return $this->sendError("Finance Item category sub assigned not found", 500);
                        }


                        // check policy 18

                        $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
                            ->where('companySystemID', $companySystemID)
                            ->first();

                        if ($itemAssigned->financeCategoryMaster == 1) {
                            $checkWhether = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', '!=', $customerInvoiceDirect->custInvoiceDirectAutoID)
                                ->where('companySystemID', $companySystemID)
                                ->select([
                                    'erp_custinvoicedirect.custInvoiceDirectAutoID',
                                    'erp_custinvoicedirect.bookingInvCode',
                                    'erp_custinvoicedirect.wareHouseSystemCode',
                                    'erp_custinvoicedirect.approved'
                                ])
                                ->groupBy(
                                    'erp_custinvoicedirect.custInvoiceDirectAutoID',
                                    'erp_custinvoicedirect.companySystemID',
                                    'erp_custinvoicedirect.bookingInvCode',
                                    'erp_custinvoicedirect.wareHouseSystemCode',
                                    'erp_custinvoicedirect.approved'
                                )
                                ->whereHas('issue_item_details', function ($query) use ($companySystemID, $input2) {
                                    $query->where('itemCodeSystem', $input2['itemCodeSystem']);
                                })
                                ->where('approved', 0)
                                ->where('canceledYN', 0)
                                ->first();
                            /* approved=0*/


                            $checkWhetherItemIssueMaster = ItemIssueMaster::where('companySystemID', $companySystemID)
                                ->select([
                                    'erp_itemissuemaster.itemIssueAutoID',
                                    'erp_itemissuemaster.companySystemID',
                                    'erp_itemissuemaster.wareHouseFromCode',
                                    'erp_itemissuemaster.itemIssueCode',
                                    'erp_itemissuemaster.approved'
                                ])
                                ->groupBy(
                                    'erp_itemissuemaster.itemIssueAutoID',
                                    'erp_itemissuemaster.companySystemID',
                                    'erp_itemissuemaster.wareHouseFromCode',
                                    'erp_itemissuemaster.itemIssueCode',
                                    'erp_itemissuemaster.approved'
                                )
                                ->whereHas('details', function ($query) use ($companySystemID, $input2) {
                                    $query->where('itemCodeSystem', $input2['itemCodeSystem']);
                                })
                                ->where('approved', 0)
                                ->first();
                            /* approved=0*/

                            if (!empty($checkWhetherItemIssueMaster)) {
                                return $this->sendError("There is a Materiel Issue (" . $checkWhetherItemIssueMaster->itemIssueCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                            }

                            $checkWhetherStockTransfer = StockTransfer::where('companySystemID', $companySystemID)
                                ->select([
                                    'erp_stocktransfer.stockTransferAutoID',
                                    'erp_stocktransfer.companySystemID',
                                    'erp_stocktransfer.locationFrom',
                                    'erp_stocktransfer.stockTransferCode',
                                    'erp_stocktransfer.approved'
                                ])
                                ->groupBy(
                                    'erp_stocktransfer.stockTransferAutoID',
                                    'erp_stocktransfer.companySystemID',
                                    'erp_stocktransfer.locationFrom',
                                    'erp_stocktransfer.stockTransferCode',
                                    'erp_stocktransfer.approved'
                                )
                                ->whereHas('details', function ($query) use ($companySystemID, $input2) {
                                    $query->where('itemCodeSystem', $input2['itemCodeSystem']);
                                })
                                ->where('approved', 0)
                                ->first();
                            /* approved=0*/

                            if (!empty($checkWhetherStockTransfer)) {
                                return $this->sendError("There is a Stock Transfer (" . $checkWhetherStockTransfer->stockTransferCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                            }

                            // check in delivery order
                            $checkWhetherDeliveryOrder = DeliveryOrder::where('companySystemID', $companySystemID)
                                ->select([
                                    'erp_delivery_order.deliveryOrderID',
                                    'erp_delivery_order.deliveryOrderCode'
                                ])
                                ->groupBy(
                                    'erp_delivery_order.deliveryOrderID',
                                    'erp_delivery_order.companySystemID'
                                )
                                ->whereHas('detail', function ($query) use ($companySystemID, $input2) {
                                    $query->where('itemCodeSystem', $input2['itemCodeSystem']);
                                })
                                ->where('approvedYN', 0)
                                ->first();

                            if (!empty($checkWhetherDeliveryOrder)) {
                                return $this->sendError("There is a Delivery Order (" . $checkWhetherDeliveryOrder->deliveryOrderCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                            }

                            /*Check in purchase return*/
                            $checkWhetherPR = PurchaseReturn::where('companySystemID', $companySystemID)
                                ->select([
                                    'erp_purchasereturnmaster.purhaseReturnAutoID',
                                    'erp_purchasereturnmaster.companySystemID',
                                    'erp_purchasereturnmaster.purchaseReturnLocation',
                                    'erp_purchasereturnmaster.purchaseReturnCode',
                                ])
                                ->groupBy(
                                    'erp_purchasereturnmaster.purhaseReturnAutoID',
                                    'erp_purchasereturnmaster.companySystemID',
                                    'erp_purchasereturnmaster.purchaseReturnLocation'
                                )
                                ->whereHas('details', function ($query) use ($input2) {
                                    $query->where('itemCode', $input2['itemCodeSystem']);
                                })
                                ->where('approved', 0)
                                ->first();
                            /* approved=0*/

                            if (!empty($checkWhetherPR)) {
                                return $this->sendError("There is a Purchase Return (" . $checkWhetherPR->purchaseReturnCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                            }
                        }

                                if ($customerInvoiceDirect->isVatEligible) {
                                    $vatDetails = TaxService::getVATDetailsByItem($customerInvoiceDirect->companySystemID, $input2['itemCodeSystem'], $customerInvoiceDirect->customerID, 0);
                                    $input2['VATApplicableOn'] = $vatDetails['applicableOn'];
                                    $input2['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
                                    $input2['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
                                    $input2['VATAmount'] = $item->taxAmount / $item->defaultQty;


                                    $input2['VATAmountLocal'] =  $item->taxAmount / $item->defaultQty;
                                    $input2['VATAmountRpt'] =  ($item->taxAmount / $item->defaultQty) / $customerInvoiceDirect->companyReportingER;
                                }

                        $customerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepository->create($input2);
                    }
                    $resVat = $this->updateVatFromSalesQuotation($customerInvoiceDirects->custInvoiceDirectAutoID);

                    $input['wanNO'] = null;
                    $input['servicePeriod'] = null;
                    $input['rigNo'] = null;
                    $input['PONumber'] = null;
                    $input['customerGRVAutoID'] = null;

                    $detailAmount = CustomerInvoiceItemDetails::select(\Illuminate\Support\Facades\DB::raw("IFNULL(SUM(qtyIssuedDefaultMeasure * sellingCostAfterMargin),0) as bookingAmountTrans"), DB::raw("IFNULL(SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginLocal),0) as bookingAmountLocal"), DB::raw("IFNULL(SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginRpt),0) as bookingAmountRpt"))->where('custInvoiceDirectAutoID', $customerInvoiceDirects->custInvoiceDirectAutoID)->first();

                    $input['bookingAmountTrans'] = \Helper::roundValue($detailAmount->bookingAmountTrans);
                    $input['bookingAmountLocal'] = \Helper::roundValue($detailAmount->bookingAmountLocal);
                    $input['bookingAmountRpt'] = \Helper::roundValue($detailAmount->bookingAmountRpt);

                    $customerInvoiceDirect = $this->customerInvoiceDirectRepository->update($input, $customerInvoiceDirects->custInvoiceDirectAutoID);

                    $params = array('autoID' => $customerInvoiceDirects->custInvoiceDirectAutoID,
                        'company' => $customerInvoiceDirects->companySystemID,
                        'document' => $customerInvoiceDirects->documentSystemiD,
                        'segment' => '',
                        'category' => '',
                        'amount' => ''
                    );


                    $confirm = \Helper::confirmDocument($params);
                    if (!$confirm["success"]) {

                        return $this->sendError($confirm["message"], 500);
                    } else {

                    }

                    $documentApproveds = DocumentApproved::where('documentSystemCode', $customerInvoiceDirects->custInvoiceDirectAutoID)->where('documentSystemID', 20)->get();
                    foreach ($documentApproveds as $documentApproved) {
                        $documentApproved["approvedComments"] = "Approved by GPOS";
                        $documentApproved["db"] = $db;
                        $approve = \Helper::approveDocument($documentApproved);
                        if (!$approve["success"]) {
                            return $this->sendError($approve["message"]);
                        }
                    }

                    if (!$resVat['status']) {
                        return $this->sendError($resVat['message']);
                    };

                }


                $returns = DB::table('pos_source_salesreturn')
                    ->selectRaw('pos_source_salesreturn.*, pos_source_invoice.invoiceCode as invoiceCode')
                    ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_salesreturn.invoiceID')
                    ->where('pos_source_salesreturn.shiftID', $shiftId)
                    ->where('pos_source_invoice.isCreditSales', 1)
                    ->get();

                foreach ($returns as $return) {

                    $input['salesReturnDate'] = new Carbon($return->salesReturnDate);

                    $getCompanyFinanceYear = CompanyFinanceYear::where('bigginingDate', "<=", $return->salesReturnDate)->where('endingDate', ">=", $return->salesReturnDate)->where('companySystemID', $shiftDetails->companyID)->where('isActive', -1)->first();


                    $input['returnType'] = 2;
                    $input['documentSystemID'] = 87;
                    $input['salesPersonID'] = 0;

                    $getCompanyFinancePeriod = CompanyFinancePeriod::where('dateFrom', "<=", $return->salesReturnDate)->where('dateTo', ">=", $return->salesReturnDate)->where('companySystemID', $shiftDetails->companyID)->where('isActive', -1)->first();



                    if (!isset($getCompanyFinancePeriod->companyFinancePeriodID) || is_null($getCompanyFinancePeriod->companyFinancePeriodID)) {
                        return $this->sendError('Financial period is not found or inactive', 500);
                    }

                    if (!isset($getCompanyFinanceYear->companyFinanceYearID) || is_null($getCompanyFinanceYear->companyFinanceYearID)) {
                        return $this->sendError('Financial year is not found or inactive', 500);
                    }


                    $input['companyFinancePeriodID'] = $getCompanyFinancePeriod->companyFinancePeriodID;
                    $input['companyFinanceYearID'] = $getCompanyFinanceYear->companyFinanceYearID;

                    $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $getCompanyFinanceYear->companyFinanceYearID)->first();
                    $input['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
                    $input['FYEnd'] = $CompanyFinanceYear->endingDate;

                    $customerID = null;
                    $serviceLineSystemID = null;
                    $serviceLineCode = null;
                    $wareHouseID = null;
                    $customerID = POSSOURCECustomerMaster::where('customerAutoID', $return->customerID)->first();

                    if ($customerID) {
                        $customerID = $customerID->erp_customer_master_id;
                    }

                    $customer = CustomerMaster::find($customerID);
                    if (empty($customer)) {
                        return $this->sendError('Selected customer not found on db', 500);
                    }

                    if (!$customer->custGLAccountSystemID) {
                        return $this->sendError('GL account is not configured for this customer', 500);
                    }

                    if (!$customer->custUnbilledAccountSystemID) {
                        return $this->sendError('Unbilled receivable account is not configured for this customer', 500);
                    }

                    $input['customerID'] = $customerID;
                    $input['custGLAccountSystemID'] = $customer->custGLAccountSystemID;
                    $input['custGLAccountCode'] = $customer->custGLaccount;
                    $input['custUnbilledAccountSystemID'] = $customer->custUnbilledAccountSystemID;
                    $input['custUnbilledAccountCode'] = $customer->custUnbilledAccount;

                    $wareHouseID = null;
                    $wareHouse = DB::table('pos_source_shiftdetails')
                        ->selectRaw('pos_source_shiftdetails.wareHouseID as wareHouseID')
                        ->where('pos_source_shiftdetails.shiftID', $shiftId)
                        ->first();
                    if ($wareHouse) {
                        $wareHouseID = $wareHouse->wareHouseID;
                    }

                    $input['wareHouseSystemCode'] = $wareHouseID;
                    
                    $lastSerial = SalesReturn::where('companySystemID', $shiftDetails->companyID)
                        ->where('companyFinanceYearID', $getCompanyFinanceYear->companyFinanceYearID)
                        ->orderBy('serialNo', 'desc')
                        ->first();

                    $lastSerialNumber = 1;
                    if ($lastSerial) {
                        $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                    }
                    $company = Company::where('companySystemID', $shiftDetails->companyID)->first()->toArray();
                    $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));
                    $input['salesReturnCode'] = ($company['CompanyID'] . '\\' . $y . '\\SLR' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                    $input['serialNo'] = $lastSerialNumber;
                    $input['companyID'] = $company['CompanyID'];
                    $input['companySystemID'] = $shiftDetails->companyID;
                    $input['documentID'] = 'SLR';
                    $input['narration'] = "Sales Return Created by GPOS System";
                    $input['referenceNo'] = $return->invoiceCode;


                    $segments = DB::table('pos_source_shiftdetails')
                        ->selectRaw('pos_source_shiftdetails.segmentID as segmentID, serviceline.ServiceLineCode as serviceLineCode')
                        ->join('serviceline', 'serviceline.serviceLineSystemID', '=', 'pos_source_shiftdetails.segmentID')
                        ->where('pos_source_shiftdetails.shiftID', $shiftId)
                        ->first();

                    if ($segments) {
                        $input['serviceLineSystemID'] = $segments->segmentID;
                        $input['serviceLineCode'] = $segments->serviceLineCode;
                    }

                    $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
                    $input['FYPeriodDateFrom'] = $companyfinanceperiod->dateFrom;
                    $input['FYPeriodDateTo'] = $companyfinanceperiod->dateTo;

                    // check date within financial period
                    if (!(($input['salesReturnDate'] >= $input['FYPeriodDateFrom']) && ($input['salesReturnDate'] <= $input['FYPeriodDateTo']))) {
                        return $this->sendError('Document date should be between financial period start date and end date', 500);
                    }

                    $companyCurrency = Helper::companyCurrency($shiftDetails->companyID);
                    $companyCurrencyConversion = Helper::currencyConversion($shiftDetails->companyID, $return->transactionCurrencyID, $return->transactionCurrencyID, 0);

                    $input['transactionAmount'] = $return->subTotal;
                    $input['transactionCurrencyID'] = $return->transactionCurrencyID;
                    $input['transactionCurrencyER'] = 1;
                    $input['companyLocalCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                    $input['companyLocalCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $input['companyLocalAmount'] = $return->subTotal / $input['companyLocalCurrencyER'];
                    $input['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $input['companyReportingCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['companyReportingAmount'] = $return->subTotal / $input['companyReportingCurrencyER'];

                    $employee = Helper::getEmployeeInfo();
                    $input['createdUserSystemID'] = $employee->employeeSystemID;
                    $input['createdPCID'] = gethostname();
                    $input['createdUserID'] = $employee->empID;
                    $input['createdUserName'] = $employee->empName;

                    $salesReturn = $this->salesReturnRepository->create($input);

                    $salesReturnID = $salesReturn->id;

                    $items = DB::table('pos_source_salesreturndetails')
                        ->selectRaw('pos_source_salesreturndetails.*')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_salesreturndetails.itemAutoID')
                        ->where('pos_source_salesreturndetails.salesReturnID', $return->salesReturnID)
                        ->get();

                    foreach ($items as $returnItem) {

                        $cusInvoice = DB::table('pos_source_invoice')
                            ->selectRaw('erp_custinvoicedirect.*')
                            ->join('erp_custinvoicedirect', 'pos_source_invoice.invoiceCode', '=', 'erp_custinvoicedirect.customerInvoiceNo')
                            ->where('pos_source_invoice.invoiceID', $returnItem->invoiceID)
                            ->first();

                        $customerInvoice = CustomerInvoiceDirect::find($cusInvoice->custInvoiceDirectAutoID);

                        $cusInvDetail = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $cusInvoice->custInvoiceDirectAutoID)->where('itemCodeSystem', $returnItem->itemAutoID)->first();

                         $detailSum = SalesReturnDetail::select(DB::raw('COALESCE(SUM(qtyReturnedDefaultMeasure),0) as totalNoQty'))->where('customerItemDetailID', $cusInvDetail->customerItemDetailID)->first();

                                $totalAddedQty = $returnItem->qty + $detailSum['totalNoQty'];

                                if ($cusInvDetail->qtyIssuedDefaultMeasure == $totalAddedQty) {
                                    $fullyReturned = 2;
                                    $closedYN = -1;
                                    $selectedForSalesReturn= -1;
                                } else {
                                    $fullyReturned = 1;
                                    $closedYN = 0;
                                    $selectedForSalesReturn = 0;
                                }

                                if ($cusInvDetail->qtyIssuedDefaultMeasure >= $returnItem->qty) {

                                    $invDetail_arr['salesReturnID'] = $salesReturnID;
                                    $invDetail_arr['custInvoiceDirectAutoID'] = $cusInvoice->custInvoiceDirectAutoID;
                                    $invDetail_arr['customerItemDetailID'] = $cusInvDetail->customerItemDetailID;
                                    $invDetail_arr['itemCodeSystem'] = $cusInvDetail->itemCodeSystem;
                                    $invDetail_arr['itemPrimaryCode'] = $cusInvDetail->itemPrimaryCode;
                                    $invDetail_arr['trackingType'] = $cusInvDetail->trackingType;
                                    $invDetail_arr['itemDescription'] = $cusInvDetail->itemDescription;
                                    $invDetail_arr['vatMasterCategoryID'] = $cusInvDetail->vatMasterCategoryID;
                                    $invDetail_arr['vatSubCategoryID'] = $cusInvDetail->vatSubCategoryID;
                                    $invDetail_arr['companySystemID'] = $customerInvoice->companySystemID;
                                    $invDetail_arr['VATPercentage'] = $cusInvDetail->VATPercentage;
                                    $invDetail_arr['VATAmount'] = $cusInvDetail->VATAmount;
                                    $invDetail_arr['VATAmountLocal'] = $cusInvDetail->VATAmountLocal;
                                    $invDetail_arr['VATAmountRpt'] = $cusInvDetail->VATAmountRpt;
                                    $invDetail_arr['VATApplicableOn'] = $cusInvDetail->VATApplicableOn;
                                    // $invDetail_arr['documentSystemID'] = $new['companySystemID'];

                                    $item = ItemMaster::find($cusInvDetail->itemCodeSystem);
                                    if(empty($item)){
                                        return $this->sendError('Item not found',500);
                                    }

                                    $data = array(
                                        'companySystemID' => $customerInvoice->companySystemID,
                                        'itemCodeSystem' => $cusInvDetail->itemCodeSystem,
                                        'wareHouseId' => $customerInvoice->wareHouseSystemCode
                                    );

                                    $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

                                    $invDetail_arr['doInvRemainingQty'] = floatval($cusInvDetail->qtyIssuedDefaultMeasure) - floatval($returnItem->qty);
                                    $invDetail_arr['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
                                    $invDetail_arr['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                                    $invDetail_arr['wacValueLocal'] = $cusInvDetail->issueCostLocal;
                                    $invDetail_arr['wacValueReporting'] = $cusInvDetail->issueCostRpt;
                                    $invDetail_arr['convertionMeasureVal'] = 1;

                                    $invDetail_arr['itemFinanceCategoryID'] = $item->financeCategoryMaster;
                                    $invDetail_arr['itemFinanceCategorySubID'] = $item->financeCategorySub;

                                    $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $customerInvoice->companySystemID)
                                        ->where('mainItemCategoryID', $invDetail_arr['itemFinanceCategoryID'])
                                        ->where('itemCategorySubID', $invDetail_arr['itemFinanceCategorySubID'])
                                        ->first();

                                    if (!empty($financeItemCategorySubAssigned)) {
                                        $invDetail_arr['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                                        $invDetail_arr['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                                        $invDetail_arr['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                                        $invDetail_arr['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                                        $invDetail_arr['financeCogsGLcodePL'] = $financeItemCategorySubAssigned->financeCogsGLcodePL;
                                        $invDetail_arr['financeCogsGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeCogsGLcodePLSystemID;
                                        $invDetail_arr['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
                                        $invDetail_arr['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;
                                    } else {
                                        return $this->sendError("Account code not updated for ".$cusInvDetail->itemSystemCode.".", 500);
                                    }

                                    if (!$invDetail_arr['financeGLcodebBS'] || !$invDetail_arr['financeGLcodebBSSystemID']
                                        || !$invDetail_arr['financeGLcodePL'] || !$invDetail_arr['financeGLcodePLSystemID']
                                        || !$invDetail_arr['financeCogsGLcodePL'] || !$invDetail_arr['financeCogsGLcodePLSystemID']
                                        || !$invDetail_arr['financeGLcodeRevenueSystemID'] || !$invDetail_arr['financeGLcodeRevenue']) {
                                        return $this->sendError("Account code not updated for ".$cusInvDetail->itemSystemCode.".", 500);
                                    }


                                    $invDetail_arr['transactionCurrencyID'] = $customerInvoice->custTransactionCurrencyID;
                                    $invDetail_arr['transactionCurrencyER'] = $customerInvoice->custTransactionCurrencyER;
                                    $invDetail_arr['companyLocalCurrencyID'] = $customerInvoice->localCurrencyID;
                                    $invDetail_arr['companyLocalCurrencyER'] = $customerInvoice->localCurrencyER;
                                    $invDetail_arr['companyReportingCurrencyID'] = $customerInvoice->companyReportingCurrencyID;
                                    $invDetail_arr['companyReportingCurrencyER'] = $customerInvoice->companyReportingER;

                                    $invDetail_arr['itemUnitOfMeasure'] = $cusInvDetail->itemUnitOfMeasure;
                                    $invDetail_arr['unitOfMeasureIssued'] = $cusInvDetail->unitOfMeasureIssued;
                                    $invDetail_arr['qtyReturned'] = $returnItem->qty;
                                    $invDetail_arr['qtyReturnedDefaultMeasure'] = $returnItem->qty;

                                    // $invDetail_arr['marginPercentage'] = 0;
                                    // if (isset($new['discountPercentage']) && $new['discountPercentage'] != 0){
                                    //     $invDetail_arr['unitTransactionAmount'] = ($new['unitTransactionAmount']) - ($new['unitTransactionAmount']*$new['discountPercentage']/100);
                                    // }else{
                                    $invDetail_arr['unitTransactionAmount'] = $cusInvDetail->sellingCostAfterMargin;
                                    // }

                                    $totalNetcost = $cusInvDetail->sellingCost * $cusInvDetail->noQty;

                                    $invDetail_arr['transactionAmount'] = \Helper::roundValue($totalNetcost);
                                    $invDetail_arr['unitTransactionAmount'] = \Helper::roundValue($invDetail_arr['unitTransactionAmount']);

                                    $itemReturnDetail = SalesReturnDetail::create($invDetail_arr);

                                    $update = CustomerInvoiceItemDetails::where('customerItemDetailID', $cusInvDetail->customerItemDetailID)
                                        ->update(['fullyReturned' => $fullyReturned, 'returnQty' => $totalAddedQty]);
                                }

                                // fetching the total count records from purchase Request Details table
                         $doDetailTotalcount = CustomerInvoiceItemDetails::select(DB::raw('count(customerItemDetailID) as detailCount'))->where('custInvoiceDirectAutoID', $cusInvDetail->custInvoiceDirectAutoID)->first();

                                // fetching the total count records from purchase Request Details table where fullyOrdered = 2
                         $doDetailExist = CustomerInvoiceItemDetails::select(DB::raw('count(deliveryOrderDetailID) as count'))
                                    ->where('custInvoiceDirectAutoID', $cusInvDetail->custInvoiceDirectAutoID)
                                    ->where('fullyReturned', 2)
                                    ->first();

                                // Updating PR Master Table After All Detail Table records updated
                        if ($doDetailTotalcount['detailCount'] == $doDetailExist['count']) {
                                    $updatedo = CustomerInvoiceDirect::find($cusInvDetail->custInvoiceDirectAutoID)
                                        ->update(['selectedForSalesReturn' => -1, 'closedYN' => -1]);
                                }

                        //check all details fullyOrdered in DO Master
                        $doMasterfullyOrdered = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $cusInvDetail->custInvoiceDirectAutoID)
                            ->whereIn('fullyReturned', [1, 0])
                            ->get()->toArray();

                        if (empty($doMasterfullyOrdered)) {
                            CustomerInvoiceDirect::find($cusInvDetail->custInvoiceDirectAutoID)
                                ->update([
                                    'selectedForSalesReturn' => -1,
                                    'closedYN' => -1,
                                ]);
                        } else {
                            CustomerInvoiceDirect::find($cusInvDetail->custInvoiceDirectAutoID)
                                ->update([
                                    'selectedForSalesReturn' => 0,
                                    'closedYN' => 0,
                                ]);
                        }


                        $updateItem = SalesReturnDetail::find($itemReturnDetail->salesReturnDetailID);

                        $data = array(
                            'companySystemID' => $salesReturn->companySystemID,
                            'itemCodeSystem' => $item->itemCodeSystem,
                            'wareHouseId' => $salesReturn->wareHouseSystemCode
                        );

                        $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

                        $updateItem->currentStockQty = $itemCurrentCostAndQty['currentStockQty'];
                        $updateItem->currentWareHouseStockQty = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                        $updateItem->currentStockQtyInDamageReturn = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];

                        $discountedUnit = $updateItem->unitTransactionAmount;

                        if($updateItem->discountAmount > 0) {
                            $discountedUnit = $updateItem->unitTransactionAmount - $updateItem->discountAmount;
                        }

                        $updateItem->transactionAmount = $discountedUnit * $updateItem->qtyReturnedDefaultMeasure;

                        $currencyConversion = Helper::currencyConversion($salesReturn->companySystemID,$updateItem->transactionCurrencyID,$updateItem->transactionCurrencyID,$updateItem->transactionAmount);
                        if(!empty($currencyConversion)){
                            $updateItem->companyLocalAmount = $currencyConversion['localAmount'];
                        }

                        $currencyConversion = Helper::currencyConversion($salesReturn->companySystemID,$updateItem->transactionCurrencyID,$updateItem->transactionCurrencyID,$updateItem->transactionAmount);
                        if(!empty($currencyConversion)){
                            $updateItem->companyReportingAmount = $currencyConversion['reportingAmount'];
                        }

                        $updateItem->unitTransactionAmount = Helper::roundValue($updateItem->unitTransactionAmount);
                        $updateItem->discountPercentage = Helper::roundValue($updateItem->discountPercentage);
                        $updateItem->discountAmount = Helper::roundValue($updateItem->discountAmount);
                        $updateItem->transactionAmount = Helper::roundValue($updateItem->transactionAmount);
                        $updateItem->companyLocalAmount = Helper::roundValue($updateItem->companyLocalAmount);
                        $updateItem->companyReportingAmount = Helper::roundValue($updateItem->companyReportingAmount);

                        $updateItem->save();


                        if ($updateItem->unitTransactionAmount < 0) {
                            return $this->sendError('Item must not have negative cost', 500);
                        }

                        $this->updateInvoiceReturnedStatus($cusInvDetail->custInvoiceDirectAutoID);

                    }

                    if(isset($updateItem->discountPercentage) != 0){
                        $amount = SalesReturnDetail::where('salesReturnID', $salesReturnID)
                            ->sum(\Illuminate\Support\Facades\DB::raw('qtyReturnedDefaultMeasure * (companyReportingAmount-(companyReportingAmount*discountPercentage/100))'));
                    }else{
                        $amount = SalesReturnDetail::where('salesReturnID', $salesReturnID)
                            ->sum(DB::raw('qtyReturnedDefaultMeasure * companyReportingAmount'));
                    }

                    $resVat = $this->updateVatOfSalesReturn($salesReturnID);
                    if (!$resVat['status']) {
                        return $this->sendError($resVat['message']);
                    }

                    $params = array('autoID' => $salesReturnID,
                        'company' => $salesReturn->companySystemID,
                        'document' => $salesReturn->documentSystemID,
                        'segment' => '',
                        'category' => '',
                        'amount' => $amount
                    );


                    $confirm = \Helper::confirmDocument($params);
                    if (!$confirm["success"]) {

                        return $this->sendError($confirm["message"], 500);
                    } else {

                    }

                    $documentApproveds = DocumentApproved::where('documentSystemCode', $salesReturnID)->where('documentSystemID', 87)->get();
                    foreach ($documentApproveds as $documentApproved) {
                        $documentApproved["approvedComments"] = "Approved by GPOS";
                        $documentApproved["db"] = $db;
                        $approve = \Helper::approveDocument($documentApproved);
                        if (!$approve["success"]) {
                            return $this->sendError($approve["message"]);
                        }
                    }

                }


                $logs = POSFinanceLog::where('shiftId', $shiftId)->update(['status' => 2]);


                \Illuminate\Support\Facades\DB::commit();

            } catch (\Exception $exception) {
                \Illuminate\Support\Facades\DB::rollback();
                return $this->sendError('Error Occurred' . $exception->getMessage() . 'Line :' . $exception->getLine());
            }

            //sales return
            $hasSales = POSInvoiceSource::where('shiftId', $shiftId)->where('isCreditSales', 0)->get();
            $hasItemsSR = DB::table('pos_source_salesreturn')
                ->selectRaw('pos_source_salesreturn.*')
                ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_salesreturn.invoiceID')
                ->where('pos_source_salesreturn.shiftID', $shiftId)
                ->where('pos_source_invoice.isCreditSales', 0)
                ->get();

            if (!$hasItemsSR->isEmpty() || !$hasSales->isEmpty()) {
                GeneralLedgerInsert::dispatch($masterData, $db);
            }
            if (!$hasSales->isEmpty()) {
                POSItemLedgerInsert::dispatch($masterData);
                BankLedgerInsert::dispatch($masterData);
                $taxLedgerData = null;
                TaxLedgerInsert::dispatch($masterData, $taxLedgerData, $db);
            }
            //end of sales return

        }

        else if ($shiftDetails->posType == 2){
            $logged_user = \Helper::getEmployeeSystemID();

            $masterData = ['documentSystemID' => 111, 'autoID' => $shiftId, 'companySystemID' => $shiftDetails->companyID, 'employeeSystemID' => $logged_user, 'companyID' => $shiftDetails->companyCode];


            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                $invoices = DB::table('pos_source_menusalesmaster')
                    ->selectRaw('pos_source_menusalesmaster.*')
                    ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                    ->where('pos_source_menusalesmaster.isCreditSales', 1)
                    ->get();
                foreach ($invoices as $invoice) {

                    $companyFinanceYear = CompanyFinanceYear::where('bigginingDate', "<=", $invoice->menuSalesDate)->where('endingDate', ">=", $invoice->menuSalesDate)->where('companySystemID', $shiftDetails->companyID)->where('isActive', -1)->first();

                    $companyFinancePeriod = CompanyFinancePeriod::where('dateFrom', "<=", $invoice->menuSalesDate)->where('dateTo', ">=", $invoice->menuSalesDate)->where('companySystemID', $shiftDetails->companyID)->where('isActive', -1)->first();

                    if (!isset($companyFinancePeriod->companyFinancePeriodID) || is_null($companyFinancePeriod->companyFinancePeriodID)) {
                        return $this->sendError('Financial period is not found or inactive', 500);
                    }

                    if (!isset($companyFinancePeriod->companyFinanceYearID) || is_null($companyFinancePeriod->companyFinanceYearID)) {
                        return $this->sendError('Financial year is not found or inactive', 500);
                    }

                    $customerID = null;
                    $serviceLineSystemID = null;
                    $serviceLineCode = null;
                    $wareHouseID = null;
                    $customerID = POSSOURCECustomerMaster::where('customerAutoID',$invoice->customerID)->first();
                    if($customerID){
                        $customerID = $customerID->erp_customer_master_id;
                    }
                    $segments = DB::table('pos_source_shiftdetails')
                        ->selectRaw('pos_source_shiftdetails.segmentID as segmentID, serviceline.ServiceLineCode as serviceLineCode')
                        ->join('serviceline', 'serviceline.serviceLineSystemID', '=', 'pos_source_shiftdetails.segmentID')
                        ->where('pos_source_shiftdetails.shiftID', $shiftId)
                        ->first();


                    if($segments){
                        $serviceLineSystemID = $segments->segmentID;
                        $serviceLineCode = $segments->serviceLineCode;
                    }
                    $wareHouse = DB::table('pos_source_shiftdetails')
                        ->selectRaw('pos_source_shiftdetails.wareHouseID as wareHouseID')
                        ->where('pos_source_shiftdetails.shiftID', $shiftId)
                        ->first();
                    if($wareHouse){
                        $wareHouseID = $wareHouse->wareHouseID;
                    }

                    $companyCurrency = \Helper::companyCurrency($shiftDetails->companyID);

                    $bank = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('erp_bankaccount.bankAccountAutoID as bankAccountID, erp_bankaccount.bankmasterAutoID as bankID')
                        ->join('pos_source_menusalespayments', 'pos_source_menusalespayments.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                        ->join('erp_bankaccount', 'erp_bankaccount.chartOfAccountSystemID', '=', 'pos_source_menusalespayments.GLCode')
                        ->where('pos_source_menusalesmaster.menuSalesID', $invoice->menuSalesID)
                        ->first();

                    if(!empty($bank)){
                        $bankID = $bank->bankID;
                        $bankAccountID = $bank->bankAccountID;

                    }else {
                        $bank = BankAssign::select('bankmasterAutoID')
                            ->where('companySystemID', $shiftDetails->companyID)
                            ->where('isDefault', -1)
                            ->first();
                        if(!empty($bank)) {
                            $bankID = $bank->bankmasterAutoID;
                        } else {
                            return $this->sendError('Default bank not assigned to the company', 500);
                        }


                        $bankAccount = BankAccount::where('companySystemID', $shiftDetails->companyID)
                            ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                            ->where('isDefault', 1)
                            ->where('accountCurrencyID', $companyCurrency->localcurrency->currencyID)
                            ->first();
                        if (!empty($bankAccount)) {
                            $bankAccountID = $bankAccount->bankAccountAutoID;
                        } else {
                            return $this->sendError('Bank default account not assigned to the bank', 500);
                        }

                    }


                    $input = ['bookingDate' => $invoice->menuSalesDate, 'comments' => "Inv Created by RPOS System. Bill No: ".$invoice->invoiceCode, 'companyFinancePeriodID' => $companyFinancePeriod->companyFinancePeriodID, 'companyFinanceYearID' => $companyFinanceYear->companyFinanceYearID, 'companyID' => $shiftDetails->companyID, 'custTransactionCurrencyID' => $companyCurrency->localcurrency->currencyID, 'customerID' => $customerID, 'date_of_supply' => $invoice->menuSalesDate, 'invoiceDueDate' => $invoice->menuSalesDate, 'isPerforma' => 2, 'serviceLineSystemID' => $serviceLineSystemID,'serviceLineCode' => $serviceLineCode, 'wareHouseSystemCode' => $wareHouseID, 'customerInvoiceNo' => $invoice->invoiceCode, 'bankAccountID' => $bankID, 'bankID' => $bankAccountID];


                    if (isset($input['isPerforma']) && $input['isPerforma'] == 2) {
                        $wareHouse = isset($input['wareHouseSystemCode']) ? $input['wareHouseSystemCode'] : 0;
                        if (!$wareHouse) {
                            return $this->sendError('Please select a warehouse', 500);
                        }
                    }

                    if (!isset($input['custTransactionCurrencyID']) || (isset($input['custTransactionCurrencyID']) && ($input['custTransactionCurrencyID'] == 0 || $input['custTransactionCurrencyID'] == null))) {
                        return $this->sendError('Please select a currency', 500);
                    }
                    $companyFinanceYearID = $input['companyFinanceYearID'];

                    if (!isset($input['companyFinanceYearID']) || is_null($input['companyFinanceYearID'])) {
                        return $this->sendError('Financial year is not selected', 500);
                    }

                    if (!isset($input['companyFinancePeriodID']) || is_null($input['companyFinancePeriodID'])) {
                        return $this->sendError('Financial period is not selected', 500);
                    }

                    $company = Company::where('companySystemID', $input['companyID'])->first()->toArray();

                    $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $companyFinanceYearID)->first();
                    $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
                    $FYPeriodDateFrom = $companyfinanceperiod->dateFrom;
                    $FYPeriodDateTo = $companyfinanceperiod->dateTo;
                    $customer = CustomerMaster::where('customerCodeSystem',  $input['customerID'])->first();

                    if(empty($customer)){
                        return $this->sendError('Customer not found', 500);

                    }

                    /*exchange added*/
                    $input['custTransactionCurrencyER'] = $invoice->transactionExchangeRate;
                    $input['companyReportingCurrencyID'] = $invoice->companyReportingCurrencyID;
                    $input['companyReportingER'] = $invoice->companyReportingExchangeRate;
                    $input['localCurrencyID'] = $invoice->companyLocalCurrencyID;
                    $input['localCurrencyER'] = $invoice->companyLocalExchangeRate;



                    if (isset($input['isPerforma']) && ($input['isPerforma'] == 2 || $input['isPerforma'] == 3 || $input['isPerforma'] == 4 || $input['isPerforma'] == 5)) {
                        $serviceLine = isset($input['serviceLineSystemID']) ? $input['serviceLineSystemID'] : 0;
                        if (!$serviceLine) {
                            return $this->sendError('Please select a Segment', 500);
                        }
                        $segment = SegmentMaster::find($input['serviceLineSystemID']);
                        $input['serviceLineCode'] = isset($segment->ServiceLineCode) ? $segment->ServiceLineCode : null;
                    }

                    $lastSerial = CustomerInvoiceDirect::where('companySystemID', $input['companyID'])
                        ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                        ->orderBy('serialNo', 'desc')
                        ->first();

                    $lastSerialNumber = 1;
                    if ($lastSerial) {
                        $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                    }

                    $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));
                    $bookingInvCode = ($company['CompanyID'] . '\\' . $y . '\\INV' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

                    $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerID'])
                        ->where('companySystemID', $input['companyID'])
                        ->first();
                    if ($customerGLCodeUpdate) {
                        $input['customerVATEligible'] = $customerGLCodeUpdate->vatEligible;
                    }

                    $company = Company::where('companySystemID', $input['companyID'])->first();

                    if ($company) {
                        $input['vatRegisteredYN'] = $company->vatRegisteredYN;
                    }

                    $input['documentID'] = "INV";
                    $input['documentSystemiD'] = 20;
                    $input['bookingInvCode'] = $bookingInvCode;
                    $input['serialNo'] = $lastSerialNumber;
                    $input['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
                    $input['FYEnd'] = $CompanyFinanceYear->endingDate;
                    $input['FYPeriodDateFrom'] = $FYPeriodDateFrom;
                    $input['FYPeriodDateTo'] = $FYPeriodDateTo;
                    $input['invoiceDueDate'] = Carbon::parse($input['invoiceDueDate'])->format('Y-m-d') . ' 00:00:00';
                    $input['bookingDate'] = Carbon::parse($input['bookingDate'])->format('Y-m-d') . ' 00:00:00';
                    $input['date_of_supply'] = Carbon::parse($input['date_of_supply'])->format('Y-m-d') . ' 00:00:00';
                    $input['customerInvoiceDate'] = $input['bookingDate'];
                    $input['companySystemID'] = $input['companyID'];
                    $input['companyID'] = $company['CompanyID'];
                    $input['customerGLCode'] = $customer->custGLaccount;
                    $input['customerGLSystemID'] = $customer->custGLAccountSystemID;
                    $input['documentType'] = 11;
                    $input['isPOS'] = 1;
                    $input['createdUserID'] = \Helper::getEmployeeID();
                    $input['createdPcID'] = getenv('COMPUTERNAME');
                    $input['modifiedUser'] = \Helper::getEmployeeID();
                    $input['modifiedPc'] = getenv('COMPUTERNAME');
                    $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                    $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();


                    $curentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
                    if ($input['bookingDate'] > $curentDate) {
                        return $this->sendResponse('e', 'Document date cannot be greater than current date');
                    }

                    $customerInvoiceDirects = $this->customerInvoiceDirectRepository->create($input);
                    $custInvoiceDirectAutoID = $customerInvoiceDirects->custInvoiceDirectAutoID;
                    $companySystemID = $shiftDetails->companyID;

                    $master = CustomerInvoiceDirect::select('*')->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();

                    $items = DB::table('pos_source_menusalesitems')
                        ->selectRaw('pos_source_menusalesitems.*, itemmaster.unit as unit, pos_source_menusalesitemdetails.itemAutoID as itemAutoID, itemmaster.primaryCode as itemPrimaryCode, itemmaster.itemDescription as itemDescription, pos_source_menusalesitemdetails.warehouseAutoID as warehouseAutoID, itemmaster.financeCategoryMaster as itemFinanceCategoryID, itemmaster.financeCategorySub as itemFinanceCategorySubID, pos_source_menusalesitemdetails.cost as cost, pos_source_menusalesitemdetails.qty as itemQty, pos_source_menusalesitemdetails.UOMID as uomID')
                        ->join('pos_source_menusalesitemdetails', 'pos_source_menusalesitemdetails.menuSalesItemID', '=', 'pos_source_menusalesitems.menuSalesItemID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_menusalesitemdetails.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->where('pos_source_menusalesitems.menuSalesID', $invoice->menuSalesID)
                        ->get();


                    foreach ($items as $item) {

                        $addToCusInvItemDetails['custInvoiceDirectAutoID'] = $custInvoiceDirectAutoID;
                        $addToCusInvItemDetails['itemCodeSystem'] = $item->itemAutoID;
                        $addToCusInvItemDetails['itemPrimaryCode'] = $item->itemPrimaryCode;
                        $addToCusInvItemDetails['itemDescription'] = $item->itemDescription;
                        $addToCusInvItemDetails['itemUnitOfMeasure'] = $item->unit;
                        $addToCusInvItemDetails['unitOfMeasureIssued'] = $item->unit;
                        $addToCusInvItemDetails['convertionMeasureVal'] = $item->unit;
                        $addToCusInvItemDetails['qtyIssued'] = $item->itemQty * $item->qty;
                        $addToCusInvItemDetails['qtyIssuedDefaultMeasure'] = $item->itemQty * $item->qty;


                        $data = array('companySystemID' => $master->companySystemID,
                            'itemCodeSystem' => $item->itemAutoID,
                            'wareHouseId' => $item->warehouseAutoID);

                        $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

                        $addToCusInvItemDetails['currentStockQty'] =  $itemCurrentCostAndQty['currentStockQty'];
                        $addToCusInvItemDetails['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                        $addToCusInvItemDetails['currentStockQtyInDamageReturn'] = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];
                        $addToCusInvItemDetails['comments'] = $master->comments;
                        $addToCusInvItemDetails['itemFinanceCategoryID'] = $item->itemFinanceCategoryID;
                        $addToCusInvItemDetails['itemFinanceCategorySubID'] = $item->itemFinanceCategorySubID;

                        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
                            ->where('mainItemCategoryID', $addToCusInvItemDetails['itemFinanceCategoryID'])
                            ->where('itemCategorySubID', $addToCusInvItemDetails['itemFinanceCategorySubID'])
                            ->first();

                        $addToCusInvItemDetails['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                        $addToCusInvItemDetails['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                        $addToCusInvItemDetails['financeCogsGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeCogsGLcodePLSystemID;
                        $addToCusInvItemDetails['financeCogsGLcodePL'] = $financeItemCategorySubAssigned->financeCogsGLcodePL;
                        $addToCusInvItemDetails['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
                        $addToCusInvItemDetails['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;


                        $addToCusInvItemDetails['localCurrencyID'] = $item->companyLocalCurrencyID;
                        $addToCusInvItemDetails['localCurrencyER'] = $item->companyLocalExchangeRate;

                        if($item->itemQty != 0 || $item->itemQty != null)
                        {
                            $addToCusInvItemDetails['issueCostLocal'] = $item->cost / $item->itemQty;
                        }
                        else
                        {
                            $addToCusInvItemDetails['issueCostLocal'] = 0;
                        }

                        $addToCusInvItemDetails['issueCostLocalTotal'] = $item->cost * $item->qty;
                        $addToCusInvItemDetails['reportingCurrencyID'] = $item->companyReportingCurrencyID;
                        $addToCusInvItemDetails['reportingCurrencyER'] = $item->companyReportingExchangeRate;

                        if($item->itemQty != 0 || $item->itemQty != null)
                        { 
                           $addToCusInvItemDetails['issueCostRpt'] = ($item->cost / $item->itemQty) / $item->companyReportingExchangeRate;
                        }
                        else
                        {
                           $addToCusInvItemDetails['issueCostRpt'] = 0;
                        }


                        $addToCusInvItemDetails['issueCostRptTotal'] = $item->cost * $item->qty / $item->companyReportingExchangeRate;
                        $addToCusInvItemDetails['sellingCurrencyID'] = $item->transactionCurrencyID;
                        $addToCusInvItemDetails['sellingCurrencyER'] = $item->transactionExchangeRate;


                        $addToCusInvItemDetails['sellingCost'] = 0;
                        $addToCusInvItemDetails['sellingCostAfterMargin'] = 0;
                        $addToCusInvItemDetails['sellingTotal'] = 0;
                        $addToCusInvItemDetails['sellingCostAfterMarginLocal'] = 0;
                        $addToCusInvItemDetails['sellingCostAfterMarginRpt'] = 0;
                        $addToCusInvItemDetails['salesPrice'] = 0;
                        $addToCusInvItemDetails['VATPercentage'] = 0;
                        $addToCusInvItemDetails['VATApplicableOn'] = 0;
                        $addToCusInvItemDetails['vatMasterCategoryID'] = 0;
                        $addToCusInvItemDetails['vatSubCategoryID'] = 0;
                        $addToCusInvItemDetails['VATAmount'] = 0;
                        $addToCusInvItemDetails['VATAmountLocal'] = 0;
                        $addToCusInvItemDetails['VATAmountRpt'] = 0;

                        CustomerInvoiceItemDetails::create($addToCusInvItemDetails);

                    }


                    //gl-selection tab

                    $msItems = DB::table('pos_source_menusalesitems')
                        ->selectRaw('pos_source_menusalesitems.*, pos_source_menusalesmaster.discountAmount as discount, pos_source_menusalesmaster.promotionDiscountAmount as promotionAmount, pos_source_menusalesmaster.promotionGLCode as promotionGLCode')
                        ->join('pos_source_menusalesmaster', 'pos_source_menusalesmaster.menuSalesID', '=', 'pos_source_menusalesitems.menuSalesID')
                        ->where('pos_source_menusalesitems.menuSalesID', $invoice->menuSalesID)
                        ->where('pos_source_menusalesmaster.isWastage', 0)
                        ->groupBy('pos_source_menusalesitems.menuSalesID')
                        ->groupBy('pos_source_menusalesitems.menuID')
                        ->get();

                    //for revenue-gl
                    foreach ($msItems as $item) {

                        $sumMenuSales = DB::table('pos_source_menusalesitems')
                            ->where('pos_source_menusalesitems.menuSalesID', $invoice->menuSalesID)
                            ->sum(DB::raw('pos_source_menusalesitems.menuSalesPrice * pos_source_menusalesitems.qty'));

                        if($sumMenuSales != 0) {

                            $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $item->revenueGLAutoID)->first();

                        if($item->promotionGLCode != null) {

                            $totAfterDiscount = (($item->menuSalesPrice * $item->qty) - $item->discountAmount) - (($item->discount / $sumMenuSales) * ($item->menuSalesPrice * $item->qty));
                            }
                            else {
                            $totAfterDiscount = (($item->menuSalesPrice * $item->qty) - $item->discountAmount) - (($item->discount / $sumMenuSales) * ($item->menuSalesPrice * $item->qty));

                            $totAfterDiscount = $totAfterDiscount - (($item->promotionAmount / $sumMenuSales) * ($item->menuSalesPrice * $item->qty));
                        }

                            $addToCusInvDetails['custInvoiceDirectID'] = $custInvoiceDirectAutoID;
                            $addToCusInvDetails['companyID'] = $master->companyID;
                            $addToCusInvDetails['serviceLineSystemID'] = $serviceLineSystemID;
                            $addToCusInvDetails['serviceLineCode'] = $serviceLineCode;
                            $addToCusInvDetails['customerID'] = $master->customerID;
                            if ($chartOfAccount) {
                                $addToCusInvDetails['glSystemID'] = $chartOfAccount->chartOfAccountSystemID;
                                $addToCusInvDetails['glCode'] = $chartOfAccount->AccountCode;
                                $addToCusInvDetails['glCodeDes'] = $chartOfAccount->AccountDescription;
                                $addToCusInvDetails['accountType'] = $chartOfAccount->catogaryBLorPL;
                            }
                            $addToCusInvDetails['comments'] = $master->comments;
                            $addToCusInvDetails['invoiceAmountCurrency'] = $master->custTransactionCurrencyID;
                            $addToCusInvDetails['invoiceAmountCurrencyER'] = $master->localCurrencyER;

                            $addToCusInvDetails['unitCost'] = $totAfterDiscount;
                            $addToCusInvDetails['salesPrice'] = $totAfterDiscount;
                            $addToCusInvDetails['invoiceAmount'] = $totAfterDiscount;

                            $addToCusInvDetails['localCurrency'] = $master->localCurrencyID;
                            $addToCusInvDetails['localCurrencyER'] = $master->localCurrencyER;

                            $addToCusInvDetails['comRptCurrency'] = $master->companyReportingCurrencyID;
                            $addToCusInvDetails['comRptCurrencyER'] = $master->companyReportingER;
                            $addToCusInvDetails["comRptAmount"] = ($totAfterDiscount / $master->companyReportingER);
                            $addToCusInvDetails["localAmount"] = $totAfterDiscount;

                            $addToCusInvDetails['unitOfMeasure'] = 0;
                            $addToCusInvDetails['invoiceQty'] = $item->qty;

                            CustomerInvoiceDirectDetail::create($addToCusInvDetails);
                        }
                    }


                    $taxItems = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, SUM(pos_source_menusalestaxes.taxAmount) as taxAmount, erp_taxmaster_new.outputVatGLAccountAutoID as outputVatGLCode')
                        ->join('pos_source_menusalestaxes', 'pos_source_menusalestaxes.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                        ->join('pos_source_taxmaster', 'pos_source_taxmaster.taxMasterAutoID', '=', 'pos_source_menusalestaxes.taxMasterID')
                        ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_taxmaster.erp_tax_master_id')
                        ->where('pos_source_menusalesmaster.menuSalesID',  $invoice->menuSalesID)
                        ->where('pos_source_menusalesmaster.isWastage', 0)
                        ->groupBy('pos_source_menusalestaxes.GLCode')
                        ->get();


                    //for tax-gl
                    foreach ($taxItems as $item) {

                        $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $item->outputVatGLCode)->first();

                        $addToCusInvDetails['custInvoiceDirectID'] = $custInvoiceDirectAutoID;
                        $addToCusInvDetails['companyID'] = $master->companyID;
                        $addToCusInvDetails['serviceLineSystemID'] = $serviceLineSystemID;
                        $addToCusInvDetails['serviceLineCode'] = $serviceLineCode;
                        $addToCusInvDetails['customerID'] = $master->customerID;
                        if ($chartOfAccount) {
                            $addToCusInvDetails['glSystemID'] = $chartOfAccount->chartOfAccountSystemID;
                            $addToCusInvDetails['glCode'] = $chartOfAccount->AccountCode;
                            $addToCusInvDetails['glCodeDes'] = $chartOfAccount->AccountDescription;
                            $addToCusInvDetails['accountType'] = $chartOfAccount->catogaryBLorPL;
                        }
                        $addToCusInvDetails['comments'] = $master->comments;
                        $addToCusInvDetails['invoiceAmountCurrency'] = $master->custTransactionCurrencyID;
                        $addToCusInvDetails['invoiceAmountCurrencyER'] = $master->localCurrencyER;

                        $addToCusInvDetails['unitCost'] = $item->taxAmount;
                        $addToCusInvDetails['salesPrice'] = $item->taxAmount;
                        $addToCusInvDetails['invoiceAmount'] = $item->taxAmount;

                        $addToCusInvDetails['localCurrency'] = $master->localCurrencyID;
                        $addToCusInvDetails['localCurrencyER'] = $master->localCurrencyER;

                        $addToCusInvDetails['comRptCurrency'] = $master->companyReportingCurrencyID;
                        $addToCusInvDetails['comRptCurrencyER'] = $master->companyReportingER;
                        $addToCusInvDetails["comRptAmount"] = $item->taxAmount / $master->companyReportingER;
                        $addToCusInvDetails["localAmount"] = $item->taxAmount;

                        $addToCusInvDetails['unitOfMeasure'] = 0;
                        $addToCusInvDetails['invoiceQty'] = 1;

                        CustomerInvoiceDirectDetail::create($addToCusInvDetails);
                    }


                    $serviceItems = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('pos_source_menusalesmaster.menuSalesID as menuSalesID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, SUM(pos_source_menusalesservicecharge.serviceChargeAmount) as serviceChargeAmount, pos_source_menusalesservicecharge.GLAutoID as glCode')
                        ->join('pos_source_menusalesservicecharge', 'pos_source_menusalesservicecharge.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                        ->where('pos_source_menusalesmaster.menuSalesID',  $invoice->menuSalesID)
                        ->where('pos_source_menusalesmaster.isWastage', 0)
                        ->groupBy('pos_source_menusalesservicecharge.GLAutoID')
                        ->groupBy('pos_source_menusalesmaster.menuSalesID')
                        ->get();

                    foreach ($serviceItems as $item) {

                        $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $item->glCode)->first();

                        $addToCusInvDetails['custInvoiceDirectID'] = $custInvoiceDirectAutoID;
                        $addToCusInvDetails['companyID'] = $master->companyID;
                        $addToCusInvDetails['serviceLineSystemID'] = $serviceLineSystemID;
                        $addToCusInvDetails['serviceLineCode'] = $serviceLineCode;
                        $addToCusInvDetails['customerID'] = $master->customerID;
                        if ($chartOfAccount) {
                            $addToCusInvDetails['glSystemID'] = $chartOfAccount->chartOfAccountSystemID;
                            $addToCusInvDetails['glCode'] = $chartOfAccount->AccountCode;
                            $addToCusInvDetails['glCodeDes'] = $chartOfAccount->AccountDescription;
                            $addToCusInvDetails['accountType'] = $chartOfAccount->catogaryBLorPL;
                        }
                        $addToCusInvDetails['comments'] = $master->comments;
                        $addToCusInvDetails['invoiceAmountCurrency'] = $master->custTransactionCurrencyID;
                        $addToCusInvDetails['invoiceAmountCurrencyER'] = $master->localCurrencyER;

                        $addToCusInvDetails['unitCost'] = $item->serviceChargeAmount;
                        $addToCusInvDetails['salesPrice'] = $item->serviceChargeAmount;
                        $addToCusInvDetails['invoiceAmount'] = $item->serviceChargeAmount;

                        $addToCusInvDetails['localCurrency'] = $master->localCurrencyID;
                        $addToCusInvDetails['localCurrencyER'] = $master->localCurrencyER;

                        $addToCusInvDetails['comRptCurrency'] = $master->companyReportingCurrencyID;
                        $addToCusInvDetails['comRptCurrencyER'] = $master->companyReportingER;
                        $addToCusInvDetails["comRptAmount"] = $item->serviceChargeAmount / $master->companyReportingER;
                        $addToCusInvDetails["localAmount"] = $item->serviceChargeAmount;

                        $addToCusInvDetails['unitOfMeasure'] = 0;
                        $addToCusInvDetails['invoiceQty'] = 1;

                        CustomerInvoiceDirectDetail::create($addToCusInvDetails);
                    }

                    //promotion discount with gl
                    $promotionItems = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('pos_source_menusalesmaster.menuSalesID as menuSalesID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalesmaster.promotionDiscountAmount as promotionAmount, pos_source_menusalesmaster.promotionGLCode as glCode')
                        ->where('pos_source_menusalesmaster.menuSalesID',  $invoice->menuSalesID)
                        ->where('pos_source_menusalesmaster.isWastage', 0)
                        ->whereNotNull('pos_source_menusalesmaster.promotionGLCode')
                        ->get();

                    foreach ($promotionItems as $item) {

                        $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $item->glCode)->first();

                        $addToCusInvDetails['custInvoiceDirectID'] = $custInvoiceDirectAutoID;
                        $addToCusInvDetails['companyID'] = $master->companyID;
                        $addToCusInvDetails['serviceLineSystemID'] = $serviceLineSystemID;
                        $addToCusInvDetails['serviceLineCode'] = $serviceLineCode;
                        $addToCusInvDetails['customerID'] = $master->customerID;
                        if ($chartOfAccount) {
                            $addToCusInvDetails['glSystemID'] = $chartOfAccount->chartOfAccountSystemID;
                            $addToCusInvDetails['glCode'] = $chartOfAccount->AccountCode;
                            $addToCusInvDetails['glCodeDes'] = $chartOfAccount->AccountDescription;
                            $addToCusInvDetails['accountType'] = $chartOfAccount->catogaryBLorPL;
                        }
                        $addToCusInvDetails['comments'] = $master->comments;
                        $addToCusInvDetails['invoiceAmountCurrency'] = $master->custTransactionCurrencyID;
                        $addToCusInvDetails['invoiceAmountCurrencyER'] = $master->localCurrencyER;

                        $addToCusInvDetails['unitCost'] = $item->promotionAmount;
                        $addToCusInvDetails['salesPrice'] = $item->promotionAmount;
                        $addToCusInvDetails['invoiceAmount'] = $item->promotionAmount;

                        $addToCusInvDetails['localCurrency'] = $master->localCurrencyID;
                        $addToCusInvDetails['localCurrencyER'] = $master->localCurrencyER;

                        $addToCusInvDetails['comRptCurrency'] = $master->companyReportingCurrencyID;
                        $addToCusInvDetails['comRptCurrencyER'] = $master->companyReportingER;
                        $addToCusInvDetails["comRptAmount"] = $item->promotionAmount / $master->companyReportingER;
                        $addToCusInvDetails["localAmount"] = $item->promotionAmount;

                        $addToCusInvDetails['unitOfMeasure'] = 0;
                        $addToCusInvDetails['invoiceQty'] = 1;

                        CustomerInvoiceDirectDetail::create($addToCusInvDetails);
                    }


                    $params = array('autoID' => $customerInvoiceDirects->custInvoiceDirectAutoID,
                        'company' => $customerInvoiceDirects->companySystemID,
                        'document' => $customerInvoiceDirects->documentSystemiD,
                        'segment' => '',
                        'category' => '',
                        'amount' => ''
                    );


                    $confirm = \Helper::confirmDocument($params);
                    if (!$confirm["success"]) {

                        return $this->sendError($confirm["message"], 500);
                    } else {

                    }

                    $documentApproveds = DocumentApproved::where('documentSystemCode', $customerInvoiceDirects->custInvoiceDirectAutoID)->where('documentSystemID', 20)->get();
                    Log::useFiles(storage_path() . '/logs/approval_setup.log');
                    $documentApproval = array();
                    foreach ($documentApproveds as $documentApproved) {

                        $documentApproval["approvalLevelID"] = $documentApproved->approvalLevelID;
                        $documentApproval["documentApprovedID"] = $documentApproved->documentApprovedID;
                        $documentApproval["documentSystemCode"] = $customerInvoiceDirects->custInvoiceDirectAutoID;
                        $documentApproval["documentSystemID"] = 20;
                        $documentApproval["companySystemID"] = $customerInvoiceDirects->companySystemID;
                        $documentApproval["approvedComments"] = "Approved by RPOS";
                        $documentApproval["rollLevelOrder"] = $documentApproved->rollLevelOrder;
                        $documentApproval["db"] = $db;
                        
                        $approve = \Helper::approveDocument($documentApproval);
                        if (!$approve["success"]) {
                            return $this->sendError($approve["message"]);
                        }
                        Log::info('---- Doc Approval -----' . $documentApproveds);

                    }


                    \Illuminate\Support\Facades\DB::commit();
                }
                $logs = POSFinanceLog::where('shiftId', $shiftId)->update(['status' => 2]);

                \Illuminate\Support\Facades\DB::commit();

                
            }
            catch (\Exception $exception) {
                \Illuminate\Support\Facades\DB::rollback();
                return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
            }

            $hasSales = POSSourceMenuSalesMaster::where('shiftId', $shiftId)->where('isCreditSales', 0)->get();
            if(!$hasSales->isEmpty()) {
                GeneralLedgerInsert::dispatch($masterData, $db);
                POSItemLedgerInsert::dispatch($masterData);
                BankLedgerInsert::dispatch($masterData);
                $taxLedgerData = null;
                TaxLedgerInsert::dispatch($masterData, $taxLedgerData, $db);
            }
        }


    }

    public function updateVatOfSalesReturn($salesReturnID)
    {
        $salesReturnMasterData = SalesReturn::find($salesReturnID);

        $totalAmount = 0;
        $totalTaxAmount = 0;
        if ($salesReturnMasterData->returnType == 1) {
            $invoiceDetails = SalesReturnDetail::where('salesReturnID', $salesReturnID)
                ->with(['delivery_order_detail'])
                ->get();

            foreach ($invoiceDetails as $key => $value) {
                $totalTaxAmount += $value->qtyReturned * ((isset($value->delivery_order_detail->VATAmount) && !is_null($value->delivery_order_detail->VATAmount)) ? $value->delivery_order_detail->VATAmount : 0);
            }
        } else {
            $invoiceDetails = SalesReturnDetail::where('salesReturnID', $salesReturnID)
                ->with(['sales_invoice_detail'])
                ->get();

            foreach ($invoiceDetails as $key => $value) {
                $totalTaxAmount += $value->qtyReturned * ((isset($value->sales_invoice_detail->VATAmount) && !is_null($value->sales_invoice_detail->VATAmount)) ? $value->sales_invoice_detail->VATAmount : 0);
            }
        }

        if ($totalTaxAmount > 0) {
            $taxDelete = Taxdetail::where('documentSystemCode', $salesReturnID)
                ->where('documentSystemID', 87)
                ->delete();

            $res = $this->saveSalesReturnTaxDetails($salesReturnID, $totalTaxAmount);

            if (!$res['status']) {
                return ['status' => false, 'message' => $res['message']];
            }
        } else {
            $taxDelete = Taxdetail::where('documentSystemCode', $salesReturnID)
                ->where('documentSystemID', 87)
                ->delete();

            $vatAmount['vatOutputGLCodeSystemID'] = 0;
            $vatAmount['vatOutputGLCode'] = null;
            $vatAmount['VATPercentage'] = 0;
            $vatAmount['VATAmount'] = 0;
            $vatAmount['VATAmountLocal'] = 0;
            $vatAmount['VATAmountRpt'] = 0;

            SalesReturn::where('id', $salesReturnID)->update($vatAmount);

        }

        return ['status' => true];
    }


    public function saveSalesReturnTaxDetails($salesReturnID, $totalVATAmount)
    {
        $percentage = 0;
        $taxMasterAutoID = 0;

        $master = SalesReturn::where('id', $salesReturnID)->first();

        if (empty($master)) {
            return ['status' => false, 'message' => 'Sales Return not found.'];
        }

        $invoiceDetail = SalesReturnDetail::where('salesReturnID', $salesReturnID)->first();

        if (empty($invoiceDetail)) {
            return ['status' => false, 'message' => 'Sales Return Details not found.'];
        }

        $totalAmount = 0;
        $decimal = \Helper::getCurrencyDecimalPlace($master->transactionCurrencyID);

        $totalDetail = SalesReturnDetail::select(\Illuminate\Support\Facades\DB::raw("SUM(transactionAmount) as amount"))
            ->where('salesReturnID', $salesReturnID)
            ->groupBy('salesReturnID')
            ->first();

        if (!empty($totalDetail)) {
            $totalAmount = $totalDetail->amount;
        }

        if ($totalAmount > 0) {
            $percentage = ($totalVATAmount / $totalAmount) * 100;
        }

        $Taxdetail = Taxdetail::where('documentSystemCode', $salesReturnID)
            ->where('documentSystemID', 87)
            ->first();

        if (!empty($Taxdetail)) {
            return ['status' => false, 'message' => 'VAT Detail Already exist.'];
        }

        $currencyConversion = \Helper::currencyConversion($master->companySystemID, $master->transactionCurrencyID, $master->transactionCurrencyID, $totalVATAmount);


        $_post['taxMasterAutoID'] = $taxMasterAutoID;
        $_post['companyID'] = $master->companyID;
        $_post['companySystemID'] = $master->companySystemID;
        $_post['documentID'] = 'SLR';
        $_post['documentSystemID'] = $master->documentSystemID;
        $_post['documentSystemCode'] = $salesReturnID;
        $_post['documentCode'] = $master->salesReturnCode;
        $_post['taxShortCode'] = ''; //$taxMaster->taxShortCode;
        $_post['taxDescription'] = ''; //$taxMaster->taxDescription;
        $_post['taxPercent'] = $percentage; //$taxMaster->taxPercent;
        $_post['payeeSystemCode'] = $master->customerID; //$taxMaster->payeeSystemCode;
        $_post['currency'] = $master->transactionCurrencyID;
        $_post['currencyER'] = $master->transactionCurrencyER;
        $_post['amount'] = round($totalVATAmount, $decimal);
        $_post['payeeDefaultCurrencyID'] = $master->transactionCurrencyID;
        $_post['payeeDefaultCurrencyER'] = $master->transactionCurrencyER;
        $_post['payeeDefaultAmount'] = round($totalVATAmount, $decimal);
        $_post['localCurrencyID'] = $master->companyLocalCurrencyID;
        $_post['localCurrencyER'] = $master->companyLocalCurrencyER;

        $_post['rptCurrencyID'] = $master->companyReportingCurrencyID;
        $_post['rptCurrencyER'] = $master->companyReportingCurrencyER;

        if ($_post['currency'] == $_post['rptCurrencyID']) {
            $MyRptAmount = $totalVATAmount;
        } else {
            if ($_post['rptCurrencyER'] > $_post['currencyER']) {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                }
            } else {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                }
            }
        }
        $_post["rptAmount"] = \Helper::roundValue($MyRptAmount);
        if ($_post['currency'] == $_post['localCurrencyID']) {
            $MyLocalAmount = $totalVATAmount;
        } else {
            if ($_post['localCurrencyER'] > $_post['currencyER']) {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                }
            } else {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                }
            }
        }

        $_post["localAmount"] = \Helper::roundValue($MyLocalAmount);

        Taxdetail::create($_post);
        $company = Company::select('vatOutputGLCode', 'vatOutputGLCodeSystemID')->where('companySystemID', $master->companySystemID)->first();

        $vatAmount['vatOutputGLCodeSystemID'] = $company->vatOutputGLCodeSystemID;
        $vatAmount['vatOutputGLCode'] = $company->vatOutputGLCode;
        $vatAmount['VATPercentage'] = $percentage;
        $vatAmount['VATAmount'] = $_post['amount'];
        $vatAmount['VATAmountLocal'] = $_post["localAmount"];
        $vatAmount['VATAmountRpt'] = $_post["rptAmount"];

        SalesReturn::where('id', $salesReturnID)->update($vatAmount);

        return ['status' => true];
    }


    private function updateInvoiceReturnedStatus($custInvoiceDirectAutoID){

        $status = 0;
        $invQty = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID',$custInvoiceDirectAutoID)->sum('qtyIssuedDefaultMeasure');

        if($invQty!=0) {
            $doQty = SalesReturnDetail::where('custInvoiceDirectAutoID',$custInvoiceDirectAutoID)->sum('qtyReturnedDefaultMeasure');
            if($invQty == $doQty){
                $status = 2;    // fully invoiced
            }else{
                $status = 1;    // partially invoiced
            }
        }
        return CustomerInvoiceDirect::where('custInvoiceDirectAutoID',$custInvoiceDirectAutoID)->update(['returnStatus'=>$status]);

    }

    public function updateTotalVAT($custInvoiceDirectAutoID)
    {
        $invoiceDetails = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)
            ->get();

        $totalVATAmount = 0;
        $invoice = CustomerInvoiceDirect::find($custInvoiceDirectAutoID);

        foreach ($invoiceDetails as $key => $value) {
            $totalVATAmount += $value->invoiceQty * $value->VATAmount;
        }

        $taxDelete = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('documentSystemID', 20)
            ->delete();

        if ($totalVATAmount > 0) {
            $res = $this->savecustomerInvoiceTaxDetails($custInvoiceDirectAutoID, $totalVATAmount);

            if (!$res['status']) {
                return ['status' => false, 'message' => $res['message']];
            }
        } else {
            $vatAmount['vatOutputGLCodeSystemID'] = null;
            $vatAmount['vatOutputGLCode'] = null;
            $vatAmount['VATPercentage'] = 0;
            $vatAmount['VATAmount'] = 0;
            $vatAmount['VATAmountLocal'] = 0;
            $vatAmount['VATAmountRpt'] = 0;

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);
        }

        return ['status' => true];
    }

    public function savecustomerInvoiceTaxDetails($custInvoiceDirectAutoID, $totalVATAmount)
    {
        $percentage = 0;
        $taxMasterAutoID = 0;

        $master = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();

        if (empty($master)) {
            return ['status' => false, 'message' => 'Customer Invoice not found.'];
        }

        $invoiceDetail = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();

        if (empty($invoiceDetail)) {
            return ['status' => false, 'message' => 'Invoice Details not found.'];
        }

        $totalAmount = 0;
        $decimal = \Helper::getCurrencyDecimalPlace($master->custTransactionCurrencyID);

        $totalDetail = CustomerInvoiceItemDetails::select(\Illuminate\Support\Facades\DB::raw("SUM(sellingTotal) as amount"))->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();
        if (!empty($totalDetail)) {
            $totalAmount = $totalDetail->amount;
        }

        if ($totalAmount > 0) {
            $percentage = ($totalVATAmount / $totalAmount) * 100;
        }

        $Taxdetail = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('documentSystemID', 20)
            ->first();

        if (!empty($Taxdetail)) {
            return ['status' => false, 'message' => 'VAT Detail Already exist.'];
        }

        $currencyConversion = \Helper::currencyConversion($master->companySystemID, $master->custTransactionCurrencyID, $master->custTransactionCurrencyID, $totalVATAmount);


        $_post['taxMasterAutoID'] = $taxMasterAutoID;
        $_post['companyID'] = $master->companyID;
        $_post['companySystemID'] = $master->companySystemID;
        $_post['documentID'] = 'INV';
        $_post['documentSystemID'] = $master->documentSystemiD;
        $_post['documentSystemCode'] = $custInvoiceDirectAutoID;
        $_post['documentCode'] = $master->bookingInvCode;
        $_post['taxShortCode'] = ''; //$taxMaster->taxShortCode;
        $_post['taxDescription'] = ''; //$taxMaster->taxDescription;
        $_post['taxPercent'] = $percentage; //$taxMaster->taxPercent;
        $_post['payeeSystemCode'] = $master->customerID; //$taxMaster->payeeSystemCode;
        $_post['currency'] = $master->custTransactionCurrencyID;
        $_post['currencyER'] = $master->custTransactionCurrencyER;
        $_post['amount'] = round($totalVATAmount, $decimal);
        $_post['payeeDefaultCurrencyID'] = $master->custTransactionCurrencyID;
        $_post['payeeDefaultCurrencyER'] = $master->custTransactionCurrencyER;
        $_post['payeeDefaultAmount'] = round($totalVATAmount, $decimal);
        $_post['localCurrencyID'] = $master->localCurrencyID;
        $_post['localCurrencyER'] = $master->localCurrencyER;

        $_post['rptCurrencyID'] = $master->companyReportingCurrencyID;
        $_post['rptCurrencyER'] = $master->companyReportingER;

        if ($_post['currency'] == $_post['rptCurrencyID']) {
            $MyRptAmount = $totalVATAmount;
        } else {
            if ($_post['rptCurrencyER'] > $_post['currencyER']) {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                }
            } else {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                }
            }
        }
        $_post["rptAmount"] = \Helper::roundValue($MyRptAmount);
        if ($_post['currency'] == $_post['localCurrencyID']) {
            $MyLocalAmount = $totalVATAmount;
        } else {
            if ($_post['localCurrencyER'] > $_post['currencyER']) {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                }
            } else {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                }
            }
        }

        $_post["localAmount"] = \Helper::roundValue($MyLocalAmount);

        Taxdetail::create($_post);
        $company = Company::select('vatOutputGLCode', 'vatOutputGLCodeSystemID')->where('companySystemID', $master->companySystemID)->first();

        $vatAmount['vatOutputGLCodeSystemID'] = $company->vatOutputGLCodeSystemID;
        $vatAmount['vatOutputGLCode'] = $company->vatOutputGLCode;
        $vatAmount['VATPercentage'] = $percentage;
        $vatAmount['VATAmount'] = $_post['amount'];
        $vatAmount['VATAmountLocal'] = $_post["localAmount"];
        $vatAmount['VATAmountRpt'] = $_post["rptAmount"];


        CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);

        return ['status' => true];
    }
    public function postPosEntries(Request $request)
    {

        $shiftId = $request->shiftId;
        $isPostGroupBy = $request->isPostGroupBy;
        $db = isset($request->db) ? $request->db : "";


        $isInsufficient = 0;

        $shiftDetails = POSSOURCEShiftDetails::where('shiftID', $shiftId)->first();
        $logs = POSFinanceLog::where('shiftID', $shiftId)->first();

        if (empty($logs)) {

            $postedShifts = POSFinanceLog::groupBy('shiftId')->where('status', 2)->get();
            $postedShifts = collect($postedShifts)->pluck('shiftId');

            POSGLEntries::where('shiftId', $shiftId)->delete();
            POSItemGLEntries::where('shiftId', $shiftId)->delete();
            POSBankGLEntries::where('shiftId', $shiftId)->delete();
            POSTaxGLEntries::where('shiftId', $shiftId)->delete();

            POSGLEntries::whereIn('shiftId', $postedShifts)->delete();
            POSItemGLEntries::whereIn('shiftId', $postedShifts)->delete();
            POSBankGLEntries::whereIn('shiftId', $postedShifts)->delete();
            POSTaxGLEntries::whereIn('shiftId', $postedShifts)->delete();


            if ($shiftDetails->posType == 1) {

                $hasItems = POSInvoiceSource::where('shiftId', $shiftId)->get();
                $hasItemsSR = POSSourceSalesReturn::where('shiftId', $shiftId)->get();
                if ($hasItems->isEmpty() && $hasItemsSR->isEmpty()) {
                    return $this->sendError('Invoices not found');
                }

                $shiftLogArray = [
                    'startTime' => $shiftDetails->startTime,
                    'endTime' => $shiftDetails->endTime,
                    'status' => 1,
                    'postGroupByYN' => 0,
                    'shiftId' => $shiftId
                ];
                $logs = POSFinanceLog::create($shiftLogArray);

                $bankGLArray = array();
                $itemGLArray = array();
                $taxGLArray = array();
                $itemArray = array();
                $bankArray = array();
                $costGLArray = array();
                $inventoryGLArray = array();


                if ($isPostGroupBy == 0) {

                    $bankGL = DB::table('pos_source_invoice')
                        ->selectRaw('SUM(pos_source_invoicepayments.amount) as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoicepayments.GLCode as glCode, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID')
                        ->join('pos_source_invoicepayments', 'pos_source_invoicepayments.invoiceID', '=', 'pos_source_invoice.invoiceID')
                        ->where('pos_source_invoice.shiftID', $shiftId)
                        ->groupBy('pos_source_invoice.invoiceID')
                        ->groupBy('pos_source_invoicepayments.paymentConfigMasterID')
                        ->groupBy('pos_source_invoicepayments.GLCode')
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->get();


                    $invItems = DB::table('pos_source_invoicedetail')
                        ->selectRaw('pos_source_invoicedetail.companyLocalAmount as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub, financeitemcategorysub.financeGLcodeRevenueSystemID as glCode, itemmaster.financeCategoryMaster as categoryID, financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, financeitemcategorysub.includePLForGRVYN as glYN, pos_source_invoicedetail.qty as qty, pos_source_invoicedetail.price as price, pos_source_invoicedetail.UOMID as uom, pos_source_invoice.wareHouseAutoID as wareHouseID')
                        ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->where('pos_source_invoice.shiftID', $shiftId)
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->get();

                    $invItemsPL = DB::table('pos_source_invoicedetail')
                        ->selectRaw('SUM(pos_source_invoicedetail.totalCost) as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub,  financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeCogsGLcodePLSystemID as plGLCode, itemmaster.financeCategoryMaster as categoryID, pos_source_invoicedetail.qty as qty, pos_source_invoicedetail.UOMID as uom, pos_source_invoice.wareHouseAutoID as wareHouseID, financeitemcategorysub.includePLForGRVYN as glYN')
                        ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->join('itemassigned', 'itemassigned.itemCodeSystem', '=', 'itemmaster.itemCodeSystem')
                        ->where('pos_source_invoice.shiftID', $shiftId)
                        ->where('itemassigned.companySystemID', $shiftDetails->companyID)
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->groupBy('financeitemcategorysub.financeCogsGLcodePLSystemID')
                        ->groupBy('pos_source_invoicedetail.invoiceID')
                        ->get();


                    $invItemsBS = DB::table('pos_source_invoicedetail')
                        ->selectRaw('SUM(pos_source_invoicedetail.totalCost) as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub,  financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, itemmaster.financeCategoryMaster as categoryID, pos_source_invoicedetail.qty as qty, pos_source_invoicedetail.UOMID as uom, pos_source_invoice.wareHouseAutoID as wareHouseID, financeitemcategorysub.includePLForGRVYN as glYN')
                        ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->join('itemassigned', 'itemassigned.itemCodeSystem', '=', 'itemmaster.itemCodeSystem')
                        ->where('pos_source_invoice.shiftID', $shiftId)
                        ->where('itemassigned.companySystemID', $shiftDetails->companyID)
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->groupBy('financeitemcategorysub.financeGLcodebBSSystemID')
                        ->groupBy('pos_source_invoicedetail.invoiceID')
                        ->get();


                    $taxItems = DB::table('pos_source_invoicedetail')
                        ->selectRaw('pos_source_taxledger.amount as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub, financeitemcategorysub.financeGLcodeRevenueSystemID as glCode, pos_source_taxledger.amount as taxAmount, pos_source_taxledger.taxMasterID as taxMasterID, erp_taxmaster_new.outputVatGLAccountAutoID as outputVatGLCode')
                        ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->join('pos_source_taxledger', 'pos_source_taxledger.documentDetailAutoID', '=', 'pos_source_invoicedetail.invoiceDetailsID')
                        ->join('pos_source_taxmaster', 'pos_source_taxmaster.taxMasterAutoID', '=', 'pos_source_taxledger.taxMasterID')
                        ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_taxmaster.erp_tax_master_id')
                        ->where('pos_source_invoice.shiftID', $shiftId)
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->get();

                    $bankItems = DB::table('pos_source_invoice')
                        ->selectRaw('SUM(pos_source_invoice.netTotal) as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_paymentglconfigdetail.erp_bank_acc_id as bankID, pos_source_invoicepayments.GLCode as glCode, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicepayments.paymentConfigDetailID as payDetailID')
                        ->join('pos_source_invoicepayments', 'pos_source_invoicepayments.invoiceID', '=', 'pos_source_invoice.invoiceID')
                        ->join('pos_source_paymentglconfigdetail', 'pos_source_paymentglconfigdetail.ID', '=', 'pos_source_invoicepayments.paymentConfigDetailID')
                        ->where('pos_source_invoice.shiftID', $shiftId)
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->groupBy('pos_source_invoice.shiftID')
                        ->groupBy('pos_source_invoice.invoiceID')
                        ->get();

                }

                if ($isPostGroupBy == 1) {

                    $bankGL = DB::table('pos_source_invoice')
                        ->selectRaw('SUM(pos_source_invoice.netTotal) as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoicepayments.GLCode as glCode, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID')
                        ->join('pos_source_invoicepayments', 'pos_source_invoicepayments.invoiceID', '=', 'pos_source_invoice.invoiceID')
                        ->where('pos_source_invoice.shiftID', $shiftId)
                        ->groupBy('pos_source_invoice.shiftID')
                        ->groupBy('pos_source_invoicepayments.GLCode')
                        ->groupBy('pos_source_invoice.invoiceID')
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->get();


                    $invItems = DB::table('pos_source_invoicedetail')
                        ->selectRaw('SUM(pos_source_invoicedetail.companyLocalAmount) as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub, financeitemcategorysub.financeGLcodeRevenueSystemID as glCode, itemmaster.financeCategoryMaster as categoryID, financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, financeitemcategorysub.includePLForGRVYN as glYN, pos_source_invoicedetail.qty as qty, pos_source_invoicedetail.price as price, pos_source_invoicedetail.UOMID as uom, pos_source_invoice.wareHouseAutoID as wareHouseID')
                        ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->where('pos_source_invoice.shiftID', $shiftId)
                        ->groupBy('pos_source_invoice.shiftID')
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->get();


                    $invItemsPLBS = DB::table('pos_source_invoicedetail')
                        ->selectRaw('SUM(itemassigned.wacValueLocal * pos_source_invoicedetail.qty) as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub,  financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, itemmaster.financeCategoryMaster as categoryID, pos_source_invoicedetail.qty as qty, itemassigned.wacValueLocal as price, pos_source_invoicedetail.UOMID as uom, pos_source_invoice.wareHouseAutoID as wareHouseID, financeitemcategorysub.includePLForGRVYN as glYN')
                        ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->join('itemassigned', 'itemassigned.itemCodeSystem', '=', 'itemmaster.itemCodeSystem')
                        ->where('pos_source_invoice.shiftID', $shiftId)
                        ->groupBy('pos_source_invoice.shiftID')
                        ->where('itemassigned.companySystemID', $shiftDetails->companyID)
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->get();


                    $taxItems = DB::table('pos_source_invoicedetail')
                        ->selectRaw('SUM(pos_source_taxledger.amount) as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub, financeitemcategorysub.financeGLcodeRevenueSystemID as glCode, SUM(pos_source_taxledger.amount) as taxAmount, pos_source_taxledger.taxMasterID as taxMasterID, erp_taxmaster_new.outputVatGLAccountAutoID as outputVatGLCode')
                        ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->join('pos_source_taxledger', 'pos_source_taxledger.documentDetailAutoID', '=', 'pos_source_invoicedetail.invoiceDetailsID')
                        ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_taxledger.taxMasterID')
                        ->where('pos_source_invoice.shiftID', $shiftId)
                        ->groupBy('pos_source_invoice.shiftID')
                        ->groupBy('pos_source_invoice.invoiceID')
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->get();


                    $bankItems = DB::table('pos_source_invoice')
                        ->selectRaw('SUM(pos_source_invoice.netTotal) as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_paymentglconfigdetail.erp_bank_acc_id as bankID, pos_source_invoicepayments.GLCode as glCode, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicepayments.paymentConfigDetailID as payDetailID')
                        ->join('pos_source_invoicepayments', 'pos_source_invoicepayments.invoiceID', '=', 'pos_source_invoice.invoiceID')
                        ->join('pos_source_paymentglconfigdetail', 'pos_source_paymentglconfigdetail.ID', '=', 'pos_source_invoicepayments.paymentConfigDetailID')
                        ->where('pos_source_invoice.shiftID', $shiftId)
                        ->groupBy('pos_source_invoice.shiftID')
                        ->groupBy('pos_source_invoice.invoiceID')
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->get();


                    //sales returns
                    $netTotGLSr = DB::table('pos_source_salesreturn')
                        ->selectRaw('SUM(pos_source_salesreturn.netTotal) as amount, pos_source_salesreturn.isRefund as isRefund,pos_source_salesreturn.shiftID as shiftId')
                        ->where('pos_source_salesreturn.shiftID', $shiftId)
                        ->groupBy('pos_source_salesreturn.isRefund')
                        ->groupBy('pos_source_salesreturn.shiftID')
                        ->get();


                    $invItemsSr = DB::table('pos_source_salesreturndetails')
                        ->selectRaw('pos_source_salesreturndetails.companyLocalAmount as amount, pos_source_salesreturn.invoiceID as invoiceID, pos_source_salesreturn.shiftID as shiftId, pos_source_salesreturn.companyID as companyID, pos_source_salesreturndetails.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub, financeitemcategorysub.financeGLcodeRevenueSystemID as glCode, itemmaster.financeCategoryMaster as categoryID, financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, financeitemcategorysub.includePLForGRVYN as glYN, pos_source_salesreturndetails.qty as qty, pos_source_salesreturndetails.price as price, pos_source_salesreturndetails.UOMID as uom, pos_source_salesreturn.wareHouseAutoID as wareHouseID')
                        ->join('pos_source_salesreturn', 'pos_source_salesreturn.invoiceID', '=', 'pos_source_salesreturndetails.invoiceID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_salesreturndetails.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->where('pos_source_salesreturn.shiftID', $shiftId)
                        ->groupBy('pos_source_invoice.shiftID')
                        ->get();


                    $invItemsPLBSSr = DB::table('pos_source_salesreturndetails')
                        ->selectRaw('pos_source_salesreturndetails.qty * itemassigned.wacValueLocal as amount, pos_source_salesreturn.invoiceID as invoiceID, pos_source_salesreturn.shiftID as shiftId, pos_source_salesreturn.companyID as companyID, pos_source_salesreturndetails.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub,  financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, itemmaster.financeCategoryMaster as categoryID, pos_source_salesreturndetails.qty as qty, itemassigned.wacValueLocal as price, pos_source_salesreturndetails.UOMID as uom, pos_source_salesreturn.wareHouseAutoID as wareHouseID, financeitemcategorysub.includePLForGRVYN as glYN')
                        ->join('pos_source_salesreturn', 'pos_source_salesreturn.invoiceID', '=', 'pos_source_salesreturndetails.invoiceID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_salesreturndetails.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->join('itemassigned', 'itemassigned.itemCodeSystem', '=', 'itemmaster.itemCodeSystem')
                        ->where('pos_source_salesreturn.shiftID', $shiftId)
                        ->where('itemassigned.companySystemID', $shiftDetails->companyID)
                        ->groupBy('pos_source_invoice.shiftID')
                        ->get();

                }


                foreach ($bankGL as $gl) {

                    $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $bankGLArray[] = array(
                        'shiftId' => $gl->shiftId,
                        'invoiceID' => $gl->invoiceID,
                        'documentSystemId' => 110,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->glCode,
                        'logId' => $logs['id'],
                        'amount' => round($gl->amount,3)
                    );

                }

                foreach ($invItems as $gl) {

                    $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $itemGLArray[] = array(
                        'shiftId' => $gl->shiftId,
                        'invoiceID' => $gl->invoiceID,
                        'documentSystemId' => 110,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->glCode,
                        'logId' => $logs['id'],
                        'amount' => round($gl->amount * -1,3)
                    );

                }

                foreach ($taxItems as $gl) {

                    $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $taxGLArray[] = array(
                        'shiftId' => $gl->shiftId,
                        'invoiceID' => $gl->invoiceID,
                        'documentSystemId' => 110,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->outputVatGLCode,
                        'logId' => $logs['id'],
                        'amount' => round($gl->taxAmount * -1,3)
                    );

                }

                POSTaxGLEntries::insert($taxGLArray);

                foreach ($invItems as $item) {
                    $itemArray[] = array(
                        'shiftId' => $item->shiftId,
                        'invoiceID' => $item->invoiceID,
                        'itemAutoId' => $item->itemID,
                        'uom' => $item->uom,
                        'qty' => $item->qty,
                        'isReturnYN' => 0,
                        'wareHouseId' => $item->wareHouseID
                    );

                }

                POSItemGLEntries::insert($itemArray);


                foreach ($bankItems as $item) {
                    $bankArray[] = array(
                        'shiftId' => $item->shiftId,
                        'invoiceID' => $item->invoiceID,
                        'bankAccId' => $item->bankID,
                        'logId' => $logs->id,
                        'isReturnYN' => 0,
                        'amount' => round($item->amount,3)
                    );

                }
                POSBankGLEntries::insert($bankArray);


                POSGLEntries::insert($bankGLArray);
                POSGLEntries::insert($itemGLArray);
                POSGLEntries::insert($taxGLArray);


                foreach ($invItemsPL as $gl) {

                    $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    if ($gl->categoryID == 1) {
                        $costGLArray[] = array(
                            'shiftId' => $gl->shiftId,
                            'invoiceID' => $gl->invoiceID,
                            'documentSystemId' => 110,
                            'documentCode' => $documentCode,
                            'glCode' => $gl->plGLCode,
                            'logId' => $logs['id'],
                            'amount' => round($gl->amount,3)
                        );
                    }
                }
                POSGLEntries::insert($costGLArray);
                foreach ($invItemsBS as $gl) {
                    if ($gl->categoryID == 1) {
                        $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                        if ($gl->glYN == -1) {
                            $inventoryGLArray[] = array(
                                'shiftId' => $gl->shiftId,
                                'invoiceID' => $gl->invoiceID,
                                'documentSystemId' => 110,
                                'documentCode' => $documentCode,
                                'glCode' => $gl->plGLCode,
                                'logId' => $logs['id'],
                                'amount' => round($gl->amount * -1,3)
                            );
                        } else {
                            $inventoryGLArray[] = array(
                                'shiftId' => $gl->shiftId,
                                'invoiceID' => $gl->invoiceID,
                                'documentSystemId' => 110,
                                'documentCode' => $documentCode,
                                'glCode' => $gl->bsGLCode,
                                'logId' => $logs['id'],
                                'amount' => round($gl->amount * -1,3)
                            );
                        }
                    }
                }
                POSGLEntries::insert($inventoryGLArray);



                //start of reverse entries for sales return

                $bankGLReturnArray = array();
                $itemGLReturnArray = array();
                $taxGLReturnArray = array();
                $itemReturnArray = array();
                $bankReturnArray = array();
                $costGLReturnArray = array();
                $inventoryGLReturnArray = array();


                $returns = DB::table('pos_source_salesreturn')
                    ->selectRaw('pos_source_salesreturn.*')
                    ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_salesreturn.invoiceID')
                    ->where('pos_source_salesreturn.shiftID', $shiftId)
                    ->where('pos_source_invoice.isCreditSales', 0)
                    ->get();

                foreach ($returns as $return) {

                    $bankGL = DB::table('pos_source_invoice')
                        ->selectRaw('pos_source_salesreturn.refundAmount as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoicepayments.GLCode as glCode, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID')
                        ->join('pos_source_invoicepayments', 'pos_source_invoicepayments.invoiceID', '=', 'pos_source_invoice.invoiceID')
                        ->join('pos_source_salesreturn', 'pos_source_salesreturn.invoiceID', '=', 'pos_source_invoice.invoiceID')
                        ->where('pos_source_invoice.invoiceID', $return->invoiceID)
                        ->groupBy('pos_source_invoice.invoiceID')
                        ->groupBy('pos_source_invoicepayments.paymentConfigMasterID')
                        ->groupBy('pos_source_invoicepayments.GLCode')
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->get();


                    $invItems = DB::table('pos_source_invoicedetail')
                        ->selectRaw('pos_source_invoicedetail.companyLocalAmount * pos_source_salesreturndetails.qty / pos_source_invoicedetail.qty as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub, financeitemcategorysub.financeGLcodeRevenueSystemID as glCode, itemmaster.financeCategoryMaster as categoryID, financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, financeitemcategorysub.includePLForGRVYN as glYN, pos_source_invoicedetail.qty as qty, pos_source_invoicedetail.price as price, pos_source_invoicedetail.UOMID as uom, pos_source_invoice.wareHouseAutoID as wareHouseID')
                        ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->join('pos_source_salesreturndetails', 'pos_source_salesreturndetails.invoiceDetailID', '=', 'pos_source_invoicedetail.invoiceDetailsID')
                        ->where('pos_source_invoice.invoiceID', $return->invoiceID)
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->get();

                    $invItemsPL = DB::table('pos_source_invoicedetail')
                        ->selectRaw('SUM(pos_source_invoicedetail.totalCost * pos_source_salesreturndetails.qty / pos_source_invoicedetail.qty) as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub,  financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeCogsGLcodePLSystemID as plGLCode, itemmaster.financeCategoryMaster as categoryID, pos_source_invoicedetail.qty as qty, pos_source_invoicedetail.UOMID as uom, pos_source_invoice.wareHouseAutoID as wareHouseID, financeitemcategorysub.includePLForGRVYN as glYN')
                        ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->join('itemassigned', 'itemassigned.itemCodeSystem', '=', 'itemmaster.itemCodeSystem')
                        ->join('pos_source_salesreturndetails', 'pos_source_salesreturndetails.invoiceDetailID', '=', 'pos_source_invoicedetail.invoiceDetailsID')
                        ->where('pos_source_invoice.invoiceID', $return->invoiceID)
                        ->where('itemassigned.companySystemID', $shiftDetails->companyID)
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->groupBy('financeitemcategorysub.financeCogsGLcodePLSystemID')
                        ->groupBy('pos_source_invoicedetail.invoiceID')
                        ->get();


                    $invItemsBS = DB::table('pos_source_invoicedetail')
                        ->selectRaw('SUM(pos_source_invoicedetail.totalCost * pos_source_salesreturndetails.qty / pos_source_invoicedetail.qty) as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub,  financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, itemmaster.financeCategoryMaster as categoryID, pos_source_invoicedetail.qty as qty, pos_source_invoicedetail.UOMID as uom, pos_source_invoice.wareHouseAutoID as wareHouseID, financeitemcategorysub.includePLForGRVYN as glYN')
                        ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->join('itemassigned', 'itemassigned.itemCodeSystem', '=', 'itemmaster.itemCodeSystem')
                        ->join('pos_source_salesreturndetails', 'pos_source_salesreturndetails.invoiceDetailID', '=', 'pos_source_invoicedetail.invoiceDetailsID')
                        ->where('pos_source_invoice.invoiceID', $return->invoiceID)
                        ->where('itemassigned.companySystemID', $shiftDetails->companyID)
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->groupBy('financeitemcategorysub.financeGLcodebBSSystemID')
                        ->groupBy('pos_source_invoicedetail.invoiceID')
                        ->get();


                    $taxItems = DB::table('pos_source_invoicedetail')
                        ->selectRaw('pos_source_taxledger.amount as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub, financeitemcategorysub.financeGLcodeRevenueSystemID as glCode, pos_source_taxledger.amount * pos_source_salesreturndetails.qty / pos_source_invoicedetail.qty as taxAmount, pos_source_taxledger.taxMasterID as taxMasterID, erp_taxmaster_new.outputVatGLAccountAutoID as outputVatGLCode')
                        ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->join('pos_source_taxledger', 'pos_source_taxledger.documentDetailAutoID', '=', 'pos_source_invoicedetail.invoiceDetailsID')
                        ->join('pos_source_taxmaster', 'pos_source_taxmaster.taxMasterAutoID', '=', 'pos_source_taxledger.taxMasterID')
                        ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_taxmaster.erp_tax_master_id')
                        ->join('pos_source_salesreturndetails', 'pos_source_salesreturndetails.invoiceDetailID', '=', 'pos_source_invoicedetail.invoiceDetailsID')
                        ->where('pos_source_invoice.invoiceID', $return->invoiceID)
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->get();

                    $bankItems = DB::table('pos_source_invoice')
                        ->selectRaw('SUM(pos_source_salesreturn.refundAmount) as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_paymentglconfigdetail.erp_bank_acc_id as bankID, pos_source_invoicepayments.GLCode as glCode, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicepayments.paymentConfigDetailID as payDetailID')
                        ->join('pos_source_invoicepayments', 'pos_source_invoicepayments.invoiceID', '=', 'pos_source_invoice.invoiceID')
                        ->join('pos_source_paymentglconfigdetail', 'pos_source_paymentglconfigdetail.ID', '=', 'pos_source_invoicepayments.paymentConfigDetailID')
                        ->join('pos_source_salesreturn', 'pos_source_salesreturn.invoiceID', '=', 'pos_source_invoice.invoiceID')
                        ->where('pos_source_invoice.invoiceID', $return->invoiceID)
                        ->where('pos_source_invoice.isCreditSales', 0)
                        ->groupBy('pos_source_invoice.shiftID')
                        ->groupBy('pos_source_invoice.invoiceID')
                        ->get();

                    foreach ($bankGL as $gl) {

                        $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                        $bankGLReturnArray[] = array(
                            'shiftId' => $shiftId,
                            'invoiceID' => $gl->invoiceID,
                            'documentSystemId' => 110,
                            'documentCode' => $documentCode,
                            'glCode' => $gl->glCode,
                            'logId' => $logs['id'],
                            'isReturnYN' => 1,
                            'amount' => round($gl->amount * -1,3)
                        );

                    }

                    foreach ($invItems as $gl) {

                        $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                        $itemGLReturnArray[] = array(
                            'shiftId' => $shiftId,
                            'invoiceID' => $gl->invoiceID,
                            'documentSystemId' => 110,
                            'documentCode' => $documentCode,
                            'glCode' => $gl->glCode,
                            'logId' => $logs['id'],
                            'isReturnYN' => 1,
                            'amount' => round($gl->amount,3)
                        );

                    }

                    foreach ($taxItems as $gl) {

                        $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                        $taxGLReturnArray[] = array(
                            'shiftId' => $shiftId,
                            'invoiceID' => $gl->invoiceID,
                            'documentSystemId' => 110,
                            'documentCode' => $documentCode,
                            'glCode' => $gl->outputVatGLCode,
                            'logId' => $logs['id'],
                            'isReturnYN' => 1,
                            'amount' => round($gl->taxAmount,3)
                        );

                    }

                    POSTaxGLEntries::insert($taxGLReturnArray);

                    foreach ($invItems as $item) {
                        $itemReturnArray[] = array(
                            'shiftId' => $shiftId,
                            'invoiceID' => $item->invoiceID,
                            'itemAutoId' => $item->itemID,
                            'uom' => $item->uom,
                            'qty' => $item->qty * -1,
                            'isReturnYN' => 1,
                            'wareHouseId' => $item->wareHouseID
                        );

                    }

                    POSItemGLEntries::insert($itemReturnArray);


                    foreach ($bankItems as $item) {
                        $bankReturnArray[] = array(
                            'shiftId' => $shiftId,
                            'invoiceID' => $item->invoiceID,
                            'bankAccId' => $item->bankID,
                            'logId' => $logs->id,
                            'isReturnYN' => 1,
                            'amount' => round($item->amount * -1,3)
                        );

                    }
                    POSBankGLEntries::insert($bankReturnArray);


                    POSGLEntries::insert($bankGLReturnArray);
                    POSGLEntries::insert($itemGLReturnArray);
                    POSGLEntries::insert($taxGLReturnArray);


                    foreach ($invItemsPL as $gl) {

                        $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                        if ($gl->categoryID == 1) {
                            $costGLReturnArray[] = array(
                                'shiftId' => $shiftId,
                                'invoiceID' => $gl->invoiceID,
                                'documentSystemId' => 110,
                                'documentCode' => $documentCode,
                                'glCode' => $gl->plGLCode,
                                'logId' => $logs['id'],
                                'isReturnYN' => 1,
                                'amount' => round($gl->amount * -1,3)
                            );
                        }
                    }
                    POSGLEntries::insert($costGLReturnArray);
                    foreach ($invItemsBS as $gl) {
                        if ($gl->categoryID == 1) {
                            $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                            if ($gl->glYN == -1) {
                                $inventoryGLReturnArray[] = array(
                                    'shiftId' => $shiftId,
                                    'invoiceID' => $gl->invoiceID,
                                    'documentSystemId' => 110,
                                    'documentCode' => $documentCode,
                                    'glCode' => $gl->plGLCode,
                                    'logId' => $logs['id'],
                                    'isReturnYN' => 1,
                                    'amount' => round($gl->amount,3)
                                );
                            } else {
                                $inventoryGLReturnArray[] = array(
                                    'shiftId' => $shiftId,
                                    'invoiceID' => $gl->invoiceID,
                                    'documentSystemId' => 110,
                                    'documentCode' => $documentCode,
                                    'glCode' => $gl->bsGLCode,
                                    'logId' => $logs['id'],
                                    'isReturnYN' => 1,
                                    'amount' => round($gl->amount,3)
                                );
                            }
                        }
                    }
                    POSGLEntries::insert($inventoryGLReturnArray);
                }
            }


            if ($shiftDetails->posType == 2) {
                $hasItems = POSSourceMenuSalesMaster::where('shiftId', $shiftId)->get();
                if ($hasItems->isEmpty()) {
                    return $this->sendError('Invoices not found');
                }
                $shiftLogArray = [
                    'startTime' => $shiftDetails->startTime,
                    'endTime' => $shiftDetails->endTime,
                    'status' => 1,
                    'postGroupByYN' => 0,
                    'shiftId' => $shiftId
                ];
                $logs = POSFinanceLog::create($shiftLogArray);

                $bankGLArray = array();
                $itemGLArray = array();
                $taxGLArray1 = array();
                $taxGLArray2 = array();
                $itemArray = array();
                $bankArray = array();
                $serviceArray = array();
                $promotionArray = array();
                $costGLArray = array();
                $inventoryGLArray = array();

                if ($isPostGroupBy == 0) {

                    $bankGL = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('SUM(pos_source_menusalespayments.amount) as amount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalespayments.GLCode as glCode, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID')
                        ->join('pos_source_menusalespayments', 'pos_source_menusalespayments.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->groupBy('pos_source_menusalespayments.menuSalesID')
                        ->groupBy('pos_source_menusalespayments.paymentConfigMasterID')
                        ->groupBy('pos_source_menusalespayments.GLCode')
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->where('pos_source_menusalesmaster.isWastage', 0)
                        ->get();

                    $invItems = DB::table('pos_source_menusalesitems')
                        ->selectRaw('pos_source_menusalesmaster.menuSalesID as invoiceID,pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalesitemdetails.itemAutoID as itemID, pos_source_menusalesitems.revenueGLAutoID as glCode,  itemmaster.financeCategoryMaster as categoryID, financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, financeitemcategorysub.includePLForGRVYN as glYN, (pos_source_menusalesitemdetails.qty * pos_source_menusalesitems.qty) as qty, (pos_source_menusalesitemdetails.cost * pos_source_menusalesitems.qty ) as price, pos_source_menusalesitemdetails.UOMID as uom, pos_source_menusalesmaster.wareHouseAutoID as wareHouseID')
                        ->join('pos_source_menusalesmaster', 'pos_source_menusalesmaster.menuSalesID', '=', 'pos_source_menusalesitems.menuSalesID')
                        ->join('pos_source_menusalesitemdetails', 'pos_source_menusalesitemdetails.menuSalesItemID', '=', 'pos_source_menusalesitems.menuSalesItemID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_menusalesitemdetails.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->get();

                    // revenue gl code
                    $revItems = DB::table('pos_source_menusalesitems')
                        ->selectRaw('pos_source_menusalesitems.*, pos_source_menusalesmaster.discountAmount as discount, pos_source_menusalesmaster.promotionDiscountAmount as promotionAmount, pos_source_menusalesmaster.promotionGLCode as promotionGLCode, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.menuSalesID as invoiceID')
                        ->join('pos_source_menusalesmaster', 'pos_source_menusalesmaster.menuSalesID', '=', 'pos_source_menusalesitems.menuSalesID')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->where('pos_source_menusalesmaster.isWastage', 0)
                        ->groupBy('pos_source_menusalesitems.menuSalesID')
                        ->groupBy('pos_source_menusalesitems.menuID')
                        ->get();





                    $invItemsBS = DB::table('pos_source_menusalesitems')
                        ->selectRaw('SUM(pos_source_menusalesitemdetails.cost * pos_source_menusalesitems.qty) as amount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalesitemdetails.itemAutoID as itemID, pos_source_menusalesitems.revenueGLAutoID as glCode,  itemmaster.financeCategoryMaster as categoryID, financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, financeitemcategorysub.includePLForGRVYN as glYN, (pos_source_menusalesitemdetails.qty * pos_source_menusalesitems.qty) as qty, (pos_source_menusalesitemdetails.cost * pos_source_menusalesitems.qty) as price, pos_source_menusalesitemdetails.UOMID as uom, pos_source_menusalesmaster.wareHouseAutoID as wareHouseID')
                        ->join('pos_source_menusalesmaster', 'pos_source_menusalesmaster.menuSalesID', '=', 'pos_source_menusalesitems.menuSalesID')
                        ->join('pos_source_menusalesitemdetails', 'pos_source_menusalesitemdetails.menuSalesItemID', '=', 'pos_source_menusalesitems.menuSalesItemID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_menusalesitemdetails.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->join('itemassigned', 'itemassigned.itemCodeSystem', '=', 'itemmaster.itemCodeSystem')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->where('itemassigned.companySystemID', $shiftDetails->companyID)
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->groupBy('financeitemcategorysub.financeGLcodebBSSystemID')
                        ->groupBy('pos_source_menusalesitemdetails.menuSalesID')
                        ->get();

                    $invItemsPL = DB::table('pos_source_menusalesitems')
                        ->selectRaw('SUM(pos_source_menusalesitemdetails.cost * pos_source_menusalesitems.qty) as amount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalesitemdetails.itemAutoID as itemID, pos_source_menusalesitems.revenueGLAutoID as glCode,  itemmaster.financeCategoryMaster as categoryID, financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeCogsGLcodePLSystemID as plGLCode, financeitemcategorysub.includePLForGRVYN as glYN, (pos_source_menusalesitemdetails.qty * pos_source_menusalesitems.qty) as qty, (pos_source_menusalesitemdetails.cost * pos_source_menusalesitems.qty) as price, pos_source_menusalesitemdetails.UOMID as uom, pos_source_menusalesmaster.wareHouseAutoID as wareHouseID')
                        ->join('pos_source_menusalesmaster', 'pos_source_menusalesmaster.menuSalesID', '=', 'pos_source_menusalesitems.menuSalesID')
                        ->join('pos_source_menusalesitemdetails', 'pos_source_menusalesitemdetails.menuSalesItemID', '=', 'pos_source_menusalesitems.menuSalesItemID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_menusalesitemdetails.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->join('itemassigned', 'itemassigned.itemCodeSystem', '=', 'itemmaster.itemCodeSystem')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->where('itemassigned.companySystemID', $shiftDetails->companyID)
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->groupBy('financeitemcategorysub.financeCogsGLcodePLSystemID')
                        ->groupBy('pos_source_menusalesitemdetails.menuSalesID')
                        ->get();


                    $taxItems = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalesoutlettaxes.taxAmount as taxAmount, erp_taxmaster_new.outputVatGLAccountAutoID as outputVatGLCode')
                        ->join('pos_source_menusalesoutlettaxes', 'pos_source_menusalesoutlettaxes.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                        ->join('pos_source_taxmaster', 'pos_source_taxmaster.taxMasterAutoID', '=', 'pos_source_menusalesoutlettaxes.taxMasterID')
                        ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_taxmaster.erp_tax_master_id')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->where('pos_source_menusalesmaster.isWastage', 0)
                        ->get();

                    $taxItems2 = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalestaxes.taxAmount as taxAmount, erp_taxmaster_new.outputVatGLAccountAutoID as outputVatGLCode')
                        ->join('pos_source_menusalestaxes', 'pos_source_menusalestaxes.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                        ->join('pos_source_taxmaster', 'pos_source_taxmaster.taxMasterAutoID', '=', 'pos_source_menusalestaxes.taxMasterID')
                        ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_taxmaster.erp_tax_master_id')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->where('pos_source_menusalesmaster.isWastage', 0)
                        ->get();

                    $bankItems = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('SUM(pos_source_menusalesmaster.cashReceivedAmount) as amount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_paymentglconfigdetail.erp_bank_acc_id as bankID, pos_source_menusalespayments.GLCode as glCode, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalespayments.paymentConfigDetailID as payDetailID')
                        ->join('pos_source_menusalespayments', 'pos_source_menusalespayments.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                        ->join('pos_source_paymentglconfigdetail', 'pos_source_paymentglconfigdetail.ID', '=', 'pos_source_menusalespayments.paymentConfigDetailID')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->groupBy('pos_source_menusalesmaster.menuSalesID')
                        ->groupBy('pos_source_menusalesmaster.shiftID')
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->where('pos_source_menusalesmaster.isWastage', 0)
                        ->get();

                    $serviceItems = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, SUM(pos_source_menusalesservicecharge.serviceChargeAmount) as serviceChargeAmount, pos_source_menusalesservicecharge.GLAutoID as glCode')
                        ->join('pos_source_menusalesservicecharge', 'pos_source_menusalesservicecharge.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->where('pos_source_menusalesmaster.isWastage', 0)
                        ->groupBy('pos_source_menusalesservicecharge.GLAutoID')
                        ->groupBy('pos_source_menusalesmaster.menuSalesID')
                        ->get();

                    $promotionItems = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalesmaster.promotionDiscountAmount as promotionAmount, pos_source_menusalesmaster.promotionGLCode as glCode')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->where('pos_source_menusalesmaster.isWastage', 0)
                        ->whereNotNull('pos_source_menusalesmaster.promotionGLCode')
                        ->get();


                }
                if ($isPostGroupBy == 1) {
                    $bankGL = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('SUM(pos_source_menusalesmaster.cashReceivedAmount) as amount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalespayments.GLCode as glCode, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID')
                        ->join('pos_source_menusalespayments', 'pos_source_menusalespayments.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->groupBy('pos_source_menusalesmaster.menuSalesID')
                        ->groupBy('pos_source_menusalesmaster.shiftID')
                        ->groupBy('pos_source_invoicepayments.GLCode')
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->get();


                    $invItems = DB::table('pos_source_menusalesitems')
                        ->selectRaw('SUM((pos_source_menusalesitems.menuSalesPrice - (pos_source_menusalesmaster.discountAmount / pos_source_menusalesmaster.grossAmount) * pos_source_menusalesitems.menuSalesPrice) * pos_source_menusalesitems.qty) as amount, pos_source_menusalesmaster.discountAmount as discount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalesitemdetails.itemAutoID as itemID, pos_source_menusalesitems.revenueGLAutoID as glCode,  itemmaster.financeCategoryMaster as categoryID, financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, financeitemcategorysub.includePLForGRVYN as glYN, (pos_source_menusalesitemdetails.qty * pos_source_menusalesitems.qty) as qty, (pos_source_menusalesitemdetails.cost * pos_source_menusalesitems.qty) as price, pos_source_menusalesitemdetails.UOMID as uom,pos_source_menusalesmaster.wareHouseAutoID as wareHouseID')
                        ->join('pos_source_menusalesmaster', 'pos_source_menusalesmaster.menuSalesID', '=', 'pos_source_menusalesitems.menuSalesID')
                        ->join('pos_source_menusalesitemdetails', 'pos_source_menusalesitemdetails.menuSalesItemID', '=', 'pos_source_menusalesitems.menuSalesItemID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_menusalesitemdetails.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->groupBy('pos_source_menusalesmaster.shiftID')
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->get();


                    $invItemsPLBS = DB::table('pos_source_menusalesitems')
                        ->selectRaw('SUM(pos_source_menusalesitemdetails.qty * pos_source_menusalesitemdetails.cost * pos_source_menusalesitems.qty) as amount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalesitemdetails.itemAutoID as itemID, pos_source_menusalesitems.revenueGLAutoID as glCode,  itemmaster.financeCategoryMaster as categoryID, financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, financeitemcategorysub.includePLForGRVYN as glYN, (pos_source_menusalesitemdetails.qty * pos_source_menusalesitems.qty) as qty, (pos_source_menusalesitemdetails.cost * pos_source_menusalesitems.qty) as price, pos_source_menusalesitemdetails.UOMID as uom, pos_source_menusalesmaster.wareHouseAutoID as wareHouseID')
                        ->join('pos_source_menusalesmaster', 'pos_source_menusalesmaster.menuSalesID', '=', 'pos_source_menusalesitems.menuSalesID')
                        ->join('pos_source_menusalesitemdetails', 'pos_source_menusalesitemdetails.menuSalesItemID', '=', 'pos_source_menusalesitems.menuSalesItemID')
                        ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_menusalesitemdetails.itemAutoID')
                        ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                        ->join('itemassigned', 'itemassigned.itemCodeSystem', '=', 'itemmaster.itemCodeSystem')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->where('itemassigned.companySystemID', $shiftDetails->companyID)
                        ->groupBy('pos_source_menusalesmaster.shiftID')
                        ->groupBy('financeitemcategorysub.financeGLcodePLSystemID')
                        ->groupBy('financeitemcategorysub.financeGLcodebBSSystemID')
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->get();


                    $taxItems = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, SUM(pos_source_menusalesoutlettaxes.taxAmount) as taxAmount, erp_taxmaster_new.outputVatGLAccountAutoID as outputVatGLCode')
                        ->join('pos_source_menusalesoutlettaxes', 'pos_source_menusalesoutlettaxes.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                        ->join('pos_source_taxmaster', 'pos_source_taxmaster.taxMasterAutoID', '=', 'pos_source_menusalesoutlettaxes.taxMasterID')
                        ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_taxmaster.erp_tax_master_id')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->groupBy('pos_source_menusalesmaster.shiftID')
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->get();

                    $taxItems2 = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, SUM(pos_source_menusalestaxes.taxAmount) as taxAmount, erp_taxmaster_new.outputVatGLAccountAutoID as outputVatGLCode')
                        ->join('pos_source_menusalestaxes', 'pos_source_menusalestaxes.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                        ->join('pos_source_taxmaster', 'pos_source_taxmaster.taxMasterAutoID', '=', 'pos_source_menusalestaxes.taxMasterID')
                        ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_taxmaster.erp_tax_master_id')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->groupBy('pos_source_menusalesmaster.shiftID')
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->get();

                    $serviceItems = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, SUM(pos_source_menusalesservicecharge.serviceChargeAmount) as serviceChargeAmount, pos_source_menusalesservicecharge.GLAutoID as glCode')
                        ->join('pos_source_menusalesservicecharge', 'pos_source_menusalesservicecharge.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->groupBy('pos_source_menusalesservicecharge.GLAutoID')
                        ->groupBy('pos_source_menusalesmaster.shiftID')
                        ->groupBy('pos_source_menusalesmaster.menuSalesID')
                        ->get();

                    $bankItems = DB::table('pos_source_menusalesmaster')
                        ->selectRaw('SUM(pos_source_menusalesmaster.cashReceivedAmount) as amount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_paymentglconfigdetail.erp_bank_acc_id as bankID, pos_source_menusalespayments.GLCode as glCode, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalespayments.paymentConfigDetailID as payDetailID')
                        ->join('pos_source_menusalespayments', 'pos_source_menusalespayments.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                        ->join('pos_source_paymentglconfigdetail', 'pos_source_paymentglconfigdetail.ID', '=', 'pos_source_menusalespayments.paymentConfigDetailID')
                        ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                        ->groupBy('pos_source_menusalesmaster.menuSalesID')
                        ->groupBy('pos_source_menusalesmaster.shiftID')
                        ->where('pos_source_menusalesmaster.isCreditSales', 0)
                        ->get();
                }

                foreach ($serviceItems as $gl) {
                    $documentCode = ('RPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $serviceArray[] = array(
                        'shiftId' => $gl->shiftId,
                        'invoiceID' => $gl->invoiceID,
                        'documentSystemId' => 111,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->glCode,
                        'logId' => $logs['id'],
                        'amount' => round($gl->serviceChargeAmount * -1,3)
                    );
                }

                foreach ($promotionItems as $gl) {
                    $documentCode = ('RPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $promotionArray[] = array(
                        'shiftId' => $gl->shiftId,
                        'invoiceID' => $gl->invoiceID,
                        'documentSystemId' => 111,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->glCode,
                        'logId' => $logs['id'],
                        'amount' => round($gl->promotionAmount,3)
                    );
                }

                foreach ($taxItems as $gl) {

                    $documentCode = ('RPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $taxGLArray1[] = array(
                        'shiftId' => $gl->shiftId,
                        'invoiceID' => $gl->invoiceID,
                        'documentSystemId' => 111,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->outputVatGLCode,
                        'logId' => $logs['id'],
                        'amount' => round($gl->taxAmount * -1,3)
                    );

                }
                POSTaxGLEntries::insert($taxGLArray1);

                foreach ($taxItems2 as $gl) {

                    $documentCode = ('RPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $taxGLArray2[] = array(
                        'shiftId' => $gl->shiftId,
                        'invoiceID' => $gl->invoiceID,
                        'documentSystemId' => 111,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->outputVatGLCode,
                        'logId' => $logs['id'],
                        'amount' => round($gl->taxAmount * -1,3)
                    );

                }

                POSTaxGLEntries::insert($taxGLArray2);

                foreach ($bankGL as $gl) {

                    $documentCode = ('RPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $bankGLArray[] = array(
                        'shiftId' => $gl->shiftId,
                        'invoiceID' => $gl->invoiceID,
                        'documentSystemId' => 111,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->glCode,
                        'logId' => $logs['id'],
                        'amount' => round($gl->amount,3)
                    );

                }

                foreach ($revItems as $gl) {


                    $sumMenuSales = DB::table('pos_source_menusalesitems')
                        ->where('pos_source_menusalesitems.menuSalesID', $gl->invoiceID)
                        ->sum(DB::raw('pos_source_menusalesitems.menuSalesPrice * pos_source_menusalesitems.qty'));

                    if ($sumMenuSales != 0) {
                        if ($gl->promotionGLCode != null) {
                            $amount = (($gl->menuSalesPrice * $gl->qty) - $gl->discountAmount ) - (($gl->discount / $sumMenuSales) * ($gl->menuSalesPrice * $gl->qty));
                        } else {
                            $amount = (($gl->menuSalesPrice * $gl->qty) - $gl->discountAmount) - (($gl->discount / $sumMenuSales) * ($gl->menuSalesPrice * $gl->qty));
                            $amount = $amount - (($gl->promotionAmount / $sumMenuSales) * ($gl->menuSalesPrice * $gl->qty));
                        }


                        $documentCode = ('RPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                        $itemGLArray[] = array(
                            'shiftId' => $gl->shiftId,
                            'invoiceID' => $gl->invoiceID,
                            'documentSystemId' => 111,
                            'documentCode' => $documentCode,
                            'glCode' => $gl->revenueGLAutoID,
                            'logId' => $logs['id'],
                            'amount' => round($amount * -1,3)
                        );
                    }

                }


                foreach ($invItems as $item) {
                    $itemArray[] = array(
                        'shiftId' => $item->shiftId,
                        'invoiceID' => $item->invoiceID,
                        'itemAutoId' => $item->itemID,
                        'uom' => $item->uom,
                        'qty' => $item->qty,
                        'isReturnYN' => 0,
                    );

                }

                POSItemGLEntries::insert($itemArray);


                foreach ($bankItems as $item) {
                    $bankArray[] = array(
                        'shiftId' => $item->shiftId,
                        'invoiceID' => $item->invoiceID,
                        'bankAccId' => $item->bankID,
                        'logId' => $logs->id,
                        'isReturnYN' => 0,
                        'amount' => round($item->amount,3)
                    );

                }
                POSBankGLEntries::insert($bankArray);

                POSGLEntries::insert($bankGLArray);
                POSGLEntries::insert($itemGLArray);
                POSGLEntries::insert($taxGLArray1);
                POSGLEntries::insert($taxGLArray2);
                POSGLEntries::insert($serviceArray);
                POSGLEntries::insert($promotionArray);


                foreach ($invItemsPL as $gl) {
                    $documentCode = ('RPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    if ($gl->categoryID == 1) {
                        $costGLArray[] = array(
                            'shiftId' => $gl->shiftId,
                            'invoiceID' => $gl->invoiceID,
                            'documentSystemId' => 111,
                            'documentCode' => $documentCode,
                            'glCode' => $gl->plGLCode,
                            'logId' => $logs['id'],
                            'amount' => round($gl->amount,3)
                        );
                    }
                }
                POSGLEntries::insert($costGLArray);

                foreach ($invItemsBS as $gl) {
                    if ($gl->categoryID == 1) {
                        $documentCode = ('RPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                        if ($gl->glYN == -1) {
                            $inventoryGLArray[] = array(
                                'shiftId' => $gl->shiftId,
                                'invoiceID' => $gl->invoiceID,
                                'documentSystemId' => 111,
                                'documentCode' => $documentCode,
                                'glCode' => $gl->plGLCode,
                                'logId' => $logs['id'],
                                'amount' => round($gl->amount * -1,3)
                            );
                        } else {
                            $inventoryGLArray[] = array(
                                'shiftId' => $gl->shiftId,
                                'invoiceID' => $gl->invoiceID,
                                'documentSystemId' => 111,
                                'documentCode' => $documentCode,
                                'glCode' => $gl->bsGLCode,
                                'logId' => $logs['id'],
                                'amount' => round($gl->amount * -1,3)
                            );
                        }
                    }

                }
                POSGLEntries::insert($inventoryGLArray);

            }
        }

        return $this->sendResponse([$logs], "Invoice Posting successfull");


    }


    public function createCustomerInvoice($input, $input2){


    }

    public function updateVatEligibilityOfCustomerInvoice($custInvoiceDirectAutoID)
    {
        $doDetailData = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
            ->groupBy('quotationMasterID')
            ->get();

        $quMasterIds = $doDetailData->pluck('quotationMasterID');

        $quotaionVatEligibleCheck = QuotationMaster::whereIn('quotationMasterID', $quMasterIds)
            ->where('vatRegisteredYN', 1)
            ->where('customerVATEligible', 1)
            ->first();
        $vatRegisteredYN = 0;
        $customerVATEligible = 0;
        if ($quotaionVatEligibleCheck) {
            $customerVATEligible = 1;
            $vatRegisteredYN = 1;
        }

        $updateRes = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
            ->update(['vatRegisteredYN' => $vatRegisteredYN, 'customerVATEligible' => $customerVATEligible]);

        return ['status' => true];
    }

    public function updateVatFromSalesQuotation($custInvoiceDirectAutoID)
    {
        $invoiceDetails = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
            ->with(['sales_quotation_detail'])
            ->get();

        $totalVATAmount = 0;
        $invoice = CustomerInvoiceDirect::find($custInvoiceDirectAutoID);

        foreach ($invoiceDetails as $key => $value) {
                $totalVATAmount += $value->qtyIssued * $value->VATAmount;
        }

        $taxDelete = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('documentSystemID', 20)
            ->delete();
        if ($totalVATAmount > 0) {
            $res = $this->savecustomerInvoiceTaxDetails($custInvoiceDirectAutoID, $totalVATAmount);

            if (!$res['status']) {
                return ['status' => false, 'message' => $res['message']];
            }
        } else {
            $vatAmount['vatOutputGLCodeSystemID'] = null;
            $vatAmount['vatOutputGLCode'] = null;
            $vatAmount['VATPercentage'] = 0;
            $vatAmount['VATAmount'] = 0;
            $vatAmount['VATAmountLocal'] = 0;
            $vatAmount['VATAmountRpt'] = 0;

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);
        }


        return ['status' => true];
    }

    public function updateVatFromSalesDeliveryOrder($custInvoiceDirectAutoID)
    {
        $invoiceDetails = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
            ->with(['delivery_order_detail'])
            ->get();

        $totalVATAmount = 0;
        foreach ($invoiceDetails as $key => $value) {
            $totalVATAmount += $value->qtyIssued * (isset($value->delivery_order_detail->VATAmount) ? $value->delivery_order_detail->VATAmount : 0);
        }

        $taxDelete = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('documentSystemID', 20)
            ->delete();
        if ($totalVATAmount > 0) {
            $res = $this->savecustomerInvoiceTaxDetails($custInvoiceDirectAutoID, $totalVATAmount);

            if (!$res['status']) {
                return ['status' => false, 'message' => $res['message']];
            }
        } else {
            $vatAmount['vatOutputGLCodeSystemID'] = null;
            $vatAmount['vatOutputGLCode'] = null;
            $vatAmount['VATPercentage'] = 0;
            $vatAmount['VATAmount'] = 0;
            $vatAmount['VATAmountLocal'] = 0;
            $vatAmount['VATAmountRpt'] = 0;

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);
        }

        return ['status' => true];
    }

    private function updateCostBySellingCost($input,$customerDirectInvoice){
        $output = array();
        if($customerDirectInvoice->custTransactionCurrencyID != $customerDirectInvoice->localCurrencyID){
            $currencyConversion = Helper::currencyConversion($customerDirectInvoice->companySystemID,$customerDirectInvoice->custTransactionCurrencyID,$customerDirectInvoice->localCurrencyID,$input['sellingCostAfterMargin']);
            if(!empty($currencyConversion)){
                $output['sellingCostAfterMarginLocal'] = $currencyConversion['documentAmount'];
            }
        }else{
            $output['sellingCostAfterMarginLocal'] = $input['sellingCostAfterMargin'];
        }

        if($customerDirectInvoice->custTransactionCurrencyID != $customerDirectInvoice->companyReportingCurrencyID){
            $currencyConversion = Helper::currencyConversion($customerDirectInvoice->companySystemID,$customerDirectInvoice->custTransactionCurrencyID,$customerDirectInvoice->companyReportingCurrencyID,$input['sellingCostAfterMargin']);
            if(!empty($currencyConversion)){
                $output['sellingCostAfterMarginRpt'] = $currencyConversion['documentAmount'];
            }
        }else{
            $output['sellingCostAfterMarginRpt'] = $input['sellingCostAfterMargin'];
        }

        return $output;
    }

    public function getPosShiftDetails(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();
        $validator = \Validator::make($input, [
            'companyId' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $company = Company::with(['localcurrency','reportingcurrency'])->find($input['companyId']);

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $currencyDenomination = CurrencyDenomination::where('currencyID',$company->localCurrencyID)
                                                      ->orderBy('amount','desc')
                                                      ->get();

        $assignedOutlet = OutletUsers::where('userID',$employee->employeeSystemID)
                                       ->where('companySystemID',$input['companyId'])
                                       ->where('isActive',1)
                                        ->whereHas('outlet',function ($q){
                                            $q->where('isActive',1);
                                        })
                                       ->first();

        if(empty($assignedOutlet)){
            return $this->sendError('You are not assigned to an outlet. Please assign and try again.');
        }

        $counters = Counter::where('companySystemID',$input['companyId'])
                             ->where('wareHouseID',$assignedOutlet->wareHouseID)
                             ->get();

        if(count($counters) == 0){
            return $this->sendError('Counter not created. Please create a counter.');
        }

        $isShiftOpen = false;

        $shift = ShiftDetails::where('isClosed',0)
                             ->where('empID',$employee->employeeSystemID)
                             ->where('wareHouseID',$assignedOutlet->wareHouseID)
                             ->with(['user','outlet','counter'])
                             ->first();

        if(!empty($shift)){
            $isShiftOpen = true;
        }
        $decimalPlaces = 2;
        if($company->localcurrency){
            $decimalPlaces = $company->localcurrency->DecimalPlaces;
        }

        $payments = GposPaymentGlConfigDetail::where('warehouseID',$assignedOutlet->wareHouseID)
                                             ->with(['type'])
                                             ->get();

        $output = array(
            'company' => $company,
            'currencyDenomination' => $currencyDenomination,
            'outlet' => $assignedOutlet,
            'counters' => $counters,
            'shift' => $shift,
            'isShiftOpen' => $isShiftOpen,
            'decimalPlaces' => $decimalPlaces,
            'payments' => $payments
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getPosMismatchEntries(Request $request)
    {
        $input = $request->all();
        $data = [];

        $shiftDetails = POSSOURCEShiftDetails::where('shiftID',$input['shiftId'])->first();
        $output = DB::table('pos_gl_entries')
            ->selectRaw('sum(amount) as Amount,COUNT(DISTINCT pos_gl_entries.invoiceID) as count, pos_source_shiftdetails.transactionCurrencyDecimalPlaces')
            ->leftjoin('pos_source_shiftdetails', 'pos_source_shiftdetails.shiftID', '=', 'pos_gl_entries.shiftID')
            ->having('count', '>', 0)
            ->where('pos_gl_entries.shiftId', $input['shiftId']);


        if($shiftDetails->posType == 1) {

            $data['invoiceEntries'] = DB::table('pos_gl_entries')
                ->selectRaw('CASE WHEN abs(ROUND(sum(amount), 3)) < 0.001 THEN 0 ELSE ROUND(sum(amount), 3) END as Amount, COUNT(pos_gl_entries.shiftId) as count,pos_source_invoice.invoiceCode,pos_gl_entries.invoiceID,pos_gl_entries.shiftid, pos_source_invoice.transactionCurrencyDecimalPlaces')
                ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_gl_entries.invoiceID')
                ->where('pos_gl_entries.shiftId', $input['shiftId'])
                ->groupBy('invoiceID')
                ->get();
        }
        else if ($shiftDetails->posType == 2) {

            $data['invoiceEntries'] = DB::table('pos_gl_entries')
                ->selectRaw('CASE WHEN abs(ROUND(sum(amount), 3)) < 0.001 THEN 0 ELSE ROUND(sum(amount), 3) END as Amount, COUNT(pos_gl_entries.shiftId) as count,pos_source_menusalesmaster.invoiceCode,pos_gl_entries.invoiceID,pos_gl_entries.shiftid,pos_source_menusalesmaster.transactionCurrencyDecimalPlaces')
                ->join('pos_source_menusalesmaster', 'pos_source_menusalesmaster.menuSalesID', '=', 'pos_gl_entries.invoiceID')
                ->where('pos_gl_entries.shiftId', $input['shiftId'])
                ->groupBy('invoiceID')
                ->get();
        }
        $data['shifEntries'] = $output->get();

        $outputMisMatch = $output->first();

        if(!empty($outputMisMatch)) {

            if(abs($outputMisMatch->Amount) < 0.001){
                $data['isMismatch'] = true;
            }
            else {
                $data['isMismatch'] = false;
            }

        }
        else {
            $data['isMismatch'] = true;
        }


        return $this->sendResponse($data, 'Record retrieved successfully');

    }

    public function getPosMisMatchData(Request $request)
    {   
        $input = $request->all();
        $data = [];

        $data['data'] = DB::table('pos_gl_entries')
        ->selectRaw('chartofaccounts.AccountDescription,pos_gl_entries.amount, pos_source_shiftdetails.transactionCurrencyDecimalPlaces')
        ->join('chartofaccounts', 'chartofaccounts.chartOfAccountSystemID', '=', 'pos_gl_entries.glCode')
        ->leftjoin('pos_source_shiftdetails', 'pos_source_shiftdetails.shiftID', '=', 'pos_gl_entries.shiftID')
        ->where('chartofaccounts.primaryCompanySystemID',$input['companyId'])
        ->where('pos_gl_entries.shiftId',$input['shiftId'])
        ->where('invoiceID',$input['invoiceId'])
        ->get();

        $data['glCodes'] = ChartOfAccountsAssigned::where('companySystemID', $input['companyId'])->where('isActive', 1)->where('isAssigned', -1)->select(DB::raw("chartOfAccountSystemID,CONCAT(AccountCode, ' | ' ,AccountDescription) as name"))
        ->get();

     
        return $this->sendResponse($data, 'Record retrieved successfully');

    }

    public function updatePosMismatch(Request $request)
    {
        $input = $request->all();

        $sum = DB::table('pos_gl_entries')
        ->selectRaw('sum(amount) as Amount,documentSystemId,documentCode,logId')
        ->where('shiftid',$input['shiftid'])
        ->where('invoiceID',$input['invoiceId'])
        ->first();
        if($sum->Amount < 0)
        {
            $amount = abs($sum->Amount);
        }
        else
        {
            $amount = -abs($sum->Amount);
        }

        $itemGLArraySR[] = array(
            'shiftId' => $input['shiftid'],
            'documentSystemId' => $sum->documentSystemId,
            'documentCode' => $sum->documentCode,
            'invoiceID' => $input['invoiceId'],
            'glCode' => $input['code'],
            'logId' => $sum->logId,
            'amount' => $amount,
        );



        POSGLEntries::insert($itemGLArraySR);
        return $this->sendResponse($sum, 'Record retrieved successfully');

    }

    public function getGlMatchEntries(Request $request)
    {
        $input = $request->all();

        $shiftDetails = POSSOURCEShiftDetails::where('shiftID',$input['shiftId'])->first();

        if($shiftDetails->posType == 1) {
            $data = DB::table('pos_source_invoice')
                ->selectRaw('sum(netTotal) as Amount, count(*) as count, transactionCurrencyDecimalPlaces')
                ->where('shiftID', $input['shiftId'])
                ->get();
        }

        else if($shiftDetails->posType == 2) {
            $data = DB::table('pos_source_menusalesmaster')
                ->selectRaw('(sum(grossTotal)) as Amount,count(*) as count, transactionCurrencyDecimalPlaces')
                ->where('shiftID', $input['shiftId'])
                ->get();
        }
        return $this->sendResponse($data, 'Record retrieved successfully');

    }
}
