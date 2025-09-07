<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSTAGTaxLedgerAPIRequest;
use App\Http\Requests\API\UpdatePOSSTAGTaxLedgerAPIRequest;
use App\Models\POSSTAGTaxLedger;
use App\Repositories\POSSTAGTaxLedgerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSTAGTaxLedgerController
 * @package App\Http\Controllers\API
 */

class POSSTAGTaxLedgerAPIController extends AppBaseController
{
    /** @var  POSSTAGTaxLedgerRepository */
    private $pOSSTAGTaxLedgerRepository;

    public function __construct(POSSTAGTaxLedgerRepository $pOSSTAGTaxLedgerRepo)
    {
        $this->pOSSTAGTaxLedgerRepository = $pOSSTAGTaxLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGTaxLedgers",
     *      summary="Get a listing of the POSSTAGTaxLedgers.",
     *      tags={"POSSTAGTaxLedger"},
     *      description="Get all POSSTAGTaxLedgers",
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
     *                  @SWG\Items(ref="#/definitions/POSSTAGTaxLedger")
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
        $this->pOSSTAGTaxLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSTAGTaxLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSTAGTaxLedgers = $this->pOSSTAGTaxLedgerRepository->all();

        return $this->sendResponse($pOSSTAGTaxLedgers->toArray(), trans('custom.p_o_s_s_t_a_g_tax_ledgers_retrieved_successfully'));
    }

    /**
     * @param CreatePOSSTAGTaxLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSTAGTaxLedgers",
     *      summary="Store a newly created POSSTAGTaxLedger in storage",
     *      tags={"POSSTAGTaxLedger"},
     *      description="Store POSSTAGTaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGTaxLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGTaxLedger")
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
     *                  ref="#/definitions/POSSTAGTaxLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSTAGTaxLedgerAPIRequest $request)
    {
        $input = $request->all();

        $pOSSTAGTaxLedger = $this->pOSSTAGTaxLedgerRepository->create($input);

        return $this->sendResponse($pOSSTAGTaxLedger->toArray(), trans('custom.p_o_s_s_t_a_g_tax_ledger_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGTaxLedgers/{id}",
     *      summary="Display the specified POSSTAGTaxLedger",
     *      tags={"POSSTAGTaxLedger"},
     *      description="Get POSSTAGTaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGTaxLedger",
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
     *                  ref="#/definitions/POSSTAGTaxLedger"
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
        /** @var POSSTAGTaxLedger $pOSSTAGTaxLedger */
        $pOSSTAGTaxLedger = $this->pOSSTAGTaxLedgerRepository->findWithoutFail($id);

        if (empty($pOSSTAGTaxLedger)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_tax_ledger_not_found'));
        }

        return $this->sendResponse($pOSSTAGTaxLedger->toArray(), trans('custom.p_o_s_s_t_a_g_tax_ledger_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSTAGTaxLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSTAGTaxLedgers/{id}",
     *      summary="Update the specified POSSTAGTaxLedger in storage",
     *      tags={"POSSTAGTaxLedger"},
     *      description="Update POSSTAGTaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGTaxLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGTaxLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGTaxLedger")
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
     *                  ref="#/definitions/POSSTAGTaxLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSTAGTaxLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSTAGTaxLedger $pOSSTAGTaxLedger */
        $pOSSTAGTaxLedger = $this->pOSSTAGTaxLedgerRepository->findWithoutFail($id);

        if (empty($pOSSTAGTaxLedger)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_tax_ledger_not_found'));
        }

        $pOSSTAGTaxLedger = $this->pOSSTAGTaxLedgerRepository->update($input, $id);

        return $this->sendResponse($pOSSTAGTaxLedger->toArray(), trans('custom.posstagtaxledger_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSTAGTaxLedgers/{id}",
     *      summary="Remove the specified POSSTAGTaxLedger from storage",
     *      tags={"POSSTAGTaxLedger"},
     *      description="Delete POSSTAGTaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGTaxLedger",
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
        /** @var POSSTAGTaxLedger $pOSSTAGTaxLedger */
        $pOSSTAGTaxLedger = $this->pOSSTAGTaxLedgerRepository->findWithoutFail($id);

        if (empty($pOSSTAGTaxLedger)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_tax_ledger_not_found'));
        }

        $pOSSTAGTaxLedger->delete();

        return $this->sendSuccess('P O S S T A G Tax Ledger deleted successfully');
    }
}
