<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLptPermissionRequest;
use App\Http\Requests\UpdateLptPermissionRequest;
use App\Repositories\LptPermissionRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class LptPermissionController extends AppBaseController
{
    /** @var  LptPermissionRepository */
    private $lptPermissionRepository;

    public function __construct(LptPermissionRepository $lptPermissionRepo)
    {
        $this->lptPermissionRepository = $lptPermissionRepo;
    }

    /**
     * Display a listing of the LptPermission.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->lptPermissionRepository->pushCriteria(new RequestCriteria($request));
        $lptPermissions = $this->lptPermissionRepository->all();

        return view('lpt_permissions.index')
            ->with('lptPermissions', $lptPermissions);
    }

    /**
     * Show the form for creating a new LptPermission.
     *
     * @return Response
     */
    public function create()
    {
        return view('lpt_permissions.create');
    }

    /**
     * Store a newly created LptPermission in storage.
     *
     * @param CreateLptPermissionRequest $request
     *
     * @return Response
     */
    public function store(CreateLptPermissionRequest $request)
    {
        $input = $request->all();

        $lptPermission = $this->lptPermissionRepository->create($input);

        Flash::success('Lpt Permission saved successfully.');

        return redirect(route('lptPermissions.index'));
    }

    /**
     * Display the specified LptPermission.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $lptPermission = $this->lptPermissionRepository->findWithoutFail($id);

        if (empty($lptPermission)) {
            Flash::error('Lpt Permission not found');

            return redirect(route('lptPermissions.index'));
        }

        return view('lpt_permissions.show')->with('lptPermission', $lptPermission);
    }

    /**
     * Show the form for editing the specified LptPermission.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $lptPermission = $this->lptPermissionRepository->findWithoutFail($id);

        if (empty($lptPermission)) {
            Flash::error('Lpt Permission not found');

            return redirect(route('lptPermissions.index'));
        }

        return view('lpt_permissions.edit')->with('lptPermission', $lptPermission);
    }

    /**
     * Update the specified LptPermission in storage.
     *
     * @param  int              $id
     * @param UpdateLptPermissionRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLptPermissionRequest $request)
    {
        $lptPermission = $this->lptPermissionRepository->findWithoutFail($id);

        if (empty($lptPermission)) {
            Flash::error('Lpt Permission not found');

            return redirect(route('lptPermissions.index'));
        }

        $lptPermission = $this->lptPermissionRepository->update($request->all(), $id);

        Flash::success('Lpt Permission updated successfully.');

        return redirect(route('lptPermissions.index'));
    }

    /**
     * Remove the specified LptPermission from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $lptPermission = $this->lptPermissionRepository->findWithoutFail($id);

        if (empty($lptPermission)) {
            Flash::error('Lpt Permission not found');

            return redirect(route('lptPermissions.index'));
        }

        $this->lptPermissionRepository->delete($id);

        Flash::success('Lpt Permission deleted successfully.');

        return redirect(route('lptPermissions.index'));
    }
}
