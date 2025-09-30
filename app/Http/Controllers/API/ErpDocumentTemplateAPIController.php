<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateErpDocumentTemplateAPIRequest;
use App\Http\Requests\API\UpdateErpDocumentTemplateAPIRequest;
use App\Models\ErpDocumentTemplate;
use App\Repositories\ErpDocumentTemplateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ErpDocumentTemplateController
 * @package App\Http\Controllers\API
 */

class ErpDocumentTemplateAPIController extends AppBaseController
{
    /** @var  ErpDocumentTemplateRepository */
    private $erpDocumentTemplateRepository;

    public function __construct(ErpDocumentTemplateRepository $erpDocumentTemplateRepo)
    {
        $this->erpDocumentTemplateRepository = $erpDocumentTemplateRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpDocumentTemplates",
     *      summary="Get a listing of the ErpDocumentTemplates.",
     *      tags={"ErpDocumentTemplate"},
     *      description="Get all ErpDocumentTemplates",
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
     *                  @SWG\Items(ref="#/definitions/ErpDocumentTemplate")
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
        $this->erpDocumentTemplateRepository->pushCriteria(new RequestCriteria($request));
        $this->erpDocumentTemplateRepository->pushCriteria(new LimitOffsetCriteria($request));
        $erpDocumentTemplates = $this->erpDocumentTemplateRepository->all();

        return $this->sendResponse($erpDocumentTemplates->toArray(), trans('custom.erp_document_templates_retrieved_successfully'));
    }

    /**
     * @param CreateErpDocumentTemplateAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/erpDocumentTemplates",
     *      summary="Store a newly created ErpDocumentTemplate in storage",
     *      tags={"ErpDocumentTemplate"},
     *      description="Store ErpDocumentTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpDocumentTemplate that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpDocumentTemplate")
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
     *                  ref="#/definitions/ErpDocumentTemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateErpDocumentTemplateAPIRequest $request)
    {
        $input = $request->all();

        $erpDocumentTemplate = $this->erpDocumentTemplateRepository->create($input);

        return $this->sendResponse($erpDocumentTemplate->toArray(), trans('custom.erp_document_template_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpDocumentTemplates/{id}",
     *      summary="Display the specified ErpDocumentTemplate",
     *      tags={"ErpDocumentTemplate"},
     *      description="Get ErpDocumentTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpDocumentTemplate",
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
     *                  ref="#/definitions/ErpDocumentTemplate"
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
        /** @var ErpDocumentTemplate $erpDocumentTemplate */
        $erpDocumentTemplate = $this->erpDocumentTemplateRepository->findWithoutFail($id);

        if (empty($erpDocumentTemplate)) {
            return $this->sendError(trans('custom.erp_document_template_not_found'));
        }

        return $this->sendResponse($erpDocumentTemplate->toArray(), trans('custom.erp_document_template_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateErpDocumentTemplateAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/erpDocumentTemplates/{id}",
     *      summary="Update the specified ErpDocumentTemplate in storage",
     *      tags={"ErpDocumentTemplate"},
     *      description="Update ErpDocumentTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpDocumentTemplate",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpDocumentTemplate that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpDocumentTemplate")
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
     *                  ref="#/definitions/ErpDocumentTemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateErpDocumentTemplateAPIRequest $request)
    {
        $input = $request->all();

        /** @var ErpDocumentTemplate $erpDocumentTemplate */
        $erpDocumentTemplate = $this->erpDocumentTemplateRepository->findWithoutFail($id);

        if (empty($erpDocumentTemplate)) {
            return $this->sendError(trans('custom.erp_document_template_not_found'));
        }

        $erpDocumentTemplate = $this->erpDocumentTemplateRepository->update($input, $id);

        return $this->sendResponse($erpDocumentTemplate->toArray(), trans('custom.erpdocumenttemplate_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/erpDocumentTemplates/{id}",
     *      summary="Remove the specified ErpDocumentTemplate from storage",
     *      tags={"ErpDocumentTemplate"},
     *      description="Delete ErpDocumentTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpDocumentTemplate",
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
        /** @var ErpDocumentTemplate $erpDocumentTemplate */
        $erpDocumentTemplate = $this->erpDocumentTemplateRepository->findWithoutFail($id);

        if (empty($erpDocumentTemplate)) {
            return $this->sendError(trans('custom.erp_document_template_not_found'));
        }

        $erpDocumentTemplate->delete();

        return $this->sendResponse($id, trans('custom.erp_document_template_deleted_successfully'));
    }
}
