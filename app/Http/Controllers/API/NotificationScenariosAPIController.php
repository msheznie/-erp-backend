<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateNotificationScenariosAPIRequest;
use App\Http\Requests\API\UpdateNotificationScenariosAPIRequest;
use App\Models\NotificationScenarios;
use App\Repositories\NotificationScenariosRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class NotificationScenariosController
 * @package App\Http\Controllers\API
 */

class NotificationScenariosAPIController extends AppBaseController
{
    /** @var  NotificationScenariosRepository */
    private $notificationScenariosRepository;

    public function __construct(NotificationScenariosRepository $notificationScenariosRepo)
    {
        $this->notificationScenariosRepository = $notificationScenariosRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/notificationScenarios",
     *      summary="Get a listing of the NotificationScenarios.",
     *      tags={"NotificationScenarios"},
     *      description="Get all NotificationScenarios",
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
     *                  @SWG\Items(ref="#/definitions/NotificationScenarios")
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
        $this->notificationScenariosRepository->pushCriteria(new RequestCriteria($request));
        $this->notificationScenariosRepository->pushCriteria(new LimitOffsetCriteria($request));
        $notificationScenarios = $this->notificationScenariosRepository->all();

        return $this->sendResponse($notificationScenarios->toArray(), trans('custom.notification_scenarios_retrieved_successfully'));
    }

    /**
     * @param CreateNotificationScenariosAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/notificationScenarios",
     *      summary="Store a newly created NotificationScenarios in storage",
     *      tags={"NotificationScenarios"},
     *      description="Store NotificationScenarios",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="NotificationScenarios that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/NotificationScenarios")
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
     *                  ref="#/definitions/NotificationScenarios"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateNotificationScenariosAPIRequest $request)
    {
        $input = $request->all();

        $notificationScenarios = $this->notificationScenariosRepository->create($input);

        return $this->sendResponse($notificationScenarios->toArray(), trans('custom.notification_scenarios_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/notificationScenarios/{id}",
     *      summary="Display the specified NotificationScenarios",
     *      tags={"NotificationScenarios"},
     *      description="Get NotificationScenarios",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationScenarios",
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
     *                  ref="#/definitions/NotificationScenarios"
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
        /** @var NotificationScenarios $notificationScenarios */
        $notificationScenarios = $this->notificationScenariosRepository->findWithoutFail($id);

        if (empty($notificationScenarios)) {
            return $this->sendError(trans('custom.notification_scenarios_not_found'));
        }

        return $this->sendResponse($notificationScenarios->toArray(), trans('custom.notification_scenarios_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateNotificationScenariosAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/notificationScenarios/{id}",
     *      summary="Update the specified NotificationScenarios in storage",
     *      tags={"NotificationScenarios"},
     *      description="Update NotificationScenarios",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationScenarios",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="NotificationScenarios that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/NotificationScenarios")
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
     *                  ref="#/definitions/NotificationScenarios"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateNotificationScenariosAPIRequest $request)
    {
        $input = $request->all();

        /** @var NotificationScenarios $notificationScenarios */
        $notificationScenarios = $this->notificationScenariosRepository->findWithoutFail($id);

        if (empty($notificationScenarios)) {
            return $this->sendError(trans('custom.notification_scenarios_not_found'));
        }

        $notificationScenarios = $this->notificationScenariosRepository->update($input, $id);

        return $this->sendResponse($notificationScenarios->toArray(), trans('custom.notificationscenarios_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/notificationScenarios/{id}",
     *      summary="Remove the specified NotificationScenarios from storage",
     *      tags={"NotificationScenarios"},
     *      description="Delete NotificationScenarios",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationScenarios",
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
        /** @var NotificationScenarios $notificationScenarios */
        $notificationScenarios = $this->notificationScenariosRepository->findWithoutFail($id);

        if (empty($notificationScenarios)) {
            return $this->sendError(trans('custom.notification_scenarios_not_found'));
        }

        $notificationScenarios->delete();

        return $this->sendSuccess('Notification Scenarios deleted successfully');
    }
}
