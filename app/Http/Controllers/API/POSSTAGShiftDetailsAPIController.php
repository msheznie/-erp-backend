<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSTAGShiftDetailsAPIRequest;
use App\Http\Requests\API\UpdatePOSSTAGShiftDetailsAPIRequest;
use App\Models\POSSTAGShiftDetails;
use App\Repositories\POSSTAGShiftDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSTAGShiftDetailsController
 * @package App\Http\Controllers\API
 */

class POSSTAGShiftDetailsAPIController extends AppBaseController
{
    /** @var  POSSTAGShiftDetailsRepository */
    private $pOSSTAGShiftDetailsRepository;

    public function __construct(POSSTAGShiftDetailsRepository $pOSSTAGShiftDetailsRepo)
    {
        $this->pOSSTAGShiftDetailsRepository = $pOSSTAGShiftDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGShiftDetails",
     *      summary="Get a listing of the POSSTAGShiftDetails.",
     *      tags={"POSSTAGShiftDetails"},
     *      description="Get all POSSTAGShiftDetails",
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
     *                  @SWG\Items(ref="#/definitions/POSSTAGShiftDetails")
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
        $this->pOSSTAGShiftDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSTAGShiftDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSTAGShiftDetails = $this->pOSSTAGShiftDetailsRepository->all();

        return $this->sendResponse($pOSSTAGShiftDetails->toArray(), trans('custom.p_o_s_s_t_a_g_shift_details_retrieved_successfully'));
    }

    /**
     * @param CreatePOSSTAGShiftDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSTAGShiftDetails",
     *      summary="Store a newly created POSSTAGShiftDetails in storage",
     *      tags={"POSSTAGShiftDetails"},
     *      description="Store POSSTAGShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGShiftDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGShiftDetails")
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
     *                  ref="#/definitions/POSSTAGShiftDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSTAGShiftDetailsAPIRequest $request)
    {
        $input = $request->all();

        $pOSSTAGShiftDetails = $this->pOSSTAGShiftDetailsRepository->create($input);

        return $this->sendResponse($pOSSTAGShiftDetails->toArray(), trans('custom.p_o_s_s_t_a_g_shift_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGShiftDetails/{id}",
     *      summary="Display the specified POSSTAGShiftDetails",
     *      tags={"POSSTAGShiftDetails"},
     *      description="Get POSSTAGShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGShiftDetails",
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
     *                  ref="#/definitions/POSSTAGShiftDetails"
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
        /** @var POSSTAGShiftDetails $pOSSTAGShiftDetails */
        $pOSSTAGShiftDetails = $this->pOSSTAGShiftDetailsRepository->findWithoutFail($id);

        if (empty($pOSSTAGShiftDetails)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_shift_details_not_found'));
        }

        return $this->sendResponse($pOSSTAGShiftDetails->toArray(), trans('custom.p_o_s_s_t_a_g_shift_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSTAGShiftDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSTAGShiftDetails/{id}",
     *      summary="Update the specified POSSTAGShiftDetails in storage",
     *      tags={"POSSTAGShiftDetails"},
     *      description="Update POSSTAGShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGShiftDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGShiftDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGShiftDetails")
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
     *                  ref="#/definitions/POSSTAGShiftDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSTAGShiftDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSTAGShiftDetails $pOSSTAGShiftDetails */
        $pOSSTAGShiftDetails = $this->pOSSTAGShiftDetailsRepository->findWithoutFail($id);

        if (empty($pOSSTAGShiftDetails)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_shift_details_not_found'));
        }

        $pOSSTAGShiftDetails = $this->pOSSTAGShiftDetailsRepository->update($input, $id);

        return $this->sendResponse($pOSSTAGShiftDetails->toArray(), trans('custom.posstagshiftdetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSTAGShiftDetails/{id}",
     *      summary="Remove the specified POSSTAGShiftDetails from storage",
     *      tags={"POSSTAGShiftDetails"},
     *      description="Delete POSSTAGShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGShiftDetails",
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
        /** @var POSSTAGShiftDetails $pOSSTAGShiftDetails */
        $pOSSTAGShiftDetails = $this->pOSSTAGShiftDetailsRepository->findWithoutFail($id);

        if (empty($pOSSTAGShiftDetails)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_shift_details_not_found'));
        }

        $pOSSTAGShiftDetails->delete();

        return $this->sendSuccess('P O S S T A G Shift Details deleted successfully');
    }
}
