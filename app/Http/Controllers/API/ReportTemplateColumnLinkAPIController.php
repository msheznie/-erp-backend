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
use App\Models\ReportTemplateDetails;
use App\Models\ReportColumnTemplateDetail;
use App\Models\ReportTemplate;
use App\Repositories\ReportTemplateColumnLinkRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;

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

        return $this->sendResponse($reportTemplateColumnLinks->toArray(), trans('custom.report_template_column_links_retrieved_successfull'));
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

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $maxSortOrder = ReportTemplateColumnLink::where('templateID',$input['templateMasterID'])->max('sortOrder');

        if ($input['columnID']) {
            foreach ($input['columnID'] as $key => $val) {
                $data['columnID'] = $val['columnID'];
                $data['templateID'] = $input['templateMasterID'];
                $data['sortOrder'] = $maxSortOrder + ($key + 1);
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

        return $this->sendResponse([], trans('custom.report_template_column_link_saved_successfully'));
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
            return $this->sendError(trans('custom.report_template_column_link_not_found'));
        }

        return $this->sendResponse($reportTemplateColumnLink->toArray(), trans('custom.report_template_column_link_retrieved_successfully'));
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
            return $this->sendError(trans('custom.report_template_column_link_not_found'));
        }

        if (isset($input['formula'])) {
            if (is_array(($input['formula']))) {
                $formula = $input['formula'];
                if ($formula) {
                    $input['formula'] = implode('~', $formula);
                    if ($input['formula']) {
                        $formulaColumnID = [];
                        $formulaRowID = [];
                        foreach ($formula as $val) {
                            $firstChar = substr($val, 0, 1);
                            if ($firstChar == '#') {
                                $formulaColumnID[] = ltrim($val, '#');
                            }
                            if ($firstChar == '$') {
                                $formulaRowID[] = ltrim($val, '$');
                            }
                        }
                        $input['formulaColumnID'] = join(',', $formulaColumnID);
                        $input['formulaRowID'] = join(',', $formulaRowID);
                    }
                } else {
                    $input['formulaColumnID'] = null;
                    $input['formulaRowID'] = null;
                    $input['formula'] = null;
                }
            }
        }

        $employee = \Helper::getEmployeeInfo();

        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        $input['modifiedDateTime'] = now();

        $input = $this->convertArrayToValue($input);

        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->update($input, $id);

        return $this->sendResponse($reportTemplateColumnLink->toArray(), trans('custom.reporttemplatecolumnlink_updated_successfully'));
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

        $columnLink = ReportTemplateColumnLink::whereRaw("formulaColumnID LIKE '$id,%' OR formulaColumnID LIKE '%,$id,%' OR formulaColumnID LIKE '%,$id' OR formulaColumnID = '$id'")->first();

        if ($columnLink) {
            return $this->sendError(trans('custom.you_cannot_delete_this_column_because_already_this'));
        }

        if (empty($reportTemplateColumnLink)) {
            return $this->sendError(trans('custom.report_template_column_link_not_found'));
        }


        $reportTemplateColumnLink->delete();

        $existingTemplates = ReportTemplateColumnLink::where('templateID',$reportTemplateColumnLink->templateID)->get();

        if (sizeof($existingTemplates) == 0) {
            ReportTemplate::where('companyReportTemplateID', $reportTemplateColumnLink->templateID)->update(['columnTemplateID' => null]);
        }

        return $this->sendResponse($id, trans('custom.report_template_column_link_deleted_successfully'));
    }

    public function getTemplateColumnLinks(Request $request)
    {
        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->orderBy('sortOrder', 'asc')->findWhere(['templateID' => $request->templateID]);
        return $this->sendResponse($reportTemplateColumnLink->toArray(), trans('custom.report_template_column_link_retrieved_successfully'));
    }

    public function reportTemplateFormulaColumn(Request $request)
    {
        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->findWithoutFail($request->columnLinkID);

        if (empty($reportTemplateColumnLink)) {
            return $this->sendError(trans('custom.report_template_column_link_not_found'));
        }

        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->findWhereIn('columnLinkID', explode(',', $reportTemplateColumnLink->formulaColumnID));

        $reportTemplateRows = ReportTemplateDetails::OfMaster($request->companyReportTemplateID)->where('itemType', 2)->get();

        $response = array('columns' => $reportTemplateColumnLink, 'rows' => $reportTemplateRows);

        return $this->sendResponse($response, trans('custom.tax_formula_detail_retrieved_successfully'));
    }

    public function loadColumnTemplate(Request $request)
    {
        $input = $request->all();

        $existing = ReportTemplateColumnLink::where('templateID',$input['templateID'])->get();

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $columns = ReportColumnTemplateDetail::with(['column_data'])
                                             ->where('reportColumnTemplateID', $input['columns']['reportColumnTemplateID'])
                                             ->get();


        DB::beginTransaction();
        try {

            if ($existing) {
                foreach ($existing as $key => $value) {
                    ReportTemplateColumnLink::where('columnLinkID',$value['columnLinkID'])->delete();
                }
            }

            $maxSortOrder = ReportTemplateColumnLink::where('templateID',$input['templateID'])->max('sortOrder');
            if ($columns) {
                foreach ($columns as $key => $val) {
                    $data['columnID'] = $val['column_data']['columnID'];
                    $data['templateID'] = $input['templateID'];
                    $data['sortOrder'] = $maxSortOrder + ($key + 1);
                    $data['description'] = $val['column_data']['description'];
                    $data['shortCode'] = $val['column_data']['shortCode'];
                    $data['type'] = $val['column_data']['type'];
                    $data['companySystemID'] = $input['companySystemID'];
                    $data['companyID'] = $input['companyID'];
                    $data['createdPCID'] = gethostname();
                    $data['createdUserID'] = \Helper::getEmployeeID();
                    $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                    $reportTemplateColumnLinks = $this->reportTemplateColumnLinkRepository->create($data);
                }
            }

            ReportTemplate::where('companyReportTemplateID', $input['templateID'])->update(['columnTemplateID' => $input['columns']['reportColumnTemplateID']]);

            DB::commit();
            return $this->sendResponse([], trans('custom.report_template_column_link_saved_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage()." Line". $exception->getLine(), 500);
        }
    }
}
