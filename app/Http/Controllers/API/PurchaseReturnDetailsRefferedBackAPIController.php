<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseReturnDetailsRefferedBackAPIRequest;
use App\Http\Requests\API\UpdatePurchaseReturnDetailsRefferedBackAPIRequest;
use App\Models\PurchaseReturnDetailsRefferedBack;
use App\Repositories\PurchaseReturnDetailsRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PurchaseReturnDetailsRefferedBackController
 * @package App\Http\Controllers\API
 */

class PurchaseReturnDetailsRefferedBackAPIController extends AppBaseController
{
    /** @var  PurchaseReturnDetailsRefferedBackRepository */
    private $purchaseReturnDetailsRefferedBackRepository;

    public function __construct(PurchaseReturnDetailsRefferedBackRepository $purchaseReturnDetailsRefferedBackRepo)
    {
        $this->purchaseReturnDetailsRefferedBackRepository = $purchaseReturnDetailsRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturnDetailsRefferedBacks",
     *      summary="Get a listing of the PurchaseReturnDetailsRefferedBacks.",
     *      tags={"PurchaseReturnDetailsRefferedBack"},
     *      description="Get all PurchaseReturnDetailsRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/PurchaseReturnDetailsRefferedBack")
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
        $this->purchaseReturnDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseReturnDetailsRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseReturnDetailsRefferedBacks = $this->purchaseReturnDetailsRefferedBackRepository->all();

        return $this->sendResponse($purchaseReturnDetailsRefferedBacks->toArray(), trans('custom.purchase_return_details_reffered_backs_retrieved_s'));
    }

    /**
     * @param CreatePurchaseReturnDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/purchaseReturnDetailsRefferedBacks",
     *      summary="Store a newly created PurchaseReturnDetailsRefferedBack in storage",
     *      tags={"PurchaseReturnDetailsRefferedBack"},
     *      description="Store PurchaseReturnDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturnDetailsRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturnDetailsRefferedBack")
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
     *                  ref="#/definitions/PurchaseReturnDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePurchaseReturnDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $purchaseReturnDetailsRefferedBack = $this->purchaseReturnDetailsRefferedBackRepository->create($input);

        return $this->sendResponse($purchaseReturnDetailsRefferedBack->toArray(), trans('custom.purchase_return_details_reffered_back_saved_succes'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturnDetailsRefferedBacks/{id}",
     *      summary="Display the specified PurchaseReturnDetailsRefferedBack",
     *      tags={"PurchaseReturnDetailsRefferedBack"},
     *      description="Get PurchaseReturnDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnDetailsRefferedBack",
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
     *                  ref="#/definitions/PurchaseReturnDetailsRefferedBack"
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
        /** @var PurchaseReturnDetailsRefferedBack $purchaseReturnDetailsRefferedBack */
        $purchaseReturnDetailsRefferedBack = $this->purchaseReturnDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($purchaseReturnDetailsRefferedBack)) {
            return $this->sendError(trans('custom.purchase_return_details_reffered_back_not_found'));
        }

        return $this->sendResponse($purchaseReturnDetailsRefferedBack->toArray(), trans('custom.purchase_return_details_reffered_back_retrieved_su'));
    }

    /**
     * @param int $id
     * @param UpdatePurchaseReturnDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/purchaseReturnDetailsRefferedBacks/{id}",
     *      summary="Update the specified PurchaseReturnDetailsRefferedBack in storage",
     *      tags={"PurchaseReturnDetailsRefferedBack"},
     *      description="Update PurchaseReturnDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnDetailsRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturnDetailsRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturnDetailsRefferedBack")
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
     *                  ref="#/definitions/PurchaseReturnDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePurchaseReturnDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseReturnDetailsRefferedBack $purchaseReturnDetailsRefferedBack */
        $purchaseReturnDetailsRefferedBack = $this->purchaseReturnDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($purchaseReturnDetailsRefferedBack)) {
            return $this->sendError(trans('custom.purchase_return_details_reffered_back_not_found'));
        }

        $purchaseReturnDetailsRefferedBack = $this->purchaseReturnDetailsRefferedBackRepository->update($input, $id);

        return $this->sendResponse($purchaseReturnDetailsRefferedBack->toArray(), trans('custom.purchasereturndetailsrefferedback_updated_successf'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/purchaseReturnDetailsRefferedBacks/{id}",
     *      summary="Remove the specified PurchaseReturnDetailsRefferedBack from storage",
     *      tags={"PurchaseReturnDetailsRefferedBack"},
     *      description="Delete PurchaseReturnDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnDetailsRefferedBack",
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
        /** @var PurchaseReturnDetailsRefferedBack $purchaseReturnDetailsRefferedBack */
        $purchaseReturnDetailsRefferedBack = $this->purchaseReturnDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($purchaseReturnDetailsRefferedBack)) {
            return $this->sendError(trans('custom.purchase_return_details_reffered_back_not_found'));
        }

        $purchaseReturnDetailsRefferedBack->delete();

        return $this->sendSuccess('Purchase Return Details Reffered Back deleted successfully');
    }

    public function getPRDetailsAmendHistory(Request $request)
    {
        $input = $request->all();
        $purhaseReturnAutoID = $input['purhaseReturnAutoID'];
        $timesReferred = $input['timesReferred'];

        $items = PurchaseReturnDetailsRefferedBack::where('purhaseReturnAutoID', $purhaseReturnAutoID)
                                                ->where('timesReferred', $timesReferred)
                                                ->with(['unit','grv_master'])
                                                ->get();

        return $this->sendResponse($items->toArray(), trans('custom.pr_details_refferedback_retrieved_successfully'));
    }
}
