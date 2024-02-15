<?php
/**
 * =============================================
 * -- File Name : ErpItemLedgerAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  ERP Item Leger
 * -- Author : Desh Dilshan
 * -- Create date : 30 - May 2018
 * -- Description : This file contains the all CRUD for Item Ledger
 * -- REVISION HISTORY
 * * -- Date: 31-May 2018 By: Desh Description: Added new functions named as getErpLedgerByFilter(),
 * * -- Date: 31-May 2018 By: Desh Description: Added new functions named as validateStockLedgerReport(),
 * * -- Date: 13-Jun 2018 By: Desh Description: Added new functions named as getWarehouse(),
 * * -- Date: 13-Jun 2018 By: Desh Description: Added new functions named as generateStockValuationReport(),
 */

namespace App\Http\Controllers\API;

use App\Exports\Inventory\ItemLedgerReport;
use App\Exports\Inventory\StockValuationReport;
use App\Http\Requests\API\CreateErpItemLedgerAPIRequest;
use App\Http\Requests\API\UpdateErpItemLedgerAPIRequest;
use App\Models\ErpItemLedger;
use App\Models\WarehouseMaster;
use App\Repositories\ErpItemLedgerRepository;
use App\Services\Currency\CurrencyService;
use App\Services\Excel\ExportReportToExcelService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\Company;
use App\Models\ItemMaster;
use App\Models\Unit;
use App\helper\CreateExcel;
/**
 * Class ErpItemLedgerController
 * @package App\Http\Controllers\API
 */
class ErpItemLedgerAPIController extends AppBaseController
{
    /** @var  ErpItemLedgerRepository */
    private $erpItemLedgerRepository;

    public function __construct(ErpItemLedgerRepository $erpItemLedgerRepo)
    {
        $this->erpItemLedgerRepository = $erpItemLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpItemLedgers",
     *      summary="Get a listing of the ErpItemLedgers.",
     *      tags={"ErpItemLedger"},
     *      description="Get all ErpItemLedgers",
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
     *                  @SWG\Items(ref="#/definitions/ErpItemLedger")
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
        $this->erpItemLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->erpItemLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $erpItemLedgers = $this->erpItemLedgerRepository->all();

        return $this->sendResponse($erpItemLedgers->toArray(), 'Erp Item Ledgers retrieved successfully');
    }

    /**
     * @param CreateErpItemLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/erpItemLedgers",
     *      summary="Store a newly created ErpItemLedger in storage",
     *      tags={"ErpItemLedger"},
     *      description="Store ErpItemLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpItemLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpItemLedger")
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
     *                  ref="#/definitions/ErpItemLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */

