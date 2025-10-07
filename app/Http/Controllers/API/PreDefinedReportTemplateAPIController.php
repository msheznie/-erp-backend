<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePreDefinedReportTemplateAPIRequest;
use App\Http\Requests\API\UpdatePreDefinedReportTemplateAPIRequest;
use App\Models\ClientPerformaAppType;
use App\Models\Company;
use App\Models\Contract;
use App\Models\CustomerAssigned;
use App\Models\PreDefinedReportTemplate;
use App\Models\SegmentMaster;
use App\Repositories\PreDefinedReportTemplateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PreDefinedReportTemplateController
 * @package App\Http\Controllers\API
 */

class PreDefinedReportTemplateAPIController extends AppBaseController
{
    /** @var  PreDefinedReportTemplateRepository */
    private $preDefinedReportTemplateRepository;

    public function __construct(PreDefinedReportTemplateRepository $preDefinedReportTemplateRepo)
    {
        $this->preDefinedReportTemplateRepository = $preDefinedReportTemplateRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/preDefinedReportTemplates",
     *      summary="Get a listing of the PreDefinedReportTemplates.",
     *      tags={"PreDefinedReportTemplate"},
     *      description="Get all PreDefinedReportTemplates",
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
     *                  @SWG\Items(ref="#/definitions/PreDefinedReportTemplate")
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
        $this->preDefinedReportTemplateRepository->pushCriteria(new RequestCriteria($request));
        $this->preDefinedReportTemplateRepository->pushCriteria(new LimitOffsetCriteria($request));
        $preDefinedReportTemplates = $this->preDefinedReportTemplateRepository->all();

        return $this->sendResponse($preDefinedReportTemplates->toArray(), trans('custom.pre_defined_report_templates_retrieved_successfull'));
    }

    /**
     * @param CreatePreDefinedReportTemplateAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/preDefinedReportTemplates",
     *      summary="Store a newly created PreDefinedReportTemplate in storage",
     *      tags={"PreDefinedReportTemplate"},
     *      description="Store PreDefinedReportTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PreDefinedReportTemplate that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PreDefinedReportTemplate")
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
     *                  ref="#/definitions/PreDefinedReportTemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePreDefinedReportTemplateAPIRequest $request)
    {
        $input = $request->all();

        $preDefinedReportTemplate = $this->preDefinedReportTemplateRepository->create($input);

        return $this->sendResponse($preDefinedReportTemplate->toArray(), trans('custom.pre_defined_report_template_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/preDefinedReportTemplates/{id}",
     *      summary="Display the specified PreDefinedReportTemplate",
     *      tags={"PreDefinedReportTemplate"},
     *      description="Get PreDefinedReportTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PreDefinedReportTemplate",
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
     *                  ref="#/definitions/PreDefinedReportTemplate"
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
        /** @var PreDefinedReportTemplate $preDefinedReportTemplate */
        $preDefinedReportTemplate = $this->preDefinedReportTemplateRepository->findWithoutFail($id);

        if (empty($preDefinedReportTemplate)) {
            return $this->sendError(trans('custom.pre_defined_report_template_not_found'));
        }

        return $this->sendResponse($preDefinedReportTemplate->toArray(), trans('custom.pre_defined_report_template_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePreDefinedReportTemplateAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/preDefinedReportTemplates/{id}",
     *      summary="Update the specified PreDefinedReportTemplate in storage",
     *      tags={"PreDefinedReportTemplate"},
     *      description="Update PreDefinedReportTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PreDefinedReportTemplate",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PreDefinedReportTemplate that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PreDefinedReportTemplate")
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
     *                  ref="#/definitions/PreDefinedReportTemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePreDefinedReportTemplateAPIRequest $request)
    {
        $input = $request->all();

        /** @var PreDefinedReportTemplate $preDefinedReportTemplate */
        $preDefinedReportTemplate = $this->preDefinedReportTemplateRepository->findWithoutFail($id);

        if (empty($preDefinedReportTemplate)) {
            return $this->sendError(trans('custom.pre_defined_report_template_not_found'));
        }

        $preDefinedReportTemplate = $this->preDefinedReportTemplateRepository->update($input, $id);

        return $this->sendResponse($preDefinedReportTemplate->toArray(), trans('custom.predefinedreporttemplate_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/preDefinedReportTemplates/{id}",
     *      summary="Remove the specified PreDefinedReportTemplate from storage",
     *      tags={"PreDefinedReportTemplate"},
     *      description="Delete PreDefinedReportTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PreDefinedReportTemplate",
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
        /** @var PreDefinedReportTemplate $preDefinedReportTemplate */
        $preDefinedReportTemplate = $this->preDefinedReportTemplateRepository->findWithoutFail($id);

        if (empty($preDefinedReportTemplate)) {
            return $this->sendError(trans('custom.pre_defined_report_template_not_found'));
        }

        $preDefinedReportTemplate->delete();

        return $this->sendResponse($id, trans('custom.pre_defined_report_template_deleted_successfully'));
    }



}
