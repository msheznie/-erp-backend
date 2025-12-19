<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSOURCEMenuSalesTaxesAPIRequest;
use App\Http\Requests\API\UpdatePOSSOURCEMenuSalesTaxesAPIRequest;
use App\Models\POSSOURCEMenuSalesTaxes;
use App\Repositories\POSSOURCEMenuSalesTaxesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSOURCEMenuSalesTaxesController
 * @package App\Http\Controllers\API
 */

class POSSOURCEMenuSalesTaxesAPIController extends AppBaseController
{
    /** @var  POSSOURCEMenuSalesTaxesRepository */
    private $pOSSOURCEMenuSalesTaxesRepository;

    public function __construct(POSSOURCEMenuSalesTaxesRepository $pOSSOURCEMenuSalesTaxesRepo)
    {
        $this->pOSSOURCEMenuSalesTaxesRepository = $pOSSOURCEMenuSalesTaxesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSOURCEMenuSalesTaxes",
     *      summary="Get a listing of the POSSOURCEMenuSalesTaxes.",
     *      tags={"POSSOURCEMenuSalesTaxes"},
     *      description="Get all POSSOURCEMenuSalesTaxes",
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
     *                  @SWG\Items(ref="#/definitions/POSSOURCEMenuSalesTaxes")
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
        $this->pOSSOURCEMenuSalesTaxesRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSOURCEMenuSalesTaxesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSOURCEMenuSalesTaxes = $this->pOSSOURCEMenuSalesTaxesRepository->all();

        return $this->sendResponse($pOSSOURCEMenuSalesTaxes->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_menu_sales_taxes_retrieved_succe'));
    }

    /**
     * @param CreatePOSSOURCEMenuSalesTaxesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSOURCEMenuSalesTaxes",
     *      summary="Store a newly created POSSOURCEMenuSalesTaxes in storage",
     *      tags={"POSSOURCEMenuSalesTaxes"},
     *      description="Store POSSOURCEMenuSalesTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSOURCEMenuSalesTaxes that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSOURCEMenuSalesTaxes")
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
     *                  ref="#/definitions/POSSOURCEMenuSalesTaxes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSOURCEMenuSalesTaxesAPIRequest $request)
    {
        $input = $request->all();

        $pOSSOURCEMenuSalesTaxes = $this->pOSSOURCEMenuSalesTaxesRepository->create($input);

        return $this->sendResponse($pOSSOURCEMenuSalesTaxes->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_menu_sales_taxes_saved_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSOURCEMenuSalesTaxes/{id}",
     *      summary="Display the specified POSSOURCEMenuSalesTaxes",
     *      tags={"POSSOURCEMenuSalesTaxes"},
     *      description="Get POSSOURCEMenuSalesTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCEMenuSalesTaxes",
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
     *                  ref="#/definitions/POSSOURCEMenuSalesTaxes"
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
        /** @var POSSOURCEMenuSalesTaxes $pOSSOURCEMenuSalesTaxes */
        $pOSSOURCEMenuSalesTaxes = $this->pOSSOURCEMenuSalesTaxesRepository->findWithoutFail($id);

        if (empty($pOSSOURCEMenuSalesTaxes)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_menu_sales_taxes_not_found'));
        }

        return $this->sendResponse($pOSSOURCEMenuSalesTaxes->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_menu_sales_taxes_retrieved_succe'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSOURCEMenuSalesTaxesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSOURCEMenuSalesTaxes/{id}",
     *      summary="Update the specified POSSOURCEMenuSalesTaxes in storage",
     *      tags={"POSSOURCEMenuSalesTaxes"},
     *      description="Update POSSOURCEMenuSalesTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCEMenuSalesTaxes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSOURCEMenuSalesTaxes that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSOURCEMenuSalesTaxes")
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
     *                  ref="#/definitions/POSSOURCEMenuSalesTaxes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSOURCEMenuSalesTaxesAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSOURCEMenuSalesTaxes $pOSSOURCEMenuSalesTaxes */
        $pOSSOURCEMenuSalesTaxes = $this->pOSSOURCEMenuSalesTaxesRepository->findWithoutFail($id);

        if (empty($pOSSOURCEMenuSalesTaxes)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_menu_sales_taxes_not_found'));
        }

        $pOSSOURCEMenuSalesTaxes = $this->pOSSOURCEMenuSalesTaxesRepository->update($input, $id);

        return $this->sendResponse($pOSSOURCEMenuSalesTaxes->toArray(), trans('custom.possourcemenusalestaxes_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSOURCEMenuSalesTaxes/{id}",
     *      summary="Remove the specified POSSOURCEMenuSalesTaxes from storage",
     *      tags={"POSSOURCEMenuSalesTaxes"},
     *      description="Delete POSSOURCEMenuSalesTaxes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCEMenuSalesTaxes",
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
        /** @var POSSOURCEMenuSalesTaxes $pOSSOURCEMenuSalesTaxes */
        $pOSSOURCEMenuSalesTaxes = $this->pOSSOURCEMenuSalesTaxesRepository->findWithoutFail($id);

        if (empty($pOSSOURCEMenuSalesTaxes)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_menu_sales_taxes_not_found'));
        }

        $pOSSOURCEMenuSalesTaxes->delete();

        return $this->sendSuccess('P O S S O U R C E Menu Sales Taxes deleted successfully');
    }
}
