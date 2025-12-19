<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSOURCEShiftDetailsAPIRequest;
use App\Http\Requests\API\UpdatePOSSOURCEShiftDetailsAPIRequest;
use App\Models\POSSOURCEShiftDetails;
use App\Repositories\POSSOURCEShiftDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSOURCEShiftDetailsController
 * @package App\Http\Controllers\API
 */

class POSSOURCEShiftDetailsAPIController extends AppBaseController
{
    /** @var  POSSOURCEShiftDetailsRepository */
    private $pOSSOURCEShiftDetailsRepository;

    public function __construct(POSSOURCEShiftDetailsRepository $pOSSOURCEShiftDetailsRepo)
    {
        $this->pOSSOURCEShiftDetailsRepository = $pOSSOURCEShiftDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSOURCEShiftDetails",
     *      summary="Get a listing of the POSSOURCEShiftDetails.",
     *      tags={"POSSOURCEShiftDetails"},
     *      description="Get all POSSOURCEShiftDetails",
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
     *                  @SWG\Items(ref="#/definitions/POSSOURCEShiftDetails")
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
        $this->pOSSOURCEShiftDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSOURCEShiftDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSOURCEShiftDetails = $this->pOSSOURCEShiftDetailsRepository->all();

        return $this->sendResponse($pOSSOURCEShiftDetails->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_shift_details_retrieved_successf'));
    }

    /**
     * @param CreatePOSSOURCEShiftDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSOURCEShiftDetails",
     *      summary="Store a newly created POSSOURCEShiftDetails in storage",
     *      tags={"POSSOURCEShiftDetails"},
     *      description="Store POSSOURCEShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSOURCEShiftDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSOURCEShiftDetails")
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
     *                  ref="#/definitions/POSSOURCEShiftDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSOURCEShiftDetailsAPIRequest $request)
    {
        $input = $request->all();

        $pOSSOURCEShiftDetails = $this->pOSSOURCEShiftDetailsRepository->create($input);

        return $this->sendResponse($pOSSOURCEShiftDetails->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_shift_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSOURCEShiftDetails/{id}",
     *      summary="Display the specified POSSOURCEShiftDetails",
     *      tags={"POSSOURCEShiftDetails"},
     *      description="Get POSSOURCEShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCEShiftDetails",
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
     *                  ref="#/definitions/POSSOURCEShiftDetails"
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
        /** @var POSSOURCEShiftDetails $pOSSOURCEShiftDetails */
        $pOSSOURCEShiftDetails = $this->pOSSOURCEShiftDetailsRepository->findWithoutFail($id);

        if (empty($pOSSOURCEShiftDetails)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_shift_details_not_found'));
        }

        return $this->sendResponse($pOSSOURCEShiftDetails->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_shift_details_retrieved_successf'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSOURCEShiftDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSOURCEShiftDetails/{id}",
     *      summary="Update the specified POSSOURCEShiftDetails in storage",
     *      tags={"POSSOURCEShiftDetails"},
     *      description="Update POSSOURCEShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCEShiftDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSOURCEShiftDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSOURCEShiftDetails")
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
     *                  ref="#/definitions/POSSOURCEShiftDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSOURCEShiftDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSOURCEShiftDetails $pOSSOURCEShiftDetails */
        $pOSSOURCEShiftDetails = $this->pOSSOURCEShiftDetailsRepository->findWithoutFail($id);

        if (empty($pOSSOURCEShiftDetails)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_shift_details_not_found'));
        }

        $pOSSOURCEShiftDetails = $this->pOSSOURCEShiftDetailsRepository->update($input, $id);

        return $this->sendResponse($pOSSOURCEShiftDetails->toArray(), trans('custom.possourceshiftdetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSOURCEShiftDetails/{id}",
     *      summary="Remove the specified POSSOURCEShiftDetails from storage",
     *      tags={"POSSOURCEShiftDetails"},
     *      description="Delete POSSOURCEShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCEShiftDetails",
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
        /** @var POSSOURCEShiftDetails $pOSSOURCEShiftDetails */
        $pOSSOURCEShiftDetails = $this->pOSSOURCEShiftDetailsRepository->findWithoutFail($id);

        if (empty($pOSSOURCEShiftDetails)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_shift_details_not_found'));
        }

        $pOSSOURCEShiftDetails->delete();

        return $this->sendSuccess('P O S S O U R C E Shift Details deleted successfully');
    }
}
