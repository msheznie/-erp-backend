<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSOURCEMenuSalesOutletTaxesAPIRequest;
use App\Http\Requests\API\UpdatePOSSOURCEMenuSalesOutletTaxesAPIRequest;
use App\Models\POSSOURCEMenuSalesOutletTaxes;
use App\Repositories\POSSOURCEMenuSalesOutletTaxesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSOURCEMenuSalesOutletTaxesController
 * @package App\Http\Controllers\API
 */

class POSSOURCEMenuSalesOutletTaxesAPIController extends AppBaseController
{
    /** @var  POSSOURCEMenuSalesOutletTaxesRepository */
    private $pOSSOURCEMenuSalesOutletTaxesRepository;

    public function __construct(POSSOURCEMenuSalesOutletTaxesRepository $pOSSOURCEMenuSalesOutletTaxesRepo)
    {
        $this->pOSSOURCEMenuSalesOutletTaxesRepository = $pOSSOURCEMenuSalesOutletTaxesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSOURCEMenuSalesOutletTaxes",
     *      summary="Get a listing of the POSSOURCEMenuSalesOutletTaxes.",
     *      tags={"POSSOURCEMenuSalesOutletTaxes"},
     *      description="Get all POSSOURCEMenuSalesOutletTaxes",
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
     *                  @SWG\Items(ref="#/definitions/POSSOURCEMenuSalesOutletTaxes")
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
        $this->pOSSOURCEMenuSalesOutletTaxesRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSOURCEMenuSalesOutletTaxesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSOURCEMenuSalesOutletTaxes = $this->pOSSOURCEMenuSalesOutletTaxesRepository->all();

        return $this->sendResponse($pOSSOURCEMenuSalesOutletTaxes->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes_retrieve'));
    }

    /**
     * @param CreatePOSSOURCEMenuSalesOutletTaxesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSOURCEMenuSalesOutletTaxes",
     *      summary="Store a newly created POSSOURCEMenuSalesOutletTaxes in storage",
     *      tags={"POSSOURCEMenuSalesOutletTaxes"},
     *      description="Store POSSOURCEMenuSalesOutletTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSOURCEMenuSalesOutletTaxes that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSOURCEMenuSalesOutletTaxes")
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
     *                  ref="#/definitions/POSSOURCEMenuSalesOutletTaxes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSOURCEMenuSalesOutletTaxesAPIRequest $request)
    {
        $input = $request->all();

        $pOSSOURCEMenuSalesOutletTaxes = $this->pOSSOURCEMenuSalesOutletTaxesRepository->create($input);

        return $this->sendResponse($pOSSOURCEMenuSalesOutletTaxes->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes_saved_su'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSOURCEMenuSalesOutletTaxes/{id}",
     *      summary="Display the specified POSSOURCEMenuSalesOutletTaxes",
     *      tags={"POSSOURCEMenuSalesOutletTaxes"},
     *      description="Get POSSOURCEMenuSalesOutletTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCEMenuSalesOutletTaxes",
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
     *                  ref="#/definitions/POSSOURCEMenuSalesOutletTaxes"
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
        /** @var POSSOURCEMenuSalesOutletTaxes $pOSSOURCEMenuSalesOutletTaxes */
        $pOSSOURCEMenuSalesOutletTaxes = $this->pOSSOURCEMenuSalesOutletTaxesRepository->findWithoutFail($id);

        if (empty($pOSSOURCEMenuSalesOutletTaxes)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes_not_foun'));
        }

        return $this->sendResponse($pOSSOURCEMenuSalesOutletTaxes->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes_retrieve'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSOURCEMenuSalesOutletTaxesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSOURCEMenuSalesOutletTaxes/{id}",
     *      summary="Update the specified POSSOURCEMenuSalesOutletTaxes in storage",
     *      tags={"POSSOURCEMenuSalesOutletTaxes"},
     *      description="Update POSSOURCEMenuSalesOutletTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCEMenuSalesOutletTaxes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSOURCEMenuSalesOutletTaxes that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSOURCEMenuSalesOutletTaxes")
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
     *                  ref="#/definitions/POSSOURCEMenuSalesOutletTaxes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSOURCEMenuSalesOutletTaxesAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSOURCEMenuSalesOutletTaxes $pOSSOURCEMenuSalesOutletTaxes */
        $pOSSOURCEMenuSalesOutletTaxes = $this->pOSSOURCEMenuSalesOutletTaxesRepository->findWithoutFail($id);

        if (empty($pOSSOURCEMenuSalesOutletTaxes)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes_not_foun'));
        }

        $pOSSOURCEMenuSalesOutletTaxes = $this->pOSSOURCEMenuSalesOutletTaxesRepository->update($input, $id);

        return $this->sendResponse($pOSSOURCEMenuSalesOutletTaxes->toArray(), trans('custom.possourcemenusalesoutlettaxes_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSOURCEMenuSalesOutletTaxes/{id}",
     *      summary="Remove the specified POSSOURCEMenuSalesOutletTaxes from storage",
     *      tags={"POSSOURCEMenuSalesOutletTaxes"},
     *      description="Delete POSSOURCEMenuSalesOutletTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCEMenuSalesOutletTaxes",
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
        /** @var POSSOURCEMenuSalesOutletTaxes $pOSSOURCEMenuSalesOutletTaxes */
        $pOSSOURCEMenuSalesOutletTaxes = $this->pOSSOURCEMenuSalesOutletTaxesRepository->findWithoutFail($id);

        if (empty($pOSSOURCEMenuSalesOutletTaxes)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes_not_foun'));
        }

        $pOSSOURCEMenuSalesOutletTaxes->delete();

        return $this->sendSuccess('P O S S O U R C E Menu Sales Outlet Taxes deleted successfully');
    }
}
