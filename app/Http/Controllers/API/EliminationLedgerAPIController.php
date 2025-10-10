<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEliminationLedgerAPIRequest;
use App\Http\Requests\API\UpdateEliminationLedgerAPIRequest;
use App\Models\EliminationLedger;
use App\Repositories\EliminationLedgerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EliminationLedgerController
 * @package App\Http\Controllers\API
 */

class EliminationLedgerAPIController extends AppBaseController
{
    /** @var  EliminationLedgerRepository */
    private $eliminationLedgerRepository;

    public function __construct(EliminationLedgerRepository $eliminationLedgerRepo)
    {
        $this->eliminationLedgerRepository = $eliminationLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/eliminationLedgers",
     *      summary="Get a listing of the EliminationLedgers.",
     *      tags={"EliminationLedger"},
     *      description="Get all EliminationLedgers",
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
     *                  @SWG\Items(ref="#/definitions/EliminationLedger")
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
        $this->eliminationLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->eliminationLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $eliminationLedgers = $this->eliminationLedgerRepository->all();

        return $this->sendResponse($eliminationLedgers->toArray(), trans('custom.elimination_ledgers_retrieved_successfully'));
    }

    /**
     * @param CreateEliminationLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/eliminationLedgers",
     *      summary="Store a newly created EliminationLedger in storage",
     *      tags={"EliminationLedger"},
     *      description="Store EliminationLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EliminationLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EliminationLedger")
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
     *                  ref="#/definitions/EliminationLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEliminationLedgerAPIRequest $request)
    {
        $input = $request->all();

        $eliminationLedger = $this->eliminationLedgerRepository->create($input);

        return $this->sendResponse($eliminationLedger->toArray(), trans('custom.elimination_ledger_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/eliminationLedgers/{id}",
     *      summary="Display the specified EliminationLedger",
     *      tags={"EliminationLedger"},
     *      description="Get EliminationLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EliminationLedger",
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
     *                  ref="#/definitions/EliminationLedger"
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
        /** @var EliminationLedger $eliminationLedger */
        $eliminationLedger = $this->eliminationLedgerRepository->findWithoutFail($id);

        if (empty($eliminationLedger)) {
            return $this->sendError(trans('custom.elimination_ledger_not_found'));
        }

        return $this->sendResponse($eliminationLedger->toArray(), trans('custom.elimination_ledger_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateEliminationLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/eliminationLedgers/{id}",
     *      summary="Update the specified EliminationLedger in storage",
     *      tags={"EliminationLedger"},
     *      description="Update EliminationLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EliminationLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EliminationLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EliminationLedger")
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
     *                  ref="#/definitions/EliminationLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEliminationLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var EliminationLedger $eliminationLedger */
        $eliminationLedger = $this->eliminationLedgerRepository->findWithoutFail($id);

        if (empty($eliminationLedger)) {
            return $this->sendError(trans('custom.elimination_ledger_not_found'));
        }

        $eliminationLedger = $this->eliminationLedgerRepository->update($input, $id);

        return $this->sendResponse($eliminationLedger->toArray(), trans('custom.eliminationledger_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/eliminationLedgers/{id}",
     *      summary="Remove the specified EliminationLedger from storage",
     *      tags={"EliminationLedger"},
     *      description="Delete EliminationLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EliminationLedger",
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
        /** @var EliminationLedger $eliminationLedger */
        $eliminationLedger = $this->eliminationLedgerRepository->findWithoutFail($id);

        if (empty($eliminationLedger)) {
            return $this->sendError(trans('custom.elimination_ledger_not_found'));
        }

        $eliminationLedger->delete();

        return $this->sendSuccess('Elimination Ledger deleted successfully');
    }

     public function getEliminationLedgerReview(Request $request)
    {
        /** @var GeneralLedger $generalLedger */
        $generalLedger = $this->eliminationLedgerRepository->with(['supplier','customer','charofaccount','localcurrency','transcurrency','rptcurrency'])->findWhere(['documentSystemID' => $request->documentSystemID,'documentSystemCode' => $request->autoID]);

        if (empty($generalLedger)) {
            return $this->sendError(trans('custom.elimination_ledger_not_found'));
        }


        $companyCurrency = \Helper::companyCurrency($request->companySystemID);

        $generalLedger = [
                'outputData' => $generalLedger->toArray(), 
                'companyCurrency' => $companyCurrency
            ];

        return $this->sendResponse($generalLedger, trans('custom.elimination_ledger_retrieved_successfully'));
    }
}
