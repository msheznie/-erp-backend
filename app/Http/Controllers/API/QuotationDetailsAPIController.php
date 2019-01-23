<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuotationDetailsAPIRequest;
use App\Http\Requests\API\UpdateQuotationDetailsAPIRequest;
use App\Models\QuotationDetails;
use App\Repositories\QuotationDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class QuotationDetailsController
 * @package App\Http\Controllers\API
 */

class QuotationDetailsAPIController extends AppBaseController
{
    /** @var  QuotationDetailsRepository */
    private $quotationDetailsRepository;

    public function __construct(QuotationDetailsRepository $quotationDetailsRepo)
    {
        $this->quotationDetailsRepository = $quotationDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationDetails",
     *      summary="Get a listing of the QuotationDetails.",
     *      tags={"QuotationDetails"},
     *      description="Get all QuotationDetails",
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
     *                  @SWG\Items(ref="#/definitions/QuotationDetails")
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
        $this->quotationDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->quotationDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $quotationDetails = $this->quotationDetailsRepository->all();

        return $this->sendResponse($quotationDetails->toArray(), 'Quotation Details retrieved successfully');
    }

    /**
     * @param CreateQuotationDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/quotationDetails",
     *      summary="Store a newly created QuotationDetails in storage",
     *      tags={"QuotationDetails"},
     *      description="Store QuotationDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationDetails")
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
     *                  ref="#/definitions/QuotationDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateQuotationDetailsAPIRequest $request)
    {
        $input = $request->all();

        $quotationDetails = $this->quotationDetailsRepository->create($input);

        return $this->sendResponse($quotationDetails->toArray(), 'Quotation Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationDetails/{id}",
     *      summary="Display the specified QuotationDetails",
     *      tags={"QuotationDetails"},
     *      description="Get QuotationDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationDetails",
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
     *                  ref="#/definitions/QuotationDetails"
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
        /** @var QuotationDetails $quotationDetails */
        $quotationDetails = $this->quotationDetailsRepository->findWithoutFail($id);

        if (empty($quotationDetails)) {
            return $this->sendError('Quotation Details not found');
        }

        return $this->sendResponse($quotationDetails->toArray(), 'Quotation Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateQuotationDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/quotationDetails/{id}",
     *      summary="Update the specified QuotationDetails in storage",
     *      tags={"QuotationDetails"},
     *      description="Update QuotationDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationDetails")
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
     *                  ref="#/definitions/QuotationDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateQuotationDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var QuotationDetails $quotationDetails */
        $quotationDetails = $this->quotationDetailsRepository->findWithoutFail($id);

        if (empty($quotationDetails)) {
            return $this->sendError('Quotation Details not found');
        }

        $quotationDetails = $this->quotationDetailsRepository->update($input, $id);

        return $this->sendResponse($quotationDetails->toArray(), 'QuotationDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/quotationDetails/{id}",
     *      summary="Remove the specified QuotationDetails from storage",
     *      tags={"QuotationDetails"},
     *      description="Delete QuotationDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationDetails",
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
        /** @var QuotationDetails $quotationDetails */
        $quotationDetails = $this->quotationDetailsRepository->findWithoutFail($id);

        if (empty($quotationDetails)) {
            return $this->sendError('Quotation Details not found');
        }

        $quotationDetails->delete();

        return $this->sendResponse($id, 'Quotation Details deleted successfully');
    }
}
