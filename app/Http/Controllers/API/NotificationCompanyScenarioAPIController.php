<?php

namespace App\Http\Controllers\API;

use App\helper\AdvancePaymentNotification;
use App\helper\HRNotificationService;
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
use App\Models\NotificationUserDayCheck;
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

        return $this->sendResponse($notificationCompanyScenarios->toArray(), 'Notification Company Scenarios retrieved successfully');
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

        return $this->sendResponse($notificationCompanyScenario->toArray(), 'Notification Company Scenario saved successfully');
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
            return $this->sendError('Notification Company Scenario not found');
        }

        return $this->sendResponse($notificationCompanyScenario->toArray(), 'Notification Company Scenario retrieved successfully');
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
            return $this->sendError('Notification Company Scenario not found');
        }

        $notificationCompanyScenario = $this->notificationCompanyScenarioRepository->update($input, $id);

        return $this->sendResponse($notificationCompanyScenario->toArray(), 'NotificationCompanyScenario updated successfully');
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
            return $this->sendError('Notification Company Scenario not found');
        }

        $notificationCompanyScenario->delete();

        return $this->sendSuccess('Notification Company Scenario deleted successfully');
    }

    public function notificatioService(Request $request)
    {

        $input = $request->all();
        $companyAssignScenarion = NotificationService::getCompanyScenarioConfiguration($input['scenarioID']); 
        $test = [];
        $details = [];
        $emailContent = [];
        $subject = 'N/A';

        //dd( $companyAssignScenarion->toArray() );

        if (count($companyAssignScenarion) > 0) {
            Log::useFiles(storage_path() . '/logs/notification_service.log');
            Log::info('------------ Successfully start ' . $companyAssignScenarion[0]->notification_scenario->scenarioDescription . ' Service ' . date('H:i:s') .  ' ------------');
            foreach ($companyAssignScenarion as $compAssignScenario) {
                Log::info('Company Name: ' . $compAssignScenario->company->CompanyName);
                if (count($compAssignScenario->notification_day_setup) > 0) {

                    $companyID = $compAssignScenario->companyID;

                    foreach ($compAssignScenario->notification_day_setup as $notDaySetup) {
                        $beforeAfter = $notDaySetup->beforeAfter;
                        $days = $notDaySetup->days;

                        switch ($input['scenarioID']) {
                            case 1:
                                $details = RolReachedNotification::getRolReachedNotification($companyID, $beforeAfter);
                                $subject = 'Inventory stock reaches a minimum order level';
                                break;
                            case 2:
                                $details = PurchaseOrderPendingDeliveryNotificationService::getPurchaseOrderPendingDelivery($companyID, $beforeAfter, $days, 1);
                                $subject = 'Purchase order pending delivery notification';
                                break;
                            case 3:
                                $details = PurchaseOrderPendingDeliveryNotificationService::getPurchaseOrderPendingDelivery($companyID, $beforeAfter, $days, 5);
                                $subject = 'Work order expiry notification';
                                break;
                            case 4:
                                $details = AdvancePaymentNotification::getadvancePaymentDetails($companyID, $beforeAfter, $days);
                                $subject = 'Advance Payment Notification';
                                break;
                            case 5:
                                $details = BudgetLimitNotification::getBudgetLimitDetails($companyID, $beforeAfter);
                                $subject = 'Budget Limit Notification';
                                break;

                            case 6:
                                $details = HRNotificationService::expired_docs($compAssignScenario, $beforeAfter, $days);
                                $subject = 'HR document expiry Notification';
                                break;

                            default:
                                Log::error('Applicable category configuration not exist');
                                break;
                        }


                        if (count($details) > 0) {
                            $notificationUserSettings = NotificationService::notificationUserSettings($notDaySetup->id);
                            if (count($notificationUserSettings['email']) > 0) {
                                foreach ($notificationUserSettings['email'] as $key => $notificationUserVal) {
                                    switch ($input['scenarioID']) {
                                        case 1:
                                            $emailContent = RolReachedNotification::getRolReachedEmailContent($details, $notificationUserVal[$key]['empName']);
                                            break;
                                        case 2:
                                            $emailContent = PurchaseOrderPendingDeliveryNotificationService::getPurchaseOrderEmailContent($details, $notificationUserVal[$key]['empName'], 1);
                                            break;
                                        case 3:
                                            $emailContent = PurchaseOrderPendingDeliveryNotificationService::getPurchaseOrderEmailContent($details, $notificationUserVal[$key]['empName'], 5);
                                            break;
                                        case 4:
                                            $emailContent = AdvancePaymentNotification::getAdvancePaymentEmailContent($details, $notificationUserVal[$key]['empName']);
                                            break;
                                        case 5:
                                            $emailContent = BudgetLimitNotification::getEmailContent($details, $notificationUserVal[$key]['empName']);
                                            break;
                                        default:
                                            Log::error('Email content configuration not done');
                                            break;
                                    }
                                    $sendEmail = NotificationService::emailNotification($companyID, $subject, $notificationUserVal[$key]['empEmail'], $emailContent);
                                    if (!$sendEmail["success"]) {
                                        Log::error($sendEmail["message"]);
                                    }
                                }
                            } else {
                                Log::info('No records found');
                            }
                        }
                        else {
                            Log::info('No records found');
                        }
                    }
                }
                else {
                    Log::info('Notification day setup not exist');
                }
            }
            Log::info('------------ Successfully end ' . $companyAssignScenarion[0]->notification_scenario->scenarioDescription . ' Service' . date('H:i:s') . ' ------------');
        } else {
            Log::info('Notification Company Scenario not exist');
        }
        
    }
}
