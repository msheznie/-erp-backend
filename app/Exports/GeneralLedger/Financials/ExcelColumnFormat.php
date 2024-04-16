<?php

namespace App\Exports\GeneralLedger\Financials;

use App\Models\ReportTemplateColumns;
use phpDocumentor\Reflection\Types\Collection;
use PhpParser\Node\Expr\Array_;

class ExcelColumnFormat
{


    private static $mxCount = 0;
    private static $parentNode;
    private static $parentNodeID = 0;
    public  static function getExcelColumnFormat($reportData,$reportID)
    {
       $excelColumnFormat = [];
       $totalAdditionalColumn = 0;
       self::$parentNode = $reportData;

        if($reportID == "FCT")
        {
            self::countDetailObjectsFCT($reportData);
            if(self::$mxCount > $totalAdditionalColumn)
                $totalAdditionalColumn = self::$mxCount;
        }else {
            foreach ($reportData as $rpt)
            {

                if(isset($rpt->detail)) {
                    //3 one column gap on excel + recursive started after two index
                    if($reportID == "FCT")
                    {
                        self::countDetailObjectsFCT($rpt);
                        if(self::$mxCount > $totalAdditionalColumn)
                            $totalAdditionalColumn = self::$mxCount + 1;
                    }else {
                        $additonColumn = self::countDetailObjects($rpt->detail,0) + 2;
                        if($additonColumn > $totalAdditionalColumn)
                            $totalAdditionalColumn = $additonColumn;
                    }



                }
            }
        }



        if(empty($excelColumnFormat))
            $excelColumnFormat =  self::convertToExcelColumn($reportData->first(),$reportID,$totalAdditionalColumn);


        return $excelColumnFormat;
    }

    private static function countDetailObjectsFCT($collection,$count = 0)
    {


        foreach ($collection as $key => $collect)
        {

            if(isset($collect->detID) && $collect->masterID == null)
            {
                $count = 0; //
                self::$parentNodeID = $collect->detID;
            }


            if(isset($collect->masterID) && $collect->masterID == self::$parentNodeID)
            {
                $count = 1;
            }



            if(isset($collect->detID))
            {


                if(isset($collect->glCodes) && $collect->glCodes->isNotEmpty())
                {
                    $count++;
                    self::countDetailObjectsFCT($collect->glCodes,$count);
                }else if(isset($collect->detail) && $collect->detail->isNotEmpty()) {
                    $count++;
                    self::countDetailObjectsFCT($collect->detail,$count);
                } else{

                    // item type 3 means the total
                    if(isset($collect->itemType) && ($collect->itemType == 3))
                    {
                        if($collect->isFinalLevel  && isset($collection[$key-1]))
                        {
                            $count = $count++;
                        }
                    }else {
                        if($collect->isFinalLevel)
                        {
                            $count++;
                        }else {
                            $count = 0;

                        }
                    }

                }

                if($count > self::$mxCount)
                    self::$mxCount = $count;
            }else {
                $count++;
            }

        }

        return self::$mxCount;
    }


    private static function countDetailObjects($collection,$count = 0)
    {
        foreach ($collection as $item)
        {

            if (isset($item->detail) && $item->detail->isNotEmpty())
            {
                $count++;

                if($item->detail)
                {
                    self::countDetailObjects($item->detail,$count);
                }

            }
            else if(isset($item->glCodes) && $item->glCodes->isNotEmpty())
            {
                $count++;
                if($item->glCodes) {
                    self::countDetailObjects($item->glCodes,$count);
                }

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
        $count = 0;

        $collection_data = collect($data);
        $collection_data->put('CYM1-005',0);
        $collection_data->put('CYM1-007',0);
        $collection_data->put('CYM1-008',0);

        foreach ($collection_data as $key=>$value) {
            if(str_contains($key,'-'))
            {
                $count++;

                $columnName = explode('-',$key)[0];
                $columnDetails = $reportColumns->filter(function($item) use ($columnName) {
                    return ($item->shortCode == $columnName);
                });


                if($columnName == 'CM')
                {
                }
                if($columnDetails->first())
                {
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
                            $excelExportColumn[chr($index)] = \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
                            break;
                    }

                    $index++;
                }
            }

        }


        return $excelExportColumn;
    }

}
