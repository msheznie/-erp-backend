<?php

namespace App\Http\Controllers\API;

use App\helper\CompanyService;
use App\helper\Helper;
use App\Http\Requests\API\CreateSystemGlCodeScenarioDetailAPIRequest;
use App\Http\Requests\API\UpdateSystemGlCodeScenarioDetailAPIRequest;
use App\Models\SystemGlCodeScenarioDetail;
use App\Repositories\SystemGlCodeScenarioDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class SystemGlCodeScenarioDetailController
 * @package App\Http\Controllers\API
 */

class SystemGlCodeScenarioDetailAPIController extends AppBaseController
{
    /** @var  SystemGlCodeScenarioDetailRepository */
    private $systemGlCodeScenarioDetailRepository;

    public function __construct(SystemGlCodeScenarioDetailRepository $systemGlCodeScenarioDetailRepo)
    {
        $this->systemGlCodeScenarioDetailRepository = $systemGlCodeScenarioDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/systemGlCodeScenarioDetails",
     *      summary="Get a listing of the SystemGlCodeScenarioDetails.",
     *      tags={"SystemGlCodeScenarioDetail"},
     *      description="Get all SystemGlCodeScenarioDetails",
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
     *                  @SWG\Items(ref="#/definitions/SystemGlCodeScenarioDetail")
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
        $this->systemGlCodeScenarioDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->systemGlCodeScenarioDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $systemGlCodeScenarioDetails = $this->systemGlCodeScenarioDetailRepository->all();

        return $this->sendResponse($systemGlCodeScenarioDetails->toArray(), 'System Gl Code Scenario Details retrieved successfully');
    }

    /**
     * @param CreateSystemGlCodeScenarioDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/systemGlCodeScenarioDetails",
     *      summary="Store a newly created SystemGlCodeScenarioDetail in storage",
     *      tags={"SystemGlCodeScenarioDetail"},
     *      description="Store SystemGlCodeScenarioDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SystemGlCodeScenarioDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SystemGlCodeScenarioDetail")
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
     *                  ref="#/definitions/SystemGlCodeScenarioDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSystemGlCodeScenarioDetailAPIRequest $request)
    {
        $input = $request->all();

        $systemGlCodeScenarioDetail = $this->systemGlCodeScenarioDetailRepository->create($input);

        return $this->sendResponse($systemGlCodeScenarioDetail->toArray(), 'System Gl Code Scenario Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/systemGlCodeScenarioDetails/{id}",
     *      summary="Display the specified SystemGlCodeScenarioDetail",
     *      tags={"SystemGlCodeScenarioDetail"},
     *      description="Get SystemGlCodeScenarioDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SystemGlCodeScenarioDetail",
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
     *                  ref="#/definitions/SystemGlCodeScenarioDetail"
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
        /** @var SystemGlCodeScenarioDetail $systemGlCodeScenarioDetail */
        $systemGlCodeScenarioDetail = $this->systemGlCodeScenarioDetailRepository
            ->has('master')
            ->has('company')
            ->with('master:id,description')
            ->with('company:companySystemID,CompanyID,CompanyName')
            ->with('chart_of_account:chartOfAccountSystemID,AccountCode,AccountDescription')
            ->findWithoutFail($id);

        if (empty($systemGlCodeScenarioDetail)) {
            return $this->sendError('System Gl Code Scenario Detail not found');
        }

        $data['company_scenario'] = $systemGlCodeScenarioDetail->toArray();

        return $this->sendResponse($data, 'System Gl Code Scenario Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateSystemGlCodeScenarioDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/systemGlCodeScenarioDetails/{id}",
     *      summary="Update the specified SystemGlCodeScenarioDetail in storage",
     *      tags={"SystemGlCodeScenarioDetail"},
     *      description="Update SystemGlCodeScenarioDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SystemGlCodeScenarioDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SystemGlCodeScenarioDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SystemGlCodeScenarioDetail")
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
     *                  ref="#/definitions/SystemGlCodeScenarioDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSystemGlCodeScenarioDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var SystemGlCodeScenarioDetail $systemGlCodeScenarioDetail */
        $systemGlCodeScenarioDetail = $this->systemGlCodeScenarioDetailRepository->findWithoutFail($id);

        if (empty($systemGlCodeScenarioDetail)) {
            return $this->sendError('System Gl Code Scenario Detail not found');
        }

        $input['updated_by'] = Helper::getEmployeeInfo()->employeeSystemID;

        $systemGlCodeScenarioDetail = $this->systemGlCodeScenarioDetailRepository->update($input, $id);

        return $this->sendResponse($systemGlCodeScenarioDetail->toArray(), 'Gl code updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/systemGlCodeScenarioDetails/{id}",
     *      summary="Remove the specified SystemGlCodeScenarioDetail from storage",
     *      tags={"SystemGlCodeScenarioDetail"},
     *      description="Delete SystemGlCodeScenarioDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SystemGlCodeScenarioDetail",
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
        /** @var SystemGlCodeScenarioDetail $systemGlCodeScenarioDetail */
        $systemGlCodeScenarioDetail = $this->systemGlCodeScenarioDetailRepository->findWithoutFail($id);

        if (empty($systemGlCodeScenarioDetail)) {
            return $this->sendError('System Gl Code Scenario Detail not found');
        }

        $systemGlCodeScenarioDetail->delete();

        return $this->sendSuccess('System Gl Code Scenario Detail deleted successfully');
    }

    public function list_config_scenarios(Request $request){
        $input = $request->all();

        $sort = Helper::dataTableSortOrder($input);
        $search = $request->input('search.value');

        $companyId = $input['companyId'];
        $company_list = CompanyService::get_company_with_sub($companyId);

        $qry = $this->systemGlCodeScenarioDetailRepository->fetch_company_scenarios($company_list, $search);
        return DataTables::eloquent($qry)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
