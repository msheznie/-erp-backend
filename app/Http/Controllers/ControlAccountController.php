<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateControlAccountRequest;
use App\Http\Requests\UpdateControlAccountRequest;
use App\Repositories\ControlAccountRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ControlAccountController extends AppBaseController
{
    /** @var  ControlAccountRepository */
    private $controlAccountRepository;

    public function __construct(ControlAccountRepository $controlAccountRepo)
    {
        $this->controlAccountRepository = $controlAccountRepo;
    }

    /**
     * Display a listing of the ControlAccount.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->controlAccountRepository->pushCriteria(new RequestCriteria($request));
        $controlAccounts = $this->controlAccountRepository->all();

        return view('control_accounts.index')
            ->with('controlAccounts', $controlAccounts);
    }

    /**
     * Show the form for creating a new ControlAccount.
     *
     * @return Response
     */
    public function create()
    {
        return view('control_accounts.create');
    }

    /**
     * Store a newly created ControlAccount in storage.
     *
     * @param CreateControlAccountRequest $request
     *
     * @return Response
     */
    public function store(CreateControlAccountRequest $request)
    {
        $input = $request->all();

        $controlAccount = $this->controlAccountRepository->create($input);

        Flash::success('Control Account saved successfully.');

        return redirect(route('controlAccounts.index'));
    }

    /**
     * Display the specified ControlAccount.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $controlAccount = $this->controlAccountRepository->findWithoutFail($id);

        if (empty($controlAccount)) {
            Flash::error('Control Account not found');

            return redirect(route('controlAccounts.index'));
        }

        return view('control_accounts.show')->with('controlAccount', $controlAccount);
    }

    /**
     * Show the form for editing the specified ControlAccount.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $controlAccount = $this->controlAccountRepository->findWithoutFail($id);

        if (empty($controlAccount)) {
            Flash::error('Control Account not found');

            return redirect(route('controlAccounts.index'));
        }

        return view('control_accounts.edit')->with('controlAccount', $controlAccount);
    }

    /**
     * Update the specified ControlAccount in storage.
     *
     * @param  int              $id
     * @param UpdateControlAccountRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateControlAccountRequest $request)
    {
        $controlAccount = $this->controlAccountRepository->findWithoutFail($id);

        if (empty($controlAccount)) {
            Flash::error('Control Account not found');

            return redirect(route('controlAccounts.index'));
        }

        $controlAccount = $this->controlAccountRepository->update($request->all(), $id);

        Flash::success('Control Account updated successfully.');

        return redirect(route('controlAccounts.index'));
    }

    /**
     * Remove the specified ControlAccount from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $controlAccount = $this->controlAccountRepository->findWithoutFail($id);

        if (empty($controlAccount)) {
            Flash::error('Control Account not found');

            return redirect(route('controlAccounts.index'));
        }

        $this->controlAccountRepository->delete($id);

        Flash::success('Control Account deleted successfully.');

        return redirect(route('controlAccounts.index'));
    }
}
