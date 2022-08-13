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

use App\Http\Requests\API\CreateShiftDetailsAPIRequest;
use App\Http\Requests\API\UpdateShiftDetailsAPIRequest;
use App\Models\BankAccount;
use App\Models\Company;
use App\Models\Counter;
use App\Models\CurrencyDenomination;
use App\Models\CustomerAssigned;
use App\Models\GposInvoice;
use App\Models\GposPaymentGlConfigDetail;
use App\Models\OutletUsers;
use App\Models\POSFinanceLog;
use App\Models\POSGLEntries;
use App\Models\POSInvoiceSource;
use App\Models\POSSourceCustomerMaster;
use App\Models\POSSourcePaymentGlConfig;
use App\Models\POSSourcePaymentGlConfigDetail;
use App\Models\POSSOURCEShiftDetails;
use App\Models\POSSourceTaxMaster;
use App\Models\ShiftDetails;
use App\Models\TaxVatCategories;
use App\Models\VatSubCategoryType;
use App\Models\WarehouseMaster;
use App\Repositories\ShiftDetailsRepository;
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

    public function __construct(ShiftDetailsRepository $shiftDetailsRepo)
    {
        $this->shiftDetailsRepository = $shiftDetailsRepo;
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
        $posSourceShiftDetails = POSSOURCEShiftDetails::selectRaw('shiftID as value,CONCAT(startTime, " | " ,endTime, " - ", createdUserName) as label')->where('posType', $posTypeID)->get();


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
        $output = POSSourceCustomerMaster::where('customerAutoID', $cusPOSId)->update(['erp_customer_master_id' => $cusERPId]);

        return $this->sendResponse($output, "Shift Details retrieved successfully");
    }

    public function postPosTaxMapping(Request $request) {

        $taxPOSId = $request->taxPOSId;
        $taxERPId = $request->taxERPId;
        $output = POSSourceTaxMaster::where('taxMasterAutoID', $taxPOSId)->update(['erp_tax_master_id' => $taxERPId]);

        return $this->sendResponse($output, "Shift Details retrieved successfully");
    }

    public function postPosPayMapping(Request $request) {

        $payPOSId = $request->payPOSId;
        $payERPId = $request->payERPId;
        $output = POSSourcePaymentGlConfigDetail::where('ID', $payPOSId)->update(['erp_bank_acc_id' => $payERPId]);

        return $this->sendResponse($output, "Shift Details retrieved successfully");
    }

    public function postPosEntries(Request $request){

        $shiftId = $request->shiftId;

        $shiftDetails = POSShiftDetails::find($shiftId);


        $shiftLogArray = [
            'startTime' => $shiftDetails->startTime,
            'endTime' => $shiftDetails->endTime,
            'status' => 1,
            'postGroupNyYN' => 0,
            'shiftId' => $shiftId
        ];
        POSFinanceLog::insert($shiftLogArray);

        $bankGLArray = array();
        $itemGLArray = array();
        $taxGLArray = array();
        $bankGL = DB::table('pos_source_invoice')
            ->selectRaw('pos_source_invoice.netTotal as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoicepayments.GLCode as glCode, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID')
            ->join('pos_source_invoicepayments', 'pos_source_invoicepayments.invoiceID', '=', 'pos_source_invoice.invoiceID')
            ->where('pos_source_invoice.shiftID', $shiftId)
            ->get();


        $invItems = DB::table('pos_source_invoicedetail')
            ->selectRaw('pos_source_invoicedetail.companyLocalAmount as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub, financeitemcategorysub.financeGLcodeRevenueSystemID as glCode, itemmaster.financeCategoryMaster as categoryID, financeitemcategorysub.financeGLcodebBSSystemID as bsGLCode, financeitemcategorysub.financeGLcodePLSystemID as plGLCode, financeitemcategorysub.includePLForGRVYN as glYN')
            ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
            ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
            ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
            ->where('pos_source_invoice.shiftID', $shiftId)
            ->get();


        $taxItems = DB::table('pos_source_invoicedetail')
            ->selectRaw('pos_source_invoicedetail.companyLocalAmount as amount, pos_source_invoice.invoiceID as invoiceID, pos_source_invoice.shiftID as shiftId, pos_source_invoice.companyID as companyID, pos_source_invoicedetail.itemAutoID as itemID, itemmaster.financeCategorySub as financeCategorySub, financeitemcategorysub.financeGLcodeRevenueSystemID as glCode, pos_source_taxledger.amount as taxAmount, pos_source_taxledger.taxMasterID as taxMasterID, erp_taxmaster_new.outputVatGLAccountAutoID as outputVatGLCode')
            ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
            ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'pos_source_invoicedetail.itemAutoID')
            ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
            ->join('pos_source_taxledger', 'pos_source_taxledger.documentDetailAutoID', '=', 'pos_source_invoicedetail.invoiceDetailsID')
            ->join('erp_taxmaster_new', 'erp_taxmaster_new.taxMasterAutoID', '=', 'pos_source_taxledger.taxMasterID')
            ->where('pos_source_invoice.shiftID', $shiftId)
            ->get();


        foreach ($bankGL as $gl){

            $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
            $bankGLArray[] = array(
                'shiftId' => $gl->shiftId,
                'documentSystemId' => 110,
                'documentCode' => $documentCode,
                'glCode' => $gl->glCode,
                'logId' => 1,
                'amount' => $gl->amount
            );

        }

        foreach ($invItems as $gl){

            $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
            $itemGLArray[] = array(
                'shiftId' => $gl->shiftId,
                'documentSystemId' => 110,
                'documentCode' => $documentCode,
                'glCode' => $gl->glCode,
                'logId' => 1,
                'amount' => $gl->amount * -1
            );

        }

        foreach ($taxItems as $gl){

            $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
            $taxGLArray[] = array(
                'shiftId' => $gl->shiftId,
                'documentSystemId' => 110,
                'documentCode' => $documentCode,
                'glCode' => $gl->outputVatGLCode,
                'logId' => 1,
                'amount' => $gl->taxAmount * -1
            );

        }

        POSGLEntries::insert($bankGLArray);
        POSGLEntries::insert($itemGLArray);
        POSGLEntries::insert($taxGLArray);


        foreach ($invItems as $gl){

            $documentCode = ('GPOS\\' . str_pad($gl->shiftId, 6, '0', STR_PAD_LEFT));
            if($gl->categoryID == 1){
                $costGLArray = [
                    'shiftId' => $gl->shiftId,
                    'documentSystemId' => 110,
                    'documentCode' => $documentCode,
                    'glCode' => $gl->plGLCode,
                    'logId' => 1,
                    'amount' => $gl->amount
                ];
                POSGLEntries::insert($costGLArray);
                if($gl->glYN == -1){
                    $inventoryGLArray = [
                        'shiftId' => $gl->shiftId,
                        'documentSystemId' => 110,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->plGLCode,
                        'logId' => 1,
                        'amount' => $gl->amount * -1
                    ];
                    POSGLEntries::insert($inventoryGLArray);
                }
                else{
                    $inventoryGLArray = [
                        'shiftId' => $gl->shiftId,
                        'documentSystemId' => 110,
                        'documentCode' => $documentCode,
                        'glCode' => $gl->bsGLCode,
                        'logId' => 1,
                        'amount' => $gl->amount * -1
                    ];
                    POSGLEntries::insert($inventoryGLArray);
                }


            }


        }


        return $this->sendResponse([$bankGL,$invItems], "Shift Details retrieved successfully");

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
