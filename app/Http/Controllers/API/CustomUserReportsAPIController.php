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
                $query->where('description', 'LIKE', "%{$search}%");
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
                default;
                    break;
            }
            
            if ($this->checkMasterColumn($report['columns'], 6, 'column_type')) {
                foreach ($templateData['statusColumns'] as $column) {
                    array_push($columns, $masterTable . '.' . $column);
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
            })->download($type);
        }
        return $this->sendError(trans('custom.no_records_found'), 500);
    }
}
