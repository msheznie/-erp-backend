<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAppointmentDetailsAPIRequest;
use App\Http\Requests\API\UpdateAppointmentDetailsAPIRequest;
use App\Models\AppointmentDetails;
use App\Repositories\AppointmentDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AppointmentDetailsController
 * @package App\Http\Controllers\API
 */

class AppointmentDetailsAPIController extends AppBaseController
{
    /** @var  AppointmentDetailsRepository */
    private $appointmentDetailsRepository;

    public function __construct(AppointmentDetailsRepository $appointmentDetailsRepo)
    {
        $this->appointmentDetailsRepository = $appointmentDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/appointmentDetails",
     *      summary="Get a listing of the AppointmentDetails.",
     *      tags={"AppointmentDetails"},
     *      description="Get all AppointmentDetails",
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
     *                  @SWG\Items(ref="#/definitions/AppointmentDetails")
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
        $this->appointmentDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->appointmentDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $appointmentDetails = $this->appointmentDetailsRepository->all();

        return $this->sendResponse($appointmentDetails->toArray(), trans('custom.appointment_details_retrieved_successfully'));
    }

    /**
     * @param CreateAppointmentDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/appointmentDetails",
     *      summary="Store a newly created AppointmentDetails in storage",
     *      tags={"AppointmentDetails"},
     *      description="Store AppointmentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AppointmentDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AppointmentDetails")
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
     *                  ref="#/definitions/AppointmentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAppointmentDetailsAPIRequest $request)
    {
        $input = $request->all();

        $appointmentDetails = $this->appointmentDetailsRepository->create($input);

        return $this->sendResponse($appointmentDetails->toArray(), trans('custom.appointment_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/appointmentDetails/{id}",
     *      summary="Display the specified AppointmentDetails",
     *      tags={"AppointmentDetails"},
     *      description="Get AppointmentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AppointmentDetails",
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
     *                  ref="#/definitions/AppointmentDetails"
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
        /** @var AppointmentDetails $appointmentDetails */
        $appointmentDetails = $this->appointmentDetailsRepository->findWithoutFail($id);

        if (empty($appointmentDetails)) {
            return $this->sendError(trans('custom.appointment_details_not_found'));
        }

        return $this->sendResponse($appointmentDetails->toArray(), trans('custom.appointment_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateAppointmentDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/appointmentDetails/{id}",
     *      summary="Update the specified AppointmentDetails in storage",
     *      tags={"AppointmentDetails"},
     *      description="Update AppointmentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AppointmentDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AppointmentDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AppointmentDetails")
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
     *                  ref="#/definitions/AppointmentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAppointmentDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var AppointmentDetails $appointmentDetails */
        $appointmentDetails = $this->appointmentDetailsRepository->findWithoutFail($id);

        if (empty($appointmentDetails)) {
            return $this->sendError(trans('custom.appointment_details_not_found'));
        }

        $appointmentDetails = $this->appointmentDetailsRepository->update($input, $id);

        return $this->sendResponse($appointmentDetails->toArray(), trans('custom.appointmentdetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/appointmentDetails/{id}",
     *      summary="Remove the specified AppointmentDetails from storage",
     *      tags={"AppointmentDetails"},
     *      description="Delete AppointmentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AppointmentDetails",
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
        /** @var AppointmentDetails $appointmentDetails */
        $appointmentDetails = $this->appointmentDetailsRepository->findWithoutFail($id);

        if (empty($appointmentDetails)) {
            return $this->sendError(trans('custom.appointment_details_not_found'));
        }

        $appointmentDetails->delete();

        return $this->sendSuccess('Appointment Details deleted successfully');
    }
}
