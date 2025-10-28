<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
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

        return $this->sendResponse($userActivityLogs->toArray(), trans('custom.user_activity_logs_retrieved_successfully'));
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

        return $this->sendResponse($userActivityLog->toArray(), trans('custom.user_activity_log_saved_successfully'));
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
            return $this->sendError(trans('custom.user_activity_log_not_found'));
        }

        return $this->sendResponse($userActivityLog->toArray(), trans('custom.user_activity_log_retrieved_successfully'));
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
            return $this->sendError(trans('custom.user_activity_log_not_found'));
        }

        $userActivityLog = $this->userActivityLogRepository->update($input, $id);

        return $this->sendResponse($userActivityLog->toArray(), trans('custom.useractivitylog_updated_successfully'));
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
            return $this->sendError(trans('custom.user_activity_log_not_found'));
        }

        $userActivityLog->delete();

        return $this->sendResponse($id, trans('custom.user_activity_log_deleted_successfully'));
    }

    /*get All View Log*/

    public function getViewLog(Request $request){

        $input = $request->all();
        $companySystemID = $input['companySystemID'];
        $documentSystemID = $input['documentSystemID'];
        $autoID = isset($input['autoID'])?$input['autoID']:0;

        $isGroup = Helper::checkIsCompanyGroup($companySystemID);

        if ($isGroup) {
            $childCompanies = Helper::getGroupCompany($companySystemID);
        } else {
            $childCompanies = [$companySystemID];
        }

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $showColumn = $this->userActivityLogRepository->showColumnByDocumentSystemID($documentSystemID);

        $log = UserActivityLog::whereIn('company_id',$childCompanies)
            ->where('document_id',$documentSystemID)
            ->with('employee','document');

        if($autoID > 0){
            $log = $log->where('module_id',$autoID);
        }


        if(!empty($showColumn)){
            $log = $log->whereIn('column_name',$showColumn);
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $log = $log->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($log)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


}
