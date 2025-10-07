<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateNotificationUserDayCheckAPIRequest;
use App\Http\Requests\API\UpdateNotificationUserDayCheckAPIRequest;
use App\Models\NotificationUserDayCheck;
use App\Repositories\NotificationUserDayCheckRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class NotificationUserDayCheckController
 * @package App\Http\Controllers\API
 */

class NotificationUserDayCheckAPIController extends AppBaseController
{
    /** @var  NotificationUserDayCheckRepository */
    private $notificationUserDayCheckRepository;

    public function __construct(NotificationUserDayCheckRepository $notificationUserDayCheckRepo)
    {
        $this->notificationUserDayCheckRepository = $notificationUserDayCheckRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/notificationUserDayChecks",
     *      summary="Get a listing of the NotificationUserDayChecks.",
     *      tags={"NotificationUserDayCheck"},
     *      description="Get all NotificationUserDayChecks",
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
     *                  @SWG\Items(ref="#/definitions/NotificationUserDayCheck")
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
        $this->notificationUserDayCheckRepository->pushCriteria(new RequestCriteria($request));
        $this->notificationUserDayCheckRepository->pushCriteria(new LimitOffsetCriteria($request));
        $notificationUserDayChecks = $this->notificationUserDayCheckRepository->all();

        return $this->sendResponse($notificationUserDayChecks->toArray(), trans('custom.notification_user_day_checks_retrieved_successfull'));
    }

    /**
     * @param CreateNotificationUserDayCheckAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/notificationUserDayChecks",
     *      summary="Store a newly created NotificationUserDayCheck in storage",
     *      tags={"NotificationUserDayCheck"},
     *      description="Store NotificationUserDayCheck",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="NotificationUserDayCheck that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/NotificationUserDayCheck")
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
     *                  ref="#/definitions/NotificationUserDayCheck"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateNotificationUserDayCheckAPIRequest $request)
    {
        $input = $request->all();

        $notificationUserDayCheck = $this->notificationUserDayCheckRepository->create($input);

        return $this->sendResponse($notificationUserDayCheck->toArray(), trans('custom.notification_user_day_check_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/notificationUserDayChecks/{id}",
     *      summary="Display the specified NotificationUserDayCheck",
     *      tags={"NotificationUserDayCheck"},
     *      description="Get NotificationUserDayCheck",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationUserDayCheck",
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
     *                  ref="#/definitions/NotificationUserDayCheck"
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
        /** @var NotificationUserDayCheck $notificationUserDayCheck */
        $notificationUserDayCheck = $this->notificationUserDayCheckRepository->findWithoutFail($id);

        if (empty($notificationUserDayCheck)) {
            return $this->sendError(trans('custom.notification_user_day_check_not_found'));
        }

        return $this->sendResponse($notificationUserDayCheck->toArray(), trans('custom.notification_user_day_check_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateNotificationUserDayCheckAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/notificationUserDayChecks/{id}",
     *      summary="Update the specified NotificationUserDayCheck in storage",
     *      tags={"NotificationUserDayCheck"},
     *      description="Update NotificationUserDayCheck",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationUserDayCheck",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="NotificationUserDayCheck that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/NotificationUserDayCheck")
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
     *                  ref="#/definitions/NotificationUserDayCheck"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateNotificationUserDayCheckAPIRequest $request)
    {
        $input = $request->all();

        /** @var NotificationUserDayCheck $notificationUserDayCheck */
        $notificationUserDayCheck = $this->notificationUserDayCheckRepository->findWithoutFail($id);

        if (empty($notificationUserDayCheck)) {
            return $this->sendError(trans('custom.notification_user_day_check_not_found'));
        }

        $notificationUserDayCheck = $this->notificationUserDayCheckRepository->update($input, $id);

        return $this->sendResponse($notificationUserDayCheck->toArray(), trans('custom.notificationuserdaycheck_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/notificationUserDayChecks/{id}",
     *      summary="Remove the specified NotificationUserDayCheck from storage",
     *      tags={"NotificationUserDayCheck"},
     *      description="Delete NotificationUserDayCheck",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationUserDayCheck",
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
        /** @var NotificationUserDayCheck $notificationUserDayCheck */
        $notificationUserDayCheck = $this->notificationUserDayCheckRepository->findWithoutFail($id);

        if (empty($notificationUserDayCheck)) {
            return $this->sendError(trans('custom.notification_user_day_check_not_found'));
        }

        $notificationUserDayCheck->delete();

        return $this->sendSuccess('Notification User Day Check deleted successfully');
    }
}
