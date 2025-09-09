<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetTransferFormDetailRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateBudgetTransferFormDetailRefferedBackAPIRequest;
use App\Models\BudgetTransferFormDetailRefferedBack;
use App\Repositories\BudgetTransferFormDetailRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\BudgetTransferFormRefferedBack;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetTransferFormDetailRefferedBackController
 * @package App\Http\Controllers\API
 */

class BudgetTransferFormDetailRefferedBackAPIController extends AppBaseController
{
    /** @var  BudgetTransferFormDetailRefferedBackRepository */
    private $budgetTransferFormDetailRefferedBackRepository;

    public function __construct(BudgetTransferFormDetailRefferedBackRepository $budgetTransferFormDetailRefferedBackRepo)
    {
        $this->budgetTransferFormDetailRefferedBackRepository = $budgetTransferFormDetailRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetTransferFormDetailRefferedBacks",
     *      summary="Get a listing of the BudgetTransferFormDetailRefferedBacks.",
     *      tags={"BudgetTransferFormDetailRefferedBack"},
     *      description="Get all BudgetTransferFormDetailRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/BudgetTransferFormDetailRefferedBack")
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
        $this->budgetTransferFormDetailRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetTransferFormDetailRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetTransferFormDetailRefferedBacks = $this->budgetTransferFormDetailRefferedBackRepository->all();

        return $this->sendResponse($budgetTransferFormDetailRefferedBacks->toArray(), trans('custom.budget_transfer_form_detail_reffered_backs_retriev'));
    }

    /**
     * @param CreateBudgetTransferFormDetailRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetTransferFormDetailRefferedBacks",
     *      summary="Store a newly created BudgetTransferFormDetailRefferedBack in storage",
     *      tags={"BudgetTransferFormDetailRefferedBack"},
     *      description="Store BudgetTransferFormDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetTransferFormDetailRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetTransferFormDetailRefferedBack")
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
     *                  ref="#/definitions/BudgetTransferFormDetailRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetTransferFormDetailRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $budgetTransferFormDetailRefferedBack = $this->budgetTransferFormDetailRefferedBackRepository->create($input);

        return $this->sendResponse($budgetTransferFormDetailRefferedBack->toArray(), trans('custom.budget_transfer_form_detail_reffered_back_saved_su'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetTransferFormDetailRefferedBacks/{id}",
     *      summary="Display the specified BudgetTransferFormDetailRefferedBack",
     *      tags={"BudgetTransferFormDetailRefferedBack"},
     *      description="Get BudgetTransferFormDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferFormDetailRefferedBack",
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
     *                  ref="#/definitions/BudgetTransferFormDetailRefferedBack"
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
        /** @var BudgetTransferFormDetailRefferedBack $budgetTransferFormDetailRefferedBack */
        $budgetTransferFormDetailRefferedBack = $this->budgetTransferFormDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($budgetTransferFormDetailRefferedBack)) {
            return $this->sendError(trans('custom.budget_transfer_form_detail_reffered_back_not_foun'));
        }

        return $this->sendResponse($budgetTransferFormDetailRefferedBack->toArray(), trans('custom.budget_transfer_form_detail_reffered_back_retrieve'));
    }

    /**
     * @param int $id
     * @param UpdateBudgetTransferFormDetailRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetTransferFormDetailRefferedBacks/{id}",
     *      summary="Update the specified BudgetTransferFormDetailRefferedBack in storage",
     *      tags={"BudgetTransferFormDetailRefferedBack"},
     *      description="Update BudgetTransferFormDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferFormDetailRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetTransferFormDetailRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetTransferFormDetailRefferedBack")
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
     *                  ref="#/definitions/BudgetTransferFormDetailRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetTransferFormDetailRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetTransferFormDetailRefferedBack $budgetTransferFormDetailRefferedBack */
        $budgetTransferFormDetailRefferedBack = $this->budgetTransferFormDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($budgetTransferFormDetailRefferedBack)) {
            return $this->sendError(trans('custom.budget_transfer_form_detail_reffered_back_not_foun'));
        }

        $budgetTransferFormDetailRefferedBack = $this->budgetTransferFormDetailRefferedBackRepository->update($input, $id);

        return $this->sendResponse($budgetTransferFormDetailRefferedBack->toArray(), trans('custom.budgettransferformdetailrefferedback_updated_succe'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetTransferFormDetailRefferedBacks/{id}",
     *      summary="Remove the specified BudgetTransferFormDetailRefferedBack from storage",
     *      tags={"BudgetTransferFormDetailRefferedBack"},
     *      description="Delete BudgetTransferFormDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferFormDetailRefferedBack",
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
        /** @var BudgetTransferFormDetailRefferedBack $budgetTransferFormDetailRefferedBack */
        $budgetTransferFormDetailRefferedBack = $this->budgetTransferFormDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($budgetTransferFormDetailRefferedBack)) {
            return $this->sendError(trans('custom.budget_transfer_form_detail_reffered_back_not_foun'));
        }

        $budgetTransferFormDetailRefferedBack->delete();

        return $this->sendSuccess('Budget Transfer Form Detail Reffered Back deleted successfully');
    }

    public function getDetailsByBudgetTransferAmend(Request $request)
    {
        $input = $request->all();
        $amedBudgetTransferAmendID = $input['budgetTransferFormAutoID'];
        $amedBudgetTransfer = BudgetTransferFormRefferedBack::where('budgetTransferFormRefferedBackID', $amedBudgetTransferAmendID)->first();
        $timesReferred = $amedBudgetTransfer->timesReferred;
        $budgetTransferID = $amedBudgetTransfer->budgetTransferFormAutoID;


        $id = $input['budgetTransferFormAutoID'];

        $items = BudgetTransferFormDetailRefferedBack::where('budgetTransferFormAutoID', $budgetTransferID)
            ->where('timesReferred', $timesReferred)
            ->with([
                'from_segment', 'to_segment', 'from_template', 'to_template',
                'contingency:ID,contingencyBudgetNo,comments'
            ])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.budget_transfer_form_amend_detail_retrieved_succes'));
    }
}
