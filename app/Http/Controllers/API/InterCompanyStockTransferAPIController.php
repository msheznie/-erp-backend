<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateInterCompanyStockTransferAPIRequest;
use App\Http\Requests\API\UpdateInterCompanyStockTransferAPIRequest;
use App\Models\InterCompanyStockTransfer;
use App\Repositories\InterCompanyStockTransferRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class InterCompanyStockTransferController
 * @package App\Http\Controllers\API
 */

class InterCompanyStockTransferAPIController extends AppBaseController
{
    /** @var  InterCompanyStockTransferRepository */
    private $interCompanyStockTransferRepository;

    public function __construct(InterCompanyStockTransferRepository $interCompanyStockTransferRepo)
    {
        $this->interCompanyStockTransferRepository = $interCompanyStockTransferRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/interCompanyStockTransfers",
     *      summary="Get a listing of the InterCompanyStockTransfers.",
     *      tags={"InterCompanyStockTransfer"},
     *      description="Get all InterCompanyStockTransfers",
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
     *                  @SWG\Items(ref="#/definitions/InterCompanyStockTransfer")
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
        $this->interCompanyStockTransferRepository->pushCriteria(new RequestCriteria($request));
        $this->interCompanyStockTransferRepository->pushCriteria(new LimitOffsetCriteria($request));
        $interCompanyStockTransfers = $this->interCompanyStockTransferRepository->all();

        return $this->sendResponse($interCompanyStockTransfers->toArray(), trans('custom.inter_company_stock_transfers_retrieved_successful'));
    }

    /**
     * @param CreateInterCompanyStockTransferAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/interCompanyStockTransfers",
     *      summary="Store a newly created InterCompanyStockTransfer in storage",
     *      tags={"InterCompanyStockTransfer"},
     *      description="Store InterCompanyStockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InterCompanyStockTransfer that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InterCompanyStockTransfer")
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
     *                  ref="#/definitions/InterCompanyStockTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateInterCompanyStockTransferAPIRequest $request)
    {
        $input = $request->all();

        $interCompanyStockTransfer = $this->interCompanyStockTransferRepository->create($input);

        return $this->sendResponse($interCompanyStockTransfer->toArray(), trans('custom.inter_company_stock_transfer_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/interCompanyStockTransfers/{id}",
     *      summary="Display the specified InterCompanyStockTransfer",
     *      tags={"InterCompanyStockTransfer"},
     *      description="Get InterCompanyStockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InterCompanyStockTransfer",
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
     *                  ref="#/definitions/InterCompanyStockTransfer"
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
        /** @var InterCompanyStockTransfer $interCompanyStockTransfer */
        $interCompanyStockTransfer = $this->interCompanyStockTransferRepository->findWithoutFail($id);

        if (empty($interCompanyStockTransfer)) {
            return $this->sendError(trans('custom.inter_company_stock_transfer_not_found'));
        }

        return $this->sendResponse($interCompanyStockTransfer->toArray(), trans('custom.inter_company_stock_transfer_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdateInterCompanyStockTransferAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/interCompanyStockTransfers/{id}",
     *      summary="Update the specified InterCompanyStockTransfer in storage",
     *      tags={"InterCompanyStockTransfer"},
     *      description="Update InterCompanyStockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InterCompanyStockTransfer",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InterCompanyStockTransfer that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InterCompanyStockTransfer")
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
     *                  ref="#/definitions/InterCompanyStockTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateInterCompanyStockTransferAPIRequest $request)
    {
        $input = $request->all();

        /** @var InterCompanyStockTransfer $interCompanyStockTransfer */
        $interCompanyStockTransfer = $this->interCompanyStockTransferRepository->findWithoutFail($id);

        if (empty($interCompanyStockTransfer)) {
            return $this->sendError(trans('custom.inter_company_stock_transfer_not_found'));
        }

        $interCompanyStockTransfer = $this->interCompanyStockTransferRepository->update($input, $id);

        return $this->sendResponse($interCompanyStockTransfer->toArray(), trans('custom.intercompanystocktransfer_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/interCompanyStockTransfers/{id}",
     *      summary="Remove the specified InterCompanyStockTransfer from storage",
     *      tags={"InterCompanyStockTransfer"},
     *      description="Delete InterCompanyStockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InterCompanyStockTransfer",
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
        /** @var InterCompanyStockTransfer $interCompanyStockTransfer */
        $interCompanyStockTransfer = $this->interCompanyStockTransferRepository->findWithoutFail($id);

        if (empty($interCompanyStockTransfer)) {
            return $this->sendError(trans('custom.inter_company_stock_transfer_not_found'));
        }

        $interCompanyStockTransfer->delete();

        return $this->sendSuccess('Inter Company Stock Transfer deleted successfully');
    }
}