    public function store(CreateErpItemLedgerAPIRequest $request)
    {
        $input = $request->all();

        $erpItemLedgers = $this->erpItemLedgerRepository->create($input);

        return $this->sendResponse($erpItemLedgers->toArray(), 'Erp Item Ledger saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpItemLedgers/{id}",
     *      summary="Display the specified ErpItemLedger",
     *      tags={"ErpItemLedger"},
     *      description="Get ErpItemLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpItemLedger",
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
     *                  ref="#/definitions/ErpItemLedger"
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
        /** @var ErpItemLedger $erpItemLedger */
        $erpItemLedger = $this->erpItemLedgerRepository->findWithoutFail($id);

        if (empty($erpItemLedger)) {
            return $this->sendError('Erp Item Ledger not found');
        }

        return $this->sendResponse($erpItemLedger->toArray(), 'Erp Item Ledger retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateErpItemLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/erpItemLedgers/{id}",
     *      summary="Update the specified ErpItemLedger in storage",
     *      tags={"ErpItemLedger"},
     *      description="Update ErpItemLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpItemLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpItemLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpItemLedger")
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
     *                  ref="#/definitions/ErpItemLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateErpItemLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var ErpItemLedger $erpItemLedger */
        $erpItemLedger = $this->erpItemLedgerRepository->findWithoutFail($id);

        if (empty($erpItemLedger)) {
            return $this->sendError('Erp Item Ledger not found');
        }

        $erpItemLedger = $this->erpItemLedgerRepository->update($input, $id);

        return $this->sendResponse($erpItemLedger->toArray(), 'ErpItemLedger updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/erpItemLedgers/{id}",
     *      summary="Remove the specified ErpItemLedger from storage",
     *      tags={"ErpItemLedger"},
     *      description="Delete ErpItemLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpItemLedger",
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
        /** @var ErpItemLedger $erpItemLedger */
        $erpItemLedger = $this->erpItemLedgerRepository->findWithoutFail($id);

        if (empty($erpItemLedger)) {
            return $this->sendError('Erp Item Ledger not found');
        }

        $erpItemLedger->delete();

        return $this->sendResponse($id, 'Erp Item Ledger deleted successfully');
    }

    public function getErpLedger(Request $request)
    {


        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }
        $item = DB::table('erp_itemledger')->select('erp_itemledger.companySystemID', 'erp_itemledger.itemSystemCode', 'erp_itemledger.itemPrimaryCode', 'erp_itemledger.itemDescription', 'itemmaster.secondaryItemCode')
            ->join('itemmaster', 'erp_itemledger.itemSystemCode', '=', 'itemmaster.itemCodeSystem')
            ->whereIn('erp_itemledger.companySystemID', $subCompanies)
            ->where('itemmaster.financeCategoryMaster', 1)
            ->groupBy('erp_itemledger.itemSystemCode')
            //->take(50)
            ->get();

        $document = DB::table('erp_itemledger')
            ->join('erp_documentmaster', 'erp_itemledger.documentSystemID', '=', 'erp_documentmaster.documentSystemID')
            ->select('erp_itemledger.companySystemID', 'erp_itemledger.documentSystemID', 'erp_documentmaster.documentDescription', 'erp_documentmaster.documentID')
            ->whereIn('erp_itemledger.companySystemID', $subCompanies)
            ->groupBy('erp_itemledger.documentSystemID')
            ->get();

        $warehouse = DB::table('erp_itemledger')
            ->join('warehousemaster', 'erp_itemledger.wareHouseSystemCode', '=', 'warehousemaster.wareHouseSystemCode')
            ->select('erp_itemledger.companySystemID', 'erp_itemledger.wareHouseSystemCode', 'warehousemaster.wareHouseDescription', 'warehousemaster.wareHouseCode')
            ->whereIn('erp_itemledger.companySystemID', $subCompanies)
            ->groupBy('erp_itemledger.wareHouseSystemCode')
            ->get();

        $output = array(
            'item' => $item,
            'document' => $document,
            'warehouse' => $warehouse
        );
        return $this->sendResponse($output, 'Supplier Master retrieved successfully');
    }

    public function generateStockLedgerReport(Request $request)
    {

        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $company = Company::find($subCompanies[0]);
        $company_name = $company->CompanyName;


        $startDate = new Carbon($request->fromDate);
        //$startDate = $startDate->addDays(1);
        $startDate = $startDate->format('Y-m-d');

        $endDate = new Carbon($request->toDate);
        //$endDate = $endDate->addDays(1);
        $endDate = $endDate->format('Y-m-d');

        $input = $request->all();
        if (array_key_exists('Docs', $input)) {
            $docs = (array)$input['Docs'];
            $docs = collect($docs)->pluck('documentSystemID');

        }
        if (array_key_exists('Warehouse', $input)) {
            $warehouse = (array)$input['Warehouse'];
            $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');

        }

        $input = $request->all();
        $stockLedger = array();
        $data = array();
        $items = array();
        $docs = array();
        $warehouse = array();
        $grandTotalQty = 0;
        $grandTotalLocalAmount = 0;
        $grandTotalRepAmount = 0;
        if (array_key_exists('Items', $input)) {
            $items = (array)$input['Items'];
            $items = collect($items)->pluck('itemSystemCode');

        }
        if (array_key_exists('Docs', $input)) {
            $docs = (array)$input['Docs'];
            $docs = collect($docs)->pluck('documentSystemID');

        }
        if (array_key_exists('Warehouse', $input)) {
            $warehouse = (array)$input['Warehouse'];
            $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');
        }

        //foreach ($items as $item){
//            $data['openQty'] = ErpItemLedger::where('transactionDate','<',$startDate)->where('itemSystemCode',$item)->sum('inOutQty');
        /* $qty = DB::table('erp_itemledger')->selectRaw('SUM(erp_itemledger.inOutQty) as inOutQty')->where('transactionDate','<=',$endDate)->where('itemSystemCode',$item)->where('erp_itemledger.companySystemID',$request->companySystemID)->get();
         $locAmount = DB::table('erp_itemledger')->selectRaw('(erp_itemledger.inOutQty*erp_itemledger.wacLocal) as TotalWacLocal')->where('transactionDate','<',$endDate)->where('itemSystemCode',$item)->where('erp_itemledger.companySystemID',$request->companySystemID)->get();
         $repAmount = DB::table('erp_itemledger')->selectRaw('(erp_itemledger.inOutQty*erp_itemledger.wacRpt) as TotalWacRpt')->where('transactionDate','<',$endDate)->where('itemSystemCode',$item)->where('erp_itemledger.companySystemID',$request->companySystemID)->get();*/
        /*$data  = DB::table('erp_itemledger')
            ->leftJoin('units', 'erp_itemledger.unitOfMeasure', '=', 'units.UnitID')
            ->leftJoin('warehousemaster', 'erp_itemledger.wareHouseSystemCode', '=', 'warehousemaster.wareHouseSystemCode')
            ->leftJoin('employees', 'erp_itemledger.createdUserSystemID', '=', 'employees.employeeSystemID')
            ->leftJoin('erp_documentmaster', 'erp_itemledger.documentSystemID', '=', 'erp_documentmaster.documentSystemID')
            ->join('companymaster', 'erp_itemledger.companySystemID', '=', 'companymaster.companySystemID')
            ->leftJoin('currencymaster', 'erp_itemledger.wacLocalCurrencyID', '=', 'currencymaster.currencyID')
            ->leftJoin('currencymaster AS currencymaster_1', 'erp_itemledger.wacRptCurrencyID', '=', 'currencymaster_1.currencyID')
            ->join('itemmaster', 'erp_itemledger.itemSystemCode', '=', 'itemmaster.itemCodeSystem')
            ->selectRaw('erp_itemledger.companyID,
                        companymaster.CompanyName,
                        erp_itemledger.documentID,
                        erp_documentmaster.documentDescription,
                        erp_itemledger.documentCode,
                        erp_itemledger.itemPrimaryCode,
                        itemmaster.secondaryItemCode,
                        erp_itemledger.itemDescription,
                        erp_itemledger.unitOfMeasure,
                        erp_itemledger.inOutQty as inOutQty,
                        erp_itemledger.comments,
                        erp_itemledger.transactionDate,
                        units.UnitShortCode,
                        warehousemaster.wareHouseDescription,
                        employees.empName,
                        currencymaster.CurrencyName AS LocalCurrency,
                        currencymaster.DecimalPlaces AS LocalCurrencyDecimals,
                        erp_itemledger.wacLocal,
                        (erp_itemledger.inOutQty*erp_itemledger.wacLocal) as TotalWacLocal,
                        currencymaster_1.CurrencyName as RepCurrency,
                        erp_itemledger.wacRpt,
                        (erp_itemledger.inOutQty*erp_itemledger.wacRpt) as TotalWacRpt,
                        (select SUM(erp_itemledger.inOutQty)
                        from erp_itemledger
                        where erp_itemledger.transactionDate <= "'.$startDate.'"  and erp_itemledger.itemSystemCode = "'.$item.'"
                        ) as openQty,
                        (select SUM(erp_itemledger.inOutQty)
                        from erp_itemledger
                        where erp_itemledger.transactionDate <= "'.$endDate.'" and erp_itemledger.itemSystemCode = "'.$item.'"
                        ) as TotalOpenQty,
                        (select SUM(erp_itemledger.wacLocal)
                        from erp_itemledger
                        where erp_itemledger.transactionDate <= "'.$startDate.'" and erp_itemledger.itemSystemCode = "'.$item.'"
                        ) as openWacLocal,
                        (select SUM(erp_itemledger.wacRpt)
                        from erp_itemledger
                        where erp_itemledger.transactionDate <= "'.$startDate.'" and erp_itemledger.itemSystemCode = "'.$item.'"
                        ) as openWacRpt,
                        (select (SUM(erp_itemledger.inOutQty)*SUM(erp_itemledger.wacLocal))
                        from erp_itemledger
                        where erp_itemledger.transactionDate <= "'.$startDate.'" and erp_itemledger.itemSystemCode = "'.$item.'"
                        ) as openWacLocalTotal,
                        (select (SUM(erp_itemledger.inOutQty)*SUM(erp_itemledger.wacLocal))
                        from erp_itemledger
                        where erp_itemledger.transactionDate <= "'.$endDate.'" and erp_itemledger.itemSystemCode = "'.$item.'"
                        ) as openWacLocalTotalByItem,
                        (select (SUM(erp_itemledger.inOutQty)*SUM(erp_itemledger.wacRpt))
                        from erp_itemledger
                        where erp_itemledger.transactionDate <= "'.$startDate.'" and erp_itemledger.itemSystemCode = "'.$item.'"
                        ) as openWacRptTotal,
                        (select (SUM(erp_itemledger.inOutQty)*SUM(erp_itemledger.wacRpt))
                        from erp_itemledger
                        where erp_itemledger.transactionDate <= "'.$endDate.'" and erp_itemledger.itemSystemCode = "'.$item.'"
                        ) as openWacRptTotalByItem')
            ->whereIn('erp_itemledger.companySystemID',$subCompanies)
            ->where('erp_itemledger.itemSystemCode',$item)
            ->whereIn('erp_documentmaster.documentSystemID',$docs)
            ->whereIn('warehousemaster.wareHouseSystemCode',$warehouse)
            ->whereBetween('erp_itemledger.transactionDate', [$startDate, $endDate])
            ->get();*/
        /*if(count($data) > 0){
            array_push($stockLedger,$data);
        }*/
        /*if(!empty($qty) ){
            $grandTotalQty += $qty[0]->inOutQty;
        }*/

        //}
//DB::enableQueryLog();
        $data = DB::select("SELECT * FROM (SELECT
	erp_itemledger.companyID,
	companymaster.CompanyName,
	erp_itemledger.documentID,
	erp_documentmaster.documentDescription,
	erp_itemledger.documentCode,
	erp_itemledger.itemPrimaryCode,
	itemmaster.secondaryItemCode,
	erp_itemledger.itemDescription,
	erp_itemledger.unitOfMeasure,
	erp_itemledger.inOutQty,
	erp_itemledger.comments,
	erp_itemledger.transactionDate,
	units.UnitShortCode,
	warehousemaster.wareHouseDescription,
	employees.empName,
	currencymaster.CurrencyName AS LocalCurrency,
	erp_itemledger.wacLocal,
	round( erp_itemledger.inOutQty * erp_itemledger.wacLocal, currencymaster.DecimalPlaces) AS TotalWacLocal,
	currencymaster_1.CurrencyName AS RepCurrency,
	erp_itemledger.wacRpt,
	round( erp_itemledger.inOutQty * erp_itemledger.wacRpt ,currencymaster_1.DecimalPlaces) AS TotalWacRpt,
	currencymaster.DecimalPlaces AS LocalCurrencyDecimals,
	currencymaster_1.DecimalPlaces AS RptCurrencyDecimals 
FROM
	erp_itemledger
	LEFT JOIN units ON erp_itemledger.unitOfMeasure = units.UnitID
	LEFT JOIN warehousemaster ON erp_itemledger.wareHouseSystemCode = warehousemaster.wareHouseSystemCode
	LEFT JOIN employees ON erp_itemledger.createdUserSystemID = employees.employeeSystemID
	LEFT JOIN erp_documentmaster ON erp_itemledger.documentSystemID = erp_documentmaster.documentSystemID
	INNER JOIN companymaster ON erp_itemledger.companySystemID = companymaster.companySystemID
	LEFT JOIN currencymaster ON erp_itemledger.wacLocalCurrencyID = currencymaster.currencyID
	LEFT JOIN currencymaster AS currencymaster_1 ON erp_itemledger.wacRptCurrencyID = currencymaster_1.currencyID
	INNER JOIN itemmaster ON erp_itemledger.itemSystemCode = itemmaster.itemCodeSystem 
WHERE
	erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") AND
	erp_itemledger.itemSystemCode IN (" . join(',', json_decode($items)) . ") AND
	erp_itemledger.documentSystemID IN (" . join(',', json_decode($docs)) . ") AND
	erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ") AND 
	DATE(erp_itemledger.transactionDate) BETWEEN '" . $startDate . "' AND '" . $endDate . "' AND itemmaster.financeCategoryMaster = 1
	
	UNION ALL 
	
	SELECT
	erp_itemledger.companyID,
	companymaster.CompanyName,
	'' as documentID,
	'' as documentDescription,
	'Opening Balance' as documentCode,
	erp_itemledger.itemPrimaryCode,
	itemmaster.secondaryItemCode,
	erp_itemledger.itemDescription,
	erp_itemledger.unitOfMeasure,
	SUM(erp_itemledger.inOutQty) as inOutQty,
	'Opening Balance' as comments,
	'1970-01-01' as transactionDate,
	units.UnitShortCode,
	'' as wareHouseDescription,
	'' as empName,
	currencymaster.CurrencyName AS LocalCurrency,
	SUM((IFNULL(erp_itemledger.wacLocal,0) * erp_itemledger.inOutQty)) / SUM(erp_itemledger.inOutQty) as wacLocal,
	SUM( erp_itemledger.inOutQty * IFNULL(erp_itemledger.wacLocal,0)) AS TotalWacLocal,
	currencymaster_1.CurrencyName AS RepCurrency,
	SUM((IFNULL(erp_itemledger.wacRpt,0) * erp_itemledger.inOutQty)) / SUM(erp_itemledger.inOutQty) as wacRpt,
	SUM( erp_itemledger.inOutQty * IFNULL(erp_itemledger.wacRpt,0)) AS TotalWacRpt,
	currencymaster.DecimalPlaces AS LocalCurrencyDecimals, 
	currencymaster_1.DecimalPlaces AS RptCurrencyDecimals
FROM
	erp_itemledger
	LEFT JOIN units ON erp_itemledger.unitOfMeasure = units.UnitID
	INNER JOIN companymaster ON erp_itemledger.companySystemID = companymaster.companySystemID
	LEFT JOIN currencymaster ON erp_itemledger.wacLocalCurrencyID = currencymaster.currencyID
	LEFT JOIN currencymaster AS currencymaster_1 ON erp_itemledger.wacRptCurrencyID = currencymaster_1.currencyID
	INNER JOIN itemmaster ON erp_itemledger.itemSystemCode = itemmaster.itemCodeSystem 
WHERE
	erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") AND
	erp_itemledger.itemSystemCode IN (" . join(',', json_decode($items)) . ") AND
	erp_itemledger.documentSystemID IN (" . join(',', json_decode($docs)) . ") AND
	erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ") AND 
	DATE(erp_itemledger.transactionDate) < '" . $startDate . "'  AND itemmaster.financeCategoryMaster = 1 GROUP BY erp_itemledger.itemSystemCode HAVING inOutQty > 0) a ORDER BY a.transactionDate asc");
        //dd(DB::getQueryLog());
        $dataFinal = [];

        foreach ($data as $val) {
            $dataFinal[$val->itemPrimaryCode][] = $val;
        }

        $TotalWacLocal = collect($data)->pluck('TotalWacLocal')->toArray();
        $TotalWacLocal = array_sum($TotalWacLocal);

        $TotalWacRpt = collect($data)->pluck('TotalWacRpt')->toArray();
        $TotalWacRpt = array_sum($TotalWacRpt);

        $grandTotalQty = collect($data)->pluck('inOutQty')->toArray();
        $grandTotalQty = array_sum($grandTotalQty);

        $output = array(
            'grandTotalQty' => $grandTotalQty,
            'grandLocalTotal' => $TotalWacLocal,
            'grandRptTotal' => $TotalWacRpt,
            'data' => $dataFinal,
            'company_name' => $company_name
        );

        return $this->sendResponse($output, 'Item ledger record retrieved successfully');
    }





///new 


public function generateStockLedger(Request $request)
{

    if(is_array($request['companySystemID']))
    {
        $selectedCompanyId = $request['companySystemID'][0];
    }
    else
    {
        $selectedCompanyId = $request['companySystemID'];
    }
    

    $company = Company::where('companySystemID',$selectedCompanyId)->with(['localcurrency'=>function($query){
        $query->select('currencyID','CurrencyCode');
    }])->select('companySystemID','localCurrencyID')->first();

    $ItemMaster = ItemMaster::where('itemCodeSystem',$request['Items'][0]['itemSystemCode'])->select('itemCodeSystem','unit')->first();
    $unit = Unit::where('UnitID',$ItemMaster->unit)->first();

    $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
 
    if ($isGroup) {
        $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
    } else {
        $subCompanies = [$selectedCompanyId];
    }

    $startDate = new Carbon($request->fromDate);
    //$startDate = $startDate->addDays(1);
    $startDate = $startDate->format('Y-m-d');

    $endDate = new Carbon($request->toDate);
    //$endDate = $endDate->addDays(1);
    $endDate = $endDate->format('Y-m-d');

    $input = $request->all();
    if (array_key_exists('Docs', $input)) {
        $docs = (array)$input['Docs'];
        $docs = collect($docs)->pluck('documentSystemID');

    }
    if (array_key_exists('Warehouse', $input)) {
        $warehouse = (array)$input['Warehouse'];
        $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');

    }
    $type = $input['type'];
    $input = $request->all();
    $stockLedger = array();
    $data = array();
    $items = array();
    $docs = array();
    $warehouse = array();
    $grandTotalQty = 0;
    $grandTotalLocalAmount = 0;
    $grandTotalRepAmount = 0;
    if (array_key_exists('Items', $input)) {
        $items = (array)$input['Items'];
        $items = collect($items)->pluck('itemSystemCode');

    }
    if (array_key_exists('Docs', $input)) {
        $docs = (array)$input['Docs'];
        $docs = collect($docs)->pluck('documentSystemID');

    }
    if (array_key_exists('Warehouse', $input)) {
        $warehouse = (array)$input['Warehouse'];
        $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');
    }
    
    $page = $input['page'];
    $per_page = 10;
    $start = intval(($page - 1) * $per_page);

    $end = intval(($page * $per_page));



    $data = DB::select("SELECT * FROM (SELECT
erp_itemledger.companyID,
companymaster.CompanyName,
erp_itemledger.documentID,
erp_itemledger.documentSystemCode,
erp_itemledger.documentSystemID,
erp_documentmaster.documentDescription,
erp_itemledger.documentCode,
erp_itemledger.itemPrimaryCode,
itemmaster.secondaryItemCode,
erp_itemledger.itemDescription,
erp_itemledger.unitOfMeasure,
erp_itemledger.inOutQty,
erp_itemledger.comments,
erp_itemledger.transactionDate,
units.UnitShortCode,
warehousemaster.wareHouseDescription,
employees.empName,
currencymaster.CurrencyName AS LocalCurrency,
currencymaster.CurrencyCode AS LocalCurrencyCode,
erp_itemledger.wacLocal,
round( erp_itemledger.inOutQty * erp_itemledger.wacLocal, currencymaster.DecimalPlaces) AS TotalWacLocal,
currencymaster_1.CurrencyName AS RepCurrency,
erp_itemledger.wacRpt,
round( erp_itemledger.inOutQty * erp_itemledger.wacRpt ,currencymaster_1.DecimalPlaces) AS TotalWacRpt,
currencymaster.DecimalPlaces AS LocalCurrencyDecimals,
currencymaster_1.DecimalPlaces AS RptCurrencyDecimals 
FROM
erp_itemledger
LEFT JOIN units ON erp_itemledger.unitOfMeasure = units.UnitID
LEFT JOIN warehousemaster ON erp_itemledger.wareHouseSystemCode = warehousemaster.wareHouseSystemCode
LEFT JOIN employees ON erp_itemledger.createdUserSystemID = employees.employeeSystemID
LEFT JOIN erp_documentmaster ON erp_itemledger.documentSystemID = erp_documentmaster.documentSystemID
INNER JOIN companymaster ON erp_itemledger.companySystemID = companymaster.companySystemID
LEFT JOIN currencymaster ON erp_itemledger.wacLocalCurrencyID = currencymaster.currencyID
LEFT JOIN currencymaster AS currencymaster_1 ON erp_itemledger.wacRptCurrencyID = currencymaster_1.currencyID
INNER JOIN itemmaster ON erp_itemledger.itemSystemCode = itemmaster.itemCodeSystem 
WHERE
erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") AND
erp_itemledger.itemSystemCode IN (" . join(',', json_decode($items)) . ") AND
erp_itemledger.documentSystemID IN (" . join(',', json_decode($docs)) . ") AND
erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ") AND 
DATE(erp_itemledger.transactionDate) BETWEEN '" . $startDate . "' AND '" . $endDate . "' AND itemmaster.financeCategoryMaster = 1

UNION ALL 

SELECT
erp_itemledger.companyID,
erp_itemledger.documentSystemCode,
erp_itemledger.documentSystemID,
companymaster.CompanyName,
'' as documentID,
'' as documentDescription,
'Opening Balance' as documentCode,
erp_itemledger.itemPrimaryCode,
itemmaster.secondaryItemCode,
erp_itemledger.itemDescription,
erp_itemledger.unitOfMeasure,
SUM(erp_itemledger.inOutQty) as inOutQty,
'Opening Balance' as comments,
'1970-01-01' as transactionDate,
units.UnitShortCode,
'' as wareHouseDescription,
'' as empName,
currencymaster.CurrencyName AS LocalCurrency,
currencymaster.CurrencyCode AS LocalCurrencyCode,
SUM((IFNULL(erp_itemledger.wacLocal,0) * erp_itemledger.inOutQty)) / SUM(erp_itemledger.inOutQty) as wacLocal,
SUM( erp_itemledger.inOutQty * IFNULL(erp_itemledger.wacLocal,0)) AS TotalWacLocal,
currencymaster_1.CurrencyName AS RepCurrency,
SUM((IFNULL(erp_itemledger.wacRpt,0) * erp_itemledger.inOutQty)) / SUM(erp_itemledger.inOutQty) as wacRpt,
SUM( erp_itemledger.inOutQty * IFNULL(erp_itemledger.wacRpt,0)) AS TotalWacRpt,
currencymaster.DecimalPlaces AS LocalCurrencyDecimals, 
currencymaster_1.DecimalPlaces AS RptCurrencyDecimals
FROM
erp_itemledger
LEFT JOIN units ON erp_itemledger.unitOfMeasure = units.UnitID
INNER JOIN companymaster ON erp_itemledger.companySystemID = companymaster.companySystemID
LEFT JOIN currencymaster ON erp_itemledger.wacLocalCurrencyID = currencymaster.currencyID
LEFT JOIN currencymaster AS currencymaster_1 ON erp_itemledger.wacRptCurrencyID = currencymaster_1.currencyID
INNER JOIN itemmaster ON erp_itemledger.itemSystemCode = itemmaster.itemCodeSystem 
WHERE
erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") AND
erp_itemledger.itemSystemCode IN (" . join(',', json_decode($items)) . ") AND
erp_itemledger.documentSystemID IN (" . join(',', json_decode($docs)) . ") AND
erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ") AND 
DATE(erp_itemledger.transactionDate) < '" . $startDate . "'  AND itemmaster.financeCategoryMaster = 1 GROUP BY erp_itemledger.itemSystemCode HAVING inOutQty > 0) a ORDER BY a.transactionDate asc");
    //dd(DB::getQueryLog());
    

  
    $total_count = 0;
    foreach($data as $detail)
    {
        
        $total_count = $total_count + $detail->inOutQty;
     
    }


    
    $details = array_slice($data,$start, $per_page);




    if($type == 1)
    {   
        $data_obj = []; 
        $type_def = 'csv';
        foreach ($data as $detail) {


          
            if($detail->documentCode == 'Opening Balance')
            {
                $dt = '';
            }
            else
            {
                $dt = date("Y-m-d", strtotime($detail->transactionDate));
           

             
     
                //$dt = sprintf("%02s", $date);

                //($currentDay<10 ? "0" : "").$currentDay."\n";
            }
      
            if($detail->inOutQty == 0)
            {
              $qua_req = '0';
            }
            else
            {
              $qua_req = $detail->inOutQty;
            }
 
            if($detail->TotalWacLocal == 0)
            {
              $tran_amount = '0';
            }
            else
            {
              $tran_amount = number_format($detail->TotalWacLocal, $detail->LocalCurrencyDecimals, '.', ',');
            }
 
       
            $data_obj[] = array(
                //'purchaseOrderMasterID' => $order->purchaseOrderMasterID,
       
                'Document Code' => $detail->documentCode,
                'Transaction Date' => $dt,
                'Location' => $detail->wareHouseDescription,
                'Quantity' => $qua_req,
                'Amount' => $tran_amount,
            );

         
        }

 
        \Excel::create('itemTransactionHistory', function ($excel) use ($data_obj) {

            $excel->sheet('sheet name', function ($sheet) use ($data_obj) {
                $sheet->fromArray($data_obj);
                //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                $sheet->setColumnFormat(array(
                    'B' => 'yyyy-mm-dd',
                ));
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:E' . $lastrow)->getAlignment()->setWrapText(true);
           // $excel->getActiveSheet()->getStyle('V'.$i)->getNumberFormat()->setFormatCode('dd-mmm-yyyy');
        })->download($type_def);

        

        return $this->sendResponse($csv, 'successfully export');
    }
    else if($type == 2)
    {


        $info['totla_quan'] = $total_count;
        $info['data'] = $details;
        $info['total'] = count($data);
        $info['curren_page'] = ($page);
        $info['currency'] = ($company->localcurrency->CurrencyCode);
        $info['unit'] = $unit->UnitShortCode;
        
        return $this->sendResponse($info, 'Item ledger record retrieved successfully');
    }

}



    public function exportStockLedgerReport(Request $request, ExportReportToExcelService $exportReportToExcelService)
    {

        $selectedCompanyId = $request['companySystemID'];


        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $company = Company::find($subCompanies[0]);
        $company_name = $company->CompanyName;

        $startDate = new Carbon($request->fromDate);
        $from_date =  ((new Carbon($request->fromDate))->format('d/m/Y'));
        //$startDate = $startDate->addDays(1);
        $startDate = $startDate->format('Y-m-d');
        

        $endDate = new Carbon($request->toDate);
        //$endDate = $endDate->addDays(1);
        $endDate = $endDate->format('Y-m-d');

        $to_date =  ((new Carbon($request->toDate))->format('d/m/Y'));

        $input = $request->all();
        if (array_key_exists('Docs', $input)) {
            $docs = (array)$input['Docs'];
            $docs = collect($docs)->pluck('documentSystemID');

        }
        if (array_key_exists('Warehouse', $input)) {
            $warehouse = (array)$input['Warehouse'];
            $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');

        }

        $input = $request->all();
        $stockLedger = array();
        $data = array();
        $items = array();
        $docs = array();
        $warehouse = array();
        $grandTotalQty = 0;
        $grandTotalLocalAmount = 0;
        $grandTotalRepAmount = 0;
        if (array_key_exists('Items', $input)) {
            $items = (array)$input['Items'];
            $items = collect($items)->pluck('itemSystemCode');

        }
        if (array_key_exists('Docs', $input)) {
            $docs = (array)$input['Docs'];
            $docs = collect($docs)->pluck('documentSystemID');

        }
        if (array_key_exists('Warehouse', $input)) {
            $warehouse = (array)$input['Warehouse'];
            $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');
        }

//DB::enableQueryLog();
        $output = DB::select("SELECT * FROM (SELECT
	erp_itemledger.companyID,
	companymaster.CompanyName,
	erp_itemledger.documentID,
	erp_documentmaster.documentDescription,
	erp_itemledger.documentCode,
	erp_itemledger.itemPrimaryCode,
	itemmaster.secondaryItemCode,
	erp_itemledger.itemDescription,
	erp_itemledger.unitOfMeasure,
	erp_itemledger.inOutQty,
	erp_itemledger.comments,
	erp_itemledger.transactionDate,
	units.UnitShortCode,
	warehousemaster.wareHouseDescription,
	employees.empName,
	currencymaster.CurrencyName AS LocalCurrency,
	erp_itemledger.wacLocal,
	round( erp_itemledger.inOutQty * erp_itemledger.wacLocal, currencymaster.DecimalPlaces) AS TotalWacLocal,
	currencymaster_1.CurrencyName AS RepCurrency,
	erp_itemledger.wacRpt,
	round( erp_itemledger.inOutQty * erp_itemledger.wacRpt ,currencymaster_1.DecimalPlaces) AS TotalWacRpt,
	currencymaster.DecimalPlaces AS LocalCurrencyDecimals,
	currencymaster_1.DecimalPlaces AS RptCurrencyDecimals 
FROM
	erp_itemledger
	LEFT JOIN units ON erp_itemledger.unitOfMeasure = units.UnitID
	LEFT JOIN warehousemaster ON erp_itemledger.wareHouseSystemCode = warehousemaster.wareHouseSystemCode
	LEFT JOIN employees ON erp_itemledger.createdUserSystemID = employees.employeeSystemID
	LEFT JOIN erp_documentmaster ON erp_itemledger.documentSystemID = erp_documentmaster.documentSystemID
	INNER JOIN companymaster ON erp_itemledger.companySystemID = companymaster.companySystemID
	LEFT JOIN currencymaster ON erp_itemledger.wacLocalCurrencyID = currencymaster.currencyID
	LEFT JOIN currencymaster AS currencymaster_1 ON erp_itemledger.wacRptCurrencyID = currencymaster_1.currencyID
	INNER JOIN itemmaster ON erp_itemledger.itemSystemCode = itemmaster.itemCodeSystem 
WHERE
	erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") AND
	erp_itemledger.itemSystemCode IN (" . join(',', json_decode($items)) . ") AND
	erp_itemledger.documentSystemID IN (" . join(',', json_decode($docs)) . ") AND
	erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ") AND 
	DATE(erp_itemledger.transactionDate) BETWEEN '" . $startDate . "' AND '" . $endDate . "' AND itemmaster.financeCategoryMaster = 1
	
	UNION ALL 
	
	SELECT
	erp_itemledger.companyID,
	companymaster.CompanyName,
	'' as documentID,
	'' as documentDescription,
	'Opening Balance' as documentCode,
	erp_itemledger.itemPrimaryCode,
	itemmaster.secondaryItemCode,
	erp_itemledger.itemDescription,
	erp_itemledger.unitOfMeasure,
	SUM(erp_itemledger.inOutQty) as inOutQty,
	'Opening Balance' as comments,
	'1970-01-01' as transactionDate,
	units.UnitShortCode,
	'' as wareHouseDescription,
	'' as empName,
	currencymaster.CurrencyName AS LocalCurrency,
	SUM((IFNULL(erp_itemledger.wacLocal,0) * erp_itemledger.inOutQty)) / SUM(erp_itemledger.inOutQty) as wacLocal,
	SUM( erp_itemledger.inOutQty * IFNULL(erp_itemledger.wacLocal,0)) AS TotalWacLocal,
	currencymaster_1.CurrencyName AS RepCurrency,
	SUM((IFNULL(erp_itemledger.wacRpt,0) * erp_itemledger.inOutQty)) / SUM(erp_itemledger.inOutQty) as wacRpt,
	SUM( erp_itemledger.inOutQty * IFNULL(erp_itemledger.wacRpt,0)) AS TotalWacRpt,
	currencymaster.DecimalPlaces AS LocalCurrencyDecimals, 
	currencymaster_1.DecimalPlaces AS RptCurrencyDecimals
FROM
	erp_itemledger
	LEFT JOIN units ON erp_itemledger.unitOfMeasure = units.UnitID
	INNER JOIN companymaster ON erp_itemledger.companySystemID = companymaster.companySystemID
	LEFT JOIN currencymaster ON erp_itemledger.wacLocalCurrencyID = currencymaster.currencyID
	LEFT JOIN currencymaster AS currencymaster_1 ON erp_itemledger.wacRptCurrencyID = currencymaster_1.currencyID
	INNER JOIN itemmaster ON erp_itemledger.itemSystemCode = itemmaster.itemCodeSystem 
WHERE
	erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") AND
	erp_itemledger.itemSystemCode IN (" . join(',', json_decode($items)) . ") AND
	erp_itemledger.documentSystemID IN (" . join(',', json_decode($docs)) . ") AND
	erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ") AND 
	DATE(erp_itemledger.transactionDate) < '" . $startDate . "'  AND itemmaster.financeCategoryMaster = 1 GROUP BY erp_itemledger.itemSystemCode HAVING inOutQty > 0) a ORDER BY a.transactionDate asc");

       if(empty($data)) {
           $itemLedgerReportHeaderObj = new ItemLedgerReport();
           array_push($data,collect($itemLedgerReportHeaderObj->getHeader())->toArray());
       }

        foreach ($output as $val) {
            $itemLedgerReportObj = new ItemLedgerReport();

            $itemLedgerReportObj->setItemCode($val->itemPrimaryCode);
            $itemLedgerReportObj->setItemDescription($val->itemDescription);
            $itemLedgerReportObj->setPartNumber($val->secondaryItemCode);
            $itemLedgerReportObj->setTranType($val->documentID);
            $itemLedgerReportObj->setDocumentCode($val->documentCode);
            $itemLedgerReportObj->setWarehouse($val->wareHouseDescription);
            $itemLedgerReportObj->setProcessedBy($val->empName);
            $itemLedgerReportObj->setTransactionDate($val->transactionDate);
            $itemLedgerReportObj->setUom($val->UnitShortCode);
            $itemLedgerReportObj->setQty($val->inOutQty);
            $itemLedgerReportObj->setWacLocal(CurrencyService::convertNumberFormatToNumber(number_format($val->wacLocal, $val->LocalCurrencyDecimals)));
            $itemLedgerReportObj->setLocalAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->TotalWacLocal, $val->LocalCurrencyDecimals)));
            $itemLedgerReportObj->setWacRep(CurrencyService::convertNumberFormatToNumber(number_format($val->wacRpt, $val->RptCurrencyDecimals)));
            $itemLedgerReportObj->setRepAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->TotalWacRpt, $val->RptCurrencyDecimals)));
            array_push($data,collect($itemLedgerReportObj)->toArray());
        }

        $requestCurrency = null;
        $excelColumnFormat = [
            'H' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,

        ];
        $fileName = 'stock_ledger_report';
        $title = 'Stock Ledger Report';
        $path = 'inventory/report/stock_ledger_report/excel/';
        $companyCode = isset($company->CompanyID)?$company->CompanyID:'common';

        $exportToExcel = $exportReportToExcelService
            ->setTitle($title)
            ->setFileName($fileName)
            ->setPath($path)
            ->setCompanyCode($companyCode)
            ->setCompanyName($company_name)
            ->setFromDate($from_date)
            ->setToDate($to_date)
            ->setData($data)
            ->setReportType(1)
            ->setType('xls')
            ->setExcelFormat($excelColumnFormat)
            ->setCurrency($requestCurrency)
            ->setDateType(2)
            ->setDetails()
            ->generateExcel();

        if(!$exportToExcel['success'])
            return $this->sendError('Unable to export excel');

        return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));

    }

    /*validate each report*/
    public function validateStockLedgerReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'SL':
                $validator = \Validator::make($request->all(), [
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'Items' => 'required',
                    'Docs' => 'required',
                    'Warehouse' => 'required',
                    'reportType' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            default:
                return $this->sendError('Error Occurred');
        }

    }

    public function validateStockValuationReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'SL':
                $validator = \Validator::make($request->all(), [
                    'date' => 'required',
                    'warehouse' => 'required',
                    'segment' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            default:
                return $this->sendError('Error Occurred!');
        }
    }

    public function validateStockTakingReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'SL':
                $validator = \Validator::make($request->all(), [
                    'date' => 'required',
                    'warehouse' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            default:
                return $this->sendError('Error Occurred!');
        }
    }

    public function getWarehouse(Request $request)
    {

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $warehouse = WarehouseMaster::whereIn("companySystemID", $subCompanies)
            ->select('wareHouseCode', 'wareHouseSystemCode', 'wareHouseDescription')
            ->get();

        return $this->sendResponse($warehouse, 'Warehouse retrieved successfully');
    }

    public function generateStockValuationReport(Request $request)
    {

//        $validator = \Validator::make($request->all(), [
//            'daterange' => 'required',
//            'Items' => 'required',
//            'Docs' => 'required',
//            'Warehouse' => 'required',
//            'reportType' => 'required',
//        ]);

//        if ($validator->fails()) {
//            return $this->sendError($validator->messages(), 422 );
//            die();
//        }

        $date = new Carbon($request->date);
        $date = $date->format('Y-m-d');


        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $company = Company::find($subCompanies[0]);
        $company_name = $company->CompanyName;

        $input = $request->all();
        $warehouse = [];
        if (array_key_exists('warehouse', $input)) {
            $warehouse = (array)$input['warehouse'];
            $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');

        }
        $segment = [];
        if (array_key_exists('segment', $input)) {
            $segment = (array)$input['segment'];
            $segment = collect($segment)->pluck('serviceLineSystemID');
        }
        //DB::enableQueryLog();
        $sql = "SELECT
                ItemLedger.companySystemID,
                ItemLedger.companyID,
                ItemLedger.itemSystemCode,
                ItemLedger.itemPrimaryCode,
                ItemLedger.itemDescription,
                ItemLedger.unitOfMeasure,
                ItemLedger.secondaryItemCode,
                ItemLedger.UnitShortCode,
                ItemLedger.categoryDescription,
                ItemLedger.transactionDate,
                ItemLedger.LocalCurrencyDecimals,
                ItemLedger.RptCurrencyDecimals,
                round(sum(Qty),3) AS Qty,
                ItemLedger.minimumQty,               
                ItemLedger.maximunQty,      
                LocalCurrency,
            IF
                ( sum( localAmount ) / round(sum(Qty),3) IS NULL, 0, sum( localAmount ) / round(sum(Qty),3) ) AS WACLocal,
                sum( localAmount ) AS WacLocalAmount,
                RepCurrency,
            IF
                ( sum( rptAmount ) / round(sum(Qty),3) IS NULL, 0, sum( rptAmount ) / round(sum(Qty),3) ) AS WACRpt,
                sum( rptAmount ) AS WacRptAmount 
            FROM
                (
            SELECT
                erp_itemledger.companySystemID,
                erp_itemledger.companyID,
                erp_itemledger.documentSystemID,
                erp_itemledger.documentSystemCode,
                erp_itemledger.itemSystemCode,
                erp_itemledger.itemPrimaryCode,
                erp_itemledger.itemDescription,
                erp_itemledger.unitOfMeasure,
                erp_itemledger.transactionDate,
                financeitemcategorysub.categoryDescription,
                itemmaster.secondaryItemCode,
                units.UnitShortCode,
                round( erp_itemledger.inOutQty, 2 ) AS Qty,
                currencymaster.CurrencyName AS LocalCurrency,
                round( erp_itemledger.inOutQty * erp_itemledger.wacLocal, 3 ) AS localAmount,
                currencymaster_1.CurrencyName AS RepCurrency,
                round( erp_itemledger.inOutQty * erp_itemledger.wacRpt, 2 ) AS rptAmount,
                IFNULL(currencymaster.DecimalPlaces,0) AS LocalCurrencyDecimals,
                IFNULL(currencymaster_1.DecimalPlaces,0) AS RptCurrencyDecimals,               
                itemassigned.minimumQty as minimumQty,               
                itemassigned.maximunQty as maximunQty             
            FROM
                `erp_itemledger`
                INNER JOIN `itemmaster` ON `erp_itemledger`.`itemSystemCode` = `itemmaster`.`itemCodeSystem`
                INNER JOIN `financeitemcategorysub` ON `itemmaster`.`financeCategorySub` = `financeitemcategorysub`.`itemCategorySubID`
                LEFT JOIN `currencymaster` ON `erp_itemledger`.`wacLocalCurrencyID` = `currencymaster`.`currencyID`
                LEFT JOIN `currencymaster` AS `currencymaster_1` ON `erp_itemledger`.`wacRptCurrencyID` = `currencymaster_1`.`currencyID`
                LEFT JOIN `units` ON `erp_itemledger`.`unitOfMeasure` = `units`.`UnitID` 
                LEFT JOIN `itemassigned` ON `erp_itemledger`.`itemSystemCode` = `itemassigned`.`itemCodeSystem` AND itemassigned.companySystemID = erp_itemledger.companySystemID
            WHERE
                erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") 
                AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
                AND erp_itemledger.serviceLineSystemID IN (" . join(',', json_decode($segment)) . ")
                AND itemmaster.financeCategoryMaster = 1 
                AND DATE(erp_itemledger.transactionDate) <= '$date' 
                ) AS ItemLedger 
            GROUP BY
                ItemLedger.companySystemID,
                ItemLedger.itemSystemCode";
        $items = DB::select($sql);
        //dd(DB::getQueryLog());
        $finalArray = array();
        if (!empty($items)) {
            foreach ($items as $element) {
                $finalArray[$element->categoryDescription][] = $element;
            }
        }

        $GrandWacLocal = collect($items)->pluck('WacLocalAmount')->toArray();
        $GrandWacLocal = array_sum($GrandWacLocal);

        $GrandWacRpt = collect($items)->pluck('WacRptAmount')->toArray();
        $GrandWacRpt = array_sum($GrandWacRpt);

//        $TotalWacRpt = collect($data)->pluck('TotalWacRpt')->toArray();
//        $TotalWacRpt = array_sum($TotalWacRpt);

        $output = array(
            'categories' => $finalArray,
            'date' => $date,
            'subCompanies' => $subCompanies,
            'grandWacLocal' => $GrandWacLocal,
            'grandWacRpt' => $GrandWacRpt,
            'warehouse' => $request->warehouse,
            'company_name' => $company_name
        );

        return $this->sendResponse($output, 'Erp Item Ledger retrieved successfully');

    }

    public function exportStockEvaluation(Request $request,ExportReportToExcelService $exportReportToExcelService)
    {

        $input = $request->all();
        $warehouse=[];
        $data = [];
        if (array_key_exists('warehouse', $input)) {
            $warehouse = (array)$input['warehouse'];
            $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');
        }

        $segment = [];
        if (array_key_exists('segment', $input)) {
            $segment = (array)$input['segment'];
            $segment = collect($segment)->pluck('serviceLineSystemID');
        }

        $date = new Carbon($input['date']);
        $date = $date->format('Y-m-d');

        $from_date =  ((new Carbon($input['date']))->format('d/m/Y'));
        $to_date =  ((new Carbon($input['date']))->format('d/m/Y'));

        $selectedCompanyId = $request->companySystemID;
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $company = Company::find($subCompanies[0]);
        $company_name = $company->CompanyName;


        $sql = "SELECT
                ItemLedger.companySystemID,
                ItemLedger.companyID,
                ItemLedger.itemSystemCode,
                ItemLedger.itemPrimaryCode,
                ItemLedger.itemDescription,
                ItemLedger.unitOfMeasure,
                ItemLedger.secondaryItemCode,
                ItemLedger.UnitShortCode,
                ItemLedger.categoryDescription,
                ItemLedger.transactionDate,
                ItemLedger.LocalCurrencyDecimals,
                ItemLedger.RptCurrencyDecimals,
                sum( Qty ) AS Qty,
                ItemLedger.minimumQty,               
                ItemLedger.maximunQty,   
                LocalCurrency,
            IF
                ( sum( localAmount ) / sum( Qty ) IS NULL, 0, sum( localAmount ) / sum( Qty ) ) AS WACLocal,
                sum( localAmount ) AS WacLocalAmount,
                RepCurrency,
            IF
                ( sum( rptAmount ) / sum( Qty ) IS NULL, 0, sum( rptAmount ) / sum( Qty ) ) AS WACRpt,
                sum( rptAmount ) AS WacRptAmount 
            FROM
                (
            SELECT
                erp_itemledger.companySystemID,
                erp_itemledger.companyID,
                erp_itemledger.documentSystemID,
                erp_itemledger.documentSystemCode,
                erp_itemledger.itemSystemCode,
                erp_itemledger.itemPrimaryCode,
                erp_itemledger.itemDescription,
                erp_itemledger.unitOfMeasure,
                erp_itemledger.transactionDate,
                financeitemcategorysub.categoryDescription,
                itemmaster.secondaryItemCode,
                units.UnitShortCode,
                round( erp_itemledger.inOutQty, 2 ) AS Qty,
                currencymaster.CurrencyName AS LocalCurrency,
                round( erp_itemledger.inOutQty * erp_itemledger.wacLocal, 3 ) AS localAmount,
                currencymaster_1.CurrencyName AS RepCurrency,
                round( erp_itemledger.inOutQty * erp_itemledger.wacRpt, 2 ) AS rptAmount,
                currencymaster.DecimalPlaces AS LocalCurrencyDecimals,
                currencymaster_1.DecimalPlaces AS RptCurrencyDecimals,
                itemassigned.minimumQty as minimumQty,               
                itemassigned.maximunQty as maximunQty                  
            FROM
                `erp_itemledger`
                INNER JOIN `itemmaster` ON `erp_itemledger`.`itemSystemCode` = `itemmaster`.`itemCodeSystem`
                INNER JOIN `financeitemcategorysub` ON `itemmaster`.`financeCategorySub` = `financeitemcategorysub`.`itemCategorySubID`
                LEFT JOIN `currencymaster` ON `erp_itemledger`.`wacLocalCurrencyID` = `currencymaster`.`currencyID`
                LEFT JOIN `currencymaster` AS `currencymaster_1` ON `erp_itemledger`.`wacRptCurrencyID` = `currencymaster_1`.`currencyID`
                LEFT JOIN `units` ON `erp_itemledger`.`unitOfMeasure` = `units`.`UnitID` 
                LEFT JOIN `itemassigned` ON `erp_itemledger`.`itemSystemCode` = `itemassigned`.`itemCodeSystem` AND itemassigned.companySystemID = erp_itemledger.companySystemID
            WHERE
                erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") 
                AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
                AND erp_itemledger.serviceLineSystemID IN (" . join(',', json_decode($segment)) . ")
                AND itemmaster.financeCategoryMaster = 1 
                AND DATE(erp_itemledger.transactionDate) <= '$date' 
                ) AS ItemLedger 
            GROUP BY
                ItemLedger.companySystemID,
                ItemLedger.itemSystemCode";
        $items = DB::select($sql);

        if(empty($data)) {
            $stockValuationReportHeader = new StockValuationReport();
            array_push($data,collect($stockValuationReportHeader->getHeader())->toArray());
        }

        foreach ($items as $val) {

            $stockValuationReport = new StockValuationReport();

            $stockValuationReport->setCategory($val->categoryDescription);
            $stockValuationReport->setItemCode($val->itemPrimaryCode);
            $stockValuationReport->setItemDescription($val->itemDescription);
            $stockValuationReport->setUom($val->UnitShortCode);
            $stockValuationReport->setPartNumber($val->secondaryItemCode);
            $stockValuationReport->setMinQty($val->minimumQty);
            $stockValuationReport->setMaxQty($val->maximunQty);
            $stockValuationReport->setQty($val->Qty);
            $stockValuationReport->setWacLocal(CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal, $val->LocalCurrencyDecimals)));
            $stockValuationReport->setLocalAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->WacLocalAmount, $val->LocalCurrencyDecimals)));
            $stockValuationReport->setWacRep(CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt, $val->LocalCurrencyDecimals)));
            $stockValuationReport->setRepAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->WacRptAmount, $val->LocalCurrencyDecimals)));

            array_push($data,collect($stockValuationReport)->toArray());
        }

        $fileName = 'stock_valuation_report';
        $title = 'Stock Valuation Report';
        $path = 'inventory/report/stock_valuation_report/excel/';
        $cur = NULL;
        $companyCode = isset($company->CompanyID)?$company->CompanyID:'common';
        $excelColumnFormat = [
            'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];


        $exportToExcel = $exportReportToExcelService
            ->setTitle($title)
            ->setFileName($fileName)
            ->setPath($path)
            ->setCompanyCode($companyCode)
            ->setCompanyName($company_name)
            ->setFromDate($from_date)
            ->setToDate($to_date)
            ->setData($data)
            ->setReportType(2)
            ->setType('xls')
            ->setExcelFormat($excelColumnFormat)
            ->setCurrency($cur)
            ->setDateType(2)
            ->setDetails()
            ->generateExcel();

        if(!$exportToExcel['success'])
            return $this->sendError('Unable to export excel');

        return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));


    }

    public function generateStockTakingReport(Request $request)
    {

        $search = [];
        $searchQry = "";

        $date = new Carbon($request->date);
        $date = $date->format('Y-m-d');

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }
        
        $company = Company::find($subCompanies[0]);
        $company_name = $company->CompanyName;

