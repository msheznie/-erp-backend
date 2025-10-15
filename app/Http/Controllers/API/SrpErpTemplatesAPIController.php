<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSrpErpTemplatesAPIRequest;
use App\Http\Requests\API\UpdateSrpErpTemplatesAPIRequest;
use App\Models\SrpErpTemplates;
use App\Repositories\SrpErpTemplatesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SrpErpTemplatesController
 * @package App\Http\Controllers\API
 */

class SrpErpTemplatesAPIController extends AppBaseController
{
    /** @var  SrpErpTemplatesRepository */
    private $srpErpTemplatesRepository;

    public function __construct(SrpErpTemplatesRepository $srpErpTemplatesRepo)
    {
        $this->srpErpTemplatesRepository = $srpErpTemplatesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/srpErpTemplates",
     *      summary="Get a listing of the SrpErpTemplates.",
     *      tags={"SrpErpTemplates"},
     *      description="Get all SrpErpTemplates",
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
     *                  @SWG\Items(ref="#/definitions/SrpErpTemplates")
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
        $this->srpErpTemplatesRepository->pushCriteria(new RequestCriteria($request));
        $this->srpErpTemplatesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $srpErpTemplates = $this->srpErpTemplatesRepository->all();

        return $this->sendResponse($srpErpTemplates->toArray(), trans('custom.srp_erp_templates_retrieved_successfully'));
    }

    /**
     * @param CreateSrpErpTemplatesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/srpErpTemplates",
     *      summary="Store a newly created SrpErpTemplates in storage",
     *      tags={"SrpErpTemplates"},
     *      description="Store SrpErpTemplates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SrpErpTemplates that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SrpErpTemplates")
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
     *                  ref="#/definitions/SrpErpTemplates"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSrpErpTemplatesAPIRequest $request)
    {
        $input = $request->all();

        $srpErpTemplates = $this->srpErpTemplatesRepository->create($input);

        return $this->sendResponse($srpErpTemplates->toArray(), trans('custom.srp_erp_templates_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/srpErpTemplates/{id}",
     *      summary="Display the specified SrpErpTemplates",
     *      tags={"SrpErpTemplates"},
     *      description="Get SrpErpTemplates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpTemplates",
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
     *                  ref="#/definitions/SrpErpTemplates"
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
        /** @var SrpErpTemplates $srpErpTemplates */
        $srpErpTemplates = $this->srpErpTemplatesRepository->findWithoutFail($id);

        if (empty($srpErpTemplates)) {
            return $this->sendError(trans('custom.srp_erp_templates_not_found'));
        }

        return $this->sendResponse($srpErpTemplates->toArray(), trans('custom.srp_erp_templates_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSrpErpTemplatesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/srpErpTemplates/{id}",
     *      summary="Update the specified SrpErpTemplates in storage",
     *      tags={"SrpErpTemplates"},
     *      description="Update SrpErpTemplates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpTemplates",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SrpErpTemplates that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SrpErpTemplates")
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
     *                  ref="#/definitions/SrpErpTemplates"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSrpErpTemplatesAPIRequest $request)
    {
        $input = $request->all();

        /** @var SrpErpTemplates $srpErpTemplates */
        $srpErpTemplates = $this->srpErpTemplatesRepository->findWithoutFail($id);

        if (empty($srpErpTemplates)) {
            return $this->sendError(trans('custom.srp_erp_templates_not_found'));
        }

        $srpErpTemplates = $this->srpErpTemplatesRepository->update($input, $id);

        return $this->sendResponse($srpErpTemplates->toArray(), trans('custom.srperptemplates_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/srpErpTemplates/{id}",
     *      summary="Remove the specified SrpErpTemplates from storage",
     *      tags={"SrpErpTemplates"},
     *      description="Delete SrpErpTemplates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpTemplates",
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
        /** @var SrpErpTemplates $srpErpTemplates */
        $srpErpTemplates = $this->srpErpTemplatesRepository->findWithoutFail($id);

        if (empty($srpErpTemplates)) {
            return $this->sendError(trans('custom.srp_erp_templates_not_found'));
        }

        $srpErpTemplates->delete();

        return $this->sendSuccess('Srp Erp Templates deleted successfully');
    }
}
