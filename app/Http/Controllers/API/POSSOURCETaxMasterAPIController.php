<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSOURCETaxMasterAPIRequest;
use App\Http\Requests\API\UpdatePOSSOURCETaxMasterAPIRequest;
use App\Models\POSSOURCETaxMaster;
use App\Repositories\POSSOURCETaxMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSOURCETaxMasterController
 * @package App\Http\Controllers\API
 */

class POSSOURCETaxMasterAPIController extends AppBaseController
{
    /** @var  POSSOURCETaxMasterRepository */
    private $pOSSOURCETaxMasterRepository;

    public function __construct(POSSOURCETaxMasterRepository $pOSSOURCETaxMasterRepo)
    {
        $this->pOSSOURCETaxMasterRepository = $pOSSOURCETaxMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSOURCETaxMasters",
     *      summary="Get a listing of the POSSOURCETaxMasters.",
     *      tags={"POSSOURCETaxMaster"},
     *      description="Get all POSSOURCETaxMasters",
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
     *                  @SWG\Items(ref="#/definitions/POSSOURCETaxMaster")
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
        $this->pOSSOURCETaxMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSOURCETaxMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSOURCETaxMasters = $this->pOSSOURCETaxMasterRepository->all();

        return $this->sendResponse($pOSSOURCETaxMasters->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_tax_masters_retrieved_successful'));
    }

    /**
     * @param CreatePOSSOURCETaxMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSOURCETaxMasters",
     *      summary="Store a newly created POSSOURCETaxMaster in storage",
     *      tags={"POSSOURCETaxMaster"},
     *      description="Store POSSOURCETaxMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSOURCETaxMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSOURCETaxMaster")
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
     *                  ref="#/definitions/POSSOURCETaxMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSOURCETaxMasterAPIRequest $request)
    {
        $input = $request->all();

        $pOSSOURCETaxMaster = $this->pOSSOURCETaxMasterRepository->create($input);

        return $this->sendResponse($pOSSOURCETaxMaster->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_tax_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSOURCETaxMasters/{id}",
     *      summary="Display the specified POSSOURCETaxMaster",
     *      tags={"POSSOURCETaxMaster"},
     *      description="Get POSSOURCETaxMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCETaxMaster",
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
     *                  ref="#/definitions/POSSOURCETaxMaster"
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
        /** @var POSSOURCETaxMaster $pOSSOURCETaxMaster */
        $pOSSOURCETaxMaster = $this->pOSSOURCETaxMasterRepository->findWithoutFail($id);

        if (empty($pOSSOURCETaxMaster)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_tax_master_not_found'));
        }

        return $this->sendResponse($pOSSOURCETaxMaster->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_tax_master_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSOURCETaxMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSOURCETaxMasters/{id}",
     *      summary="Update the specified POSSOURCETaxMaster in storage",
     *      tags={"POSSOURCETaxMaster"},
     *      description="Update POSSOURCETaxMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCETaxMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSOURCETaxMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSOURCETaxMaster")
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
     *                  ref="#/definitions/POSSOURCETaxMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSOURCETaxMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSOURCETaxMaster $pOSSOURCETaxMaster */
        $pOSSOURCETaxMaster = $this->pOSSOURCETaxMasterRepository->findWithoutFail($id);

        if (empty($pOSSOURCETaxMaster)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_tax_master_not_found'));
        }

        $pOSSOURCETaxMaster = $this->pOSSOURCETaxMasterRepository->update($input, $id);

        return $this->sendResponse($pOSSOURCETaxMaster->toArray(), trans('custom.possourcetaxmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSOURCETaxMasters/{id}",
     *      summary="Remove the specified POSSOURCETaxMaster from storage",
     *      tags={"POSSOURCETaxMaster"},
     *      description="Delete POSSOURCETaxMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCETaxMaster",
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
        /** @var POSSOURCETaxMaster $pOSSOURCETaxMaster */
        $pOSSOURCETaxMaster = $this->pOSSOURCETaxMasterRepository->findWithoutFail($id);

        if (empty($pOSSOURCETaxMaster)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_tax_master_not_found'));
        }

        $pOSSOURCETaxMaster->delete();

        return $this->sendSuccess('P O S S O U R C E Tax Master deleted successfully');
    }
}
