<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAppointmentAPIRequest;
use App\Http\Requests\API\UpdateAppointmentAPIRequest;
use App\Models\Appointment;
use App\Repositories\AppointmentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AppointmentController
 * @package App\Http\Controllers\API
 */

class AppointmentAPIController extends AppBaseController
{
    /** @var  AppointmentRepository */
    private $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepo)
    {
        $this->appointmentRepository = $appointmentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/appointments",
     *      summary="Get a listing of the Appointments.",
     *      tags={"Appointment"},
     *      description="Get all Appointments",
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
     *                  @SWG\Items(ref="#/definitions/Appointment")
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
        $this->appointmentRepository->pushCriteria(new RequestCriteria($request));
        $this->appointmentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $appointments = $this->appointmentRepository->all();

        return $this->sendResponse($appointments->toArray(), 'Appointments retrieved successfully');
    }

    /**
     * @param CreateAppointmentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/appointments",
     *      summary="Store a newly created Appointment in storage",
     *      tags={"Appointment"},
     *      description="Store Appointment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Appointment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Appointment")
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
     *                  ref="#/definitions/Appointment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAppointmentAPIRequest $request)
    {
        $input = $request->all();

        $appointment = $this->appointmentRepository->create($input);

        return $this->sendResponse($appointment->toArray(), 'Appointment saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/appointments/{id}",
     *      summary="Display the specified Appointment",
     *      tags={"Appointment"},
     *      description="Get Appointment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Appointment",
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
     *                  ref="#/definitions/Appointment"
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
        /** @var Appointment $appointment */
        $appointment = $this->appointmentRepository->findWithoutFail($id);

        if (empty($appointment)) {
            return $this->sendError('Appointment not found');
        }

        return $this->sendResponse($appointment->toArray(), 'Appointment retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAppointmentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/appointments/{id}",
     *      summary="Update the specified Appointment in storage",
     *      tags={"Appointment"},
     *      description="Update Appointment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Appointment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Appointment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Appointment")
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
     *                  ref="#/definitions/Appointment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAppointmentAPIRequest $request)
    {
        $input = $request->all();

        /** @var Appointment $appointment */
        $appointment = $this->appointmentRepository->findWithoutFail($id);

        if (empty($appointment)) {
            return $this->sendError('Appointment not found');
        }

        $appointment = $this->appointmentRepository->update($input, $id);

        return $this->sendResponse($appointment->toArray(), 'Appointment updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/appointments/{id}",
     *      summary="Remove the specified Appointment from storage",
     *      tags={"Appointment"},
     *      description="Delete Appointment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Appointment",
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
        /** @var Appointment $appointment */
        $appointment = $this->appointmentRepository->findWithoutFail($id);

        if (empty($appointment)) {
            return $this->sendError('Appointment not found');
        }

        $appointment->delete();

        return $this->sendSuccess('Appointment deleted successfully');
    }
}
