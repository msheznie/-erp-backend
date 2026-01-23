<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAccountsPayableLedgerAPIRequest;
use App\Http\Requests\API\UpdateAccountsPayableLedgerAPIRequest;
use App\Models\AccountsPayableLedger;
use App\Repositories\AccountsPayableLedgerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AccountsPayableLedgerController
 * @package App\Http\Controllers\API
 */

class AccountsPayableLedgerAPIController extends AppBaseController
{
    /** @var  AccountsPayableLedgerRepository */
    private $accountsPayableLedgerRepository;

    public function __construct(AccountsPayableLedgerRepository $accountsPayableLedgerRepo)
    {
        $this->accountsPayableLedgerRepository = $accountsPayableLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/accountsPayableLedgers",
     *      summary="Get a listing of the AccountsPayableLedgers.",
     *      tags={"AccountsPayableLedger"},
     *      description="Get all AccountsPayableLedgers",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/AccountsPayableLedger")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->accountsPayableLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->accountsPayableLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $accountsPayableLedgers = $this->accountsPayableLedgerRepository->all();

        return $this->sendResponse($accountsPayableLedgers->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.acc_payable_ledger')]));
    }

    /**
     * @param CreateAccountsPayableLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/accountsPayableLedgers",
     *      summary="Store a newly created AccountsPayableLedger in storage",
     *      tags={"AccountsPayableLedger"},
     *      description="Store AccountsPayableLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AccountsPayableLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AccountsPayableLedger")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/AccountsPayableLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAccountsPayableLedgerAPIRequest $request)
    {
        $input = $request->all();

        $accountsPayableLedgers = $this->accountsPayableLedgerRepository->create($input);

        return $this->sendResponse($accountsPayableLedgers->toArray(), trans('custom.save', ['attribute' => trans('custom.acc_payable_ledger')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/accountsPayableLedgers/{id}",
     *      summary="Display the specified AccountsPayableLedger",
     *      tags={"AccountsPayableLedger"},
     *      description="Get AccountsPayableLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AccountsPayableLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/AccountsPayableLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var AccountsPayableLedger $accountsPayableLedger */
        $accountsPayableLedger = $this->accountsPayableLedgerRepository->findWithoutFail($id);

        if (empty($accountsPayableLedger)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.acc_payable_ledger')]));
        }

        return $this->sendResponse($accountsPayableLedger->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.acc_payable_ledger')]));
    }

    /**
     * @param int $id
     * @param UpdateAccountsPayableLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/accountsPayableLedgers/{id}",
     *      summary="Update the specified AccountsPayableLedger in storage",
     *      tags={"AccountsPayableLedger"},
     *      description="Update AccountsPayableLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AccountsPayableLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AccountsPayableLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AccountsPayableLedger")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/AccountsPayableLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAccountsPayableLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var AccountsPayableLedger $accountsPayableLedger */
        $accountsPayableLedger = $this->accountsPayableLedgerRepository->findWithoutFail($id);

        if (empty($accountsPayableLedger)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.acc_payable_ledger')]));
        }

        $accountsPayableLedger = $this->accountsPayableLedgerRepository->update($input, $id);

        return $this->sendResponse($accountsPayableLedger->toArray(), trans('custom.update', ['attribute' => trans('custom.acc_payable_ledger')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/accountsPayableLedgers/{id}",
     *      summary="Remove the specified AccountsPayableLedger from storage",
     *      tags={"AccountsPayableLedger"},
     *      description="Delete AccountsPayableLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AccountsPayableLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var AccountsPayableLedger $accountsPayableLedger */
        $accountsPayableLedger = $this->accountsPayableLedgerRepository->findWithoutFail($id);

        if (empty($accountsPayableLedger)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.acc_payable_ledger')]));
        }

        $accountsPayableLedger->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.acc_payable_ledger')]));
    }
}
