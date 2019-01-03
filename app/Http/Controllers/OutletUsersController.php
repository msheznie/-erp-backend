<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOutletUsersRequest;
use App\Http\Requests\UpdateOutletUsersRequest;
use App\Repositories\OutletUsersRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class OutletUsersController extends AppBaseController
{
    /** @var  OutletUsersRepository */
    private $outletUsersRepository;

    public function __construct(OutletUsersRepository $outletUsersRepo)
    {
        $this->outletUsersRepository = $outletUsersRepo;
    }

    /**
     * Display a listing of the OutletUsers.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->outletUsersRepository->pushCriteria(new RequestCriteria($request));
        $outletUsers = $this->outletUsersRepository->all();

        return view('outlet_users.index')
            ->with('outletUsers', $outletUsers);
    }

    /**
     * Show the form for creating a new OutletUsers.
     *
     * @return Response
     */
    public function create()
    {
        return view('outlet_users.create');
    }

    /**
     * Store a newly created OutletUsers in storage.
     *
     * @param CreateOutletUsersRequest $request
     *
     * @return Response
     */
    public function store(CreateOutletUsersRequest $request)
    {
        $input = $request->all();

        $outletUsers = $this->outletUsersRepository->create($input);

        Flash::success('Outlet Users saved successfully.');

        return redirect(route('outletUsers.index'));
    }

    /**
     * Display the specified OutletUsers.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $outletUsers = $this->outletUsersRepository->findWithoutFail($id);

        if (empty($outletUsers)) {
            Flash::error('Outlet Users not found');

            return redirect(route('outletUsers.index'));
        }

        return view('outlet_users.show')->with('outletUsers', $outletUsers);
    }

    /**
     * Show the form for editing the specified OutletUsers.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $outletUsers = $this->outletUsersRepository->findWithoutFail($id);

        if (empty($outletUsers)) {
            Flash::error('Outlet Users not found');

            return redirect(route('outletUsers.index'));
        }

        return view('outlet_users.edit')->with('outletUsers', $outletUsers);
    }

    /**
     * Update the specified OutletUsers in storage.
     *
     * @param  int              $id
     * @param UpdateOutletUsersRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateOutletUsersRequest $request)
    {
        $outletUsers = $this->outletUsersRepository->findWithoutFail($id);

        if (empty($outletUsers)) {
            Flash::error('Outlet Users not found');

            return redirect(route('outletUsers.index'));
        }

        $outletUsers = $this->outletUsersRepository->update($request->all(), $id);

        Flash::success('Outlet Users updated successfully.');

        return redirect(route('outletUsers.index'));
    }

    /**
     * Remove the specified OutletUsers from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $outletUsers = $this->outletUsersRepository->findWithoutFail($id);

        if (empty($outletUsers)) {
            Flash::error('Outlet Users not found');

            return redirect(route('outletUsers.index'));
        }

        $this->outletUsersRepository->delete($id);

        Flash::success('Outlet Users deleted successfully.');

        return redirect(route('outletUsers.index'));
    }
}
