<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStagCustomerTypeMasterAPIRequest;
use App\Http\Requests\API\UpdateStagCustomerTypeMasterAPIRequest;
use App\Models\StagCustomerTypeMaster;
use App\Repositories\StagCustomerTypeMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StagCustomerTypeMasterController
 * @package App\Http\Controllers\API
 */

class StagCustomerTypeMasterAPIController extends AppBaseController
{
    /** @var  StagCustomerTypeMasterRepository */
    private $stagCustomerTypeMasterRepository;

    public function __construct(StagCustomerTypeMasterRepository $stagCustomerTypeMasterRepo)
    {
        $this->stagCustomerTypeMasterRepository = $stagCustomerTypeMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stagCustomerTypeMasters",
     *      summary="Get a listing of the StagCustomerTypeMasters.",
     *      tags={"StagCustomerTypeMaster"},
     *      description="Get all StagCustomerTypeMasters",
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
     *                  @SWG\Items(ref="#/definitions/StagCustomerTypeMaster")
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
        $this->stagCustomerTypeMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->stagCustomerTypeMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stagCustomerTypeMasters = $this->stagCustomerTypeMasterRepository->all();

        return $this->sendResponse($stagCustomerTypeMasters->toArray(), trans('custom.stag_customer_type_masters_retrieved_successfully'));
    }

    /**
     * @param CreateStagCustomerTypeMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stagCustomerTypeMasters",
     *      summary="Store a newly created StagCustomerTypeMaster in storage",
     *      tags={"StagCustomerTypeMaster"},
     *      description="Store StagCustomerTypeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StagCustomerTypeMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StagCustomerTypeMaster")
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
     *                  ref="#/definitions/StagCustomerTypeMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStagCustomerTypeMasterAPIRequest $request)
    {
        $input = $request->all();

        $stagCustomerTypeMaster = $this->stagCustomerTypeMasterRepository->create($input);

        return $this->sendResponse($stagCustomerTypeMaster->toArray(), trans('custom.stag_customer_type_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stagCustomerTypeMasters/{id}",
     *      summary="Display the specified StagCustomerTypeMaster",
     *      tags={"StagCustomerTypeMaster"},
     *      description="Get StagCustomerTypeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StagCustomerTypeMaster",
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
     *                  ref="#/definitions/StagCustomerTypeMaster"
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
        /** @var StagCustomerTypeMaster $stagCustomerTypeMaster */
        $stagCustomerTypeMaster = $this->stagCustomerTypeMasterRepository->findWithoutFail($id);

        if (empty($stagCustomerTypeMaster)) {
            return $this->sendError(trans('custom.stag_customer_type_master_not_found'));
        }

        return $this->sendResponse($stagCustomerTypeMaster->toArray(), trans('custom.stag_customer_type_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateStagCustomerTypeMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stagCustomerTypeMasters/{id}",
     *      summary="Update the specified StagCustomerTypeMaster in storage",
     *      tags={"StagCustomerTypeMaster"},
     *      description="Update StagCustomerTypeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StagCustomerTypeMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StagCustomerTypeMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StagCustomerTypeMaster")
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
     *                  ref="#/definitions/StagCustomerTypeMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStagCustomerTypeMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var StagCustomerTypeMaster $stagCustomerTypeMaster */
        $stagCustomerTypeMaster = $this->stagCustomerTypeMasterRepository->findWithoutFail($id);

        if (empty($stagCustomerTypeMaster)) {
            return $this->sendError(trans('custom.stag_customer_type_master_not_found'));
        }

        $stagCustomerTypeMaster = $this->stagCustomerTypeMasterRepository->update($input, $id);

        return $this->sendResponse($stagCustomerTypeMaster->toArray(), trans('custom.stagcustomertypemaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stagCustomerTypeMasters/{id}",
     *      summary="Remove the specified StagCustomerTypeMaster from storage",
     *      tags={"StagCustomerTypeMaster"},
     *      description="Delete StagCustomerTypeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StagCustomerTypeMaster",
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
        /** @var StagCustomerTypeMaster $stagCustomerTypeMaster */
        $stagCustomerTypeMaster = $this->stagCustomerTypeMasterRepository->findWithoutFail($id);

        if (empty($stagCustomerTypeMaster)) {
            return $this->sendError(trans('custom.stag_customer_type_master_not_found'));
        }

        $stagCustomerTypeMaster->delete();

        return $this->sendSuccess('Stag Customer Type Master deleted successfully');
    }
}
