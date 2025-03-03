<?php

namespace App\Http\Controllers\API;

use App\helper\BudgetConsumptionService;
use App\helper\Helper;
use App\Http\Requests\API\CreateDashboardWidgetMasterAPIRequest;
use App\Http\Requests\API\UpdateDashboardWidgetMasterAPIRequest;
use App\Models\BookInvSuppDet;
use App\Models\BudgetMaster;
use App\Models\Budjetdetails;
use App\Models\ChartOfAccount;
use App\Models\CompanyFinanceYear;
use App\Models\Company;
use App\Models\BookInvSuppMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\DashboardWidgetMaster;
use App\Models\AccountsReceivableLedger;
use App\Models\CustomerMaster;
use App\Models\DepartmentMaster;
use App\Models\GeneralLedger;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Repositories\DashboardWidgetMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\BudgetConsumedData;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;
use App\Models\SupplierGroup;
use App\Models\SupplierMaster;

/**
 * Class DashboardWidgetMasterController
 * @package App\Http\Controllers\API
 */

class DashboardWidgetMasterAPIController extends AppBaseController
{
    /** @var  DashboardWidgetMasterRepository */
    private $dashboardWidgetMasterRepository;

    public function __construct(DashboardWidgetMasterRepository $dashboardWidgetMasterRepo)
    {
        $this->dashboardWidgetMasterRepository = $dashboardWidgetMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/dashboardWidgetMasters",
     *      summary="Get a listing of the DashboardWidgetMasters.",
     *      tags={"DashboardWidgetMaster"},
     *      description="Get all DashboardWidgetMasters",
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
     *                  @SWG\Items(ref="#/definitions/DashboardWidgetMaster")
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
        $input = $request->all();

        $this->dashboardWidgetMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->dashboardWidgetMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $dashboardWidgetMasters = $this->dashboardWidgetMasterRepository->with(['department'])->paginate(isset($input['limit']) ? $input['limit'] : 15);

        return $this->sendResponse($dashboardWidgetMasters->toArray(), 'Dashboard Widget Masters retrieved successfully');
    }

    /**
     * @param CreateDashboardWidgetMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/dashboardWidgetMasters",
     *      summary="Store a newly created DashboardWidgetMaster in storage",
     *      tags={"DashboardWidgetMaster"},
     *      description="Store DashboardWidgetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DashboardWidgetMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DashboardWidgetMaster")
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
     *                  ref="#/definitions/DashboardWidgetMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDashboardWidgetMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('departmentID'));
        $validator = \Validator::make($input, [
            'WidgetMasterName' => 'required',
            'departmentID' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        $dashboardWidgetMaster = $this->dashboardWidgetMasterRepository->create($input);

        return $this->sendResponse($dashboardWidgetMaster->toArray(), 'Dashboard Widget Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/dashboardWidgetMasters/{id}",
     *      summary="Display the specified DashboardWidgetMaster",
     *      tags={"DashboardWidgetMaster"},
     *      description="Get DashboardWidgetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DashboardWidgetMaster",
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
     *                  ref="#/definitions/DashboardWidgetMaster"
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
        /** @var DashboardWidgetMaster $dashboardWidgetMaster */
        $dashboardWidgetMaster = $this->dashboardWidgetMasterRepository->with(['department'])->findWithoutFail($id);

        if (empty($dashboardWidgetMaster)) {
            return $this->sendError('Dashboard Widget Master not found');
        }

        return $this->sendResponse($dashboardWidgetMaster->toArray(), 'Dashboard Widget Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDashboardWidgetMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/dashboardWidgetMasters/{id}",
     *      summary="Update the specified DashboardWidgetMaster in storage",
     *      tags={"DashboardWidgetMaster"},
     *      description="Update DashboardWidgetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DashboardWidgetMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DashboardWidgetMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DashboardWidgetMaster")
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
     *                  ref="#/definitions/DashboardWidgetMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDashboardWidgetMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input,'department');
        $input = $this->convertArrayToSelectedValue($input, array('departmentID'));
//        $validator = \Validator::make($input, [
//            'WidgetMasterName' => 'required',
//            'departmentID' => 'required|numeric|min:1',
//        ]);
//
//        if ($validator->fails()) {
//            return $this->sendError($validator->messages(), 422);
//        }
        /** @var DashboardWidgetMaster $dashboardWidgetMaster */
        $dashboardWidgetMaster = $this->dashboardWidgetMasterRepository->findWithoutFail($id);

        if (empty($dashboardWidgetMaster)) {
            return $this->sendError('Dashboard Widget Master not found');
        }

        $dashboardWidgetMaster = $this->dashboardWidgetMasterRepository->update($input, $id);

        return $this->sendResponse($dashboardWidgetMaster->toArray(), 'DashboardWidgetMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/dashboardWidgetMasters/{id}",
     *      summary="Remove the specified DashboardWidgetMaster from storage",
     *      tags={"DashboardWidgetMaster"},
     *      description="Delete DashboardWidgetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DashboardWidgetMaster",
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
        /** @var DashboardWidgetMaster $dashboardWidgetMaster */
        $dashboardWidgetMaster = $this->dashboardWidgetMasterRepository->findWithoutFail($id);

        if (empty($dashboardWidgetMaster)) {
            return $this->sendError('Dashboard Widget Master not found');
        }

        $dashboardWidgetMaster->delete();

        return $this->sendResponse($id, 'Dashboard Widget Master deleted successfully');
    }

