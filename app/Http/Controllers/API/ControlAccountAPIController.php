<?php
/**
 * =============================================
 * -- File Name : ControlAccountAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Control Account
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file contains the all CRUD for Control Account
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateControlAccountAPIRequest;
use App\Http\Requests\API\UpdateControlAccountAPIRequest;
use App\Models\ControlAccount;
use App\Repositories\ControlAccountRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
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
        $input = $request->all();
        $this->controlAccountRepository->pushCriteria(new RequestCriteria($request));
        $this->controlAccountRepository->pushCriteria(new LimitOffsetCriteria($request));
        $controlAccounts = $this->controlAccountRepository->all();

        if (isset($input['controlAccountCode']) && $input['controlAccountCode'] != "") {
            $controlAccounts = $this->controlAccountRepository->where('controlAccountCode', $input['controlAccountCode'])->get();
        }

        return $this->sendResponse($controlAccounts->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.control_accounts')]));
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

        return $this->sendResponse($controlAccounts->toArray(), trans('custom.save', ['attribute' => trans('custom.control_accounts')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.control_accounts')]));
        }

        return $this->sendResponse($controlAccount->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.control_accounts')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.control_accounts')]));
        }

        $controlAccount = $this->controlAccountRepository->update($input, $id);

        return $this->sendResponse($controlAccount->toArray(), trans('custom.update', ['attribute' => trans('custom.control_accounts')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.control_accounts')]));
        }

        $controlAccount->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.control_accounts')]));
    }
}
