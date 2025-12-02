<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\BudgetNotification;
use App\Models\BudgetNotificationDetail;
use App\Models\BudgetNotificationRecipient;
use Illuminate\Http\Request;
use Response;

/**
 * Class DepartmentBudgetNotificationAPIController
 * @package App\Http\Controllers\API
 */
class DepartmentBudgetNotificationAPIController extends AppBaseController
{
    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/budgetNotifications",
     *      summary="getBudgetNotificationList",
     *      tags={"BudgetNotification"},
     *      description="Get all BudgetNotifications",
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
     *                  @OA\Items(ref="#/definitions/BudgetNotification")
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
        $budgetNotifications = BudgetNotification::with('notificationDetails')->get();

        return $this->sendResponse($budgetNotifications->toArray(), 'Budget Notifications retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/budgetNotifications",
     *      summary="createBudgetNotification",
     *      tags={"BudgetNotification"},
     *      description="Create BudgetNotification",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={"title", "purpose", "subject", "body"},
     *                @OA\Property(
     *                    property="title",
     *                    description="Notification title",
     *                    type="string"
     *                ),
     *                @OA\Property(
     *                    property="purpose",
     *                    description="Notification purpose",
     *                    type="string"
     *                ),
     *                @OA\Property(
     *                    property="recipient",
     *                    description="Recipient JSON",
     *                    type="string"
     *                ),
     *                @OA\Property(
     *                    property="subject",
     *                    description="Email subject",
     *                    type="string"
     *                ),
     *                @OA\Property(
     *                    property="body",
     *                    description="Email body",
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
     *                  ref="#/definitions/BudgetNotification"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, BudgetNotification::$rules);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', 422, $validator->errors());
        }

        if (isset($input['recipient']) && is_string($input['recipient'])) {
            $input['recipient'] = json_decode($input['recipient'], true);
        }

        $budgetNotification = BudgetNotification::create($input);

