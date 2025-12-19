<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSStagMenuSalesMasterAPIRequest;
use App\Http\Requests\API\UpdatePOSStagMenuSalesMasterAPIRequest;
use App\Models\POSStagMenuSalesMaster;
use App\Repositories\POSStagMenuSalesMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSStagMenuSalesMasterController
 * @package App\Http\Controllers\API
 */

class POSStagMenuSalesMasterAPIController extends AppBaseController
{
    /** @var  POSStagMenuSalesMasterRepository */
    private $pOSStagMenuSalesMasterRepository;

    public function __construct(POSStagMenuSalesMasterRepository $pOSStagMenuSalesMasterRepo)
    {
        $this->pOSStagMenuSalesMasterRepository = $pOSStagMenuSalesMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagMenuSalesMasters",
     *      summary="Get a listing of the POSStagMenuSalesMasters.",
     *      tags={"POSStagMenuSalesMaster"},
     *      description="Get all POSStagMenuSalesMasters",
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
     *                  @SWG\Items(ref="#/definitions/POSStagMenuSalesMaster")
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
        $this->pOSStagMenuSalesMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSStagMenuSalesMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSStagMenuSalesMasters = $this->pOSStagMenuSalesMasterRepository->all();

        return $this->sendResponse($pOSStagMenuSalesMasters->toArray(), trans('custom.p_o_s_stag_menu_sales_masters_retrieved_successful'));
    }

    /**
     * @param CreatePOSStagMenuSalesMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSStagMenuSalesMasters",
     *      summary="Store a newly created POSStagMenuSalesMaster in storage",
     *      tags={"POSStagMenuSalesMaster"},
     *      description="Store POSStagMenuSalesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagMenuSalesMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagMenuSalesMaster")
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
     *                  ref="#/definitions/POSStagMenuSalesMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSStagMenuSalesMasterAPIRequest $request)
    {
        $input = $request->all();

        $pOSStagMenuSalesMaster = $this->pOSStagMenuSalesMasterRepository->create($input);

        return $this->sendResponse($pOSStagMenuSalesMaster->toArray(), trans('custom.p_o_s_stag_menu_sales_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagMenuSalesMasters/{id}",
     *      summary="Display the specified POSStagMenuSalesMaster",
     *      tags={"POSStagMenuSalesMaster"},
     *      description="Get POSStagMenuSalesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenuSalesMaster",
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
     *                  ref="#/definitions/POSStagMenuSalesMaster"
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
        /** @var POSStagMenuSalesMaster $pOSStagMenuSalesMaster */
        $pOSStagMenuSalesMaster = $this->pOSStagMenuSalesMasterRepository->findWithoutFail($id);

        if (empty($pOSStagMenuSalesMaster)) {
            return $this->sendError(trans('custom.p_o_s_stag_menu_sales_master_not_found'));
        }

        return $this->sendResponse($pOSStagMenuSalesMaster->toArray(), trans('custom.p_o_s_stag_menu_sales_master_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdatePOSStagMenuSalesMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSStagMenuSalesMasters/{id}",
     *      summary="Update the specified POSStagMenuSalesMaster in storage",
     *      tags={"POSStagMenuSalesMaster"},
     *      description="Update POSStagMenuSalesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenuSalesMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagMenuSalesMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagMenuSalesMaster")
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
     *                  ref="#/definitions/POSStagMenuSalesMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSStagMenuSalesMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSStagMenuSalesMaster $pOSStagMenuSalesMaster */
        $pOSStagMenuSalesMaster = $this->pOSStagMenuSalesMasterRepository->findWithoutFail($id);

        if (empty($pOSStagMenuSalesMaster)) {
            return $this->sendError(trans('custom.p_o_s_stag_menu_sales_master_not_found'));
        }

        $pOSStagMenuSalesMaster = $this->pOSStagMenuSalesMasterRepository->update($input, $id);

        return $this->sendResponse($pOSStagMenuSalesMaster->toArray(), trans('custom.posstagmenusalesmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSStagMenuSalesMasters/{id}",
     *      summary="Remove the specified POSStagMenuSalesMaster from storage",
     *      tags={"POSStagMenuSalesMaster"},
     *      description="Delete POSStagMenuSalesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenuSalesMaster",
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
        /** @var POSStagMenuSalesMaster $pOSStagMenuSalesMaster */
        $pOSStagMenuSalesMaster = $this->pOSStagMenuSalesMasterRepository->findWithoutFail($id);

        if (empty($pOSStagMenuSalesMaster)) {
            return $this->sendError(trans('custom.p_o_s_stag_menu_sales_master_not_found'));
        }

        $pOSStagMenuSalesMaster->delete();

        return $this->sendSuccess('P O S Stag Menu Sales Master deleted successfully');
    }
}
