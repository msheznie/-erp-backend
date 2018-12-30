<?php
/**
 * =============================================
 * -- File Name : ReportTemplateColumnLinkAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report Template
 * -- Author : Mubashir
 * -- Create date : 27 - December 2018
 * -- Description :  This file contains the all CRUD for Report template detail
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportTemplateColumnLinkAPIRequest;
use App\Http\Requests\API\UpdateReportTemplateColumnLinkAPIRequest;
use App\Models\Company;
use App\Models\ReportTemplateColumnLink;
use App\Repositories\ReportTemplateColumnLinkRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReportTemplateColumnLinkController
 * @package App\Http\Controllers\API
 */
class ReportTemplateColumnLinkAPIController extends AppBaseController
{
    /** @var  ReportTemplateColumnLinkRepository */
    private $reportTemplateColumnLinkRepository;

    public function __construct(ReportTemplateColumnLinkRepository $reportTemplateColumnLinkRepo)
    {
        $this->reportTemplateColumnLinkRepository = $reportTemplateColumnLinkRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateColumnLinks",
     *      summary="Get a listing of the ReportTemplateColumnLinks.",
     *      tags={"ReportTemplateColumnLink"},
     *      description="Get all ReportTemplateColumnLinks",
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
     *                  @SWG\Items(ref="#/definitions/ReportTemplateColumnLink")
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
        $this->reportTemplateColumnLinkRepository->pushCriteria(new RequestCriteria($request));
        $this->reportTemplateColumnLinkRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportTemplateColumnLinks = $this->reportTemplateColumnLinkRepository->all();

        return $this->sendResponse($reportTemplateColumnLinks->toArray(), 'Report Template Column Links retrieved successfully');
    }

    /**
     * @param CreateReportTemplateColumnLinkAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reportTemplateColumnLinks",
     *      summary="Store a newly created ReportTemplateColumnLink in storage",
     *      tags={"ReportTemplateColumnLink"},
     *      description="Store ReportTemplateColumnLink",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateColumnLink that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateColumnLink")
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
     *                  ref="#/definitions/ReportTemplateColumnLink"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportTemplateColumnLinkAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($request->all(), [
            'columnID' => 'required'
        ]);

        if ($validator->fails()) {//echo 'in';exit;
            return $this->sendError($validator->messages(), 422);
        }

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        if ($input['columnID']) {
            foreach ($input['columnID'] as $key => $val) {
                $data['columnID'] = $val['columnID'];
                $data['templateID'] = $input['templateMasterID'];
                $data['sortOrder'] = $key + 1;
                $data['description'] = $val['description'];
                $data['shortCode'] = $val['shortCode'];
                $data['type'] = $val['type'];
                $data['companySystemID'] = $input['companySystemID'];
                $data['companyID'] = $input['companyID'];
                $data['createdPCID'] = gethostname();
                $data['createdUserID'] = \Helper::getEmployeeID();
                $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                $reportTemplateColumnLinks = $this->reportTemplateColumnLinkRepository->create($data);
            }
        }

        return $this->sendResponse([], 'Report Template Column Link saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateColumnLinks/{id}",
     *      summary="Display the specified ReportTemplateColumnLink",
     *      tags={"ReportTemplateColumnLink"},
     *      description="Get ReportTemplateColumnLink",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateColumnLink",
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
     *                  ref="#/definitions/ReportTemplateColumnLink"
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
        /** @var ReportTemplateColumnLink $reportTemplateColumnLink */
        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->findWithoutFail($id);

        if (empty($reportTemplateColumnLink)) {
            return $this->sendError('Report Template Column Link not found');
        }

        return $this->sendResponse($reportTemplateColumnLink->toArray(), 'Report Template Column Link retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateReportTemplateColumnLinkAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reportTemplateColumnLinks/{id}",
     *      summary="Update the specified ReportTemplateColumnLink in storage",
     *      tags={"ReportTemplateColumnLink"},
     *      description="Update ReportTemplateColumnLink",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateColumnLink",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateColumnLink that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateColumnLink")
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
     *                  ref="#/definitions/ReportTemplateColumnLink"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportTemplateColumnLinkAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReportTemplateColumnLink $reportTemplateColumnLink */
        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->findWithoutFail($id);

        if (empty($reportTemplateColumnLink)) {
            return $this->sendError('Report Template Column Link not found');
        }

        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->update($input, $id);

        return $this->sendResponse($reportTemplateColumnLink->toArray(), 'ReportTemplateColumnLink updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reportTemplateColumnLinks/{id}",
     *      summary="Remove the specified ReportTemplateColumnLink from storage",
     *      tags={"ReportTemplateColumnLink"},
     *      description="Delete ReportTemplateColumnLink",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateColumnLink",
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
        /** @var ReportTemplateColumnLink $reportTemplateColumnLink */
        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->findWithoutFail($id);

        if (empty($reportTemplateColumnLink)) {
            return $this->sendError('Report Template Column Link not found');
        }

        $reportTemplateColumnLink->delete();

        return $this->sendResponse($id, 'Report Template Column Link deleted successfully');
    }

    public function getTemplateColumnLinks(Request $request){
        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->orderBy('sortOrder','asc')->findWhere(['templateID' => $request->templateID]);
        return $this->sendResponse($reportTemplateColumnLink->toArray(), 'Report Template Column Link retrieved successfully');
    }
}
