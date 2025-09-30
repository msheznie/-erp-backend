<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetTransferFormRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateBudgetTransferFormRefferedBackAPIRequest;
use App\Models\BudgetTransferFormRefferedBack;
use App\Repositories\BudgetTransferFormRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetTransferFormRefferedBackController
 * @package App\Http\Controllers\API
 */

class BudgetTransferFormRefferedBackAPIController extends AppBaseController
{
    /** @var  BudgetTransferFormRefferedBackRepository */
    private $budgetTransferFormRefferedBackRepository;

    public function __construct(BudgetTransferFormRefferedBackRepository $budgetTransferFormRefferedBackRepo)
    {
        $this->budgetTransferFormRefferedBackRepository = $budgetTransferFormRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetTransferFormRefferedBacks",
     *      summary="Get a listing of the BudgetTransferFormRefferedBacks.",
     *      tags={"BudgetTransferFormRefferedBack"},
     *      description="Get all BudgetTransferFormRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/BudgetTransferFormRefferedBack")
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
        $this->budgetTransferFormRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetTransferFormRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetTransferFormRefferedBacks = $this->budgetTransferFormRefferedBackRepository->all();

        return $this->sendResponse($budgetTransferFormRefferedBacks->toArray(), trans('custom.budget_transfer_form_reffered_backs_retrieved_succ'));
    }

    /**
     * @param CreateBudgetTransferFormRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetTransferFormRefferedBacks",
     *      summary="Store a newly created BudgetTransferFormRefferedBack in storage",
     *      tags={"BudgetTransferFormRefferedBack"},
     *      description="Store BudgetTransferFormRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetTransferFormRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetTransferFormRefferedBack")
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
     *                  ref="#/definitions/BudgetTransferFormRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetTransferFormRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $budgetTransferFormRefferedBack = $this->budgetTransferFormRefferedBackRepository->create($input);

        return $this->sendResponse($budgetTransferFormRefferedBack->toArray(), trans('custom.budget_transfer_form_reffered_back_saved_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetTransferFormRefferedBacks/{id}",
     *      summary="Display the specified BudgetTransferFormRefferedBack",
     *      tags={"BudgetTransferFormRefferedBack"},
     *      description="Get BudgetTransferFormRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferFormRefferedBack",
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
     *                  ref="#/definitions/BudgetTransferFormRefferedBack"
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
        /** @var BudgetTransferFormRefferedBack $budgetTransferFormRefferedBack */
        $budgetTransferFormRefferedBack = $this->budgetTransferFormRefferedBackRepository->findWithoutFail($id);

        if (empty($budgetTransferFormRefferedBack)) {
            return $this->sendError(trans('custom.budget_transfer_form_reffered_back_not_found'));
        }

        return $this->sendResponse($budgetTransferFormRefferedBack->toArray(), trans('custom.budget_transfer_form_reffered_back_retrieved_succe'));
    }

    /**
     * @param int $id
     * @param UpdateBudgetTransferFormRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetTransferFormRefferedBacks/{id}",
     *      summary="Update the specified BudgetTransferFormRefferedBack in storage",
     *      tags={"BudgetTransferFormRefferedBack"},
     *      description="Update BudgetTransferFormRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferFormRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetTransferFormRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetTransferFormRefferedBack")
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
     *                  ref="#/definitions/BudgetTransferFormRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetTransferFormRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetTransferFormRefferedBack $budgetTransferFormRefferedBack */
        $budgetTransferFormRefferedBack = $this->budgetTransferFormRefferedBackRepository->findWithoutFail($id);

        if (empty($budgetTransferFormRefferedBack)) {
            return $this->sendError(trans('custom.budget_transfer_form_reffered_back_not_found'));
        }

        $budgetTransferFormRefferedBack = $this->budgetTransferFormRefferedBackRepository->update($input, $id);

        return $this->sendResponse($budgetTransferFormRefferedBack->toArray(), trans('custom.budgettransferformrefferedback_updated_successfull'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetTransferFormRefferedBacks/{id}",
     *      summary="Remove the specified BudgetTransferFormRefferedBack from storage",
     *      tags={"BudgetTransferFormRefferedBack"},
     *      description="Delete BudgetTransferFormRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferFormRefferedBack",
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
        /** @var BudgetTransferFormRefferedBack $budgetTransferFormRefferedBack */
        $budgetTransferFormRefferedBack = $this->budgetTransferFormRefferedBackRepository->findWithoutFail($id);

        if (empty($budgetTransferFormRefferedBack)) {
            return $this->sendError(trans('custom.budget_transfer_form_reffered_back_not_found'));
        }

        $budgetTransferFormRefferedBack->delete();

        return $this->sendSuccess('Budget Transfer Form Reffered Back deleted successfully');
    }


    public function getBudgetTransferAmendHistory(Request $request)
    {
        $input = $request->all();
        $budgetTransferAutoID = $input['budgetTransferID'];
        $budgetTransfetAmendHistory = BudgetTransferFormRefferedBack::with(['created_by'])
            ->where('budgetTransferFormAutoID', $budgetTransferAutoID)
            ->get();
        return $this->sendResponse($budgetTransfetAmendHistory, trans('custom.budget_transfer_amend_retrieved_successfully'));
    }

    public function budgetTransferAmend($id)
    {
        $budgetTransferFormAmend = $this->budgetTransferFormRefferedBackRepository->with(['company.reportingcurrency', 'created_by', 'confirmed_by', 'from_reviews'])->findWithoutFail($id);
        return $this->sendResponse($budgetTransferFormAmend->toArray(), trans('custom.budget_transfer_amend_retrieved_successfully'));
    }
}
