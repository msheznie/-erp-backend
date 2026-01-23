<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateCustomerInvoiceTrackingDetailAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceTrackingDetailAPIRequest;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceTracking;
use App\Models\CustomerInvoiceTrackingDetail;
use App\Repositories\CustomerInvoiceTrackingDetailRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Arr;

/**
 * Class CustomerInvoiceTrackingDetailController
 * @package App\Http\Controllers\API
 */

class CustomerInvoiceTrackingDetailAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceTrackingDetailRepository */
    private $customerInvoiceTrackingDetailRepository;

    public function __construct(CustomerInvoiceTrackingDetailRepository $customerInvoiceTrackingDetailRepo)
    {
        $this->customerInvoiceTrackingDetailRepository = $customerInvoiceTrackingDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceTrackingDetails",
     *      summary="Get a listing of the CustomerInvoiceTrackingDetails.",
     *      tags={"CustomerInvoiceTrackingDetail"},
     *      description="Get all CustomerInvoiceTrackingDetails",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceTrackingDetail")
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
        $this->customerInvoiceTrackingDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceTrackingDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceTrackingDetails = $this->customerInvoiceTrackingDetailRepository->all();

        return $this->sendResponse($customerInvoiceTrackingDetails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice_tracking_details')]));
    }

    /**
     * @param CreateCustomerInvoiceTrackingDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceTrackingDetails",
     *      summary="Store a newly created CustomerInvoiceTrackingDetail in storage",
     *      tags={"CustomerInvoiceTrackingDetail"},
     *      description="Store CustomerInvoiceTrackingDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceTrackingDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceTrackingDetail")
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
     *                  ref="#/definitions/CustomerInvoiceTrackingDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceTrackingDetailAPIRequest $request)
    {
        $input = $request->all();

        $customerInvoiceTrackingDetail = $this->customerInvoiceTrackingDetailRepository->create($input);

        return $this->sendResponse($customerInvoiceTrackingDetail->toArray(), trans('custom.save', ['attribute' => trans('custom.customer_invoice_tracking_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceTrackingDetails/{id}",
     *      summary="Display the specified CustomerInvoiceTrackingDetail",
     *      tags={"CustomerInvoiceTrackingDetail"},
     *      description="Get CustomerInvoiceTrackingDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceTrackingDetail",
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
     *                  ref="#/definitions/CustomerInvoiceTrackingDetail"
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
        /** @var CustomerInvoiceTrackingDetail $customerInvoiceTrackingDetail */
        $customerInvoiceTrackingDetail = $this->customerInvoiceTrackingDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceTrackingDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_tracking_details')]));
        }

        return $this->sendResponse($customerInvoiceTrackingDetail->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice_tracking_details')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceTrackingDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceTrackingDetails/{id}",
     *      summary="Update the specified CustomerInvoiceTrackingDetail in storage",
     *      tags={"CustomerInvoiceTrackingDetail"},
     *      description="Update CustomerInvoiceTrackingDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceTrackingDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceTrackingDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceTrackingDetail")
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
     *                  ref="#/definitions/CustomerInvoiceTrackingDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceTrackingDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = Arr::except($input, ['approved_by','rejected_by']);
        if(isset($input['customerApprovedByDate'])){
            $input['customerApprovedByDate'] = Carbon::parse($input['customerApprovedByDate'])->format('Y-m-d H:i:s');
            $input['customerApprovedDate'] = $input['customerApprovedByDate'];
        }

        if(isset($input['customerRejectedByDate'])){
            $input['customerRejectedByDate'] = Carbon::parse($input['customerRejectedByDate'])->format('Y-m-d H:i:s');
            $input['customerRejectedDate'] = $input['customerRejectedByDate'];
        }

        if($input['customerRejectedYN'] && $input['customerRejectedYN'] == -1){

        }

        $employee = Helper::getEmployeeInfo();

        if($input['customerApprovedYN'] && $input['customerApprovedYN'] == -1){
            $input['customerApprovedByEmpID']=$employee->empID;
            $input['customerApprovedByEmpSystemID']=$employee->employeeSystemID;
            $input['customerApprovedByEmpName']=$employee->empName;
        }

        if($input['customerRejectedYN'] && $input['customerRejectedYN'] == -1){
            $input['customerRejectedByEmpID']=$employee->empID;
            $input['customerRejectedByEmpSystemID']=$employee->employeeSystemID;
            $input['customerRejectedByEmpName']=$employee->empName;
        }

        /** @var CustomerInvoiceTrackingDetail $customerInvoiceTrackingDetail */
        $customerInvoiceTrackingDetail = $this->customerInvoiceTrackingDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceTrackingDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_tracking_details')]));
        }

        $customerInvoiceTrackingDetail = $this->customerInvoiceTrackingDetailRepository->update($input, $id);
        $this->updateMasterPayment($customerInvoiceTrackingDetail->customerInvoiceTrackingID);
        return $this->sendResponse($customerInvoiceTrackingDetail->toArray(), trans('custom.update', ['attribute' => trans('custom.customer_invoice_tracking_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceTrackingDetails/{id}",
     *      summary="Remove the specified CustomerInvoiceTrackingDetail from storage",
     *      tags={"CustomerInvoiceTrackingDetail"},
     *      description="Delete CustomerInvoiceTrackingDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceTrackingDetail",
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
        /** @var CustomerInvoiceTrackingDetail $customerInvoiceTrackingDetail */
        $customerInvoiceTrackingDetail = $this->customerInvoiceTrackingDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceTrackingDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_tracking_details')]));
        }
        $master_id = $customerInvoiceTrackingDetail->customerInvoiceTrackingID;
        CustomerInvoiceDirect::find($customerInvoiceTrackingDetail->custInvoiceDirectAutoID)->update(['selectedForTracking' => 0,'customerInvoiceTrackingID' => null]);
        $customerInvoiceTrackingDetail->delete();
        $this->updateMasterPayment($master_id);

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.customer_invoice_tracking_details')]));
    }

    public function addBatchSubmitDetails(Request $request)
    {
        $input = $request->all();

        $customerInvoiceTrackingID = $input['customerInvoiceTrackingID'];

        $masterData = CustomerInvoiceTracking::find($customerInvoiceTrackingID);

        $itemExistArray = array();
        //check invoice all ready exist
        foreach ($input['detailTable'] as $itemExist) {

            if (isset($itemExist['isChecked']) && $itemExist['isChecked']) {
                $siDetailExist = CustomerInvoiceTrackingDetail::where('customerInvoiceTrackingID', $customerInvoiceTrackingID)
                    ->where('companyID', $itemExist['companyID'])
                    ->where('bookingInvCode', $itemExist['bookingInvCode'])
                    ->first();

                if (!empty($siDetailExist)) {
                    $itemDrt = "Selected Invoice " . $itemExist['bookingInvCode'] . " is all ready added. Please check again";
                    $itemExistArray[] = [$itemDrt];
                }
            }
        }


        if (!empty($itemExistArray)) {
            return $this->sendError($itemExistArray, 422);
        }
        DB::beginTransaction();
        try {
            $total_amount = 0;
            foreach ($input['detailTable'] as $new) {
                if (isset($new['isChecked']) && $new['isChecked']) {
                    $tempArray = $new;
                    $tempArray["customerInvoiceTrackingID"] = $masterData->customerInvoiceTrackingID;
                    $tempArray["companyID"] = $new['companyID'];
                    $tempArray["customerID"] = $masterData->customerID;
                    $tempArray["custInvoiceDirectAutoID"] = $new['custInvoiceDirectID'];
                    $tempArray["bookingInvCode"] = $new['bookingInvCode'];
                    $tempArray["bookingDate"] = $new['bookingDate'];
                    $tempArray["customerInvoiceNo"] = $new['customerInvoiceNo'];
                    $tempArray["customerInvoiceDate"] = $new['customerInvoiceDate'];
                    $tempArray["invoiceDueDate"] = $new['invoiceDueDate'];
                    $tempArray["contractID"] = $new['clientContractID'];
                    $tempArray["PerformaInvoiceNo"] = $new['performaMasterID'];
                    $tempArray["wanNO"] = $new['wanNo'];
                    $tempArray["PONumber"] = $new['PONumber'];
                    $tempArray["rigNo"] = $new['regNo'];
                    $tempArray["wellNo"] = $new['wellNo'];
                    $tempArray["amount"] = $new['wellAmount'];

                    unset($tempArray['isChecked']);

                    $total_amount += $tempArray["wellAmount"];

                    if ($tempArray) {

                       $this->customerInvoiceTrackingDetailRepository->create($tempArray);
                       CustomerInvoiceDirect::find($new['custInvoiceDirectID'])
                            ->update(['selectedForTracking' => -1, 'customerInvoiceTrackingID' => $masterData->customerInvoiceTrackingID]);
                    }
                }
            }
            $current_total = $masterData->totalBatchAmount;
            $total_amount = $total_amount+$current_total;
            $masterData->update(['totalBatchAmount' => $total_amount]);
            DB::commit();
            return $this->sendResponse('', trans('custom.retrieve', ['attribute' => trans('custom.details')]));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getLine().' - '. $exception->getMessage());
        }

    }


    public function getItemsByBatchSubmission(Request $request){
        $input = $request->all();
        $customerInvoiceTrackingID = $input['customerInvoiceTrackingID'];

        $items = CustomerInvoiceTrackingDetail::where('customerInvoiceTrackingID', $customerInvoiceTrackingID)
            ->with(['approved_by', 'rejected_by'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.request_details')]));
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
}
