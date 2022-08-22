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
use App\Models\OutletUsers;
use App\Models\POSBankGLEntries;
use App\Models\POSFinanceLog;
use App\Models\POSGLEntries;
use App\Models\POSInvoiceSource;
use App\Models\POSItemGLEntries;
use App\Models\POSSOURCECustomerMaster;
use App\Models\POSSourcePaymentGlConfig;
use App\Models\POSSOURCEPaymentGlConfigDetail;
use App\Models\POSSOURCEShiftDetails;
use App\Models\POSSOURCETaxMaster;
use App\Models\POSTaxGLEntries;
use App\Models\PurchaseReturn;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
use App\Models\SegmentMaster;
use App\Models\ShiftDetails;
use App\Models\StockTransfer;
use App\Models\Taxdetail;
use App\Models\TaxVatCategories;
use App\Models\VatSubCategoryType;
use App\Models\WarehouseMaster;
use App\Repositories\CustomerInvoiceDirectRepository;
use App\Repositories\CustomerInvoiceItemDetailsRepository;
use App\Repositories\ShiftDetailsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
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


    public function __construct(ShiftDetailsRepository $shiftDetailsRepo, CustomerInvoiceDirectRepository $customerInvoiceDirectRepo, CustomerInvoiceItemDetailsRepository $customerInvoiceItemDetailsRepo)
    {
        $this->shiftDetailsRepository = $shiftDetailsRepo;
        $this->customerInvoiceDirectRepository = $customerInvoiceDirectRepo;
        $this->customerInvoiceItemDetailsRepository = $customerInvoiceItemDetailsRepo;


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
        $postedShifts = POSFinanceLog::groupBy('shiftId')->get();
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
        $shiftDetailLabels = POSSOURCEShiftDetails::selectRaw('startTime ,endTime, createdUserName')->where('shiftID', $shiftID)->first();

        $posCustomers = DB::table('pos_source_invoice')
            ->selectRaw('customerSystemCode, customerName, customerAutoID')
            ->join('pos_source_customermaster', 'pos_source_customermaster.customerAutoID', '=', 'pos_source_invoice.customerID')
            ->where('pos_source_invoice.shiftID', $shiftID)
            ->where('pos_source_invoice.isCreditSales', 1)
            ->where('pos_source_customermaster.erp_customer_master_id', 0)
            ->where('pos_source_customermaster.companyID', $companySystemID)
            ->get();

        $customers = CustomerAssigned::selectRaw('customerCodeSystem as value,CONCAT(CutomerCode, " | " ,CustomerName) as label')->where('companySystemID', $companySystemID)->get();


        $posTaxes = DB::table('pos_source_taxmaster')
            ->selectRaw('taxMasterAutoID, taxDescription, taxShortCode')
            ->where('pos_source_taxmaster.taxType', 1)
            ->where('pos_source_taxmaster.erp_tax_master_id', 0)
            ->where('pos_source_taxmaster.companyID', $companySystemID)
            ->get();

        $taxes = TaxVatCategories::selectRaw('taxVatSubCategoriesAutoID as value, subCategoryDescription as label')
            ->join('erp_taxmaster', 'erp_taxmaster.taxMasterAutoID', '=', 'erp_tax_vat_sub_categories.taxMasterAutoID')
            ->where('erp_taxmaster.companySystemID', $companySystemID)
            ->get();

        $posPayments = DB::table('pos_source_paymentglconfigdetail')
            ->selectRaw('ID, description, GLCode')
            ->join('pos_source_paymentglconfigmaster', 'pos_source_paymentglconfigmaster.autoID', '=', 'pos_source_paymentglconfigdetail.paymentConfigMasterID')
            ->where('pos_source_paymentglconfigdetail.companyID', $companySystemID)
            ->where('pos_source_paymentglconfigdetail.erp_bank_acc_id', 0)
            ->get();

        foreach ($posPayments as $pt){
            $pt->dropOptions = BankAccount::selectRaw('bankAccountAutoID as value, CONCAT(bankShortCode, " | " ,AccountNo) as label')->where('companySystemID', $companySystemID)->where('chartOfAccountSystemID', $pt->GLCode)
                ->get();
        }


        $output = array(
            'shiftDetailLabels' => $shiftDetailLabels,
            'posCustomers' => $posCustomers,
            'customers' => $customers,
            'posTaxes' => $posTaxes,
            'taxes' => $taxes,
            'posPayments' => $posPayments,
        );

        return $this->sendResponse($output, "Shift Details retrieved successfully");
    }

    public function postPosCustomerMapping(Request $request) {

        $cusPOSId = $request->cusPOSId;
        $cusERPId = $request->cusERPId;
        $output = POSSOURCECustomerMaster::where('customerAutoID', $cusPOSId)->update(['erp_customer_master_id' => $cusERPId]);

        return $this->sendResponse($output, "Shift Details retrieved successfully");
    }

    public function postPosTaxMapping(Request $request) {

        $taxPOSId = $request->taxPOSId;
        $taxERPId = $request->taxERPId;
        $output = POSSOURCETaxMaster::where('taxMasterAutoID', $taxPOSId)->update(['erp_tax_master_id' => $taxERPId]);

        return $this->sendResponse($output, "Shift Details retrieved successfully");
    }

    public function postPosPayMapping(Request $request) {

        $payPOSId = $request->payPOSId;
        $payERPId = $request->payERPId;
        $output = POSSOURCEPaymentGlConfigDetail::where('ID', $payPOSId)->update(['erp_bank_acc_id' => $payERPId]);

        return $this->sendResponse($output, "Shift Details retrieved successfully");
    }

    public function postPosEntries(Request $request){

        $shiftId = $request->shiftId;
        $isPostGroupBy = $request->isPostGroupBy;

        $isInsufficient = 0;

        $shiftDetails = POSSOURCEShiftDetails::where('shiftID',$shiftId)->first();

        if($shiftDetails->posType == 1){
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

            if($isPostGroupBy == 0) {

                $bankGL = DB::table('pos_source_invoice')
                    ->selectRaw('SUM(pos_source_invoice.netTotal) as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoicepayments.GLCode as glCode, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID')
                    ->join('pos_source_invoicepayments', 'pos_source_invoicepayments.invoiceID', '=', 'pos_source_invoice.invoiceID')
                    ->where('pos_source_invoice.shiftID', $shiftId)
                    ->groupBy('pos_source_invoice.shiftID')
                    ->groupBy('pos_source_invoice.invoiceID')
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


                $taxItems = DB::table('pos_source_invoicedetail')
                    ->selectRaw('pos_source_taxledger.amount as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub, financeitemcategorysub.financeGLcodeRevenueSystemID as glCode, pos_source_taxledger.amount as taxAmount, pos_source_taxledger.taxMasterID as taxMasterID, erp_taxmaster_new.outputVatGLAccountAutoID as outputVatGLCode')
                    ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                    ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                    ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                    ->join('pos_source_taxledger', 'pos_source_taxledger.documentDetailAutoID', '=', 'pos_source_invoicedetail.invoiceDetailsID')
                    ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_taxledger.taxMasterID')
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
            if($isPostGroupBy == 1) {

                $bankGL = DB::table('pos_source_invoice')
                    ->selectRaw('SUM(pos_source_invoice.netTotal) as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoicepayments.GLCode as glCode, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID')
                    ->join('pos_source_invoicepayments', 'pos_source_invoicepayments.invoiceID', '=', 'pos_source_invoice.invoiceID')
                    ->where('pos_source_invoice.shiftID', $shiftId)
                    ->groupBy('pos_source_invoice.shiftID')
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

            }


                foreach ($bankGL as $gl) {

                    $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $bankGLArray[] = array(
                        'shiftId' => $gl->shiftId,
                        'documentSystemId' => 110,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->glCode,
                        'logId' => $logs['id'],
                        'amount' => $gl->amount
                    );

                }

                foreach ($invItems as $gl) {

                    $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $itemGLArray[] = array(
                        'shiftId' => $gl->shiftId,
                        'documentSystemId' => 110,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->glCode,
                        'logId' => $logs['id'],
                        'amount' => $gl->amount * -1
                    );

                }

                foreach ($taxItems as $gl) {

                    $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $taxGLArray[] = array(
                        'shiftId' => $gl->shiftId,
                        'documentSystemId' => 110,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->outputVatGLCode,
                        'logId' => $logs['id'],
                        'amount' => $gl->taxAmount * -1
                    );

                }

                POSTaxGLEntries::insert($taxGLArray);

                foreach ($invItems as $item) {
                    $itemArray[] = array(
                        'shiftId' => $item->shiftId,
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
                        'bankAccId' => $item->bankID,
                        'logId' => $logs->id,
                        'isReturnYN' => 0,
                        'amount' => $item->amount
                    );

                }
                POSBankGLEntries::insert($bankArray);


                POSGLEntries::insert($bankGLArray);
                POSGLEntries::insert($itemGLArray);
                POSGLEntries::insert($taxGLArray);


                foreach ($invItems as $gl) {

                    $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    if ($gl->categoryID == 1) {
                        $costGLArray = [
                            'shiftId' => $gl->shiftId,
                            'documentSystemId' => 110,
                            'documentCode' => $documentCode,
                            'glCode' => $gl->plGLCode,
                            'logId' => $logs['id'],
                            'amount' => $gl->amount
                        ];
                        POSGLEntries::insert($costGLArray);
                        if ($gl->glYN == -1) {
                            $inventoryGLArray = [
                                'shiftId' => $gl->shiftId,
                                'documentSystemId' => 110,
                                'documentCode' => $documentCode,
                                'glCode' => $gl->plGLCode,
                                'logId' => $logs['id'],
                                'amount' => $gl->amount * -1
                            ];
                            POSGLEntries::insert($inventoryGLArray);
                        } else {
                            $inventoryGLArray = [
                                'shiftId' => $gl->shiftId,
                                'documentSystemId' => 110,
                                'documentCode' => $documentCode,
                                'glCode' => $gl->bsGLCode,
                                'logId' => $logs['id'],
                                'amount' => $gl->amount * -1
                            ];
                            POSGLEntries::insert($inventoryGLArray);
                        }


                    }
                    $sumQty = ErpItemLedger::where('itemSystemCode', $gl->itemID)->where('companySystemID',$shiftDetails->companyID)->where('wareHouseSystemCode', $gl->wareHouseID)->sum('inOutQty');

                    if($gl->qty > $sumQty){
                        $remQty =  $gl->qty - $sumQty;
                        POSItemGLEntries::where('shiftId',$gl->shiftId)->where('itemAutoId', $gl->itemID)->update(['insufficientQty' => $remQty]);
                        $isInsufficient = 1;

                    }
                }
                if($isInsufficient == 1){
                    return $this->sendError('Insufficient Quantities');
                }


            $logged_user = \Helper::getEmployeeSystemID();

            $masterData = ['documentSystemID' => 110, 'autoID' => $shiftId, 'companySystemID' => $shiftDetails->companyID, 'employeeSystemID' => $logged_user, 'companyID' => $shiftDetails->companyCode];
            GeneralLedgerInsert::dispatch($masterData);
            POSItemLedgerInsert::dispatch($masterData);
            BankLedgerInsert::dispatch($masterData);
            $taxLedgerData = null;
            TaxLedgerInsert::dispatch($masterData,$taxLedgerData);


            \Illuminate\Support\Facades\DB::beginTransaction();

            try {

                $invoices = DB::table('pos_source_invoice')
                    ->selectRaw('pos_source_invoice.*')
                    ->where('pos_source_invoice.shiftID', $shiftId)
                    ->where('pos_source_invoice.isCreditSales', 1)
                    ->get();
                foreach ($invoices as $invoice) {

                        $companyFinanceYear = CompanyFinanceYear::where('bigginingDate', "<", $invoice->invoiceDate)->where('endingDate', ">", $invoice->invoiceDate)->where('companySystemID', $shiftDetails->companyID)->first();

                        $companyFinancePeriod = CompanyFinancePeriod::where('dateFrom', "<", $invoice->invoiceDate)->where('dateTo', ">", $invoice->invoiceDate)->where('companySystemID', $shiftDetails->companyID)->first();

                        $input = ['bookingDate' => $invoice->invoiceDate, 'comments' => "Inv Created by POS System", 'companyFinancePeriodID' => $companyFinancePeriod->companyFinancePeriodID, 'companyFinanceYearID' => $companyFinanceYear->companyFinanceYearID, 'companyID' => 1, 'custTransactionCurrencyID' => 1, 'customerID' => 1, 'date_of_supply' => $invoice->invoiceDate, 'invoiceDueDate' => $invoice->invoiceDate, 'isPerforma' => 2, 'serviceLineSystemID' => 1, 'wareHouseSystemCode' => 1, 'customerInvoiceNo' => "abd-56", 'bankAccountID' => 1, 'bankID' => 2];


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
                        $myCurr = $input['custTransactionCurrencyID'];

                        $companyCurrency = \Helper::companyCurrency($company['companySystemID']);
                        $companyCurrencyConversion = \Helper::currencyConversion($company['companySystemID'], $myCurr, $myCurr, 0);
                        /*exchange added*/
                        $input['custTransactionCurrencyER'] = 1;
                        $input['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                        $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                        $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                        $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

                        $bank = BankAssign::select('bankmasterAutoID')
                            ->where('companySystemID', $input['companyID'])
                            ->where('isDefault', -1)
                            ->first();
                        if ($bank) {
                            $input['bankID'] = $bank->bankmasterAutoID;
                            $bankAccount = BankAccount::where('companySystemID', $input['companyID'])
                                ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                                ->where('isDefault', 1)
                                ->where('accountCurrencyID', $myCurr)
                                ->first();
                            if ($bankAccount) {
                                $input['bankAccountID'] = $bankAccount->bankAccountAutoID;
                            }

                        }

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
                        if (($input['bookingDate'] >= $FYPeriodDateFrom) && ($input['bookingDate'] <= $FYPeriodDateTo)) {
                            $customerInvoiceDirects = $this->customerInvoiceDirectRepository->create($input);
                            $items = DB::table('pos_source_invoicedetail')
                                ->selectRaw('pos_source_invoicedetail.*')
                                ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
                                ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                                ->where('pos_source_invoicedetail.invoiceID', $invoice->invoiceID)
                                ->get();
                            foreach ($items as $item) {
                                $input2 = ['customerCatalogDetailID' => 0, 'customerCatalogMasterID' => 0, 'itemCode' => $item->itemAutoID, 'qtyIssued' => $item->qty, 'issueCostLocal' => $item->companyLocalAmount, 'issueCostRpt' => $item->companyReportingAmount, 'qtyIssuedDefaultMeasure' => $item->qty, 'sellingCost' => $item->companyLocalAmount, 'sellingCostAfterMargin' => $item->companyLocalAmount, 'sellingCostAfterMarginLocal' => $item->companyLocalAmount, 'sellingCostAfterMarginRpt' => $item->companyReportingAmount, 'sellingCurrencyER' => $item->transactionExchangeRate, 'sellingCurrencyID' => $item->transactionCurrencyID];

                                $input2['companySystemID'] = $customerInvoiceDirects->companySystemID;
                                $input2['custInvoiceDirectAutoID'] = $customerInvoiceDirects->custInvoiceDirectAutoID;

                                $companySystemID = $input2['companySystemID'];


                                $item = ItemAssigned::with(['item_master'])
                                    ->where('itemCodeSystem', $item->itemAutoID)
                                    ->where('companySystemID', $companySystemID)
                                    ->first();
                                if (empty($item)) {
                                    return $this->sendError('Item not found');
                                }

                                $customerInvoiceDirect = CustomerInvoiceDirect::find($input2['custInvoiceDirectAutoID']);

                                if (empty($customerInvoiceDirect)) {
                                    return $this->sendError('Customer Invoice Direct Not Found');
                                }


                                $input2['itemCodeSystem'] = $item->itemCodeSystem;
                                $input2['itemPrimaryCode'] = $item->itemPrimaryCode;
                                $input2['itemDescription'] = $item->itemDescription;
                                $input2['itemUnitOfMeasure'] = $item->itemUnitOfMeasure;

                                $input2['unitOfMeasureIssued'] = $item->itemUnitOfMeasure;
                                $input2['trackingType'] = isset($item->item_master->trackingType) ? $item->item_master->trackingType : null;
                                $input2['convertionMeasureVal'] = 1;

                                if (!isset($input2['qtyIssued'])) {
                                    $input2['qtyIssued'] = 0;
                                    $input2['qtyIssuedDefaultMeasure'] = 0;
                                }

                                $input2['comments'] = '';
                                $input2['itemFinanceCategoryID'] = $item->financeCategoryMaster;
                                $input2['itemFinanceCategorySubID'] = $item->financeCategorySub;

                                $input2['localCurrencyID'] = $customerInvoiceDirect->localCurrencyID;
                                $input2['localCurrencyER'] = $customerInvoiceDirect->localCurrencyER;


                                $data = array('companySystemID' => $companySystemID,
                                    'itemCodeSystem' => $input2['itemCodeSystem'],
                                    'wareHouseId' => $customerInvoiceDirect->wareHouseSystemCode);

                                $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

                                $input2['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
                                $input2['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                                $input2['currentStockQtyInDamageReturn'] = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];


                                $input2['issueCostLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
                                $input2['issueCostRpt'] = $itemCurrentCostAndQty['wacValueReporting'];



                                $input2['issueCostLocalTotal'] = $input2['issueCostLocal'] * $input2['qtyIssuedDefaultMeasure'];

                                $input2['reportingCurrencyID'] = $customerInvoiceDirect->companyReportingCurrencyID;
                                $input2['reportingCurrencyER'] = $customerInvoiceDirect->companyReportingER;

                                $input2['issueCostRptTotal'] = $input2['issueCostRpt'] * $input2['qtyIssuedDefaultMeasure'];
                                $input2['marginPercentage'] = 0;

                                $companyCurrencyConversion = Helper::currencyConversion($companySystemID, $customerInvoiceDirect->companyReportingCurrencyID, $customerInvoiceDirect->custTransactionCurrencyID, $input2['issueCostRpt']);
                                $input2['sellingCurrencyID'] = $customerInvoiceDirect->custTransactionCurrencyID;
                                $input2['sellingCurrencyER'] = $customerInvoiceDirect->custTransactionCurrencyER;
                                $input2['sellingCost'] = ($companyCurrencyConversion['documentAmount'] != 0) ? $companyCurrencyConversion['documentAmount'] : 1.0;
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

                                    $input2['sellingCostAfterMargin'] = $catalogDetail->localPrice;
                                    $input2['marginPercentage'] = ($input2['sellingCostAfterMargin'] - $input2['sellingCost']) / $input2['sellingCost'] * 100;
                                    $input2['part_no'] = $catalogDetail->partNo;
                                } else {
                                    $input2['sellingCostAfterMargin'] = $input2['sellingCost'];
                                    $input2['part_no'] = $item->secondaryItemCode;
                                }

                                if (isset($input2['marginPercentage']) && $input2['marginPercentage'] != 0) {
//            $input2['sellingCostAfterMarginLocal'] = ($input2['issueCostLocal']) + ($input2['issueCostLocal']*$input2['marginPercentage']/100);
//            $input2['sellingCostAfterMarginRpt'] = ($input2['issueCostRpt']) + ($input2['issueCostRpt']*$input2['marginPercentage']/100);
                                } else {
                                    $input2['sellingCostAfterMargin'] = $input2['sellingCost'];
//            $input2['sellingCostAfterMarginLocal'] = $input2['issueCostLocal'];
//            $input2['sellingCostAfterMarginRpt'] = $input2['issueCostRpt'];
                                }

                                $costs = $this->updateCostBySellingCost($input2, $customerInvoiceDirect);
                                $input2['sellingCostAfterMarginLocal'] = $costs['sellingCostAfterMarginLocal'];
                                $input2['sellingCostAfterMarginRpt'] = $costs['sellingCostAfterMarginRpt'];

                                $input2['sellingTotal'] = $input2['sellingCostAfterMargin'] * $input2['qtyIssuedDefaultMeasure'];

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
                                    $input2['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                                    $input2['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                                    $input2['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                                    $input2['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
                                    $input2['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;
                                } else {
                                    return $this->sendError("Finance Item category sub assigned not found", 500);
                                }


                                // check policy 18

                                $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
                                    ->where('companySystemID', $companySystemID)
                                    ->first();

                                if ($item->financeCategoryMaster == 1) {
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
                                    $input2['VATPercentage'] = $vatDetails['percentage'];
                                    $input2['VATApplicableOn'] = $vatDetails['applicableOn'];
                                    $input2['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
                                    $input2['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
                                    $input2['VATAmount'] = 0;
                                    if (isset($input2['sellingCostAfterMargin']) && $input2['sellingCostAfterMargin'] > 0) {
                                        $input2['VATAmount'] = (($input2['sellingCostAfterMargin'] / 100) * $vatDetails['percentage']);
                                    }
                                    $currencyConversionVAT = \Helper::currencyConversion($customerInvoiceDirect->companySystemID, $customerInvoiceDirect->custTransactionCurrencyID, $customerInvoiceDirect->custTransactionCurrencyID, $input2['VATAmount']);

                                    $input2['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                                    $input2['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
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

                            $documentApproved = DocumentApproved::where('documentSystemCode', $customerInvoiceDirects->custInvoiceDirectAutoID)->where('documentSystemID', 20)->first();

                            $customerInvoiceDirects["approvalLevelID"] = 14;
                            $customerInvoiceDirects["documentApprovedID"] = $documentApproved->documentApprovedID;
                            $customerInvoiceDirects["documentSystemCode"] = $customerInvoiceDirects->custInvoiceDirectAutoID;
                            $customerInvoiceDirects["rollLevelOrder"] = 1;
                            $approve = \Helper::approveDocument($customerInvoiceDirects);
                            if (!$approve["success"]) {
                                return $this->sendError($approve["message"]);
                            }

                            if (!$resVat['status']) {
                                return $this->sendError($resVat['message']);
                            };
                        } else {
                            return $this->sendResponse('e', 'Document date should be between financial period start date and end date');
                        }
                    \Illuminate\Support\Facades\DB::commit();

                }

                \Illuminate\Support\Facades\DB::commit();

            }
            catch (\Exception $exception){
                \Illuminate\Support\Facades\DB::rollback();
                return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
            }


        }

        if($shiftDetails->posType == 2) {
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

            if ($isPostGroupBy == 0) {

                $bankGL = DB::table('pos_source_menusalesmaster')
                    ->selectRaw('SUM(pos_source_menusalesmaster.cashReceivedAmount) as amount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalespayments.GLCode as glCode, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID')
                    ->join('pos_source_menusalespayments', 'pos_source_menusalespayments.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                    ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                    ->groupBy('pos_source_menusalesmaster.shiftID')
                    ->groupBy('pos_source_menusalesmaster.menuSalesID')
                    ->get();


                $invItems = DB::table('pos_source_menusalesitems')
                    ->selectRaw('pos_source_menusalesitems.menuSalesPrice as amount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalesitemdetails.itemAutoID as itemID, pos_source_menusalesitems.revenueGLAutoID as glCode,  itemmaster.financeCategoryMaster as categoryID, financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, financeitemcategorysub.includePLForGRVYN as glYN, pos_source_menusalesitemdetails.qty as qty, pos_source_menusalesitemdetails.cost as price, pos_source_menusalesitemdetails.UOMID as uom, pos_source_menusalesmaster.wareHouseAutoID as wareHouseID')
                    ->join('pos_source_menusalesmaster', 'pos_source_menusalesmaster.menuSalesID', '=', 'pos_source_menusalesitems.menuSalesID')
                    ->join('pos_source_menusalesitemdetails', 'pos_source_menusalesitemdetails.menuSalesItemID', '=', 'pos_source_menusalesitems.menuSalesItemID')
                    ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_menusalesitemdetails.itemAutoID')
                    ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                    ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                    ->get();


                $taxItems = DB::table('pos_source_menusalesmaster')
                    ->selectRaw('pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalesoutlettaxes.taxAmount as taxAmount, erp_taxmaster_new.outputVatGLAccountAutoID as outputVatGLCode')
                    ->join('pos_source_menusalesoutlettaxes', 'pos_source_menusalesoutlettaxes.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                    ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_menusalesoutlettaxes.taxMasterID')
                    ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                    ->get();

                $taxItems2 = DB::table('pos_source_menusalesmaster')
                    ->selectRaw('pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalestaxes.taxAmount as taxAmount, erp_taxmaster_new.outputVatGLAccountAutoID as outputVatGLCode')
                    ->join('pos_source_menusalestaxes', 'pos_source_menusalestaxes.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                    ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_menusalestaxes.taxMasterID')
                    ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                    ->get();

                $bankItems = DB::table('pos_source_menusalesmaster')
                    ->selectRaw('SUM(pos_source_menusalesmaster.cashReceivedAmount) as amount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_paymentglconfigdetail.erp_bank_acc_id as bankID, pos_source_menusalespayments.GLCode as glCode, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalespayments.paymentConfigDetailID as payDetailID')
                    ->join('pos_source_menusalespayments', 'pos_source_menusalespayments.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                    ->join('pos_source_paymentglconfigdetail', 'pos_source_paymentglconfigdetail.ID', '=', 'pos_source_menusalespayments.paymentConfigDetailID')
                    ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                    ->groupBy('pos_source_menusalesmaster.menuSalesID')
                    ->groupBy('pos_source_menusalesmaster.shiftID')
                    ->get();

            }
            if ($isPostGroupBy == 1) {
                $bankGL = DB::table('pos_source_menusalesmaster')
                    ->selectRaw('SUM(pos_source_menusalesmaster.cashReceivedAmount) as amount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalespayments.GLCode as glCode, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID')
                    ->join('pos_source_menusalespayments', 'pos_source_menusalespayments.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                    ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                    ->groupBy('pos_source_menusalesmaster.menuSalesID')
                    ->groupBy('pos_source_menusalesmaster.shiftID')
                    ->get();


                $invItems = DB::table('pos_source_menusalesitems')
                    ->selectRaw('SUM(pos_source_menusalesitems.menuSalesPrice) as amount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalesitemdetails.itemAutoID as itemID, pos_source_menusalesitems.revenueGLAutoID as glCode,  itemmaster.financeCategoryMaster as categoryID, financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, financeitemcategorysub.includePLForGRVYN as glYN, pos_source_menusalesitemdetails.qty as qty, pos_source_menusalesitemdetails.cost as price, pos_source_menusalesitemdetails.UOMID as uom,pos_source_menusalesmaster.wareHouseAutoID as wareHouseID')
                    ->join('pos_source_menusalesmaster', 'pos_source_menusalesmaster.menuSalesID', '=', 'pos_source_menusalesitems.menuSalesID')
                    ->join('pos_source_menusalesitemdetails', 'pos_source_menusalesitemdetails.menuSalesItemID', '=', 'pos_source_menusalesitems.menuSalesItemID')
                    ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_menusalesitemdetails.itemAutoID')
                    ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                    ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                    ->groupBy('pos_source_menusalesmaster.shiftID')
                    ->get();


                $taxItems = DB::table('pos_source_menusalesmaster')
                    ->selectRaw('pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, SUM(pos_source_menusalesoutlettaxes.taxAmount) as taxAmount, erp_taxmaster_new.outputVatGLAccountAutoID as outputVatGLCode')
                    ->join('pos_source_menusalesoutlettaxes', 'pos_source_menusalesoutlettaxes.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                    ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_menusalesoutlettaxes.taxMasterID')
                    ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                    ->groupBy('pos_source_menusalesmaster.shiftID')
                    ->get();

                $taxItems2 = DB::table('pos_source_menusalesmaster')
                    ->selectRaw('pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, SUM(pos_source_menusalestaxes.taxAmount) as taxAmount, erp_taxmaster_new.outputVatGLAccountAutoID as outputVatGLCode')
                    ->join('pos_source_menusalestaxes', 'pos_source_menusalestaxes.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                    ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_menusalestaxes.taxMasterID')
                    ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                    ->groupBy('pos_source_menusalesmaster.shiftID')
                    ->get();

                $bankItems = DB::table('pos_source_menusalesmaster')
                    ->selectRaw('SUM(pos_source_menusalesmaster.cashReceivedAmount) as amount, pos_source_menusalesmaster.menuSalesID as invoiceID, pos_source_paymentglconfigdetail.erp_bank_acc_id as bankID, pos_source_menusalespayments.GLCode as glCode, pos_source_menusalesmaster.shiftID as shiftId, pos_source_menusalesmaster.companyID as companyID, pos_source_menusalespayments.paymentConfigDetailID as payDetailID')
                    ->join('pos_source_menusalespayments', 'pos_source_menusalespayments.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                    ->join('pos_source_paymentglconfigdetail', 'pos_source_paymentglconfigdetail.ID', '=', 'pos_source_menusalespayments.paymentConfigDetailID')
                    ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                    ->groupBy('pos_source_menusalesmaster.menuSalesID')
                    ->groupBy('pos_source_menusalesmaster.shiftID')
                    ->get();
            }

                foreach ($taxItems as $gl) {

                    $documentCode = ('RPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $taxGLArray1[] = array(
                        'shiftId' => $gl->shiftId,
                        'documentSystemId' => 111,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->outputVatGLCode,
                        'logId' => $logs['id'],
                        'amount' => $gl->taxAmount * -1
                    );

                }
                POSTaxGLEntries::insert($taxGLArray1);

                foreach ($taxItems2 as $gl) {

                    $documentCode = ('RPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $taxGLArray2[] = array(
                        'shiftId' => $gl->shiftId,
                        'documentSystemId' => 111,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->outputVatGLCode,
                        'logId' => $logs['id'],
                        'amount' => $gl->taxAmount * -1
                    );

                }

                POSTaxGLEntries::insert($taxGLArray2);

                foreach ($bankGL as $gl) {

                    $documentCode = ('RPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $bankGLArray[] = array(
                        'shiftId' => $gl->shiftId,
                        'documentSystemId' => 111,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->glCode,
                        'logId' => $logs['id'],
                        'amount' => $gl->amount
                    );

                }

                foreach ($invItems as $gl) {

                    $documentCode = ('RPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    $itemGLArray[] = array(
                        'shiftId' => $gl->shiftId,
                        'documentSystemId' => 111,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->glCode,
                        'logId' => $logs['id'],
                        'amount' => $gl->amount * -1
                    );

                }


                foreach ($invItems as $item) {
                    $itemArray[] = array(
                        'shiftId' => $item->shiftId,
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
                        'bankAccId' => $item->bankID,
                        'logId' => $logs->id,
                        'isReturnYN' => 0,
                        'amount' => $item->amount
                    );

                }
                POSBankGLEntries::insert($bankArray);


                POSGLEntries::insert($bankGLArray);
                POSGLEntries::insert($itemGLArray);
                POSGLEntries::insert($taxGLArray1);
                POSGLEntries::insert($taxGLArray2);


                foreach ($invItems as $gl) {

                    $documentCode = ('RPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
                    if ($gl->categoryID == 1) {
                        $costGLArray = [
                            'shiftId' => $gl->shiftId,
                            'documentSystemId' => 111,
                            'documentCode' => $documentCode,
                            'glCode' => $gl->plGLCode,
                            'logId' => $logs['id'],
                            'amount' => $gl->amount
                        ];
                        POSGLEntries::insert($costGLArray);
                        if ($gl->glYN == -1) {
                            $inventoryGLArray = [
                                'shiftId' => $gl->shiftId,
                                'documentSystemId' => 111,
                                'documentCode' => $documentCode,
                                'glCode' => $gl->plGLCode,
                                'logId' => $logs['id'],
                                'amount' => $gl->amount * -1
                            ];
                            POSGLEntries::insert($inventoryGLArray);
                        } else {
                            $inventoryGLArray = [
                                'shiftId' => $gl->shiftId,
                                'documentSystemId' => 111,
                                'documentCode' => $documentCode,
                                'glCode' => $gl->bsGLCode,
                                'logId' => $logs['id'],
                                'amount' => $gl->amount * -1
                            ];
                            POSGLEntries::insert($inventoryGLArray);
                        }


                    }
                    $sumQty = ErpItemLedger::where('itemSystemCode', $gl->itemID)->where('companySystemID', $shiftDetails->companyID)->where('wareHouseSystemCode', $gl->wareHouseID)->sum('inOutQty');

                    if ($gl->qty > $sumQty) {
                        $remQty = $gl->qty - $sumQty;
                        POSItemGLEntries::where('shiftId', $gl->shiftId)->where('itemAutoId', $gl->itemID)->update(['insufficientQty' => $remQty]);
                        $isInsufficient = 1;

                    }
                    if ($isInsufficient == 1) {
                        return $this->sendError('Insufficient Quantities');
                    }
                }


            $logged_user = \Helper::getEmployeeSystemID();

            $masterData = ['documentSystemID' => 111, 'autoID' => $shiftId, 'companySystemID' => $shiftDetails->companyID, 'employeeSystemID' => $logged_user, 'companyID' => $shiftDetails->companyCode];
            GeneralLedgerInsert::dispatch($masterData);
            POSItemLedgerInsert::dispatch($masterData);

            BankLedgerInsert::dispatch($masterData);
            $taxLedgerData = null;
            TaxLedgerInsert::dispatch($masterData, $taxLedgerData);

            \Illuminate\Support\Facades\DB::beginTransaction();

            try {
                $invoices = DB::table('pos_source_menusalesmaster')
                    ->selectRaw('pos_source_menusalesmaster.*')
                    ->where('pos_source_menusalesmaster.shiftID', $shiftId)
                    ->where('pos_source_menusalesmaster.isCreditSales', 1)
                    ->get();
                foreach ($invoices as $invoice) {

                    $companyFinanceYear = CompanyFinanceYear::where('bigginingDate', "<", $invoice->menuSalesDate)->where('endingDate', ">", $invoice->menuSalesDate)->where('companySystemID', $shiftDetails->companyID)->first();

                    $companyFinancePeriod = CompanyFinancePeriod::where('dateFrom', "<", $invoice->menuSalesDate)->where('dateTo', ">", $invoice->menuSalesDate)->where('companySystemID', $shiftDetails->companyID)->first();

                    if (!isset($companyFinancePeriod->companyFinancePeriodID) || is_null($companyFinancePeriod->companyFinancePeriodID)) {
                        return $this->sendError('Financial period is not found', 500);
                    }

                    if (!isset($companyFinancePeriod->companyFinanceYearID) || is_null($companyFinancePeriod->companyFinanceYearID)) {
                        return $this->sendError('Financial year is not found', 500);
                    }

                    $input = ['bookingDate' => $invoice->menuSalesDate, 'comments' => "Inv Created by RPOS System", 'companyFinancePeriodID' => $companyFinancePeriod->companyFinancePeriodID, 'companyFinanceYearID' => $companyFinanceYear->companyFinanceYearID, 'companyID' => 1, 'custTransactionCurrencyID' => 1, 'customerID' => 1, 'date_of_supply' => $invoice->menuSalesDate, 'invoiceDueDate' => $invoice->menuSalesDate, 'isPerforma' => 0, 'serviceLineSystemID' => 1, 'wareHouseSystemCode' => 1, 'customerInvoiceNo' => "abd-56", 'bankAccountID' => 1, 'bankID' => 2];


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
                    $myCurr = $input['custTransactionCurrencyID'];

                    $companyCurrency = \Helper::companyCurrency($company['companySystemID']);
                    $companyCurrencyConversion = \Helper::currencyConversion($company['companySystemID'], $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $input['custTransactionCurrencyER'] = 1;
                    $input['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

                    $bank = BankAssign::select('bankmasterAutoID')
                        ->where('companySystemID', $input['companyID'])
                        ->where('isDefault', -1)
                        ->first();
                    if ($bank) {
                        $input['bankID'] = $bank->bankmasterAutoID;
                        $bankAccount = BankAccount::where('companySystemID', $input['companyID'])
                            ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                            ->where('isDefault', 1)
                            ->where('accountCurrencyID', $myCurr)
                            ->first();
                        if ($bankAccount) {
                            $input['bankAccountID'] = $bankAccount->bankAccountAutoID;
                        }

                    }

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
                    if (($input['bookingDate'] >= $FYPeriodDateFrom) && ($input['bookingDate'] <= $FYPeriodDateTo)) {
                        $customerInvoiceDirects = $this->customerInvoiceDirectRepository->create($input);
                        $items = DB::table('pos_source_menusalesitems')
                            ->selectRaw('pos_source_menusalesitems.*')
                            ->join('pos_source_menusalesitemdetails', 'pos_source_menusalesitemdetails.menuSalesItemID', '=', 'pos_source_menusalesitems.menuSalesItemID')
                            ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_menusalesitemdetails.itemAutoID')
                            ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                            ->where('pos_source_menusalesitems.menuSalesID', $invoice->menuSalesID)
                            ->get();
                        foreach ($items as $item) {
                            /* $amount = $request['amount'];
                   $comments = $request['comments'];*/
                            $companySystemID = $shiftDetails->companyID;
                            /* $contractID = $request['contractID'];*/
                            $custInvoiceDirectAutoID = $customerInvoiceDirects->custInvoiceDirectAutoID;
                            $glCode = $item->revenueGLAutoID;
                            /* $qty = $request['qty'];*/
                            /* $serviceLineSystemID = $request['serviceLineSystemID'];
                             $unitCost = $request['unitCost'];
                             $unitID = $request['unitID'];*/


                            /*this*/


                            /*get master*/
                            $master = CustomerInvoiceDirect::select('*')->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();
                            $bookingInvCode = $master->bookingInvCode;
                            /*selectedPerformaMaster*/


                            $tax = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
                                ->where('companySystemID', $master->companySystemID)
                                ->where('documentSystemID', $master->documentSystemiD)
                                ->first();
                            if (!empty($tax)) {
                                // return $this->sendError('Please delete tax details to continue !');
                            }

                            $myCurr = $master->custTransactionCurrencyID;
                            /*currencyID*/

                            //$companyCurrency = \Helper::companyCurrency($myCurr);
                            $decimal = \Helper::getCurrencyDecimalPlace($myCurr);
                            $x = 0;


                            /*$serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')->where('serviceLineSystemID', $serviceLineSystemID)->first();*/
                            $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $glCode)->first();
                            $totalAmount = 0; //$unitCost * $qty;

                            $addToCusInvDetails['custInvoiceDirectID'] = $custInvoiceDirectAutoID;
                            $addToCusInvDetails['companyID'] = $master->companyID;
                            /*  $addToCusInvDetails['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;*/
                            /*        $addToCusInvDetails['serviceLineCode'] = $serviceLine->ServiceLineCode;*/
                            $addToCusInvDetails['customerID'] = $master->customerID;
                            $addToCusInvDetails['glSystemID'] = $chartOfAccount->chartOfAccountSystemID;
                            $addToCusInvDetails['glCode'] = $chartOfAccount->AccountCode;
                            $addToCusInvDetails['glCodeDes'] = $chartOfAccount->AccountDescription;
                            $addToCusInvDetails['accountType'] = $chartOfAccount->catogaryBLorPL;
                            $addToCusInvDetails['comments'] = $master->comments;
                            $addToCusInvDetails['invoiceAmountCurrency'] = $master->custTransactionCurrencyID;
                            $addToCusInvDetails['invoiceAmountCurrencyER'] = 1;
                            /* $addToCusInvDetails['unitOfMeasure'] = $unitID;
                             $addToCusInvDetails['invoiceQty'] = $qty;*/
                            $addToCusInvDetails['unitCost'] = $item->menuCost;
                            $addToCusInvDetails['invoiceAmount'] = $item->salesPriceAfterDiscount;

                            $addToCusInvDetails['localCurrency'] = $master->localCurrencyID;
                            $addToCusInvDetails['localCurrencyER'] = $master->localCurrencyER;

                            $addToCusInvDetails['comRptCurrency'] = $master->companyReportingCurrencyID;
                            $addToCusInvDetails['comRptCurrencyER'] = $master->companyReportingER;
                            $addToCusInvDetails["comRptAmount"] = $item->salesPriceAfterDiscount / $master->companyReportingER; // \Helper::roundValue($MyRptAmount);
                            $addToCusInvDetails["localAmount"] = $item->salesPriceAfterDiscount; // \Helper::roundValue($MyLocalAmount);
                            if ($master->isPerforma == 0) {
                                $addToCusInvDetails['unitOfMeasure'] = 7;
                                $addToCusInvDetails['invoiceQty'] = 1;
                            }

                            if ($master->isVatEligible) {
                                $vatDetails = TaxService::getDefaultVAT($master->companySystemID, $master->customerID, 0);
                                $addToCusInvDetails['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
                                $addToCusInvDetails['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
                                $addToCusInvDetails['VATPercentage'] = $vatDetails['percentage'];
                            }

                            /**/


                            CustomerInvoiceDirectDetail::create($addToCusInvDetails);
                            $details = CustomerInvoiceDirectDetail::select(DB::raw("SUM(invoiceAmount) as bookingAmountTrans"), DB::raw("SUM(localAmount) as bookingAmountLocal"), DB::raw("SUM(comRptAmount) as bookingAmountRpt"))->where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first()->toArray();

                            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($details);


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

                        $documentApproved = DocumentApproved::where('documentSystemCode', $customerInvoiceDirects->custInvoiceDirectAutoID)->where('documentSystemID', 20)->first();

                        $customerInvoiceDirects["approvalLevelID"] = 14;
                        $customerInvoiceDirects["documentApprovedID"] = $documentApproved->documentApprovedID;
                        $customerInvoiceDirects["documentSystemCode"] = $customerInvoiceDirects->custInvoiceDirectAutoID;
                        $customerInvoiceDirects["rollLevelOrder"] = 1;
                        $approve = \Helper::approveDocument($customerInvoiceDirects);
                        if (!$approve["success"]) {
                            return $this->sendError($approve["message"]);
                        }
                    }

                    \Illuminate\Support\Facades\DB::commit();
                }
                \Illuminate\Support\Facades\DB::commit();
            }
            catch (\Exception $exception) {
                \Illuminate\Support\Facades\DB::rollback();
                return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
            }
        }


//            POSTaxGLEntries::where('shiftId', $shiftId)->delete();
//            POSItemGLEntries::where('shiftId', $shiftId)->delete();
//            POSBankGLEntries::where('shiftId', $shiftId)->delete();
//            POSGLEntries::where('shiftId', $shiftId)->delete();

         $logs=POSFinanceLog::where('shiftId', $shiftId)->update(['status' => 2]);

        return $this->sendResponse([$logs, $invItems], "Shift Details retrieved successfully");

    }


    public function createCustomerInvoice($input, $input2){


    }

    public function updateVatFromSalesQuotation($custInvoiceDirectAutoID)
    {
        $invoiceDetails = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
            ->with(['sales_quotation_detail'])
            ->get();

        $totalVATAmount = 0;
        $invoice = CustomerInvoiceDirect::find($custInvoiceDirectAutoID);

        foreach ($invoiceDetails as $key => $value) {
            if ($invoice->isPerforma == 2 || $invoice->isPerforma == 5) {
                $totalVATAmount += $value->qtyIssued * $value->VATAmount;
            } else {
                $totalVATAmount += $value->qtyIssued * ((isset($value->sales_quotation_detail->VATAmount) && !is_null($value->sales_quotation_detail->VATAmount)) ? $value->sales_quotation_detail->VATAmount : 0);
            }
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

}