//        $input = $request->all();
        if (array_key_exists('warehouse', $input)) {
            $warehouse = (array)$input['warehouse'];
            $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');

        }

        if (isset($request->itemCode)) {
            if (!empty($request->itemCode)) {
                $search[] = "erp_itemledger.itemPrimaryCode LIKE '%" . $request->itemCode . "%'";
            }
        }

        if (isset($request->itemDescription)) {
            if (!empty($request->itemDescription)) {
                $search[] = "erp_itemledger.itemDescription LIKE '%" . $request->itemDescription . "%'";
            }
        }

        if (isset($request->partNumber)) {
            if (!empty($request->partNumber)) {
                $search[] = "itemmaster.secondaryItemCode LIKE '%" . $request->partNumber . "%'";
            }
        }

        if ($search) {
            $searchQry = "AND (" . join(' OR ', $search) . ")";
        }

        /*$sql = "SELECT
	finalStockTaking.companySystemID,
	finalStockTaking.companyID,
	finalStockTaking.wareHouseSystemCode,
	finalStockTaking.wareHouseDescription,
	finalStockTaking.itemSystemCode,
	finalStockTaking.itemPrimaryCode,
	finalStockTaking.itemDescription,
	finalStockTaking.partNumber,
	finalStockTaking.unitOfMeasure,
	finalStockTaking.UnitShortCode,
	round(sum(stockQty),8) as StockQty,
	IFNULL(round((sum(AmountLocal)/sum(stockQty)),8),0) as AvgCostLocal,
	IFNULL(round((sum(AmountRpt)/sum(stockQty)),8),0) as AvgCostRpt,
	IFNULL(round((sum(AmountLocal)/sum(stockQty)),8),0) * round(sum(stockQty),8) as TotalCostLocal,
	IFNULL(round((sum(AmountRpt)/sum(stockQty)),8),0) * round(sum(stockQty),8) as TotalCostRpt,
	finalStockTaking.BinLocation,
	LocalCurrencyDecimals,
	RptCurrencyDecimals
FROM
(
SELECT
	erp_itemledger.itemLedgerAutoID,
	erp_itemledger.companySystemID,
	erp_itemledger.companyID,
	erp_itemledger.wareHouseSystemCode,
	warehousemaster.wareHouseDescription,
	erp_itemledger.itemSystemCode,
	itemmaster.primaryCode AS itemPrimaryCode,
	itemmaster.itemDescription,
	itemmaster.secondaryItemCode AS partNumber,
	erp_itemledger.unitOfMeasure,
	units.UnitShortCode,
	inOutQty AS stockQty,
	wacRpt * inOutQty AS AmountRpt,
	wacLocal * inOutQty AS AmountLocal,
	StockTaking_BinLocation.binLocationDes AS BinLocation,
	currencymaster.DecimalPlaces AS LocalCurrencyDecimals,
	currencymaster_1.DecimalPlaces AS RptCurrencyDecimals

FROM
	erp_itemledger
	LEFT JOIN itemmaster ON erp_itemledger.itemSystemCode = itemmaster.itemCodeSystem
	AND itemmaster.financeCategoryMaster = 1
	LEFT JOIN currencymaster ON erp_itemledger.wacLocalCurrencyID = currencymaster.currencyID
	LEFT JOIN currencymaster AS currencymaster_1 ON erp_itemledger.wacRptCurrencyID = currencymaster_1.currencyID
	LEFT JOIN warehousemaster ON erp_itemledger.wareHouseSystemCode = warehousemaster.wareHouseSystemCode
	LEFT JOIN units ON erp_itemledger.unitOfMeasure = units.UnitID
	LEFT JOIN (
SELECT
	warehouseitems.companySystemID,
	warehouseitems.companyID,
	warehouseitems.warehouseSystemCode,
	warehouseitems.itemSystemCode,
	warehouseitems.binNumber,
	warehousebinlocationmaster.binLocationDes
FROM
	warehouseitems
	INNER JOIN warehousebinlocationmaster ON warehouseitems.binNumber = warehousebinlocationmaster.binLocationID
	AND warehouseitems.warehouseSystemCode = warehousebinlocationmaster.wareHouseSystemCode
	AND warehouseitems.companySystemID = warehousebinlocationmaster.companySystemID
WHERE
	warehouseitems.companySystemID IN (".join(',',$subCompanies).") AND
	warehouseitems.warehouseSystemCode IN (".join(',',json_decode($warehouse)).")
	) AS StockTaking_BinLocation ON erp_itemledger.companySystemID = StockTaking_BinLocation.companySystemID
	AND erp_itemledger.wareHouseSystemCode = StockTaking_BinLocation.warehouseSystemCode
	AND erp_itemledger.itemSystemCode = StockTaking_BinLocation.itemSystemCode
WHERE
	erp_itemledger.fromDamagedTransactionYN = 0
	AND DATE(erp_itemledger.transactionDate) <= '$date'
	AND erp_itemledger.companySystemID IN (".join(',',$subCompanies).")
	AND erp_itemledger.wareHouseSystemCode IN (".join(',',json_decode($warehouse)).")
	AND itemmaster.financeCategoryMaster = 1
ORDER BY
	erp_itemledger.itemSystemCode ASC) AS finalStockTaking
	GROUP BY companySystemID,wareHouseSystemCode,itemSystemCode";*/

        $sql = "SELECT
	ItemLedger.companySystemID,
	ItemLedger.companyID,
	ItemLedger.itemSystemCode,
	ItemLedger.itemPrimaryCode,
	ItemLedger.itemDescription,
	ItemLedger.unitOfMeasure,
	ItemLedger.UnitShortCode,
	ItemLedger.partNumber,
	ItemLedger.categoryDescription,
	ItemLedger.wareHouseSystemCode,
	ItemLedger.wareHouseDescription,
	sum( Qty ) AS StockQty,
	LocalCurrency,
