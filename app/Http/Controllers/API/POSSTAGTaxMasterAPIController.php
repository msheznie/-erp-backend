<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSTAGTaxMasterAPIRequest;
use App\Http\Requests\API\UpdatePOSSTAGTaxMasterAPIRequest;
use App\Models\POSSTAGTaxMaster;
use App\Repositories\POSSTAGTaxMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSTAGTaxMasterController
 * @package App\Http\Controllers\API
 */

class POSSTAGTaxMasterAPIController extends AppBaseController
{
    /** @var  POSSTAGTaxMasterRepository */
    private $pOSSTAGTaxMasterRepository;

    public function __construct(POSSTAGTaxMasterRepository $pOSSTAGTaxMasterRepo)
    {
        $this->pOSSTAGTaxMasterRepository = $pOSSTAGTaxMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGTaxMasters",
     *      summary="Get a listing of the POSSTAGTaxMasters.",
     *      tags={"POSSTAGTaxMaster"},
     *      description="Get all POSSTAGTaxMasters",
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
     *                  @SWG\Items(ref="#/definitions/POSSTAGTaxMaster")
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
        $this->pOSSTAGTaxMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSTAGTaxMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSTAGTaxMasters = $this->pOSSTAGTaxMasterRepository->all();

        return $this->sendResponse($pOSSTAGTaxMasters->toArray(), trans('custom.p_o_s_s_t_a_g_tax_masters_retrieved_successfully'));
    }

    /**
     * @param CreatePOSSTAGTaxMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSTAGTaxMasters",
     *      summary="Store a newly created POSSTAGTaxMaster in storage",
     *      tags={"POSSTAGTaxMaster"},
     *      description="Store POSSTAGTaxMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGTaxMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGTaxMaster")
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
     *                  ref="#/definitions/POSSTAGTaxMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSTAGTaxMasterAPIRequest $request)
    {
        $input = $request->all();

        $pOSSTAGTaxMaster = $this->pOSSTAGTaxMasterRepository->create($input);

        return $this->sendResponse($pOSSTAGTaxMaster->toArray(), trans('custom.p_o_s_s_t_a_g_tax_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGTaxMasters/{id}",
     *      summary="Display the specified POSSTAGTaxMaster",
     *      tags={"POSSTAGTaxMaster"},
     *      description="Get POSSTAGTaxMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGTaxMaster",
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
     *                  ref="#/definitions/POSSTAGTaxMaster"
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
        /** @var POSSTAGTaxMaster $pOSSTAGTaxMaster */
        $pOSSTAGTaxMaster = $this->pOSSTAGTaxMasterRepository->findWithoutFail($id);

        if (empty($pOSSTAGTaxMaster)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_tax_master_not_found'));
        }

        return $this->sendResponse($pOSSTAGTaxMaster->toArray(), trans('custom.p_o_s_s_t_a_g_tax_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSTAGTaxMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSTAGTaxMasters/{id}",
     *      summary="Update the specified POSSTAGTaxMaster in storage",
     *      tags={"POSSTAGTaxMaster"},
     *      description="Update POSSTAGTaxMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGTaxMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGTaxMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGTaxMaster")
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
     *                  ref="#/definitions/POSSTAGTaxMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSTAGTaxMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSTAGTaxMaster $pOSSTAGTaxMaster */
        $pOSSTAGTaxMaster = $this->pOSSTAGTaxMasterRepository->findWithoutFail($id);

        if (empty($pOSSTAGTaxMaster)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_tax_master_not_found'));
        }

        $pOSSTAGTaxMaster = $this->pOSSTAGTaxMasterRepository->update($input, $id);

        return $this->sendResponse($pOSSTAGTaxMaster->toArray(), trans('custom.posstagtaxmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSTAGTaxMasters/{id}",
     *      summary="Remove the specified POSSTAGTaxMaster from storage",
     *      tags={"POSSTAGTaxMaster"},
     *      description="Delete POSSTAGTaxMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGTaxMaster",
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
        /** @var POSSTAGTaxMaster $pOSSTAGTaxMaster */
        $pOSSTAGTaxMaster = $this->pOSSTAGTaxMasterRepository->findWithoutFail($id);

        if (empty($pOSSTAGTaxMaster)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_tax_master_not_found'));
        }

        $pOSSTAGTaxMaster->delete();

        return $this->sendSuccess('P O S S T A G Tax Master deleted successfully');
    }
}
