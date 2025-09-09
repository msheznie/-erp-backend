<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSTAGMenuSalesOutletTaxesAPIRequest;
use App\Http\Requests\API\UpdatePOSSTAGMenuSalesOutletTaxesAPIRequest;
use App\Models\POSSTAGMenuSalesOutletTaxes;
use App\Repositories\POSSTAGMenuSalesOutletTaxesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSTAGMenuSalesOutletTaxesController
 * @package App\Http\Controllers\API
 */

class POSSTAGMenuSalesOutletTaxesAPIController extends AppBaseController
{
    /** @var  POSSTAGMenuSalesOutletTaxesRepository */
    private $pOSSTAGMenuSalesOutletTaxesRepository;

    public function __construct(POSSTAGMenuSalesOutletTaxesRepository $pOSSTAGMenuSalesOutletTaxesRepo)
    {
        $this->pOSSTAGMenuSalesOutletTaxesRepository = $pOSSTAGMenuSalesOutletTaxesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGMenuSalesOutletTaxes",
     *      summary="Get a listing of the POSSTAGMenuSalesOutletTaxes.",
     *      tags={"POSSTAGMenuSalesOutletTaxes"},
     *      description="Get all POSSTAGMenuSalesOutletTaxes",
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
     *                  @SWG\Items(ref="#/definitions/POSSTAGMenuSalesOutletTaxes")
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
        $this->pOSSTAGMenuSalesOutletTaxesRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSTAGMenuSalesOutletTaxesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSTAGMenuSalesOutletTaxes = $this->pOSSTAGMenuSalesOutletTaxesRepository->all();

        return $this->sendResponse($pOSSTAGMenuSalesOutletTaxes->toArray(), trans('custom.p_o_s_s_t_a_g_menu_sales_outlet_taxes_retrieved_su'));
    }

    /**
     * @param CreatePOSSTAGMenuSalesOutletTaxesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSTAGMenuSalesOutletTaxes",
     *      summary="Store a newly created POSSTAGMenuSalesOutletTaxes in storage",
     *      tags={"POSSTAGMenuSalesOutletTaxes"},
     *      description="Store POSSTAGMenuSalesOutletTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGMenuSalesOutletTaxes that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGMenuSalesOutletTaxes")
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
     *                  ref="#/definitions/POSSTAGMenuSalesOutletTaxes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSTAGMenuSalesOutletTaxesAPIRequest $request)
    {
        $input = $request->all();

        $pOSSTAGMenuSalesOutletTaxes = $this->pOSSTAGMenuSalesOutletTaxesRepository->create($input);

        return $this->sendResponse($pOSSTAGMenuSalesOutletTaxes->toArray(), trans('custom.p_o_s_s_t_a_g_menu_sales_outlet_taxes_saved_succes'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGMenuSalesOutletTaxes/{id}",
     *      summary="Display the specified POSSTAGMenuSalesOutletTaxes",
     *      tags={"POSSTAGMenuSalesOutletTaxes"},
     *      description="Get POSSTAGMenuSalesOutletTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGMenuSalesOutletTaxes",
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
     *                  ref="#/definitions/POSSTAGMenuSalesOutletTaxes"
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
        /** @var POSSTAGMenuSalesOutletTaxes $pOSSTAGMenuSalesOutletTaxes */
        $pOSSTAGMenuSalesOutletTaxes = $this->pOSSTAGMenuSalesOutletTaxesRepository->findWithoutFail($id);

        if (empty($pOSSTAGMenuSalesOutletTaxes)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_menu_sales_outlet_taxes_not_found'));
        }

        return $this->sendResponse($pOSSTAGMenuSalesOutletTaxes->toArray(), trans('custom.p_o_s_s_t_a_g_menu_sales_outlet_taxes_retrieved_su'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSTAGMenuSalesOutletTaxesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSTAGMenuSalesOutletTaxes/{id}",
     *      summary="Update the specified POSSTAGMenuSalesOutletTaxes in storage",
     *      tags={"POSSTAGMenuSalesOutletTaxes"},
     *      description="Update POSSTAGMenuSalesOutletTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGMenuSalesOutletTaxes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGMenuSalesOutletTaxes that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGMenuSalesOutletTaxes")
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
     *                  ref="#/definitions/POSSTAGMenuSalesOutletTaxes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSTAGMenuSalesOutletTaxesAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSTAGMenuSalesOutletTaxes $pOSSTAGMenuSalesOutletTaxes */
        $pOSSTAGMenuSalesOutletTaxes = $this->pOSSTAGMenuSalesOutletTaxesRepository->findWithoutFail($id);

        if (empty($pOSSTAGMenuSalesOutletTaxes)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_menu_sales_outlet_taxes_not_found'));
        }

        $pOSSTAGMenuSalesOutletTaxes = $this->pOSSTAGMenuSalesOutletTaxesRepository->update($input, $id);

        return $this->sendResponse($pOSSTAGMenuSalesOutletTaxes->toArray(), trans('custom.posstagmenusalesoutlettaxes_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSTAGMenuSalesOutletTaxes/{id}",
     *      summary="Remove the specified POSSTAGMenuSalesOutletTaxes from storage",
     *      tags={"POSSTAGMenuSalesOutletTaxes"},
     *      description="Delete POSSTAGMenuSalesOutletTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGMenuSalesOutletTaxes",
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
        /** @var POSSTAGMenuSalesOutletTaxes $pOSSTAGMenuSalesOutletTaxes */
        $pOSSTAGMenuSalesOutletTaxes = $this->pOSSTAGMenuSalesOutletTaxesRepository->findWithoutFail($id);

        if (empty($pOSSTAGMenuSalesOutletTaxes)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_menu_sales_outlet_taxes_not_found'));
        }

        $pOSSTAGMenuSalesOutletTaxes->delete();

        return $this->sendSuccess('P O S S T A G Menu Sales Outlet Taxes deleted successfully');
    }
}
