<?php

namespace App\helper;

use Illuminate\Support\Facades\Storage;

class CreateExcel
{

    public static function process($data,$type,$fileName,$path_dir,$array=NULL)
    {
        $columnFormat = isset($array['excelFormat']) ? $array['excelFormat'] : NULL;
        $excel_content =  \Excel::create('payment_suppliers_by_year', function ($excel) use ($data,$fileName,$array,$columnFormat) {
            if(isset($array['origin']) && $array['origin'] == 'SRM'){
                $dataNew = $array['faq_data'];
                $dataNewPrebid = $array['prebid_data'];
                $faqFile = "FAQ";

                $lookup = [
                    'purchase_order_summary_report' => 'Purchase Order',
                    'supplier_invoice_summary' => 'Supplier Invoice',
                ];

                $prebidFile = $lookup[$fileName] ?? 'Pre-bid Clarifications';

                if (!empty($dataNew) && count($dataNew) > 0) {
                    $excel->sheet($faqFile, function ($sheet) use ($dataNew,$faqFile,$array) {
                        $i = 2;
                        $sheet->fromArray($dataNew, null, 'A1', true);
                        (isset($array['setColumnAutoSize'])) ?  $sheet->setAutoSize($array['setColumnAutoSize']) : $sheet->setAutoSize(true);

                        $sheet->row(1, function($row) {
                            $row->setBackground('#827e7e');
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        foreach ($dataNew as $valId) {
                            $sheet->row($i, function($row) {
                                $row->setBackground('#ebdfdf');
                                $row->setFont(array(
                                    'family'     => 'Calibri',
                                    'size'       => '12',
                                ));
                            });
                            $i++;
                        }

                    });
                }

                $excel->sheet($prebidFile, function ($sheet) use ($dataNewPrebid,$prebidFile,$array) {
                    $sheet->fromArray($dataNewPrebid, null, 'A1', true);
                    (isset($array['setColumnAutoSize'])) ?  $sheet->setAutoSize($array['setColumnAutoSize']) : $sheet->setAutoSize(true);
                    $sheet->row(1, function($row) {
                        $row->setBackground('#827e7e');
                        $row->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '12',
                            'bold'       =>  true
                        ));
                    });
                    foreach ($array['parentIdList'] as $valId) {
                        $sheet->row($valId, function($row) {
                            $row->setBackground('#CCCCCC');
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12'
                            ));
                        });
                    }

                    foreach ($array['nonParentIdList'] as $valId) {
                        $sheet->row($valId, function($row) {
                            $row->setBackground('#ebdfdf');
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                            ));
                        });
                    }

                });
            }
            else {
                $excel->sheet($fileName, function ($sheet) use ($data,$fileName,$array,$columnFormat) {

                    $search = ['='];

                    foreach ($data as &$record) {
                        foreach ($record as $key => &$value) {
                            if (is_string($value)) {
                                $value = str_replace($search, '', $value);
                            }
                        }
                    }


                    $i = 7;
                    if(!isset($array['title']) && empty($array['title']))
                    {
                        $i = $i - 1;
                    }

                    if(!isset($array['company_name']) && empty($array['company_name']))
                    {
                        $i = $i - 1;
                    }

                    if(!isset($array['type']) && empty($array['type']))
                    {
                        $i = $i - 4;
                    }

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
                    if($columnFormat) {
                        $sheet->setColumnFormat($columnFormat);
                    }

                    if(isset($array['type']))
                    {
                        if(($array['type']) == 1)
                        {
                            
                            if(!isset($array['from_date']) && empty($array['from_date']))
                            {
                                $i = $i - 1;
                            }

                            if(!isset($array['to_date']) && empty($array['to_date']))
                            {
                                $i = $i - 1;
                            }

                            if(isset($array['from_date']) && !empty($array['from_date']))
                            {
                                $i = $i - 1;
                            }
                            
                           
                            self::fromDate($array,$sheet,trans('custom.from_date'));
                            self::toDate($array,$sheet);
                        }
                        else if(($array['type']) == 2)
                        {
                            if(isset($array['report_type']) && $array['report_type'] == 'SSD') {
                                $i = $i - 0;
                                self::branch($array, $sheet, __('custom.branch').' ');
                                self::selectedCurrency($array, $sheet, trans('custom.currency'));
                                self::fromDate($array,$sheet,trans('custom.as_of_date'));
                            } else {
                                $i = $i - 2;
                                self::fromDate($array,$sheet,trans('custom.as_of_date'));
                            }
                        }
                        else if(($array['type']) == 3)
                        {
                            $i = $i - 2;
                            self::currency($array,$sheet,'A3');
                        }
                        else if(($array['type']) == 4)
                        {
                            self::fromDate($array,$sheet,trans('custom.from_date'));
                            self::toDate($array,$sheet);
                            self::currency($array,$sheet,'A5');

                        }
                        else if(($array['type']) == 5)
                        {
                            $i = $i - 1;
                            self::fromDate($array,$sheet,trans('custom.as_of_date'));
                            self::currency($array,$sheet,'A4');

                        }
                        else if(($array['type']) == 6)
                        {
                            self::fromDate($array,$sheet,trans('custom.from_date'));
                            self::toDate($array,$sheet);
                            $sheet->cell('A5', function($cell) use($array)
                            {
                    
                                if(isset($array['company_vat_registration_number']))
                    
                                {
                    
                                    $cell->setValue(__('custom.company_vat_registration_no').' - '.$array['company_vat_registration_number']);  
                    
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

                    if(isset($array['dataType']) && $array['dataType'] == 2) {
                        $sheet->fromArray($data, null, 'A'.$i, false,false);
                    }else {
                        $sheet->fromArray($data, null, 'A'.$i, false,true);
                    }
                    (isset($array['setColumnAutoSize'])) ?  $sheet->setAutoSize($array['setColumnAutoSize']) : $sheet->setAutoSize(true);
                    //$sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);

                    $sheet->row($i, function($row) {



                        // call cell manipulation methods

                        $row->setAlignment('left');
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
        $companyCode = isset($array['company_code'])?$array['company_code']:'common';

        $full_name = $companyCode.'_'.$fileName.'_'.strtotime(date("Y-m-d H:i:s")).'.'.$type;
        $path = $companyCode.'/'.$path_dir.$full_name;
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

    public static function processDetailExport($data, $companyCode) {
        $excel_content = \Excel::create('po_details_export', function($excel) use ($data) {
            $excel->sheet('Sheet1', function($sheet) use ($data) {
                $sheet->setStyle([
                    'font' => [
                        'name' => 'Calibri',
                        'size' => 11,
                    ]
                ]);

                $rowNum = 1;
                $knownHeaders = [
                    'company id',
                    'order details',
                    'item code',
                    'pr number',
                    'logistics details',
                    'category',
                    'addon details',
                ];

                $columnWidths = [
                    'A' => 4, // Company ID
                    'B' => 20, // Company ID
                    'C' => 13, // Company Name
                    'D' => 13, // Order Code
                    'E' => 15, // Segment
                    'F' => 13, // Created at
                    'G' => 13, // Created By
                    'H' => 13, // Category
                    'I' => 13, // Narration
                    'J' => 13, // Supplier Code
                    'K' => 13, // Supplier Name
                    'L' => 13, // Credit Period
                    'M' => 13, // Supplier Country
                    'N' => 13, // Expected Delivery Date
                    'O' => 13, // Delivery Terms
                    'P' => 13, // Penalty Terms
                    'Q' => 13, // Confirmed Status
                    'R' => 13, // Confirmed Date
                    'S' => 13, // Confirmed By
                    'T' => 13, // Approved Status
                    'U' => 13, // Approved Date
                    'V' => 13, // Transaction Currency
                    'W' => 13, // Transaction Amount
                    'X' => 13, // Local Amount
                    'Y' => 13, // Reporting Amount
                    'z' => 13, // Advance Payment Available
                    'AA' => 13, // Total Advance Payment Amount
                ];

                foreach ($columnWidths as $col => $width) {
                    $sheet->setWidth($col, $width);
                }

                $maxColumns = 0;
                foreach ($data as $row) {
                    $maxColumns = max($maxColumns, count($row));
                }

                foreach ($data as $row) {
                    $paddedRow = array_pad($row, $maxColumns, '');
                    $sheet->appendRow($paddedRow);

                    $isHeader = false;
                    foreach ($paddedRow as $cell) {
                        $clean = strtolower(trim($cell));
                        foreach ($knownHeaders as $keyword) {
                            if ($clean === $keyword || strpos($clean, $keyword) !== false) {
                                $isHeader = true;
                                break 2;
                            }
                        }
                    }

                    if ($isHeader) {
                        $highestColumn = \PHPExcel_Cell::stringFromColumnIndex($maxColumns - 1);
                        $sheet->cells("A{$rowNum}:{$highestColumn}{$rowNum}", function($cells) {
                            $cells->setFont([
                                'bold' => true,
                                'size' => 12,
                                'name' => 'Calibri'
                            ]);
                        });
                    }

                    $rowNum++;
                }
            });
        })->string('xlsx');

        $disk = 's3';
        $fileName = 'po_detail_export';
        $path_dir='procurement/purchase_order/excel/';
        $type='xlsx';

        $full_name = $companyCode.'_'.$fileName.'_'.strtotime(date("Y-m-d H:i:s")).'.'.$type;
        $path = $companyCode.'/'.$path_dir.$full_name;
        $result = Storage::disk($disk)->put($path, $excel_content);
        $basePath = '';
        if($result)
        {
            if (Storage::disk($disk)->exists($path))
            {
                $basePath = \Helper::getFileUrlFromS3($path);
            }
        }
        return $path;
    }

    public static function fromDate($array,$sheet,$type)
    {
        if(isset($array['report_type']) && $array['report_type'] == 'SSD') {
            $sheet->cell('A5', function($cell) use($array,$type)
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
        } else {
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
    }

    public static function toDate($array,$sheet)
    {
        $sheet->cell('A4', function($cell) use($array)
        {
            if(isset($array))
            {
                $cell->setValue(__('custom.to_date').' - '.$array['to_date']);
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
                $cell->setValue(__('custom.currency').' - '.$array['cur']);  
                $cell->setFont(array(
    
                    'family'     => 'Calibri',
    
                    'size'       => '12',
    
                    'bold'       =>  true
    
                ));
                $cell->setAlignment('left');
            }
       });
    }

    public static function branch($array,$sheet,$type)
    {
        $sheet->cell('A3', function($cell) use($array,$type)
        {
            if(isset($array['company_name']))
            {
                $cell->setValue($type.' - '.$array['company_name']);
                $cell->setFont(array(

                    'family'     => 'Calibri',

                    'size'       => '12',

                    'bold'       =>  true

                ));
                $cell->setAlignment('left');
            }
        });

    }

    public static function selectedCurrency($array,$sheet,$type)
    {
        $sheet->cell('A4', function($cell) use($array,$type)
        {
            if(isset($array['currencyName']) && !empty($array['currencyName']))
            {
                $cell->setValue($type.' - '.$array['currencyName']);
                $cell->setFont(array(

                    'family'     => 'Calibri',

                    'size'       => '12',

                    'bold'       =>  true

                ));
                $cell->setAlignment('left');
            }
        });

    }

    public static function loadView($data,$type,$fileName,$path_dir,$templateName, $excelColumnFormat = [])
    {

        $excel_content = \Excel::create('finance', function ($excel) use ($data, $templateName,$fileName, $excelColumnFormat) {
                       $excel->sheet($fileName, function ($sheet) use ($data, $templateName, $excelColumnFormat) {
                           $sheet->setColumnFormat($excelColumnFormat);
                           $sheet->loadView($templateName, $data);
                       });
                   })->string($type);


       $disk = 's3';
       $companyCode = isset($data['companyCode'])?$data['companyCode']:'common';

       $full_name = $companyCode.'_'.$fileName.'_'.strtotime(date("Y-m-d H:i:s")).'.'.$type;
       $path = $companyCode.'/'.$path_dir.$full_name;
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


     public static function processOpenRequestReport($data,$companyCode) {

        $excel_content =  \Excel::create('open_request_detail_report', function ($excel) use ($data) {
       
                $excel->sheet('open_requests', function ($sheet) use ($data) {

                    $i = 1;
                  
                    $sheet->setAutoSize(true);

                    if (!empty($data)) {
                        $headerRow = array_keys($data[0]);
                        if (($key = array_search('details', $headerRow)) !== false) {
                            unset($headerRow[$key]);
                        }
                        $sheet->appendRow($headerRow);

                        $sheet->row(1, function($row) {
                            $row->setAlignment('left');
                            $row->setFontColor('#000000');
                            $row->setFont([
                                'family' => 'Calibri',
                                'size'   => '12',
                                'bold'   => true,
                            ]);
                        });
                    }

                    foreach ($data as $row) {
                        $first = true; 
                        if (!empty($row['details'])) {
                            foreach ($row['details'] as $detail) {
                                $sheet->appendRow([
                                    $first ? $row['PR Number'] : '',
                                    $first ? $row['PR Requested Date'] : '',
                                    $first ? $row['Department'] : '',
                                    $detail['Item Code'],
                                    $detail['Part No / Ref.Number'],
                                    $detail['Item Description'],
                                    $detail['Req Qty'],
                                    $first ? $row['Narration'] : '',
                                    $first ? $row['Location'] : '',
                                    $first ? $row['Priority'] : '',         
                                    $first ? $row['Created By'] : '',
                                    $first ? $row['Confirmed Date'] : '',
                                    $first ? $row['Approved Date'] : '',
                                    
                                ]);
                                $first = false; 
                            }
                        } else {
                            $sheet->appendRow([
                                $row['PR Number'],
                                $row['PR Requested Date'],
                                $row['Department'],
                                '', '', '', '',
                                $row['Narration'],
                                $row['Location'],
                                $row['Priority'],
                                $row['Created By'],
                                $row['Confirmed Date'],
                                $row['Approved Date'],
                            ]);
                        }

                        $sheet->appendRow([]);
                    }


                });
            

            $lastrow = $excel->getActiveSheet()->getHighestRow();
        })->string('xlsx');

        $disk = 's3';
        $fileName = 'or_detail_export';
        $path_dir='procurement/open_request/excel/';
        $type='xlsx';

        $full_name = $companyCode.'_'.$fileName.'_'.strtotime(date("Y-m-d H:i:s")).'.'.$type;
        $path = $companyCode.'/'.$path_dir.$full_name;
        $result = Storage::disk($disk)->put($path, $excel_content);
        $basePath = '';
        if($result)
        {
            if (Storage::disk($disk)->exists($path))
            {
                $basePath = \Helper::getFileUrlFromS3($path);
            }
        }
        return $path;
    }

    public static function processPRDetailExport($data,$companyCode) 
    {
        $excel_content = \Excel::create('pr_details_export', function($excel) use ($data) {
            $excel->sheet('Sheet1', function($sheet) use ($data) {
                $sheet->setStyle([
                    'font' => [
                        'name' => 'Calibri',
                        'size' => 11,
                    ]
                ]);

                $rowNum = 1;
                  
                $sheet->setAutoSize(true);


                $columnWidths = [
                    'A' => 25.80,
                    'B' => 12.80, 
                    'C' => 13, 
                    'D' => 13, 
                    'E' => 13, 
                    'F' => 13, 
                    'G' => 13, 
                    'H' => 15.80, 
                    'I' => 15.80, 
                    'J' => 13, 
                    'K' => 13, 
                    'L' => 13, 
                ];

                foreach ($columnWidths as $col => $width) {
                    $sheet->setWidth($col, $width);
                }

                $maxColumns = 0;
                foreach ($data as $row) {
                    $maxColumns = max($maxColumns, count($row));
                }

                foreach ($data as $row) {
                    $isHeader = isset($row['IsHeader']) ? $row['IsHeader'] : false;
                    unset($row['IsHeader']);
                    $paddedRow = array_pad($row, $maxColumns, '');
                    $sheet->appendRow($paddedRow);
                    
                    if ($isHeader) {
                        $highestColumn = \PHPExcel_Cell::stringFromColumnIndex($maxColumns - 1);
                        $sheet->cells("A{$rowNum}:{$highestColumn}{$rowNum}", function($cells) {
                            $cells->setFont([
                                'bold' => true,
                                'size' => 12,
                                'name' => 'Calibri'
                            ]);
                        });
                    }

                    $rowNum++;
                }
            });
        })->string('xlsx');

        $disk = 's3';
        $fileName = 'PR_detail_export';
        $path_dir='procurement/purchase_order/excel/';
        $type='xlsx';

        $full_name = $companyCode.'_'.$fileName.'_'.strtotime(date("Y-m-d H:i:s")).'.'.$type;
        $path = $companyCode.'/'.$path_dir.$full_name;
        $result = Storage::disk($disk)->put($path, $excel_content);
        $basePath = '';
        if($result)
        {
            if (Storage::disk($disk)->exists($path))
            {
                $basePath = \Helper::getFileUrlFromS3($path);
            }
        }
        return $path;
    }
}
