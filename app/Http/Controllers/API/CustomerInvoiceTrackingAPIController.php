<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateCustomerInvoiceTrackingAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceTrackingAPIRequest;
use App\Models\ClientPerformaAppType;
use App\Models\Company;
use App\Models\Contract;
use App\Models\CustomerAssigned;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceTracking;
use App\Models\CustomerInvoiceTrackingDetail;
use App\Models\DocumentMaster;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\Year;
use App\Repositories\CustomerInvoiceTrackingRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerInvoiceTrackingController
 * @package App\Http\Controllers\API
 */

class CustomerInvoiceTrackingAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceTrackingRepository */
    private $customerInvoiceTrackingRepository;

    public function __construct(CustomerInvoiceTrackingRepository $customerInvoiceTrackingRepo)
    {
        $this->customerInvoiceTrackingRepository = $customerInvoiceTrackingRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceTrackings",
     *      summary="Get a listing of the CustomerInvoiceTrackings.",
     *      tags={"CustomerInvoiceTracking"},
     *      description="Get all CustomerInvoiceTrackings",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceTracking")
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
        $this->customerInvoiceTrackingRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceTrackingRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceTrackings = $this->customerInvoiceTrackingRepository->all();

        return $this->sendResponse($customerInvoiceTrackings->toArray(), trans('custom.customer_invoice_trackings_retrieved_successfully'));
    }

    /**
     * @param CreateCustomerInvoiceTrackingAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceTrackings",
     *      summary="Store a newly created CustomerInvoiceTracking in storage",
     *      tags={"CustomerInvoiceTracking"},
     *      description="Store CustomerInvoiceTracking",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceTracking that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceTracking")
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
     *                  ref="#/definitions/CustomerInvoiceTracking"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceTrackingAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('companyFinanceYearID', 'companyFinancePeriodID'));

        $validator = \Validator::make($input, [
            'companyFinancePeriodID' => 'required|numeric|min:1',
            'companyFinanceYearID' => 'required|numeric|min:1',
            'submittedDate' => 'required|date',
            'serviceLineSystemID' => 'required|numeric|min:1',
            'customerID' => 'required|numeric|min:1',
            'companySystemID' => 'required|numeric|min:1',
            'contractUID' => 'required|numeric|min:1',
            'customerInvoiceTrackingCode' => 'required',
            'manualTrackingNo' => 'required',
            'approvalType' => 'required',
            'comments' => 'required',
        ]);


        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if(isset($input['contractUID']) && $input['contractUID']){
            $contract = Contract::find($input['contractUID']);
            if ($contract) {
                $input['contractNumber'] = $contract->ContractNumber;
            }
        }

        if(isset($input['serviceLineSystemID']) && $input['serviceLineSystemID']){
            $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
            if ($segment) {
                $input['serviceLineCode'] = $segment->ServiceLineCode;
            }
        }

        if (isset($input['documentSystemID'])) {

            $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

            if ($documentMaster) {
                $input['documentID'] = $documentMaster->documentID;
            }
        }else{
            $input['documentSystemID'] = 39;
            $input['documentID'] = 'BS';
        }

        if (isset($input['companySystemID'])) {

            $companyMaster = Company::where('companySystemID', $input['companySystemID'])->first();

            if ($companyMaster) {
                $input['companyID'] = $companyMaster->CompanyID;
            }
        }

        $input['submittedDate'] = Carbon::parse($input['submittedDate'])->format('Y-m-d H:i:s');
        $input['submittedYear'] = date('Y', strtotime($input['submittedDate']));

        $employee = Helper::getEmployeeInfo();

        $input['submittedYN'] = -1;
        $input['submittedEmpID'] = $employee->empID;
        $input['submittedEmpSystemID'] = $employee->employeeSystemID;
        $input['submittedEmpName'] = $employee->empName;

        $companyFinanceYear = Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
        }else{
            $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
            $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
        }

        $companyFinancePeriod = Helper::companyFinancePeriodCheck($input);
        if (!$companyFinancePeriod["success"]) {
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else {
            $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
        }

        $documentDate = Carbon::parse($input['submittedDate'])->format('Y-m-d');
        $monthBegin = Carbon::parse($input['FYPeriodDateFrom'])->format('Y-m-d');
        $monthEnd = Carbon::parse($input['FYPeriodDateTo'])->format('Y-m-d');
        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('submitted date is not within the selected financial period !', 500);
        }

        // get last serial number by company financial year
        $lastSerial = CustomerInvoiceTracking::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $input['serialNo'] = $lastSerialNumber;

        $employee = Helper::getEmployeeInfo();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $customerInvoiceTracking = $this->customerInvoiceTrackingRepository->create($input);

        return $this->sendResponse($customerInvoiceTracking->toArray(), trans('custom.customer_invoice_tracking_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceTrackings/{id}",
     *      summary="Display the specified CustomerInvoiceTracking",
     *      tags={"CustomerInvoiceTracking"},
     *      description="Get CustomerInvoiceTracking",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceTracking",
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
     *                  ref="#/definitions/CustomerInvoiceTracking"
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
        /** @var CustomerInvoiceTracking $customerInvoiceTracking */
        $customerInvoiceTracking = $this->customerInvoiceTrackingRepository
            ->with(['detail','customer','finance_period_by' => function ($query) {
                $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
            }, 'finance_year_by' => function ($query) {
                $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
            }])->findWithoutFail($id);

        if (empty($customerInvoiceTracking)) {
            return $this->sendError(trans('custom.batch_submission_not_found_1'));
        }

        return $this->sendResponse($customerInvoiceTracking->toArray(), trans('custom.customer_invoice_tracking_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceTrackingAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceTrackings/{id}",
     *      summary="Update the specified CustomerInvoiceTracking in storage",
     *      tags={"CustomerInvoiceTracking"},
     *      description="Update CustomerInvoiceTracking",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceTracking",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceTracking that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceTracking")
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
     *                  ref="#/definitions/CustomerInvoiceTracking"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceTrackingAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('companyFinanceYearID', 'companyFinancePeriodID','customerID','contractUID','serviceLineSystemID','approvalType'));
        $input = array_except($input,['detail','customer','finance_period_by','finance_year_by']);
        $validator = \Validator::make($input, [
            'companyFinancePeriodID' => 'required|numeric|min:1',
            'companyFinanceYearID' => 'required|numeric|min:1',
            'submittedDate' => 'required|date',
            'serviceLineSystemID' => 'required|numeric|min:1',
            'customerID' => 'required|numeric|min:1',
            'companySystemID' => 'required|numeric|min:1',
            'contractUID' => 'required|numeric|min:1',
            'customerInvoiceTrackingCode' => 'required',
            'manualTrackingNo' => 'required',
            'approvalType' => 'required',
            'comments' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }


        if(isset($input['contractUID']) && $input['contractUID']){
            $contract = Contract::find($input['contractUID']);
            if ($contract) {
                $input['contractNumber'] = $contract->ContractNumber;
            }
        }

        if(isset($input['serviceLineSystemID']) && $input['serviceLineSystemID']){
            $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
            if ($segment) {
                $input['serviceLineCode'] = $segment->ServiceLineCode;
            }
        }

        if (isset($input['documentSystemID'])) {

            $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

            if ($documentMaster) {
                $input['documentID'] = $documentMaster->documentID;
            }
        }else{
            $input['documentSystemID'] = 39;
            $input['documentID'] = 'BS';
        }

        if (isset($input['companySystemID'])) {

            $companyMaster = Company::where('companySystemID', $input['companySystemID'])->first();

            if ($companyMaster) {
                $input['companyID'] = $companyMaster->CompanyID;
            }
        }

        $input['submittedDate'] = Carbon::parse($input['submittedDate'])->format('Y-m-d H:i:s');
        $input['submittedYear'] = date('Y', strtotime($input['submittedDate']));

        $documentDate = Carbon::parse($input['submittedDate'])->format('Y-m-d');
        $monthBegin = Carbon::parse($input['FYPeriodDateFrom'])->format('Y-m-d');
        $monthEnd = Carbon::parse($input['FYPeriodDateTo'])->format('Y-m-d');
        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('submitted date is not within the selected financial period !', 500);
        }

        /** @var CustomerInvoiceTracking $customerInvoiceTracking */
        $customerInvoiceTracking = $this->customerInvoiceTrackingRepository->findWithoutFail($id);

        if (empty($customerInvoiceTracking)) {
            return $this->sendError(trans('custom.customer_invoice_tracking_not_found'));
        }

        $customerInvoiceTracking = $this->customerInvoiceTrackingRepository->update($input, $id);
        $this->updateMasterPayment($id);
        return $this->sendResponse($customerInvoiceTracking->toArray(), trans('custom.customerinvoicetracking_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceTrackings/{id}",
     *      summary="Remove the specified CustomerInvoiceTracking from storage",
     *      tags={"CustomerInvoiceTracking"},
     *      description="Delete CustomerInvoiceTracking",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceTracking",
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
        /** @var CustomerInvoiceTracking $customerInvoiceTracking */
        $customerInvoiceTracking = $this->customerInvoiceTrackingRepository->findWithoutFail($id);

        if (empty($customerInvoiceTracking)) {
            return $this->sendError(trans('custom.customer_invoice_tracking_not_found'));
        }

        $customerInvoiceTracking->delete();

        return $this->sendResponse($id, trans('custom.customer_invoice_tracking_deleted_successfully'));
    }


    /**
     * get All Materiel Issues By Company
     * POST /getAllMaterielIssuesByCompany
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getAllBatchSubmissionByCompany(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'contractUID', 'year', 'month', 'customerID'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        
        $search = $request->input('search.value');

        $customerInvoiceTracking = $this->customerInvoiceTrackingRepository->customerInvoiceTrackingListQuery($request, $input, $search);

        return \DataTables::eloquent($customerInvoiceTracking)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('customerInvoiceTrackingID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getBatchSubmissionFormData(Request $request){
        $companyId = $request['companyId'];
        $isCreate = $request['isCreate'];
        $output['customer'] = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName"))
            ->whereHas('customer_master',function($q){
                $q->where('isCustomerActive',1);
            })     
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));
        $output['companyFinanceYear'] = Helper::companyFinanceYear($companyId, 1);
         $company = Company::select('CompanyName', 'CompanyID', 'companySystemID','reportingCurrency')->where('companySystemID', $companyId)->first();

        if(!empty($company)){
            $output['companyRptCurrency'] = $company->reportingcurrency;
        }else{
            $output['companyRptCurrency'] = null;
        }
        $output['company'] = $company;
        $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companyId)->get();
        $output['approvalType'] = ClientPerformaAppType::all();

        $financeYear = isset($output['companyFinanceYear'][0]['companyFinanceYearID'])?$output['companyFinanceYear'][0]['companyFinanceYearID']:'';
        if($isCreate && $financeYear){
            $output['customerInvoiceTrackingCode'] = $this->nextBSCode($companyId,$output['companyFinanceYear'][0]);
        }

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function getContractServiceLine(Request $request){
        $companyId = $request['companyId'];
        $customerID = $request['customerID'];
        $output['contracts'] = Contract::where('companySystemID', $companyId)->where('clientID', $customerID)->get();
        $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companyId)->approved()->withAssigned($companyId)
                                        ->whereIn('serviceLineSystemID', function($q) use($companyId,$customerID){
                                            $q->select('serviceLineSystemID')->from('contractmaster')->where('companySystemID', $companyId)->where('clientID', $customerID);
                                        })->get();
        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function nextBSCode($companySystemID,$financeYear)
    {
        $compny = Company::find($companySystemID);
        $compnyID = $compny->CompanyID;

        // get last serial number by company
        $lastSerial = CustomerInvoiceTracking::Where('companySystemID' , $companySystemID)
            ->where('companyFinanceYearID', $financeYear['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if (!empty($lastSerial)) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $serialNo = ('BS' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
        $code = $compnyID.'/'.date('Y', strtotime($financeYear['bigginingDate'])).'/'.$serialNo;

        return $code;
    }

    public function getCustomerInvoicesForBatchSubmission(Request $request){

        $input = $request->all();
        $search = $request->input('search.value');

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $batchSubmission = CustomerInvoiceTracking::find($input['customerInvoiceTrackingID']);
        $approvalType = $batchSubmission->approvalType;
        $where = '';
        if($approvalType){
            $where = ' AND performamaster.clientAppPerformaType = '.$approvalType;
        }
        if(empty($batchSubmission)){
            return $this->sendError(trans('custom.customer_invoice_tracking_not_found_1'));
        }

        $companySystemID = $batchSubmission->companySystemID;
        $contractUID = $batchSubmission->contractUID;
        $customerID = $batchSubmission->customerID;



        $sql = "SELECT
                companyID,
                custInvoiceDirectID,
                bookingInvCode,
                bookingDate,
                customerInvoiceNo,
                customerInvoiceDate,
                invoiceDueDate,
                clientContractID,
                performaMasterID,
                wanNo,
                wellNo,
                netWorkNo,
                PONumber,
                regNo,
                sum( wellAmount ) as wellAmount
            FROM
                (
                    (
                    SELECT
                        erp_custinvoicedirectdet.companyID,
                        erp_custinvoicedirectdet.custInvoiceDirectID,
                        erp_custinvoicedirect.bookingInvCode,
                        erp_custinvoicedirect.bookingDate,
                        erp_custinvoicedirect.customerInvoiceNo,
                        erp_custinvoicedirect.customerInvoiceDate,
                        erp_custinvoicedirect.invoiceDueDate,
                        erp_custinvoicedirectdet.clientContractID,
                        erp_custinvoicedirectdet.performaMasterID,
                        performa_service_entry_wellgroup.SEno AS wanNo,
                        performa_service_entry_wellgroup.wellNo,
                        performa_service_entry_wellgroup.netWorkNo,
                        erp_custinvoicedirect.PONumber,
                        CONCAT( rigmaster.RigDescription, ':', ticketmaster.regNo ) AS regNo,
                        performa_service_entry_wellgroup.wellAmount 
                    FROM
                        erp_custinvoicedirectdet
                        INNER JOIN erp_custinvoicedirect ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custinvoicedirectdet.custInvoiceDirectID
                        LEFT JOIN performamaster ON performamaster.PerformaInvoiceNo = erp_custinvoicedirectdet.performaMasterID 
                        AND erp_custinvoicedirect.companyID = performamaster.companyID 
                        AND erp_custinvoicedirectdet.customerID = performamaster.customerSystemID 
                        AND performamaster.contractID = erp_custinvoicedirectdet.clientContractID
                        LEFT JOIN performa_service_entry_wellgroup ON performa_service_entry_wellgroup.performaMasID = performamaster.PerformaMasterID
                        LEFT JOIN ticketmaster ON ticketmaster.companyID = erp_custinvoicedirectdet.companyID 
                        AND ticketmaster.clientSystemID = erp_custinvoicedirectdet.customerID 
                        AND ticketmaster.ticketidAtuto = performa_service_entry_wellgroup.ticketNo
                        LEFT JOIN rigmaster ON rigmaster.idrigmaster = ticketmaster.regName 
                        AND rigmaster.companyID = ticketmaster.companyID 
                    WHERE
                        erp_custinvoicedirect.companySystemID = $companySystemID 
                        AND erp_custinvoicedirect.isPerforma != '2'
                        AND erp_custinvoicedirectdet.contractID=$contractUID
                        AND erp_custinvoicedirectdet.customerID=$customerID
                        AND erp_custinvoicedirectdet.performaMasterID > 0 
                        AND erp_custinvoicedirect.selectedForTracking = 0 
                        AND erp_custinvoicedirect.confirmedYN = 1 
                        AND erp_custinvoicedirect.approved = 0 
                        AND erp_custinvoicedirect.canceledYN = 0 
                        $where
                    GROUP BY
                        performa_service_entry_wellgroup.performaMasID,
                        performa_service_entry_wellgroup.SEno,
                        performa_service_entry_wellgroup.netWorkNo,
                        performa_service_entry_wellgroup.wellAmount 
                    ) UNION
                    (
                    SELECT
                        erp_custinvoicedirectdet.companyID,
                        erp_custinvoicedirectdet.custInvoiceDirectID,
                        erp_custinvoicedirect.bookingInvCode,
                        erp_custinvoicedirect.bookingDate,
                        erp_custinvoicedirect.customerInvoiceNo,
                        erp_custinvoicedirect.customerInvoiceDate,
                        erp_custinvoicedirect.invoiceDueDate,
                        erp_custinvoicedirectdet.clientContractID,
                        erp_custinvoicedirectdet.performaMasterID,
                        \"\" AS wanNo,
                        \"\" AS wellNo,
                        \"\" AS netWorkNo,
                        erp_custinvoicedirect.PONumber,
                        \"\" AS regNo,
                        sum( erp_custinvoicedirectdet.invoiceAmount ) AS wellAmount 
                    FROM
                        erp_custinvoicedirectdet
                        INNER JOIN erp_custinvoicedirect ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custinvoicedirectdet.custInvoiceDirectID 
                        LEFT JOIN performamaster ON performamaster.PerformaInvoiceNo = erp_custinvoicedirectdet.performaMasterID 
                    WHERE
                        erp_custinvoicedirect.companySystemID = $companySystemID 
                        AND erp_custinvoicedirect.isPerforma != '2'
                        AND erp_custinvoicedirectdet.contractID=$contractUID
                        AND erp_custinvoicedirectdet.customerID=$customerID
                        AND erp_custinvoicedirect.selectedForTracking = 0 
                        AND erp_custinvoicedirectdet.performaMasterID = 0 
                        AND erp_custinvoicedirect.confirmedYN = 1 
                        AND erp_custinvoicedirect.approved = 0 
                        AND erp_custinvoicedirect.canceledYN = 0 
                        $where
                    GROUP BY
                        erp_custinvoicedirectdet.companyID,
                        erp_custinvoicedirectdet.custInvoiceDirectID,
                        erp_custinvoicedirect.bookingInvCode 
                    ) 
                ) AS final 
            GROUP BY
                clientContractID,
                performaMasterID,
                wanNo,
                wellNo,
                netWorkNo,
                PONumber";

        $output = DB::select($sql);
        $request->request->remove('search.value');
        $col[0] = $input['order'][0]['column'];
        $col[1] = $input['order'][0]['dir'];
        $request->request->remove('order');
        $data['order'] = [];
        $data['search']['value'] = '';
        $request->merge($data);
        return \DataTables::of($output)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function updateMasterPayment($id){

        if($id>0){
            $master = CustomerInvoiceTracking::find($id);

            if(!empty($master)){

                $details = CustomerInvoiceTrackingDetail::where('customerInvoiceTrackingID',$id)->get();
                $approved_amount = $details->sum('approvedAmount');
                $rejected_amount = $details->sum('rejectedAmount');
                $amount = $details->sum('amount');

                CustomerInvoiceTracking::where('customerInvoiceTrackingID',$id)->update(
                    [
                        'totalApprovedAmount' => $approved_amount,
                        'totalRejectedAmount' => $rejected_amount,
                        'totalBatchAmount' => $amount,
                    ]
                );

            }
        }
    }

    public function getBatchSubmissionDetailsPrintPDF(Request $request){

        $id = $request->get('id');
        $batchSubmission =  CustomerInvoiceTracking::find($id);

        if (empty($batchSubmission)) {
            return $this->sendError(trans('custom.batch_submission_not_found'));
        }

        $batchSubmission = CustomerInvoiceTracking::where('customerInvoiceTrackingID',$id)
            ->with(['company','detail' => function($q){
                $q->with(['customer_invoice_direct']);
            }])->first();

        if (empty($batchSubmission)) {
            return $this->sendError(trans('custom.batch_submission_details_not_found'));
        }

        $company = Company::select('CompanyName', 'CompanyID', 'companySystemID','reportingCurrency')
            ->where('companySystemID', $batchSubmission->companySystemID)
            ->first();

        $companyRptCurrency = 'USD';
        if(!empty($company) && $company->reportingcurrency){
            $companyRptCurrency= $company->reportingcurrency->CurrencyCode;
        }

        $order = ['masterdata'=>$batchSubmission,'currencyCode' => $companyRptCurrency];
        $time = strtotime("now");
        $fileName = 'batch_submission_detail_' . $id . '_' . $time . '.pdf';
        $html = view('print.batch_submission', $order);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream($fileName);

    }


    public function exportBatchSubmissionDetails(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');

        $batchSubmission =  CustomerInvoiceTracking::find($id);

        if (empty($batchSubmission)) {
            return $this->sendError(trans('custom.batch_submission_not_found'));
        }


        $data = array();
        $output = CustomerInvoiceTrackingDetail::where('customerInvoiceTrackingID',$id)
            ->with(['customer_invoice_direct','master'=>function($q){
                $q->with('approval_type');
            }])->get();

        if (empty($output)) {
            return $this->sendError(trans('custom.batch_submission_details_not_found'));
        }


        $company = Company::select('CompanyName', 'CompanyID', 'companySystemID','reportingCurrency')
                            ->where('companySystemID', $batchSubmission->companySystemID)
                            ->first();

        $companyRptCurrency = 'USD';
        if(!empty($company) && $company->reportingcurrency){
            $companyRptCurrency= $company->reportingcurrency->CurrencyCode;
        }

        if (!empty($output)) {
            $x = 0;
            foreach ($output as $value) {
                $data[$x]['PO Number'] = $value->PONumber;
                $data[$x]['Sap SE'] = $value->wanNO;
                $data[$x]['Rig'] = $value->rigNo;
                $data[$x]['WellNo'] = $value->wellNo;
                $data[$x]['Booking Inv Code'] = $value->bookingInvCode;
                $data[$x]['Customer Invoice Date'] = \Helper::dateFormat($value->bookingDate);
                $data[$x]['Rental Start Date'] = isset($value->customer_invoice_direct->serviceStartDate)?\Helper::dateFormat($value->customer_invoice_direct->serviceStartDate):'';
                $data[$x]['Rental End Date'] = isset($value->customer_invoice_direct->serviceEndDate)?\Helper::dateFormat($value->customer_invoice_direct->serviceEndDate):'';
                $data[$x]['Month Service Formation'] = $value->servicePeriod;
                $data[$x]['Amount ('.$companyRptCurrency.')'] = number_format($value->amount, 2);
                $data[$x]['Description'] = isset($value->master->approval_type->description)?$value->master->approval_type->description:'';
                $x++;
            }
        }

         \Excel::create('batch_submission_detail', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:K' . $lastrow)->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('A1:K1')->getFont()->setBold( true );
        })->download($type);

        return $this->sendResponse(array(), trans('custom.success_export'));
    }

    public function getINVTrackingFormData(Request $request){
        $companyId = $request['companyId'];

//        $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companyId)->get();

        $output['years'] = Year::orderBy('year', 'desc')->get();

        $output['months'] = Months::all();

        $output['customers'] = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName"))
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function updateAllInvoiceTrackingDetail(Request $request){
        $input = $request->all();
        $type = isset($input['type']) ? $input['type'] : 0;
        $employee = Helper::getEmployeeInfo();
        switch ($type){
            case 1:
                $validator = \Validator::make($input, [
                    'customerInvoiceTrackingID' => 'required|numeric|min:1',
                    'customerApprovedByDate' => 'required',
                ]);
                if(isset($input['customerApprovedByDate'])){
                    $input['customerApprovedByDate'] = Carbon::parse($input['customerApprovedByDate'])->format('Y-m-d H:i:s');
                }
                $update = [
                    'approvedAmount' =>DB::raw( 'amount' ),
                    'rejectedAmount'=>0,
                    'customerRejectedYN'=>0,
                    'customerApprovedYN'=>-1,
                    'customerApprovedByDate'=>$input['customerApprovedByDate'],
                    'customerApprovedDate'=>$input['customerApprovedByDate'],
                    'customerApprovedByEmpID'=>$employee->empID,
                    'customerApprovedByEmpSystemID'=>$employee->employeeSystemID,
                    'customerApprovedByEmpName'=>$employee->empName
                ];
                break;

            case 2:
                $validator = \Validator::make($input, [
                    'customerInvoiceTrackingID' => 'required|numeric|min:1',
                    'customerRejectedByDate' => 'required',
                ]);
                $update = [
                    'approvedAmount' =>0,
                    'rejectedAmount'=>DB::raw( 'amount' ),
                    'customerRejectedYN'=>-1,
                    'customerApprovedYN'=>0,
                    'customerRejectedByDate'=>$input['customerRejectedByDate'],
                    'customerRejectedDate'=>$input['customerRejectedByDate'],
                    'customerApprovedByEmpID'=>$employee->empID,
                    'customerApprovedByEmpSystemID'=>$employee->employeeSystemID,
                    'customerApprovedByEmpName'=>$employee->empName
                ];
                break;

            case 3:
                $validator = \Validator::make($input, [
                    'customerInvoiceTrackingID' => 'required|numeric|min:1',
                ]);
                $update = ['customerApprovedYN' => 0,'approvedAmount' => 0,'customerRejectedYN' => 0,'rejectedAmount' => 0];
                break;

            case 5:
                $validator = \Validator::make($input, [
                    'customerInvoiceTrackingID' => 'required|numeric|min:1',
                    'remarks' => 'required',
                ]);
                $update = ['remarks' => $input['remarks']];
                break;

            default:
                return $this->sendError(trans('custom.type_not_found'), 422);
                break;

        }

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $master = CustomerInvoiceTracking::find($input['customerInvoiceTrackingID']);

        if(empty($master)){
            return $this->sendError('Customer Invoice Tracking Data Found',500);
        }

        $details = CustomerInvoiceTrackingDetail::where('customerInvoiceTrackingID',$input['customerInvoiceTrackingID'])->get();
        if(empty($details)){
            return $this->sendError('Customer Invoice Tracking Detail Found',500);
        }

        $isUpdate = CustomerInvoiceTrackingDetail::where('customerInvoiceTrackingID',$input['customerInvoiceTrackingID'])->update($update);

        if($isUpdate){
            $this->updateMasterPayment($input['customerInvoiceTrackingID']);
            return $this->sendResponse($isUpdate,trans('custom.successfully_updated'));
        }
        return $this->sendError(trans('custom.error_occured_1'), 500);
    }

    public function deleteAllInvoiceTrackingDetail(Request $request){
        $input = $request->all();
        $masterID = isset($input['customerInvoiceTrackingID']) ? $input['customerInvoiceTrackingID'] : 0;
        if (!$masterID){
            return $this->sendError(trans('custom.customer_invoice_tracking_id_not_found'),500);
        }

        $master = CustomerInvoiceTracking::find($masterID);

        if(empty($master)){
            return $this->sendError('Customer Invoice Tracking Data Found',500);
        }

        $details = CustomerInvoiceTrackingDetail::where('customerInvoiceTrackingID',$masterID)->get();
        if(empty($details)){
            return $this->sendError('Customer Invoice Tracking Data Found',500);
        }

        foreach ($details as $detail){
            CustomerInvoiceDirect::find($detail['custInvoiceDirectAutoID'])->update(['selectedForTracking' => 0,'customerInvoiceTrackingID' => null]);
        }

        $isDelete = CustomerInvoiceTrackingDetail::where('customerInvoiceTrackingID',$masterID)->delete();

        if($isDelete){
            $this->updateMasterPayment($masterID);
            return $this->sendResponse([],trans('custom.all_customer_invoice_tracking_details_deleted_succ'));
        }
        return $this->sendError(trans('custom.error_in_delete_process'),500);

    }


}
