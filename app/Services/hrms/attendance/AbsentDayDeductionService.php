<?php
namespace App\Services\hrms\attendance;
use App\enums\attendance\AttDeduction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AbsentDayDeductionService{
    protected $results = [];
    protected $formulaDecode;
    protected $empId;
    protected $attDate;
    protected $isNonPayroll;
    protected $formula;
    protected $formulaData;
    protected $table;
    protected $decodeFormulaData;
    protected $decodedFormula;
    protected $selectStr2;
    protected $whereInClause;
    protected $queryResult;
    protected $query;
    protected $trAmount;
    protected $arrKey;

    protected $formulaText;
    protected $salaryCatId;
    protected $formulaDecodeArr;
    protected $operandArr;
    protected $formulaArr;
    protected $n;
    protected $elementType;
    protected $numArr;
    protected $catArr;
    protected $keys;
    protected $salaryDescription;
    protected $fBSelectStr2;
    protected $fBWhereInClause;
    protected $separator;
    protected $salaryCategoryId;

    protected $dbErrorList = [];
    protected $captionList = [];
    protected $caption;
    protected $companyId;
    public function __construct($empId, $attDate, $companyId){

        $this->empId = $empId;
        $this->attDate = $attDate;
        $this->companyId = $companyId;

    }

    function process(){
        $this->formulaData = $this->getFormulaDetails();

        $this->results = [];
        $this->captionList = [];

        foreach ($this->formulaData as $key => $fRow) {

            $this->caption = $fRow->description;

            $this->processFormula($fRow);
        }

        $this->checkAndThrowExceptions();

        return $this->results;
    }

    private function checkAndThrowExceptions(){
        if (!empty($this->captionList)) {
            $implodedCaptionList = implode('<br>', $this->captionList);
            $msg = 'The following absent day deduction setups are not configured properly:';
            Log::error($msg . '<br><b>' . $implodedCaptionList . '</b>');
            return false;
        }
    }

    private function processFormula($fRow){
        $this->formula = trim($fRow->formulaString);

        if (empty($this->formula)) {
            return;
        }

        $this->isNonPayroll = $fRow->isNonPayroll;
        $this->salaryCategoryId = $fRow->salaryCategoryID;

        $this->table = ($this->isNonPayroll != 'Y') ?
            'srp_erp_pay_salarydeclartion' : 'srp_erp_non_pay_salarydeclartion';

        $this->decodeFormulaData = $this->formulaBuilderToSql();

        $this->decodedFormula = $this->decodeFormulaData['formulaDecode'];

        $this->decodedFormula = (!empty($this->decodedFormula)) ? $this->decodedFormula : '0';

        $this->selectStr2 = $this->decodeFormulaData['select_str2'];

        $this->selectStr2 = (!empty($this->selectStr2)) ? $this->selectStr2 . ',' : '';
        $this->whereInClause = $this->decodeFormulaData['whereInClause'];

        $this->whereInClause = (!empty($this->whereInClause)) ?
            'AND salDec.salaryCategoryID IN (' . $this->whereInClause . ' )' : '';

        $this->buildQuery();

        $this->arrKey = ($this->isNonPayroll == 'N') ? 'pay' : 'nonPay';
        $this->trAmount = (!empty($this->queryResult[0]->trAmount)) ? $this->queryResult[0]->trAmount : 0;

        $this->results[$this->arrKey]['salaryCategoryId'] = $this->salaryCategoryId;
        $this->results[$this->arrKey]['trAmount'] = $this->trAmount;
    }

    function buildQuery(){

        $this->query = "SELECT calculationTB.employeeNo, '$this->isNonPayroll' AS type, 
            (({$this->decodedFormula}))AS trAmount, transactionCurrencyID, 
            transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, 
            round( (({$this->decodedFormula}) / companyLocalER) , 
            companyLocalCurrencyDecimalPlaces)AS localAmount,  companyLocalCurrencyID , companyLocalCurrency, 
            companyLocalER, companyLocalCurrencyDecimalPlaces, 
            round( (({$this->decodedFormula} ) / companyReportingER), 
            companyReportingCurrencyDecimalPlaces  )AS reportingAmount, companyReportingCurrencyID, 
            companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces, 
            seg.serviceLineSystemID AS segmentID, seg.ServiceLineCode AS segmentCode 
            FROM 
            ( 
                SELECT employeeNo, {$this->selectStr2} transactionCurrencyID, transactionCurrency, 
                transactionER, transactionCurrencyDecimalPlaces, companyLocalCurrencyID , companyLocalCurrency, 
                companyLocalER, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, 
                companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces 
                FROM {$this->table} AS salDec 
                JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID = salDec.salaryCategoryID 
                AND salCat.companyID ={$this->companyId} 
                JOIN 
                (
                    SELECT salarydeclarationMasterID, certainPeriod FROM srp_erp_salarydeclarationmaster 
                    WHERE companyID = {$this->companyId}
                ) AS sm ON sm.salarydeclarationMasterID = salDec.sdMasterID
                WHERE salDec.companyID = {$this->companyId} AND employeeNo={$this->empId} 
                AND ( 
                    (sm.certainPeriod = 0 AND effectiveDate < '{$this->attDate}' ) OR 
                    (sm.certainPeriod = 1 AND DATE('{$this->attDate}') BETWEEN salDec.period_from 
                    AND salDec.period_to)
                )
                $this->whereInClause 
                GROUP BY employeeNo, salDec.salaryCategoryID 
            ) calculationTB JOIN srp_employeesdetails AS emp ON emp.EIdNo = calculationTB.employeeNo 
            AND emp.Erp_companyID = {$this->companyId} 
            JOIN serviceline seg ON seg.serviceLineSystemID = emp.segmentID GROUP BY employeeNo";

        $this->queryResult = DB::select($this->query);

        if ($this->queryResult !== false) {
            return $this->queryResult;
        } else {
            $this->captionList[] = $this->caption;
        }
    }

    function formulaBuilderToSql(){
        $fSalaryCategoriesArr = $this->getSalaryCategories(['A', 'D']);
        $this->formulaText = '';
        $this->salaryCatId = [];
        $this->formulaDecodeArr = [];
        $this->operandArr = $this->operandArr();

        $this->formulaArr = explode('|', $this->formula);

        $this->n = 0;
        foreach ($this->formulaArr as $formulaRow) {
            if (trim($formulaRow) == '') {
                continue;
            }

            if (in_array($formulaRow, $this->operandArr)) {
                $this->processOperand($formulaRow);
            } else {
                $this->processElementType($formulaRow, $fSalaryCategoriesArr);
            }
        }

        $this->formulaDecode = implode(' ', $this->formulaDecodeArr);
        $this->buildSelectAndWhereInClauses();

        return [
            'formulaDecode' => $this->formulaDecode,
            'select_str2' => $this->fBSelectStr2,
            'whereInClause' => $this->fBWhereInClause,
        ];
    }

    private function buildSelectAndWhereInClauses(){
        $this->fBSelectStr2 = '';
        $this->fBWhereInClause = '';
        $this->separator = '';

        foreach ($this->salaryCatId as $key1 => $row) {
            $this->fBSelectStr2 .= $this->separator . 'IF(salDec.salaryCategoryID=' . $row['ID'] . ', 
                SUM(transactionAmount), 0) AS ' . $row['cat'];
            $this->fBWhereInClause .= $this->separator . ' ' . $row['ID'];
            $this->separator = ',';
        }

    }

    function processElementType($formulaRow, $fSalaryCategoriesArr){
        $this->elementType = $formulaRow[0];

        if ($this->elementType == '_') {
            $this->processNumberElementType($formulaRow);
        } elseif ($this->elementType == '#') {
            $this->processSalaryCategoryElementType($formulaRow, $fSalaryCategoriesArr);
        }
    }

    private function processNumberElementType($formulaRow){
        $this->numArr = explode('_', $formulaRow);
        $this->formulaText .= (is_numeric($this->numArr[1])) ? $this->numArr[1] : $this->numArr[0];
        $this->formulaDecodeArr[] = (is_numeric($this->numArr[1])) ? $this->numArr[1] : $this->numArr[0];
    }

    private function processSalaryCategoryElementType($formulaRow, $fSalaryCategoriesArr){
        $this->catArr = explode('#', $formulaRow);
        $this->salaryCatId[$this->n]['ID'] = $this->catArr[1];

        $this->keys = array_keys(array_column($fSalaryCategoriesArr, 'salaryCategoryID'), $this->catArr[1]);

        $newArray = array_map(function ($k) use ($fSalaryCategoriesArr) {
            return $fSalaryCategoriesArr[$k];
        }, $this->keys);

        $this->salaryDescription = (!empty($newArray[0])) ? trim($newArray[0]->salaryDescription) : '';

        $this->formulaText .= $this->salaryDescription;

        $salaryDescriptionArr = explode(' ', $this->salaryDescription);
        $salaryDescriptionArr = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescriptionArr);

        $this->salaryCatId[$this->n]['cat'] = implode('_', $salaryDescriptionArr) . '_' . $this->n;
        $this->formulaDecodeArr[] = 'SUM(' . $this->salaryCatId[$this->n]['cat'] . ')';

        $this->n++;
    }

    function processOperand($operand){
        $this->formulaText .= $operand;
        $this->formulaDecodeArr[] = $operand;
    }

    function operandArr(){
        return array('+', '*', '/', '-', '(', ')');
    }

    function getSalaryCategories($type = []) {
        return DB::table('srp_erp_pay_salarycategories')
            ->select('salaryDescription', 'salaryCategoryID', 'salaryCategoryType')
            ->where('companyID', $this->companyId)
            ->whereIn('salaryCategoryType', $type)
            ->get()
            ->toArray();
    }
    public function getFormulaDetails(){
        return DB::table('srp_erp_nopaysystemtable AS pst')
            ->select('pst.id AS masterID', 'sc.salaryDescription AS salaryDescription',
                'pst.description', 'ft.id AS formulaTBID', 'ft.formulaString', 'pst.isNonPayroll',
                'ft.salaryCategoryID', 'ft.salaryCategories')
            ->join('srp_erp_nopayformula AS ft', function ($join) {
                $join->on('ft.nopaySystemID', '=', 'pst.id')
                    ->where('ft.companyID', '=', $this->companyId);
            }, 'INNER')
            ->leftJoin('srp_erp_pay_salarycategories AS sc', function ($join) {
                $join->on('sc.salaryCategoryID', '=', 'ft.salaryCategoryID')
                    ->where('ft.companyID', '=', $this->companyId);
            })
            ->whereIn('pst.id', [AttDeduction::ABSENT, AttDeduction::ABSENT_NON_PAYROLL])
            ->get()
            ->toArray();
    }
}