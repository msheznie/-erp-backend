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
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseOrderStatusAPIRequest;
use App\Http\Requests\API\UpdatePurchaseOrderStatusAPIRequest;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderStatus;
use App\Providers\AuthServiceProvider;
use App\Repositories\PurchaseOrderStatusRepository;
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

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID',$input['purchaseOrderID'])->first();
        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $procumentOrderStatus = PurchaseOrderStatus::where('purchaseOrderID',$input['purchaseOrderID'])
                                                          ->with(['category'])
                                                          ->orderBy('POStatusID','desc')
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

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID',$input['purchaseOrderID'])->first();
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

        if( $employee->employeeSystemID != $purchaseOrderStatus->updatedByEmpSystemID){
            return $this->sendError('You unable to delete this status',500);
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
        $errorMessage = "Server Error";

        /** @var PurchaseOrderStatus $purchaseOrderStatus */
        $purchaseOrderStatus = $this->purchaseOrderStatusRepository->findWithoutFail($id);

        if (empty($purchaseOrderStatus)) {
            return $this->sendError('Purchase Order Status not found');
        }

        if($employee->employeeSystemID  != $purchaseOrderStatus->updatedByEmpSystemID){

            if($type == 1){
                $errorMessage = "You unable to edit this status";
            }else if($type == 2){
                $errorMessage = "You unable to delete this status";
            }else if($type == 3){
                $errorMessage = "You unable to send emails";
            }

            return $this->sendError($errorMessage,500);
        }

        return $this->sendResponse($id, 'Purchase Order Status deleted successfully');
    }


}
