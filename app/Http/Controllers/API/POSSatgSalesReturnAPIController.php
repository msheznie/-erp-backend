<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSatgSalesReturnAPIRequest;
use App\Http\Requests\API\UpdatePOSSatgSalesReturnAPIRequest;
use App\Models\POSSatgSalesReturn;
use App\Repositories\POSSatgSalesReturnRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSatgSalesReturnController
 * @package App\Http\Controllers\API
 */

class POSSatgSalesReturnAPIController extends AppBaseController
{
    /** @var  POSSatgSalesReturnRepository */
    private $pOSSatgSalesReturnRepository;

    public function __construct(POSSatgSalesReturnRepository $pOSSatgSalesReturnRepo)
    {
        $this->pOSSatgSalesReturnRepository = $pOSSatgSalesReturnRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSatgSalesReturns",
     *      summary="Get a listing of the POSSatgSalesReturns.",
     *      tags={"POSSatgSalesReturn"},
     *      description="Get all POSSatgSalesReturns",
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
     *                  @SWG\Items(ref="#/definitions/POSSatgSalesReturn")
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
        $this->pOSSatgSalesReturnRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSatgSalesReturnRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSatgSalesReturns = $this->pOSSatgSalesReturnRepository->all();

        return $this->sendResponse($pOSSatgSalesReturns->toArray(), trans('custom.p_o_s_satg_sales_returns_retrieved_successfully'));
    }

    /**
     * @param CreatePOSSatgSalesReturnAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSatgSalesReturns",
     *      summary="Store a newly created POSSatgSalesReturn in storage",
     *      tags={"POSSatgSalesReturn"},
     *      description="Store POSSatgSalesReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSatgSalesReturn that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSatgSalesReturn")
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
     *                  ref="#/definitions/POSSatgSalesReturn"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSatgSalesReturnAPIRequest $request)
    {
        $input = $request->all();

        $pOSSatgSalesReturn = $this->pOSSatgSalesReturnRepository->create($input);

        return $this->sendResponse($pOSSatgSalesReturn->toArray(), trans('custom.p_o_s_satg_sales_return_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSatgSalesReturns/{id}",
     *      summary="Display the specified POSSatgSalesReturn",
     *      tags={"POSSatgSalesReturn"},
     *      description="Get POSSatgSalesReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSatgSalesReturn",
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
     *                  ref="#/definitions/POSSatgSalesReturn"
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
        /** @var POSSatgSalesReturn $pOSSatgSalesReturn */
        $pOSSatgSalesReturn = $this->pOSSatgSalesReturnRepository->findWithoutFail($id);

        if (empty($pOSSatgSalesReturn)) {
            return $this->sendError(trans('custom.p_o_s_satg_sales_return_not_found'));
        }

        return $this->sendResponse($pOSSatgSalesReturn->toArray(), trans('custom.p_o_s_satg_sales_return_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSatgSalesReturnAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSatgSalesReturns/{id}",
     *      summary="Update the specified POSSatgSalesReturn in storage",
     *      tags={"POSSatgSalesReturn"},
     *      description="Update POSSatgSalesReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSatgSalesReturn",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSatgSalesReturn that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSatgSalesReturn")
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
     *                  ref="#/definitions/POSSatgSalesReturn"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSatgSalesReturnAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSatgSalesReturn $pOSSatgSalesReturn */
        $pOSSatgSalesReturn = $this->pOSSatgSalesReturnRepository->findWithoutFail($id);

        if (empty($pOSSatgSalesReturn)) {
            return $this->sendError(trans('custom.p_o_s_satg_sales_return_not_found'));
        }

        $pOSSatgSalesReturn = $this->pOSSatgSalesReturnRepository->update($input, $id);

        return $this->sendResponse($pOSSatgSalesReturn->toArray(), trans('custom.possatgsalesreturn_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSatgSalesReturns/{id}",
     *      summary="Remove the specified POSSatgSalesReturn from storage",
     *      tags={"POSSatgSalesReturn"},
     *      description="Delete POSSatgSalesReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSatgSalesReturn",
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
        /** @var POSSatgSalesReturn $pOSSatgSalesReturn */
        $pOSSatgSalesReturn = $this->pOSSatgSalesReturnRepository->findWithoutFail($id);

        if (empty($pOSSatgSalesReturn)) {
            return $this->sendError(trans('custom.p_o_s_satg_sales_return_not_found'));
        }

        $pOSSatgSalesReturn->delete();

        return $this->sendSuccess('P O S Satg Sales Return deleted successfully');
    }
}
