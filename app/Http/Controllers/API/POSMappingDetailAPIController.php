<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSMappingDetailAPIRequest;
use App\Http\Requests\API\UpdatePOSMappingDetailAPIRequest;
use App\Models\POSMappingDetail;
use App\Repositories\POSMappingDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSMappingDetailController
 * @package App\Http\Controllers\API
 */

class POSMappingDetailAPIController extends AppBaseController
{
    /** @var  POSMappingDetailRepository */
    private $pOSMappingDetailRepository;

    public function __construct(POSMappingDetailRepository $pOSMappingDetailRepo)
    {
        $this->pOSMappingDetailRepository = $pOSMappingDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSMappingDetails",
     *      summary="Get a listing of the POSMappingDetails.",
     *      tags={"POSMappingDetail"},
     *      description="Get all POSMappingDetails",
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
     *                  @SWG\Items(ref="#/definitions/POSMappingDetail")
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
        $this->pOSMappingDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSMappingDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSMappingDetails = $this->pOSMappingDetailRepository->all();

        return $this->sendResponse($pOSMappingDetails->toArray(), trans('custom.p_o_s_mapping_details_retrieved_successfully'));
    }

    /**
     * @param CreatePOSMappingDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSMappingDetails",
     *      summary="Store a newly created POSMappingDetail in storage",
     *      tags={"POSMappingDetail"},
     *      description="Store POSMappingDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSMappingDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSMappingDetail")
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
     *                  ref="#/definitions/POSMappingDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSMappingDetailAPIRequest $request)
    {
        $input = $request->all();

        $pOSMappingDetail = $this->pOSMappingDetailRepository->create($input);

        return $this->sendResponse($pOSMappingDetail->toArray(), trans('custom.p_o_s_mapping_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSMappingDetails/{id}",
     *      summary="Display the specified POSMappingDetail",
     *      tags={"POSMappingDetail"},
     *      description="Get POSMappingDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSMappingDetail",
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
     *                  ref="#/definitions/POSMappingDetail"
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
        /** @var POSMappingDetail $pOSMappingDetail */
        $pOSMappingDetail = $this->pOSMappingDetailRepository->findWithoutFail($id);

        if (empty($pOSMappingDetail)) {
            return $this->sendError(trans('custom.p_o_s_mapping_detail_not_found'));
        }

        return $this->sendResponse($pOSMappingDetail->toArray(), trans('custom.p_o_s_mapping_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSMappingDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSMappingDetails/{id}",
     *      summary="Update the specified POSMappingDetail in storage",
     *      tags={"POSMappingDetail"},
     *      description="Update POSMappingDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSMappingDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSMappingDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSMappingDetail")
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
     *                  ref="#/definitions/POSMappingDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSMappingDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSMappingDetail $pOSMappingDetail */
        $pOSMappingDetail = $this->pOSMappingDetailRepository->findWithoutFail($id);

        if (empty($pOSMappingDetail)) {
            return $this->sendError(trans('custom.p_o_s_mapping_detail_not_found'));
        }

        $pOSMappingDetail = $this->pOSMappingDetailRepository->update($input, $id);

        return $this->sendResponse($pOSMappingDetail->toArray(), trans('custom.posmappingdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSMappingDetails/{id}",
     *      summary="Remove the specified POSMappingDetail from storage",
     *      tags={"POSMappingDetail"},
     *      description="Delete POSMappingDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSMappingDetail",
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
        /** @var POSMappingDetail $pOSMappingDetail */
        $pOSMappingDetail = $this->pOSMappingDetailRepository->findWithoutFail($id);

        if (empty($pOSMappingDetail)) {
            return $this->sendError(trans('custom.p_o_s_mapping_detail_not_found'));
        }

        $pOSMappingDetail->delete();

        return $this->sendSuccess('P O S Mapping Detail deleted successfully');
    }
}
