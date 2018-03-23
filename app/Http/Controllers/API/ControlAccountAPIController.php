<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateControlAccountAPIRequest;
use App\Http\Requests\API\UpdateControlAccountAPIRequest;
use App\Models\ControlAccount;
use App\Repositories\ControlAccountRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ControlAccountController
 * @package App\Http\Controllers\API
 */

class ControlAccountAPIController extends AppBaseController
{
    /** @var  ControlAccountRepository */
    private $controlAccountRepository;

    public function __construct(ControlAccountRepository $controlAccountRepo)
    {
        $this->controlAccountRepository = $controlAccountRepo;
    }

    /**
     * Display a listing of the ControlAccount.
     * GET|HEAD /controlAccounts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->controlAccountRepository->pushCriteria(new RequestCriteria($request));
        $this->controlAccountRepository->pushCriteria(new LimitOffsetCriteria($request));
        $controlAccounts = $this->controlAccountRepository->all();

        return $this->sendResponse($controlAccounts->toArray(), 'Control Accounts retrieved successfully');
    }

    /**
     * Store a newly created ControlAccount in storage.
     * POST /controlAccounts
     *
     * @param CreateControlAccountAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateControlAccountAPIRequest $request)
    {
        $input = $request->all();

        $controlAccounts = $this->controlAccountRepository->create($input);

        return $this->sendResponse($controlAccounts->toArray(), 'Control Account saved successfully');
    }

    /**
     * Display the specified ControlAccount.
     * GET|HEAD /controlAccounts/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ControlAccount $controlAccount */
        $controlAccount = $this->controlAccountRepository->findWithoutFail($id);

        if (empty($controlAccount)) {
            return $this->sendError('Control Account not found');
        }

        return $this->sendResponse($controlAccount->toArray(), 'Control Account retrieved successfully');
    }

    /**
     * Update the specified ControlAccount in storage.
     * PUT/PATCH /controlAccounts/{id}
     *
     * @param  int $id
     * @param UpdateControlAccountAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateControlAccountAPIRequest $request)
    {
        $input = $request->all();

        /** @var ControlAccount $controlAccount */
        $controlAccount = $this->controlAccountRepository->findWithoutFail($id);

        if (empty($controlAccount)) {
            return $this->sendError('Control Account not found');
        }

        $controlAccount = $this->controlAccountRepository->update($input, $id);

        return $this->sendResponse($controlAccount->toArray(), 'ControlAccount updated successfully');
    }

    /**
     * Remove the specified ControlAccount from storage.
     * DELETE /controlAccounts/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ControlAccount $controlAccount */
        $controlAccount = $this->controlAccountRepository->findWithoutFail($id);

        if (empty($controlAccount)) {
            return $this->sendError('Control Account not found');
        }

        $controlAccount->delete();

        return $this->sendResponse($id, 'Control Account deleted successfully');
    }
}
