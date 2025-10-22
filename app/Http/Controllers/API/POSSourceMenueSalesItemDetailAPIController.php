<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSourceMenueSalesItemDetailAPIRequest;
use App\Http\Requests\API\UpdatePOSSourceMenueSalesItemDetailAPIRequest;
use App\Models\POSSourceMenueSalesItemDetail;
use App\Repositories\POSSourceMenueSalesItemDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSourceMenueSalesItemDetailController
 * @package App\Http\Controllers\API
 */

class POSSourceMenueSalesItemDetailAPIController extends AppBaseController
{
    /** @var  POSSourceMenueSalesItemDetailRepository */
    private $pOSSourceMenueSalesItemDetailRepository;

    public function __construct(POSSourceMenueSalesItemDetailRepository $pOSSourceMenueSalesItemDetailRepo)
    {
        $this->pOSSourceMenueSalesItemDetailRepository = $pOSSourceMenueSalesItemDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceMenueSalesItemDetails",
     *      summary="Get a listing of the POSSourceMenueSalesItemDetails.",
     *      tags={"POSSourceMenueSalesItemDetail"},
     *      description="Get all POSSourceMenueSalesItemDetails",
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
     *                  @SWG\Items(ref="#/definitions/POSSourceMenueSalesItemDetail")
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
        $this->pOSSourceMenueSalesItemDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSourceMenueSalesItemDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSourceMenueSalesItemDetails = $this->pOSSourceMenueSalesItemDetailRepository->all();

        return $this->sendResponse($pOSSourceMenueSalesItemDetails->toArray(), trans('custom.p_o_s_source_menue_sales_item_details_retrieved_su'));
    }

    /**
     * @param CreatePOSSourceMenueSalesItemDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSourceMenueSalesItemDetails",
     *      summary="Store a newly created POSSourceMenueSalesItemDetail in storage",
     *      tags={"POSSourceMenueSalesItemDetail"},
     *      description="Store POSSourceMenueSalesItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceMenueSalesItemDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceMenueSalesItemDetail")
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
     *                  ref="#/definitions/POSSourceMenueSalesItemDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSourceMenueSalesItemDetailAPIRequest $request)
    {
        $input = $request->all();

        $pOSSourceMenueSalesItemDetail = $this->pOSSourceMenueSalesItemDetailRepository->create($input);

        return $this->sendResponse($pOSSourceMenueSalesItemDetail->toArray(), trans('custom.p_o_s_source_menue_sales_item_detail_saved_success'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceMenueSalesItemDetails/{id}",
     *      summary="Display the specified POSSourceMenueSalesItemDetail",
     *      tags={"POSSourceMenueSalesItemDetail"},
     *      description="Get POSSourceMenueSalesItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenueSalesItemDetail",
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
     *                  ref="#/definitions/POSSourceMenueSalesItemDetail"
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
        /** @var POSSourceMenueSalesItemDetail $pOSSourceMenueSalesItemDetail */
        $pOSSourceMenueSalesItemDetail = $this->pOSSourceMenueSalesItemDetailRepository->findWithoutFail($id);

        if (empty($pOSSourceMenueSalesItemDetail)) {
            return $this->sendError(trans('custom.p_o_s_source_menue_sales_item_detail_not_found'));
        }

        return $this->sendResponse($pOSSourceMenueSalesItemDetail->toArray(), trans('custom.p_o_s_source_menue_sales_item_detail_retrieved_suc'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSourceMenueSalesItemDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSourceMenueSalesItemDetails/{id}",
     *      summary="Update the specified POSSourceMenueSalesItemDetail in storage",
     *      tags={"POSSourceMenueSalesItemDetail"},
     *      description="Update POSSourceMenueSalesItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenueSalesItemDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceMenueSalesItemDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceMenueSalesItemDetail")
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
     *                  ref="#/definitions/POSSourceMenueSalesItemDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSourceMenueSalesItemDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSourceMenueSalesItemDetail $pOSSourceMenueSalesItemDetail */
        $pOSSourceMenueSalesItemDetail = $this->pOSSourceMenueSalesItemDetailRepository->findWithoutFail($id);

        if (empty($pOSSourceMenueSalesItemDetail)) {
            return $this->sendError(trans('custom.p_o_s_source_menue_sales_item_detail_not_found'));
        }

        $pOSSourceMenueSalesItemDetail = $this->pOSSourceMenueSalesItemDetailRepository->update($input, $id);

        return $this->sendResponse($pOSSourceMenueSalesItemDetail->toArray(), trans('custom.possourcemenuesalesitemdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSourceMenueSalesItemDetails/{id}",
     *      summary="Remove the specified POSSourceMenueSalesItemDetail from storage",
     *      tags={"POSSourceMenueSalesItemDetail"},
     *      description="Delete POSSourceMenueSalesItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenueSalesItemDetail",
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
        /** @var POSSourceMenueSalesItemDetail $pOSSourceMenueSalesItemDetail */
        $pOSSourceMenueSalesItemDetail = $this->pOSSourceMenueSalesItemDetailRepository->findWithoutFail($id);

        if (empty($pOSSourceMenueSalesItemDetail)) {
            return $this->sendError(trans('custom.p_o_s_source_menue_sales_item_detail_not_found'));
        }

        $pOSSourceMenueSalesItemDetail->delete();

        return $this->sendSuccess('P O S Source Menue Sales Item Detail deleted successfully');
    }
}
