<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExpenseClaimDetailsMasterAPIRequest;
use App\Http\Requests\API\UpdateExpenseClaimDetailsMasterAPIRequest;
use App\Models\ExpenseClaimDetailsMaster;
use App\Repositories\ExpenseClaimDetailsMasterRepository;
use App\Repositories\ExpenseClaimMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ExpenseClaimDetailsMasterController
 * @package App\Http\Controllers\API
 */

class ExpenseClaimDetailsMasterAPIController extends AppBaseController
{
    /** @var  ExpenseClaimDetailsMasterRepository */
    private $expenseClaimDetailsMasterRepository;
    private $expenseClaimMasterRepository;

    public function __construct(ExpenseClaimDetailsMasterRepository $expenseClaimDetailsMasterRepo, ExpenseClaimMasterRepository $expenseClaimMasterRepository)
    {
        $this->expenseClaimDetailsMasterRepository = $expenseClaimDetailsMasterRepo;
        $this->expenseClaimMasterRepository = $expenseClaimMasterRepository;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaimDetailsMasters",
     *      summary="Get a listing of the ExpenseClaimDetailsMasters.",
     *      tags={"ExpenseClaimDetailsMaster"},
     *      description="Get all ExpenseClaimDetailsMasters",
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
     *                  @SWG\Items(ref="#/definitions/ExpenseClaimDetailsMaster")
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
        $this->expenseClaimDetailsMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->expenseClaimDetailsMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $expenseClaimDetailsMasters = $this->expenseClaimDetailsMasterRepository->all();

        return $this->sendResponse($expenseClaimDetailsMasters->toArray(), trans('custom.expense_claim_details_masters_retrieved_successful'));
    }

    /**
     * @param CreateExpenseClaimDetailsMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/expenseClaimDetailsMasters",
     *      summary="Store a newly created ExpenseClaimDetailsMaster in storage",
     *      tags={"ExpenseClaimDetailsMaster"},
     *      description="Store ExpenseClaimDetailsMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaimDetailsMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaimDetailsMaster")
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
     *                  ref="#/definitions/ExpenseClaimDetailsMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExpenseClaimDetailsMasterAPIRequest $request)
    {
        $input = $request->all();

        $expenseClaimDetailsMaster = $this->expenseClaimDetailsMasterRepository->create($input);

        return $this->sendResponse($expenseClaimDetailsMaster->toArray(), trans('custom.expense_claim_details_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaimDetailsMasters/{id}",
     *      summary="Display the specified ExpenseClaimDetailsMaster",
     *      tags={"ExpenseClaimDetailsMaster"},
     *      description="Get ExpenseClaimDetailsMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimDetailsMaster",
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
     *                  ref="#/definitions/ExpenseClaimDetailsMaster"
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
        /** @var ExpenseClaimDetailsMaster $expenseClaimDetailsMaster */
        $expenseClaimDetailsMaster = $this->expenseClaimDetailsMasterRepository->findWithoutFail($id);

        if (empty($expenseClaimDetailsMaster)) {
            return $this->sendError(trans('custom.expense_claim_details_master_not_found'));
        }

        return $this->sendResponse($expenseClaimDetailsMaster->toArray(), trans('custom.expense_claim_details_master_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdateExpenseClaimDetailsMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/expenseClaimDetailsMasters/{id}",
     *      summary="Update the specified ExpenseClaimDetailsMaster in storage",
     *      tags={"ExpenseClaimDetailsMaster"},
     *      description="Update ExpenseClaimDetailsMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimDetailsMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaimDetailsMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaimDetailsMaster")
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
     *                  ref="#/definitions/ExpenseClaimDetailsMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExpenseClaimDetailsMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExpenseClaimDetailsMaster $expenseClaimDetailsMaster */
        $expenseClaimDetailsMaster = $this->expenseClaimDetailsMasterRepository->findWithoutFail($id);

        if (empty($expenseClaimDetailsMaster)) {
            return $this->sendError(trans('custom.expense_claim_details_master_not_found'));
        }

        $expenseClaimDetailsMaster = $this->expenseClaimDetailsMasterRepository->update($input, $id);

        return $this->sendResponse($expenseClaimDetailsMaster->toArray(), trans('custom.expenseclaimdetailsmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/expenseClaimDetailsMasters/{id}",
     *      summary="Remove the specified ExpenseClaimDetailsMaster from storage",
     *      tags={"ExpenseClaimDetailsMaster"},
     *      description="Delete ExpenseClaimDetailsMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimDetailsMaster",
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
        /** @var ExpenseClaimDetailsMaster $expenseClaimDetailsMaster */
        $expenseClaimDetailsMaster = $this->expenseClaimDetailsMasterRepository->findWithoutFail($id);

        if (empty($expenseClaimDetailsMaster)) {
            return $this->sendError(trans('custom.expense_claim_details_master_not_found'));
        }

        $expenseClaimDetailsMaster->delete();

        return $this->sendSuccess('Expense Claim Details Master deleted successfully');
    }

    public function getDetailsByExpenseClaimMaster(Request $request)
    {
        $input = $request->all();
        $id = $input['expenseClaimMasterAutoID'];

        $items = ExpenseClaimDetailsMaster::where('expenseClaimMasterAutoID', $id)
            ->with(['segment','currency', 'category', 'local_currency'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.expense_claim_details_retrieved_successfully'));
    }

    public function preCheckECDetailMasterEdit(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        /** @var ExpenseClaimDetailsMaster $expenseClaimDetails */
        $expenseClaimDetails = $this->expenseClaimDetailsMasterRepository->findWithoutFail($id);

        if (empty($expenseClaimDetails)) {
            return $this->sendError(trans('custom.expense_claim_details_not_found_1'));
        }

        $expenseClaim = $this->expenseClaimMasterRepository->findWithoutFail($expenseClaimDetails->expenseClaimMasterAutoID);

        if (empty($expenseClaim)) {
            return $this->sendError(trans('custom.expense_claim_not_found'));
        }


        if ($expenseClaim->approved != -1) {
            return $this->sendError(trans('custom.this_expense_claim_is_not_approved_you_cannot_edit'), 500);
        }

        if ($expenseClaim->addedForPayment != 0) {
            return $this->sendError(trans('custom.cannot_edit_this_expense_claim_is_already_paid'), 500);
        }

        return $this->sendResponse($expenseClaimDetails->toArray(), trans('custom.expense_claim_details_can_update_successfully'));
    }
}
