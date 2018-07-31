<?php
/**
 * =============================================
 * -- File Name : PurchaseReturnDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Return Details
 * -- Author : Mohamed Fayas
 * -- Create date : 31 - July 2018
 * -- Description : This file contains the all CRUD for Purchase Return
 * -- REVISION HISTORY
 * -- Date: 26-March 2018 By: Fayas Description: Added new functions named as getPurchaseRequestByDocumentType()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseReturnDetailsAPIRequest;
use App\Http\Requests\API\UpdatePurchaseReturnDetailsAPIRequest;
use App\Models\PurchaseReturnDetails;
use App\Repositories\PurchaseReturnDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PurchaseReturnDetailsController
 * @package App\Http\Controllers\API
 */

class PurchaseReturnDetailsAPIController extends AppBaseController
{
    /** @var  PurchaseReturnDetailsRepository */
    private $purchaseReturnDetailsRepository;

    public function __construct(PurchaseReturnDetailsRepository $purchaseReturnDetailsRepo)
    {
        $this->purchaseReturnDetailsRepository = $purchaseReturnDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturnDetails",
     *      summary="Get a listing of the PurchaseReturnDetails.",
     *      tags={"PurchaseReturnDetails"},
     *      description="Get all PurchaseReturnDetails",
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
     *                  @SWG\Items(ref="#/definitions/PurchaseReturnDetails")
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
        $this->purchaseReturnDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseReturnDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->all();

        return $this->sendResponse($purchaseReturnDetails->toArray(), 'Purchase Return Details retrieved successfully');
    }

    /**
     * @param CreatePurchaseReturnDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/purchaseReturnDetails",
     *      summary="Store a newly created PurchaseReturnDetails in storage",
     *      tags={"PurchaseReturnDetails"},
     *      description="Store PurchaseReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturnDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturnDetails")
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
     *                  ref="#/definitions/PurchaseReturnDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePurchaseReturnDetailsAPIRequest $request)
    {
        $input = $request->all();

        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->create($input);

        return $this->sendResponse($purchaseReturnDetails->toArray(), 'Purchase Return Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturnDetails/{id}",
     *      summary="Display the specified PurchaseReturnDetails",
     *      tags={"PurchaseReturnDetails"},
     *      description="Get PurchaseReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnDetails",
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
     *                  ref="#/definitions/PurchaseReturnDetails"
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
        /** @var PurchaseReturnDetails $purchaseReturnDetails */
        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->findWithoutFail($id);

        if (empty($purchaseReturnDetails)) {
            return $this->sendError('Purchase Return Details not found');
        }

        return $this->sendResponse($purchaseReturnDetails->toArray(), 'Purchase Return Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePurchaseReturnDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/purchaseReturnDetails/{id}",
     *      summary="Update the specified PurchaseReturnDetails in storage",
     *      tags={"PurchaseReturnDetails"},
     *      description="Update PurchaseReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturnDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturnDetails")
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
     *                  ref="#/definitions/PurchaseReturnDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePurchaseReturnDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseReturnDetails $purchaseReturnDetails */
        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->findWithoutFail($id);

        if (empty($purchaseReturnDetails)) {
            return $this->sendError('Purchase Return Details not found');
        }

        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->update($input, $id);

        return $this->sendResponse($purchaseReturnDetails->toArray(), 'PurchaseReturnDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/purchaseReturnDetails/{id}",
     *      summary="Remove the specified PurchaseReturnDetails from storage",
     *      tags={"PurchaseReturnDetails"},
     *      description="Delete PurchaseReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnDetails",
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
        /** @var PurchaseReturnDetails $purchaseReturnDetails */
        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->findWithoutFail($id);

        if (empty($purchaseReturnDetails)) {
            return $this->sendError('Purchase Return Details not found');
        }

        $purchaseReturnDetails->delete();

        return $this->sendResponse($id, 'Purchase Return Details deleted successfully');
    }
}
