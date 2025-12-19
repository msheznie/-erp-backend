<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSTAGCustomerMasterAPIRequest;
use App\Http\Requests\API\UpdatePOSSTAGCustomerMasterAPIRequest;
use App\Models\POSSTAGCustomerMaster;
use App\Repositories\POSSTAGCustomerMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSTAGCustomerMasterController
 * @package App\Http\Controllers\API
 */

class POSSTAGCustomerMasterAPIController extends AppBaseController
{
    /** @var  POSSTAGCustomerMasterRepository */
    private $pOSSTAGCustomerMasterRepository;

    public function __construct(POSSTAGCustomerMasterRepository $pOSSTAGCustomerMasterRepo)
    {
        $this->pOSSTAGCustomerMasterRepository = $pOSSTAGCustomerMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGCustomerMasters",
     *      summary="Get a listing of the POSSTAGCustomerMasters.",
     *      tags={"POSSTAGCustomerMaster"},
     *      description="Get all POSSTAGCustomerMasters",
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
     *                  @SWG\Items(ref="#/definitions/POSSTAGCustomerMaster")
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
        $this->pOSSTAGCustomerMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSTAGCustomerMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSTAGCustomerMasters = $this->pOSSTAGCustomerMasterRepository->all();

        return $this->sendResponse($pOSSTAGCustomerMasters->toArray(), trans('custom.p_o_s_s_t_a_g_customer_masters_retrieved_successfu'));
    }

    /**
     * @param CreatePOSSTAGCustomerMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSTAGCustomerMasters",
     *      summary="Store a newly created POSSTAGCustomerMaster in storage",
     *      tags={"POSSTAGCustomerMaster"},
     *      description="Store POSSTAGCustomerMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGCustomerMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGCustomerMaster")
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
     *                  ref="#/definitions/POSSTAGCustomerMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSTAGCustomerMasterAPIRequest $request)
    {
        $input = $request->all();

        $pOSSTAGCustomerMaster = $this->pOSSTAGCustomerMasterRepository->create($input);

        return $this->sendResponse($pOSSTAGCustomerMaster->toArray(), trans('custom.p_o_s_s_t_a_g_customer_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSTAGCustomerMasters/{id}",
     *      summary="Display the specified POSSTAGCustomerMaster",
     *      tags={"POSSTAGCustomerMaster"},
     *      description="Get POSSTAGCustomerMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGCustomerMaster",
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
     *                  ref="#/definitions/POSSTAGCustomerMaster"
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
        /** @var POSSTAGCustomerMaster $pOSSTAGCustomerMaster */
        $pOSSTAGCustomerMaster = $this->pOSSTAGCustomerMasterRepository->findWithoutFail($id);

        if (empty($pOSSTAGCustomerMaster)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_customer_master_not_found'));
        }

        return $this->sendResponse($pOSSTAGCustomerMaster->toArray(), trans('custom.p_o_s_s_t_a_g_customer_master_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSTAGCustomerMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSTAGCustomerMasters/{id}",
     *      summary="Update the specified POSSTAGCustomerMaster in storage",
     *      tags={"POSSTAGCustomerMaster"},
     *      description="Update POSSTAGCustomerMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGCustomerMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSTAGCustomerMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSTAGCustomerMaster")
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
     *                  ref="#/definitions/POSSTAGCustomerMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSTAGCustomerMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSTAGCustomerMaster $pOSSTAGCustomerMaster */
        $pOSSTAGCustomerMaster = $this->pOSSTAGCustomerMasterRepository->findWithoutFail($id);

        if (empty($pOSSTAGCustomerMaster)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_customer_master_not_found'));
        }

        $pOSSTAGCustomerMaster = $this->pOSSTAGCustomerMasterRepository->update($input, $id);

        return $this->sendResponse($pOSSTAGCustomerMaster->toArray(), trans('custom.posstagcustomermaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSTAGCustomerMasters/{id}",
     *      summary="Remove the specified POSSTAGCustomerMaster from storage",
     *      tags={"POSSTAGCustomerMaster"},
     *      description="Delete POSSTAGCustomerMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSTAGCustomerMaster",
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
        /** @var POSSTAGCustomerMaster $pOSSTAGCustomerMaster */
        $pOSSTAGCustomerMaster = $this->pOSSTAGCustomerMasterRepository->findWithoutFail($id);

        if (empty($pOSSTAGCustomerMaster)) {
            return $this->sendError(trans('custom.p_o_s_s_t_a_g_customer_master_not_found'));
        }

        $pOSSTAGCustomerMaster->delete();

        return $this->sendSuccess('P O S S T A G Customer Master deleted successfully');
    }
}
