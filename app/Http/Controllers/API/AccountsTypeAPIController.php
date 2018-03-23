<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAccountsTypeAPIRequest;
use App\Http\Requests\API\UpdateAccountsTypeAPIRequest;
use App\Models\AccountsType;
use App\Repositories\AccountsTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AccountsTypeController
 * @package App\Http\Controllers\API
 */

class AccountsTypeAPIController extends AppBaseController
{
    /** @var  AccountsTypeRepository */
    private $accountsTypeRepository;

    public function __construct(AccountsTypeRepository $accountsTypeRepo)
    {
        $this->accountsTypeRepository = $accountsTypeRepo;
    }

    /**
     * Display a listing of the AccountsType.
     * GET|HEAD /accountsTypes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->accountsTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->accountsTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $accountsTypes = $this->accountsTypeRepository->all();

        return $this->sendResponse($accountsTypes->toArray(), 'Accounts Types retrieved successfully');
    }

    /**
     * Store a newly created AccountsType in storage.
     * POST /accountsTypes
     *
     * @param CreateAccountsTypeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAccountsTypeAPIRequest $request)
    {
        $input = $request->all();

        $accountsTypes = $this->accountsTypeRepository->create($input);

        return $this->sendResponse($accountsTypes->toArray(), 'Accounts Type saved successfully');
    }

    /**
     * Display the specified AccountsType.
     * GET|HEAD /accountsTypes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var AccountsType $accountsType */
        $accountsType = $this->accountsTypeRepository->findWithoutFail($id);

        if (empty($accountsType)) {
            return $this->sendError('Accounts Type not found');
        }

        return $this->sendResponse($accountsType->toArray(), 'Accounts Type retrieved successfully');
    }

    /**
     * Update the specified AccountsType in storage.
     * PUT/PATCH /accountsTypes/{id}
     *
     * @param  int $id
     * @param UpdateAccountsTypeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccountsTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var AccountsType $accountsType */
        $accountsType = $this->accountsTypeRepository->findWithoutFail($id);

        if (empty($accountsType)) {
            return $this->sendError('Accounts Type not found');
        }

        $accountsType = $this->accountsTypeRepository->update($input, $id);

        return $this->sendResponse($accountsType->toArray(), 'AccountsType updated successfully');
    }

    /**
     * Remove the specified AccountsType from storage.
     * DELETE /accountsTypes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var AccountsType $accountsType */
        $accountsType = $this->accountsTypeRepository->findWithoutFail($id);

        if (empty($accountsType)) {
            return $this->sendError('Accounts Type not found');
        }

        $accountsType->delete();

        return $this->sendResponse($id, 'Accounts Type deleted successfully');
    }


}
