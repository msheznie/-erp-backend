<?php

namespace App\Http\Controllers\API;

use App\helper\AdvancePaymentNotification;
use App\helper\HRContractNotificationService;
use App\helper\HRNotificationService;
use App\helper\HRProbationNotificationService;
use App\helper\NotificationService;
use App\helper\BudgetLimitNotification;
use App\helper\PurchaseOrderPendingDeliveryNotificationService;
use App\helper\RolReachedNotification;
use App\Http\Requests\API\CreateNotificationCompanyScenarioAPIRequest;
use App\Http\Requests\API\UpdateNotificationCompanyScenarioAPIRequest;
use App\Models\NotificationCompanyScenario;
use App\Repositories\NotificationCompanyScenarioRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Log;

/**
 * Class NotificationCompanyScenarioController
 * @package App\Http\Controllers\API
 */

class NotificationCompanyScenarioAPIController extends AppBaseController
{
    /** @var  NotificationCompanyScenarioRepository */
    private $notificationCompanyScenarioRepository;

    public function __construct(NotificationCompanyScenarioRepository $notificationCompanyScenarioRepo)
    {
        $this->notificationCompanyScenarioRepository = $notificationCompanyScenarioRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/notificationCompanyScenarios",
     *      summary="Get a listing of the NotificationCompanyScenarios.",
     *      tags={"NotificationCompanyScenario"},
     *      description="Get all NotificationCompanyScenarios",
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
     *                  @SWG\Items(ref="#/definitions/NotificationCompanyScenario")
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
        $this->notificationCompanyScenarioRepository->pushCriteria(new RequestCriteria($request));
        $this->notificationCompanyScenarioRepository->pushCriteria(new LimitOffsetCriteria($request));
        $notificationCompanyScenarios = $this->notificationCompanyScenarioRepository->all();

        return $this->sendResponse($notificationCompanyScenarios->toArray(), trans('custom.notification_company_scenarios_retrieved_successfu'));
    }

    /**
     * @param CreateNotificationCompanyScenarioAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/notificationCompanyScenarios",
     *      summary="Store a newly created NotificationCompanyScenario in storage",
     *      tags={"NotificationCompanyScenario"},
     *      description="Store NotificationCompanyScenario",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="NotificationCompanyScenario that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/NotificationCompanyScenario")
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
     *                  ref="#/definitions/NotificationCompanyScenario"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateNotificationCompanyScenarioAPIRequest $request)
    {
        $input = $request->all();

        $notificationCompanyScenario = $this->notificationCompanyScenarioRepository->create($input);

        return $this->sendResponse($notificationCompanyScenario->toArray(), trans('custom.notification_company_scenario_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/notificationCompanyScenarios/{id}",
     *      summary="Display the specified NotificationCompanyScenario",
     *      tags={"NotificationCompanyScenario"},
     *      description="Get NotificationCompanyScenario",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationCompanyScenario",
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
     *                  ref="#/definitions/NotificationCompanyScenario"
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
        /** @var NotificationCompanyScenario $notificationCompanyScenario */
        $notificationCompanyScenario = $this->notificationCompanyScenarioRepository->findWithoutFail($id);

        if (empty($notificationCompanyScenario)) {
            return $this->sendError(trans('custom.notification_company_scenario_not_found'));
        }

        return $this->sendResponse($notificationCompanyScenario->toArray(), trans('custom.notification_company_scenario_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param UpdateNotificationCompanyScenarioAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/notificationCompanyScenarios/{id}",
     *      summary="Update the specified NotificationCompanyScenario in storage",
     *      tags={"NotificationCompanyScenario"},
     *      description="Update NotificationCompanyScenario",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationCompanyScenario",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="NotificationCompanyScenario that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/NotificationCompanyScenario")
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
     *                  ref="#/definitions/NotificationCompanyScenario"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateNotificationCompanyScenarioAPIRequest $request)
    {
        $input = $request->all();

        /** @var NotificationCompanyScenario $notificationCompanyScenario */
        $notificationCompanyScenario = $this->notificationCompanyScenarioRepository->findWithoutFail($id);

        if (empty($notificationCompanyScenario)) {
            return $this->sendError(trans('custom.notification_company_scenario_not_found'));
        }

        $notificationCompanyScenario = $this->notificationCompanyScenarioRepository->update($input, $id);

        return $this->sendResponse($notificationCompanyScenario->toArray(), trans('custom.notificationcompanyscenario_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/notificationCompanyScenarios/{id}",
     *      summary="Remove the specified NotificationCompanyScenario from storage",
     *      tags={"NotificationCompanyScenario"},
     *      description="Delete NotificationCompanyScenario",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of NotificationCompanyScenario",
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
        /** @var NotificationCompanyScenario $notificationCompanyScenario */
        $notificationCompanyScenario = $this->notificationCompanyScenarioRepository->findWithoutFail($id);

        if (empty($notificationCompanyScenario)) {
            return $this->sendError(trans('custom.notification_company_scenario_not_found'));
        }

        $notificationCompanyScenario->delete();

        return $this->sendSuccess('Notification Company Scenario deleted successfully');
    }

    public function notification_service(Request $request)
    {
        $input = $request->all();

        $status = NotificationService::process($input['scenarioID']);

        return ['process'=> $status];
    }

    function job_check(){
        $log = DB::table('jobs')->get();

        foreach ($log as $row){
            $payload = json_decode($row->payload);
            $data = unserialize($payload->data->command);

            echo '<pre>'; print_r($data); echo '</pre>';
        }
    }
}
