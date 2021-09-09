<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateSystemGlCodeScenarioAPIRequest;
use App\Http\Requests\API\UpdateSystemGlCodeScenarioAPIRequest;
use App\Models\Company;
use App\Models\SystemGlCodeScenario;
use App\Repositories\SystemGlCodeScenarioRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class SystemGlCodeScenarioController
 * @package App\Http\Controllers\API
 */

class SystemGlCodeScenarioAPIController extends AppBaseController
{
    /** @var  SystemGlCodeScenarioRepository */
    private $systemGlCodeScenarioRepository;

    public function __construct(SystemGlCodeScenarioRepository $systemGlCodeScenarioRepo)
    {
        $this->systemGlCodeScenarioRepository = $systemGlCodeScenarioRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/systemGlCodeScenarios",
     *      summary="Get a listing of the SystemGlCodeScenarios.",
     *      tags={"SystemGlCodeScenario"},
     *      description="Get all SystemGlCodeScenarios",
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
     *                  @SWG\Items(ref="#/definitions/SystemGlCodeScenario")
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
        $this->systemGlCodeScenarioRepository->pushCriteria(new RequestCriteria($request));
        $this->systemGlCodeScenarioRepository->pushCriteria(new LimitOffsetCriteria($request));
        $systemGlCodeScenarios = $this->systemGlCodeScenarioRepository->all();

        return $this->sendResponse($systemGlCodeScenarios->toArray(), 'System Gl Code Scenarios retrieved successfully');
    }

    /**
     * @param CreateSystemGlCodeScenarioAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/systemGlCodeScenarios",
     *      summary="Store a newly created SystemGlCodeScenario in storage",
     *      tags={"SystemGlCodeScenario"},
     *      description="Store SystemGlCodeScenario",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SystemGlCodeScenario that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SystemGlCodeScenario")
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
     *                  ref="#/definitions/SystemGlCodeScenario"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSystemGlCodeScenarioAPIRequest $request)
    {
        $input = $request->all();

        $systemGlCodeScenario = $this->systemGlCodeScenarioRepository->create($input);

        return $this->sendResponse($systemGlCodeScenario->toArray(), 'System Gl Code Scenario saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/systemGlCodeScenarios/{id}",
     *      summary="Display the specified SystemGlCodeScenario",
     *      tags={"SystemGlCodeScenario"},
     *      description="Get SystemGlCodeScenario",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SystemGlCodeScenario",
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
     *                  ref="#/definitions/SystemGlCodeScenario"
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
        /** @var SystemGlCodeScenario $systemGlCodeScenario */
        $systemGlCodeScenario = $this->systemGlCodeScenarioRepository->findWithoutFail($id);

        if (empty($systemGlCodeScenario)) {
            return $this->sendError('System Gl Code Scenario not found');
        }

        return $this->sendResponse($systemGlCodeScenario->toArray(), 'System Gl Code Scenario retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateSystemGlCodeScenarioAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/systemGlCodeScenarios/{id}",
     *      summary="Update the specified SystemGlCodeScenario in storage",
     *      tags={"SystemGlCodeScenario"},
     *      description="Update SystemGlCodeScenario",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SystemGlCodeScenario",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SystemGlCodeScenario that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SystemGlCodeScenario")
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
     *                  ref="#/definitions/SystemGlCodeScenario"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSystemGlCodeScenarioAPIRequest $request)
    {
        $input = $request->all();

        /** @var SystemGlCodeScenario $systemGlCodeScenario */
        $systemGlCodeScenario = $this->systemGlCodeScenarioRepository->findWithoutFail($id);

        if (empty($systemGlCodeScenario)) {
            return $this->sendError('System Gl Code Scenario not found');
        }

        $systemGlCodeScenario = $this->systemGlCodeScenarioRepository->update($input, $id);

        return $this->sendResponse($systemGlCodeScenario->toArray(), 'SystemGlCodeScenario updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/systemGlCodeScenarios/{id}",
     *      summary="Remove the specified SystemGlCodeScenario from storage",
     *      tags={"SystemGlCodeScenario"},
     *      description="Delete SystemGlCodeScenario",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SystemGlCodeScenario",
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
        /** @var SystemGlCodeScenario $systemGlCodeScenario */
        $systemGlCodeScenario = $this->systemGlCodeScenarioRepository->findWithoutFail($id);

        if (empty($systemGlCodeScenario)) {
            return $this->sendError('System Gl Code Scenario not found');
        }

        $systemGlCodeScenario->delete();

        return $this->sendSuccess('System Gl Code Scenario deleted successfully');
    }

    function get_company_list( $companyId ){
        $isGroup = Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            return  Helper::getGroupCompany($companyId);
        }

        return  [$companyId];
    }

    public function coa_config_companies(Request $request){
        $current_companyId = $request['current_companyId'];
        $subCompanies = $this->get_company_list($current_companyId);

        $company_list = Company::selectRaw("companySystemID AS value, CONCAT(CompanyID, ' - ', CompanyName) AS label")
            ->whereIn("companySystemID", $subCompanies)->get();

        $company_list = ($company_list)? $company_list->toArray(): [];

        $data['company_list'] = $company_list;

        return $this->sendResponse($data, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function list_config_scenarios(Request $request){
        $input = $request->all();

        $sort = Helper::dataTableSortOrder($input);
        $search = $request->input('search.value');

        $companyId = $input['companyId'];
        $company_list = $this->get_company_list($companyId); dd($company_list);

        $qry = $this->systemGlCodeScenarioRepository->fetch_company_data($company_list, $search);
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
