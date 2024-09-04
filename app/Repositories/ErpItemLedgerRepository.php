<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\ErpItemLedger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ErpItemLedgerRepository
 * @package App\Repositories
 * @version May 30, 2018, 10:37 am UTC
 *
 * @method ErpItemLedger findWithoutFail($id, $columns = ['*'])
 * @method ErpItemLedger find($id, $columns = ['*'])
 * @method ErpItemLedger first($columns = ['*'])
*/
class ErpItemLedgerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'referenceNumber',
        'wareHouseSystemCode',
        'itemSystemCode',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'inOutQty',
        'wacLocalCurrencyID',
        'wacLocal',
        'wacRptCurrencyID',
        'wacRpt',
        'comments',
        'transactionDate',
        'fromDamagedTransactionYN',
        'createdUserSystemID',
        'createdUserID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ErpItemLedger::class;
    }

    public static function getItemLedgerDetails($input, $isReviewCheck = false): array
    {
        $stockLedger = $data = $items = $docs = $warehouse = [];
        $grandTotalQty = $grandTotalLocalAmount = $grandTotalRepAmount = 0;

        $isGroup = \Helper::checkIsCompanyGroup($input['companySystemID']);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($input['companySystemID']);
        }
        else {
            $subCompanies = [$input['companySystemID']];
        }

        if($subCompanies && $subCompanies[0]) {
            $company = Company::find($subCompanies[0]);
        }
        else {
            return [
                'status' => false,
                'message' => 'Company System ID not found'
            ];
        }

        if(!isset($company)){
            return [
                'status' => false,
                'message' => 'Company Details not found'
            ];
        }

        $company_name = $company->CompanyName;

        $startDate = new Carbon($input['fromDate']);
        //$startDate = $startDate->addDays(1);
        $startDate = $startDate->format('Y-m-d');

        $endDate = new Carbon($input['toDate']);
        //$endDate = $endDate->addDays(1);
        $endDate = $endDate->format('Y-m-d');

        if (array_key_exists('Docs', $input)) {
            $docs = (array)$input['Docs'];
            $docs = collect($docs)->pluck('documentSystemID');
        }

        if (array_key_exists('Warehouse', $input)) {
            $warehouse = (array)$input['Warehouse'];
            $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');
        }

        $items=[];
        if (array_key_exists('Items', $input)) {
            $items = (array)$input['Items'];
            $items = collect($items)->pluck('itemSystemCode');
        }

        /*$test = [
          'company' => $company,
          'company_name' => $company_name,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'docs' => $docs,
            'warehouse' => $warehouse,
            'items' => $items
        ];

        dd($test);*/

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

        if ($isReviewCheck) {
            return [
                'status' => true,
                'data' => $dataFinal
            ];
        }
        else {
            $TotalWacLocal = collect($data)->pluck('TotalWacLocal')->toArray();
            $TotalWacLocal = array_sum($TotalWacLocal);

            $TotalWacRpt = collect($data)->pluck('TotalWacRpt')->toArray();
            $TotalWacRpt = array_sum($TotalWacRpt);

            $grandTotalQty = collect($data)->pluck('inOutQty')->toArray();
            $grandTotalQty = array_sum($grandTotalQty);

            return [
                'status' => true,
                'data' => [
                    'grandTotalQty' => $grandTotalQty,
                    'grandLocalTotal' => $TotalWacLocal,
                    'grandRptTotal' => $TotalWacRpt,
                    'data' => $dataFinal,
                    'company_name' => $company_name
                ]
            ];
        }
    }
}
