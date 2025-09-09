<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxLedgerDetailAPIRequest;
use App\Http\Requests\API\UpdateTaxLedgerDetailAPIRequest;
use App\Models\TaxLedgerDetail;
use App\Repositories\TaxLedgerDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxLedgerDetailController
 * @package App\Http\Controllers\API
 */

class TaxLedgerDetailAPIController extends AppBaseController
{
    /** @var  TaxLedgerDetailRepository */
    private $taxLedgerDetailRepository;

    public function __construct(TaxLedgerDetailRepository $taxLedgerDetailRepo)
    {
        $this->taxLedgerDetailRepository = $taxLedgerDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxLedgerDetails",
     *      summary="Get a listing of the TaxLedgerDetails.",
     *      tags={"TaxLedgerDetail"},
     *      description="Get all TaxLedgerDetails",
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
     *                  @SWG\Items(ref="#/definitions/TaxLedgerDetail")
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
        $this->taxLedgerDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->taxLedgerDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxLedgerDetails = $this->taxLedgerDetailRepository->all();

        return $this->sendResponse($taxLedgerDetails->toArray(), trans('custom.tax_ledger_details_retrieved_successfully'));
    }

    /**
     * @param CreateTaxLedgerDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/taxLedgerDetails",
     *      summary="Store a newly created TaxLedgerDetail in storage",
     *      tags={"TaxLedgerDetail"},
     *      description="Store TaxLedgerDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TaxLedgerDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TaxLedgerDetail")
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
     *                  ref="#/definitions/TaxLedgerDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTaxLedgerDetailAPIRequest $request)
    {
        $input = $request->all();

        $taxLedgerDetail = $this->taxLedgerDetailRepository->create($input);

        return $this->sendResponse($taxLedgerDetail->toArray(), trans('custom.tax_ledger_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxLedgerDetails/{id}",
     *      summary="Display the specified TaxLedgerDetail",
     *      tags={"TaxLedgerDetail"},
     *      description="Get TaxLedgerDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxLedgerDetail",
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
     *                  ref="#/definitions/TaxLedgerDetail"
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
        /** @var TaxLedgerDetail $taxLedgerDetail */
        $taxLedgerDetail = $this->taxLedgerDetailRepository->findWithoutFail($id);

        if (empty($taxLedgerDetail)) {
            return $this->sendError(trans('custom.tax_ledger_detail_not_found'));
        }

        return $this->sendResponse($taxLedgerDetail->toArray(), trans('custom.tax_ledger_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTaxLedgerDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/taxLedgerDetails/{id}",
     *      summary="Update the specified TaxLedgerDetail in storage",
     *      tags={"TaxLedgerDetail"},
     *      description="Update TaxLedgerDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxLedgerDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TaxLedgerDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TaxLedgerDetail")
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
     *                  ref="#/definitions/TaxLedgerDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTaxLedgerDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var TaxLedgerDetail $taxLedgerDetail */
        $taxLedgerDetail = $this->taxLedgerDetailRepository->findWithoutFail($id);

        if (empty($taxLedgerDetail)) {
            return $this->sendError(trans('custom.tax_ledger_detail_not_found'));
        }

        $taxLedgerDetail = $this->taxLedgerDetailRepository->update($input, $id);

        return $this->sendResponse($taxLedgerDetail->toArray(), trans('custom.taxledgerdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/taxLedgerDetails/{id}",
     *      summary="Remove the specified TaxLedgerDetail from storage",
     *      tags={"TaxLedgerDetail"},
     *      description="Delete TaxLedgerDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxLedgerDetail",
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
        /** @var TaxLedgerDetail $taxLedgerDetail */
        $taxLedgerDetail = $this->taxLedgerDetailRepository->findWithoutFail($id);

        if (empty($taxLedgerDetail)) {
            return $this->sendError(trans('custom.tax_ledger_detail_not_found'));
        }

        $taxLedgerDetail->delete();

        return $this->sendSuccess('Tax Ledger Detail deleted successfully');
    }
}
