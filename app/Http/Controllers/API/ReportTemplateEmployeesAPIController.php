<?php
/**
 * =============================================
 * -- File Name : ReportTemplateEmployeesAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report Template
 * -- Author : Mubashir
 * -- Create date : 21 - January 2019
 * -- Description :  This file contains the all CRUD for Report template employee assign
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportTemplateEmployeesAPIRequest;
use App\Http\Requests\API\UpdateReportTemplateEmployeesAPIRequest;
use App\Models\ReportTemplateEmployees;
use App\Repositories\ReportTemplateEmployeesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReportTemplateEmployeesController
 * @package App\Http\Controllers\API
 */
class ReportTemplateEmployeesAPIController extends AppBaseController
{
    /** @var  ReportTemplateEmployeesRepository */
    private $reportTemplateEmployeesRepository;

    public function __construct(ReportTemplateEmployeesRepository $reportTemplateEmployeesRepo)
    {
        $this->reportTemplateEmployeesRepository = $reportTemplateEmployeesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateEmployees",
     *      summary="Get a listing of the ReportTemplateEmployees.",
     *      tags={"ReportTemplateEmployees"},
     *      description="Get all ReportTemplateEmployees",
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
     *                  @SWG\Items(ref="#/definitions/ReportTemplateEmployees")
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
        $this->reportTemplateEmployeesRepository->pushCriteria(new RequestCriteria($request));
        $this->reportTemplateEmployeesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportTemplateEmployees = $this->reportTemplateEmployeesRepository->all();

        return $this->sendResponse($reportTemplateEmployees->toArray(), trans('custom.report_template_employees_retrieved_successfully'));
    }

    /**
     * @param CreateReportTemplateEmployeesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reportTemplateEmployees",
     *      summary="Store a newly created ReportTemplateEmployees in storage",
     *      tags={"ReportTemplateEmployees"},
     *      description="Store ReportTemplateEmployees",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateEmployees that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateEmployees")
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
     *                  ref="#/definitions/ReportTemplateEmployees"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportTemplateEmployeesAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'employeeSystemID' => 'required|array'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['companyID'] = \Helper::getCompanyById($input['companySystemID']);

        $reportTemplateEmployees = '';
        foreach ($input['employeeSystemID'] as $val) {
            $data['companyReportTemplateID'] = $input['companyReportTemplateID'];
            $data['employeeSystemID'] = $val['employeeSystemID'];
            $data['companySystemID'] = $input['companySystemID'];
            $data['companyID'] = $input['companyID'];
            $data['createdPCID'] = gethostname();
            $data['createdUserID'] = \Helper::getEmployeeID();
            $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateEmployees = $this->reportTemplateEmployeesRepository->create($data);
        }

        return $this->sendResponse($reportTemplateEmployees->toArray(), trans('custom.report_template_employees_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateEmployees/{id}",
     *      summary="Display the specified ReportTemplateEmployees",
     *      tags={"ReportTemplateEmployees"},
     *      description="Get ReportTemplateEmployees",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateEmployees",
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
     *                  ref="#/definitions/ReportTemplateEmployees"
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
        /** @var ReportTemplateEmployees $reportTemplateEmployees */
        $reportTemplateEmployees = $this->reportTemplateEmployeesRepository->findWithoutFail($id);

        if (empty($reportTemplateEmployees)) {
            return $this->sendError(trans('custom.report_template_employees_not_found'));
        }

        return $this->sendResponse($reportTemplateEmployees->toArray(), trans('custom.report_template_employees_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateReportTemplateEmployeesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reportTemplateEmployees/{id}",
     *      summary="Update the specified ReportTemplateEmployees in storage",
     *      tags={"ReportTemplateEmployees"},
     *      description="Update ReportTemplateEmployees",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateEmployees",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateEmployees that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateEmployees")
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
     *                  ref="#/definitions/ReportTemplateEmployees"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportTemplateEmployeesAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReportTemplateEmployees $reportTemplateEmployees */
        $reportTemplateEmployees = $this->reportTemplateEmployeesRepository->findWithoutFail($id);

        if (empty($reportTemplateEmployees)) {
            return $this->sendError(trans('custom.report_template_employees_not_found'));
        }

        $reportTemplateEmployees = $this->reportTemplateEmployeesRepository->update($input, $id);

        return $this->sendResponse($reportTemplateEmployees->toArray(), trans('custom.reporttemplateemployees_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reportTemplateEmployees/{id}",
     *      summary="Remove the specified ReportTemplateEmployees from storage",
     *      tags={"ReportTemplateEmployees"},
     *      description="Delete ReportTemplateEmployees",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateEmployees",
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
        /** @var ReportTemplateEmployees $reportTemplateEmployees */
        $reportTemplateEmployees = $this->reportTemplateEmployeesRepository->findWithoutFail($id);

        if (empty($reportTemplateEmployees)) {
            return $this->sendError(trans('custom.report_template_employees_not_found'));
        }

        $reportTemplateEmployees->delete();

        return $this->sendResponse($id, trans('custom.report_template_employees_deleted_successfully'));
    }

    public function getReportTemplateAssignedEmployee(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $output = ReportTemplateEmployees::with('employee_by')->where('companyReportTemplateID',$request->companyReportTemplateID)->where('companySystemID',$request->companySystemID);

        return \DataTables::eloquent($output)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }
}
