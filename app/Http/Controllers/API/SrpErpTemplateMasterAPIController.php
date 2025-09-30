<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSrpErpTemplateMasterAPIRequest;
use App\Http\Requests\API\UpdateSrpErpTemplateMasterAPIRequest;
use App\Models\SrpErpTemplateMaster;
use App\Repositories\SrpErpTemplateMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SrpErpTemplateMasterController
 * @package App\Http\Controllers\API
 */

class SrpErpTemplateMasterAPIController extends AppBaseController
{
    /** @var  SrpErpTemplateMasterRepository */
    private $srpErpTemplateMasterRepository;

    public function __construct(SrpErpTemplateMasterRepository $srpErpTemplateMasterRepo)
    {
        $this->srpErpTemplateMasterRepository = $srpErpTemplateMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/srpErpTemplateMasters",
     *      summary="Get a listing of the SrpErpTemplateMasters.",
     *      tags={"SrpErpTemplateMaster"},
     *      description="Get all SrpErpTemplateMasters",
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
     *                  @SWG\Items(ref="#/definitions/SrpErpTemplateMaster")
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
        $this->srpErpTemplateMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->srpErpTemplateMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $srpErpTemplateMasters = $this->srpErpTemplateMasterRepository->all();

        return $this->sendResponse($srpErpTemplateMasters->toArray(), trans('custom.srp_erp_template_masters_retrieved_successfully'));
    }

    /**
     * @param CreateSrpErpTemplateMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/srpErpTemplateMasters",
     *      summary="Store a newly created SrpErpTemplateMaster in storage",
     *      tags={"SrpErpTemplateMaster"},
     *      description="Store SrpErpTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SrpErpTemplateMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SrpErpTemplateMaster")
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
     *                  ref="#/definitions/SrpErpTemplateMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSrpErpTemplateMasterAPIRequest $request)
    {
        $input = $request->all();

        $srpErpTemplateMaster = $this->srpErpTemplateMasterRepository->create($input);

        return $this->sendResponse($srpErpTemplateMaster->toArray(), trans('custom.srp_erp_template_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/srpErpTemplateMasters/{id}",
     *      summary="Display the specified SrpErpTemplateMaster",
     *      tags={"SrpErpTemplateMaster"},
     *      description="Get SrpErpTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpTemplateMaster",
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
     *                  ref="#/definitions/SrpErpTemplateMaster"
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
        /** @var SrpErpTemplateMaster $srpErpTemplateMaster */
        $srpErpTemplateMaster = $this->srpErpTemplateMasterRepository->findWithoutFail($id);

        if (empty($srpErpTemplateMaster)) {
            return $this->sendError(trans('custom.srp_erp_template_master_not_found'));
        }

        return $this->sendResponse($srpErpTemplateMaster->toArray(), trans('custom.srp_erp_template_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSrpErpTemplateMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/srpErpTemplateMasters/{id}",
     *      summary="Update the specified SrpErpTemplateMaster in storage",
     *      tags={"SrpErpTemplateMaster"},
     *      description="Update SrpErpTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpTemplateMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SrpErpTemplateMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SrpErpTemplateMaster")
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
     *                  ref="#/definitions/SrpErpTemplateMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSrpErpTemplateMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SrpErpTemplateMaster $srpErpTemplateMaster */
        $srpErpTemplateMaster = $this->srpErpTemplateMasterRepository->findWithoutFail($id);

        if (empty($srpErpTemplateMaster)) {
            return $this->sendError(trans('custom.srp_erp_template_master_not_found'));
        }

        $srpErpTemplateMaster = $this->srpErpTemplateMasterRepository->update($input, $id);

        return $this->sendResponse($srpErpTemplateMaster->toArray(), trans('custom.srperptemplatemaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/srpErpTemplateMasters/{id}",
     *      summary="Remove the specified SrpErpTemplateMaster from storage",
     *      tags={"SrpErpTemplateMaster"},
     *      description="Delete SrpErpTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpTemplateMaster",
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
        /** @var SrpErpTemplateMaster $srpErpTemplateMaster */
        $srpErpTemplateMaster = $this->srpErpTemplateMasterRepository->findWithoutFail($id);

        if (empty($srpErpTemplateMaster)) {
            return $this->sendError(trans('custom.srp_erp_template_master_not_found'));
        }

        $srpErpTemplateMaster->delete();

        return $this->sendSuccess('Srp Erp Template Master deleted successfully');
    }
}
