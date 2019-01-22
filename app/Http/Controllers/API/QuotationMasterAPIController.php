<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuotationMasterAPIRequest;
use App\Http\Requests\API\UpdateQuotationMasterAPIRequest;
use App\Models\QuotationMaster;
use App\Repositories\QuotationMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class QuotationMasterController
 * @package App\Http\Controllers\API
 */

class QuotationMasterAPIController extends AppBaseController
{
    /** @var  QuotationMasterRepository */
    private $quotationMasterRepository;

    public function __construct(QuotationMasterRepository $quotationMasterRepo)
    {
        $this->quotationMasterRepository = $quotationMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationMasters",
     *      summary="Get a listing of the QuotationMasters.",
     *      tags={"QuotationMaster"},
     *      description="Get all QuotationMasters",
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
     *                  @SWG\Items(ref="#/definitions/QuotationMaster")
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
        $this->quotationMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->quotationMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $quotationMasters = $this->quotationMasterRepository->all();

        return $this->sendResponse($quotationMasters->toArray(), 'Quotation Masters retrieved successfully');
    }

    /**
     * @param CreateQuotationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/quotationMasters",
     *      summary="Store a newly created QuotationMaster in storage",
     *      tags={"QuotationMaster"},
     *      description="Store QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationMaster")
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
     *                  ref="#/definitions/QuotationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateQuotationMasterAPIRequest $request)
    {
        $input = $request->all();

        $quotationMasters = $this->quotationMasterRepository->create($input);

        return $this->sendResponse($quotationMasters->toArray(), 'Quotation Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationMasters/{id}",
     *      summary="Display the specified QuotationMaster",
     *      tags={"QuotationMaster"},
     *      description="Get QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMaster",
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
     *                  ref="#/definitions/QuotationMaster"
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
        /** @var QuotationMaster $quotationMaster */
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMaster)) {
            return $this->sendError('Quotation Master not found');
        }

        return $this->sendResponse($quotationMaster->toArray(), 'Quotation Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateQuotationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/quotationMasters/{id}",
     *      summary="Update the specified QuotationMaster in storage",
     *      tags={"QuotationMaster"},
     *      description="Update QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationMaster")
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
     *                  ref="#/definitions/QuotationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateQuotationMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var QuotationMaster $quotationMaster */
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMaster)) {
            return $this->sendError('Quotation Master not found');
        }

        $quotationMaster = $this->quotationMasterRepository->update($input, $id);

        return $this->sendResponse($quotationMaster->toArray(), 'QuotationMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/quotationMasters/{id}",
     *      summary="Remove the specified QuotationMaster from storage",
     *      tags={"QuotationMaster"},
     *      description="Delete QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMaster",
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
        /** @var QuotationMaster $quotationMaster */
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMaster)) {
            return $this->sendError('Quotation Master not found');
        }

        $quotationMaster->delete();

        return $this->sendResponse($id, 'Quotation Master deleted successfully');
    }
}
