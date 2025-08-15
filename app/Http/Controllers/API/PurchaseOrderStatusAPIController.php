<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderStatusAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  PurchaseOrderStatus
 * -- Author : Mohamed Fayas
 * -- Create date : 30- May 2018
 * -- Description : This file contains the all CRUD for PurchaseOrderStatus
 * -- REVISION HISTORY
 *  Date: 30-May 2018 By: Fayas Description: Added new functions named as getAllStatusByPurchaseOrder()
 *  Date: 31-May 2018 By: Fayas Description: Added new functions named as destroyPreCheck()
 *  Date: 05-June 2018 By: Fayas Description: Added new functions named as reportOrderStatus(),purchaseOrderStatusesSendEmail(),reportOrderStatusFilterOptions()
 *  Date: 06-June 2018 By: Fayas Description: Added new functions named as reportOrderStatusPreCheck(),exportReportOrderStatus()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseOrderStatusAPIRequest;
use App\Http\Requests\API\UpdatePurchaseOrderStatusAPIRequest;
use App\Models\Company;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderStatus;
use App\Models\SegmentMaster;
use App\Models\SupplierMaster;
use App\Providers\AuthServiceProvider;
use App\Repositories\PurchaseOrderStatusRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PurchaseOrderStatusController
 * @package App\Http\Controllers\API
 */
class PurchaseOrderStatusAPIController extends AppBaseController
{
    /** @var  PurchaseOrderStatusRepository */
    private $purchaseOrderStatusRepository;

    public function __construct(PurchaseOrderStatusRepository $purchaseOrderStatusRepo)
    {
        $this->purchaseOrderStatusRepository = $purchaseOrderStatusRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseOrderStatuses",
     *      summary="Get a listing of the PurchaseOrderStatuses.",
     *      tags={"PurchaseOrderStatus"},
     *      description="Get all PurchaseOrderStatuses",
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
     *                  @SWG\Items(ref="#/definitions/PurchaseOrderStatus")
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
        $this->purchaseOrderStatusRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseOrderStatusRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseOrderStatuses = $this->purchaseOrderStatusRepository->all();

        return $this->sendResponse($purchaseOrderStatuses->toArray(), 'Purchase Order Statuses retrieved successfully');
    }

    /**
     * Display all status by specified Procument Order.
     * GET|HEAD /getAllStatusByPurchaseOrder
     *
     * @param  $request
     *
     * @return Response
     */

    public function getAllStatusByPurchaseOrder(Request $request)
    {

        $input = $request->all();

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $input['purchaseOrderID'])->first();
        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $procumentOrderStatus = PurchaseOrderStatus::where('purchaseOrderID', $input['purchaseOrderID'])
            ->with(['category'])
            ->orderBy('POStatusID', 'desc')
            ->paginate($input['itemPerPage']);


