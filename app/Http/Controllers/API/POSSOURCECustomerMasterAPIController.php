<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSOURCECustomerMasterAPIRequest;
use App\Http\Requests\API\UpdatePOSSOURCECustomerMasterAPIRequest;
use App\Models\POSSOURCECustomerMaster;
use App\Repositories\POSSOURCECustomerMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSOURCECustomerMasterController
 * @package App\Http\Controllers\API
 */

class POSSOURCECustomerMasterAPIController extends AppBaseController
{
    /** @var  POSSOURCECustomerMasterRepository */
    private $pOSSOURCECustomerMasterRepository;

    public function __construct(POSSOURCECustomerMasterRepository $pOSSOURCECustomerMasterRepo)
    {
        $this->pOSSOURCECustomerMasterRepository = $pOSSOURCECustomerMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSOURCECustomerMasters",
     *      summary="Get a listing of the POSSOURCECustomerMasters.",
     *      tags={"POSSOURCECustomerMaster"},
     *      description="Get all POSSOURCECustomerMasters",
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
     *                  @SWG\Items(ref="#/definitions/POSSOURCECustomerMaster")
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
        $this->pOSSOURCECustomerMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSOURCECustomerMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSOURCECustomerMasters = $this->pOSSOURCECustomerMasterRepository->all();

        return $this->sendResponse($pOSSOURCECustomerMasters->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_customer_masters_retrieved_succe'));
    }

    /**
     * @param CreatePOSSOURCECustomerMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSOURCECustomerMasters",
     *      summary="Store a newly created POSSOURCECustomerMaster in storage",
     *      tags={"POSSOURCECustomerMaster"},
     *      description="Store POSSOURCECustomerMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSOURCECustomerMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSOURCECustomerMaster")
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
     *                  ref="#/definitions/POSSOURCECustomerMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSOURCECustomerMasterAPIRequest $request)
    {
        $input = $request->all();

        $pOSSOURCECustomerMaster = $this->pOSSOURCECustomerMasterRepository->create($input);

        return $this->sendResponse($pOSSOURCECustomerMaster->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_customer_master_saved_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSOURCECustomerMasters/{id}",
     *      summary="Display the specified POSSOURCECustomerMaster",
     *      tags={"POSSOURCECustomerMaster"},
     *      description="Get POSSOURCECustomerMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCECustomerMaster",
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
     *                  ref="#/definitions/POSSOURCECustomerMaster"
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
        /** @var POSSOURCECustomerMaster $pOSSOURCECustomerMaster */
        $pOSSOURCECustomerMaster = $this->pOSSOURCECustomerMasterRepository->findWithoutFail($id);

        if (empty($pOSSOURCECustomerMaster)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_customer_master_not_found'));
        }

        return $this->sendResponse($pOSSOURCECustomerMaster->toArray(), trans('custom.p_o_s_s_o_u_r_c_e_customer_master_retrieved_succes'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSOURCECustomerMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSOURCECustomerMasters/{id}",
     *      summary="Update the specified POSSOURCECustomerMaster in storage",
     *      tags={"POSSOURCECustomerMaster"},
     *      description="Update POSSOURCECustomerMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCECustomerMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSOURCECustomerMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSOURCECustomerMaster")
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
     *                  ref="#/definitions/POSSOURCECustomerMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSOURCECustomerMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSOURCECustomerMaster $pOSSOURCECustomerMaster */
        $pOSSOURCECustomerMaster = $this->pOSSOURCECustomerMasterRepository->findWithoutFail($id);

        if (empty($pOSSOURCECustomerMaster)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_customer_master_not_found'));
        }

        $pOSSOURCECustomerMaster = $this->pOSSOURCECustomerMasterRepository->update($input, $id);

        return $this->sendResponse($pOSSOURCECustomerMaster->toArray(), trans('custom.possourcecustomermaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSOURCECustomerMasters/{id}",
     *      summary="Remove the specified POSSOURCECustomerMaster from storage",
     *      tags={"POSSOURCECustomerMaster"},
     *      description="Delete POSSOURCECustomerMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCECustomerMaster",
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
        /** @var POSSOURCECustomerMaster $pOSSOURCECustomerMaster */
        $pOSSOURCECustomerMaster = $this->pOSSOURCECustomerMasterRepository->findWithoutFail($id);

        if (empty($pOSSOURCECustomerMaster)) {
            return $this->sendError(trans('custom.p_o_s_s_o_u_r_c_e_customer_master_not_found'));
        }

        $pOSSOURCECustomerMaster->delete();

        return $this->sendSuccess('P O S S O U R C E Customer Master deleted successfully');
    }
}
