<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSourceSalesReturnDetailsAPIRequest;
use App\Http\Requests\API\UpdatePOSSourceSalesReturnDetailsAPIRequest;
use App\Models\POSSourceSalesReturnDetails;
use App\Repositories\POSSourceSalesReturnDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSourceSalesReturnDetailsController
 * @package App\Http\Controllers\API
 */

class POSSourceSalesReturnDetailsAPIController extends AppBaseController
{
    /** @var  POSSourceSalesReturnDetailsRepository */
    private $pOSSourceSalesReturnDetailsRepository;

    public function __construct(POSSourceSalesReturnDetailsRepository $pOSSourceSalesReturnDetailsRepo)
    {
        $this->pOSSourceSalesReturnDetailsRepository = $pOSSourceSalesReturnDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceSalesReturnDetails",
     *      summary="Get a listing of the POSSourceSalesReturnDetails.",
     *      tags={"POSSourceSalesReturnDetails"},
     *      description="Get all POSSourceSalesReturnDetails",
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
     *                  @SWG\Items(ref="#/definitions/POSSourceSalesReturnDetails")
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
        $this->pOSSourceSalesReturnDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSourceSalesReturnDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSourceSalesReturnDetails = $this->pOSSourceSalesReturnDetailsRepository->all();

        return $this->sendResponse($pOSSourceSalesReturnDetails->toArray(), trans('custom.p_o_s_source_sales_return_details_retrieved_succes'));
    }

    /**
     * @param CreatePOSSourceSalesReturnDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSourceSalesReturnDetails",
     *      summary="Store a newly created POSSourceSalesReturnDetails in storage",
     *      tags={"POSSourceSalesReturnDetails"},
     *      description="Store POSSourceSalesReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceSalesReturnDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceSalesReturnDetails")
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
     *                  ref="#/definitions/POSSourceSalesReturnDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSourceSalesReturnDetailsAPIRequest $request)
    {
        $input = $request->all();

        $pOSSourceSalesReturnDetails = $this->pOSSourceSalesReturnDetailsRepository->create($input);

        return $this->sendResponse($pOSSourceSalesReturnDetails->toArray(), trans('custom.p_o_s_source_sales_return_details_saved_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceSalesReturnDetails/{id}",
     *      summary="Display the specified POSSourceSalesReturnDetails",
     *      tags={"POSSourceSalesReturnDetails"},
     *      description="Get POSSourceSalesReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceSalesReturnDetails",
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
     *                  ref="#/definitions/POSSourceSalesReturnDetails"
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
        /** @var POSSourceSalesReturnDetails $pOSSourceSalesReturnDetails */
        $pOSSourceSalesReturnDetails = $this->pOSSourceSalesReturnDetailsRepository->findWithoutFail($id);

        if (empty($pOSSourceSalesReturnDetails)) {
            return $this->sendError(trans('custom.p_o_s_source_sales_return_details_not_found'));
        }

        return $this->sendResponse($pOSSourceSalesReturnDetails->toArray(), trans('custom.p_o_s_source_sales_return_details_retrieved_succes'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSourceSalesReturnDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSourceSalesReturnDetails/{id}",
     *      summary="Update the specified POSSourceSalesReturnDetails in storage",
     *      tags={"POSSourceSalesReturnDetails"},
     *      description="Update POSSourceSalesReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceSalesReturnDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceSalesReturnDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceSalesReturnDetails")
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
     *                  ref="#/definitions/POSSourceSalesReturnDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSourceSalesReturnDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSourceSalesReturnDetails $pOSSourceSalesReturnDetails */
        $pOSSourceSalesReturnDetails = $this->pOSSourceSalesReturnDetailsRepository->findWithoutFail($id);

        if (empty($pOSSourceSalesReturnDetails)) {
            return $this->sendError(trans('custom.p_o_s_source_sales_return_details_not_found'));
        }

        $pOSSourceSalesReturnDetails = $this->pOSSourceSalesReturnDetailsRepository->update($input, $id);

        return $this->sendResponse($pOSSourceSalesReturnDetails->toArray(), trans('custom.possourcesalesreturndetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSourceSalesReturnDetails/{id}",
     *      summary="Remove the specified POSSourceSalesReturnDetails from storage",
     *      tags={"POSSourceSalesReturnDetails"},
     *      description="Delete POSSourceSalesReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceSalesReturnDetails",
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
        /** @var POSSourceSalesReturnDetails $pOSSourceSalesReturnDetails */
        $pOSSourceSalesReturnDetails = $this->pOSSourceSalesReturnDetailsRepository->findWithoutFail($id);

        if (empty($pOSSourceSalesReturnDetails)) {
            return $this->sendError(trans('custom.p_o_s_source_sales_return_details_not_found'));
        }

        $pOSSourceSalesReturnDetails->delete();

        return $this->sendSuccess('P O S Source Sales Return Details deleted successfully');
    }
}
