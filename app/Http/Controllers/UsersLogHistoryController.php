<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUsersLogHistoryRequest;
use App\Http\Requests\UpdateUsersLogHistoryRequest;
use App\Repositories\UsersLogHistoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class UsersLogHistoryController extends AppBaseController
{
    /** @var  UsersLogHistoryRepository */
    private $usersLogHistoryRepository;

    public function __construct(UsersLogHistoryRepository $usersLogHistoryRepo)
    {
        $this->usersLogHistoryRepository = $usersLogHistoryRepo;
    }

    /**
     * Display a listing of the UsersLogHistory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->usersLogHistoryRepository->pushCriteria(new RequestCriteria($request));
        $usersLogHistories = $this->usersLogHistoryRepository->all();

        return view('users_log_histories.index')
            ->with('usersLogHistories', $usersLogHistories);
    }

    /**
     * Show the form for creating a new UsersLogHistory.
     *
     * @return Response
     */
    public function create()
    {
        return view('users_log_histories.create');
    }

    /**
     * Store a newly created UsersLogHistory in storage.
     *
     * @param CreateUsersLogHistoryRequest $request
     *
     * @return Response
     */
    public function store(CreateUsersLogHistoryRequest $request)
    {
        $input = $request->all();

        $usersLogHistory = $this->usersLogHistoryRepository->create($input);

        Flash::success('Users Log History saved successfully.');

        return redirect(route('usersLogHistories.index'));
    }

    /**
     * Display the specified UsersLogHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $usersLogHistory = $this->usersLogHistoryRepository->findWithoutFail($id);

        if (empty($usersLogHistory)) {
            Flash::error('Users Log History not found');

            return redirect(route('usersLogHistories.index'));
        }

        return view('users_log_histories.show')->with('usersLogHistory', $usersLogHistory);
    }

    /**
     * Show the form for editing the specified UsersLogHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $usersLogHistory = $this->usersLogHistoryRepository->findWithoutFail($id);

        if (empty($usersLogHistory)) {
            Flash::error('Users Log History not found');

            return redirect(route('usersLogHistories.index'));
        }

        return view('users_log_histories.edit')->with('usersLogHistory', $usersLogHistory);
    }

    /**
     * Update the specified UsersLogHistory in storage.
     *
     * @param  int              $id
     * @param UpdateUsersLogHistoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUsersLogHistoryRequest $request)
    {
        $usersLogHistory = $this->usersLogHistoryRepository->findWithoutFail($id);

        if (empty($usersLogHistory)) {
            Flash::error('Users Log History not found');

            return redirect(route('usersLogHistories.index'));
        }

        $usersLogHistory = $this->usersLogHistoryRepository->update($request->all(), $id);

        Flash::success('Users Log History updated successfully.');

        return redirect(route('usersLogHistories.index'));
    }

    /**
     * Remove the specified UsersLogHistory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $usersLogHistory = $this->usersLogHistoryRepository->findWithoutFail($id);

        if (empty($usersLogHistory)) {
            Flash::error('Users Log History not found');

            return redirect(route('usersLogHistories.index'));
        }

        $this->usersLogHistoryRepository->delete($id);

        Flash::success('Users Log History deleted successfully.');

        return redirect(route('usersLogHistories.index'));
    }
}
