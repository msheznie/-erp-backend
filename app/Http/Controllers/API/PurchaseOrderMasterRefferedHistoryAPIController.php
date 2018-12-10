<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderMasterRefferedHistoryAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Order Master Reffered History
 * -- Author : Nazir
 * -- Create date : 24 - July 2018
 * -- Description : This file contains the all CRUD for  Purchase Order Master Reffered History
 * -- REVISION HISTORY
 * -- Date: 24-July 2018 By: Nazir Description: Added new function getPoMasterAmendHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseOrderMasterRefferedHistoryAPIRequest;
use App\Http\Requests\API\UpdatePurchaseOrderMasterRefferedHistoryAPIRequest;
use App\Models\PurchaseOrderMasterRefferedHistory;
use App\Repositories\PurchaseOrderMasterRefferedHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PurchaseOrderMasterRefferedHistoryController
 * @package App\Http\Controllers\API
 */

class PurchaseOrderMasterRefferedHistoryAPIController extends AppBaseController
{
    /** @var  PurchaseOrderMasterRefferedHistoryRepository */
    private $purchaseOrderMasterRefferedHistoryRepository;

    public function __construct(PurchaseOrderMasterRefferedHistoryRepository $purchaseOrderMasterRefferedHistoryRepo)
    {
        $this->purchaseOrderMasterRefferedHistoryRepository = $purchaseOrderMasterRefferedHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseOrderMasterRefferedHistories",
     *      summary="Get a listing of the PurchaseOrderMasterRefferedHistories.",
     *      tags={"PurchaseOrderMasterRefferedHistory"},
     *      description="Get all PurchaseOrderMasterRefferedHistories",
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
     *                  @SWG\Items(ref="#/definitions/PurchaseOrderMasterRefferedHistory")
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
        $this->purchaseOrderMasterRefferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseOrderMasterRefferedHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseOrderMasterRefferedHistories = $this->purchaseOrderMasterRefferedHistoryRepository->all();

        return $this->sendResponse($purchaseOrderMasterRefferedHistories->toArray(), 'Purchase Order Master Reffered Histories retrieved successfully');
    }

    /**
     * @param CreatePurchaseOrderMasterRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/purchaseOrderMasterRefferedHistories",
     *      summary="Store a newly created PurchaseOrderMasterRefferedHistory in storage",
     *      tags={"PurchaseOrderMasterRefferedHistory"},
     *      description="Store PurchaseOrderMasterRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseOrderMasterRefferedHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseOrderMasterRefferedHistory")
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
     *                  ref="#/definitions/PurchaseOrderMasterRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePurchaseOrderMasterRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        $purchaseOrderMasterRefferedHistories = $this->purchaseOrderMasterRefferedHistoryRepository->create($input);

        return $this->sendResponse($purchaseOrderMasterRefferedHistories->toArray(), 'Purchase Order Master Reffered History saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseOrderMasterRefferedHistories/{id}",
     *      summary="Display the specified PurchaseOrderMasterRefferedHistory",
     *      tags={"PurchaseOrderMasterRefferedHistory"},
     *      description="Get PurchaseOrderMasterRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderMasterRefferedHistory",
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
     *                  ref="#/definitions/PurchaseOrderMasterRefferedHistory"
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
        /** @var PurchaseOrderMasterRefferedHistory $purchaseOrderMasterRefferedHistory */
        $purchaseOrderMasterRefferedHistory = $this->purchaseOrderMasterRefferedHistoryRepository->with(['created_by', 'confirmed_by', 'segment'])->findWithoutFail($id);

        if (empty($purchaseOrderMasterRefferedHistory)) {
            return $this->sendError('Purchase Order Master Reffered History not found');
        }

        return $this->sendResponse($purchaseOrderMasterRefferedHistory->toArray(), 'Purchase Order Master Reffered History retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePurchaseOrderMasterRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/purchaseOrderMasterRefferedHistories/{id}",
     *      summary="Update the specified PurchaseOrderMasterRefferedHistory in storage",
     *      tags={"PurchaseOrderMasterRefferedHistory"},
     *      description="Update PurchaseOrderMasterRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderMasterRefferedHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseOrderMasterRefferedHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseOrderMasterRefferedHistory")
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
     *                  ref="#/definitions/PurchaseOrderMasterRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePurchaseOrderMasterRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseOrderMasterRefferedHistory $purchaseOrderMasterRefferedHistory */
        $purchaseOrderMasterRefferedHistory = $this->purchaseOrderMasterRefferedHistoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderMasterRefferedHistory)) {
            return $this->sendError('Purchase Order Master Reffered History not found');
        }

        $purchaseOrderMasterRefferedHistory = $this->purchaseOrderMasterRefferedHistoryRepository->update($input, $id);

        return $this->sendResponse($purchaseOrderMasterRefferedHistory->toArray(), 'PurchaseOrderMasterRefferedHistory updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/purchaseOrderMasterRefferedHistories/{id}",
     *      summary="Remove the specified PurchaseOrderMasterRefferedHistory from storage",
     *      tags={"PurchaseOrderMasterRefferedHistory"},
     *      description="Delete PurchaseOrderMasterRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderMasterRefferedHistory",
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
        /** @var PurchaseOrderMasterRefferedHistory $purchaseOrderMasterRefferedHistory */
        $purchaseOrderMasterRefferedHistory = $this->purchaseOrderMasterRefferedHistoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderMasterRefferedHistory)) {
            return $this->sendError('Purchase Order Master Reffered History not found');
        }

        $purchaseOrderMasterRefferedHistory->delete();

        return $this->sendResponse($id, 'Purchase Order Master Reffered History deleted successfully');
    }

    public function getPoMasterAmendHistory(Request $request)
    {
        $input = $request->all();

        $procumentOrderHistory = PurchaseOrderMasterRefferedHistory::where('purchaseOrderID', $input['purchaseOrderID'])
            ->with(['created_by','location','supplier','currency','fcategory','segment','approved_by'])
            ->get();

        return $this->sendResponse($procumentOrderHistory, 'Purchase Order Master retrieved successfully');
    }

}
