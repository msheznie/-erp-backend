<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateCustomUserReportsAPIRequest;
use App\Http\Requests\API\UpdateCustomUserReportsAPIRequest;
use App\Models\CustomReportColumns;
use App\Models\CustomReportMaster;
use App\Models\CustomUserReports;
use App\Models\ExpenseClaim;
use App\Repositories\CustomUserReportColumnsRepository;
use App\Repositories\CustomUserReportsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomUserReportsController
 * @package App\Http\Controllers\API
 */
class CustomUserReportsAPIController extends AppBaseController
{
    /** @var  CustomUserReportsRepository */
    private $customUserReportsRepository;
    private $customUserReportColumnsRepository;

    public function __construct(CustomUserReportsRepository $customUserReportsRepo, CustomUserReportColumnsRepository $customUserReportColumnsRepo)
    {
        $this->customUserReportsRepository = $customUserReportsRepo;
        $this->customUserReportColumnsRepository = $customUserReportColumnsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customUserReports",
     *      summary="Get a listing of the CustomUserReports.",
     *      tags={"CustomUserReports"},
     *      description="Get all CustomUserReports",
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
     *                  @SWG\Items(ref="#/definitions/CustomUserReports")
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
        $this->customUserReportsRepository->pushCriteria(new RequestCriteria($request));
        $this->customUserReportsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customUserReports = $this->customUserReportsRepository->all();

        return $this->sendResponse($customUserReports->toArray(), 'Custom User Reports retrieved successfully');
    }

    /**
     * @param CreateCustomUserReportsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customUserReports",
     *      summary="Store a newly created CustomUserReports in storage",
     *      tags={"CustomUserReports"},
     *      description="Store CustomUserReports",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomUserReports that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomUserReports")
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
     *                  ref="#/definitions/CustomUserReports"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomUserReportsAPIRequest $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'report_master_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $reportMaster = CustomReportMaster::where('id', $input['report_master_id'])
            ->where('is_active', 1)
            ->first();
        if (empty($reportMaster)) {
            return $this->sendError('Reports Master not found');
        }

        $input['user_id'] = Helper::getEmployeeSystemID();
        if (!isset($input['name'])) {
            $count = CustomUserReports::where('user_id', $input['user_id'])->count();
            $input['name'] = 'Report' . ($count + 1);
        }

        DB::beginTransaction();
        try {
            $customUserReports = $this->customUserReportsRepository->create($input);

            $masterColumns = CustomReportColumns::where('report_master_id', $input['report_master_id'])
                ->get();

            foreach ($masterColumns as $col) {
                $data['user_report_id'] = $customUserReports->id;
                $data['column_id'] = $col['id'];
                $data['label'] = $col['label'];
                $data['is_sort'] = $col['is_default_sort'];
                $data['sort_by'] = $col['sort_by'];
                $data['sort_order'] = $col['sort_order'];
                $data['is_group_by'] = $col['is_default_group_by'];
                $data['is_filter'] = 0;
                $this->customUserReportColumnsRepository->create($data);
            }

            DB::commit();
            return $this->sendResponse($customUserReports->toArray(), 'Custom User Reports saved successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customUserReports/{id}",
     *      summary="Display the specified CustomUserReports",
     *      tags={"CustomUserReports"},
     *      description="Get CustomUserReports",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomUserReports",
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
     *                  ref="#/definitions/CustomUserReports"
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
        /** @var CustomUserReports $customUserReports */
        $customUserReports = $this->customUserReportsRepository->with(['columns' => function($q){
            $q->orderBy('sort_order','asc');
        }, 'default_columns' => function($q){
            $q->orderBy('sort_order','asc');
        }])->find($id);

        if (empty($customUserReports)) {
            return $this->sendError('Custom User Reports not found');
        }

