<?php

namespace App\helper;

use App\Models\PricingScheduleDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
class PirceBidFormula
{

    public static function process($details,$tender_id = null)
    {
        $details1 = [];

        foreach($details as $key=>$val) {

            if ($val['typeId'] == 4) {


                 $p = '';
                $cont = '';
                $data = [];
                $formula_arr = null;
          
                if (!is_null($val['formula_string'])) {
                       
                        if ($val['formula_string']) {

                           $formula = self::decodeFormula($val['formula_string'],$tender_id);

                            $formula_arr = explode('~', $formula);


                 
                            foreach ($formula_arr as $formula_row) {
                                if (trim($formula_row) != '') 
                                {
                                    $val1 = '';

                                    $elementType = $formula_row[0];
                                    if ($elementType == '$') {
                                        $elementArr = explode('$', $formula_row);
                                        $value = intval($elementArr[1]);
                                        foreach($details as $result)
                                        {
                                            if($result['bid_format_detail_id'] == $value)
                                            {
                                        
                                                    if($result['typeId'] == 2)
                                                    {
                                                        if($result['value'] != null)
                                                        {
                                                            $val1 = $result['value'];
                                                        }
                                                        else
                                                        {
                                                            $val1 = 0;
                                                        }
                                                        
                                                    }
                                                    else if($result['typeId'] == 3)
                                                    {
                                                       

                                                        if($result['value'] != null)
                                                        {
                                                            $val1 = $result['value']/100;
                                                        }
                                                        else
                                                        {
                                                            $val1 = 0;
                                                        }
                                                        
                                                    }
                                                $cont = $cont.$val1;
                                                break;
                                            }
                                            
                                        }
                                   
                                      
                                    }

                                    else if($elementType == '|')
                                    {
                                        
                                        $elementArr1 = explode('|', $formula_row);
                                        $value = ($elementArr1[1]);
                                        $cont = $cont.$value;
                                       
                                           
                                    }
                                    else if($elementType == '_')
                                    {
                                        $elementArr2 = explode('_', $formula_row);
                                        if(empty($elementArr2[1]) || is_null($elementArr2))
                                        {
                                            $value2 = 0;
                                        }
                                        else
                                        {
                                            $value2 = ($elementArr2[1]);
                                        }

                                        
                                        $cont = $cont.$value2;
                                        

                                    }
                                }
                               
                            }
                            
                       
                            $p = eval('return '.$cont.';');

                        } 
                    
                        }
                
                        $data[$val['id']] = $p;

                
                        array_push($details1,$data);
                    }


            }

        return $details1;
    }


    public static function decodeFormula($formula,$tender_id){
        $formulaD = $formula;
        $pattern = '/#(\d+)/';

        preg_match_all($pattern, $formulaD, $matches);

        if (!empty($matches[0])) {
            $dynamicNumbers = array_unique($matches[1]);
            foreach ($dynamicNumbers as $number) {
                $replacementValue = self::formula($number,$tender_id);
                $formulaD = str_replace("#$number", $replacementValue, $formulaD);
            }
        }else {
            $formulaD = $formula;
        }

         return $formulaD;
    }

    public static  function formula($id,$tender_id){
        $formula = PricingScheduleDetail::select('formula_string')
            ->where('tender_id',$tender_id)
            ->where('bid_format_detail_id',$id)
            ->first();
        return self::decodeFormula($formula['formula_string'],$tender_id);
    }




}
