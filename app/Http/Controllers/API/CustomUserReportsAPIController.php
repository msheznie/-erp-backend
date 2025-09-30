<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateCustomUserReportsAPIRequest;
use App\Http\Requests\API\UpdateCustomUserReportsAPIRequest;
use App\Models\CustomReportColumns;
use App\Models\CustomReportMaster;
use App\Models\CustomUserReports;
use App\Repositories\CustomFiltersColumnRepository;
use App\Repositories\CustomReportEmployeesRepository;
use App\Repositories\CustomUserReportColumnsRepository;
use App\Repositories\CustomUserReportsRepository;
use App\Repositories\CustomUserReportSummarizeRepository;
use Carbon\Carbon;
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
    private $customFiltersColumnRepository;
    private $customReportEmployeesRepository;
    private $customUserReportSummarizeRepository;

    public function __construct(CustomUserReportsRepository $customUserReportsRepo,
                                CustomUserReportColumnsRepository $customUserReportColumnsRepo,
                                CustomFiltersColumnRepository $customFiltersColumnRepo,
                                CustomReportEmployeesRepository $customReportEmployeesRepo,
                                CustomUserReportSummarizeRepository $customUserReportSummarizeRepo)
    {
        $this->customUserReportsRepository = $customUserReportsRepo;
        $this->customUserReportColumnsRepository = $customUserReportColumnsRepo;
        $this->customFiltersColumnRepository = $customFiltersColumnRepo;
        $this->customReportEmployeesRepository = $customReportEmployeesRepo;
        $this->customUserReportSummarizeRepository = $customUserReportSummarizeRepo;
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

        return $this->sendResponse($customUserReports->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.custom_user_reports')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.reports_master')]));
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
            return $this->sendResponse($customUserReports->toArray(), trans('custom.save', ['attribute' => trans('custom.custom_user_reports')]));
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
        $customUserReports = $this->customUserReportsRepository->with(['columns' => function ($q) {
            $q->orderBy('sort_order', 'asc');
        }, 'default_columns' => function ($q) {
            $q->orderBy('sort_order', 'asc');
        }, 'filter_columns','summarize'])->find($id);

        if (empty($customUserReports)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_user_reports')]));
        }

        return $this->sendResponse($customUserReports->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.custom_user_reports')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_report')]));
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
                $this->customUserReportColumnsRepository->where('user_report_id', $id)->delete();
                $this->customUserReportSummarizeRepository->where('user_report_id', $id)->delete();
                foreach ($input['columns'] as $col) {
                    $col['user_report_id'] = $id;
                    $this->customUserReportColumnsRepository->create($col);

                    if(isset($col['new_summarize']) && count($col['new_summarize']) > 0){
                        foreach ($col['new_summarize'] as $summarize){
                            if(isset($summarize['isChecked']) && $summarize['isChecked']){
                                $tem = array(
                                    'user_report_id' => $id,
                                    'column_id' => $col['column_id'],
                                    'type_id' => $summarize['value']
                                );
                                $this->customUserReportSummarizeRepository->create($tem);
                            }
                        }
                    }
                }
            }

            if (isset($input['filterColumns']) && is_array($input['filterColumns'])) {
                $this->customFiltersColumnRepository->where('user_report_id', $id)->delete();
                foreach ($input['filterColumns'] as $col) {
                    $col = $this->convertArrayToSelectedValue($col, ['operator']);
                    if (is_array($col['value'])) {
                        $col = $this->convertArrayToSelectedValue($col, ['value']);
                    }
                    $col['user_report_id'] = $id;
                    $this->customFiltersColumnRepository->create($col);
                }
            }

            $customUserReports = $this->customUserReportsRepository->update(array_only($input, ['name', 'is_private']), $id);

            DB::commit();
            return $this->sendResponse($customUserReports->toArray(), trans('custom.update', ['attribute' => trans('custom.custom_report')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.reports')]));
        }
        DB::beginTransaction();
        try{
            $this->customUserReportColumnsRepository->where('user_report_id',$id)->delete();
            $this->customFiltersColumnRepository->where('user_report_id',$id)->delete();
            $this->customReportEmployeesRepository->where('user_report_id',$id)->delete();
            $this->customUserReportSummarizeRepository->where('user_report_id', $id)->delete();
            $customUserReports->delete();
            DB::commit();
            return $this->sendResponse($id,trans('custom.delete', ['attribute' => trans('custom.reports')]));
        }catch(\Exception $e){
            DB::rollBack();
            return $this->sendError($e->getMessage(), 500);
        }
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

        $reports = CustomUserReports::with('created_by')
            ->where(function ($q) use ($userId) {
                $q->where(function ($q1) use ($userId) {
                    $q1->where('is_private', 1)
                        ->where('user_id', $userId)
                        ->orWhereHas('assigned_employees');
                })->orWhere(function ($q1) use ($userId) {
                    $q1->where('is_private', 0);
                });
            });
        
        if (array_key_exists('is_private', $input) && ($input['is_private'] == 0 || $input['is_private'] == 1) && !is_null($input['is_private'])) {
            $reports = $reports->where('is_private', $input['is_private']);
        }
        
        $search = $request->input('search.value');      
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $reports = $reports->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->OrWhereHas('created_by', function ($q) use ($search) {
                        $q->where('empName', 'LIKE', "%{$search}%");
                    });
            });
        }

        return \DataTables::of($reports)
            ->order(function ($query) use ($input) {
                if (request()->has('order') && $input['order'][0]['column'] == 0) {
                    $query->orderBy('id', $input['order'][0]['dir']);
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    private function geReportColumns($columns)
    {
        $result = [];
        foreach ($columns as $column) {
            if (isset($column['column']) && isset($column['column']['column']) && $column['column']['column_type'] != 5) {
                $tmpColumn = $column['column']['table'] . '.' . $column['column']['column'];
                if ($column['column']['column_type'] == 4) {
                    $tmpColumn = 'SUM(' . $tmpColumn . ') as ' . $column['column']['column_as'];
                }else{
                    $tmpColumn = $tmpColumn. ' as '. $column['column']['column_as'];
                }
                array_push($result, $tmpColumn);
            }
        }

        return array_unique($result);
    }

    private function getSortByColumns($columns)
    {
        $result = [];
        foreach ($columns as $column) {
            if (isset($column['column']) && $column['column']['is_sortabel'] && $column['column']['column_type'] != 5 && $column['is_sort']) {
                $temp = array(
                    'column' => $column['column']['table'] . '.' . $column['column']['column'],
                    'by' => $column['sort_by']
                );
                array_push($result, $temp);
            }
        }

        return $result;
    }

    private function getGroupByColumns($columns, $m, $d)
    {
        $result = [];
        foreach ($columns as $column) {
            if (isset($column['column']) && $column['column']['is_group_by'] && $column['is_group_by']) {
                $table = $m;
                if (!$column['column']['is_master']) {
                    $table = $d;
                }
                $tmpColumn = $table . '.' . $column['column']['group_by_column'];
                array_push($result, $tmpColumn);
            }
        }
        return $result;
    }

    private function getFilterColumns($columns)
    {
        $result = [];
        foreach ($columns as $column) {
            if (isset($column['column']) && $column['column']['is_filter']) {
                $tmpColumn = $column['column']['table'] . '.' . $column['column']['filter_column'];
                $column['columnName'] = $tmpColumn;
                $column['column_type'] = $column['column']['column_type'];
                array_push($result, $column);
            }
        }
        return $result;
    }

    private function getSummarizeColumns($columns)
    {
        $result = [];
        foreach ($columns as $column) {
            if (isset($column['column']) && $column['column']['column_type'] == 4) {
                $tmpColumn = $column['column']['table'] . '.' . $column['column']['filter_column'];
                $column['columnName'] = $tmpColumn;
                $column['column_as'] =  $column['column']['column'];
                $column['column_type'] = $column['column']['column_type'];
                array_push($result, $column);
            }
        }
        return $result;
    }


    private function checkMasterColumn($columns, $value, $columnName)
    {
        foreach ($columns as $column) {
            if (isset($column['column']) && $column['column'][$columnName] == $value) {
                return true;
            }
        }
        return false;
    }

    public function customReportView(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'id' => 'required:numeric',
            'companyId' => 'required:numeric',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $limit = isset($input['limit']) ? $input['limit'] : 10;
        // return date("Y");
        DB::enableQueryLog();
        $result = $this->getCustomReportQry($request);
       

        if (!$result['success']) {
            return $this->sendError($result['message'], 500);
        }
       
        $data = $result['data'];

        if($data){
            $data = $data->paginate($limit);
        }

        if(isset($result['summarize']) && $result['summarize']){
            foreach ($result['summarize'] as $key => $summarize){

                $itemsTransformed = $data
                    ->getCollection()
                    ->toArray();
                 $decimalPlace = collect($itemsTransformed)->pluck($summarize['column'].'DecimalPlaces')->toArray();
                 $decimalPlace = array_unique($decimalPlace);
                 if(count($decimalPlace) == 1){
                     $decimalPlace = $decimalPlace[0];
                 }else{
                     $decimalPlace = 0;
                 }
                $result['summarize'][$key]['decimalPlaces'] = $decimalPlace;
            }
        }

        //dd(DB::getQueryLog());
        $output = array(
            'data' => $data,
            'report' => $result['report'],
            'summarize' => $result['summarize']
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.custom_report')]));

    }

    private function getCustomReportQry(Request $request)
    {
       
        $output = array(
            'success' => false,
            'message' => '',
            'data' => [],
            'report' => []
        );
        
        $input = $request->all();
        $report = $this->customUserReportsRepository->with(['columns' => function ($q) {
            $q->with(['column'])->orderBy('sort_order', 'asc');
        }, 'filter_columns' => function ($q) {
            $q->with(['column'])
                ->wherehas('column');
        },'summarize.column'])->find($input['id']);
        
        if (empty($report)) {
            $output['message'] = 'Report not found';
            return $output;
        }
       
        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        /* column_type
         * 1 - Text
         * 2 - Date
         * 3 -
         * 4 - Amount
         * 5 - Get Column as name, and for multiple column with same table join
         * 6 - Status
         */
       
        $primaryKey = '';
        $detailPrimaryKey = '';
        $masterTable = '';
        $detailTable = '';
        $summarize = [];

        // sort by columns
        $sortByColumns = $this->getSortByColumns($report['columns']);
        

        $isMasterExist = $this->checkMasterColumn($report['columns'], 1, 'is_master'); // 1 - master, 0 - details
        $isDetailExist = false ; // $this->checkMasterColumn($report['columns'], 0, 'is_master'); // 1 - master, 0 - details

        if($this->checkMasterColumn($report['columns'], 0, 'is_master') || $this->checkMasterColumn($report['filter_columns'], 0, 'is_master')){
            $isDetailExist = true;
        }
        
        $data = [];
        $templateData = array();
        
        if (isset($report['columns']) && count($report['columns']) > 0) {

            // select columns
            $columns = $this->geReportColumns($report['columns']);
            switch ($report->report_master_id) {
                case 1:
                    $masterTable = 'erp_expenseclaimmaster';
                    $detailTable = 'erp_expenseclaimdetails';
                    $primaryKey  = $masterTable . '.expenseClaimMasterAutoID';
                    $detailPrimaryKey = $detailTable . '.expenseClaimDetailsID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['canceledColumn']  = '';
                    $templateData['canceledValue']   =  0;
                    $templateData['timesReferredColumn'] = '';
                    $templateData['timesReferredValue'] = 0;
                    $templateData['tables'] = ['created_by', 'confirmed_by', 'currency', 'currency_local', 'currency_reporting', 'department', 'category', 'chartOfAccount'];
                    $templateData['statusColumns'] = ['confirmedYN', 'approved', 'documentSystemID', 'companySystemID'];
                    $templateData['model'] = 'ExpenseClaim';
                    break;
                case 10:
                    $masterTable = 'erp_purchaseordermaster';
                    $detailTable = 'erp_purchaseorderdetails';
                    $primaryKey  = $masterTable . '.purchaseOrderID';
                    $detailPrimaryKey = $detailTable . '.purchaseOrderDetailsID';
                    $templateData['confirmedColumn'] = 'poConfirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['canceledColumn']  = 'poCancelledYN';
                    $templateData['canceledValue']   =  -1;
                    $templateData['timesReferredColumn'] = 'refferedBackYN';
                    $templateData['timesReferredValue'] = -1;
                    $templateData['tables'] = ['created_by', 'confirmed_by','department','category','supplier','canceled_by','manually_closed_by',
                        'currency','currency_local', 'currency_reporting','unit','supplier_currency','supplier_country'];
                    $templateData['statusColumns'] = ['poConfirmedYN as confirmedYN', 'approved', 'documentSystemID',
                        'companySystemID','poCancelledYN as canceledYN','refferedBackYN'];
                    $templateData['model'] = 'ProcumentOrder';
                    $templateData['localCurrency'] = ['poTotalLocalCurrency','GRVcostPerUnitLocalCur'];
                    $templateData['rptCurrency'] = ['poTotalComRptCurrency','GRVcostPerUnitComRptCur'];
                    $templateData['transCurrency'] = ['poTotalSupplierTransactionCurrency','GRVcostPerUnitSupTransCur'];
                    break;
                case 9:  
                    $masterTable = 'erp_purchaserequest';
                    $detailTable = 'erp_purchaserequestdetails';
                    $primaryKey  = $masterTable . '.purchaseRequestID';
                    $detailPrimaryKey = $detailTable . '.purchaseRequestDetailsID';
                    $templateData['confirmedColumn'] = 'PRConfirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['canceledColumn']  = 'cancelledYN';
                    $templateData['canceledValue']   =  -1;
                    $templateData['timesReferredColumn'] = 'refferedBackYN';
                    $templateData['timesReferredValue'] = -1;
                    $templateData['tables'] = ['created_by', 'confirmed_by','department','category','supplier','canceled_by','manually_closed_by',
                        'currency_by','currency_local', 'currency_reporting','unit','supplier_currency','supplier_country','location'];
                    $templateData['statusColumns'] = ['PRConfirmedYN as confirmedYN', 'approved', 'documentSystemID',
                        'companySystemID','cancelledYN','refferedBackYN'];
                    $templateData['model'] = 'PurchaseRequest';
                    // $templateData['localCurrency'] = ['poTotalLocalCurrency','GRVcostPerUnitLocalCur'];
                    // $templateData['rptCurrency'] = ['poTotalComRptCurrency','GRVcostPerUnitComRptCur'];
                    // $templateData['transCurrency'] = ['poTotalSupplierTransactionCurrency','GRVcostPerUnitSupTransCur'];  
                    break;
                case 2:  
                    $masterTable = 'erp_bookinvsuppmaster';
                    $primaryKey  = $masterTable . '.bookingSuppMasInvAutoID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['canceledColumn']  = 'cancelYN';
                    $templateData['canceledValue']   =  -1;
                    $templateData['model'] = 'BookInvSuppMaster';
                    $templateData['tables'] = ['created_by','modified_by','confirmed_by','department','category','supplier','canceled_by','manually_closed_by',
                    'currency_by','currency_local', 'currency_reporting','localcurrency','transactioncurrency','supplier_country','location','approved_by','modified_by','company'];
                 
                    break;   
                case 3:  
                    $masterTable = 'erp_debitnote';
                    $detailTable = 'erp_debitnotedetails';
                    $primaryKey  = $masterTable . '.debitNoteAutoID';
                    $detailPrimaryKey = $detailTable . '.debitNoteDetailsID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'DebitNote';
                    $templateData['tables'] = ['rptcurrency','created_by','modified_by','confirmed_by','department','category','supplier','canceled_by','manually_closed_by',
                    'currency_by','currency_local', 'currency_reporting','localcurrency','transactioncurrency','supplier_country','location','approved_by','modified_by','company'];
                    
                    break;     
                case 4:  
                    $masterTable = 'erp_paysupplierinvoicemaster';
                    $detailTable = 'erp_paysupplierinvoicedetail';
                    $primaryKey  = $masterTable . '.PayMasterAutoId';
                    $detailPrimaryKey = $detailTable . '.payDetailAutoID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'PaySupplierInvoiceMaster';
                    $templateData['tables'] = ['supplierDefcurrency','suppliercurrency','bankcurrency','directPaycurrency','rptcurrency','created_by','modified_by','confirmed_by','department','category','supplier','canceled_by','manually_closed_by',
                    'currency_by','currency_local', 'currency_reporting','localcurrency','transactioncurrency','supplier_country','location','approved_by','modified_by','company','bank'];
                    
                    break;    
                case 5:  
                    $masterTable = 'erp_custinvoicedirect';
                    $detailTable = 'erp_custinvoicedirectdet';
                    $primaryKey  = $masterTable . '.custInvoiceDirectAutoID';
                    $detailPrimaryKey = $detailTable . '.custInvDirDetAutoID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'CustomerInvoiceDirect';
                    $templateData['tables'] = ['customer','warehouse','supplierDefcurrency','suppliercurrency','bankcurrency','transCurrency','reportCurrency','created_by','modified_by','confirmed_by','department','category','supplier','canceled_by','manually_closed_by',
                    'currency_by','currency_local', 'currency_reporting','local_currency','supplier_country','location','approve_by','modified_by','company','bank'];
                    
                    break;  
                case 6:  
                    $masterTable = 'erp_creditnote';
                    $detailTable = 'erp_creditnotedetails';
                    $primaryKey  = $masterTable . '.creditNoteAutoID';
                    $detailPrimaryKey = $detailTable . '.creditNoteDetailsID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'CreditNote';
                    $templateData['tables'] = ['customer','customer_currency','rpt_currency','created_by','modified_by','confirmed_by','supplier','canceled_by','manually_closed_by',
                    'currency_by','currency_local', 'currency_reporting','local_currency','supplier_country','location','approve_by','modified_by','company'];
                    
                    break;  
                case 7:  
                    $masterTable = 'erp_customerreceivepayment';
                    $detailTable = 'erp_custreceivepaymentdet';
                    $primaryKey  = $masterTable . '.custReceivePaymentAutoID';
                    $detailPrimaryKey = $detailTable . '.custRecivePayDetAutoID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'CustomerReceivePayment';
                    $templateData['tables'] = ['customer','customer_tran_currency','rpt_currency','created_by','modified_by','confirmed_by','supplier','canceled_by','manually_closed_by',
                    'currency_by','currency_local', 'currency_reporting','local_currency','payee_currency','location','approve_by','bank','bank_currency'];
                    
                    break;  
                case 8:  
                    $masterTable = 'erp_grvmaster';
                    $detailTable = 'erp_grvdetails';
                    $primaryKey  = $masterTable . '.grvAutoID';
                    $detailPrimaryKey = $detailTable . '.grvDetailsID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'GRVMaster';
                    $templateData['tables'] = ['company','customer','customer_tran_currency','rpt_currency','created_by','modified_by','confirmed_by','supplier','canceled_by','manually_closed_by',
                    'currency_by','currency_local', 'sup_def_currency','local_currency','sup_tra_currency','warehouse','approved_by','bank','bank_currency'];
                    
                    break;  

                case 12:  
                    $masterTable = 'erp_request';
                    $detailTable = 'erp_requestdetails';
                    $primaryKey  = $masterTable . '.RequestID';
                    $detailPrimaryKey = $detailTable . '.RequestDetailsID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'MaterielRequest';
                    $templateData['tables'] = ['company','customer','priority','created_by','approved_by',
                    'location_by'];
                    $templateData['statusColumns'] = ['approved'];
                    break; 

                case 13:  
                    $masterTable = 'erp_itemissuemaster';
                    $detailTable = 'erp_itemissuedetails';
                    $primaryKey  = $masterTable . '.itemIssueAutoID';
                    $detailPrimaryKey = $detailTable . '.itemIssueDetailID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'ItemIssueMaster';
                    $templateData['tables'] = ['company','customer','created_by','approved_by',
                    'warehouse'];
                    $templateData['statusColumns'] = ['approved'];
                    break; 
                    
                case 14:  
                    $masterTable = 'erp_itemreturnmaster';
                    $detailTable = 'erp_itemreturndetails';
                    $primaryKey  = $masterTable . '.itemReturnAutoID';
                    $detailPrimaryKey = $detailTable . '.itemReturnDetailID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'ItemReturnMaster';
                    $templateData['tables'] = ['company','customer','created_by','approved_by',
                    'warehouse'];
                    $templateData['statusColumns'] = ['approved','refferedBackYN','confirmedYN'];
                    break; 
                case 15:  
                    $masterTable = 'erp_stocktransfer';
                    $detailTable = 'erp_stocktransferdetails';
                    $primaryKey  = $masterTable . '.stockTransferAutoID';
                    $detailPrimaryKey = $detailTable . '.stockTransferDetailsID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'StockTransfer';
                    $templateData['tables'] = ['company','created_by','approved_by',
                    'warehouse','location_to','location_from','segment'];
                    $templateData['statusColumns'] = ['approved','refferedBackYN','confirmedYN'];
                    break; 
                case 16:  
                    $masterTable = 'erp_stockreceive';
                    $detailTable = 'erp_stockreceivedetails';
                    $primaryKey  = $masterTable . '.stockReceiveAutoID';
                    $detailPrimaryKey = $detailTable . '.stockReceiveDetailsID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'StockReceive';
                    $templateData['tables'] = ['company','created_by','approved_by',
                    'warehouse','location_to','location_from','segment'];
                    $templateData['statusColumns'] = ['approved','refferedBackYN','confirmedYN'];
                    break; 
                case 17:  
                    $masterTable = 'erp_stockadjustment';
                    $detailTable = 'erp_stockadjustmentdetails';
                    $primaryKey  = $masterTable . '.stockAdjustmentAutoID';
                    $detailPrimaryKey = $detailTable . '.stockAdjustmentDetailsAutoID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'StockAdjustment';
                    $templateData['tables'] = ['company','created_by','approved_by',
                    'warehouse','location','reason','segment'];
                    $templateData['statusColumns'] = ['approved','refferedBackYN','confirmedYN'];
                    break; 
                case 18:  
                    $masterTable = 'erp_purchasereturnmaster';
                    $detailTable = 'erp_purchasereturndetails';
                    $primaryKey  = $masterTable . '.purhaseReturnAutoID';
                    $detailPrimaryKey = $detailTable . '.purhasereturnDetailID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'PurchaseReturn';
                    $templateData['tables'] = ['company','created_by','approved_by',
                    'warehouse','location','supplier_default_currency','supplier_tran_currency','company_reporting_currency','local_currency','segment'];
                    $templateData['statusColumns'] = ['approved','refferedBackYN','confirmedYN'];
                    break; 
                case 19:  
                    $masterTable = 'erp_stockcount';
                    $detailTable = 'erp_stock_count_details';
                    $primaryKey  = $masterTable . '.stockCountAutoID';
                    $detailPrimaryKey = $detailTable . '.stockCountDetailsAutoID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'StockCount';
                    $templateData['tables'] = ['company','created_by','approved_by',
                    'warehouse','location','segment'];
                    $templateData['statusColumns'] = ['approved','refferedBackYN','confirmedYN'];
                    break; 
                case 20:  
                    $masterTable = 'erp_inventoryreclassification';
                    $detailTable = 'erp_inventoryreclassificationdetail';
                    $primaryKey  = $masterTable . '.inventoryreclassificationID';
                    $detailPrimaryKey = $detailTable . '.inventoryReclassificationDetailID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'InventoryReclassification';
                    $templateData['tables'] = ['company','created_by','approved_by',
                    'wareHouse','location','modify_by'];
                    $templateData['statusColumns'] = ['approved','confirmedYN'];
                    break; 
                case 21:  
                    $masterTable = 'erp_quotationmaster';
                    $detailTable = 'erp_quotationdetails';
                    $primaryKey  = $masterTable . '.quotationMasterID';
                    $detailPrimaryKey = $detailTable . '.quotationDetailsID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approvedYN';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'QuotationMaster';
                    $templateData['tables'] = ['company','created_by','sales_person',
                    'segment'];
                    $templateData['statusColumns'] = ['approvedYN'];
                    break; 
                case 22:  
                    $masterTable = 'erp_quotationmaster';
                    $detailTable = 'erp_quotationdetails';
                    $primaryKey  = $masterTable . '.quotationMasterID';
                    $detailPrimaryKey = $detailTable . '.quotationDetailsID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approvedYN';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'QuotationMaster';
                    $templateData['tables'] = ['company','created_by','sales_person',
                    'segment'];
                    $templateData['statusColumns'] = ['approvedYN'];
                    break; 
                case 23:  
                    $masterTable = 'erp_delivery_order';
                    $detailTable = 'erp_delivery_order_detail';
                    $primaryKey  = $masterTable . '.deliveryOrderID';
                    $detailPrimaryKey = $detailTable . '.deliveryOrderDetailID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approvedYN';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'DeliveryOrder';
                    $templateData['tables'] = ['company','created_by','sales_person',
                    'segment','customer','tran_currency','tran_currency_er','local_currency','local_currency_ET','reporting_currency','reporting_currency_ET','wareHouse'];
                    $templateData['statusColumns'] = ['approvedYN'];
                    break; 
                case 24:  
                    $masterTable = 'erp_jvmaster';
                    $detailTable = 'erp_jvdetail';
                    $primaryKey  = $masterTable . '.jvMasterAutoId';
                    $detailPrimaryKey = $detailTable . '.jvDetailAutoID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'JvMaster';
                    $templateData['tables'] = ['company','created_by','currency',
                    'currency_rpt'];
                    $templateData['statusColumns'] = ['approved'];
                    break; 
                case 25:  
                    $masterTable = 'erp_budgetmaster';
                    $detailTable = 'erp_budjetdetails_history';
                    $primaryKey  = $masterTable . '.budgetmasterID';
                    $detailPrimaryKey = $detailTable . '.budjetDetailsID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approvedYN';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'BudgetMaster';
                    $templateData['tables'] = ['company','created_by','template',
                    'confirm_by','approved_by','segment'];
                    $templateData['statusColumns'] = ['approvedYN'];
                    break; 
                case 26:  
                    $masterTable = 'erp_budgettransferform';
                    $detailTable = 'erp_budgettransferformdetail';
                    $primaryKey  = $masterTable . '.budgetTransferFormAutoID';
                    $detailPrimaryKey = $detailTable . '.budgetTransferFormDetailAutoID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approvedYN';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'BudgetTransferForm';
                    $templateData['tables'] = ['company','created_by',
                    'approved_by'];
                    $templateData['statusColumns'] = ['approvedYN'];
                    break; 
                case 27:  
                    $masterTable = 'erp_budgetaddition';
                    $detailTable = 'erp_budgetadditiondetail';
                    $primaryKey  = $masterTable . '.id';
                    $detailPrimaryKey = $detailTable . '.id';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approvedYN';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'ErpBudgetAddition';
                    $templateData['tables'] = ['company','created_by',
                    'approved_by','template'];
                    $templateData['statusColumns'] = ['approvedYN'];
                    break; 
                case 28:  
                    $masterTable = 'erp_consolejvmaster';
                    $detailTable = 'erp_consolejvdetail';
                    $primaryKey  = $masterTable . '.consoleJvMasterAutoId';
                    $detailPrimaryKey = $detailTable . '.consoleJvDetailAutoID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'ConsoleJVMaster';
                    $templateData['tables'] = ['company','created_by',
                    'approved_by','currency','local_currency','rpt_currency'];
                    $templateData['statusColumns'] = ['approved'];
                    break; 
                case 29:  
                    $masterTable = 'erp_budget_contingency';
                    $primaryKey  = $masterTable . '.ID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approvedYN';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'ContingencyBudgetPlan';
                    $templateData['tables'] = ['company','created_by',
                    'approved_by','currency','confirmed_by','segment','modified_by','template'];
                    $templateData['statusColumns'] = ['approvedYN'];
                    break; 
                case 30:  
                    $masterTable = 'erp_fa_asset_master';
                    $primaryKey  = $masterTable . '.faID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approvedYN';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'FixedAssetMaster';
                    $templateData['tables'] = ['company','created_by',
                    'approved_by','doc_origin_detail','confirmed_by','segment','modified_by','depratment','fa_cat','fa_cat_sub','fa_cat_sub_2','fa_cat_sub_3','finance_cat','location','asset_type'];
                    $templateData['statusColumns'] = ['approvedYN'];
                    break; 
                case 31:  
                    $masterTable = 'erp_fa_depmaster';
                    $primaryKey  = $masterTable . '.depMasterAutoID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'FixedAssetDepreciationMaster';
                    $templateData['tables'] = ['company','rpt_currency',
                    'approved_by','local_currency','created_by'];
                    $templateData['statusColumns'] = ['approved'];
                    break; 
                case 32:  
                    $masterTable = 'erp_fa_asset_disposalmaster';
                    $primaryKey  = $masterTable . '.assetdisposalMasterAutoID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approvedYN';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'AssetDisposalMaster';
                    $templateData['tables'] = ['company','company_to','customer',
                    'approved_by','dis_type','created_by','modified_by'];
                    $templateData['statusColumns'] = ['approvedYN'];
                    break; 
                case 33:  
                    $masterTable = 'erp_fa_assetcapitalization';
                    $detailTable = 'erp_fa_assetcapitalization_detail';
                    $primaryKey  = $masterTable . '.capitalizationID';
                    $detailPrimaryKey = $detailTable . '.capitalizationDetailID';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'AssetCapitalization';
                    $templateData['tables'] = ['company','fa_cat','asset',
                    'approved_by','chart_acc','created_by','modified_by','confirmed_by'];
                    $templateData['statusColumns'] = ['approved'];
                    break; 
                case 34:  
                    $masterTable = 'erp_fa_asset_verification';
                    $detailTable = 'erp_fa_asset_verificationdetails';
                    $primaryKey  = $masterTable . '.id';
                    $detailPrimaryKey = $detailTable . '.id';
                    $templateData['confirmedColumn'] = 'confirmedYN';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'AssetVerification';
                    $templateData['tables'] = ['company',
                    'approved_by','created_by','modified_by','confirmed_by'];
                    $templateData['statusColumns'] = ['approved'];
                    break; 
                case 35:  
                    $masterTable = 'erp_fa_fa_asset_transfer';
                    $detailTable = 'erp_fa_fa_asset_transfer_details';
                    $primaryKey  = $masterTable . '.id';
                    $detailPrimaryKey = $detailTable . '.id';
                    $templateData['confirmedColumn'] = 'confirmed_yn';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved_yn';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'ERPAssetTransfer';
                    $templateData['tables'] = ['location',
                    'approved_by','created_by','modified_by','confirmed_by'];
                    $templateData['statusColumns'] = ['approved_yn'];
                    break; 
                case 36:  
                    $masterTable = 'erp_fa_fa_asset_request';
                    $detailTable = 'erp_fa_fa_asset_request_details';
                    $primaryKey  = $masterTable . '.id';
                    $detailPrimaryKey = $detailTable . '.id';
                    $templateData['confirmedColumn'] = 'confirmed_yn';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = 'approved_yn';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'AssetRequest';
                    $templateData['tables'] = [''];
                    $templateData['statusColumns'] = ['approved_yn'];
                    break; 
                case 37:  
                    $masterTable = 'erp_generalledger';
                    $primaryKey  = $masterTable . '.GeneralLedgerID';
                    $templateData['confirmedColumn'] = '';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = '';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'GeneralLedger';
                    $templateData['tables'] = ['created_by','rpt_currency','local_currency','doc_currency','document_approved_by','document_confirm_by','chart_acc','company_master','segment','company'];
                    $templateData['statusColumns'] = [''];
                    break; 
                case 38:  
                    $masterTable = 'erp_itemledger';
                    $primaryKey  = $masterTable . '.itemLedgerAutoID';
                    $templateData['confirmedColumn'] = '';
                    $templateData['confirmedValue']  = 1;
                    $templateData['approvedColumn']  = '';
                    $templateData['approvedValue']   = -1;
                    $templateData['model'] = 'ErpItemLedger';
                    $templateData['tables'] = ['created_by','rpt_currency','local_currency','doc_currency','unit','warehouse','segment','company'];
                    $templateData['statusColumns'] = [''];
                    break; 
                default;
                    break;
            }
            
            if ($this->checkMasterColumn($report['columns'], 6, 'column_type')) {
                if(isset($templateData['statusColumns']) && !empty($templateData['statusColumns']))
                {
                    foreach ($templateData['statusColumns'] as $column) {
                        array_push($columns, $masterTable . '.' . $column);
                    }
                }
            }
          
            if ($isMasterExist) {
                array_push($columns, $primaryKey . ' as masterId');
            }
           
            if ($isDetailExist) {
                array_push($columns, $detailPrimaryKey . ' as detailId');
            }
            $namespacedModel = 'App\Models\\' . $templateData['model'];
            $data = $namespacedModel::selectRaw(implode(",", $columns));
            
            // join tables
            switch ($report->report_master_id) {
                case 1:
                    // details table
                    if ($isDetailExist) {
                        $data->detailJoin();
                    }

                    foreach ($templateData['tables'] as $table) {
                        if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                            if ($table == 'created_by') {
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                            } else if ($table == 'confirmed_by') {
                                $data->employeeJoin('confirmed_by', 'confirmedByEmpSystemID', 'confirmedByName');
                            }else if ($table == 'currency') {
                                $data->currencyJoin('currency', 'currencyID', 'currencyCode', 'amount');
                            } else if ($table == 'currency_local') {
                                $data->currencyJoin('currency_local', 'localCurrency', 'localCurrencyCode', 'localAmount');
                            } else if ($table == 'currency_reporting') {
                                $data->currencyJoin('currency_reporting', 'comRptCurrency', 'rptCurrencyCode', 'comRptAmount');
                            } else if ($table == 'department') {
                                $data->departmentJoin('department', 'serviceLineSystemID', 'ServiceLineDes');
                            } else if ($table == 'category') {
                                $data->categoryJoin('category', 'expenseClaimCategoriesAutoID', 'claimcategoriesDescription');
                            } else if ($table == 'chartOfAccount') {
                                $data->chartOfAccountJoin('chartOfAccount', 'chartOfAccountSystemID', 'AccountCode');
                            }
                        }
                    }

                    if (!$this->checkMasterColumn($report['columns'], 'currency', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'currency', 'table') && ($this->checkMasterColumn($report['columns'], 'amount', 'column'))) {
                        $data->currencyJoin('currency', 'currencyID', 'currencyCode', 'amount');
                    }

                    if (!$this->checkMasterColumn($report['columns'], 'currency_local', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'currency_local', 'table') && $this->checkMasterColumn($report['columns'], 'localAmount', 'column')) {
                        $data->currencyJoin('currency_local', 'localCurrency', 'localCurrencyCode', 'localAmount');
                    }

                    if (!$this->checkMasterColumn($report['columns'], 'currency_reporting', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'currency_reporting', 'table') && $this->checkMasterColumn($report['columns'], 'comRptAmount', 'column')) {
                        $data->currencyJoin('currency_reporting', 'comRptCurrency', 'rptCurrencyCode', 'comRptAmount');
                    }

                    $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                    break;
                case 10:
                    // details table
                    if ($isDetailExist) {
                        $data->detailJoin();
                    }


                    if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                    }

                    foreach ($templateData['tables'] as $table) {
                        if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                            if ($table == 'created_by') {
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                            } else if ($table == 'confirmed_by') {
                                $data->employeeJoin('confirmed_by', 'poConfirmedByEmpSystemID', 'confirmedByName');
                            } else if ($table == 'canceled_by') {
                                $data->employeeJoin('canceled_by', 'poCancelledBySystemID', 'canceledByName');
                            } else if ($table == 'manually_closed_by') {
                                $data->employeeJoin('manually_closed_by', 'manuallyClosedByEmpSystemID', 'manuallyClosedByName');
                            } else if ($table == 'department') {
                                $data->departmentJoin('department', 'serviceLineSystemID', 'ServiceLineDes');
                            } else if ($table == 'category') {
                                $data->categoryJoin('category', 'financeCategory', 'claimcategoriesDescription');
                            }else if($table == 'supplier'){
                                $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                            } else if ($table == 'unit') {
                                $data->unitJoin('unit', 'unitOfMeasure', 'UnitShortCode');
                            }else if ($table == 'currency') {
                                $data->currencyJoin('currency', 'supplierTransactionCurrencyID', 'currencyCode', $templateData['transCurrency']);
                            } else if ($table == 'currency_local') {
                                $data->currencyJoin('currency_local', 'localCurrencyID', 'localCurrencyCode', $templateData['localCurrency']);
                            } else if ($table == 'currency_reporting') {
                                $data->currencyJoin('currency_reporting', 'companyReportingCurrencyID', 'rptCurrencyCode', $templateData['rptCurrency']);
                            } else if ($table == 'supplier_currency') {
                                $data->supplierCurrencyJoin('supplier_currency', 'currency', 'supplierCurrency,', 'poTotalComRptCurrency');
                            } else if ($table == 'supplier_country') {
                                $data->supplierCountryJoin('supplier_country', 'countryID', 'countryName');
                            }
                        }
                    }

                    if (!$this->checkMasterColumn($report['columns'], 'currency', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'currency', 'table') && ($this->checkMasterColumn($report['columns'], 'poTotalSupplierTransactionCurrency', 'column')
                            || $this->checkMasterColumn($report['columns'], 'GRVcostPerUnitSupTransCur', 'column'))) {
                        $data->currencyJoin('currency', 'supplierTransactionCurrencyID', 'currencyCode', $templateData['transCurrency']);
                    }

                    if (!$this->checkMasterColumn($report['columns'], 'currency_local', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'currency_local', 'table') && ($this->checkMasterColumn($report['columns'], 'poTotalLocalCurrency', 'column')
                        || $this->checkMasterColumn($report['columns'], 'GRVcostPerUnitLocalCur', 'column'))) {
                        $data->currencyJoin('currency_local', 'localCurrencyID', 'localCurrencyCode', $templateData['localCurrency']);
                    }

                    if (!$this->checkMasterColumn($report['columns'], 'currency_reporting', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'currency_reporting', 'table') && ($this->checkMasterColumn($report['columns'], 'poTotalComRptCurrency', 'column')
                        || $this->checkMasterColumn($report['columns'], 'GRVcostPerUnitComRptCur', 'column'))) {
                        $data->currencyJoin('currency_reporting', 'companyReportingCurrencyID', 'rptCurrencyCode', $templateData['rptCurrency']);
                    }

                    $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                    break;
                case 9:
                    // details table
                    if ($isDetailExist) {
                    $data->detailJoin();
                    }
                    
                        foreach ($templateData['tables'] as $table) {
                        if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                            if ($table == 'created_by') {
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                            } else if ($table == 'confirmed_by') {
                                $data->employeeJoin('confirmed_by', 'PRConfirmedBySystemID', 'confirmedByName');
                            } else if ($table == 'canceled_by') {
                                $data->employeeJoin('canceled_by', 'cancelledByEmpSystemID', 'canceledByName');
                            } 
                            else if ($table == 'manually_closed_by') {
                                $data->employeeJoin('manually_closed_by', 'manuallyClosedByEmpSystemID', 'manuallyClosedByName');
                            } else if ($table == 'department') {
                                $data->departmentJoin('department', 'serviceLineSystemID', 'ServiceLineDes');
                            }else if ($table == 'category') {
                                $data->categoryJoin('category', 'financeCategory', 'categoryDescription');
                            }else if($table == 'supplier'){
                                $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                            }else if ($table == 'currency_by') {
                                $data->currencyJoin('currency_by', 'currency', 'currencyByName');
                            }else if ($table == 'location') {
                                $data->locationJoin('location', 'location', 'locationByName');
                            }  
                        }
                    }
                    $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                    break;
                case 2:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                      }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                   
                                    $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                 }
                                 else if ($table == 'transactioncurrency') {
                                    
                                    $data->currencyJoin('transactioncurrency', 'supplierTransactionCurrencyID', 'CurrencyName');
                                } 
                                else if($table == 'supplier'){
                                    $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                                } 
                                  else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'localcurrency') {
                                    $data->currencyJoin('localcurrency', 'localCurrencyID', 'CurrencyName');
                                } 
                                else if ($table == 'approved_by') {
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                              
                            }
                        }
                    $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                    break;
                case 3:
                        if ($isDetailExist) {
                            $data->detailJoin();
                            }
    
                            if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                            ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                                $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                            $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                          }
                        
                            foreach ($templateData['tables'] as $table) {
                                if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                    if ($table == 'created_by') {
                                       
                                        $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                     }
                                     else if ($table == 'transactioncurrency') {
                                        
                                        $data->currencyJoin('transactioncurrency', 'supplierTransactionCurrencyID', 'CurrencyName');
                                    } 
                                    else if ($table == 'rptcurrency') {
                                        
                                        $data->currencyJoin('rptcurrency', 'companyReportingCurrencyID', 'CurrencyName');
                                    }
                                    else if($table == 'supplier'){
                                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                                    } 
                                      else if ($table == 'company') {
                                        $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                    } 
                                    else if ($table == 'localcurrency') {
                                        $data->currencyJoin('localcurrency', 'localCurrencyID', 'CurrencyName');
                                    } 
                                    else if ($table == 'approved_by') {
                                        $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                    } 
                                  
                                }
                            }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;

                case 4:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                    $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                    }
                                    else if ($table == 'transactioncurrency') {
                                    
                                    $data->currencyJoin('transactioncurrency', 'supplierTransactionCurrencyID', 'CurrencyName');
                                } 
                                else if ($table == 'rptcurrency') {
                                    
                                    $data->currencyJoin('rptcurrency', 'companyRptCurrencyID', 'CurrencyName');
                                }
                                else if ($table == 'directPaycurrency') {
                                    
                                    $data->currencyJoin('directPaycurrency', 'directPayeeCurrency', 'CurrencyName');
                                }
                                else if ($table == 'bankcurrency') {
                                    
                                    $data->currencyJoin('bankcurrency', 'BPVbankCurrency', 'CurrencyName');
                                }
                                else if ($table == 'supplierDefcurrency') {
                                    
                                    $data->currencyJoin('supplierDefcurrency', 'supplierDefCurrencyID', 'CurrencyName');
                                }
                                else if ($table == 'suppliercurrency') {
                                    
                                    $data->currencyJoin('suppliercurrency', 'supplierTransCurrencyID', 'CurrencyName');
                                }
                                else if($table == 'supplier'){
                                    $data->supplierJoin('supplier', 'BPVsupplierID', 'primarySupplierCode');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'localcurrency') {
                                    $data->currencyJoin('localcurrency', 'localCurrencyID', 'CurrencyName');
                                } 
                                else if ($table == 'approved_by') {
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'bank') {
                                    $data->bankJoin('bank', 'BPVbank', 'bankName');
                                } 
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                        
                case 5:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                    $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                    }
                                    else if ($table == 'transCurrency') {
                                    
                                    $data->currencyJoin('transCurrency', 'custTransactionCurrencyID', 'CurrencyName');
                                } 
                                else if ($table == 'reportCurrency') {
                                    
                                    $data->currencyJoin('reportCurrency', 'companyReportingCurrencyID', 'CurrencyName');
                                }
                                
                                else if ($table == 'local_currency') {
                                    
                                    $data->currencyJoin('local_currency', 'localCurrencyID', 'CurrencyName');
                                }
                                else if ($table == 'supplierDefcurrency') {
                                    
                                    $data->currencyJoin('supplierDefcurrency', 'supplierDefCurrencyID', 'CurrencyName');
                                }
                                else if ($table == 'suppliercurrency') {
                                    
                                    $data->currencyJoin('suppliercurrency', 'supplierTransCurrencyID', 'CurrencyName');
                                }
                                else if($table == 'supplier'){
                                    $data->supplierJoin('supplier', 'BPVsupplierID', 'primarySupplierCode');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'localcurrency') {
                                    $data->currencyJoin('localcurrency', 'localCurrencyID', 'CurrencyName');
                                } 
                                else if ($table == 'approve_by') {
                                    $data->employeeJoin('approve_by', 'approvedByUserID', 'createdByName');
                                } 
                                else if ($table == 'bank') {
                                    $data->bankJoin('bank', 'bankID', 'bankName');
                                } 
                                else if ($table == 'warehouse') {
                                    $data->wareHouseJoin('warehouse', 'wareHouseSystemCode', 'wareHouseDescription');
                                } 
                                else if ($table == 'customer') {
                                    $data->customerJoin('customer', 'customerID', 'CustomerName');
                                } 
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 6:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'customer_currency') {
                                    
                                    $data->currencyJoin('customer_currency', 'customerCurrencyID', 'CurrencyName');
                                } 
                                else if ($table == 'rpt_currency') {
                                    
                                    $data->currencyJoin('rpt_currency', 'companyReportingCurrencyID', 'CurrencyName');
                                }
                                
                                else if ($table == 'local_currency') {
                                    
                                    $data->currencyJoin('local_currency', 'localCurrencyID', 'CurrencyName');
                                }
                                
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'approve_by') {
                                    $data->employeeJoin('approve_by', 'approvedByUserID', 'createdByName');
                                } 
                                else if ($table == 'created_by') {
                                    $data->employeeJoin('approve_by', 'createdUserSystemID', 'createdByName');
                                }  
                                else if ($table == 'customer') {
                                    $data->customerJoin('customer', 'customerID', 'CustomerName');
                                } 
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 7:
                        if ($isDetailExist) {
                            $data->detailJoin();
                            }
    
                            if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                            ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                                $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                            $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                            }
                        
                            foreach ($templateData['tables'] as $table) {
                                if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                    if ($table == 'created_by') {
                                        
                                    $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                    }
                                    else if ($table == 'customer_tran_currency') {
                                        
                                        $data->currencyJoin('customer_tran_currency', 'custTransactionCurrencyID', 'CurrencyName');
                                    } 
                                    else if ($table == 'rpt_currency') {
                                        
                                        $data->currencyJoin('rpt_currency', 'companyRptCurrencyID', 'CurrencyName');
                                    }
                                    
                                    else if ($table == 'local_currency') {
                                        
                                        $data->currencyJoin('local_currency', 'localCurrencyID', 'CurrencyName');
                                    }
                                    else if ($table == 'bank_currency') {
                                        
                                        $data->currencyJoin('bank_currency', 'bankCurrency', 'CurrencyName');
                                    }
                                    else if ($table == 'payee_currency') {
                                        
                                        $data->currencyJoin('payee_currency', 'PayeeCurrency', 'CurrencyName');
                                    }
                                    else if ($table == 'company') {
                                        $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                    } 
                                    else if ($table == 'approve_by') {
                                        $data->employeeJoin('approve_by', 'approvedByUserID', 'createdByName');
                                    } 
                                    else if ($table == 'created_by') {
                                        $data->employeeJoin('approve_by', 'createdUserSystemID', 'createdByName');
                                    }  
                                    else if ($table == 'customer') {
                                        $data->customerJoin('customer', 'customerID', 'CustomerName');
                                    } 
                                    else if ($table == 'bank') {
                                        $data->bankJoin('bank', 'bankID', 'bankName');
                                    }  
                                }
                            }
                            $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                            break;
                case 8:
                            if ($isDetailExist) {
                                $data->detailJoin();
                                }

                                if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                                ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                                    $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                                $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                                }
                            
                                foreach ($templateData['tables'] as $table) {
                                    if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                        if ($table == 'created_by') {
                                            
                                        $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                        }
                                        else if ($table == 'customer_tran_currency') {
                                            
                                            $data->currencyJoin('customer_tran_currency', 'custTransactionCurrencyID', 'CurrencyName');
                                        } 
                                        else if ($table == 'rpt_currency') {
                                            
                                            $data->currencyJoin('rpt_currency', 'companyReportingCurrencyID', 'CurrencyName');
                                        }
                                        
                                        else if ($table == 'local_currency') {
                                            
                                            $data->currencyJoin('local_currency', 'localCurrencyID', 'CurrencyName');
                                        }
                                        else if ($table == 'sup_tra_currency') {
                                            
                                            $data->currencyJoin('sup_tra_currency', 'supplierTransactionCurrencyID', 'CurrencyName');
                                        }
                                        else if ($table == 'sup_def_currency') {
                                            
                                            $data->currencyJoin('sup_def_currency', 'supplierDefaultCurrencyID', 'CurrencyName');
                                        }
                                        else if ($table == 'company') {
                                            $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                        } 
                                        else if ($table == 'approved_by') {
                                            $data->employeeJoin('approved_by', 'approvedByUserID', 'createdByName');
                                        } 
                                        else if ($table == 'created_by') {
                                            $data->employeeJoin('approve_by', 'createdUserSystemID', 'createdByName');
                                        } 
                                        else if ($table == 'warehouse') {
                                            $data->wareHouseJoin('warehouse', 'grvLocation', 'wareHouseDescription');
                                        }
                                        else if($table == 'supplier'){
                                            $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                                        } 
                                    }
                                }
                                $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                                break;

                case 12:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'approved_by') {
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'location_by') {
                                    $data->locationJoin('location_by', 'location', 'locationName');
                                } 
                                else if ($table == 'priority') {
                                    $data->priorityJoin('priority', 'priority', 'priorityDescription');
                                } 
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 13:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'approved_by') {
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'customer') {
                                    $data->customerJoin('customer', 'customerSystemID', 'CustomerName');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'warehouse') {
                                    $data->wareHouseJoin('warehouse', 'wareHouseFrom', 'wareHouseDescription');
                                }
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 14:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'approved_by') {
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'customer') {
                                    $data->customerJoin('customer', 'customerID', 'CustomerName');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'warehouse') {
                                    $data->wareHouseJoin('warehouse', 'wareHouseLocation', 'wareHouseDescription');
                                }
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 15:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'approved_by') {
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'segment') {
                                    $data->segmentJoin('segment', 'serviceLineSystemID', 'ServiceLineDes');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'location_to') {
                                    $data->wareHouseJoin('location_to', 'locationTo', 'wareHouseDescription');
                                }
                                else if ($table == 'location_from') {
                                    $data->wareHouseJoin('location_from', 'locationFrom', 'wareHouseDescription');
                                }
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 16:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'approved_by') {
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'segment') {
                                    $data->segmentJoin('segment', 'serviceLineSystemID', 'ServiceLineDes');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'location_to') {
                                    $data->wareHouseJoin('location_to', 'locationTo', 'wareHouseDescription');
                                }
                                else if ($table == 'location_from') {
                                    $data->wareHouseJoin('location_from', 'locationFrom', 'wareHouseDescription');
                                }
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 17:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'approved_by') {
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'segment') {
                                    $data->segmentJoin('segment', 'serviceLineSystemID', 'ServiceLineDes');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'location') {
                                    $data->wareHouseJoin('location', 'location', 'wareHouseDescription');
                                }
                                else if ($table == 'reason') {
                                    $data->reasonJoin('reason', 'reason', 'reason');
                                }
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;   
                case 18:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'approved_by') {
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'segment') {
                                    $data->segmentJoin('segment', 'serviceLineSystemID', 'ServiceLineDes');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'location') {
                                    $data->wareHouseJoin('location', 'purchaseReturnLocation', 'wareHouseDescription');
                                }
                                else if ($table == 'supplier_default_currency') {   
                                    $data->currencyJoin('supplier_default_currency', 'supplierDefaultCurrencyID', 'CurrencyName');
                                } 
                                else if ($table == 'supplier_tran_currency') { 
                                    $data->currencyJoin('supplier_tran_currency', 'supplierTransactionCurrencyID', 'CurrencyName');
                                } 
                                else if ($table == 'local_currency') {   
                                    $data->currencyJoin('local_currency', 'localCurrencyID', 'CurrencyName');
                                } 
                                else if ($table == 'company_reporting_currency') {   
                                    $data->currencyJoin('company_reporting_currency', 'companyReportingCurrencyID', 'CurrencyName');
                                } 
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;   
                case 19:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'approved_by') {
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'segment') {
                                    $data->segmentJoin('segment', 'serviceLineSystemID', 'ServiceLineDes');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'location') {
                                    $data->wareHouseJoin('location', 'location', 'wareHouseDescription');
                                }
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;   
                case 20:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'approved_by') {
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'modify_by') {
                                    $data->employeeJoin('modify_by', 'modifiedUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'wareHouse') {
                                    $data->wareHouseJoin('wareHouse', 'wareHouseCode', 'wareHouseDescription');
                                }
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;  

                case 21:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'sales_person') {
                                    $data->salesPersonJoin('sales_person', 'salesPersonID', 'SalesPersonName');
                                } 
                                else if ($table == 'segment') {
                                    $data->segmentJoin('segment', 'serviceLineSystemID', 'ServiceLineDes');
                                }
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        $data->where($masterTable . '.documentSystemID', 67);
                        break;  
                case 22:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'sales_person') {
                                    $data->salesPersonJoin('sales_person', 'salesPersonID', 'SalesPersonName');
                                } 
                                else if ($table == 'segment') {
                                    $data->segmentJoin('segment', 'serviceLineSystemID', 'ServiceLineDes');
                                }
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        $data->where($masterTable . '.documentSystemID', 68);
                        break;  
                case 23:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'sales_person') {
                                    $data->salesPersonJoin('sales_person', 'salesPersonID', 'SalesPersonName');
                                } 
                                else if ($table == 'segment') {
                                    $data->segmentJoin('segment', 'serviceLineSystemID', 'ServiceLineDes');
                                }
                                else if ($table == 'wareHouse') {
                                    $data->wareHouseJoin('wareHouse', 'wareHouseSystemCode', 'wareHouseDescription');
                                }
                                else if ($table == 'tran_currency') {   
                                    $data->currencyJoin('tran_currency', 'transactionCurrencyID', 'CurrencyName');
                                } 
                                else if ($table == 'tran_currency_er') {   
                                    $data->currencyJoin('tran_currency_er', 'transactionCurrencyER', 'CurrencyName');
                                } 
                                else if ($table == 'local_currency') {   
                                    $data->currencyJoin('local_currency', 'companyLocalCurrencyID', 'CurrencyName');
                                } 
                                else if ($table == 'local_currency_ET') {   
                                    $data->currencyJoin('local_currency_ET', 'companyLocalCurrencyER', 'CurrencyName');
                                } 
                                else if ($table == 'reporting_currency') {   
                                    $data->currencyJoin('reporting_currency', 'companyReportingCurrencyID', 'CurrencyName');
                                } 
                                else if ($table == 'reporting_currency_ET') {   
                                    $data->currencyJoin('reporting_currency_ET', 'companyReportingCurrencyER', 'CurrencyName');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'customer') {
                                    $data->customerJoin('customer', 'customerID', 'CustomerName');
                                } 
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;              
                case 24:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'currency') {   
                                    $data->currencyJoin('currency', 'currencyID', 'CurrencyName');
                                } 
                                else if ($table == 'currency_rpt') {   
                                    $data->currencyJoin('currency_rpt', 'rptCurrencyID', 'CurrencyName');
                                } 
                            
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 25:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdByUserSystemID', 'createdByName');
                                }
                                else if ($table == 'template') {   
                                    $data->templateJoin('template', 'templateMasterID', 'reportName');
                                } 
                                else if ($table == 'confirm_by') {   
                                    $data->employeeJoin('confirm_by', 'confirmedByEmpSystemID', 'createdByName');
                                } 
                                else if ($table == 'approved_by') {   
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'segment') {
                                    $data->segmentJoin('segment', 'serviceLineSystemID', 'ServiceLineDes');
                                }
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 26:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'approved_by') {   
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                }
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                break;
                case 27:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'template') {   
                                    $data->templateJoin('template', 'templatesMasterAutoID', 'reportName');
                                } 
                               
                                else if ($table == 'approved_by') {   
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 28:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'currency') {   
                                    $data->currencyJoin('currency', 'currencyID', 'CurrencyName');
                                } 
                                else if ($table == 'local_currency') {   
                                    $data->currencyJoin('local_currency', 'localCurrencyID', 'CurrencyName');
                                } 
                                else if ($table == 'rpt_currency') {   
                                    $data->currencyJoin('rpt_currency', 'rptCurrencyID', 'CurrencyName');
                                } 
                                
                                else if ($table == 'approved_by') {   
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 29:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'modified_by') {   
                                    $data->employeeJoin('modified_by', 'modifiedUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'confirmed_by') {   
                                    $data->employeeJoin('confirmed_by', 'confirmedByEmpSystemID', 'createdByName');
                                } 
                                else if ($table == 'currency') {   
                                    $data->currencyJoin('currency', 'currencyID', 'CurrencyName');
                                } 
                                else if ($table == 'template') {   
                                    $data->templateJoin('template', 'templateMasterID', 'reportName');
                                } 
                                
                                else if ($table == 'approved_by') {   
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'segment') {
                                    $data->segmentJoin('segment', 'serviceLineSystemID', 'ServiceLineDes');
                                }
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 30:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'modified_by') {   
                                    $data->employeeJoin('modified_by', 'modifiedUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'confirmed_by') {   
                                    $data->employeeJoin('confirmed_by', 'confirmedByEmpSystemID', 'createdByName');
                                }
                                else if ($table == 'approved_by') {   
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'segment') {
                                    $data->segmentJoin('segment', 'serviceLineSystemID', 'ServiceLineDes');
                                }
                                else if ($table == 'depratment') {
                                    $data->departmentJoin('depratment', 'departmentSystemID', 'DepartmentDescription');
                                }
                                else if ($table == 'asset_type') {
                                    $data->assetTypeJoin('asset_type', 'assetType', 'typeDes');
                                }
                                else if ($table == 'fa_cat') {
                                    $data->faCatTypeJoin('fa_cat', 'faCatID', 'catDescription');
                                }
                                else if ($table == 'fa_cat_sub') {
                                    $data->faCatSubTypeJoin('fa_cat_sub', 'faSubCatID', 'catDescription');
                                }
                                else if ($table == 'fa_cat_sub_2') {
                                    $data->faCatSubTypeJoin('fa_cat_sub_2', 'faSubCatID2', 'catDescription');
                                }
                                else if ($table == 'fa_cat_sub_3') {
                                    $data->faCatSubTypeJoin('fa_cat_sub_3', 'faSubCatID3', 'catDescription');
                                }
                                else if ($table == 'doc_origin_detail') {
                                    $data->docIdJoin('doc_origin_detail', 'docOriginDetailID', 'itemDescription');
                                }
                                else if ($table == 'location') {
                                    $data->locationJoin('location', 'LOCATION', 'locationName');
                                }
                                else if ($table == 'finance_cat') {
                                    $data->financeCatJoin('finance_cat', 'AUDITCATOGARY', 'financeCatDescription');
                                }
                                
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 31:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'confirmed_by') {   
                                    $data->employeeJoin('confirmed_by', 'confirmedByEmpSystemID', 'createdByName');
                                } 
                                else if ($table == 'local_currency') {   
                                    $data->currencyJoin('local_currency', 'depLocalCur', 'CurrencyName');
                                } 
                                else if ($table == 'rpt_currency') {   
                                    $data->currencyJoin('rpt_currency', 'depRptCur', 'CurrencyName');
                                } 
                                else if ($table == 'approved_by') {   
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 32:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'confirmed_by') {   
                                    $data->employeeJoin('confirmed_by', 'confirmedByEmpSystemID', 'createdByName');
                                } 
                                else if ($table == 'modified_by') {   
                                    $data->employeeJoin('modified_by', 'modifiedUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'approved_by') {   
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'company_to') {
                                    $data->companyJoin('company_to', 'toCompanySystemID', 'CompanyName');
                                } 
                                     else if ($table == 'dis_type') {
                                    $data->disposTypeJoin('dis_type', 'disposalType', 'typeDescription');
                                } 
                                else if ($table == 'customer') {
                                    $data->customerJoin('customer', 'customerID', 'CustomerName');
                                } 
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 33:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'confirmed_by') {   
                                    $data->employeeJoin('confirmed_by', 'confirmedByEmpSystemID', 'createdByName');
                                } 
                                else if ($table == 'modified_by') {   
                                    $data->employeeJoin('modified_by', 'modifiedUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'approved_by') {   
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                                else if ($table == 'chart_acc') {   
                                    $data->charAccJoin('chart_acc', 'contraAccountSystemID', 'AccountDescription');
                                } 
                                else if ($table == 'fa_cat') {
                                    $data->assetCatJoin('fa_cat', 'faCatID', 'catDescription');
                                } 
                                else if ($table == 'asset') {
                                    $data->assetJoin('asset', 'faID', 'assetDescription');
                                } 
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 34:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'confirmed_by') {   
                                    $data->employeeJoin('confirmed_by', 'confirmedByEmpSystemID', 'createdByName');
                                } 
                                else if ($table == 'modified_by') {   
                                    $data->employeeJoin('modified_by', 'modifiedUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'approved_by') {   
                                    $data->employeeJoin('approved_by', 'approvedByUserSystemID', 'createdByName');
                                } 
                                else if ($table == 'company') {
                                    $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                } 
                            }
                        }
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        break;
                case 35:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                    
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                }
                                else if ($table == 'confirmed_by') {   
                                    $data->employeeJoin('confirmed_by', 'confirmed_by_emp_id', 'createdByName');
                                } 
                                else if ($table == 'approved_by') {   
                                    $data->employeeJoin('approved_by', 'approved_by_emp_id', 'createdByName');
                                } 
                                else if ($table == 'location') {
                                    $data->locationJoin('location', 'location', 'locationByName');
                                }  
                            }
                        }
                       
                        $data->whereIn($masterTable . '.company_id', $subCompanies);
                        break;
                case 36:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                       
                     
                        $data->whereIn($masterTable . '.company_id', $subCompanies);
                       
                        break;
                case 37:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                        
                        
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                    $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                    }
                                   else if ($table == 'rpt_currency') {   
                                       $data->currencyJoin('rpt_currency', 'documentRptCurrencyID', 'CurrencyName');
                                   } 
                                   else if ($table == 'local_currency') {   
                                       $data->currencyJoin('local_currency', 'documentLocalCurrencyID', 'CurrencyName');
                                   } 
                                   else if ($table == 'doc_currency') {   
                                       $data->currencyJoin('doc_currency', 'documentTransCurrencyID', 'CurrencyName');
                                   } 
                                   else if ($table == 'document_approved_by') {   
                                       $data->employeeJoin('document_approved_by', 'documentFinalApprovedByEmpSystemID', 'createdByName');
                                   } 
                                   else if ($table == 'document_confirm_by') {   
                                       $data->employeeJoin('document_confirm_by', 'documentConfirmedByEmpSystemID', 'createdByName');
                                   } 
                                   else if ($table == 'chart_acc') {
                                       $data->chartOfAccountJoin('chart_acc', 'chartOfAccountSystemID', 'AccountCode');
                                   }  
                                   else if ($table == 'company_master') {
                                       $data->companyJoin('company_master', 'masterCompanyID', 'CompanyName');
                                   } 
                                   else if ($table == 'company') {
                                       $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                   } 
                                   else if ($table == 'segment') {
                                       $data->segmentJoin('segment', 'serviceLineSystemID', 'ServiceLineDes');
                                   } 
           
                            }
                        }

                   
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        
                        break;
                case 38:
                    if ($isDetailExist) {
                        $data->detailJoin();
                        }

                        if (!$this->checkMasterColumn($report['columns'], 'supplier', 'table') && !$this->checkMasterColumn($report['filter_columns'], 'supplier', 'table') &&
                        ($this->checkMasterColumn($report['columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['columns'], 'supplier_country', 'table') ||
                            $this->checkMasterColumn($report['filter_columns'], 'supplier_currency', 'table') ||$this->checkMasterColumn($report['filter_columns'], 'supplier_country', 'table'))) {
                        $data->supplierJoin('supplier', 'supplierID', 'primarySupplierCode');
                        }
                        
                        
                        foreach ($templateData['tables'] as $table) {
                            if ($this->checkMasterColumn($report['columns'], $table, 'table') || $this->checkMasterColumn($report['filter_columns'], $table, 'table')) {
                                if ($table == 'created_by') {
                                    
                                    $data->employeeJoin('created_by', 'createdUserSystemID', 'createdByName');
                                    }
                                    else if ($table == 'rpt_currency') {   
                                        $data->currencyJoin('rpt_currency', 'wacRptCurrencyID', 'CurrencyName');
                                    } 
                                    else if ($table == 'local_currency') {   
                                        $data->currencyJoin('local_currency', 'wacLocalCurrencyID', 'CurrencyName');
                                    }  
                                    else if ($table == 'unit') {
                                        $data->unitJoin('unit', 'unitOfMeasure', 'UnitShortCode');
                                    }
                                    else if ($table == 'company') {
                                        $data->companyJoin('company', 'companySystemID', 'CompanyName');
                                    } 
                                    else if ($table == 'segment') {
                                        $data->segmentJoin('segment', 'serviceLineSystemID', 'ServiceLineDes');
                                    } 
                                    else if ($table == 'warehouse') {
                                        $data->wareHouseJoin('warehouse', 'wareHouseSystemCode', 'wareHouseDescription');
                                    } 
            
                            }
                        }

                    
                        $data->whereIn($masterTable . '.companySystemID', $subCompanies);
                        
                        break;
                default:
                    $data = [];
                    break;
            }
                
           
            $uniqueId = $detailPrimaryKey;
            if (!$isDetailExist) {
                $uniqueId = $primaryKey;
            }
            
            $search = isset($input['search']) ? $input['search'] : '';

            /*  1 : 'equals',
                2 : 'not equals',
                3 : 'less than',
                4 : 'greater than',
                5 : 'less or equal',
                6 : 'greater or equal',
                7 : 'contain',
                8 : 'does not contain',
                9 : 'start with'
            */

            // filter
           
            if (isset($report['filter_columns']) && count($report['filter_columns']) > 0) {
                $filterColumns = $this->getFilterColumns($report['filter_columns']);
                
                foreach ($filterColumns as $column) {
                    $operator = $column['operator'];

                    if ($column['column_type'] == 2) { // date columns
                        $date = Carbon::parse($column['value'])->format('Y-m-d');
                        $dateTo = isset($column['value_to']) ? Carbon::parse($column['value_to'])->format('Y-m-d') : Carbon::parse(now())->format('Y-m-d');
                        switch ($operator) {
                            case 1:
                                $data->whereDate($column['columnName'], $date);
                                break;
                            case 2:
                                $data->whereDate($column['columnName'], '!=', $date);
                                break;
                            case 3:
                                $data->whereDate($column['columnName'], '<', $date);
                                break;
                            case 4:
                                $data->whereDate($column['columnName'], '>', $date);
                                break;
                            case 5:
                                $data->whereDate($column['columnName'], '<=', $date);
                                break;
                            case 6:
                                $data->whereDate($column['columnName'], '>=', $date);
                                break;
                            /*case 7:
                                $data->whereDate($column['columnName'], 'like', "%{$date}%");
                                break;
                            case 8:
                                $data->whereDate($column['columnName'], 'not like', "%{$date}%");
                                break;
                            case 9:
                                $data->whereDate($column['columnName'], 'like', "{$date}%");
                                break;*/
                            case 10:

                                if ($date && $dateTo) {
                                    $data->whereBetween($column['columnName'], [$date, $dateTo]);
                                }
                                break;
                            case 11:
                                if ($date && $dateTo) {
                                    $data->whereNotBetween($column['columnName'], [$date, $dateTo]);
                                }
                                break;
                            case 12:
                                $currentYear = date("Y");
                                $data->whereYear($column['columnName'],'=', $currentYear);
                                break;
                            case 13:
                                $currentMont = date('m');
                                $data->whereMonth($column['columnName'],'=', $currentMont);
                                break;
                            default:
                                break;
                        }


                    } else if ($column['column_type'] == 6) { // status
                        /*
                         * 1 = 'Not Confirmed'
                         * 2 = 'Pending Approval'
                         * 3 = 'Fully Approved'
                         * 4 = 'Referred Back'
                         * 5 = 'Cancelled'
                         */
                        switch ($operator) {
                            case 1:
                                if (intval($column['value']) == 1) { //Not Confirmed
                                    $data->where($masterTable . '.' . $templateData['confirmedColumn'], 0)
                                        ->where($masterTable . '.' . $templateData['approvedColumn'], 0);
                                } else if (intval($column['value']) == 2) { // Pending Approval
                                    $data->where($masterTable . '.' . $templateData['confirmedColumn'], $templateData['confirmedValue'])
                                        ->where($masterTable . '.' . $templateData['approvedColumn'], 0);
                                } else if (intval($column['value']) == 3) { // Fully Approved
                                    $data->where($masterTable . '.' . $templateData['confirmedColumn'], $templateData['confirmedValue'])
                                        ->where($masterTable . '.' . $templateData['approvedColumn'], $templateData['approvedValue']);

                                    if($templateData['canceledColumn']){
                                        $data->where($masterTable . '.' . $templateData['canceledColumn'], 0);
                                    }

                                    if ($templateData['timesReferredColumn']) {
                                        $data->where($masterTable . '.' . $templateData['timesReferredColumn'], 0);
                                    }

                                } else if (intval($column['value']) == 4) { //Referred Back
                                    $data->where($masterTable . '.' . $templateData['confirmedColumn'], $templateData['confirmedValue'])
                                        ->where($masterTable . '.' . $templateData['approvedColumn'], $templateData['approvedValue']);
                                    if ($templateData['timesReferredColumn']) {
                                        $data->where($masterTable . '.' . $templateData['timesReferredColumn'], -1);
                                    }
                                } else if (intval($column['value']) == 5) { //Cancelled
                                    if ($templateData['canceledColumn']) {
                                        $data->where($masterTable . '.' . $templateData['canceledColumn'], $templateData['canceledValue']);
                                    }
                                }
                                break;
                            case 2:
                                if (intval($column['value']) == 1) { //Not Confirmed
                                    $data->where($masterTable . '.' . $templateData['confirmedColumn'], '!=', 0);
                                } else if (intval($column['value']) == 2) { // Pending Approval
                                    $data->where(function ($q) use ($masterTable, $templateData) {
                                        $q->where($masterTable . '.' . $templateData['confirmedColumn'], 0)
                                            ->orWhere($masterTable . '.' . $templateData['approvedColumn'], '!=', 0);
                                    });
                                } else if (intval($column['value']) == 3) { // Fully Approved
                                    $data->where($masterTable . '.' . $templateData['approvedColumn'], 0);
                                } else if (intval($column['value']) == 4) { //Referred Back
                                    if ($templateData['timesReferredColumn']) {
                                        $data->where($masterTable . '.' . $templateData['timesReferredColumn'], 0);
                                    }
                                } else if (intval($column['value']) == 5) { //Cancelled
                                    if ($templateData['canceledColumn']) {
                                        $data->where($masterTable . '.' . $templateData['canceledColumn'], 0);
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                    } else {
                        switch ($operator) {
                            case 1:
                                $data->where($column['columnName'], $column['value']);
                                break;
                            case 2:
                                $data->where($column['columnName'], '!=', $column['value']);
                                break;
                            case 3:
                                $data->where($column['columnName'], '<', $column['value']);
                                break;
                            case 4:
                                $data->where($column['columnName'], '>', $column['value']);
                                break;
                            case 5:
                                $data->where($column['columnName'], '<=', $column['value']);
                                break;
                            case 6:
                                $data->where($column['columnName'], '>=', $column['value']);
                                break;
                            case 7:
                                $data->where($column['columnName'], 'like', "%{$column['value']}%");
                                break;
                            case 8:
                                $data->where($column['columnName'], 'not like', "%{$column['value']}%");
                                break;
                            case 9:
                                $data->where($column['columnName'], 'like', "{$column['value']}%");
                                break;
                            default:
                                break;
                        }
                    }
                }
            }

            $searchColumns = $this->getFilterColumns($report['columns']);
            //search
            if ($search) {
                $search = str_replace("\\", "\\\\\\\\", $search);
                $data->where(function ($q) use ($searchColumns, $search) {
                    foreach ($searchColumns as $key => $column) {
                        if ($column['column_type'] == 2) { // date field
                            //
                        } else if ($column['column_type'] == 6) { // status
                            //
                        } else {
                            if ($key == 0) {
                                $q->where($column['columnName'], 'like', "%{$search}%");
                            } else {
                                $q->orWhere($column['columnName'], 'like', "%{$search}%");
                            }
                        }
                    }
                });
            }
            
            // sort by
            if (!$sortByColumns) {
                array_push($sortByColumns, array('column' => $uniqueId, 'by' => 'desc'));
            }
            
            foreach ($sortByColumns as $column) {
                $data = $data->orderBy($column['column'], $column['by']);
            }
            
            // group by columns
            $groupByColumns = $this->getGroupByColumns($report['columns'], $masterTable, $detailTable);
           
            // group by
            if (!$groupByColumns) {
                array_push($groupByColumns, $uniqueId);
            }

            $summarizeColumns = $this->getSummarizeColumns($report['summarize']);
            
            if(count($summarizeColumns) > 0 && $data){
                foreach ($summarizeColumns as $key => $summarizeColumn){
                    $tem = array(
                        'id' => $key + 1,
                        'column_id' => $summarizeColumn['column_id'],
                        'column' => $summarizeColumn['column_as'],
                        'label' => '',
                        'value' => 0,
                        'decimalPlaces' => 2
                    );
                    switch ($summarizeColumn['type_id']) {
                        case 1:
                            $tem['label'] = 'Sum';
                            $tem['value'] =  $data->sum($summarizeColumn['columnName']);
                            break;
                        case 2:
                            $tem['label'] = 'Avg';
                            $tem['value'] = $data->avg($summarizeColumn['columnName']);
                            break;
                        case 3:
                            $tem['label'] = 'Max';
                            $tem['value'] =  $data->max($summarizeColumn['columnName']);
                            break;
                        case 4:
                            $tem['label'] = 'Min';
                            $tem['value'] =  $data->min($summarizeColumn['columnName']);
                            break;
                        default:
                            break;

                    }

                    array_push($summarize, $tem);

                }
            }
            
            $data = $data->groupBy($groupByColumns);
            
          
            // paginate
        }


        // summarize
        /*  1 = 'Sum'
            2 = 'Average'
            3 = 'Max'
            4 = 'Min'
        */


        $output['data']      = $data;
        $output['report']    = $report;
        $output['summarize'] = $summarize;
        $output['success']   = true;
        return $output;

    }

    public function exportCustomReport(Request $request)
    {

        $input = $request->all();
        $type = isset($input['type']) ? $input['type'] : 'csv';
        $result = $this->getCustomReportQry($request);

        $dataRows = $result['data'];
        if($dataRows) {
            $dataRows = $dataRows->get();
        }
        $report = $result['report'];

        if (!empty($report) && !empty($dataRows)) {
            $x = 0;
            $data = [];
            foreach ($dataRows as $val) {
                $x++;

                foreach ($report['columns'] as $column) {
                    if ($column['column']['column_type'] == 1 || $column['column']['column_type'] == 5) {
                        $data[$x][$column['label']] = $val[$column['column']['column_as']];
                    } else if ($column['column']['column_type'] == 2) {
                        $data[$x][$column['label']] = Helper::dateFormat($val[$column['column']['column_as']]);
                    } else if ($column['column']['column_type'] == 4) {
                        $data[$x][$column['label']] = round($val[$column['column']['column_as']], $val[$column['column']['column'] . 'DecimalPlaces']);
                    } else if ($column['column']['column_type'] == 6) {

                        if ($val['canceledYN'] == -1) {
                            $data[$x][$column['label']] = "Cancelled";
                        } else if ($val['confirmedYN'] == 0 && $val['approved'] == 0) {
                            $data[$x][$column['label']] = " Not Confirmed";
                        } else if ($val['confirmedYN'] == 1 && $val['approved'] == 0 && $val['timesReferred'] == 0) {
                            $data[$x][$column['label']] = "Pending Approval";
                        } else if ($val['confirmedYN'] == 1 && $val['approved'] == 0 && $val['timesReferred'] == -1) {
                            $data[$x][$column['label']] = "Referred Back";
                        } else if ($val['confirmedYN'] == 1 && ($val['approved'] == -1 || $val['approved'] == 1)) {
                            $data[$x][$column['label']] = "Fully Approved";
                        } else {
                            $data[$x][$column['label']] = '';
                        }

                    }else if($column['column']['column_type'] == 3 || $column['column']['column_type'] == 7){

                        if(!$val[$column['column']['column_as']]){
                            $data[$x][$column['label']] = 'NO';
                        }else{
                            $data[$x][$column['label']] = 'YES';
                        }
                    }
                }
            }


            \Excel::create('custom_report', function ($excel) use ($data) {
                $excel->sheet('sheet name', function ($sheet) use ($data) {
                    $sheet->fromArray($data, null, 'A1', true);
                    $sheet->setAutoSize(true);
                    $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                });
                $lastrow = $excel->getActiveSheet()->getHighestRow();
                $excel->getActiveSheet()->getStyle('A1:N' . $lastrow)->getAlignment()->setWrapText(true);
            })->download('xls');
        }
        return $this->sendError(trans('custom.no_records_found'), 500);
    }
}
