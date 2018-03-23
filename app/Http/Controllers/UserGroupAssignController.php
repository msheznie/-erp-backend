<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserGroupAssignRequest;
use App\Http\Requests\UpdateUserGroupAssignRequest;
use App\Repositories\UserGroupAssignRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class UserGroupAssignController extends AppBaseController
{
    /** @var  UserGroupAssignRepository */
    private $userGroupAssignRepository;

    public function __construct(UserGroupAssignRepository $userGroupAssignRepo)
    {
        $this->userGroupAssignRepository = $userGroupAssignRepo;
    }

    /**
     * Display a listing of the UserGroupAssign.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->userGroupAssignRepository->pushCriteria(new RequestCriteria($request));
        $userGroupAssigns = $this->userGroupAssignRepository->all();

        return view('user_group_assigns.index')
            ->with('userGroupAssigns', $userGroupAssigns);
    }

    /**
     * Show the form for creating a new UserGroupAssign.
     *
     * @return Response
     */
    public function create()
    {
        return view('user_group_assigns.create');
    }

    /**
     * Store a newly created UserGroupAssign in storage.
     *
     * @param CreateUserGroupAssignRequest $request
     *
     * @return Response
     */
    public function store(CreateUserGroupAssignRequest $request)
    {
        $input = $request->all();

        $userGroupAssign = $this->userGroupAssignRepository->create($input);

        Flash::success('User Group Assign saved successfully.');

        return redirect(route('userGroupAssigns.index'));
    }

    /**
     * Display the specified UserGroupAssign.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $userGroupAssign = $this->userGroupAssignRepository->findWithoutFail($id);

        if (empty($userGroupAssign)) {
            Flash::error('User Group Assign not found');

            return redirect(route('userGroupAssigns.index'));
        }

        return view('user_group_assigns.show')->with('userGroupAssign', $userGroupAssign);
    }

    /**
     * Show the form for editing the specified UserGroupAssign.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $userGroupAssign = $this->userGroupAssignRepository->findWithoutFail($id);

        if (empty($userGroupAssign)) {
            Flash::error('User Group Assign not found');

            return redirect(route('userGroupAssigns.index'));
        }

        return view('user_group_assigns.edit')->with('userGroupAssign', $userGroupAssign);
    }

    /**
     * Update the specified UserGroupAssign in storage.
     *
     * @param  int              $id
     * @param UpdateUserGroupAssignRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserGroupAssignRequest $request)
    {
        $userGroupAssign = $this->userGroupAssignRepository->findWithoutFail($id);

        if (empty($userGroupAssign)) {
            Flash::error('User Group Assign not found');

            return redirect(route('userGroupAssigns.index'));
        }

        $userGroupAssign = $this->userGroupAssignRepository->update($request->all(), $id);

        Flash::success('User Group Assign updated successfully.');

        return redirect(route('userGroupAssigns.index'));
    }

    /**
     * Remove the specified UserGroupAssign from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $userGroupAssign = $this->userGroupAssignRepository->findWithoutFail($id);

        if (empty($userGroupAssign)) {
            Flash::error('User Group Assign not found');

            return redirect(route('userGroupAssigns.index'));
        }

        $this->userGroupAssignRepository->delete($id);

        Flash::success('User Group Assign deleted successfully.');

        return redirect(route('userGroupAssigns.index'));
    }
}
