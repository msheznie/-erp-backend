<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSourceMenuSalesMasterAPIRequest;
use App\Http\Requests\API\UpdatePOSSourceMenuSalesMasterAPIRequest;
use App\Models\POSSourceMenuSalesMaster;
use App\Repositories\POSSourceMenuSalesMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSourceMenuSalesMasterController
 * @package App\Http\Controllers\API
 */

class POSSourceMenuSalesMasterAPIController extends AppBaseController
{
    /** @var  POSSourceMenuSalesMasterRepository */
    private $pOSSourceMenuSalesMasterRepository;

    public function __construct(POSSourceMenuSalesMasterRepository $pOSSourceMenuSalesMasterRepo)
    {
        $this->pOSSourceMenuSalesMasterRepository = $pOSSourceMenuSalesMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceMenuSalesMasters",
     *      summary="Get a listing of the POSSourceMenuSalesMasters.",
     *      tags={"POSSourceMenuSalesMaster"},
     *      description="Get all POSSourceMenuSalesMasters",
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
     *                  @SWG\Items(ref="#/definitions/POSSourceMenuSalesMaster")
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
        $this->pOSSourceMenuSalesMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSourceMenuSalesMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSourceMenuSalesMasters = $this->pOSSourceMenuSalesMasterRepository->all();

        return $this->sendResponse($pOSSourceMenuSalesMasters->toArray(), trans('custom.p_o_s_source_menu_sales_masters_retrieved_successf'));
    }

    /**
     * @param CreatePOSSourceMenuSalesMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSourceMenuSalesMasters",
     *      summary="Store a newly created POSSourceMenuSalesMaster in storage",
     *      tags={"POSSourceMenuSalesMaster"},
     *      description="Store POSSourceMenuSalesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceMenuSalesMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceMenuSalesMaster")
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
     *                  ref="#/definitions/POSSourceMenuSalesMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSourceMenuSalesMasterAPIRequest $request)
    {
        $input = $request->all();

        $pOSSourceMenuSalesMaster = $this->pOSSourceMenuSalesMasterRepository->create($input);

        return $this->sendResponse($pOSSourceMenuSalesMaster->toArray(), trans('custom.p_o_s_source_menu_sales_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceMenuSalesMasters/{id}",
     *      summary="Display the specified POSSourceMenuSalesMaster",
     *      tags={"POSSourceMenuSalesMaster"},
     *      description="Get POSSourceMenuSalesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenuSalesMaster",
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
     *                  ref="#/definitions/POSSourceMenuSalesMaster"
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
        /** @var POSSourceMenuSalesMaster $pOSSourceMenuSalesMaster */
        $pOSSourceMenuSalesMaster = $this->pOSSourceMenuSalesMasterRepository->findWithoutFail($id);

        if (empty($pOSSourceMenuSalesMaster)) {
            return $this->sendError(trans('custom.p_o_s_source_menu_sales_master_not_found'));
        }

        return $this->sendResponse($pOSSourceMenuSalesMaster->toArray(), trans('custom.p_o_s_source_menu_sales_master_retrieved_successfu'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSourceMenuSalesMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSourceMenuSalesMasters/{id}",
     *      summary="Update the specified POSSourceMenuSalesMaster in storage",
     *      tags={"POSSourceMenuSalesMaster"},
     *      description="Update POSSourceMenuSalesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenuSalesMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceMenuSalesMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceMenuSalesMaster")
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
     *                  ref="#/definitions/POSSourceMenuSalesMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSourceMenuSalesMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSourceMenuSalesMaster $pOSSourceMenuSalesMaster */
        $pOSSourceMenuSalesMaster = $this->pOSSourceMenuSalesMasterRepository->findWithoutFail($id);

        if (empty($pOSSourceMenuSalesMaster)) {
            return $this->sendError(trans('custom.p_o_s_source_menu_sales_master_not_found'));
        }

        $pOSSourceMenuSalesMaster = $this->pOSSourceMenuSalesMasterRepository->update($input, $id);

        return $this->sendResponse($pOSSourceMenuSalesMaster->toArray(), trans('custom.possourcemenusalesmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSourceMenuSalesMasters/{id}",
     *      summary="Remove the specified POSSourceMenuSalesMaster from storage",
     *      tags={"POSSourceMenuSalesMaster"},
     *      description="Delete POSSourceMenuSalesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenuSalesMaster",
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
        /** @var POSSourceMenuSalesMaster $pOSSourceMenuSalesMaster */
        $pOSSourceMenuSalesMaster = $this->pOSSourceMenuSalesMasterRepository->findWithoutFail($id);

        if (empty($pOSSourceMenuSalesMaster)) {
            return $this->sendError(trans('custom.p_o_s_source_menu_sales_master_not_found'));
        }

        $pOSSourceMenuSalesMaster->delete();

        return $this->sendSuccess('P O S Source Menu Sales Master deleted successfully');
    }
}
