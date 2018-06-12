<?php
/**
 * =============================================
 * -- File Name : AccountsReceivableLedgerAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Accounts Receivable
 * -- Author : Mubashir
 * -- Create date : 12 - June 2018
 * -- Description : This file contains the all CRUD for Accounts receivable ledger
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAccountsReceivableLedgerAPIRequest;
use App\Http\Requests\API\UpdateAccountsReceivableLedgerAPIRequest;
use App\Models\AccountsReceivableLedger;
use App\Repositories\AccountsReceivableLedgerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AccountsReceivableLedgerController
 * @package App\Http\Controllers\API
 */

class AccountsReceivableLedgerAPIController extends AppBaseController
{
    /** @var  AccountsReceivableLedgerRepository */
    private $accountsReceivableLedgerRepository;

    public function __construct(AccountsReceivableLedgerRepository $accountsReceivableLedgerRepo)
    {
        $this->accountsReceivableLedgerRepository = $accountsReceivableLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/accountsReceivableLedgers",
     *      summary="Get a listing of the AccountsReceivableLedgers.",
     *      tags={"AccountsReceivableLedger"},
     *      description="Get all AccountsReceivableLedgers",
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
     *                  @SWG\Items(ref="#/definitions/AccountsReceivableLedger")
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
        $this->accountsReceivableLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->accountsReceivableLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $accountsReceivableLedgers = $this->accountsReceivableLedgerRepository->all();

        return $this->sendResponse($accountsReceivableLedgers->toArray(), 'Accounts Receivable Ledgers retrieved successfully');
    }

    /**
     * @param CreateAccountsReceivableLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/accountsReceivableLedgers",
     *      summary="Store a newly created AccountsReceivableLedger in storage",
     *      tags={"AccountsReceivableLedger"},
     *      description="Store AccountsReceivableLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AccountsReceivableLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AccountsReceivableLedger")
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
     *                  ref="#/definitions/AccountsReceivableLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAccountsReceivableLedgerAPIRequest $request)
    {
        $input = $request->all();

        $accountsReceivableLedgers = $this->accountsReceivableLedgerRepository->create($input);

        return $this->sendResponse($accountsReceivableLedgers->toArray(), 'Accounts Receivable Ledger saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/accountsReceivableLedgers/{id}",
     *      summary="Display the specified AccountsReceivableLedger",
     *      tags={"AccountsReceivableLedger"},
     *      description="Get AccountsReceivableLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AccountsReceivableLedger",
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
     *                  ref="#/definitions/AccountsReceivableLedger"
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
        /** @var AccountsReceivableLedger $accountsReceivableLedger */
        $accountsReceivableLedger = $this->accountsReceivableLedgerRepository->findWithoutFail($id);

        if (empty($accountsReceivableLedger)) {
            return $this->sendError('Accounts Receivable Ledger not found');
        }

        return $this->sendResponse($accountsReceivableLedger->toArray(), 'Accounts Receivable Ledger retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAccountsReceivableLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/accountsReceivableLedgers/{id}",
     *      summary="Update the specified AccountsReceivableLedger in storage",
     *      tags={"AccountsReceivableLedger"},
     *      description="Update AccountsReceivableLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AccountsReceivableLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AccountsReceivableLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AccountsReceivableLedger")
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
     *                  ref="#/definitions/AccountsReceivableLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAccountsReceivableLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var AccountsReceivableLedger $accountsReceivableLedger */
        $accountsReceivableLedger = $this->accountsReceivableLedgerRepository->findWithoutFail($id);

        if (empty($accountsReceivableLedger)) {
            return $this->sendError('Accounts Receivable Ledger not found');
        }

        $accountsReceivableLedger = $this->accountsReceivableLedgerRepository->update($input, $id);

        return $this->sendResponse($accountsReceivableLedger->toArray(), 'AccountsReceivableLedger updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/accountsReceivableLedgers/{id}",
     *      summary="Remove the specified AccountsReceivableLedger from storage",
     *      tags={"AccountsReceivableLedger"},
     *      description="Delete AccountsReceivableLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AccountsReceivableLedger",
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
        /** @var AccountsReceivableLedger $accountsReceivableLedger */
        $accountsReceivableLedger = $this->accountsReceivableLedgerRepository->findWithoutFail($id);

        if (empty($accountsReceivableLedger)) {
            return $this->sendError('Accounts Receivable Ledger not found');
        }

        $accountsReceivableLedger->delete();

        return $this->sendResponse($id, 'Accounts Receivable Ledger deleted successfully');
    }
}
