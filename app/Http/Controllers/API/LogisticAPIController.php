<?php
/**
 * =============================================
 * -- File Name : LogisticAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Logistic
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - September 2018
 * -- Description : This file contains the all CRUD for Logistic
 * -- REVISION HISTORY
 * -- Date: 12-September 2018 By: Fayas Description: Added new functions named as getAllLogisticByCompany(),getLogisticFormData(),
 *                                exportLogisticsByCompanyReport(),getLogisticAudit(),getStatusByLogistic(),checkPullFromGrv()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLogisticAPIRequest;
use App\Http\Requests\API\UpdateLogisticAPIRequest;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\CurrencyMaster;
use App\Models\DocumentMaster;
use App\Models\Logistic;
use App\Models\LogisticModeOfImport;
use App\Models\LogisticShippingMode;
use App\Models\LogisticShippingStatus;
use App\Models\LogisticStatus;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\Unit;
use App\Models\WarehouseMaster;
use App\Repositories\LogisticRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\CreateExcel;
/**
 * Class LogisticController
 * @package App\Http\Controllers\API
 */
class LogisticAPIController extends AppBaseController
{
    /** @var  LogisticRepository */
    private $logisticRepository;

    public function __construct(LogisticRepository $logisticRepo)
    {
        $this->logisticRepository = $logisticRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/logistics",
     *      summary="Get a listing of the Logistics.",
     *      tags={"Logistic"},
     *      description="Get all Logistics",
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
     *                  @SWG\Items(ref="#/definitions/Logistic")
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
        $this->logisticRepository->pushCriteria(new RequestCriteria($request));
        $this->logisticRepository->pushCriteria(new LimitOffsetCriteria($request));
        $logistics = $this->logisticRepository->all();

        return $this->sendResponse($logistics->toArray(), trans('custom.logistics_retrieved_successfully'));
    }

