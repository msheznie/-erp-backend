<?php

namespace App\helper;

use App\Models\ProcumentOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class CreateExcel
{

    public static function process($data,$type,$fileName,$path_dir)
    {

        $excel_content =  \Excel::create('payment_suppliers_by_year', function ($excel) use ($data,$fileName) {
            $excel->sheet($fileName, function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });

            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
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


    public static function loadView($data,$type,$fileName,$path_dir)
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