IF
	( sum( localAmount ) / sum( Qty ) IS NULL, 0, sum( localAmount ) / sum( Qty ) ) AS AvgCostLocal,
	sum( localAmount ) AS TotalCostLocal,
	RepCurrency,
IF
	( sum( rptAmount ) / sum( Qty ) IS NULL, 0, sum( rptAmount ) / sum( Qty ) ) AS AvgCostRpt,
	sum( rptAmount ) AS TotalCostRpt,
	ItemLedger.LocalCurrencyDecimals,
	ItemLedger.RptCurrencyDecimals 
FROM
	(
SELECT
	erp_itemledger.companySystemID,
	erp_itemledger.companyID,
	erp_itemledger.wareHouseSystemCode,
	warehousemaster.wareHouseDescription,
	erp_itemledger.documentSystemID,
	erp_itemledger.documentSystemCode,
	erp_itemledger.itemSystemCode,
	erp_itemledger.itemPrimaryCode,
	erp_itemledger.itemDescription,
	erp_itemledger.unitOfMeasure,
	units.UnitShortCode,
	financeitemcategorysub.categoryDescription,
	itemmaster.secondaryItemCode AS partNumber,
	units.UnitShortCode AS UOM,
	round( erp_itemledger.inOutQty, 2 ) AS Qty,
	currencymaster.CurrencyName AS LocalCurrency,
	round( erp_itemledger.inOutQty * erp_itemledger.wacLocal, 3 ) AS localAmount,
	currencymaster_1.CurrencyName AS RepCurrency,
	round( erp_itemledger.inOutQty * erp_itemledger.wacRpt, 2 ) AS rptAmount,
	currencymaster.DecimalPlaces AS LocalCurrencyDecimals,
	currencymaster_1.DecimalPlaces AS RptCurrencyDecimals 
FROM
	`erp_itemledger`
	INNER JOIN `itemmaster` ON `erp_itemledger`.`itemSystemCode` = `itemmaster`.`itemCodeSystem`
	INNER JOIN `financeitemcategorysub` ON `itemmaster`.`financeCategorySub` = `financeitemcategorysub`.`itemCategorySubID`
	LEFT JOIN `currencymaster` ON `erp_itemledger`.`wacLocalCurrencyID` = `currencymaster`.`currencyID`
	LEFT JOIN `currencymaster` AS `currencymaster_1` ON `erp_itemledger`.`wacRptCurrencyID` = `currencymaster_1`.`currencyID`
	LEFT JOIN `units` ON `erp_itemledger`.`unitOfMeasure` = `units`.`UnitID`
	LEFT JOIN warehousemaster ON erp_itemledger.wareHouseSystemCode = warehousemaster.wareHouseSystemCode 
WHERE
	itemmaster.financeCategoryMaster = 1 
	AND DATE(erp_itemledger.transactionDate) <= '$date' 
	AND erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ")
	AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
	$searchQry
	) AS ItemLedger 
