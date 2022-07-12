<?php

namespace App\helper;

use App\Models\ProcumentOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class CreateExcel
{

    public static function process($data,$type,$fileName,$path_dir,$array=NULL)
    {

        $excel_content =  \Excel::create('payment_suppliers_by_year', function ($excel) use ($data,$fileName,$array) {
            Log::info($array);
            if($array['faq'] == 'FAQ'){
                $dataNew = $array['faq_data'];
                $dataNewPrebid = $array['prebid_data'];
                $faqFile = "FAQ";
                $prebidFile = "pre-bid_clarifications";
                $excel->sheet($faqFile, function ($sheet) use ($dataNew,$faqFile,$array) {
                    /*$sheet->cell('D1', function($cell) use($array)
                    {
                        if(isset($array['title']))
                        {
                            $cell->setValue($array['title']);
                            $cell->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '15',
                                'bold'       =>  true
                            ));
                            $cell->setAlignment('center');
                        }
                    });*/
                    /*$sheet->cell('D2', function($cell) use($array)
                    {
                        if(isset($array['company_name']))
                        {
                            $cell->setValue($array['company_name']);
                            $cell->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '15',
                                'bold'       =>  true
                            ));
                            $cell->setAlignment('center');
                        }
                    });*/
                   /* if(isset($array['type']))
                    {
                        if(($array['type']) == 1)
                        {
                            self::fromDate($array,$sheet,'From Date ');
                            self::toDate($array,$sheet);
                        }
                        else if(($array['type']) == 2)
                        {

                            self::fromDate($array,$sheet,'As of Date');

                        }
                        else if(($array['type']) == 3)
                        {

                            self::currency($array,$sheet,'A3');
                        }
                        else if(($array['type']) == 4)
                        {
                            self::fromDate($array,$sheet,'From Date ');
                            self::toDate($array,$sheet);
                            self::currency($array,$sheet,'A5');

                        }
                        else if(($array['type']) == 5)
                        {
                            self::fromDate($array,$sheet,'As of Date');
                            self::currency($array,$sheet,'A4');

                        }
                    }*/

                    $sheet->fromArray($dataNew, null, 'A1', true);
                    $sheet->setAutoSize(true);
                    //$sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);

                    $sheet->row(1, function($row) {
                        // call cell manipulation methods
                        //$row->setFontColor('#00000');
                        $row->setFont(array(

                            'family'     => 'Calibri',
                            'size'       => '12',
                            'bold'       =>  true

                        ));
                    });
                });
                $excel->sheet($prebidFile, function ($sheet) use ($dataNewPrebid,$prebidFile,$array) {
                    /*$sheet->cell('D1', function($cell) use($array)
                    {
                        if(isset($array['title']))
                        {
                            $cell->setValue($array['title']);
                            $cell->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '15',
                                'bold'       =>  true
                            ));
                            $cell->setAlignment('center');
                        }
                    });*/
                    /*$sheet->cell('D2', function($cell) use($array)
                    {
                        if(isset($array['company_name']))
                        {
                            $cell->setValue($array['company_name']);
                            $cell->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '15',
                                'bold'       =>  true
                            ));
                            $cell->setAlignment('center');
                        }
                    });*/
                   /* if(isset($array['type']))
                    {
                        if(($array['type']) == 1)
                        {
                            self::fromDate($array,$sheet,'From Date ');
                            self::toDate($array,$sheet);
                        }
                        else if(($array['type']) == 2)
                        {

                            self::fromDate($array,$sheet,'As of Date');

                        }
                        else if(($array['type']) == 3)
                        {

                            self::currency($array,$sheet,'A3');
                        }
                        else if(($array['type']) == 4)
                        {
                            self::fromDate($array,$sheet,'From Date ');
                            self::toDate($array,$sheet);
                            self::currency($array,$sheet,'A5');

                        }
                        else if(($array['type']) == 5)
                        {
                            self::fromDate($array,$sheet,'As of Date');
                            self::currency($array,$sheet,'A4');

                        }
                    }*/

                    $sheet->fromArray($dataNewPrebid, null, 'A1', true);
                    $sheet->setAutoSize(true);
                    //$sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);

                    $sheet->row(1, function($row) {
                        // call cell manipulation methods
                        //$row->setFontColor('#00000');
                        $row->setFont(array(

                            'family'     => 'Calibri',
                            'size'       => '12',
                            'bold'       =>  true

                        ));
                    });
                });
            } else {
                $excel->sheet($fileName, function ($sheet) use ($data,$fileName,$array) {


                    $sheet->cell('D1', function($cell) use($array)
                    {
                        if(isset($array['title']))
                        {
                            $cell->setValue($array['title']);
                            $cell->setFont(array(

                                'family'     => 'Calibri',

                                'size'       => '15',

                                'bold'       =>  true

                            ));
                            $cell->setAlignment('center');
                        }


                    });

                    $sheet->cell('D2', function($cell) use($array)
                    {
                        if(isset($array['company_name']))
                        {
                            $cell->setValue($array['company_name']);
                            $cell->setFont(array(

                                'family'     => 'Calibri',

                                'size'       => '15',

                                'bold'       =>  true

                            ));
                            $cell->setAlignment('center');
                        }
                    });


                    if(isset($array['type']))
                    {
                        if(($array['type']) == 1)
                        {
                            self::fromDate($array,$sheet,'From Date ');
                            self::toDate($array,$sheet);
                        }
                        else if(($array['type']) == 2)
                        {

                            self::fromDate($array,$sheet,'As of Date');

                        }
                        else if(($array['type']) == 3)
                        {

                            self::currency($array,$sheet,'A3');
                        }
                        else if(($array['type']) == 4)
                        {
                            self::fromDate($array,$sheet,'From Date ');
                            self::toDate($array,$sheet);
                            self::currency($array,$sheet,'A5');

                        }
                        else if(($array['type']) == 5)
                        {
                            self::fromDate($array,$sheet,'As of Date');
                            self::currency($array,$sheet,'A4');

                        }
                    }

                    $sheet->fromArray($data, null, 'A7', true);
                    $sheet->setAutoSize(true);
                    //$sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);

                    $sheet->row(7, function($row) {



                        // call cell manipulation methods

                        $row->setFontColor('#00000');

                        $row->setFont(array(

                            'family'     => 'Calibri',

                            'size'       => '12',

                            'bold'       =>  true

                        ));



                    });
                });
            }

            $lastrow = $excel->getActiveSheet()->getHighestRow();
            //$excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->string($type);

        $disk = 's3';

        $full_name = $fileName.'_'.strtotime(date("Y-m-d H:i:s")).'.'.$type;
        $path = $path_dir.$full_name;
        $result = Storage::disk($disk)->put($path, $excel_content);
        $basePath = '';
        if($result)
        {
            if (Storage::disk($disk)->exists($path))
            {
                $basePath = \Helper::getFileUrlFromS3($path);
            }
        }

        return $basePath;
    }


    public static function loadView($data,$type,$fileName,$path_dir,$templateName)
    {

     $excel_content = \Excel::create('finance', function ($excel) use ($data, $templateName,$fileName) {
                    $excel->sheet($fileName, function ($sheet) use ($data, $templateName) {
                        $sheet->loadView($templateName, $data);
                    });
                })->string($type);


        $disk = 's3';

        $full_name = $fileName.'_'.strtotime(date("Y-m-d H:i:s")).'.'.$type;
        $path = $path_dir.$full_name;
        $result = Storage::disk($disk)->put($path, $excel_content);
        $basePath = '';
        if($result)
        {
            if (Storage::disk($disk)->exists($path))
            {
                $basePath = \Helper::getFileUrlFromS3($path);
            }
        }

        return $basePath;
    }

    public static function fromDate($array,$sheet,$type)
    {
        $sheet->cell('A3', function($cell) use($array,$type) 
        {
            if(isset($array['from_date']))
            {
                $cell->setValue($type.' - '.$array['from_date']);  
                $cell->setFont(array(

                    'family'     => 'Calibri',

                    'size'       => '12',

                    'bold'       =>  true

                ));
                $cell->setAlignment('left');
            }
       });
    }

    public static function toDate($array,$sheet)
    {
        $sheet->cell('A4', function($cell) use($array) 
        {
            if(isset($array['to_date']))
            {
                $cell->setValue('To Date - '.$array['to_date']);  
                $cell->setFont(array(
    
                    'family'     => 'Calibri',
    
                    'size'       => '12',
    
                    'bold'       =>  true
    
                ));
                $cell->setAlignment('left');
            }
       });
    }

    public static function currency($array,$sheet,$col)
    {
        $sheet->cell($col, function($cell) use($array) 
        {
            if(isset($array['cur']))
            {
                $cell->setValue('Currency - '.$array['cur']);  
                $cell->setFont(array(
    
                    'family'     => 'Calibri',
    
                    'size'       => '12',
    
                    'bold'       =>  true
    
                ));
                $cell->setAlignment('left');
            }
       });
    }


}
