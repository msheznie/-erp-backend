<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockCountDetailsRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateStockCountDetailsRefferedBackAPIRequest;
use App\Models\StockCountDetailsRefferedBack;
use App\Repositories\StockCountDetailsRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockCountDetailsRefferedBackController
 * @package App\Http\Controllers\API
 */

class StockCountDetailsRefferedBackAPIController extends AppBaseController
{
    /** @var  StockCountDetailsRefferedBackRepository */
    private $stockCountDetailsRefferedBackRepository;

    public function __construct(StockCountDetailsRefferedBackRepository $stockCountDetailsRefferedBackRepo)
    {
        $this->stockCountDetailsRefferedBackRepository = $stockCountDetailsRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockCountDetailsRefferedBacks",
     *      summary="Get a listing of the StockCountDetailsRefferedBacks.",
     *      tags={"StockCountDetailsRefferedBack"},
     *      description="Get all StockCountDetailsRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/StockCountDetailsRefferedBack")
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
        $this->stockCountDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->stockCountDetailsRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockCountDetailsRefferedBacks = $this->stockCountDetailsRefferedBackRepository->all();

        return $this->sendResponse($stockCountDetailsRefferedBacks->toArray(), trans('custom.stock_count_details_reffered_backs_retrieved_succe'));
    }

    /**
     * @param CreateStockCountDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockCountDetailsRefferedBacks",
     *      summary="Store a newly created StockCountDetailsRefferedBack in storage",
     *      tags={"StockCountDetailsRefferedBack"},
     *      description="Store StockCountDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockCountDetailsRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockCountDetailsRefferedBack")
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
     *                  ref="#/definitions/StockCountDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockCountDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $stockCountDetailsRefferedBack = $this->stockCountDetailsRefferedBackRepository->create($input);

        return $this->sendResponse($stockCountDetailsRefferedBack->toArray(), trans('custom.stock_count_details_reffered_back_saved_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockCountDetailsRefferedBacks/{id}",
     *      summary="Display the specified StockCountDetailsRefferedBack",
     *      tags={"StockCountDetailsRefferedBack"},
     *      description="Get StockCountDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockCountDetailsRefferedBack",
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
     *                  ref="#/definitions/StockCountDetailsRefferedBack"
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
        /** @var StockCountDetailsRefferedBack $stockCountDetailsRefferedBack */
        $stockCountDetailsRefferedBack = $this->stockCountDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockCountDetailsRefferedBack)) {
            return $this->sendError(trans('custom.stock_count_details_reffered_back_not_found'));
        }

        return $this->sendResponse($stockCountDetailsRefferedBack->toArray(), trans('custom.stock_count_details_reffered_back_retrieved_succes'));
    }

    /**
     * @param int $id
     * @param UpdateStockCountDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockCountDetailsRefferedBacks/{id}",
     *      summary="Update the specified StockCountDetailsRefferedBack in storage",
     *      tags={"StockCountDetailsRefferedBack"},
     *      description="Update StockCountDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockCountDetailsRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockCountDetailsRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockCountDetailsRefferedBack")
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
     *                  ref="#/definitions/StockCountDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockCountDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var StockCountDetailsRefferedBack $stockCountDetailsRefferedBack */
        $stockCountDetailsRefferedBack = $this->stockCountDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockCountDetailsRefferedBack)) {
            return $this->sendError(trans('custom.stock_count_details_reffered_back_not_found'));
        }

        $stockCountDetailsRefferedBack = $this->stockCountDetailsRefferedBackRepository->update($input, $id);

        return $this->sendResponse($stockCountDetailsRefferedBack->toArray(), trans('custom.stockcountdetailsrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockCountDetailsRefferedBacks/{id}",
     *      summary="Remove the specified StockCountDetailsRefferedBack from storage",
     *      tags={"StockCountDetailsRefferedBack"},
     *      description="Delete StockCountDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockCountDetailsRefferedBack",
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
        /** @var StockCountDetailsRefferedBack $stockCountDetailsRefferedBack */
        $stockCountDetailsRefferedBack = $this->stockCountDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockCountDetailsRefferedBack)) {
            return $this->sendError(trans('custom.stock_count_details_reffered_back_not_found'));
        }

        $stockCountDetailsRefferedBack->delete();

        return $this->sendSuccess('Stock Count Details Reffered Back deleted successfully');
    }

    public function getSCDetailsReferBack(Request $request)
    {
        $input = $request->all();
        $id = $input['stockCountAutoID'];

        $items = StockCountDetailsRefferedBack::where('stockCountAutoID', $id)
            ->where('timesReferred',$input['timesReferred'])
            ->with(['uom', 'local_currency', 'rpt_currency'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.request_details_retrieved_successfully'));
    }
}
