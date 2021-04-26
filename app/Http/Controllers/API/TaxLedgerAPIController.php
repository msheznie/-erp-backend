<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxLedgerAPIRequest;
use App\Http\Requests\API\UpdateTaxLedgerAPIRequest;
use App\Models\TaxLedger;
use App\Repositories\TaxLedgerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxLedgerController
 * @package App\Http\Controllers\API
 */

class TaxLedgerAPIController extends AppBaseController
{
    /** @var  TaxLedgerRepository */
    private $taxLedgerRepository;

    public function __construct(TaxLedgerRepository $taxLedgerRepo)
    {
        $this->taxLedgerRepository = $taxLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxLedgers",
     *      summary="Get a listing of the TaxLedgers.",
     *      tags={"TaxLedger"},
     *      description="Get all TaxLedgers",
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
     *                  @SWG\Items(ref="#/definitions/TaxLedger")
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
        $this->taxLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->taxLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxLedgers = $this->taxLedgerRepository->all();

        return $this->sendResponse($taxLedgers->toArray(), 'Tax Ledgers retrieved successfully');
    }

    /**
     * @param CreateTaxLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/taxLedgers",
     *      summary="Store a newly created TaxLedger in storage",
     *      tags={"TaxLedger"},
     *      description="Store TaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TaxLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TaxLedger")
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
     *                  ref="#/definitions/TaxLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTaxLedgerAPIRequest $request)
    {
        $input = $request->all();

        $taxLedger = $this->taxLedgerRepository->create($input);

        return $this->sendResponse($taxLedger->toArray(), 'Tax Ledger saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxLedgers/{id}",
     *      summary="Display the specified TaxLedger",
     *      tags={"TaxLedger"},
     *      description="Get TaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxLedger",
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
     *                  ref="#/definitions/TaxLedger"
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
        /** @var TaxLedger $taxLedger */
        $taxLedger = $this->taxLedgerRepository->findWithoutFail($id);

        if (empty($taxLedger)) {
            return $this->sendError('Tax Ledger not found');
        }

        return $this->sendResponse($taxLedger->toArray(), 'Tax Ledger retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTaxLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/taxLedgers/{id}",
     *      summary="Update the specified TaxLedger in storage",
     *      tags={"TaxLedger"},
     *      description="Update TaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TaxLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TaxLedger")
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
     *                  ref="#/definitions/TaxLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTaxLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var TaxLedger $taxLedger */
        $taxLedger = $this->taxLedgerRepository->findWithoutFail($id);

        if (empty($taxLedger)) {
            return $this->sendError('Tax Ledger not found');
        }

        $taxLedger = $this->taxLedgerRepository->update($input, $id);

        return $this->sendResponse($taxLedger->toArray(), 'TaxLedger updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/taxLedgers/{id}",
     *      summary="Remove the specified TaxLedger from storage",
     *      tags={"TaxLedger"},
     *      description="Delete TaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxLedger",
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
        /** @var TaxLedger $taxLedger */
        $taxLedger = $this->taxLedgerRepository->findWithoutFail($id);

        if (empty($taxLedger)) {
            return $this->sendError('Tax Ledger not found');
        }

        $taxLedger->delete();

        return $this->sendSuccess('Tax Ledger deleted successfully');
    }
}
