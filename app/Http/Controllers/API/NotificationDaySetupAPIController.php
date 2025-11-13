<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateNotificationDaySetupAPIRequest;
use App\Http\Requests\API\UpdateNotificationDaySetupAPIRequest;
use App\Models\NotificationDaySetup;
use App\Repositories\NotificationDaySetupRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class NotificationDaySetupController
 * @package App\Http\Controllers\API
 */

class NotificationDaySetupAPIController extends AppBaseController
{
    /** @var  NotificationDaySetupRepository */
    private $notificationDaySetupRepository;

    public function __construct(NotificationDaySetupRepository $notificationDaySetupRepo)
    {
        $this->notificationDaySetupRepository = $notificationDaySetupRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/notificationDaySetups",
     *      summary="Get a listing of the NotificationDaySetups.",
     *      tags={"NotificationDaySetup"},
     *      description="Get all NotificationDaySetups",
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
     *                  @SWG\Items(ref="#/definitions/NotificationDaySetup")
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
        $this->notificationDaySetupRepository->pushCriteria(new RequestCriteria($request));
        $this->notificationDaySetupRepository->pushCriteria(new LimitOffsetCriteria($request));
        $notificationDaySetups = $this->notificationDaySetupRepository->all();

        return $this->sendResponse($notificationDaySetups->toArray(), trans('custom.notification_day_setups_retrieved_successfully'));
    }

    /**
     * @param CreateNotificationDaySetupAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/notificationDaySetups",
     *      summary="Store a newly created NotificationDaySetup in storage",
     *      tags={"NotificationDaySetup"},
     *      description="Store NotificationDaySetup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="NotificationDaySetup that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/NotificationDaySetup")
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
     *                  ref="#/definitions/NotificationDaySetup"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateNotificationDaySetupAPIRequest $request)
    {
        $input = $request->all();

        $notificationDaySetup = $this->notificationDaySetupRepository->create($input);

        return $this->sendResponse($notificationDaySetup->toArray(), trans('custom.notification_day_setup_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/notificationDaySetups/{id}",
     *      summary="Display the specified NotificationDaySetup",
     *      tags={"NotificationDaySetup"},
     *      description="Get NotificationDaySetup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationDaySetup",
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
     *                  ref="#/definitions/NotificationDaySetup"
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
        /** @var NotificationDaySetup $notificationDaySetup */
        $notificationDaySetup = $this->notificationDaySetupRepository->findWithoutFail($id);

        if (empty($notificationDaySetup)) {
            return $this->sendError(trans('custom.notification_day_setup_not_found'));
        }

        return $this->sendResponse($notificationDaySetup->toArray(), trans('custom.notification_day_setup_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateNotificationDaySetupAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/notificationDaySetups/{id}",
     *      summary="Update the specified NotificationDaySetup in storage",
     *      tags={"NotificationDaySetup"},
     *      description="Update NotificationDaySetup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationDaySetup",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="NotificationDaySetup that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/NotificationDaySetup")
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
     *                  ref="#/definitions/NotificationDaySetup"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateNotificationDaySetupAPIRequest $request)
    {
        $input = $request->all();

        /** @var NotificationDaySetup $notificationDaySetup */
        $notificationDaySetup = $this->notificationDaySetupRepository->findWithoutFail($id);

        if (empty($notificationDaySetup)) {
            return $this->sendError(trans('custom.notification_day_setup_not_found'));
        }

        $notificationDaySetup = $this->notificationDaySetupRepository->update($input, $id);

        return $this->sendResponse($notificationDaySetup->toArray(), trans('custom.notificationdaysetup_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/notificationDaySetups/{id}",
     *      summary="Remove the specified NotificationDaySetup from storage",
     *      tags={"NotificationDaySetup"},
     *      description="Delete NotificationDaySetup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationDaySetup",
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
        /** @var NotificationDaySetup $notificationDaySetup */
        $notificationDaySetup = $this->notificationDaySetupRepository->findWithoutFail($id);

        if (empty($notificationDaySetup)) {
            return $this->sendError(trans('custom.notification_day_setup_not_found'));
        }

        $notificationDaySetup->delete();

        return $this->sendSuccess('Notification Day Setup deleted successfully');
    }
}
