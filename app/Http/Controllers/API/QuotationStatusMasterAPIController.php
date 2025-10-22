<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuotationStatusMasterAPIRequest;
use App\Http\Requests\API\UpdateQuotationStatusMasterAPIRequest;
use App\Models\QuotationStatusMaster;
use App\Repositories\QuotationStatusMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class QuotationStatusMasterController
 * @package App\Http\Controllers\API
 */

class QuotationStatusMasterAPIController extends AppBaseController
{
    /** @var  QuotationStatusMasterRepository */
    private $quotationStatusMasterRepository;

    public function __construct(QuotationStatusMasterRepository $quotationStatusMasterRepo)
    {
        $this->quotationStatusMasterRepository = $quotationStatusMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationStatusMasters",
     *      summary="Get a listing of the QuotationStatusMasters.",
     *      tags={"QuotationStatusMaster"},
     *      description="Get all QuotationStatusMasters",
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
     *                  @SWG\Items(ref="#/definitions/QuotationStatusMaster")
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
        $this->quotationStatusMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->quotationStatusMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $quotationStatusMasters = $this->quotationStatusMasterRepository->all();

        return $this->sendResponse($quotationStatusMasters->toArray(), trans('custom.quotation_status_masters_retrieved_successfully'));
    }

    /**
     * @param CreateQuotationStatusMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/quotationStatusMasters",
     *      summary="Store a newly created QuotationStatusMaster in storage",
     *      tags={"QuotationStatusMaster"},
     *      description="Store QuotationStatusMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationStatusMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationStatusMaster")
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
     *                  ref="#/definitions/QuotationStatusMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateQuotationStatusMasterAPIRequest $request)
    {
        $input = $request->all();

        $quotationStatusMaster = $this->quotationStatusMasterRepository->create($input);

        return $this->sendResponse($quotationStatusMaster->toArray(), trans('custom.quotation_status_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationStatusMasters/{id}",
     *      summary="Display the specified QuotationStatusMaster",
     *      tags={"QuotationStatusMaster"},
     *      description="Get QuotationStatusMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationStatusMaster",
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
     *                  ref="#/definitions/QuotationStatusMaster"
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
        /** @var QuotationStatusMaster $quotationStatusMaster */
        $quotationStatusMaster = $this->quotationStatusMasterRepository->findWithoutFail($id);

        if (empty($quotationStatusMaster)) {
            return $this->sendError(trans('custom.quotation_status_master_not_found'));
        }

        return $this->sendResponse($quotationStatusMaster->toArray(), trans('custom.quotation_status_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateQuotationStatusMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/quotationStatusMasters/{id}",
     *      summary="Update the specified QuotationStatusMaster in storage",
     *      tags={"QuotationStatusMaster"},
     *      description="Update QuotationStatusMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationStatusMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationStatusMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationStatusMaster")
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
     *                  ref="#/definitions/QuotationStatusMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateQuotationStatusMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var QuotationStatusMaster $quotationStatusMaster */
        $quotationStatusMaster = $this->quotationStatusMasterRepository->findWithoutFail($id);

        if (empty($quotationStatusMaster)) {
            return $this->sendError(trans('custom.quotation_status_master_not_found'));
        }

        $quotationStatusMaster = $this->quotationStatusMasterRepository->update($input, $id);

        return $this->sendResponse($quotationStatusMaster->toArray(), trans('custom.quotationstatusmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/quotationStatusMasters/{id}",
     *      summary="Remove the specified QuotationStatusMaster from storage",
     *      tags={"QuotationStatusMaster"},
     *      description="Delete QuotationStatusMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationStatusMaster",
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
        /** @var QuotationStatusMaster $quotationStatusMaster */
        $quotationStatusMaster = $this->quotationStatusMasterRepository->findWithoutFail($id);

        if (empty($quotationStatusMaster)) {
            return $this->sendError(trans('custom.quotation_status_master_not_found'));
        }

        $quotationStatusMaster->delete();

        return $this->sendSuccess('Quotation Status Master deleted successfully');
    }
}
