<?php
/**
 * =============================================
 * -- File Name : Formula.php
 * -- Project Name : ERP
 * -- Module Name :  Helper class
 * -- Author : Mohamed Mubashir
 * -- Create date : 05 - May 2018
 * -- Description : This file contains the all the function related formula decoding
 * -- REVISION HISTORY
 */

namespace App\helper;

use App\Models;
use Illuminate\Support\Facades\DB;

class Formula
{
    protected $globalFormula; //keep whole formula ro replace

    /**
     * function to decode tax formula to multiple combined formula
     * @param $formulaDetailID
     * @param $amount
     * @return array
     */
    public static function taxFormulaDecode($formulaDetailID, $amount)
    {
        global $globalFormula;
        $sepFormulaArr = [];
        $finalArr = [];
        $taxFormula = Models\TaxFormulaDetail::find($formulaDetailID);
        $globalFormula = $taxFormula->formula;
        $taxMasters = $taxFormula->taxMasters;
        $sepFormulaArr[$formulaDetailID] = self::decodeTaxFormula($taxMasters);
        $globalFormula = '';
        $taxArr = Models\TaxFormulaDetail::whereIn('formulaDetailID',explode(',',$taxMasters))->get();
        if($taxArr){
            foreach ($taxArr as $val){
                $globalFormula = $val->formula;
                $sepFormulaArr[$val->formulaDetailID] = self::decodeTaxFormula($val->formulaDetailID);
                $globalFormula = '';
            }
        }
        if($sepFormulaArr) {
            foreach ($sepFormulaArr as $key => $val) {
                $fomulaFinal = '';
                $formulaArr = explode('~', $val);
                if ($formulaArr) {
                    foreach ($formulaArr as $val2) {
                        $firstChar = substr($val2, 0, 1);
                        $removedFirstChar = substr($val2, 1);
                        $fomulaFinal .= $removedFirstChar;
                    }
                    $fomulaFinal = str_replace("AMT", $amount, $fomulaFinal);
                    $finalArr[$key] = eval("return $fomulaFinal;");
                }
            }
        }
        return $finalArr;
    }


    /**
     * function tp decode tax in a recursive loop
     * @param $taxMasters - connected formulas
     * @return mixed
     */
    public static function decodeTaxFormula($taxMasters){
        global $globalFormula;
        $taxFormula = Models\TaxFormulaDetail::with('taxmaster')->whereIn('formulaDetailID',explode(',',$taxMasters))->get();
        if($taxFormula){
            foreach ($taxFormula as $val){
                $searchVal = '#'.$val['formulaDetailID'];
                $replaceVal = '|(~'.$val['formula'].'~|)';
                if(!empty($val['taxMasters'])){
                    $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                    $return = self::decodeTaxFormula($val['taxMasters']);
                    if(is_array($return)){
                        if($return[0] == 'e'){
                            return $return;
                            break;
                        }
                    }
                }
                else{
                    $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                }
            }
        }
        return $globalFormula;
    }
}