        return $this->sendResponse($budgetNotification->toArray(), 'Budget Notification saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/budgetNotifications/{id}",
     *      summary="getBudgetNotificationItem",
     *      tags={"BudgetNotification"},
     *      description="Get BudgetNotification",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BudgetNotification",
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
     *                  ref="#/definitions/BudgetNotification"
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
        /** @var BudgetNotification $budgetNotification */
        $budgetNotification = BudgetNotification::with('notificationDetails')->find($id);

        if (empty($budgetNotification)) {
            return $this->sendError('Budget Notification not found');
        }

        return $this->sendResponse($budgetNotification->toArray(), 'Budget Notification retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/budgetNotifications/{id}",
     *      summary="updateBudgetNotification",
     *      tags={"BudgetNotification"},
     *      description="Update BudgetNotification",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BudgetNotification",
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
     *                @OA\Property(
     *                    property="title",
     *                    description="Notification title",
     *                    type="string"
     *                ),
     *                @OA\Property(
     *                    property="purpose",
     *                    description="Notification purpose",
     *                    type="string"
     *                ),
     *                @OA\Property(
     *                    property="recipient",
     *                    description="Recipient JSON",
     *                    type="string"
     *                ),
     *                @OA\Property(
     *                    property="subject",
     *                    description="Email subject",
     *                    type="string"
     *                ),
     *                @OA\Property(
     *                    property="body",
     *                    description="Email body",
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
     *                  ref="#/definitions/BudgetNotification"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, Request $request)
    {
        $input = $request->all();

        /** @var BudgetNotification $budgetNotification */
        $budgetNotification = BudgetNotification::find($id);

        if (empty($budgetNotification)) {
            return $this->sendError('Budget Notification not found');
        }

        $validator = \Validator::make($input, [
            'title' => 'sometimes|required|string|max:255',
            'purpose' => 'sometimes|required|string',
            'subject' => 'sometimes|required|string',
            'body' => 'sometimes|required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', 422, $validator->errors());
        }

        if (isset($input['recipient']) && is_string($input['recipient'])) {
            $input['recipient'] = json_decode($input['recipient'], true);
        }

        $budgetNotification->fill($input);
        $budgetNotification->save();

        return $this->sendResponse($budgetNotification->toArray(), 'Budget Notification updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/budgetNotifications/{id}",
     *      summary="deleteBudgetNotification",
     *      tags={"BudgetNotification"},
     *      description="Delete BudgetNotification",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BudgetNotification",
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
        /** @var BudgetNotification $budgetNotification */
        $budgetNotification = BudgetNotification::find($id);

        if (empty($budgetNotification)) {
            return $this->sendError('Budget Notification not found');
        }

        $budgetNotification->delete();

        return $this->sendResponse($id, 'Budget Notification deleted successfully');
    }

    /**
     * Get all notification recipients
     *
     * @param Request $request
     * @return Response
     */
    public function getRecipients(Request $request)
    {
        $recipients = BudgetNotificationRecipient::all();

        return $this->sendResponse($recipients->toArray(), 'Notification Recipients retrieved successfully');
    }

    /**
     * Create notification recipient
     *
     * @param Request $request
     * @return Response
     */
    public function storeRecipient(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, BudgetNotificationRecipient::$rules);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', 422, $validator->errors());
        }

        $recipient = BudgetNotificationRecipient::create($input);

        return $this->sendResponse($recipient->toArray(), 'Notification Recipient saved successfully');
    }

    /**
     * Get notification details by notification ID
     *
     * @param int $notificationId
     * @return Response
     */
    public function getNotificationDetails($notificationId)
    {
        $details = BudgetNotificationDetail::where('notification_id', $notificationId)->get();

        return $this->sendResponse($details->toArray(), 'Notification Details retrieved successfully');
    }

    /**
     * Get all notification details with relationships for datatable
     *
     * @param Request $request
     * @return Response
     */
    public function getAllBudgetNotificationDetails(Request $request)
    {
        $companyId = $request->input('companyId');

        if ($companyId) {
            // Check if notification details exist for this company
            $existingCount = BudgetNotificationDetail::where('companySystemID', $companyId)->count();
            
            if ($existingCount == 0) {
                // Insert default notification details for this company
                $now = now();
                $kickOffNotifications = BudgetNotification::all();
                foreach($kickOffNotifications as $kickOffNotification){ 
                    if($kickOffNotification->slug == 'task-delegation' || $kickOffNotification->slug == 'deadline-warning'){
                        $reminderTime = 48;
                    }else{
                        $reminderTime = 0;
                    }
                    BudgetNotificationDetail::insert([
                        [
                            'notification_id' => $kickOffNotification->id,
                            'companySystemID' => $companyId,
                            'reminderTime' => $reminderTime,
                            'isActive' => true,
                        ]
                    ]);
                }
            }
        }

        $query = BudgetNotificationDetail::with('notification')
            ->whereHas('notification');

        if ($companyId) {
            $query->where('companySystemID', $companyId);
        }

        // Fetch all recipients to build a map for efficient lookup
        // This is more efficient than fetching per row
        $allRecipients = BudgetNotificationRecipient::all();
        $recipientsMap = [];
        foreach ($allRecipients as $recipient) {
            $recipientsMap[$recipient->id] = $recipient->title;
        }

        return \DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('event', function ($row) {
                return $row->notification ? $row->notification->title : '-';
            })
            ->addColumn('purpose', function ($row) {
                return $row->notification ? $row->notification->purpose : '-';
            })
            ->addColumn('recipients', function ($row) use ($recipientsMap) {
                $recipientTitles = [];
                if ($row->notification && $row->notification->recipient) {
                    $recipientIds = is_array($row->notification->recipient) 
                        ? $row->notification->recipient 
                        : json_decode($row->notification->recipient, true);
                    if (is_array($recipientIds)) {
                        foreach ($recipientIds as $recipientId) {
                            if (isset($recipientsMap[$recipientId])) {
                                $recipientTitles[] = $recipientsMap[$recipientId];
                            }
                        }
                    }
                }
                return !empty($recipientTitles) ? implode(' & ', $recipientTitles) : '-';
            })
            ->make(true);
    }

    /**
     * Create notification detail
     *
     * @param Request $request
     * @return Response
     */
    public function storeNotificationDetail(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, BudgetNotificationDetail::$rules);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', 422, $validator->errors());
        }

        $detail = BudgetNotificationDetail::create($input);

        return $this->sendResponse($detail->toArray(), 'Notification Detail saved successfully');
    }

    /**
     * Update notification detail
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function updateNotificationDetail($id, Request $request)
    {
        $input = $request->all();

        /** @var BudgetNotificationDetail $detail */
        $detail = BudgetNotificationDetail::find($id);

        if (empty($detail)) {
            return $this->sendError('Notification Detail not found');
        }

        $validator = \Validator::make($input, [
            'notification_id' => 'sometimes|required|integer',
            'companySystemID' => 'sometimes|required|integer',
            'reminderTime' => 'sometimes|required|integer',
            'isActive' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', 422, $validator->errors());
        }

        $detail->fill($input);
        $detail->save();

        return $this->sendResponse($detail->toArray(), 'Notification Detail updated successfully');
    }

    /**
     * Delete notification detail
     *
     * @param int $id
     * @return Response
     */
    public function destroyNotificationDetail($id)
    {
        /** @var BudgetNotificationDetail $detail */
        $detail = BudgetNotificationDetail::find($id);

        if (empty($detail)) {
            return $this->sendError('Notification Detail not found');
        }

        $detail->delete();

        return $this->sendResponse($id, 'Notification Detail deleted successfully');
    }
}

