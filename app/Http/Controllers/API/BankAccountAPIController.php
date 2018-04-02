<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankAccountAPIRequest;
use App\Http\Requests\API\UpdateBankAccountAPIRequest;
use App\Models\BankAccount;
use App\Repositories\BankAccountRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BankAccountController
 * @package App\Http\Controllers\API
 */

class BankAccountAPIController extends AppBaseController
{
    /** @var  BankAccountRepository */
    private $bankAccountRepository;

    public function __construct(BankAccountRepository $bankAccountRepo)
    {
        $this->bankAccountRepository = $bankAccountRepo;
    }

    /**
     * Display a listing of the BankAccount.
     * GET|HEAD /bankAccounts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankAccountRepository->pushCriteria(new RequestCriteria($request));
        $this->bankAccountRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankAccounts = $this->bankAccountRepository->all();

        return $this->sendResponse($bankAccounts->toArray(), 'Bank Accounts retrieved successfully');
    }

    /**
     * Store a newly created BankAccount in storage.
     * POST /bankAccounts
     *
     * @param CreateBankAccountAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateBankAccountAPIRequest $request)
    {
        $input = $request->all();

        $bankAccounts = $this->bankAccountRepository->create($input);

        return $this->sendResponse($bankAccounts->toArray(), 'Bank Account saved successfully');
    }

    /**
     * Display the specified BankAccount.
     * GET|HEAD /bankAccounts/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var BankAccount $bankAccount */
        $bankAccount = $this->bankAccountRepository->findWithoutFail($id);

        if (empty($bankAccount)) {
            return $this->sendError('Bank Account not found');
        }

        return $this->sendResponse($bankAccount->toArray(), 'Bank Account retrieved successfully');
    }

    /**
     * Update the specified BankAccount in storage.
     * PUT/PATCH /bankAccounts/{id}
     *
     * @param  int $id
     * @param UpdateBankAccountAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankAccountAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankAccount $bankAccount */
        $bankAccount = $this->bankAccountRepository->findWithoutFail($id);

        if (empty($bankAccount)) {
            return $this->sendError('Bank Account not found');
        }

        $bankAccount = $this->bankAccountRepository->update($input, $id);

        return $this->sendResponse($bankAccount->toArray(), 'BankAccount updated successfully');
    }

    /**
     * Remove the specified BankAccount from storage.
     * DELETE /bankAccounts/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var BankAccount $bankAccount */
        $bankAccount = $this->bankAccountRepository->findWithoutFail($id);

        if (empty($bankAccount)) {
            return $this->sendError('Bank Account not found');
        }

        $bankAccount->delete();

        return $this->sendResponse($id, 'Bank Account deleted successfully');
    }
}
