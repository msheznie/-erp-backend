<?php

namespace App\helper;

use App\Models\ProcumentOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class CreateExcel
{

    public static function process($data,$type,$fileName,$path_dir,$from_date = NULL,$to_date = NULL,$company_name = NULL,$curr = NULL,$report_type = NULL)
    {

        $excel_content =  \Excel::create('payment_suppliers_by_year', function ($excel) use ($data,$fileName,$company_name,$from_date,$to_date,$curr,$report_type) {
            $excel->sheet($fileName, function ($sheet) use ($data,$fileName,$company_name,$from_date,$to_date,$curr,$report_type) {
                $sheet->cell('D1', function($cell) use($fileName,$company_name) 
                {
                    $cell->setValue($fileName);  
                    $cell->setFont(array(

                        'family'     => 'Calibri',

                        'size'       => '16',

                        'bold'       =>  true

                    ));
                    $cell->setAlignment('center');
                    
                });
                $sheet->cell('D2', function($cell) use($fileName,$company_name) 
                {
                    $cell->setValue($company_name);  
                    $cell->setFont(array(

                        'family'     => 'Calibri',

                        'size'       => '16',

                        'bold'       =>  true

                    ));
                    $cell->setAlignment('center');
                });

                switch ($report_type) {
                    case 1:
                        $sheet->cell('A3', function($cell) use($fileName,$from_date) 
                        {
                            $cell->setValue('From Date - '.$from_date);  
                            $cell->setFont(array(

                                'family'     => 'Calibri',
        
                                'size'       => '12',
        
                                'bold'       =>  true
        
                            ));
                        });
                        $sheet->cell('A4', function($cell) use($fileName,$to_date) 
                        {
                            $cell->setValue('To Date - '.$to_date);  
                            $cell->setFont(array(

                                'family'     => 'Calibri',
        
                                'size'       => '12',
        
                                'bold'       =>  true
        
                            ));
                        });
                        break;
                    case 2:
                        $sheet->cell('A3', function($cell) use($fileName,$from_date) 
                        {
                            $cell->setValue('Report as of : '.$from_date);  
                            $cell->setFont(array(

                                'family'     => 'Calibri',
        
                                'size'       => '12',
        
                                'bold'       =>  true
        
                            ));
                        });
                        break;
                    case 3:
                        $sheet->cell('A3', function($cell) use($fileName,$curr) 
                        {
                            $cell->setValue('Currency : '.$curr);  
                            $cell->setFont(array(

                                'family'     => 'Calibri',
        
                                'size'       => '12',
        
                                'bold'       =>  true
        
                            ));
                        });
                        break;
                    case 4:
                            $sheet->cell('A3', function($cell) use($fileName,$from_date) 
                            {
                                $cell->setValue('From Date - '.$from_date);  
                                $cell->setFont(array(
    
                                    'family'     => 'Calibri',
            
                                    'size'       => '12',
            
                                    'bold'       =>  true
            
                                ));
                            });
                            $sheet->cell('A4', function($cell) use($fileName,$to_date) 
                            {
                                $cell->setValue('To Date - '.$to_date);  
                                $cell->setFont(array(
    
                                    'family'     => 'Calibri',
            
                                    'size'       => '12',
            
                                    'bold'       =>  true
            
                                ));
                            });
                            $sheet->cell('A5', function($cell) use($fileName,$curr) 
                            {
                                $cell->setValue('Currency - '.$curr);  
                                $cell->setFont(array(
    
                                    'family'     => 'Calibri',
            
                                    'size'       => '12',
            
                                    'bold'       =>  true
            
                                ));
                            });
                            break;
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


}
