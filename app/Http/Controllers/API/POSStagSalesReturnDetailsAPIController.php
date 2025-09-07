<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSStagSalesReturnDetailsAPIRequest;
use App\Http\Requests\API\UpdatePOSStagSalesReturnDetailsAPIRequest;
use App\Models\POSStagSalesReturnDetails;
use App\Repositories\POSStagSalesReturnDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSStagSalesReturnDetailsController
 * @package App\Http\Controllers\API
 */

class POSStagSalesReturnDetailsAPIController extends AppBaseController
{
    /** @var  POSStagSalesReturnDetailsRepository */
    private $pOSStagSalesReturnDetailsRepository;

    public function __construct(POSStagSalesReturnDetailsRepository $pOSStagSalesReturnDetailsRepo)
    {
        $this->pOSStagSalesReturnDetailsRepository = $pOSStagSalesReturnDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagSalesReturnDetails",
     *      summary="Get a listing of the POSStagSalesReturnDetails.",
     *      tags={"POSStagSalesReturnDetails"},
     *      description="Get all POSStagSalesReturnDetails",
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
     *                  @SWG\Items(ref="#/definitions/POSStagSalesReturnDetails")
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
        $this->pOSStagSalesReturnDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSStagSalesReturnDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSStagSalesReturnDetails = $this->pOSStagSalesReturnDetailsRepository->all();

        return $this->sendResponse($pOSStagSalesReturnDetails->toArray(), trans('custom.p_o_s_stag_sales_return_details_retrieved_successf'));
    }

    /**
     * @param CreatePOSStagSalesReturnDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSStagSalesReturnDetails",
     *      summary="Store a newly created POSStagSalesReturnDetails in storage",
     *      tags={"POSStagSalesReturnDetails"},
     *      description="Store POSStagSalesReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagSalesReturnDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagSalesReturnDetails")
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
     *                  ref="#/definitions/POSStagSalesReturnDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSStagSalesReturnDetailsAPIRequest $request)
    {
        $input = $request->all();

        $pOSStagSalesReturnDetails = $this->pOSStagSalesReturnDetailsRepository->create($input);

        return $this->sendResponse($pOSStagSalesReturnDetails->toArray(), trans('custom.p_o_s_stag_sales_return_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagSalesReturnDetails/{id}",
     *      summary="Display the specified POSStagSalesReturnDetails",
     *      tags={"POSStagSalesReturnDetails"},
     *      description="Get POSStagSalesReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagSalesReturnDetails",
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
     *                  ref="#/definitions/POSStagSalesReturnDetails"
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
        /** @var POSStagSalesReturnDetails $pOSStagSalesReturnDetails */
        $pOSStagSalesReturnDetails = $this->pOSStagSalesReturnDetailsRepository->findWithoutFail($id);

        if (empty($pOSStagSalesReturnDetails)) {
            return $this->sendError(trans('custom.p_o_s_stag_sales_return_details_not_found'));
        }

        return $this->sendResponse($pOSStagSalesReturnDetails->toArray(), trans('custom.p_o_s_stag_sales_return_details_retrieved_successf'));
    }

    /**
     * @param int $id
     * @param UpdatePOSStagSalesReturnDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSStagSalesReturnDetails/{id}",
     *      summary="Update the specified POSStagSalesReturnDetails in storage",
     *      tags={"POSStagSalesReturnDetails"},
     *      description="Update POSStagSalesReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagSalesReturnDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagSalesReturnDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagSalesReturnDetails")
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
     *                  ref="#/definitions/POSStagSalesReturnDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSStagSalesReturnDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSStagSalesReturnDetails $pOSStagSalesReturnDetails */
        $pOSStagSalesReturnDetails = $this->pOSStagSalesReturnDetailsRepository->findWithoutFail($id);

        if (empty($pOSStagSalesReturnDetails)) {
            return $this->sendError(trans('custom.p_o_s_stag_sales_return_details_not_found'));
        }

        $pOSStagSalesReturnDetails = $this->pOSStagSalesReturnDetailsRepository->update($input, $id);

        return $this->sendResponse($pOSStagSalesReturnDetails->toArray(), trans('custom.posstagsalesreturndetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSStagSalesReturnDetails/{id}",
     *      summary="Remove the specified POSStagSalesReturnDetails from storage",
     *      tags={"POSStagSalesReturnDetails"},
     *      description="Delete POSStagSalesReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagSalesReturnDetails",
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
        /** @var POSStagSalesReturnDetails $pOSStagSalesReturnDetails */
        $pOSStagSalesReturnDetails = $this->pOSStagSalesReturnDetailsRepository->findWithoutFail($id);

        if (empty($pOSStagSalesReturnDetails)) {
            return $this->sendError(trans('custom.p_o_s_stag_sales_return_details_not_found'));
        }

        $pOSStagSalesReturnDetails->delete();

        return $this->sendSuccess('P O S Stag Sales Return Details deleted successfully');
    }
}
