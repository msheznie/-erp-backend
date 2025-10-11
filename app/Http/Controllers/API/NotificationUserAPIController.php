<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateNotificationUserAPIRequest;
use App\Http\Requests\API\UpdateNotificationUserAPIRequest;
use App\Models\NotificationUser;
use App\Repositories\NotificationUserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class NotificationUserController
 * @package App\Http\Controllers\API
 */

class NotificationUserAPIController extends AppBaseController
{
    /** @var  NotificationUserRepository */
    private $notificationUserRepository;

    public function __construct(NotificationUserRepository $notificationUserRepo)
    {
        $this->notificationUserRepository = $notificationUserRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/notificationUsers",
     *      summary="Get a listing of the NotificationUsers.",
     *      tags={"NotificationUser"},
     *      description="Get all NotificationUsers",
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
     *                  @SWG\Items(ref="#/definitions/NotificationUser")
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
        $this->notificationUserRepository->pushCriteria(new RequestCriteria($request));
        $this->notificationUserRepository->pushCriteria(new LimitOffsetCriteria($request));
        $notificationUsers = $this->notificationUserRepository->all();

        return $this->sendResponse($notificationUsers->toArray(), trans('custom.notification_users_retrieved_successfully'));
    }

    /**
     * @param CreateNotificationUserAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/notificationUsers",
     *      summary="Store a newly created NotificationUser in storage",
     *      tags={"NotificationUser"},
     *      description="Store NotificationUser",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="NotificationUser that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/NotificationUser")
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
     *                  ref="#/definitions/NotificationUser"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateNotificationUserAPIRequest $request)
    {
        $input = $request->all();

        $notificationUser = $this->notificationUserRepository->create($input);

        return $this->sendResponse($notificationUser->toArray(), trans('custom.notification_user_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/notificationUsers/{id}",
     *      summary="Display the specified NotificationUser",
     *      tags={"NotificationUser"},
     *      description="Get NotificationUser",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationUser",
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
     *                  ref="#/definitions/NotificationUser"
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
        /** @var NotificationUser $notificationUser */
        $notificationUser = $this->notificationUserRepository->findWithoutFail($id);

        if (empty($notificationUser)) {
            return $this->sendError(trans('custom.notification_user_not_found'));
        }

        return $this->sendResponse($notificationUser->toArray(), trans('custom.notification_user_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateNotificationUserAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/notificationUsers/{id}",
     *      summary="Update the specified NotificationUser in storage",
     *      tags={"NotificationUser"},
     *      description="Update NotificationUser",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationUser",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="NotificationUser that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/NotificationUser")
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
     *                  ref="#/definitions/NotificationUser"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateNotificationUserAPIRequest $request)
    {
        $input = $request->all();

        /** @var NotificationUser $notificationUser */
        $notificationUser = $this->notificationUserRepository->findWithoutFail($id);

        if (empty($notificationUser)) {
            return $this->sendError(trans('custom.notification_user_not_found'));
        }

        $notificationUser = $this->notificationUserRepository->update($input, $id);

        return $this->sendResponse($notificationUser->toArray(), trans('custom.notificationuser_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/notificationUsers/{id}",
     *      summary="Remove the specified NotificationUser from storage",
     *      tags={"NotificationUser"},
     *      description="Delete NotificationUser",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationUser",
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
        /** @var NotificationUser $notificationUser */
        $notificationUser = $this->notificationUserRepository->findWithoutFail($id);

        if (empty($notificationUser)) {
            return $this->sendError(trans('custom.notification_user_not_found'));
        }

        $notificationUser->delete();

        return $this->sendSuccess('Notification User deleted successfully');
    }
}
