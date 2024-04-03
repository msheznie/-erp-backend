<?php

namespace App\Exports\GeneralLedger\Financials;

use App\Models\ReportTemplateColumns;
use phpDocumentor\Reflection\Types\Collection;
use PhpParser\Node\Expr\Array_;

class ExcelColumnFormat
{

    public  static function getExcelColumnFormat($reportData,$reportID)
    {
       $excelColumnFormat = [];
       $totalAdditionalColumn = 0;
       foreach ($reportData as $rpt)
       {

           if(isset($rpt->detail)) {
               //3 one column gap on excel + recurisve started after two index
               if($reportID == "FCT")
               {
                   $additonColumn = self::countDetailObjects($rpt->detail) + 3;
               }else {
                   $additonColumn = self::countDetailObjects($rpt->detail) + 2;
               }

               if($additonColumn > $totalAdditionalColumn)
                   $totalAdditionalColumn = $additonColumn;
           }
       }


        if(empty($excelColumnFormat))
            $excelColumnFormat =  self::convertToExcelColumn($reportData->first(),$reportID,$totalAdditionalColumn);


        return $excelColumnFormat;
    }


    static function countDetailObjects($collection)
    {
        $count = 0;
        foreach ($collection as $item) {
            if (isset($item->detail) && $item->detail->isNotEmpty()) {
                $count++;
                if($item->detail)
                $count += self::countDetailObjects($item->detail);
            }

        }
        return $count;
    }

    private static function convertToExcelColumn($data,$reportID,$totalAdditionalColumn) : Array
    {

        $index = ord('A') + $totalAdditionalColumn;
        $start = chr($index);
        $end = 'BCM';

        $data =  is_array($data) ? $data : collect($data)->toArray();
        $reportColumns = ReportTemplateColumns::select(['shortCode','type'])->get();
        $excelExportColumn = [];
        $data = (isset($data['columnData'])) ? $data['columnData'][0] : $data;
        foreach (collect($data) as $key=>$value) {

            if(str_contains($key,'-'))
            {
                $columnName = explode('-',$key)[0];
                $columnDetails = $reportColumns->filter(function($item) use ($columnName) {
                    return ($item->shortCode == $columnName);
                });


                if($columnName == 'CM') {
                }
                if($columnDetails->first()) {
                    $columnDetail = $columnDetails->first();
                    /*
                        1 - GL Code
                        2 - Year
                        3 - Month
                        4 - Formula Amount
                        5 - Percentage
                    */

                    switch ($columnDetail->type)
                    {
                        case 1 :
                            $excelExportColumn[chr($index)] = \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
                            break;
                        case 2 :
                            $excelExportColumn[chr($index)] =  \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
                            break;
                        case 3 :
                            $excelExportColumn[chr($index)] = \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
                            break;
                        case 4 :
                            $excelExportColumn[chr($index)] =  \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
                            break;
                        case 5 :
                            $excelExportColumn[chr($index)] = \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
                            break;
                        default :
                            break;
                    }

                    $index++;
                }
            }

        }

        return $excelExportColumn;
    }

}