    public function getWidgetMasterFormData(Request $request){
//        $input = $request->all();
//        $companyID = $input['companySystemID'];
        $departmentIDs = [1,3,4];

        $departmentMasters = DepartmentMaster::selectRaw('DepartmentDescription as label,
                                                departmentSystemID as value')
            ->whereIn('departmentSystemID', $departmentIDs)
            ->orderBy('departmentSystemID', 'asc')
            ->get();

        $departmentMasters = $departmentMasters->toArray();

        $array = array('departments' => $departmentMasters);
        return $this->sendResponse($array, 'Data retrieved successfully');
    }

    public function getDashboardDepartment(Request $request){

        $input = $request->all();
        $companyID = isset($input['companyId'])?$input['companyId']:0;
        $empSystemID = Helper::getEmployeeSystemID();
        $departmentMasters = DepartmentMaster::selectRaw('DepartmentDescription,departmentSystemID,
        CASE
    WHEN departmentSystemID = 1 THEN 2
    WHEN departmentSystemID = 3 THEN 3
    WHEN departmentSystemID = 4 THEN 1
    ELSE 0
END AS sortDashboard')
            ->whereHas('widget', function($query){
                $query->where('isActive',1);
            })->whereHas('employees', function($query) use($empSystemID,$companyID){
                $query->where('employeeSystemID',$empSystemID)
//                    ->where('companySystemID',$companyID)
                    ->whereHas('employee', function($q){
                        $q->where('dischargedYN', 0);
                    });
            })
            ->orderBy('sortDashboard', 'DESC')
            ->get();

        if(!empty($departmentMasters)){
            $departmentMasters = $departmentMasters->toArray();
        }else{
            $departmentMasters = [];
        }
        return $this->sendResponse($departmentMasters, 'Data retrieved successfully');
    }

    public function getDashboardWidget(Request $request){

        $input = $request->all();
        $departmentID = isset($input['departmentID'])?$input['departmentID']:0;
        $companyId = isset($input['companyId'])?$input['companyId']:0;
        $widget = DashboardWidgetMaster::where('isActive',1)
            ->where('departmentID',$departmentID)
            ->orderBy('sortOrder')
            ->get();
        $output = [];
        $supplierGroup = SupplierGroup::notDeleted();
        $glAccounts = ChartOfAccount::all()->toArray();
        if(!empty($widget)){
            $output['widget'] = $widget->toArray();
            $output['supplierGroup'] = $supplierGroup;
            $output['glAccounts'] = $glAccounts;
        }
        return $this->sendResponse($output, 'Data retrieved successfully');
    }

    public function getCustomWidgetGraphData(Request $request){

        $input = $request->all();
        $data = [];
        $suplierrGroup = [];
        $glAccount = [];
        $isSupplierGroupExists = false;

        if(isset($input['glAccount'])){
            $glAccount = ChartOfAccount::where('chartOfAccountSystemID',$input['glAccount'])
                    ->pluck('chartOfAccountSystemID')->toArray();
        }

        if(isset($input['supplierGroup'])){
           $isSupplierGroupExists = true;
           $suplierrGroup =  SupplierMaster::where('supplier_group_id',$input['supplierGroup'])->pluck('supplierCodeSystem')->toArray();
        }

        $id = isset($input['widgetMasterID']) ? $input['widgetMasterID'] : 0;
        if($id==0){
            return $this->sendError('Widget Master ID Not Found');
        }
        $companyID = isset($input['companyID']) ? $input['companyID'] : 0;
        $isGroup = \Helper::checkIsCompanyGroup($companyID);

        if($isGroup){
            $childCompanies = \Helper::getGroupCompany($companyID);
        }else{
            $childCompanies = [$companyID];
        }

        $dashBoardWidget = DashboardWidgetMaster::find($id);
        $currentYear = date("Y");
        switch ($id){
            case 1:// top 10 subcategory by spent
                $temSeries = array(
//                    'color' => array(
//                        'linearGradient'=> array(
//                            'x1'=> 0,
//                            'x2'=> 0,
//                            'y1'=> 0,
//                            'y2'=> 1
//                        ),
//                        'stops'=> [
//                            [0, '#003399'],
//                            [1, '#ff66AA']
//                        ]
//                    ),
                    'labelFontColor' => "#ffffff",
                    'name' => 'Item Sub Category',
                    'marker' => array(
                        'enabled' => false
                    ),
                    'colorByPoint'=> true,
                    'data' => array()
                );
                $data = [];
                $temSeries['id'] = $dashBoardWidget->widgetMasterID;
                $result = PurchaseOrderDetails::whereHas('order', function($query) use($childCompanies){
                    $query->whereIn('companySystemID', $childCompanies)
                    ->where('approved', -1)
                    ->where('poCancelledYN', 0)
                    ->whereIn('poType_N', [1,2,3,4,6]);
                })
                    ->whereHas('financecategorysub')
                    ->select(DB::raw('itemFinanceCategorySubID,SUM(GRVcostPerUnitComRptCur) AS total'))
                    ->with(['financecategorysub'])
                    ->groupBy('itemFinanceCategorySubID')
                    ->orderBy('total','DESC')
                    ->limit(10)
                    ->get();

                $dataArray = [];
                if(!empty($result) && $result->count()){
                    foreach ($result as $raw){
                        if(isset($raw->financecategorysub->categoryDescription)){
                            $dataArray[0][$raw->financecategorysub->categoryDescription]=array(
                                'y'=>$raw->total/1000,
                                'description'=>$raw->financecategorysub->categoryDescription
                            );
                        }
                    }
                    $temSeries['data'] = array_values((array)$dataArray[0]);
                    $temSeries['categories'] = array_keys((array)$dataArray[0]);
                    array_push($data,$temSeries);
                }
                return $this->sendResponse($data, 'Data retrieved successfully');

            case 2:
            // Top 10 Suppliers
              $data = [];
              $temSeries = array(
                  'name' => '',
                  'positive' => true,
//                  'color' => '#C7EEB4',
                  'y' => 0
              );
                $pieData = [];

//                $result = ProcumentOrder::select(DB::raw('supplierID,SUM(GRVcostPerUnitComRptCur) AS total'))
//                    ->whereIn('companySystemID', $childCompanies)
//                    ->with(['supplier'])
//                    ->whereHas('supplier')
//                    ->where('approved', -1)
//                    ->where('poCancelledYN', 0)
//                    ->whereIn('poType_N', [1,2,3,4,6])
//                    ->groupBy('supplierID')
//                    ->orderBy('total','DESC')
//                    ->limit(10)
//                    ->get();
                $result = ProcumentOrder::select(DB::raw('supplierID,SUM(GRVcostPerUnitComRptCur) AS total'))
                    ->join('erp_purchaseorderdetails','erp_purchaseordermaster.purchaseOrderID','=','erp_purchaseorderdetails.purchaseOrderMasterID')
                    ->whereIn('erp_purchaseordermaster.companySystemID', $childCompanies)
                    ->with(['supplier'])
                    ->whereHas('supplier')
                    ->where('approved', -1)
                    ->where('poCancelledYN', 0)
                    ->whereIn('poType_N', [1,2,3,4,6])
                    ->groupBy('supplierID')
                    ->orderBy('total','DESC')
                    ->limit(10)
                    ->when($isSupplierGroupExists == true, function ($query) use ($suplierrGroup) {
                            $query->whereIn('supplierID', $suplierrGroup);
                          })
                    ->get();
                if(!empty($result) && $result->count()){
//
//                    $finalTotal = 0;
//                    foreach ($result as $raw){
//                        $finalTotal = $finalTotal+$raw->total;
//                    }

                    foreach ($result as $raw){
                        $temSeries['name'] = isset($raw->supplier->supplierName)?$raw->supplier->supplierName:'';
//                        $temSeries['id'] = $raw->supplierID;
//                        $temSeries['y'] = $raw->total*100/$finalTotal;
                        $temSeries['y'] = $raw->total;
                        array_push($pieData, $temSeries);
                    }
                    $data[0]['data'] = $pieData;
//                    $data[0]['showInLegend'] = true;
//                    array_push($data,$pieData);
                }
                return $this->sendResponse($data, 'Data retrieved successfully');

            case 3:
                //Savings Per month
                $temSeries = array(
//                'color' => '#283593',
                    'labelFontColor' => "#ffffff",
//                'name' => 'Item Sub Category',
                    'marker' => array(
                        'enabled' => false
                    ),
                    'data' => array()
                );
                $data = [];
                return $this->sendResponse($data, 'Data retrieved successfully');

            case 4:
                //Delivery Time
            $temSeries = array(
//                'color' => '#283593',
                'labelFontColor' => "#ffffff",
//                'name' => 'Item Sub Category',
                'marker' => array(
                    'enabled' => false
                ),
                'data' => array()
            );
            $data = [];

            $sql = 'SELECT code,name,
	early / totalCount * 100 AS early,
	ontime / totalCount * 100 AS ontime,
	late / totalCount * 100 AS late
FROM
	(
SELECT 
    supplierID,code,name,
	count( purchaseOrderID ) AS totalCount,
	sum( early ) AS early,
	sum( ontime ) AS ontime,
	sum( late ) AS late
FROM
	(
SELECT
	erp_purchaseordermaster.purchaseOrderID,
	erp_purchaseordermaster.supplierID as supplierID,
	suppliermaster.primarySupplierCode as code,
	suppliermaster.supplierName as name,
IF
	( DATEDIFF( erp_purchaseordermaster.expectedDeliveryDate, erp_grvmaster.grvDate ) > 7, 1, 0 ) AS early,
IF
	(
	DATEDIFF( erp_purchaseordermaster.expectedDeliveryDate, erp_grvmaster.grvDate ) <= 7 && DATEDIFF( erp_purchaseordermaster.expectedDeliveryDate, erp_grvmaster.grvDate ) > -1,
	1,
	0 
	) AS ontime,

IF
	( DATEDIFF( erp_purchaseordermaster.expectedDeliveryDate, erp_grvmaster.grvDate ) <= -1, 1, 0 ) AS late   
FROM
	erp_purchaseordermaster
	JOIN suppliermaster ON erp_purchaseordermaster.supplierID = suppliermaster.supplierCodeSystem
	JOIN erp_purchaseorderdetails ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID
	JOIN erp_grvdetails ON erp_purchaseorderdetails.purchaseOrderDetailsID = erp_grvdetails.purchaseOrderDetailsID
	JOIN erp_grvmaster ON erp_grvdetails.grvAutoID = erp_grvmaster.grvAutoID AND
	erp_purchaseordermaster.companySystemID IN (' . join(',', $childCompanies) . ')     
	WHERE erp_purchaseordermaster.approved = -1 
	AND erp_purchaseordermaster.poCancelledYN = 0
	AND erp_purchaseordermaster.poType_N IN (1,2,3,4,6)
GROUP BY
	erp_purchaseordermaster.purchaseOrderID ,
	erp_purchaseordermaster.supplierID 
	ORDER BY
	erp_purchaseordermaster.poTotalSupplierTransactionCurrency DESC
	) temp GROUP BY
supplierID LIMIT 10
	) temp2';
//WHERE ((YEAR (erp_purchaseordermaster.approvedDate ) = "' . $year . '" AND MONTH (erp_purchaseordermaster.approvedDate ) <= ' . $month . '))
            $output = DB::select($sql);

            if(!empty($output)){
                $i=0;

                $early = [];
                $ontime = [];
                $late = [];

                foreach ($output as $raw){

                    $early[] = array(
                        'y'=> round($raw->early,2),
                        'description'=> $raw->name
                    );
                    $ontime[]=array(
                        'y'=> round($raw->ontime,2),
                        'description'=> $raw->name
                    );
                    $late[]=array(
                        'y'=> round($raw->late,2),
                        'description'=> $raw->name
                    );

                    $temSeries['categories'][]= $raw->code;
                }
                $temSeries['name']='Late';
                $temSeries['data']=$late;
                $temSeries['color']='#EF5350';
                array_push($data,$temSeries);
                $temSeries['name']='On Time';
                $temSeries['data']=$ontime;
                $temSeries['color']='#FFE082';
                array_push($data,$temSeries);
                $temSeries['name']='Early';
                $temSeries['data']=$early;
                $temSeries['color']='#81C784';
                array_push($data,$temSeries);
            }
            return $this->sendResponse($data, 'Data retrieved successfully');

            case 5:
                // Top 10 supplier by payment
                $data = [];
                $temSeries = array(
                    'name' => '',
                    'positive' => true,
//                  'color' => '#C7EEB4',
                    'y' => 0
                );
                $pieData = [];

                /*
                 * SELECT
	erp_generalledger.supplierCodeSystem,
	suppliermaster.supplierName,
	round(sum( erp_generalledger.documentRptAmount ) ,2)
FROM
	erp_generalledger
	INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
	AND suppliermaster.liabilityAccountSysemID = erp_generalledger.chartOfAccountSystemID
WHERE
	erp_generalledger.documentSystemID = 4
GROUP BY
	erp_generalledger.supplierCodeSystem*/

                $result = GeneralLedger::where('documentSystemID',4)
                    ->whereHas('supplier', function ($query) use($suplierrGroup,$isSupplierGroupExists){
                        $query->whereRaw('suppliermaster.liabilityAccountSysemID = erp_generalledger.chartOfAccountSystemID')
                        ->when($isSupplierGroupExists == true, function ($query) use ($suplierrGroup) {
                            $query->whereIn('supplierCodeSystem', $suplierrGroup);
                          });
                    })
                    ->whereIn('companySystemID', $childCompanies)
                    ->select(DB::raw('supplierCodeSystem,SUM(documentRptAmount) AS total'))
                    ->with(['supplier'])
                    ->groupBy('supplierCodeSystem')
                    ->orderBy('total','DESC')
                    ->limit(10)
                    ->get();

                    
                if(!empty($result) && $result->count()){
//
//                    $finalTotal = 0;
//                    foreach ($result as $raw){
//                        $finalTotal = $finalTotal+$raw->total;
//                    }

                    foreach ($result as $raw){
                        $temSeries['name'] = isset($raw->supplier->supplierName)?$raw->supplier->supplierName:'';
                        $temSeries['id'] = $raw->supplierCodeSystem;
                        $temSeries['y'] = $raw->total;
//                        $temSeries['y'] = $raw->total*100/$finalTotal;
                        array_push($pieData, $temSeries);
                    }
                    $data[0]['data'] = $pieData;
                }
                return $this->sendResponse($data, 'Data retrieved successfully');

            case 6:
                // Top 10 outstanding payables
                $temSeries = array(
//                    'color' => array(
//                        'linearGradient'=> array(
//                            'x1'=> 0,
//                            'x2'=> 0,
//                            'y1'=> 0,
//                            'y2'=> 1
//                        ),
//                        'stops'=> [
//                            [0, '#622774'],
//                            [1, '#C53364']
//                        ]
//                    ),
                    'colorByPoint'=> true,
                    'labelFontColor' => "#ffffff",
                    'name' => 'Outstanding',
                    'marker' => array(
                        'enabled' => false
                    ),
                    'data' => array()
                );
                $data = [];

                /*
                 * SELECT
	erp_generalledger.supplierCodeSystem,
	suppliermaster.supplierName,
	round(sum( erp_generalledger.documentRptAmount*-1 ) ,2)
FROM
	erp_generalledger
	INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
	AND suppliermaster.liabilityAccountSysemID = erp_generalledger.chartOfAccountSystemID
WHERE
	erp_generalledger.documentSystemID IN( 4,11,15)

GROUP BY
	erp_generalledger.supplierCodeSystem*/

                $result = GeneralLedger::whereIn('documentSystemID',[4,11,15])
                    ->whereHas('supplier', function ($query) use($suplierrGroup,$isSupplierGroupExists){
                        $query->whereRaw('suppliermaster.liabilityAccountSysemID = erp_generalledger.chartOfAccountSystemID')
                        ->when($isSupplierGroupExists == true, function ($query) use ($suplierrGroup) {
                            $query->whereIn('supplierCodeSystem', $suplierrGroup);
                          });
                    })
                    ->whereIn('companySystemID', $childCompanies)
                    ->select(DB::raw('supplierCodeSystem,SUM(documentRptAmount*-1) AS total'))
                    ->with(['supplier'])
                    ->groupBy('supplierCodeSystem')
                    ->orderBy('total','DESC')
                    ->limit(10)
                    ->get();

                $dataArray = [];
                if(!empty($result) && $result->count()){
                    foreach ($result as $raw){
                        if(isset($raw->supplier->supplierName)){
                            $dataArray[0][$raw->supplier->supplierName]=array(
                                'y'=>$raw->total/1000,
                                'description'=>$raw->supplier->supplierName
                            );
                        }
                    }
                    $temSeries['data'] = array_values((array)$dataArray[0]);
                    $temSeries['categories'] = array_keys((array)$dataArray[0]);
                    array_push($data,$temSeries);
                }
                return $this->sendResponse($data, 'Data retrieved successfully');

            case 7:
                // payment by status
                $data = [];
                return $this->sendResponse($data, 'Data retrieved successfully');

            case 8:
            // Top 10 GL Code by expense
            $temSeries = array(
//                'color' => array(
//                    'linearGradient'=> array(
//                        'x1'=> 0,
//                        'x2'=> 0,
//                        'y1'=> 0,
//                        'y2'=> 1
//                    ),
//                    'stops'=> [
//                        [0, '#184E68'],
//                        [1, '#57CA85']
//                    ]
//                ),
                'colorByPoint'=> true,
                'labelFontColor' => "#ffffff",
                'name' => 'GL Code',
                'marker' => array(
                    'enabled' => false
                ),
                'data' => array()
            );
            $data = [];

            /*
             * SELECT
erp_generalledger.chartOfAccountSystemID,
                                        erp_generalledger.glCode,
round(sum( erp_generalledger.documentRptAmount ) ,2)
FROM
                                        erp_generalledger
                                        INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                                    WHERE
                                        erp_generalledger.documentSystemID = 4 -- hard code as 4
                                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                        AND chartofaccounts.relatedPartyYN = 0
                                        AND (erp_generalledger.supplierCodeSystem IS NULL
                                        OR erp_generalledger.supplierCodeSystem = 0) -- hard code filers
                                        AND YEAR ( erp_generalledger.documentDate ) = "' . $year . '"
                                        AND erp_generalledger.documentTransAmount > 0 -- hard code this filter

GROUP BY
erp_generalledger.chartOfAccountSystemID
            */

            $result = GeneralLedger::where('documentSystemID',4)
                ->whereHas('charofaccount', function($query){
                    $query->where('relatedPartyYN',0);
                })
                ->whereIn('companySystemID', $childCompanies)
                ->where(function ($q){
                    $q->whereNull('supplierCodeSystem')->orWhere('supplierCodeSystem',0);
                })
                ->select(DB::raw('erp_generalledger.chartOfAccountSystemID as chartOfAccountSystemID,glCode,SUM(documentRptAmount) AS total'))
//                ->where('documentRptAmount','>',0)
                ->with(['charofaccount'])
                ->groupBy('chartOfAccountSystemID')
                ->orderBy('total','DESC')
                ->havingRaw('total > 0')
                ->limit(10)
                ->get();

            $dataArray = [];
            if(!empty($result) && $result->count()){
                foreach ($result as $raw){
                    $description = (isset($raw->charofaccount->AccountDescription))?$raw->charofaccount->AccountDescription:'';
                    $dataArray[0][$raw->glCode]=array(
                        'y'=>$raw->total/1000,
                        'description'=>$raw->glCode.' - '.$description
                    );
                }
                $temSeries['data'] = array_values((array)$dataArray[0]);
                $temSeries['categories'] = array_keys((array)$dataArray[0]);
                array_push($data,$temSeries);
            }
            return $this->sendResponse($data, 'Data retrieved successfully');

            case 9:
                //Top 10 Customer based on revenue
                /*
SELECT
	erp_generalledger.supplierCodeSystem,
	customermaster.CustomerName,
	round(sum( erp_generalledger.documentRptAmount ) ,2)
FROM
	erp_generalledger
	INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
	AND customermaster.custGLAccountSystemID = erp_generalledger.chartOfAccountSystemID
WHERE
	erp_generalledger.documentSystemID = 20
GROUP BY
	erp_generalledger.supplierCodeSystem
                 * */


                $data = [];
                $temSeries = array(
                    'name' => '',
                    'positive' => true,
//                  'color' => '#C7EEB4',
                    'y' => 0
                );
                $pieData = [];

//                $result = GeneralLedger::where('documentSystemID',20)
//                    ->whereHas('customer', function ($query){
//                        $query->whereRaw('customermaster.custGLAccountSystemID = erp_generalledger.chartOfAccountSystemID');
//                    })
//                    ->whereIn('companySystemID', $childCompanies)
//                    ->select(DB::raw('supplierCodeSystem,SUM(documentRptAmount) AS total'))
//                    ->with(['customer'])
//                    ->groupBy('supplierCodeSystem')
//                    ->orderBy('total','DESC')
//                    ->limit(10)
//                    ->get();

                $result = DB::select('SELECT
                                revenueCustomerDetail.companySystemID,
                                revenueCustomerDetail.companyID,
                                revenueCustomerDetail.CompanyName,
                                customermaster.CutomerCode,
                                customermaster.CustomerName,
                                documentLocalCurrency,
                                documentRptCurrency,
                                round(revenueCustomerDetail.MyLocalAmount,0) localAmount,
                                round(SUM(revenueCustomerDetail.MyRptAmount),0) RptAmount
                            FROM
                            (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                companymaster.CompanyName,
                                erp_generalledger.serviceLineCode,
                                erp_generalledger.clientContractID,
                                contractmaster.ContractNumber,
                                contractmaster.contractDescription,
                                contractmaster.ContEndDate,
                                erp_generalledger.documentID,
                                erp_generalledger.documentSystemCode,
                                erp_generalledger.documentCode,
                                erp_generalledger.documentSystemID,
                                erp_generalledger.documentDate,
                                erp_generalledger.documentNarration,
                                erp_generalledger.glCode,
                                erp_generalledger.glAccountType,
                                chartofaccounts.controlAccounts,
                                chartofaccounts.AccountDescription,
                                erp_generalledger.supplierCodeSystem,
                                currLocal.CurrencyCode as documentLocalCurrency,
                                currLocal.DecimalPlaces as documentLocalDecimalPlaces,
                                currRpt.CurrencyCode as documentRptCurrency,
                                currRpt.DecimalPlaces as documentRptDecimalPlaces,
                            IF
                                (
                                erp_generalledger.clientContractID = "X" 
                                AND erp_generalledger.supplierCodeSystem = 0,
                                0,
                            IF
                                (
                                erp_generalledger.clientContractID <> "X" 
                                AND erp_generalledger.supplierCodeSystem = 0,
                                contractmaster.clientID,
                            IF
                                ( erp_generalledger.documentSystemID = 11 OR erp_generalledger.documentSystemID = 15 OR erp_generalledger.documentSystemID = 4, contractmaster.clientID, erp_generalledger.supplierCodeSystem ) 
                                ) 
                                ) AS mySupplierCode,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalAmount,
                                (documentLocalAmount * -1) AS MyLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptAmount,
                                (documentRptAmount * -1) AS MyRptAmount,
                            IF
                                ( contractmaster.isContract = 1, "Contract", "PO" ) AS CONTRACT_PO 
                            FROM
                                erp_generalledger
                                INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                                AND chartofaccounts.controlAccountsSystemID = 1
                                INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                                LEFT JOIN contractmaster ON erp_generalledger.companyID = contractmaster.CompanyID 
                                AND erp_generalledger.clientContractID = contractmaster.ContractNumber
                                LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
                                LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
                                WHERE erp_generalledger.companySystemID IN (' . join(',', $childCompanies) . ')
                                AND YEAR(erp_generalledger.documentDate) = '.$currentYear.'
                                ) AS revenueCustomerDetail
                                INNER JOIN customermaster ON revenueCustomerDetail.mySupplierCode = customermaster.customerCodeSystem
																Group By mySupplierCode
																LIMIT 10');

                if(!empty($result) && count($result)){


                    foreach ($result as $raw){
                        $temSeries['name'] = isset($raw->CustomerName)?$raw->CustomerName:'';
//                        $temSeries['id'] = $raw->supplierCodeSystem;
//                        $temSeries['y'] = $raw->total*100/$finalTotal;
                        $temSeries['y'] = $raw->RptAmount;
                        array_push($pieData, $temSeries);
                    }
                    $data[0]['data'] = $pieData;
                }
                return $this->sendResponse($data, 'Data retrieved successfully');

            case 10:
                // Top 10 outstanding receivable
                /*
	SELECT
	erp_generalledger.supplierCodeSystem,
	customermaster.CustomerName,
	round(sum( erp_generalledger.documentRptAmount ) ,2)
FROM
	erp_generalledger
	INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
	AND customermaster.custGLAccountSystemID = erp_generalledger.chartOfAccountSystemID
WHERE
	erp_generalledger.documentSystemID IN( 20,19,21)

GROUP BY
	erp_generalledger.supplierCodeSystem
                 *
                 * */
                $temSeries = array(
//                    'color' => '#E57373 ',
                    'colorByPoint'=> true,
                    'labelFontColor' => "#ffffff",
                    'name' => 'Outstanding',
                    'marker' => array(
                        'enabled' => false
                    ),
                    'data' => array()
                );
                $data = [];

                $result = GeneralLedger::whereIn('documentSystemID',[20,19,21])
                    ->whereHas('customer', function ($query){
                        $query->whereRaw('customermaster.custGLAccountSystemID = erp_generalledger.chartOfAccountSystemID');
                    })
                    ->whereIn('companySystemID', $childCompanies)
                    ->select(DB::raw('supplierCodeSystem,SUM(documentRptAmount) AS total'))
                    ->with(['customer'])
                    ->groupBy('supplierCodeSystem')
                    ->orderBy('total','DESC')
                    ->limit(10)
                    ->get();

                $dataArray = [];
                if(!empty($result) && $result->count()){
                    foreach ($result as $raw){
                        if(isset($raw->customer->customerShortCode)){
                            $dataArray[0][$raw->customer->customerShortCode]=array(
                                'y'=>$raw->total/1000,
                                'description'=>$raw->customer->CustomerName
                            );
                        }
                    }
                    $temSeries['data'] = array_values((array)$dataArray[0]);
                    $temSeries['categories'] = array_keys((array)$dataArray[0]);
                    array_push($data,$temSeries);
                }
                return $this->sendResponse($data, 'Data retrieved successfully');

            case 11:
                /*
                 *
                 * #Top 10 customers collection
	SELECT
	erp_generalledger.supplierCodeSystem,
	customermaster.CustomerName,
	round(sum( erp_generalledger.documentRptAmount*-1 ) ,2)
FROM
	erp_generalledger
	INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
	AND customermaster.custGLAccountSystemID = erp_generalledger.chartOfAccountSystemID
WHERE
	erp_generalledger.documentSystemID IN( 21)

GROUP BY
	erp_generalledger.supplierCodeSystem
                 * */

                $temSeries = array(
//                    'color' => '#5C6BC0',
                    'colorByPoint'=> true,
                    'labelFontColor' => "#ffffff",
                    'name' => 'GL Code',
                    'marker' => array(
                        'enabled' => false
                    ),
                    'data' => array()
                );
                $data = [];
                $result = GeneralLedger::where('documentSystemID',21)
                    ->whereHas('customer', function ($query){
                        $query->whereRaw('customermaster.custGLAccountSystemID = erp_generalledger.chartOfAccountSystemID');
                    })
                    ->whereIn('companySystemID', $childCompanies)
                    ->select(DB::raw('supplierCodeSystem,SUM(documentRptAmount *- 1) AS total'))
                    ->with(['customer'])
                    ->groupBy('supplierCodeSystem')
                    ->orderBy('total','DESC')
                    ->limit(10)
                    ->get();
                $dataArray = [];
                if(!empty($result) && $result->count()){
                    foreach ($result as $raw){
                        if(isset($raw->customer->customerShortCode)){
                            $dataArray[0][$raw->customer->customerShortCode]=array(
                                'y'=>$raw->total/1000,
                                'description'=>$raw->customer->CustomerName
                            );
                        }
                    }
                    $temSeries['data'] = array_values((array)$dataArray[0]);
                    $temSeries['categories'] = array_keys((array)$dataArray[0]);
                    array_push($data,$temSeries);
                }
                return $this->sendResponse($data, 'Data retrieved successfully');

            case 12:
                //
                /*
                 * #Top 10 revenue based on GL Code(From Invoice)
SELECT
	erp_generalledger.chartOfAccountSystemID,
	chartofaccounts.AccountDescription,
	round(sum( erp_generalledger.documentRptAmount*-1 ) ,2)
FROM
	erp_generalledger
	INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
	AND chartofaccounts.controlAccountsSystemID=1
WHERE
	erp_generalledger.documentSystemID = 20
GROUP BY
	erp_generalledger.chartOfAccountSystemID*/

            $temSeries = array(
//                'color' => '#4DB6AC',
                'colorByPoint'=> true,
                'labelFontColor' => "#ffffff",
                'name' => 'GL Code',
                'marker' => array(
                    'enabled' => false
                ),
                'data' => array()
            );
            $data = [];
            $result = GeneralLedger::where('documentSystemID',20)
                ->whereHas('charofaccount', function($query){
                    $query->where('controlAccountsSystemID',1);
                })
                ->whereIn('companySystemID', $childCompanies)
                ->select(DB::raw('erp_generalledger.chartOfAccountSystemID as chartOfAccountSystemID,glCode,SUM(documentRptAmount*-1) AS total'))
//                ->where('documentRptAmount','>',0)
                ->with(['charofaccount'])
                ->groupBy('chartOfAccountSystemID')
                ->orderBy('total','DESC')
//                ->havingRaw('total > 0')
                ->limit(10)
                ->get();
            $dataArray = [];
            if(!empty($result) && $result->count()){
                foreach ($result as $raw){
                    if(isset($raw->glCode)){
                        $description = (isset($raw->charofaccount->AccountDescription))?$raw->charofaccount->AccountDescription:'';
                        $dataArray[0][$raw->glCode]=array(
                            'y'=>$raw->total/1000,
                            'description'=>$raw->glCode.' - '.$description
                        );
                    }
                }
                $temSeries['data'] = array_values((array)$dataArray[0]);
                $temSeries['categories'] = array_keys((array)$dataArray[0]);
                array_push($data,$temSeries);
            }
            return $this->sendResponse($data, 'Data retrieved successfully');

            case 18 :
                $currentFinancialYear = CompanyFinanceYear::currentFinanceYear($companyID);

                if(!$currentFinancialYear) {
                    return $this->sendError('Company finance year not set');
                }

                $companyCurrency = \Helper::companyCurrency($companyID);

                $actualConsumption = BudgetConsumptionService::getActualConsumption($companyID, 
                                        $currentFinancialYear->companyFinanceYearID, $glAccount);
                   
                $actual = collect($actualConsumption)->map(function ($value) {
                    return ['amount' => $value['amount']]; })->values();

                $data['financialYear'] = $currentFinancialYear;
                $data['reportingCurrency'] = $companyCurrency->reportingcurrency->CurrencyCode;
                $data['decimalPlaces'] = $companyCurrency->reportingcurrency->DecimalPlaces;
                $data['actual'] = $actual;
                $data['budget'] = Budjetdetails::with(['budget_master.segment_by',
                    'budget_master.company'])
                   ->whereHas('budget_master.company', function($query) use ($companyID) {
                        $query->where('companySystemID', $companyID);
                    })->whereHas('budget_master',function ($query) use ($currentFinancialYear) {
                        $query->where('companyFinanceYearID', $currentFinancialYear->companyFinanceYearID);
                    })->whereHas('budget_master',function ($query) {
                        $query->where('confirmedYN', 1);
                    })->whereHas('budget_master',function ($query) {
                        $query->where('approvedYN', -1);
                    })->selectRaw('SUM(budjetAmtRpt) as amount, month')
                    ->when(!empty($glAccount), function ($query) use ($glAccount) {
                        $query->whereIn('erp_budjetdetails.chartOfAccountID', $glAccount);
                    })
                    ->groupBy('month')
                    ->get();

                return $this->sendResponse($data, 'Data retrieved successfully');
            default:
                return $this->sendError('Data retrieved successfully');
        }
    }

    public function getPreDefinedWidgetData(Request $request)
    {
        $input = $request->all();

        if (!isset($input['widgetTypeID']) || (isset($input['widgetTypeID']) && is_null($input['widgetTypeID']))) {
            return $this->sendError("Widget type not found");
        }

        $companyID = isset($input['companyID']) ? $input['companyID'] : 0;
        $isGroup = \Helper::checkIsCompanyGroup($companyID);

        if($isGroup){
            $childCompanies = \Helper::getGroupCompany($companyID);
        }else{
            $childCompanies = [$companyID];
        }

        $companyData = Company::with(['reportingcurrency'])->find($companyID);

        if (!$companyData) {
            return $this->sendError("Company not found");
        }


        switch ($input['widgetTypeID']) {
            case 1:
                $currentYear = CompanyFinanceYear::currentFinanceYear($input['companyID']);

                if (!$currentYear) {
                    return $this->sendError("Current finance year is not found");
                }

                $currentFinanceYearID = $currentYear->companyFinanceYearID;


                $previosYear = CompanyFinanceYear::selectRaw("companyFinanceYearID, DATE(bigginingDate) AS startDate, DATE(endingDate) AS endDate")
                                                 ->where('companySystemID', $input['companyID'])
                                                 ->whereDate('bigginingDate', '<', $currentYear->startDate)
                                                 ->orderBy('bigginingDate', 'desc')
                                                 ->first();

                $previousFinanceYearID = $previosYear->companyFinanceYearID;


                $currentYearData = AccountsReceivableLedger::where('documentSystemID',20)
                                                ->whereIn('companySystemID', $childCompanies)
                                                ->selectRaw('customerID, documentCodeSystem ,SUM(comRptAmount) AS currentYearValue')
                                                ->whereHas('customer_invoice', function($query) use ($currentFinanceYearID) {
                                                    $query->where('companyFinanceYearID', $currentFinanceYearID);
                                                })
                                                ->with(['customer'])
                                                ->groupBy('customerID')
                                                ->orderBy('currentYearValue','DESC')
                                                ->limit(10)
                                                ->get();

                $currentYearTotalSales = AccountsReceivableLedger::where('documentSystemID',20)
                                                ->whereIn('companySystemID', $childCompanies)
                                                ->whereHas('customer_invoice', function($query) use ($currentFinanceYearID) {
                                                    $query->where('companyFinanceYearID', $currentFinanceYearID);
                                                })
                                                ->sum('comRptAmount');

                $previousYearTotalSales = AccountsReceivableLedger::where('documentSystemID',20)
                                                ->whereIn('companySystemID', $childCompanies)
                                                ->whereHas('customer_invoice', function($query) use ($previousFinanceYearID) {
                                                    $query->where('companyFinanceYearID', $previousFinanceYearID);
                                                })
                                                ->sum('comRptAmount');

                foreach ($currentYearData as $key => $value) {
                    $value->previousYearValue = AccountsReceivableLedger::where('documentSystemID',20)
                                                                        ->whereIn('companySystemID', $childCompanies)
                                                                        ->where('customerID', $value->customerID)
                                                                        ->whereHas('customer_invoice', function($query) use ($previousFinanceYearID) {
                                                                            $query->where('companyFinanceYearID', $previousFinanceYearID);
                                                                        })
                                                                        ->sum('comRptAmount');

                    $value->previousYearPercentage = ($previousYearTotalSales != 0) ? ($value->previousYearValue / $previousYearTotalSales) * 100 : 0;
                    $value->previousYearPercentage = ($currentYearTotalSales != 0) ? ($value->currentYearValue / $currentYearTotalSales) * 100 : 0;
                }
                

                $data = ['data' => $currentYearData, 'currency' => $companyData->reportingcurrency];

                return $this->sendResponse($data, "widget data retrived successfully");

                break;
            case 2:
                $overdueRecivable = GeneralLedger::whereIn('documentSystemID',[20,19,21])
                                                ->whereHas('customer', function ($query){
                                                    $query->whereRaw('customermaster.custGLAccountSystemID = erp_generalledger.chartOfAccountSystemID');
                                                })
                                                ->whereIn('companySystemID', $childCompanies)
                                                ->whereDate('documentDate', '<=', Carbon::now()->format('Y-m-d'))
                                                ->selectRaw('supplierCodeSystem,SUM(documentRptAmount) AS total, documentRptCurrencyID')
                                                ->with(['customer', 'rptcurrency'])
                                                ->groupBy('supplierCodeSystem')
                                                ->orderBy('total','DESC')
                                                ->limit(10)
                                                ->get();

                 $overduePayable = GeneralLedger::whereIn('documentSystemID',[4,11,15])
                                        ->whereHas('supplier', function ($query){
                                            $query->whereRaw('suppliermaster.liabilityAccountSysemID = erp_generalledger.chartOfAccountSystemID');
                                        })
                                        ->whereIn('companySystemID', $childCompanies)
                                        ->selectRaw('supplierCodeSystem,SUM(documentRptAmount*-1) AS total, documentRptCurrencyID')
                                        ->with(['supplier', 'rptcurrency'])
                                        ->groupBy('supplierCodeSystem')
                                        ->orderBy('total','DESC')
                                        ->limit(10)
                                        ->get();

                return $this->sendResponse(['overdueRecivable' => $overdueRecivable, 'overduePayable' => $overduePayable], "widget data retrived successfully");
                break;
            default:
                // code...
                break;
        }
    }


    public function previousYearValue($companySystemID, $companyFinanceYearID, $customerID)
    {
        $res = AccountsReceivableLedger::where('documentSystemID',20)
                                        ->whereIn('companySystemID', $companySystemID)
                                        ->whereIn('customerID', $customerID)
                                        ->selectRaw('customerID, documentCodeSystem ,SUM(comRptAmount) AS previousYearValue')
                                        ->whereHas('customer_invoice', function($query) use ($companyFinanceYearID) {
                                            $query->where('companyFinanceYearID', $companyFinanceYearID);
                                        })
                                        ->first();

        return $res ? $res->previousYearValue : 0;

    }

    public function exportWidgetExcel(Request $request)
    {
        $reportData = $this->getPreDefinedWidgetDataArray($request);

        if($request->input('widgetTypeID') == 1) {
            $templateName = "export_report.sales_log";

            $reportData['report_tittle'] = 'Sales Log';
            $reportData['report_date'] = Carbon::now()->format('d/m/Y');
    
            return \Excel::create('sales_log', function ($excel) use ($reportData, $templateName) {
                $excel->sheet('New sheet', function ($sheet) use ($reportData, $templateName) {
                    $sheet->loadView($templateName, $reportData);
                });
            })->download('xlsx');
        }

        if($request->input('widgetTypeID') == 2) {
            $templateName = "export_report.accounts_payable";
            $templateName2 = "export_report.account_receivable";
            $reportData['report_tittle'] = 'Account Payables and Receivables';
            $reportData['report_date'] = Carbon::now()->format('d/m/Y');
            return \Excel::create('accounts_payable_and_receivable', function ($excel) use ($reportData, $templateName,$templateName2) {
                $excel->sheet('Overdue Payables', function ($sheet) use ($reportData, $templateName) {
                    $sheet->loadView($templateName, $reportData);
                });
                $excel->sheet('Overdue Receivables', function ($sheet) use ($reportData, $templateName2) {
                    $sheet->loadView($templateName2, $reportData);
                });
            })->download('xlsx');
        }



       
    }

    public function getPreDefinedWidgetDataArray(Request $request)
    {
        $input = $request->all();

        if (!isset($input['widgetTypeID']) || (isset($input['widgetTypeID']) && is_null($input['widgetTypeID']))) {
            return $this->sendError("Widget type not found");
        }

        $companyID = isset($input['companyID']) ? $input['companyID'] : 0;
        $isGroup = \Helper::checkIsCompanyGroup($companyID);

        if($isGroup){
            $childCompanies = \Helper::getGroupCompany($companyID);
        }else{
            $childCompanies = [$companyID];
        }

        $companyData = Company::with(['reportingcurrency'])->find($companyID);

        if (!$companyData) {
            return $this->sendError("Company not found");
        }


        switch ($input['widgetTypeID']) {
            case 1:
                $currentYear = CompanyFinanceYear::currentFinanceYear($input['companyID']);

                if (!$currentYear) {
                    return $this->sendError("Current finance year is not found");
                }

                $currentFinanceYearID = $currentYear->companyFinanceYearID;


                $previosYear = CompanyFinanceYear::selectRaw("companyFinanceYearID, DATE(bigginingDate) AS startDate, DATE(endingDate) AS endDate")
                                                 ->where('companySystemID', $input['companyID'])
                                                 ->whereDate('bigginingDate', '<', $currentYear->startDate)
                                                 ->orderBy('bigginingDate', 'desc')
                                                 ->first();

                $previousFinanceYearID = $previosYear->companyFinanceYearID;


                $currentYearData = AccountsReceivableLedger::where('documentSystemID',20)
                                                ->whereIn('companySystemID', $childCompanies)
                                                ->selectRaw('customerID, documentCodeSystem ,SUM(comRptAmount) AS currentYearValue')
                                                ->whereHas('customer_invoice', function($query) use ($currentFinanceYearID) {
                                                    $query->where('companyFinanceYearID', $currentFinanceYearID);
                                                })
                                                ->with(['customer'])
                                                ->groupBy('customerID')
                                                ->orderBy('currentYearValue','DESC')
                                                ->limit(10)
                                                ->get();

                $currentYearTotalSales = AccountsReceivableLedger::where('documentSystemID',20)
                                                ->whereIn('companySystemID', $childCompanies)
                                                ->whereHas('customer_invoice', function($query) use ($currentFinanceYearID) {
                                                    $query->where('companyFinanceYearID', $currentFinanceYearID);
                                                })
                                                ->sum('comRptAmount');

                $previousYearTotalSales = AccountsReceivableLedger::where('documentSystemID',20)
                                                ->whereIn('companySystemID', $childCompanies)
                                                ->whereHas('customer_invoice', function($query) use ($previousFinanceYearID) {
                                                    $query->where('companyFinanceYearID', $previousFinanceYearID);
                                                })
                                                ->sum('comRptAmount');

                foreach ($currentYearData as $key => $value) {
                    $value->previousYearValue = AccountsReceivableLedger::where('documentSystemID',20)
                                                                        ->whereIn('companySystemID', $childCompanies)
                                                                        ->where('customerID', $value->customerID)
                                                                        ->whereHas('customer_invoice', function($query) use ($previousFinanceYearID) {
                                                                            $query->where('companyFinanceYearID', $previousFinanceYearID);
                                                                        })
                                                                        ->sum('comRptAmount');

                    $value->currentYearPercentage = ($value->currentYearValue / $currentYearTotalSales) * 100;
                    $value->previousYearPercentage = ($value->previousYearValue / $previousYearTotalSales) * 100;
                }
                

                $data = ['data' => $currentYearData, 'currency' => $companyData->reportingcurrency,'companyData' => $companyData];

                return $data;

                break;
            case 2:
                $overdueRecivable = GeneralLedger::whereIn('documentSystemID',[20,19,21])
                                                ->whereHas('customer', function ($query){
                                                    $query->whereRaw('customermaster.custGLAccountSystemID = erp_generalledger.chartOfAccountSystemID');
                                                })
                                                ->whereIn('companySystemID', $childCompanies)
                                                ->whereDate('documentDate', '<=', Carbon::now()->format('Y-m-d'))
                                                ->selectRaw('supplierCodeSystem,SUM(documentRptAmount) AS total, documentRptCurrencyID')
                                                ->with(['customer', 'rptcurrency'])
                                                ->groupBy('supplierCodeSystem')
                                                ->orderBy('total','DESC')
                                                ->limit(10)
                                                ->get();

                 $overduePayable = GeneralLedger::whereIn('documentSystemID',[4,11,15])
                                        ->whereHas('supplier', function ($query){
                                            $query->whereRaw('suppliermaster.liabilityAccountSysemID = erp_generalledger.chartOfAccountSystemID');
                                        })
                                        ->whereIn('companySystemID', $childCompanies)
                                        ->selectRaw('supplierCodeSystem,SUM(documentRptAmount*-1) AS total, documentRptCurrencyID')
                                        ->with(['supplier', 'rptcurrency'])
                                        ->groupBy('supplierCodeSystem')
                                        ->orderBy('total','DESC')
                                        ->limit(10)
                                        ->get();

                return ['overdueRecivable' => $overdueRecivable, 'overduePayable' => $overduePayable,'companyData' => $companyData];
                break;
            default:
                // code...
                break;
        }
    }
}
