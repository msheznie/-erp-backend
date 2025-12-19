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
                $totalAdditionalColumn = self::$mxCount + 1;

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

        foreach ($collection as $key => $node)
        {
            if(isset($node->masterID) && is_null($node->masterID))
            {
                $parentNode = $node;
                if(isset($parentNode->detail) && $parentNode->detail->isNotEmpty())
                {
                    $count++;

                    if($key > 0)
                    {
                        $prvNode = $collection[$key-1];
                        if($prvNode->masterID == $node->masterID)
                        {
                            $count=0;
                        }
                    }
                    self::countDetailObjectsFCT($node->detail,$count);
                }

                if(isset($parentNode->glCodes) && $parentNode->glCodes->isNotEmpty())
                {
                    $count++;
                }
            } 

            if($count > self::$mxCount)
            {
                self::$mxCount = $count;
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
        $count = $index;

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

                    $columns = [];
                    $start = $start;
                    $end = 'ZZ'; // Adjust as needed
                    $current = $start;

                    while ($current != $end) {
                        $columns[$current] = \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
                        $current++;
                    }

                    return $columns;

                    $index++;
                }
            }

        }


        return $excelExportColumn;
    }

}
