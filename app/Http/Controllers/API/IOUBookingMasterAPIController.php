<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateIOUBookingMasterAPIRequest;
use App\Http\Requests\API\UpdateIOUBookingMasterAPIRequest;
use App\Models\IOUBookingMaster;
use App\Repositories\IOUBookingMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class IOUBookingMasterController
 * @package App\Http\Controllers\API
 */

class IOUBookingMasterAPIController extends AppBaseController
{
    /** @var  IOUBookingMasterRepository */
    private $iOUBookingMasterRepository;

    public function __construct(IOUBookingMasterRepository $iOUBookingMasterRepo)
    {
        $this->iOUBookingMasterRepository = $iOUBookingMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/i-o-u-booking-masters",
     *      summary="getIOUBookingMasterList",
     *      tags={"IOUBookingMaster"},
     *      description="Get all IOUBookingMasters",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/IOUBookingMaster")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->iOUBookingMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->iOUBookingMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $iOUBookingMasters = $this->iOUBookingMasterRepository->all();

        return $this->sendResponse($iOUBookingMasters->toArray(), trans('custom.i_o_u_booking_masters_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/i-o-u-booking-masters",
     *      summary="createIOUBookingMaster",
     *      tags={"IOUBookingMaster"},
     *      description="Create IOUBookingMaster",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/IOUBookingMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateIOUBookingMasterAPIRequest $request)
    {
        $input = $request->all();

        $iOUBookingMaster = $this->iOUBookingMasterRepository->create($input);

        return $this->sendResponse($iOUBookingMaster->toArray(), trans('custom.i_o_u_booking_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/i-o-u-booking-masters/{id}",
     *      summary="getIOUBookingMasterItem",
     *      tags={"IOUBookingMaster"},
     *      description="Get IOUBookingMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of IOUBookingMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/IOUBookingMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var IOUBookingMaster $iOUBookingMaster */
        $iOUBookingMaster = $this->iOUBookingMasterRepository->findWithoutFail($id);

        if (empty($iOUBookingMaster)) {
            return $this->sendError(trans('custom.i_o_u_booking_master_not_found'));
        }

        return $this->sendResponse($iOUBookingMaster->toArray(), trans('custom.i_o_u_booking_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/i-o-u-booking-masters/{id}",
     *      summary="updateIOUBookingMaster",
     *      tags={"IOUBookingMaster"},
     *      description="Update IOUBookingMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of IOUBookingMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/IOUBookingMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateIOUBookingMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var IOUBookingMaster $iOUBookingMaster */
        $iOUBookingMaster = $this->iOUBookingMasterRepository->findWithoutFail($id);

        if (empty($iOUBookingMaster)) {
            return $this->sendError(trans('custom.i_o_u_booking_master_not_found'));
        }

        $iOUBookingMaster = $this->iOUBookingMasterRepository->update($input, $id);

        return $this->sendResponse($iOUBookingMaster->toArray(), trans('custom.ioubookingmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/i-o-u-booking-masters/{id}",
     *      summary="deleteIOUBookingMaster",
     *      tags={"IOUBookingMaster"},
     *      description="Delete IOUBookingMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of IOUBookingMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var IOUBookingMaster $iOUBookingMaster */
        $iOUBookingMaster = $this->iOUBookingMasterRepository->findWithoutFail($id);

        if (empty($iOUBookingMaster)) {
            return $this->sendError(trans('custom.i_o_u_booking_master_not_found'));
        }

        $iOUBookingMaster->delete();

        return $this->sendSuccess('I O U Booking Master deleted successfully');
    }
}
