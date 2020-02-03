<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRightsRequest;
use App\Http\Requests\UpdateUserRightsRequest;
use App\Repositories\UserRightsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class UserRightsController extends AppBaseController
{
    /** @var  UserRightsRepository */
    private $userRightsRepository;

    public function __construct(UserRightsRepository $userRightsRepo)
    {
        $this->userRightsRepository = $userRightsRepo;
    }

    /**
     * Display a listing of the UserRights.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->userRightsRepository->pushCriteria(new RequestCriteria($request));
        $userRights = $this->userRightsRepository->all();

        return view('user_rights.index')
            ->with('userRights', $userRights);
    }

    /**
     * Show the form for creating a new UserRights.
     *
     * @return Response
     */
    public function create()
    {
        return view('user_rights.create');
    }

    /**
     * Store a newly created UserRights in storage.
     *
     * @param CreateUserRightsRequest $request
     *
     * @return Response
     */
    public function store(CreateUserRightsRequest $request)
    {
        $input = $request->all();

        $userRights = $this->userRightsRepository->create($input);

        Flash::success('User Rights saved successfully.');

        return redirect(route('userRights.index'));
    }

    /**
     * Display the specified UserRights.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $userRights = $this->userRightsRepository->findWithoutFail($id);

        if (empty($userRights)) {
            Flash::error('User Rights not found');

            return redirect(route('userRights.index'));
        }

        return view('user_rights.show')->with('userRights', $userRights);
    }

    /**
     * Show the form for editing the specified UserRights.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $userRights = $this->userRightsRepository->findWithoutFail($id);

        if (empty($userRights)) {
            Flash::error('User Rights not found');

            return redirect(route('userRights.index'));
        }

        return view('user_rights.edit')->with('userRights', $userRights);
    }

    /**
     * Update the specified UserRights in storage.
     *
     * @param  int              $id
     * @param UpdateUserRightsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserRightsRequest $request)
    {
        $userRights = $this->userRightsRepository->findWithoutFail($id);

        if (empty($userRights)) {
            Flash::error('User Rights not found');

            return redirect(route('userRights.index'));
        }

        $userRights = $this->userRightsRepository->update($request->all(), $id);

        Flash::success('User Rights updated successfully.');

        return redirect(route('userRights.index'));
    }

    /**
     * Remove the specified UserRights from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $userRights = $this->userRightsRepository->findWithoutFail($id);

        if (empty($userRights)) {
            Flash::error('User Rights not found');

            return redirect(route('userRights.index'));
        }

        $this->userRightsRepository->delete($id);

        Flash::success('User Rights deleted successfully.');

        return redirect(route('userRights.index'));
    }
}
