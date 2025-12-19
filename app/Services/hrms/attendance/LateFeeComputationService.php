<?php
namespace App\Services\hrms\attendance;

use App\helper\CommonJobService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\hrms\HrFormulaBuilderOTDecodeService;

class LateFeeComputationService{
    private $empId;
    private $attendanceDate;
    private $companyId;

    public function __construct($empId, $attendanceDate, $companyId){
        Log::useFiles( CommonJobService::get_specific_log_file('attendance-clockIn') );

        $this->empId = $empId;
        $this->attendanceDate = $attendanceDate;
        $this->companyId = $companyId;
    }

    function compute(){
        $formula_arr = $this->load_formula_details(); 

        return (empty($formula_arr))
            ? 0
            : $this->getAmountForPerMinutes($formula_arr);
    }

    function load_formula_details(){
        $q = "SELECT *, t1.id AS masterID
        FROM srp_erp_nopaysystemtable AS t1
        JOIN srp_erp_nopayformula AS f ON f.nopaySystemID=t1.id AND companyID={$this->companyId} AND t1.id = 3";

        return DB::select($q);
    }

    function getAmountForPerMinutes($formula_arr){
                 
        foreach ($formula_arr as $row) {
            $q = $this->decodeQuery($row);
            $result = DB::select($q);
            
            return (empty($result)) ? 0: $result[0]->transactionAmount;
        }
    }
    
    function decodeQuery($row){
        $formula = trim($row->formulaString);

        $obj = new HrFormulaBuilderOTDecodeService();
        $formulaBuilder = $obj->decode($formula, $this->companyId);

        [
            'formulaDecode'=> $decodedFormula, 'select_str2'=> $select_str2, 'whereInClause'=> $whereInClause,
        ] = $formulaBuilder;
                 
        $decodedFormula = (!empty($decodedFormula)) ? $decodedFormula : '0';        
        $select_str2 = (!empty($select_str2)) ? $select_str2 . ',' : '';        
        $whereInClause = (!empty($whereInClause)) ? 'AND salDec.salaryCategoryID IN (' . $whereInClause . ' )' : ''; 

        $q = "SELECT calculationTB.employeeNo, (({$decodedFormula}) )AS transactionAmount
        FROM ( 
            SELECT employeeNo, " . $select_str2 . "  transactionCurrencyID
            FROM srp_erp_pay_salarydeclartion AS salDec 
            JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID = salDec.salaryCategoryID AND 
            salCat.companyID = {$this->companyId} 
            JOIN (
                SELECT salarydeclarationMasterID, certainPeriod FROM srp_erp_salarydeclarationmaster 
                WHERE companyID = {$this->companyId}
            ) AS sm ON sm.salarydeclarationMasterID = salDec.sdMasterID
            WHERE salDec.companyID = {$this->companyId}  AND employeeNo={$this->empId} 
            AND ( 
                (sm.certainPeriod = 0 AND effectiveDate < '{$this->attendanceDate}' ) OR 
                (sm.certainPeriod = 1 AND DATE('{$this->attendanceDate}') BETWEEN salDec.period_from AND salDec.period_to)
            )
            {$whereInClause}
            GROUP BY employeeNo, salDec.salaryCategoryID 
        ) calculationTB JOIN srp_employeesdetails AS emp ON emp.EIdNo = calculationTB.employeeNo 
        AND emp.Erp_companyID = {$this->companyId} 
        GROUP BY employeeNo";
        
        return $q;
    }
}