        return $this->sendResponse($customUserReports->toArray(), 'Custom User Reports retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustomUserReportsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customUserReports/{id}",
     *      summary="Update the specified CustomUserReports in storage",
     *      tags={"CustomUserReports"},
     *      description="Update CustomUserReports",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomUserReports",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomUserReports that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomUserReports")
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
     *                  ref="#/definitions/CustomUserReports"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomUserReportsAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomUserReports $customUserReports */
        $customUserReports = $this->customUserReportsRepository->findWithoutFail($id);

        if (empty($customUserReports)) {
            return $this->sendError('Custom Reports not found');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        DB::beginTransaction();
        try {
            if (isset($input['columns']) && is_array($input['columns'])) {
                $this->customUserReportColumnsRepository->where('user_report_id',$id)->delete();
                foreach ($input['columns'] as $col) {
                    $col['user_report_id'] = $id;
                    $this->customUserReportColumnsRepository->create($col);
                }
            }

            $customUserReports = $this->customUserReportsRepository->update(array_only($input, ['name', 'is_private']), $id);

            DB::commit();
            return $this->sendResponse($customUserReports->toArray(), 'Custom Reports updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customUserReports/{id}",
     *      summary="Remove the specified CustomUserReports from storage",
     *      tags={"CustomUserReports"},
     *      description="Delete CustomUserReports",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomUserReports",
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
        /** @var CustomUserReports $customUserReports */
        $customUserReports = $this->customUserReportsRepository->findWithoutFail($id);

        if (empty($customUserReports)) {
            return $this->sendError('Custom User Reports not found');
        }

        $customUserReports->delete();

        return $this->sendSuccess('Custom User Reports deleted successfully');
    }

    public function getCustomReportsByUser(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('is_private'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $userId = Helper::getEmployeeSystemID();

        $reports = CustomUserReports::where('user_id', $userId)
            ->with('created_by');

        if (array_key_exists('is_private', $input)) {
            if (($input['is_private'] == 0 || $input['is_private'] == 1) && !is_null($input['is_private'])) {
                $reports = $reports->where('is_private', $input['is_private']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $reports = $reports->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($reports)
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

    private function geReportColumns($columns){
        $result = [];
        foreach ($columns as $column){
            if(isset($column['column']) && isset($column['column']['column'])){
                array_push($result,$column['column']['column']);
            }
        }

        return array_unique($result);
    }

    private function getReportRelationship($columns){
        $result = [];
        foreach ($columns as $column){
            if(isset($column['column']) && $column['column']['is_relationship']){
                $temp = array(
                    'relationship' => $column['column']['relationship'],
                    'columns' => $column['column']['relationship_columns']
                );
                array_push($result,$temp);
            }
        }

        return $result;
    }

    public function customReportView(Request $request){

        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'id' => 'required:numeric',
            'companyId' => 'required:numeric',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $report = $this->customUserReportsRepository->with(['columns' => function($q){
            $q->with(['column'])->orderBy('sort_order','asc');
        }])->find($input['id']);

        if (empty($report)) {
            return $this->sendError('Report not found');
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $primaryKey = '';
        $data = [];
        switch ($report->report_master_id) {
            case 1:
                $primaryKey = 'expenseClaimMasterAutoID';
                $data = ExpenseClaim::whereIn('companySystemID', $subCompanies);
                    //->with('created_by')
                break;
            case 2:
                break;
            case 3:
                break;
            case 4:
                break;
            default:
                $data = [];
                break;
        }


        if($data && $report['columns']){
            $columns = $this->geReportColumns($report['columns']);
            array_push($columns,$primaryKey);
            $data = $data->select($columns);
            $relationships = $this->getReportRelationship($report['columns']);
            if($relationships){
                foreach ($relationships as $relation){
                    $data = $data->with([$relation['relationship'] => function ($q) use($relation){
                         $q->select(array_unique(explode(',',$relation['columns'])));
                    }]);
                }
            }
            $data = $data->paginate(10);
        }

        $output = array(
            'data' => $data,
            'report' => $report
        );

        return $this->sendResponse($output , 'Custom Report retrieved successfully');

    }
}
