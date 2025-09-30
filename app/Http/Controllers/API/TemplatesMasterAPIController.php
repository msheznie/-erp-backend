<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTemplatesMasterAPIRequest;
use App\Http\Requests\API\UpdateTemplatesMasterAPIRequest;
use App\Models\TemplatesMaster;
use App\Repositories\TemplatesMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TemplatesMasterController
 * @package App\Http\Controllers\API
 */

class TemplatesMasterAPIController extends AppBaseController
{
    /** @var  TemplatesMasterRepository */
    private $templatesMasterRepository;

    public function __construct(TemplatesMasterRepository $templatesMasterRepo)
    {
        $this->templatesMasterRepository = $templatesMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/templatesMasters",
     *      summary="Get a listing of the TemplatesMasters.",
     *      tags={"TemplatesMaster"},
     *      description="Get all TemplatesMasters",
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
     *                  @SWG\Items(ref="#/definitions/TemplatesMaster")
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
        $this->templatesMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->templatesMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $templatesMasters = $this->templatesMasterRepository->all();

        return $this->sendResponse($templatesMasters->toArray(), trans('custom.templates_masters_retrieved_successfully'));
    }

    /**
     * @param CreateTemplatesMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/templatesMasters",
     *      summary="Store a newly created TemplatesMaster in storage",
     *      tags={"TemplatesMaster"},
     *      description="Store TemplatesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TemplatesMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TemplatesMaster")
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
     *                  ref="#/definitions/TemplatesMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTemplatesMasterAPIRequest $request)
    {
        $input = $request->all();

        $templatesMasters = $this->templatesMasterRepository->create($input);

        return $this->sendResponse($templatesMasters->toArray(), trans('custom.templates_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/templatesMasters/{id}",
     *      summary="Display the specified TemplatesMaster",
     *      tags={"TemplatesMaster"},
     *      description="Get TemplatesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TemplatesMaster",
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
     *                  ref="#/definitions/TemplatesMaster"
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
        /** @var TemplatesMaster $templatesMaster */
        $templatesMaster = $this->templatesMasterRepository->findWithoutFail($id);

        if (empty($templatesMaster)) {
            return $this->sendError(trans('custom.templates_master_not_found'));
        }

        return $this->sendResponse($templatesMaster->toArray(), trans('custom.templates_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTemplatesMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/templatesMasters/{id}",
     *      summary="Update the specified TemplatesMaster in storage",
     *      tags={"TemplatesMaster"},
     *      description="Update TemplatesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TemplatesMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TemplatesMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TemplatesMaster")
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
     *                  ref="#/definitions/TemplatesMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTemplatesMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var TemplatesMaster $templatesMaster */
        $templatesMaster = $this->templatesMasterRepository->findWithoutFail($id);

        if (empty($templatesMaster)) {
            return $this->sendError(trans('custom.templates_master_not_found'));
        }

        $templatesMaster = $this->templatesMasterRepository->update($input, $id);

        return $this->sendResponse($templatesMaster->toArray(), trans('custom.templatesmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/templatesMasters/{id}",
     *      summary="Remove the specified TemplatesMaster from storage",
     *      tags={"TemplatesMaster"},
     *      description="Delete TemplatesMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TemplatesMaster",
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
        /** @var TemplatesMaster $templatesMaster */
        $templatesMaster = $this->templatesMasterRepository->findWithoutFail($id);

        if (empty($templatesMaster)) {
            return $this->sendError(trans('custom.templates_master_not_found'));
        }

        $templatesMaster->delete();

        return $this->sendResponse($id, trans('custom.templates_master_deleted_successfully'));
    }
}
