<?php

namespace App\Http\Controllers\API;

use App\helper\CompanyService;
use App\helper\Helper;
use App\Http\Requests\API\CreateSystemGlCodeScenarioDetailAPIRequest;
use App\Http\Requests\API\UpdateSystemGlCodeScenarioDetailAPIRequest;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\JvMaster;
use App\Models\SystemGlCodeScenario;
use App\Models\SystemGlCodeScenarioDetail;
use App\Repositories\SystemGlCodeScenarioDetailRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\AuditLogsTrait;

/**
 * Class SystemGlCodeScenarioDetailController
 * @package App\Http\Controllers\API
 */

class SystemGlCodeScenarioDetailAPIController extends AppBaseController
{
    /** @var  SystemGlCodeScenarioDetailRepository */
    private $systemGlCodeScenarioDetailRepository;
    use AuditLogsTrait;

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
        $input = $this->convertArrayToValue($input);

        /** @var SystemGlCodeScenarioDetail $systemGlCodeScenarioDetail */
        $systemGlCodeScenarioDetail = $this->systemGlCodeScenarioDetailRepository->findWithoutFail($id);

        if (empty($systemGlCodeScenarioDetail)) {
            return $this->sendError('System Gl Code Scenario Detail not found');
        }

        $input['updated_by'] = Helper::getEmployeeInfo()->employeeSystemID;

        $uuid = $input['tenant_uuid'] ?? 'local';
        $db = $input['db'] ?? '';

        if(isset($input['tenant_uuid']) ){
            unset($input['tenant_uuid']);
        }

        if(isset($input['db']) ){
            unset($input['db']);
        }

        $previousValue = $systemGlCodeScenarioDetail->toArray();
        $newValue = $input;
        $transactionID = 0;

        DB::beginTransaction();
        try {

            $systemGlCodeScenarioDetail = $this->systemGlCodeScenarioDetailRepository->update($input, $id);

            $hr_scenarios = [7, 8, 25];
            if(in_array($systemGlCodeScenarioDetail->systemGlScenarioID, $hr_scenarios)){
                $this->update_hr_config($systemGlCodeScenarioDetail);
            }

            if($systemGlCodeScenarioDetail->systemGlScenarioID == 14){

                $chartOfAccountAssigned = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $systemGlCodeScenarioDetail->chartOfAccountSystemID)->where('companySystemID', $systemGlCodeScenarioDetail->companySystemID)->first();
                if($chartOfAccountAssigned){
                    Company::where('companySystemID', $systemGlCodeScenarioDetail->companySystemID)->update(['exchangeGainLossGLCodeSystemID' => $systemGlCodeScenarioDetail->chartOfAccountSystemID, 'exchangeGainLossGLCode' => $chartOfAccountAssigned->AccountCode]);
                }
                else {
                    if(!isset($input['isFromGLConfig'])) {
                        return $this->sendError('GL Code is not assigned to the company');
                    }
                }

            }

            DB::commit();

            $this->auditLog($db, $transactionID, $uuid, "chart_of_account_config", "{$input['departmentName']} - {$systemGlCodeScenarioDetail->master->description} has updated", "U", $newValue, $previousValue);

            return $this->sendResponse($systemGlCodeScenarioDetail->toArray(), 'Gl code updated successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return $this->sendError($e->getMessage(), 500);
        }

    }

    function update_hr_config($data){

        switch ($data->systemGlScenarioID){
            case 7: //SPC
                $glTypeCode = 'SPC';
            break;

            case 8: //IOU
                $glTypeCode = 'IOU';
            break;

            case 25: //NSPC
                $glTypeCode = 'NSPC';
            break;

            default:
                return true;
        }

        $id = DB::table('hrms_otherglcode')
            ->selectRaw('otherglcodeID')
            ->where('glTypeCode', $glTypeCode)
            ->where('companySystemID', $data->companySystemID)
            ->first();

        if($id){
            $id = $id->otherglcodeID;

            DB::table('hrms_otherglcode')
                ->where('otherglcodeID', $id)
                ->update(['glCode'=> $data->chartOfAccountSystemID]);
        }
        else{
            DB::table('hrms_otherglcode')->insert([
                'glTypeCode'=> $glTypeCode,
                'glCode'=> $data->chartOfAccountSystemID,
                'companySystemID'=> $data->companySystemID,
                'timestamp'=> Carbon::now()
            ]);
        }

        return true;
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

        $qry = $this->systemGlCodeScenarioDetailRepository->fetch_company_scenarios($company_list, $search,$input['id']);
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