    /**
     * @param CreateLogisticAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/logistics",
     *      summary="Store a newly created Logistic in storage",
     *      tags={"Logistic"},
     *      description="Store Logistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Logistic that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Logistic")
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
     *                  ref="#/definitions/Logistic"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLogisticAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        $input['createdPCid'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'customInvoiceNo' => 'required',
            'customInvoiceDate' => 'required',
            'customInvoiceCurrencyID' => 'required|numeric|min:1',
            'customInvoiceAmount' => 'required',
            'logisticShippingModeID' => 'required|numeric|min:1',
            'modeOfImportID' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $warning = 0;

        $checkInvoiceNo = Logistic::where('companySystemID',$input['companySystemID'])
                                    ->where('customInvoiceNo',$input['customInvoiceNo'])
                                    ->first();
        $message = trans('custom.logistic_saved_successfully');
        if(!empty($checkInvoiceNo)){
            $warning = 1;
            $message = trans('custom.invoice_number_already_exists', ['docCode' => $checkInvoiceNo->logisticDocCode]);
        }

        if (isset($input['nextCustomDocRenewalDate']) && $input['nextCustomDocRenewalDate']) {
            $input['nextCustomDocRenewalDate'] = new Carbon($input['nextCustomDocRenewalDate']);
        }

        if (isset($input['customInvoiceDate']) && $input['customInvoiceDate']) {
            $input['customInvoiceDate'] = new Carbon($input['customInvoiceDate']);
        }

        if (isset($input['customeArrivalDate']) && $input['customeArrivalDate']) {
            $input['customeArrivalDate'] = new Carbon($input['customeArrivalDate']);
        }

        if (isset($input['deliveryDate']) && $input['deliveryDate']) {
            $input['deliveryDate'] = new Carbon($input['deliveryDate']);
        }

        if (isset($input['billofEntryDate']) && $input['billofEntryDate']) {
            $input['billofEntryDate'] = new Carbon($input['billofEntryDate']);
        }

        if (isset($input['agentDOdate']) && $input['agentDOdate']) {
            $input['agentDOdate'] = new Carbon($input['agentDOdate']);
        }

        if (isset($input['shippingDestinationDate']) && $input['shippingDestinationDate']) {
            $input['shippingDestinationDate'] = new Carbon($input['shippingDestinationDate']);
        }

        if (isset($input['shippingOriginDate']) && $input['shippingOriginDate']) {
            $input['shippingOriginDate'] = new Carbon($input['shippingOriginDate']);
        }

        $input['documentSystemID'] = 14;
        $input['documentID'] = 'LOG';

        $company = Company::where('companySystemID', $input['companySystemID'])->with(['localcurrency','reportingcurrency'])->first();

        $localDecimal = 3;
        $rptDecimal = 2;

        if (empty($company)) {
            return $this->sendError(trans('custom.company_not_found'), 500);
        }
        $input['companyID'] = $company->CompanyID;

        if($company->localcurrency){
            $localDecimal = $company->localcurrency->DecimalPlaces;
        }

        if($company->reportingcurrency){
            $rptDecimal = $company->reportingcurrency->DecimalPlaces;
        }

        if (isset($input['serviceLineSystemID'])) {
            $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
            if ($segment) {
                $input['serviceLineCode'] = $segment->ServiceLineCode;
            }
        }

        $lastSerial = Logistic::where('companySystemID', $input['companySystemID'])
                                ->orderBy('serialNo', 'desc')
                                ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $input['serialNo'] = $lastSerialNumber;
        $input['RollLevForApp_curr'] = 1;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        if ($documentMaster) {
            $code = ($company->CompanyID . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['logisticDocCode'] = $code;
        }

        if(isset($input['agentFeeCurrencyID'])){
            $agentFeeConvection = \Helper::currencyConversion($input['companySystemID'], $input['agentFeeCurrencyID'], $input['agentFeeCurrencyID'], $input['agentFee']);
            $input['agentFeeLocalAmount'] = round($agentFeeConvection['localAmount'],$localDecimal);
            $input['agenFeeRptAmount']    = round($agentFeeConvection['reportingAmount'],$rptDecimal);
        }else{
            $input['agenFeeRptAmount']    = 0;
            $input['agentFeeLocalAmount'] = 0;
        }

        if(isset($input['customDutyFeeCurrencyID'])){
            $dutyFeeConvection = \Helper::currencyConversion($input['companySystemID'], $input['customDutyFeeCurrencyID'], $input['customDutyFeeCurrencyID'], $input['customDutyFeeAmount']);
            $input['customDutyFeeLocalAmount']  =  round($dutyFeeConvection['localAmount'],$localDecimal);
            $input['customDutyFeeRptAmount']    =  round($dutyFeeConvection['reportingAmount'],$rptDecimal);
        }else{
            $input['customDutyFeeRptAmount'] = 0;
            $input['customDutyFeeLocalAmount'] = 0;
        }

        $input['customDutyTotalAmount'] = $input['agentFee'] + $input['customDutyFeeAmount'];

        $logistics = $this->logisticRepository->create($input);

        $logistics->warningMsg = $warning;

        return $this->sendResponse($logistics->toArray(), $message);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/logistics/{id}",
     *      summary="Display the specified Logistic",
     *      tags={"Logistic"},
     *      description="Get Logistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Logistic",
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
     *                  ref="#/definitions/Logistic"
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
        /** @var Logistic $logistic */
        $logistic = $this->logisticRepository->with(['location','supplier_by'])->findWithoutFail($id);

        if (empty($logistic)) {
            return $this->sendError(trans('custom.logistic_not_found'));
        }

