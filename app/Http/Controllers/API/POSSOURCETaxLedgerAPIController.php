<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSOURCETaxLedgerAPIRequest;
use App\Http\Requests\API\UpdatePOSSOURCETaxLedgerAPIRequest;
use App\Models\POSSOURCETaxLedger;
use App\Repositories\POSSOURCETaxLedgerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSOURCETaxLedgerController
 * @package App\Http\Controllers\API
 */

class POSSOURCETaxLedgerAPIController extends AppBaseController
{
    /** @var  POSSOURCETaxLedgerRepository */
    private $pOSSOURCETaxLedgerRepository;

    public function __construct(POSSOURCETaxLedgerRepository $pOSSOURCETaxLedgerRepo)
    {
        $this->pOSSOURCETaxLedgerRepository = $pOSSOURCETaxLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSOURCETaxLedgers",
     *      summary="Get a listing of the POSSOURCETaxLedgers.",
     *      tags={"POSSOURCETaxLedger"},
     *      description="Get all POSSOURCETaxLedgers",
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
     *                  @SWG\Items(ref="#/definitions/POSSOURCETaxLedger")
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
        $this->pOSSOURCETaxLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSOURCETaxLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSOURCETaxLedgers = $this->pOSSOURCETaxLedgerRepository->all();

        return $this->sendResponse($pOSSOURCETaxLedgers->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_tax_ledgers_retrieved_successful'));
    }

    /**
     * @param CreatePOSSOURCETaxLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSOURCETaxLedgers",
     *      summary="Store a newly created POSSOURCETaxLedger in storage",
     *      tags={"POSSOURCETaxLedger"},
     *      description="Store POSSOURCETaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSOURCETaxLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSOURCETaxLedger")
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
     *                  ref="#/definitions/POSSOURCETaxLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSOURCETaxLedgerAPIRequest $request)
    {
        $input = $request->all();

        $pOSSOURCETaxLedger = $this->pOSSOURCETaxLedgerRepository->create($input);

        return $this->sendResponse($pOSSOURCETaxLedger->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_tax_ledger_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSOURCETaxLedgers/{id}",
     *      summary="Display the specified POSSOURCETaxLedger",
     *      tags={"POSSOURCETaxLedger"},
     *      description="Get POSSOURCETaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCETaxLedger",
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
     *                  ref="#/definitions/POSSOURCETaxLedger"
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
        /** @var POSSOURCETaxLedger $pOSSOURCETaxLedger */
        $pOSSOURCETaxLedger = $this->pOSSOURCETaxLedgerRepository->findWithoutFail($id);

        if (empty($pOSSOURCETaxLedger)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_tax_ledger_not_found'));
        }

        return $this->sendResponse($pOSSOURCETaxLedger->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_tax_ledger_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSOURCETaxLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSOURCETaxLedgers/{id}",
     *      summary="Update the specified POSSOURCETaxLedger in storage",
     *      tags={"POSSOURCETaxLedger"},
     *      description="Update POSSOURCETaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCETaxLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSOURCETaxLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSOURCETaxLedger")
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
     *                  ref="#/definitions/POSSOURCETaxLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSOURCETaxLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSOURCETaxLedger $pOSSOURCETaxLedger */
        $pOSSOURCETaxLedger = $this->pOSSOURCETaxLedgerRepository->findWithoutFail($id);

        if (empty($pOSSOURCETaxLedger)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_tax_ledger_not_found'));
        }

        $pOSSOURCETaxLedger = $this->pOSSOURCETaxLedgerRepository->update($input, $id);

        return $this->sendResponse($pOSSOURCETaxLedger->toArray(), trans('custom.possourcetaxledger_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSOURCETaxLedgers/{id}",
     *      summary="Remove the specified POSSOURCETaxLedger from storage",
     *      tags={"POSSOURCETaxLedger"},
     *      description="Delete POSSOURCETaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCETaxLedger",
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
        /** @var POSSOURCETaxLedger $pOSSOURCETaxLedger */
        $pOSSOURCETaxLedger = $this->pOSSOURCETaxLedgerRepository->findWithoutFail($id);

        if (empty($pOSSOURCETaxLedger)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_tax_ledger_not_found'));
        }

        $pOSSOURCETaxLedger->delete();

        return $this->sendSuccess('P O S S O U R C E Tax Ledger deleted successfully');
    }
}
