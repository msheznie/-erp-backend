<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRecurringVoucherSetupScheduleAPIRequest;
use App\Http\Requests\API\UpdateRecurringVoucherSetupScheduleAPIRequest;
use App\Models\RecurringVoucherSetupSchedule;
use App\Repositories\RecurringVoucherSetupScheduleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RecurringVoucherSetupScheduleController
 * @package App\Http\Controllers\API
 */

class RecurringVoucherSetupScheduleAPIController extends AppBaseController
{
    /** @var  RecurringVoucherSetupScheduleRepository */
    private $recurringVoucherSetupScheduleRepository;

    public function __construct(RecurringVoucherSetupScheduleRepository $recurringVoucherSetupScheduleRepo)
    {
        $this->recurringVoucherSetupScheduleRepository = $recurringVoucherSetupScheduleRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/recurringVoucherSetupSchedules",
     *      summary="getRecurringVoucherSetupScheduleList",
     *      tags={"RecurringVoucherSetupSchedule"},
     *      description="Get all RecurringVoucherSetupSchedules",
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
     *                  @OA\Items(ref="#/definitions/RecurringVoucherSetupSchedule")
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
        $this->recurringVoucherSetupScheduleRepository->pushCriteria(new RequestCriteria($request));
        $this->recurringVoucherSetupScheduleRepository->pushCriteria(new LimitOffsetCriteria($request));
        $recurringVoucherSetupSchedules = $this->recurringVoucherSetupScheduleRepository->all();

        return $this->sendResponse($recurringVoucherSetupSchedules->toArray(), 'Recurring Voucher Setup Schedules retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/recurringVoucherSetupSchedules",
     *      summary="createRecurringVoucherSetupSchedule",
     *      tags={"RecurringVoucherSetupSchedule"},
     *      description="Create RecurringVoucherSetupSchedule",
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
     *                  ref="#/definitions/RecurringVoucherSetupSchedule"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRecurringVoucherSetupScheduleAPIRequest $request)
    {
        $input = $request->all();

        $recurringVoucherSetupSchedule = $this->recurringVoucherSetupScheduleRepository->create($input);

        return $this->sendResponse($recurringVoucherSetupSchedule->toArray(), 'Recurring Voucher Setup Schedule saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/recurringVoucherSetupSchedules/{id}",
     *      summary="getRecurringVoucherSetupScheduleItem",
     *      tags={"RecurringVoucherSetupSchedule"},
     *      description="Get RecurringVoucherSetupSchedule",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetupSchedule",
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
     *                  ref="#/definitions/RecurringVoucherSetupSchedule"
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
        /** @var RecurringVoucherSetupSchedule $recurringVoucherSetupSchedule */
        $recurringVoucherSetupSchedule = $this->recurringVoucherSetupScheduleRepository->findWithoutFail($id);

        if (empty($recurringVoucherSetupSchedule)) {
            return $this->sendError('Recurring Voucher Setup Schedule not found');
        }

        return $this->sendResponse($recurringVoucherSetupSchedule->toArray(), 'Recurring Voucher Setup Schedule retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/recurringVoucherSetupSchedules/{id}",
     *      summary="updateRecurringVoucherSetupSchedule",
     *      tags={"RecurringVoucherSetupSchedule"},
     *      description="Update RecurringVoucherSetupSchedule",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetupSchedule",
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
     *                  ref="#/definitions/RecurringVoucherSetupSchedule"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRecurringVoucherSetupScheduleAPIRequest $request)
    {
        $input = $request->all();

        /** @var RecurringVoucherSetupSchedule $recurringVoucherSetupSchedule */
        $recurringVoucherSetupSchedule = $this->recurringVoucherSetupScheduleRepository->findWithoutFail($id);

        if (empty($recurringVoucherSetupSchedule)) {
            return $this->sendError('Recurring Voucher Setup Schedule not found');
        }

        $recurringVoucherSetupSchedule = $this->recurringVoucherSetupScheduleRepository->update($input, $id);

        return $this->sendResponse($recurringVoucherSetupSchedule->toArray(), 'RecurringVoucherSetupSchedule updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/recurringVoucherSetupSchedules/{id}",
     *      summary="deleteRecurringVoucherSetupSchedule",
     *      tags={"RecurringVoucherSetupSchedule"},
     *      description="Delete RecurringVoucherSetupSchedule",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RecurringVoucherSetupSchedule",
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
        /** @var RecurringVoucherSetupSchedule $recurringVoucherSetupSchedule */
        $recurringVoucherSetupSchedule = $this->recurringVoucherSetupScheduleRepository->findWithoutFail($id);

        if (empty($recurringVoucherSetupSchedule)) {
            return $this->sendError('Recurring Voucher Setup Schedule not found');
        }

        $recurringVoucherSetupSchedule->delete();

        return $this->sendSuccess('Recurring Voucher Setup Schedule deleted successfully');
    }
}
