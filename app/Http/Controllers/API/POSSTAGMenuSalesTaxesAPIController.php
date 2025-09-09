<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSTAGMenuSalesTaxesAPIRequest;
use App\Http\Requests\API\UpdatePOSSTAGMenuSalesTaxesAPIRequest;
use App\Models\POSSTAGMenuSalesTaxes;
use App\Repositories\POSSTAGMenuSalesTaxesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSTAGMenuSalesTaxesController
 * @package App\Http\Controllers\API
 */

class POSSTAGMenuSalesTaxesAPIController extends AppBaseController
{
    /** @var  POSSTAGMenuSalesTaxesRepository */
    private $pOSSTAGMenuSalesTaxesRepository;

    public function __construct(POSSTAGMenuSalesTaxesRepository $pOSSTAGMenuSalesTaxesRepo)
    {
        $this->pOSSTAGMenuSalesTaxesRepository = $pOSSTAGMenuSalesTaxesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGMenuSalesTaxes",
     *      summary="Get a listing of the POSSTAGMenuSalesTaxes.",
     *      tags={"POSSTAGMenuSalesTaxes"},
     *      description="Get all POSSTAGMenuSalesTaxes",
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
     *                  @SWG\Items(ref="#/definitions/POSSTAGMenuSalesTaxes")
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
        $this->pOSSTAGMenuSalesTaxesRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSTAGMenuSalesTaxesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSTAGMenuSalesTaxes = $this->pOSSTAGMenuSalesTaxesRepository->all();

        return $this->sendResponse($pOSSTAGMenuSalesTaxes->toArray(), trans('custom.p_o_s_s_t_a_g_menu_sales_taxes_retrieved_successfu'));
    }

    /**
     * @param CreatePOSSTAGMenuSalesTaxesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSTAGMenuSalesTaxes",
     *      summary="Store a newly created POSSTAGMenuSalesTaxes in storage",
     *      tags={"POSSTAGMenuSalesTaxes"},
     *      description="Store POSSTAGMenuSalesTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGMenuSalesTaxes that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGMenuSalesTaxes")
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
     *                  ref="#/definitions/POSSTAGMenuSalesTaxes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSTAGMenuSalesTaxesAPIRequest $request)
    {
        $input = $request->all();

        $pOSSTAGMenuSalesTaxes = $this->pOSSTAGMenuSalesTaxesRepository->create($input);

        return $this->sendResponse($pOSSTAGMenuSalesTaxes->toArray(), trans('custom.p_o_s_s_t_a_g_menu_sales_taxes_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGMenuSalesTaxes/{id}",
     *      summary="Display the specified POSSTAGMenuSalesTaxes",
     *      tags={"POSSTAGMenuSalesTaxes"},
     *      description="Get POSSTAGMenuSalesTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGMenuSalesTaxes",
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
     *                  ref="#/definitions/POSSTAGMenuSalesTaxes"
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
        /** @var POSSTAGMenuSalesTaxes $pOSSTAGMenuSalesTaxes */
        $pOSSTAGMenuSalesTaxes = $this->pOSSTAGMenuSalesTaxesRepository->findWithoutFail($id);

        if (empty($pOSSTAGMenuSalesTaxes)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_menu_sales_taxes_not_found'));
        }

        return $this->sendResponse($pOSSTAGMenuSalesTaxes->toArray(), trans('custom.p_o_s_s_t_a_g_menu_sales_taxes_retrieved_successfu'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSTAGMenuSalesTaxesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSTAGMenuSalesTaxes/{id}",
     *      summary="Update the specified POSSTAGMenuSalesTaxes in storage",
     *      tags={"POSSTAGMenuSalesTaxes"},
     *      description="Update POSSTAGMenuSalesTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGMenuSalesTaxes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGMenuSalesTaxes that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGMenuSalesTaxes")
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
     *                  ref="#/definitions/POSSTAGMenuSalesTaxes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSTAGMenuSalesTaxesAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSTAGMenuSalesTaxes $pOSSTAGMenuSalesTaxes */
        $pOSSTAGMenuSalesTaxes = $this->pOSSTAGMenuSalesTaxesRepository->findWithoutFail($id);

        if (empty($pOSSTAGMenuSalesTaxes)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_menu_sales_taxes_not_found'));
        }

        $pOSSTAGMenuSalesTaxes = $this->pOSSTAGMenuSalesTaxesRepository->update($input, $id);

        return $this->sendResponse($pOSSTAGMenuSalesTaxes->toArray(), trans('custom.posstagmenusalestaxes_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSTAGMenuSalesTaxes/{id}",
     *      summary="Remove the specified POSSTAGMenuSalesTaxes from storage",
     *      tags={"POSSTAGMenuSalesTaxes"},
     *      description="Delete POSSTAGMenuSalesTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGMenuSalesTaxes",
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
        /** @var POSSTAGMenuSalesTaxes $pOSSTAGMenuSalesTaxes */
        $pOSSTAGMenuSalesTaxes = $this->pOSSTAGMenuSalesTaxesRepository->findWithoutFail($id);

        if (empty($pOSSTAGMenuSalesTaxes)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_menu_sales_taxes_not_found'));
        }

        $pOSSTAGMenuSalesTaxes->delete();

        return $this->sendSuccess('P O S S T A G Menu Sales Taxes deleted successfully');
    }
}