        return $this->sendResponse($logistic->toArray(), trans('custom.logistic_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLogisticAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/logistics/{id}",
     *      summary="Update the specified Logistic in storage",
     *      tags={"Logistic"},
     *      description="Update Logistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Logistic",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Logistic that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Logistic")
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
     *                  ref="#/definitions/Logistic"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLogisticAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['supplier_by','location']);
        $input = $this->convertArrayToValue($input);
        /** @var Logistic $logistic */
        $logistic = $this->logisticRepository->with(['local_currency','reporting_currency'])->findWithoutFail($id);

        if (empty($logistic)) {
            return $this->sendError(trans('custom.logistic_not_found'));
        }

        if (isset($input['nextCustomDocRenewalDate']) && $input['nextCustomDocRenewalDate']) {
            $input['nextCustomDocRenewalDate'] = new Carbon($input['nextCustomDocRenewalDate']);
        }

        if (isset($input['customInvoiceDate']) && $input['customInvoiceDate']) {
            $input['customInvoiceDate'] = new Carbon($input['customInvoiceDate']);
        }

        if (isset($input['customeArrivalDate']) && $input['customeArrivalDate']) {
            $input['customeArrivalDate'] = new Carbon($input['customeArrivalDate']);
        }

        if (isset($input['deliveryDate']) && $input['deliveryDate']) {
            $input['deliveryDate'] = new Carbon($input['deliveryDate']);
        }

        if (isset($input['billofEntryDate']) && $input['billofEntryDate']) {
            $input['billofEntryDate'] = new Carbon($input['billofEntryDate']);
        }

        if (isset($input['agentDOdate']) && $input['agentDOdate']) {
            $input['agentDOdate'] = new Carbon($input['agentDOdate']);
        }

        if (isset($input['shippingDestinationDate']) && $input['shippingDestinationDate']) {
            $input['shippingDestinationDate'] = new Carbon($input['shippingDestinationDate']);
        }

        if (isset($input['shippingOriginDate']) && $input['shippingOriginDate']) {
            $input['shippingOriginDate'] = new Carbon($input['shippingOriginDate']);
        }

        $localDecimal = 3;
        $rptDecimal = 2;


        if($input['customInvoiceCurrencyID'] != $logistic->customInvoiceCurrencyID){

            $invoiceAmountConversion = \Helper::currencyConversion($logistic->companySystemID, $input['customInvoiceCurrencyID'], $input['customInvoiceCurrencyID'], $input['customInvoiceAmount']);

            if(!empty($invoiceAmountConversion)){
                $input['customInvoiceLocalAmount'] = round($invoiceAmountConversion['localAmount'],$localDecimal);
                $input['customInvoiceRptAmount']   =  round($invoiceAmountConversion['reportingAmount'],$rptDecimal);
                $input['customInvoiceLocalER']     = $invoiceAmountConversion['trasToLocER'];
                $input['customInvoiceRptER']       = $invoiceAmountConversion['trasToRptER'];
            }
        }else{
            $invoiceAmountConversion = \Helper::convertAmountToLocalRpt($logistic->documentSystemID,
                                                                        $logistic->logisticMasterID,
                                                                        $input['customInvoiceAmount']);

            if($invoiceAmountConversion){
                $input['customInvoiceLocalAmount'] = round($invoiceAmountConversion['localAmount'],$localDecimal);
                $input['customInvoiceRptAmount']   = round($invoiceAmountConversion['reportingAmount'],$rptDecimal);
            }
        }

        if($logistic->local_currency){
            $localDecimal = $logistic->local_currency->DecimalPlaces;
        }

        if($logistic->reporting_currency){
            $rptDecimal = $logistic->reporting_currency->DecimalPlaces;
        }

        if (isset($input['serviceLineSystemID'])) {
            $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
            if ($segment) {
                $input['serviceLineCode'] = $segment->ServiceLineCode;
            }
        }
        $input['customDutyFeeCurrencyID'] = $input['agentFeeCurrencyID'];
        if(isset($input['agentFeeCurrencyID']) && $input['agentFeeCurrencyID']){
            $agentFeeConvection = \Helper::currencyConversion($input['companySystemID'], $input['agentFeeCurrencyID'], $input['agentFeeCurrencyID'], $input['agentFee']);
            $input['agentFeeLocalAmount'] = round($agentFeeConvection['localAmount'],$localDecimal);
            $input['agenFeeRptAmount']    = round($agentFeeConvection['reportingAmount'],$rptDecimal);
        }else{
            $input['agenFeeRptAmount']    = 0;
            $input['agentFeeLocalAmount'] = 0;
        }

        if(isset($input['customDutyFeeCurrencyID']) && $input['customDutyFeeCurrencyID']){
            $dutyFeeConvection = \Helper::currencyConversion($input['companySystemID'], $input['customDutyFeeCurrencyID'], $input['customDutyFeeCurrencyID'], $input['customDutyFeeAmount']);
            $input['customDutyFeeLocalAmount']  =  round($dutyFeeConvection['localAmount'],$localDecimal);
            $input['customDutyFeeRptAmount']    =  round($dutyFeeConvection['reportingAmount'],$rptDecimal);
        }else{
            $input['customDutyFeeRptAmount'] = 0;
            $input['customDutyFeeLocalAmount'] = 0;
        }

        $input['customDutyTotalAmount'] = $input['agentFee'] + $input['customDutyFeeAmount'];

        $employee = \Helper::getEmployeeInfo();

        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        $input['modifiedDate'] = now();

        $warning = 0;

        $checkInvoiceNo = Logistic::where('companySystemID',$input['companySystemID'])
                                    ->where('logisticMasterID','!=',$logistic->logisticMasterID)
                                    ->where('customInvoiceNo',$input['customInvoiceNo'])
                                    ->first();
        $message = trans('custom.logistic_updated_successfully');
        if(!empty($checkInvoiceNo)){
            $warning = 1;
            $message = trans('custom.invoice_number_already_exists', ['docCode' => $checkInvoiceNo->logisticDocCode]);
        }

        $logistic = $this->logisticRepository->update($input, $id);

        $logistic->warningMsg = $warning;

        return $this->sendResponse($logistic->toArray(), $message);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/logistics/{id}",
     *      summary="Remove the specified Logistic from storage",
     *      tags={"Logistic"},
     *      description="Delete Logistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Logistic",
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
        /** @var Logistic $logistic */
        $logistic = $this->logisticRepository->findWithoutFail($id);

        if (empty($logistic)) {
            return $this->sendError(trans('custom.logistic_not_found'));
        }

        $logistic->delete();

        return $this->sendResponse($id, trans('custom.logistic_deleted_successfully'));
    }

