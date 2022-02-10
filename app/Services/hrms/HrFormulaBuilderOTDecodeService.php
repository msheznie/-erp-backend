<?php

namespace App\Services\hrms;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HrFormulaBuilderOTDecodeService{
    
    function decode($formula, $companyId)
    {
        $salaryCategories = $this->getSalaryCategories($companyId);        
        $salaryCatID = [];
        $formulaText = '';
        
        $formulaDecode_arr = [];
        $operand_arr = $this->operand_arr();
        $formula_arr = explode('|', $formula); // break the formula

        $formula_arr = explode('|', $formula); // break the formula

        $n = 0;
        foreach ($formula_arr as $formula_row) {

            if (trim($formula_row) != '') {
                if (in_array($formula_row, $operand_arr)) { //validate is a operand
                    $formulaText .= $formula_row;
                    $formulaDecode_arr[] = $formula_row;
                } else {

                    $elementType = $formula_row[0];

                    if ($elementType == '_') {
                        /*** Number ***/
                        $numArr = explode('_', $formula_row);
                        $formulaText .= (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];
                        $formulaDecode_arr[] = (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];

                    } else if ($elementType == '#') {
                        /*** Salary category ***/
                        $catArr = explode('#', $formula_row);
                        $salaryCatID[$n]['ID'] = $catArr[1];

                        $keys = array_keys(array_column($salaryCategories, 'salaryCategoryID'), $catArr[1]);
                        $new_array = array_map(function ($k) use ($salaryCategories) {
                            return $salaryCategories[$k];
                        }, $keys);

                        $salaryDescription = (!empty($new_array[0])) ? trim($new_array[0]['salaryDescription']) : '';

                        $formulaText .= $salaryDescription;

                        $salaryDescription_arr = explode(' ', $salaryDescription);
                        $salaryDescription_arr = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescription_arr);
                        $salaryCatID[$n]['cat'] = implode('_', $salaryDescription_arr) . '_' . $n;
                        $formulaDecode_arr[] = 'SUM(' . $salaryCatID[$n]['cat'] . ')';

                        $n++;

                    }
                }
            }

        }

        $formulaDecode = implode(' ', $formulaDecode_arr);

        $select_str2 = '';
        $whereInClause = '';
        $separator = '';

        foreach ($salaryCatID as $row) {
            $select_str2 .= $separator . 'IF(salDec.salaryCategoryID=' . $row['ID'] . ', SUM(transactionAmount) , 0 ) AS ' . $row['cat'] . '';
            $whereInClause .= $separator . ' ' . $row['ID'];
            $separator = ',';
        }

        return [
            'formulaDecode' => $formulaDecode,
            'select_str2' => $select_str2,
            'whereInClause' => $whereInClause,
        ];
    }     

     

    function getSalaryCategories($companyId)
    {        
        $data = DB::table('srp_erp_pay_salarycategories')
            ->selectRaw('salaryDescription, salaryCategoryID, salaryCategoryType')
            ->where('companyID', $companyId)
            ->get();
         
        return $this->salaryCategoriesToArray($data);
        
    }

    function salaryCategoriesToArray($data){
        if(empty($data)){
            return [];
        }
        
        $catArr = [];
        foreach($data as $row){
            $catArr[] = get_object_vars($row);
        }

        return $catArr;
    }

    function operand_arr()
    {
        return ['+', '*', '/', '-', '(', ')'];
    } 
     
}