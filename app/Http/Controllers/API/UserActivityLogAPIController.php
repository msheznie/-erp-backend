<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUserActivityLogAPIRequest;
use App\Http\Requests\API\UpdateUserActivityLogAPIRequest;
use App\Models\UserActivityLog;
use App\Repositories\UserActivityLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class UserActivityLogController
 * @package App\Http\Controllers\API
 */

class UserActivityLogAPIController extends AppBaseController
{
    /** @var  UserActivityLogRepository */
    private $userActivityLogRepository;

    public function __construct(UserActivityLogRepository $userActivityLogRepo)
    {
        $this->userActivityLogRepository = $userActivityLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/userActivityLogs",
     *      summary="Get a listing of the UserActivityLogs.",
     *      tags={"UserActivityLog"},
     *      description="Get all UserActivityLogs",
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
     *                  @SWG\Items(ref="#/definitions/UserActivityLog")
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
        $this->userActivityLogRepository->pushCriteria(new RequestCriteria($request));
        $this->userActivityLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $userActivityLogs = $this->userActivityLogRepository->all();

        return $this->sendResponse($userActivityLogs->toArray(), 'User Activity Logs retrieved successfully');
    }

    /**
     * @param CreateUserActivityLogAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/userActivityLogs",
     *      summary="Store a newly created UserActivityLog in storage",
     *      tags={"UserActivityLog"},
     *      description="Store UserActivityLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserActivityLog that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserActivityLog")
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
     *                  ref="#/definitions/UserActivityLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateUserActivityLogAPIRequest $request)
    {
        $input = $request->all();

        $userActivityLog = $this->userActivityLogRepository->create($input);

        return $this->sendResponse($userActivityLog->toArray(), 'User Activity Log saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/userActivityLogs/{id}",
     *      summary="Display the specified UserActivityLog",
     *      tags={"UserActivityLog"},
     *      description="Get UserActivityLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserActivityLog",
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
     *                  ref="#/definitions/UserActivityLog"
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
        /** @var UserActivityLog $userActivityLog */
        $userActivityLog = $this->userActivityLogRepository->findWithoutFail($id);

        if (empty($userActivityLog)) {
            return $this->sendError('User Activity Log not found');
        }

        return $this->sendResponse($userActivityLog->toArray(), 'User Activity Log retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateUserActivityLogAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/userActivityLogs/{id}",
     *      summary="Update the specified UserActivityLog in storage",
     *      tags={"UserActivityLog"},
     *      description="Update UserActivityLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserActivityLog",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserActivityLog that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserActivityLog")
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
     *                  ref="#/definitions/UserActivityLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateUserActivityLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var UserActivityLog $userActivityLog */
        $userActivityLog = $this->userActivityLogRepository->findWithoutFail($id);

        if (empty($userActivityLog)) {
            return $this->sendError('User Activity Log not found');
        }

        $userActivityLog = $this->userActivityLogRepository->update($input, $id);

        return $this->sendResponse($userActivityLog->toArray(), 'UserActivityLog updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/userActivityLogs/{id}",
     *      summary="Remove the specified UserActivityLog from storage",
     *      tags={"UserActivityLog"},
     *      description="Delete UserActivityLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserActivityLog",
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
        /** @var UserActivityLog $userActivityLog */
        $userActivityLog = $this->userActivityLogRepository->findWithoutFail($id);

        if (empty($userActivityLog)) {
            return $this->sendError('User Activity Log not found');
        }

        $userActivityLog->delete();

        return $this->sendResponse($id, 'User Activity Log deleted successfully');
    }
}
