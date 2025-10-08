<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSourceSalesReturnAPIRequest;
use App\Http\Requests\API\UpdatePOSSourceSalesReturnAPIRequest;
use App\Models\POSSourceSalesReturn;
use App\Repositories\POSSourceSalesReturnRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSourceSalesReturnController
 * @package App\Http\Controllers\API
 */

class POSSourceSalesReturnAPIController extends AppBaseController
{
    /** @var  POSSourceSalesReturnRepository */
    private $pOSSourceSalesReturnRepository;

    public function __construct(POSSourceSalesReturnRepository $pOSSourceSalesReturnRepo)
    {
        $this->pOSSourceSalesReturnRepository = $pOSSourceSalesReturnRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceSalesReturns",
     *      summary="Get a listing of the POSSourceSalesReturns.",
     *      tags={"POSSourceSalesReturn"},
     *      description="Get all POSSourceSalesReturns",
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
     *                  @SWG\Items(ref="#/definitions/POSSourceSalesReturn")
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
        $this->pOSSourceSalesReturnRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSourceSalesReturnRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSourceSalesReturns = $this->pOSSourceSalesReturnRepository->all();

        return $this->sendResponse($pOSSourceSalesReturns->toArray(), trans('custom.p_o_s_source_sales_returns_retrieved_successfully'));
    }

    /**
     * @param CreatePOSSourceSalesReturnAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSourceSalesReturns",
     *      summary="Store a newly created POSSourceSalesReturn in storage",
     *      tags={"POSSourceSalesReturn"},
     *      description="Store POSSourceSalesReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceSalesReturn that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceSalesReturn")
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
     *                  ref="#/definitions/POSSourceSalesReturn"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSourceSalesReturnAPIRequest $request)
    {
        $input = $request->all();

        $pOSSourceSalesReturn = $this->pOSSourceSalesReturnRepository->create($input);

        return $this->sendResponse($pOSSourceSalesReturn->toArray(), trans('custom.p_o_s_source_sales_return_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceSalesReturns/{id}",
     *      summary="Display the specified POSSourceSalesReturn",
     *      tags={"POSSourceSalesReturn"},
     *      description="Get POSSourceSalesReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceSalesReturn",
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
     *                  ref="#/definitions/POSSourceSalesReturn"
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
        /** @var POSSourceSalesReturn $pOSSourceSalesReturn */
        $pOSSourceSalesReturn = $this->pOSSourceSalesReturnRepository->findWithoutFail($id);

        if (empty($pOSSourceSalesReturn)) {
            return $this->sendError(trans('custom.p_o_s_source_sales_return_not_found'));
        }

        return $this->sendResponse($pOSSourceSalesReturn->toArray(), trans('custom.p_o_s_source_sales_return_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSourceSalesReturnAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSourceSalesReturns/{id}",
     *      summary="Update the specified POSSourceSalesReturn in storage",
     *      tags={"POSSourceSalesReturn"},
     *      description="Update POSSourceSalesReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceSalesReturn",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceSalesReturn that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceSalesReturn")
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
     *                  ref="#/definitions/POSSourceSalesReturn"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSourceSalesReturnAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSourceSalesReturn $pOSSourceSalesReturn */
        $pOSSourceSalesReturn = $this->pOSSourceSalesReturnRepository->findWithoutFail($id);

        if (empty($pOSSourceSalesReturn)) {
            return $this->sendError(trans('custom.p_o_s_source_sales_return_not_found'));
        }

        $pOSSourceSalesReturn = $this->pOSSourceSalesReturnRepository->update($input, $id);

        return $this->sendResponse($pOSSourceSalesReturn->toArray(), trans('custom.possourcesalesreturn_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSourceSalesReturns/{id}",
     *      summary="Remove the specified POSSourceSalesReturn from storage",
     *      tags={"POSSourceSalesReturn"},
     *      description="Delete POSSourceSalesReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceSalesReturn",
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
        /** @var POSSourceSalesReturn $pOSSourceSalesReturn */
        $pOSSourceSalesReturn = $this->pOSSourceSalesReturnRepository->findWithoutFail($id);

        if (empty($pOSSourceSalesReturn)) {
            return $this->sendError(trans('custom.p_o_s_source_sales_return_not_found'));
        }

        $pOSSourceSalesReturn->delete();

        return $this->sendSuccess('P O S Source Sales Return deleted successfully');
    }
}
