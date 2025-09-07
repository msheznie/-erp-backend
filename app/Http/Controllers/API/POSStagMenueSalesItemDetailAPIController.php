<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSStagMenueSalesItemDetailAPIRequest;
use App\Http\Requests\API\UpdatePOSStagMenueSalesItemDetailAPIRequest;
use App\Models\POSStagMenueSalesItemDetail;
use App\Repositories\POSStagMenueSalesItemDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSStagMenueSalesItemDetailController
 * @package App\Http\Controllers\API
 */

class POSStagMenueSalesItemDetailAPIController extends AppBaseController
{
    /** @var  POSStagMenueSalesItemDetailRepository */
    private $pOSStagMenueSalesItemDetailRepository;

    public function __construct(POSStagMenueSalesItemDetailRepository $pOSStagMenueSalesItemDetailRepo)
    {
        $this->pOSStagMenueSalesItemDetailRepository = $pOSStagMenueSalesItemDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagMenueSalesItemDetails",
     *      summary="Get a listing of the POSStagMenueSalesItemDetails.",
     *      tags={"POSStagMenueSalesItemDetail"},
     *      description="Get all POSStagMenueSalesItemDetails",
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
     *                  @SWG\Items(ref="#/definitions/POSStagMenueSalesItemDetail")
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
        $this->pOSStagMenueSalesItemDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSStagMenueSalesItemDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSStagMenueSalesItemDetails = $this->pOSStagMenueSalesItemDetailRepository->all();

        return $this->sendResponse($pOSStagMenueSalesItemDetails->toArray(), trans('custom.p_o_s_stag_menue_sales_item_details_retrieved_succ'));
    }

    /**
     * @param CreatePOSStagMenueSalesItemDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSStagMenueSalesItemDetails",
     *      summary="Store a newly created POSStagMenueSalesItemDetail in storage",
     *      tags={"POSStagMenueSalesItemDetail"},
     *      description="Store POSStagMenueSalesItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagMenueSalesItemDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagMenueSalesItemDetail")
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
     *                  ref="#/definitions/POSStagMenueSalesItemDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSStagMenueSalesItemDetailAPIRequest $request)
    {
        $input = $request->all();

        $pOSStagMenueSalesItemDetail = $this->pOSStagMenueSalesItemDetailRepository->create($input);

        return $this->sendResponse($pOSStagMenueSalesItemDetail->toArray(), trans('custom.p_o_s_stag_menue_sales_item_detail_saved_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagMenueSalesItemDetails/{id}",
     *      summary="Display the specified POSStagMenueSalesItemDetail",
     *      tags={"POSStagMenueSalesItemDetail"},
     *      description="Get POSStagMenueSalesItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenueSalesItemDetail",
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
     *                  ref="#/definitions/POSStagMenueSalesItemDetail"
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
        /** @var POSStagMenueSalesItemDetail $pOSStagMenueSalesItemDetail */
        $pOSStagMenueSalesItemDetail = $this->pOSStagMenueSalesItemDetailRepository->findWithoutFail($id);

        if (empty($pOSStagMenueSalesItemDetail)) {
            return $this->sendError(trans('custom.p_o_s_stag_menue_sales_item_detail_not_found'));
        }

        return $this->sendResponse($pOSStagMenueSalesItemDetail->toArray(), trans('custom.p_o_s_stag_menue_sales_item_detail_retrieved_succe'));
    }

    /**
     * @param int $id
     * @param UpdatePOSStagMenueSalesItemDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSStagMenueSalesItemDetails/{id}",
     *      summary="Update the specified POSStagMenueSalesItemDetail in storage",
     *      tags={"POSStagMenueSalesItemDetail"},
     *      description="Update POSStagMenueSalesItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenueSalesItemDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagMenueSalesItemDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagMenueSalesItemDetail")
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
     *                  ref="#/definitions/POSStagMenueSalesItemDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSStagMenueSalesItemDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSStagMenueSalesItemDetail $pOSStagMenueSalesItemDetail */
        $pOSStagMenueSalesItemDetail = $this->pOSStagMenueSalesItemDetailRepository->findWithoutFail($id);

        if (empty($pOSStagMenueSalesItemDetail)) {
            return $this->sendError(trans('custom.p_o_s_stag_menue_sales_item_detail_not_found'));
        }

        $pOSStagMenueSalesItemDetail = $this->pOSStagMenueSalesItemDetailRepository->update($input, $id);

        return $this->sendResponse($pOSStagMenueSalesItemDetail->toArray(), trans('custom.posstagmenuesalesitemdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSStagMenueSalesItemDetails/{id}",
     *      summary="Remove the specified POSStagMenueSalesItemDetail from storage",
     *      tags={"POSStagMenueSalesItemDetail"},
     *      description="Delete POSStagMenueSalesItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenueSalesItemDetail",
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
        /** @var POSStagMenueSalesItemDetail $pOSStagMenueSalesItemDetail */
        $pOSStagMenueSalesItemDetail = $this->pOSStagMenueSalesItemDetailRepository->findWithoutFail($id);

        if (empty($pOSStagMenueSalesItemDetail)) {
            return $this->sendError(trans('custom.p_o_s_stag_menue_sales_item_detail_not_found'));
        }

        $pOSStagMenueSalesItemDetail->delete();

        return $this->sendSuccess('P O S Stag Menue Sales Item Detail deleted successfully');
    }
}
