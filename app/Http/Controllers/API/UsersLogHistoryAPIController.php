<?php
/**
 * =============================================
 * -- File Name : UsersLogHistoryAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Users Log History
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file contains the all CRUD for Users Log History
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUsersLogHistoryAPIRequest;
use App\Http\Requests\API\UpdateUsersLogHistoryAPIRequest;
use App\Models\UsersLogHistory;
use App\Repositories\UsersLogHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class UsersLogHistoryController
 * @package App\Http\Controllers\API
 */

class UsersLogHistoryAPIController extends AppBaseController
{
    /** @var  UsersLogHistoryRepository */
    private $usersLogHistoryRepository;

    public function __construct(UsersLogHistoryRepository $usersLogHistoryRepo)
    {
        $this->usersLogHistoryRepository = $usersLogHistoryRepo;
    }

    /**
     * Display a listing of the UsersLogHistory.
     * GET|HEAD /usersLogHistories
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->usersLogHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->usersLogHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $usersLogHistories = $this->usersLogHistoryRepository->all();

        return $this->sendResponse($usersLogHistories->toArray(), trans('custom.users_log_histories_retrieved_successfully'));
    }

    /**
     * Store a newly created UsersLogHistory in storage.
     * POST /usersLogHistories
     *
     * @param CreateUsersLogHistoryAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateUsersLogHistoryAPIRequest $request)
    {
        $input = $request->all();

        $usersLogHistories = $this->usersLogHistoryRepository->create($input);

        return $this->sendResponse($usersLogHistories->toArray(), trans('custom.users_log_history_saved_successfully'));
    }

    /**
     * Display the specified UsersLogHistory.
     * GET|HEAD /usersLogHistories/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var UsersLogHistory $usersLogHistory */
        $usersLogHistory = $this->usersLogHistoryRepository->findWithoutFail($id);

        if (empty($usersLogHistory)) {
            return $this->sendError(trans('custom.users_log_history_not_found'));
        }

        return $this->sendResponse($usersLogHistory->toArray(), trans('custom.users_log_history_retrieved_successfully'));
    }

    /**
     * Update the specified UsersLogHistory in storage.
     * PUT/PATCH /usersLogHistories/{id}
     *
     * @param  int $id
     * @param UpdateUsersLogHistoryAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUsersLogHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var UsersLogHistory $usersLogHistory */
        $usersLogHistory = $this->usersLogHistoryRepository->findWithoutFail($id);

        if (empty($usersLogHistory)) {
            return $this->sendError(trans('custom.users_log_history_not_found'));
        }

        $usersLogHistory = $this->usersLogHistoryRepository->update($input, $id);

        return $this->sendResponse($usersLogHistory->toArray(), trans('custom.usersloghistory_updated_successfully'));
    }

    /**
     * Remove the specified UsersLogHistory from storage.
     * DELETE /usersLogHistories/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var UsersLogHistory $usersLogHistory */
        $usersLogHistory = $this->usersLogHistoryRepository->findWithoutFail($id);

        if (empty($usersLogHistory)) {
            return $this->sendError(trans('custom.users_log_history_not_found'));
        }

        $usersLogHistory->delete();

        return $this->sendResponse($id, trans('custom.users_log_history_deleted_successfully'));
    }
}