GROUP BY
	ItemLedger.companySystemID,
	ItemLedger.itemSystemCode,
	ItemLedger.wareHouseSystemCode
	HAVING 
	(StockQty != 0 OR AvgCostLocal != 0 OR TotalCostLocal != 0 OR AvgCostRpt != 0 OR AvgCostRpt != 0 OR TotalCostRpt != 0)";

        $data = DB::select($sql);

        $dataRec = \DataTables::of($data)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->with('company_name', $company_name)
            ->make(true);

        return $dataRec;
    }

    public function exportStockTaking(Request $request)
    {
        $search = [];
        $searchQry = "";

        $date = new Carbon($request->date);
        $date = $date->format('Y-m-d');

        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $company = Company::find($subCompanies[0]);
        $company_name = $company->CompanyName;

        
        $from_date =  ((new Carbon($request->date))->format('d/m/Y'));
        $to_date =  ((new Carbon($request->date))->format('d/m/Y'));

        $input = $request->all();
        if (array_key_exists('warehouse', $input)) {
            $warehouse = (array)$input['warehouse'];
            $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');
        }

        if (isset($request->itemCode)) {
            if (!empty($request->itemCode)) {
                $search[] = "erp_itemledger.itemPrimaryCode LIKE '%" . $request->itemCode . "%'";
            }
        }

        if (isset($request->itemDescription)) {
            if (!empty($request->itemDescription)) {
                $search[] = "erp_itemledger.itemDescription LIKE '%" . $request->itemDescription . "%'";
            }
        }

        if (isset($request->partNumber)) {
            if (!empty($request->partNumber)) {
                $search[] = "itemmaster.secondaryItemCode LIKE '%" . $request->partNumber . "%'";
            }
        }

        if ($search) {
            $searchQry = "AND (" . join(' OR ', $search) . ")";
        }

        /*$stockTaking = DB::select("SELECT
	finalStockTaking.companySystemID,
	finalStockTaking.companyID,
	finalStockTaking.wareHouseSystemCode,
	finalStockTaking.wareHouseDescription,
	finalStockTaking.itemSystemCode,
	finalStockTaking.itemPrimaryCode,
	finalStockTaking.itemDescription,
	finalStockTaking.partNumber,
	finalStockTaking.unitOfMeasure,
	finalStockTaking.UnitShortCode,
	round(sum(stockQty),8) as StockQty,
	IFNULL(round((sum(AmountLocal)/sum(stockQty)),8),0) as AvgCostLocal,
	IFNULL(round((sum(AmountRpt)/sum(stockQty)),8),0) as AvgCostRpt,
	IFNULL(round((sum(AmountLocal)/sum(stockQty)),8),0) * round(sum(stockQty),8) as TotalCostLocal,
	IFNULL(round((sum(AmountRpt)/sum(stockQty)),8),0) * round(sum(stockQty),8) as TotalCostRpt,
	finalStockTaking.BinLocation,
	LocalCurrencyDecimals, 
	RptCurrencyDecimals
FROM
(
SELECT
	erp_itemledger.companySystemID,
	erp_itemledger.companyID,
	erp_itemledger.wareHouseSystemCode,
	warehousemaster.wareHouseDescription,
	erp_itemledger.itemSystemCode,
	itemmaster.primaryCode AS itemPrimaryCode,
	itemmaster.itemDescription,
	itemmaster.secondaryItemCode AS partNumber,
	erp_itemledger.unitOfMeasure,
	units.UnitShortCode,
	inOutQty AS stockQty,
	wacRpt * inOutQty AS AmountRpt,
	wacLocal * inOutQty AS AmountLocal,
	StockTaking_BinLocation.binLocationDes AS BinLocation,
	currencymaster.DecimalPlaces AS LocalCurrencyDecimals, 
	currencymaster_1.DecimalPlaces AS RptCurrencyDecimals 
FROM
	erp_itemledger
	LEFT JOIN itemmaster ON erp_itemledger.itemSystemCode = itemmaster.itemCodeSystem 
	AND itemmaster.financeCategoryMaster = 1
	LEFT JOIN currencymaster ON erp_itemledger.wacLocalCurrencyID = currencymaster.currencyID
	LEFT JOIN currencymaster AS currencymaster_1 ON erp_itemledger.wacRptCurrencyID = currencymaster_1.currencyID
	LEFT JOIN warehousemaster ON erp_itemledger.wareHouseSystemCode = warehousemaster.wareHouseSystemCode
	LEFT JOIN units ON erp_itemledger.unitOfMeasure = units.UnitID
	LEFT JOIN (
SELECT
	warehouseitems.companySystemID,
	warehouseitems.companyID,
	warehouseitems.warehouseSystemCode,
	warehouseitems.itemSystemCode,
	warehouseitems.binNumber,
	warehousebinlocationmaster.binLocationDes 
FROM
	warehouseitems
	INNER JOIN warehousebinlocationmaster ON warehouseitems.binNumber = warehousebinlocationmaster.binLocationID 
	AND warehouseitems.warehouseSystemCode = warehousebinlocationmaster.wareHouseSystemCode 
	AND warehouseitems.companySystemID = warehousebinlocationmaster.companySystemID 
WHERE
	warehouseitems.companySystemID IN (".join(',',$subCompanies).") AND
	warehouseitems.warehouseSystemCode IN (".join(',',json_decode($warehouse)).")
	) AS StockTaking_BinLocation ON erp_itemledger.companySystemID = StockTaking_BinLocation.companySystemID 
	AND erp_itemledger.wareHouseSystemCode = StockTaking_BinLocation.warehouseSystemCode 
	AND erp_itemledger.itemSystemCode = StockTaking_BinLocation.itemSystemCode 
WHERE
	erp_itemledger.fromDamagedTransactionYN = 0 
	AND DATE(erp_itemledger.transactionDate) <= '$date'  
	AND erp_itemledger.companySystemID IN (".join(',',$subCompanies).")
	AND erp_itemledger.wareHouseSystemCode IN (".join(',',json_decode($warehouse)).")
	AND itemmaster.financeCategoryMaster = 1  
ORDER BY
	erp_itemledger.itemSystemCode ASC) AS finalStockTaking
	GROUP BY companySystemID,wareHouseSystemCode,itemSystemCode");*/

        $sql = "SELECT
	ItemLedger.companySystemID,
	ItemLedger.companyID,
	ItemLedger.itemSystemCode,
	ItemLedger.itemPrimaryCode,
	ItemLedger.itemDescription,
	ItemLedger.unitOfMeasure,
	ItemLedger.UnitShortCode,
	ItemLedger.partNumber,
	ItemLedger.categoryDescription,
	ItemLedger.wareHouseSystemCode,
	ItemLedger.wareHouseDescription,
	sum( Qty ) AS StockQty,
	LocalCurrency,
IF
	( sum( localAmount ) / sum( Qty ) IS NULL, 0, sum( localAmount ) / sum( Qty ) ) AS AvgCostLocal,
	sum( localAmount ) AS TotalCostLocal,
	RepCurrency,
IF
	( sum( rptAmount ) / sum( Qty ) IS NULL, 0, sum( rptAmount ) / sum( Qty ) ) AS AvgCostRpt,
	sum( rptAmount ) AS TotalCostRpt,
	ItemLedger.LocalCurrencyDecimals,
	ItemLedger.RptCurrencyDecimals 
FROM
	(
SELECT
	erp_itemledger.companySystemID,
	erp_itemledger.companyID,
	erp_itemledger.wareHouseSystemCode,
	warehousemaster.wareHouseDescription,
	erp_itemledger.documentSystemID,
	erp_itemledger.documentSystemCode,
	erp_itemledger.itemSystemCode,
	erp_itemledger.itemPrimaryCode,
	erp_itemledger.itemDescription,
	erp_itemledger.unitOfMeasure,
	units.UnitShortCode,
	financeitemcategorysub.categoryDescription,
	itemmaster.secondaryItemCode AS partNumber,
	units.UnitShortCode AS UOM,
	round( erp_itemledger.inOutQty, 2 ) AS Qty,
	currencymaster.CurrencyName AS LocalCurrency,
	round( erp_itemledger.inOutQty * erp_itemledger.wacLocal, 3 ) AS localAmount,
	currencymaster_1.CurrencyName AS RepCurrency,
	round( erp_itemledger.inOutQty * erp_itemledger.wacRpt, 2 ) AS rptAmount,
	currencymaster.DecimalPlaces AS LocalCurrencyDecimals,
	currencymaster_1.DecimalPlaces AS RptCurrencyDecimals 
FROM
	`erp_itemledger`
	INNER JOIN `itemmaster` ON `erp_itemledger`.`itemSystemCode` = `itemmaster`.`itemCodeSystem`
	INNER JOIN `financeitemcategorysub` ON `itemmaster`.`financeCategorySub` = `financeitemcategorysub`.`itemCategorySubID`
	LEFT JOIN `currencymaster` ON `erp_itemledger`.`wacLocalCurrencyID` = `currencymaster`.`currencyID`
	LEFT JOIN `currencymaster` AS `currencymaster_1` ON `erp_itemledger`.`wacRptCurrencyID` = `currencymaster_1`.`currencyID`
	LEFT JOIN `units` ON `erp_itemledger`.`unitOfMeasure` = `units`.`UnitID`
	LEFT JOIN warehousemaster ON erp_itemledger.wareHouseSystemCode = warehousemaster.wareHouseSystemCode 
WHERE
	itemmaster.financeCategoryMaster = 1 
	AND DATE(erp_itemledger.transactionDate) <= '$date' 
	AND erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ")
	AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
	$searchQry
	) AS ItemLedger 
GROUP BY
	ItemLedger.companySystemID,
	ItemLedger.itemSystemCode,
	ItemLedger.wareHouseSystemCode
        HAVING
        (StockQty != 0 OR AvgCostLocal != 0 OR TotalCostLocal != 0 OR AvgCostRpt != 0 OR AvgCostRpt != 0 OR TotalCostRpt != 0)";

        $stockTaking = DB::select($sql);

        foreach ($stockTaking as $val) {
            $data[] = array(
                'Warehouse' => $val->wareHouseDescription,
                'Item Code' => $val->itemPrimaryCode,
                'Item Description' => $val->itemDescription,
                'UOM' => $val->UnitShortCode,
                'Part No / Ref.Number' => $val->partNumber,
                'Stock Qty' => $val->StockQty,
                'Avg Cost Rpt' => round($val->AvgCostRpt, $val->RptCurrencyDecimals),
                'Avg Cost Local' => round($val->AvgCostLocal, $val->LocalCurrencyDecimals),
                'Total Cost Rpt' => round($val->TotalCostRpt, $val->RptCurrencyDecimals),
                'Total Cost Local' => round($val->TotalCostLocal, $val->LocalCurrencyDecimals),
                'Physical Qty' => '',
                'Bin Location' => ''
            );
        }


        $fileName = 'stock_taking_report';
        $title = 'Stock Taking Report';
        $path = 'inventory/report/stock_taking_report/excel/';
        $cur = NULL;
        $detail_array = array('type' => 2,'from_date'=>$from_date,'to_date'=>$to_date,'company_name'=>$company_name,'cur'=>$cur,'title'=>$title);
        $basePath = CreateExcel::process($data,$request->type,$fileName,$path,$detail_array);

        if($basePath == '')
        {
             return $this->sendError('Unable to export excel');
        }
        else
        {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }
    }



    public function getItemStockDetails(Request $request)
    {

        $company = Company::where('companySystemID',$request['company_id'])->with(['localcurrency'=>function($query){
            $query->select('currencyID','CurrencyCode');
        }])->select('companySystemID','localCurrencyID')->first();

 
        $date = new Carbon($request->date);
        $date = $date->format('Y-m-d');

        $page = $request->page;
        $per_page = 10;
        $start = intval(($page - 1) * $per_page);
    
        $end = intval(($page * $per_page));
    
 

        if(is_array($request['company_id']))
        {
            $selectedCompanyId = $request['company_id'][0];
        }
        else
        {
            $selectedCompanyId = $request['company_id'];
        }
       
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $item_code = $request['item_code'];
        $input = $request->all();

        $warehouse = WarehouseMaster::pluck('wareHouseSystemCode');


        //DB::enableQueryLog();
        $sql = "SELECT
                ItemLedger.companySystemID,
                ItemLedger.wareHouseSystemCode,
                ItemLedger.companyID,
                ItemLedger.itemSystemCode,
                ItemLedger.itemPrimaryCode,
                ItemLedger.itemDescription,
                ItemLedger.unitOfMeasure,
                ItemLedger.secondaryItemCode,
                ItemLedger.UnitShortCode,
                ItemLedger.categoryDescription,
                ItemLedger.transactionDate,
                ItemLedger.LocalCurrencyDecimals,
                ItemLedger.RptCurrencyDecimals,
                round(sum(Qty),3) AS Qty,
                ItemLedger.minimumQty,               
                ItemLedger.maximunQty,      
                LocalCurrency,
                LocalCurrencyCode,
                warehouse,
                rol,
            IF
                ( sum( localAmount ) / round(sum(Qty),3) IS NULL, 0, sum( localAmount ) / round(sum(Qty),3) ) AS WACLocal,
                sum( localAmount ) AS WacLocalAmount,
                RepCurrency,
            IF
                ( sum( rptAmount ) / round(sum(Qty),3) IS NULL, 0, sum( rptAmount ) / round(sum(Qty),3) ) AS WACRpt,
                sum( rptAmount ) AS WacRptAmount 
            FROM
                (
            SELECT
                erp_itemledger.companySystemID,
                erp_itemledger.companyID,
                erp_itemledger.documentSystemID,
                erp_itemledger.documentSystemCode,
                erp_itemledger.itemSystemCode,
                erp_itemledger.itemPrimaryCode,
                erp_itemledger.itemDescription,
                erp_itemledger.unitOfMeasure,
                erp_itemledger.transactionDate,
                erp_itemledger.wareHouseSystemCode,
                financeitemcategorysub.categoryDescription,
                itemmaster.secondaryItemCode,
                units.UnitShortCode,
                round( erp_itemledger.inOutQty, 2 ) AS Qty,
                currencymaster.CurrencyName AS LocalCurrency,
                currencymaster.CurrencyCode AS LocalCurrencyCode,
                round( erp_itemledger.inOutQty * erp_itemledger.wacLocal, 3 ) AS localAmount,
                currencymaster_1.CurrencyName AS RepCurrency,
                round( erp_itemledger.inOutQty * erp_itemledger.wacRpt, 2 ) AS rptAmount,
                currencymaster.DecimalPlaces AS LocalCurrencyDecimals,
                currencymaster_1.DecimalPlaces AS RptCurrencyDecimals,               
                itemassigned.minimumQty as minimumQty,               
                itemassigned.maximunQty as maximunQty,   
                itemassigned.rolQuantity as rol,   
                warehousemaster.wareHouseDescription AS warehouse          
            FROM
                `erp_itemledger`
                INNER JOIN `warehousemaster` ON `erp_itemledger`.`wareHouseSystemCode` = `warehousemaster`.`wareHouseSystemCode`
                INNER JOIN `itemmaster` ON `erp_itemledger`.`itemSystemCode` = `itemmaster`.`itemCodeSystem`
                INNER JOIN `financeitemcategorysub` ON `itemmaster`.`financeCategorySub` = `financeitemcategorysub`.`itemCategorySubID`
                LEFT JOIN `currencymaster` ON `erp_itemledger`.`wacLocalCurrencyID` = `currencymaster`.`currencyID`
                LEFT JOIN `currencymaster` AS `currencymaster_1` ON `erp_itemledger`.`wacRptCurrencyID` = `currencymaster_1`.`currencyID`
                LEFT JOIN `units` ON `erp_itemledger`.`unitOfMeasure` = `units`.`UnitID` 
                LEFT JOIN `itemassigned` ON `erp_itemledger`.`itemSystemCode` = `itemassigned`.`itemCodeSystem` AND itemassigned.companySystemID = erp_itemledger.companySystemID
            WHERE
                erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") 
                AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
                AND erp_itemledger.itemSystemCode = $item_code 
                AND itemmaster.financeCategoryMaster = 1 
                ) AS ItemLedger 
            GROUP BY
                ItemLedger.wareHouseSystemCode";
        $items = DB::select($sql);
        $total_count = 0;
        foreach($items as $item)
        {
            $total_count = $total_count + $item->Qty;
        }


        $details = array_slice($items,$start, $per_page);



        $ItemMaster = ItemMaster::where('itemCodeSystem',$item_code)->select('itemCodeSystem','unit')->first();
        $unit = Unit::where('UnitID',$ItemMaster->unit)->first();


        $info['count'] = $total_count;
        $info['datas'] = $details;
        $info['total'] = count($items);
        $info['curren_page'] = ($page);
        $info['currency'] = ($company->localcurrency->CurrencyCode);
        $info['unit'] = $unit->UnitShortCode;   
        return $this->sendResponse($info, 'Erp Item Ledger retrieved successfully');

    }


    public function getErpLedgerItems(Request $request)
    {


        $selectedCompanyId = $request['selectedCompanyId'];


        $category = (array)$request['category'];
        $category = collect($category)->pluck('id');
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }
        $item = DB::table('erp_itemledger')->select('erp_itemledger.companySystemID', 'erp_itemledger.itemSystemCode', 'erp_itemledger.itemPrimaryCode', 'erp_itemledger.itemDescription', 'itemmaster.secondaryItemCode')
            ->join('itemmaster', 'erp_itemledger.itemSystemCode', '=', 'itemmaster.itemCodeSystem')
            ->whereIn('erp_itemledger.companySystemID', $subCompanies)
            ->whereIn('itemmaster.financeCategorySub', $category)
            ->where('itemmaster.financeCategoryMaster', 1)
            ->groupBy('erp_itemledger.itemSystemCode')
            //->take(50)
            ->get();


       


        $output = array(
            'item' => $item
        );
        return $this->sendResponse($output, 'Supplier Master retrieved successfully');
    }





}
