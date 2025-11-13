<?php

namespace App\Http\Controllers\API;

use App\helper\CompanyService;
use App\helper\Helper;
use App\Http\Requests\API\CreateSystemGlCodeScenarioAPIRequest;
use App\Http\Requests\API\UpdateSystemGlCodeScenarioAPIRequest;
use App\Models\Company;
use App\Models\SystemGlCodeScenario;
use App\Models\SystemGlCodeScenarioDetail;
use App\Repositories\SystemGlCodeScenarioRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

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

        return $this->sendResponse($systemGlCodeScenarios->toArray(), trans('custom.system_gl_code_scenarios_retrieved_successfully'));
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

        return $this->sendResponse($systemGlCodeScenario->toArray(), trans('custom.system_gl_code_scenario_saved_successfully'));
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
            return $this->sendError(trans('custom.system_gl_code_scenario_not_found'));
        }

        return $this->sendResponse($systemGlCodeScenario->toArray(), trans('custom.system_gl_code_scenario_retrieved_successfully'));
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
            return $this->sendError(trans('custom.system_gl_code_scenario_not_found'));
        }

        $systemGlCodeScenario = $this->systemGlCodeScenarioRepository->update($input, $id);

        return $this->sendResponse($systemGlCodeScenario->toArray(), trans('custom.systemglcodescenario_updated_successfully'));
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
            return $this->sendError(trans('custom.system_gl_code_scenario_not_found'));
        }

        $systemGlCodeScenario->delete();

        return $this->sendSuccess('System Gl Code Scenario deleted successfully');
    }


    public function scenario_assign(Request $request){
        $current_companyId = $request['current_companyId'];
        $company_list = CompanyService::get_company_with_sub($current_companyId);

        $date_time = Carbon::now();
        $user_id = 0;
        $un_assign = [];
        foreach ($company_list as $company_id){
           $scenarios = $this->systemGlCodeScenarioRepository->un_assign_scenario($company_id);

           if($scenarios){
               foreach ($scenarios as $item){
                   $un_assign[] = [
                       'systemGlScenarioID' => $item['id'],
                       'companySystemID' => $company_id,
                       'chartOfAccountSystemID' => null,
                       'serviceLineSystemID' => null,
                       'created_by' => &$user_id,
                       'created_at' => $date_time
                   ];
               }
           }
        }

        if($un_assign){
            $user_id = Helper::getEmployeeInfo()->employeeSystemID;

            SystemGlCodeScenarioDetail::insert( $un_assign );

            return $this->sendResponse([], trans('custom.retrieve', ['attribute' => trans('custom.record')]));
        }

        $this->sendResponse([], trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function coa_config_companies(Request $request){
        $current_companyId = $request['current_companyId'];
        $subCompanies = CompanyService::get_company_with_sub($current_companyId);

        $company_list = Company::selectRaw("companySystemID AS value, CONCAT(CompanyID, ' - ', CompanyName) AS label")
            ->whereIn("companySystemID", $subCompanies)->get();

        $company_list = ($company_list)? $company_list->toArray(): [];

        $data['company_list'] = $company_list;

        return $this->sendResponse($data, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }
}