    /**
     * get All Logistic By Company
     * POST /getAllLogisticByCompany
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getAllLogisticByCompany(Request $request)
    {

        $input = $request->all();
        $logistics = ($this->getAllLogisticByCompanyQry($request));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        return \DataTables::eloquent($logistics)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('logisticMasterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * get Logistic Form Data
     * Get /getLogisticFormData
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getLogisticFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $countries = CountryMaster::all();
        $currencies = CurrencyMaster::all();

        $units = Unit::all();

        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $wareHouseLocation = $wareHouseLocation->get();

        $modeOfShipping = LogisticShippingMode::all();

        $modeOfImport = LogisticModeOfImport::all();

        $suppliers = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName"))
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $company = Company::where('companySystemID',$companyId)->with(['localcurrency','reportingcurrency'])->first();

        $status = LogisticStatus::all();

        $output = array(
            'units' => $units,
            'countries' => $countries,
            'currencies' => $currencies,
            'wareHouseLocation' => $wareHouseLocation,
            'modeOfShipping' => $modeOfShipping,
            'modeOfImport' => $modeOfImport,
            'suppliers' => $suppliers,
            'company' => $company,
            'status' => $status
        );
        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function exportLogisticsByCompanyReport(Request $request)
    {
        $data = array();
        $output = ($this->getAllLogisticByCompanyQry($request))->orderBy('logisticMasterID', 'DES')->get();
        $type = $request->type;
        if (!empty($output)) {
            $x = 0;
            foreach ($output as $value) {
                $data[$x][trans('custom.logistic_code')] = $value->logisticDocCode;
                $data[$x][trans('custom.invoice_no')] = $value->customInvoiceNo;
                $data[$x][trans('custom.invoice_amount')] = $value->customInvoiceAmount;
                $data[$x][trans('custom.invoice_date')] = \Helper::dateFormat($value->customInvoiceDate);
                if ($value->shipping_mode) {
                    $data[$x][trans('custom.mode')] = $value->shipping_mode->modeShippingDescription;
                } else {
                    $data[$x][trans('custom.mode')] = '';
                }
                if ($value->supplier_by) {
                    $data[$x][trans('custom.supplier')] = $value->supplier_by->supplierName;
                } else {
                    $data[$x][trans('custom.supplier')] = '';
                }
                $data[$x][trans('custom.comments')] = $value->comments;

                $data[$x][trans('custom.renewal_date')] = \Helper::dateFormat($value->nextCustomDocRenewalDate);
                $data[$x][trans('custom.arrival_date')] = \Helper::dateFormat($value->customeArrivalDate);
                if ($value->ftaOrDF) {
                    $data[$x][trans('custom.fta_df')] = $value->ftaOrDF;
                } else {
                    $data[$x][trans('custom.fta_df')] = 'NA';
                }
                if ($value->created_by) {
                    $data[$x][trans('custom.created_by')] = $value->created_by->empName;
                } else {
                    $data[$x][trans('custom.created_by')] = '';
                }
                $data[$x][trans('custom.created_at')] = \Helper::dateFormat($value->createdDateTime);
                $x++;
            }
        }


        $companyMaster = Company::find(isset($request->companyId)?$request->companyId:null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );
                    
        $fileName = 'logistic_by_company';
        $path = 'logistic/logistic_by_company/excel/';
        $basePath = CreateExcel::process($data,$type,$fileName,$path, $detail_array);

        if($basePath == '')
        {
             return $this->sendError(trans('custom.unable_to_export_excel'));
        }
        else
        {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }



    }

    public function getAllLogisticByCompanyQry($request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year'));
        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $logistics = Logistic::whereIn('companySystemID', $subCompanies)
            ->with(['created_by', 'supplier_by', 'shipping_mode']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $logistics = $logistics->where(function ($query) use ($search) {
                $query->where('logisticDocCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        }

        return $logistics;
    }

    public function  getCompanyLocalAndRptAmount(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('transactionCurrencyID'));
        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'transactionCurrencyID' => 'required|numeric|min:1',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $company = Company::find($input['companySystemID']);

        if(!$company) {
            return $this->sendError(trans('custom.company_not_found'), 500);
        }
        


        $data = \Helper::currencyConversion($input['companySystemID'], $company->reportingCurrency, $company->reportingCurrency, $input['amount']);

        return $this->sendResponse($data, trans('custom.record_retrieved_successfully_1'));
        
    }


    /**
     * Display the specified Logistic Audit.
     * GET|HEAD /getLogisticAudit
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getLogisticAudit(Request $request)
    {
        $id = $request->get('id');
        $logistic = $this->logisticRepository->getAudit($id);

        if (empty($logistic)) {
            return $this->sendError(trans('custom.logistic_not_found'));
        }

        $logistic->docRefNo = \Helper::getCompanyDocRefNo($logistic->companySystemID, $logistic->documentSystemID);

        return $this->sendResponse($logistic->toArray(), trans('custom.logistic_retrieved_successfully'));
    }

    /**
     * Display a listing of the status by Logistic.
     * GET|HEAD /getStatusByLogistic
     *
     * @param Request $request
     * @return Response
     */
    public function getStatusByLogistic(Request $request)
    {
        $input = $request->all();
        $rId = $input['logisticMasterID'];

        $items = LogisticShippingStatus::where('logisticMasterID', $rId)
            ->with(['status'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.logistic_status_retrieved_successfully'));
    }

    public function checkPullFromGrv(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        $logistic = $this->logisticRepository->findWithoutFail($id);

        if (empty($logistic)) {
            return $this->sendError(trans('custom.logistic_not_found'));
        }

        $validator = \Validator::make($logistic->toArray(), [
            'supplierID' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }


        return $this->sendResponse($logistic->toArray(), trans('custom.logistic_retrieved_successfully'));
    }

}