        return $this->sendResponse($procumentOrderStatus, 'Procurement Order retrieved successfully');
    }

    /**
     * @param CreatePurchaseOrderStatusAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/purchaseOrderStatuses",
     *      summary="Store a newly created PurchaseOrderStatus in storage",
     *      tags={"PurchaseOrderStatus"},
     *      description="Store PurchaseOrderStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseOrderStatus that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseOrderStatus")
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
     *                  ref="#/definitions/PurchaseOrderStatus"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePurchaseOrderStatusAPIRequest $request)
    {
        $input = $request->all();

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $input['purchaseOrderID'])->first();
        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $input['purchaseOrderCode'] = $purchaseOrder->purchaseOrderCode;
        $employee = \Helper::getEmployeeInfo();

        $input['updatedByEmpSystemID'] = $employee->employeeSystemID;
        $input['updatedByEmpID'] = $employee->empID;
        $input['updatedByEmpName'] = $employee->empName;

        $purchaseOrderStatuses = $this->purchaseOrderStatusRepository->create($input);

        return $this->sendResponse($purchaseOrderStatuses->toArray(), 'Purchase Order Status saved successfully');
    }


    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseOrderStatuses/{id}",
     *      summary="Display the specified PurchaseOrderStatus",
     *      tags={"PurchaseOrderStatus"},
     *      description="Get PurchaseOrderStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderStatus",
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
     *                  ref="#/definitions/PurchaseOrderStatus"
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
        /** @var PurchaseOrderStatus $purchaseOrderStatus */
        $purchaseOrderStatus = $this->purchaseOrderStatusRepository->findWithoutFail($id);

        if (empty($purchaseOrderStatus)) {
            return $this->sendError('Purchase Order Status not found');
        }

        return $this->sendResponse($purchaseOrderStatus->toArray(), 'Purchase Order Status retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePurchaseOrderStatusAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/purchaseOrderStatuses/{id}",
     *      summary="Update the specified PurchaseOrderStatus in storage",
     *      tags={"PurchaseOrderStatus"},
     *      description="Update PurchaseOrderStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderStatus",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseOrderStatus that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseOrderStatus")
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
     *                  ref="#/definitions/PurchaseOrderStatus"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePurchaseOrderStatusAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['category']);
        $input = $this->convertArrayToValue($input);

        /** @var PurchaseOrderStatus $purchaseOrderStatus */
        $purchaseOrderStatus = $this->purchaseOrderStatusRepository->findWithoutFail($id);

        if (empty($purchaseOrderStatus)) {
            return $this->sendError('Purchase Order Status not found');
        }

        $purchaseOrderStatus = $this->purchaseOrderStatusRepository->update($input, $id);

        return $this->sendResponse($purchaseOrderStatus->toArray(), 'PurchaseOrderStatus updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/purchaseOrderStatuses/{id}",
     *      summary="Remove the specified PurchaseOrderStatus from storage",
     *      tags={"PurchaseOrderStatus"},
     *      description="Delete PurchaseOrderStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderStatus",
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
        $employee = \Helper::getEmployeeInfo();

        /** @var PurchaseOrderStatus $purchaseOrderStatus */
        $purchaseOrderStatus = $this->purchaseOrderStatusRepository->findWithoutFail($id);

        if (empty($purchaseOrderStatus)) {
            return $this->sendError('Purchase Order Status not found');
        }

        if ($employee->employeeSystemID != $purchaseOrderStatus->updatedByEmpSystemID) {
            return $this->sendError('You unable to delete this status', 500);
        }

        $purchaseOrderStatus->delete();

        return $this->sendResponse($id, 'Purchase Order Status deleted successfully');
    }

    /**
     * destroy pre check.
     * GET|HEAD /destroyPreCheck
     *
     * @param  $request
     *
     * @return Response
     */

    public function destroyPreCheck(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');
        $employee = \Helper::getEmployeeInfo();
        $errorMessage = "Something went wrong. Please contact system administrator";

        /** @var PurchaseOrderStatus $purchaseOrderStatus */
        $purchaseOrderStatus = $this->purchaseOrderStatusRepository->findWithoutFail($id);

        if (empty($purchaseOrderStatus)) {
            return $this->sendError('Purchase Order Status not found');
        }

        if ($employee->employeeSystemID != $purchaseOrderStatus->updatedByEmpSystemID) {

            if ($type == 1) {
                $errorMessage = "You unable to edit this status";
            } else if ($type == 2) {
                $errorMessage = "You unable to delete this status";
            } else if ($type == 3) {
                $errorMessage = "You unable to send emails";
            }

            return $this->sendError($errorMessage, 500);
        }

        return $this->sendResponse($id, 'Purchase Order Status deleted successfully');
    }

    /**
     * purchase order statuses send Emails to approved users
     * POST|HEAD /purchaseOrderStatusesSendEmail
     *
     * @param  $request
     *
     * @return Response
     */

    public function purchaseOrderStatusesSendEmail(Request $request)
    {
        $id = $request->get('POStatusID');
        $type = $request->get('type');
        $employee = \Helper::getEmployeeInfo();
        $errorMessage = "Something went wrong. Please contact system administrator";

        /** @var PurchaseOrderStatus $purchaseOrderStatus */
        $purchaseOrderStatus = $this->purchaseOrderStatusRepository->findWithoutFail($id);

        if (empty($purchaseOrderStatus)) {
            return $this->sendError('Purchase Order Status not found');
        }

        if ($employee->employeeSystemID != $purchaseOrderStatus->updatedByEmpSystemID) {

            if ($type == 1) {
                $errorMessage = "You unable to edit this status";
            } else if ($type == 2) {
                $errorMessage = "You unable to delete this status";
            } else if ($type == 3) {
                $errorMessage = "You unable to send emails";
            }

            return $this->sendError($errorMessage, 500);
        }

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderStatus->purchaseOrderID)->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $statusCategory = PurchaseOrderStatus::where('POCategoryID', $purchaseOrderStatus->POCategoryID)->first();

        $emails = array();
        $document = DocumentMaster::where('documentSystemID', $purchaseOrder->documentSystemID)->first();

        $emailBody = $document->documentDescription . ' <b>' . $purchaseOrder->purchaseOrderCode . '</b>';
        $emailSubject = $document->documentDescription . ' ' . $purchaseOrder->purchaseOrderCode;

        $body = '<p>' . $emailBody . '  is updated with a new status by ' . $purchaseOrderStatus->updatedByEmpName . '.</p><p>Status : ' . $statusCategory->description . '</p><p>Comment : ' . $purchaseOrderStatus->comments . '</p>';
        $subject = $emailSubject . ' is updated with a new status';

        if ($purchaseOrder->poConfirmedYN == 1) {
            $emails[] = array('empSystemID' => $purchaseOrder->poConfirmedByEmpSystemID,
                'companySystemID' => $purchaseOrder->companySystemID,
                'docSystemID' => $purchaseOrder->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $purchaseOrder->purchaseOrderID);
        }

        $documentApproval = DocumentApproved::where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemCode', $purchaseOrder->purchaseOrderID)
            ->where('documentSystemID', $purchaseOrder->documentSystemID)
            ->where('approvedYN', -1)
            ->get();

        foreach ($documentApproval as $da) {
            $emails[] = array('empSystemID' => $da->employeeSystemID,
                'companySystemID' => $purchaseOrder->companySystemID,
                'docSystemID' => $purchaseOrder->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $purchaseOrder->purchaseOrderID);
        }

        $sendEmail = \Email::sendEmail($emails);
        if (!$sendEmail["success"]) {
            return $this->sendError($sendEmail["message"], 500);
        }

        return $this->sendResponse($id, 'Purchase Order Status deleted successfully');
    }

    /**
     * report Order Status
     * POST|HEAD /reportOrderStatus
     *
     * @param  $request
     *
     * @return Response
     */
    public function reportOrderStatus(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        if($input['selectedStatus'] == 1){
            $grvStatus = [0];
        }else if($input['selectedStatus'] == 2){
            $grvStatus = [1];
        }else{
            $grvStatus = [0,1];
        }

        $purchaseOrders = ProcumentOrder::whereIn('companySystemID', $subCompanies)
            ->where('approved', -1)
            ->where('poCancelledYN', 0)
            ->whereIn('grvRecieved',$grvStatus)
            ->with(['segment','currency','status_one' => function ($q) {
                  $q->with(['category']);
            }, 'supplier' => function ($q) {
                $q->with(['country']);
            }]);

        if (array_key_exists('dateRange', $input)) {
            $from = ((new Carbon($input['dateRange'][0]))->addDays(1)->format('Y-m-d'));
            $to = ((new Carbon($input['dateRange'][1]))->addDays(1)->format('Y-m-d'));

            $purchaseOrders = $purchaseOrders->whereBetween('createdDateTime', [$from, $to]);
        }
        if (array_key_exists('suppliers', $input)) {
            $suppliers = (array)$input['suppliers'];
            $suppliers = collect($suppliers)->pluck('supplierCodeSystem');
            $purchaseOrders = $purchaseOrders->whereIn('supplierID', $suppliers);
        }

        if (array_key_exists('segment', $input)) {
            $segment = (array)$input['segment'];
            $segment = collect($segment)->pluck('serviceLineSystemID');
            $purchaseOrders = $purchaseOrders->whereIn('serviceLineSystemID', $segment);
        }


        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            /*  $procumentOrders = $procumentOrders->where(function ($query) use ($search) {
                  $query->where('purchaseOrderCode', 'LIKE', "%{$search}%")
                      ->orWhere('narration', 'LIKE', "%{$search}%")
                      ->orWhere('supplierName', 'LIKE', "%{$search}%");
              });*/
        }

        return \DataTables::eloquent($purchaseOrders)
            ->addColumn('Actions', 1)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        //$query->orderBy('purchaseOrderID', $input['order'][0]['dir']);
                        $query->orderBy('approvedDate', 'asc');
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * reportOrderStatusPreCheck
     * POST|HEAD /reportOrderStatusPreCheck
     *
     * @param  $request
     *
     * @return Response
     */
    public function reportOrderStatusPreCheck(Request $request)
    {
        $input = $request->all();


        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        if (empty($company)) {
            return $this->sendError('Please select the company', 500);
        }


        if (array_key_exists('dateRange', $input)) {
            $from = ((new Carbon($input['dateRange'][0]))->addDays(1)->format('Y-m-d'));
            $to = ((new Carbon($input['dateRange'][1]))->addDays(1)->format('Y-m-d'));
        } else {
            return $this->sendError('Please select date range', 500);
        }
        if (array_key_exists('suppliers', $input)) {
            $suppliers = (array)$input['suppliers'];
            $suppliers = collect($suppliers)->pluck('supplierCodeSystem');

            if (count($suppliers) == 0) {
                return $this->sendError('Please select the suppliers', 500);
            }

        }

        if (array_key_exists('segment', $input)) {
            $segment = (array)$input['segment'];
            $segment = collect($segment)->pluck('serviceLineSystemID');

            if (count($segment) == 0) {
                return $this->sendError('Please select segments', 500);
            }

        }

        return $this->sendResponse([], 'valid');
    }

    /**
     * reportOrderStatusFilterOptions
     * GET|HEAD /reportOrderStatusFilterOptions
     *
     * @param  $request
     *
     * @return Response
     */
    public function reportOrderStatusFilterOptions(Request $request)
    {

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $companies = Company::whereIn("companySystemID", $subCompanies)
                                ->select('companySystemID', 'CompanyID', 'CompanyName')
                                ->get();

        $filterSuppliers = ProcumentOrder::whereIn('companySystemID', $subCompanies)
                                            ->select('supplierID')
                                            ->groupBy('supplierID')
                                            ->pluck('supplierID');

        $suppliers = SupplierMaster::whereIn('supplierCodeSystem', $filterSuppliers)
                                    ->select(['supplierCodeSystem', 'primarySupplierCode', 'supplierName'])
                                    ->get();

        $segments = SegmentMaster::where("companySystemID", $subCompanies)
                    ->approved()->withAssigned($subCompanies)
                    ->where('isActive', 1)->get();

        $output = array(
            'companies' => $companies,
            'suppliers' => $suppliers,
            'segment' => $segments
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    /**
     * exportReportOrderStatus
     * POST|HEAD /exportReportOrderStatus
     *
     * @param  $request
     *
     * @return Response
     */
    public function exportReportOrderStatus(Request $request)
    {
        $input = $request->all();
        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }
        $type = $input['type'];
        if($input['selectedStatus'] == 1){
            $grvStatus = [0];
        }else if($input['selectedStatus'] == 2){
            $grvStatus = [1];
        }else{
            $grvStatus = [0,1];
        }


        $purchaseOrders = ProcumentOrder::whereIn('companySystemID', $subCompanies)
            ->where('approved', -1)
            ->where('poCancelledYN', 0)
            ->whereIn('grvRecieved',$grvStatus)
            ->with(['supplier','segment', 'currency', 'status_one' => function ($q) {
                $q->with(['category']);
            }, 'supplier' => function ($q) {
                $q->with(['country']);
            }]);

        if (array_key_exists('dateRange', $input)) {
            $from = ((new Carbon($input['dateRange'][0]))->addDays(1)->format('Y-m-d'));
            $to = ((new Carbon($input['dateRange'][1]))->addDays(1)->format('Y-m-d'));

            $purchaseOrders = $purchaseOrders->whereBetween('createdDateTime', [$from, $to]);
        }
        if (array_key_exists('suppliers', $input)) {
            $suppliers = (array)$input['suppliers'];
            $suppliers = collect($suppliers)->pluck('supplierCodeSystem');
            $purchaseOrders = $purchaseOrders->whereIn('supplierID', $suppliers);
        }

        if (array_key_exists('segment', $input)) {
            $segment = (array)$input['segment'];
            $segment = collect($segment)->pluck('serviceLineSystemID');
            $purchaseOrders = $purchaseOrders->whereIn('serviceLineSystemID', $segment);
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            /*  $procumentOrders = $procumentOrders->where(function ($query) use ($search) {
                  $query->where('purchaseOrderCode', 'LIKE', "%{$search}%")
                      ->orWhere('narration', 'LIKE', "%{$search}%")
                      ->orWhere('supplierName', 'LIKE', "%{$search}%");
              });*/
        }

        $purchaseOrders = $purchaseOrders
            ->orderBy('approvedDate', 'asc')
            ->get();
        $data = array();
        foreach ($purchaseOrders as $val) {

            $status = "";
            $countryName = "";
            $currencyName = "";
            $comments = "";
            $grvStatus = "";

            if(!empty($val->status_one)){
                if($val->status_one){
                    $comments  =  $val->status_one->comments;

                    if(!empty($val->status_one->category)){
                        $status = $val->status_one->category->description;
                    }
                }
            }

            if(!empty($val->supplier)){
                if(!empty($val->supplier->country)){
                   $countryName = $val->supplier->country->countryName;
                }
            }

            if(!empty($val->currency)){
                $currencyName = $val->currency->CurrencyName;
            }

            if($val->grvRecieved == 0){
                $grvStatus = "Not Received";
            }
            else if($val->grvRecieved == 1){
                $grvStatus = "Partially Received";
            }


            $data[] = array(
                'Company ID' => $val->companyID,
                'PO Code' => $val->purchaseOrderCode,
                'Segment' => isset($val->segment->ServiceLineDes)?$val->segment->ServiceLineDes:'',
                'Created Date' => \Helper::dateFormat($val->createdDateTime),
                'Approved Date' => \Helper::dateFormat($val->approvedDate),
                'ETA' => \Helper::dateFormat($val->expectedDeliveryDate),
                'Narration' => $val->narration,
                'Supplier Code' => $val->supplierPrimaryCode,
                'Supplier Name' => $val->supplierName,
                'Supplier Country' => $countryName,
                'Currency' => $currencyName,
                'Amount' => $val->poTotalSupplierTransactionCurrency,
                'GRV Status' => $grvStatus,
                'Status' => $status,
                'Comments' => $comments,
            );
        }
         \Excel::create('order_status', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);
        return $this->sendResponse(array(), 'successfully export');
    }

}
