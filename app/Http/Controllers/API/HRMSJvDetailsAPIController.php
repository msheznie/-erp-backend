<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHRMSJvDetailsAPIRequest;
use App\Http\Requests\API\UpdateHRMSJvDetailsAPIRequest;
use App\Models\HRMSJvDetails;
use App\Repositories\HRMSJvDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HRMSJvDetailsController
 * @package App\Http\Controllers\API
 */

class HRMSJvDetailsAPIController extends AppBaseController
{
    /** @var  HRMSJvDetailsRepository */
    private $hRMSJvDetailsRepository;

    public function __construct(HRMSJvDetailsRepository $hRMSJvDetailsRepo)
    {
        $this->hRMSJvDetailsRepository = $hRMSJvDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSJvDetails",
     *      summary="Get a listing of the HRMSJvDetails.",
     *      tags={"HRMSJvDetails"},
     *      description="Get all HRMSJvDetails",
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
     *                  @SWG\Items(ref="#/definitions/HRMSJvDetails")
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
        $this->hRMSJvDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->hRMSJvDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hRMSJvDetails = $this->hRMSJvDetailsRepository->all();

        return $this->sendResponse($hRMSJvDetails->toArray(), trans('custom.h_r_m_s_jv_details_retrieved_successfully'));
    }

    /**
     * @param CreateHRMSJvDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hRMSJvDetails",
     *      summary="Store a newly created HRMSJvDetails in storage",
     *      tags={"HRMSJvDetails"},
     *      description="Store HRMSJvDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSJvDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSJvDetails")
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
     *                  ref="#/definitions/HRMSJvDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHRMSJvDetailsAPIRequest $request)
    {
        $input = $request->all();

        $hRMSJvDetails = $this->hRMSJvDetailsRepository->create($input);

        return $this->sendResponse($hRMSJvDetails->toArray(), trans('custom.h_r_m_s_jv_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSJvDetails/{id}",
     *      summary="Display the specified HRMSJvDetails",
     *      tags={"HRMSJvDetails"},
     *      description="Get HRMSJvDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSJvDetails",
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
     *                  ref="#/definitions/HRMSJvDetails"
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
        /** @var HRMSJvDetails $hRMSJvDetails */
        $hRMSJvDetails = $this->hRMSJvDetailsRepository->findWithoutFail($id);

        if (empty($hRMSJvDetails)) {
            return $this->sendError(trans('custom.h_r_m_s_jv_details_not_found'));
        }

        return $this->sendResponse($hRMSJvDetails->toArray(), trans('custom.h_r_m_s_jv_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHRMSJvDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hRMSJvDetails/{id}",
     *      summary="Update the specified HRMSJvDetails in storage",
     *      tags={"HRMSJvDetails"},
     *      description="Update HRMSJvDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSJvDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSJvDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSJvDetails")
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
     *                  ref="#/definitions/HRMSJvDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHRMSJvDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var HRMSJvDetails $hRMSJvDetails */
        $hRMSJvDetails = $this->hRMSJvDetailsRepository->findWithoutFail($id);

        if (empty($hRMSJvDetails)) {
            return $this->sendError(trans('custom.h_r_m_s_jv_details_not_found'));
        }

        $hRMSJvDetails = $this->hRMSJvDetailsRepository->update($input, $id);

        return $this->sendResponse($hRMSJvDetails->toArray(), trans('custom.hrmsjvdetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hRMSJvDetails/{id}",
     *      summary="Remove the specified HRMSJvDetails from storage",
     *      tags={"HRMSJvDetails"},
     *      description="Delete HRMSJvDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSJvDetails",
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
        /** @var HRMSJvDetails $hRMSJvDetails */
        $hRMSJvDetails = $this->hRMSJvDetailsRepository->findWithoutFail($id);

        if (empty($hRMSJvDetails)) {
            return $this->sendError(trans('custom.h_r_m_s_jv_details_not_found'));
        }

        $hRMSJvDetails->delete();

        return $this->sendResponse($id, trans('custom.h_r_m_s_jv_details_deleted_successfully'));
    }
}
