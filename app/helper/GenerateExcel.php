<?php

namespace App\helper;

use Illuminate\Support\Facades\Storage;

class GenerateExcel
{
    public static function process($data,$type,$fileName = 'payment_suppliers_by_year',$path_dir,$array=NULL)
    {
        
        $columnFormat = isset($array['excelFormat']) ? $array['excelFormat'] : NULL;
        $excel_content =  \Excel::create($fileName, function ($excel) use ($data,$fileName,$array,$columnFormat) {
            if(isset($array['origin']) && $array['origin'] == 'SRM'){
                $dataNew = $array['faq_data'];
                $dataNewPrebid = $array['prebid_data'];
                $faqFile = __('custom.faq');
                $prebidFile = __('custom.prebid_clarifications');

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

                    // Set right-to-left for Arabic locale
                    if (app()->getLocale() == 'ar') {
                        $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $sheet->setRightToLeft(true);
                    }

                });

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

                    // Set right-to-left for Arabic locale
                    if (app()->getLocale() == 'ar') {
                        $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $sheet->setRightToLeft(true);
                    }

                });
            }
            else {
                $excel->sheet($fileName, function ($sheet) use ($data,$fileName,$array,$columnFormat) {

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


                            self::fromDate($array,$sheet,__('custom.from_date').' ');
                            self::toDate($array,$sheet);
                        }
                        else if(($array['type']) == 2)
                        {
                            if(isset($array['report_type']) && $array['report_type'] == 'SSD') {
                                $i = $i - 0;
                                self::branch($array, $sheet, __('custom.branch').' ');
                                self::selectedCurrency($array, $sheet, __('custom.currency'));
                                self::fromDate($array,$sheet,__('custom.as_of_date'));
                            } else {
                                $i = $i - 2;
                                self::fromDate($array,$sheet,__('custom.as_of_date'));
                            }
                        }
                        else if(($array['type']) == 3)
                        {
                            $i = $i - 2;
                            self::currency($array,$sheet,'A3');
                        }
                        else if(($array['type']) == 4)
                        {
                            self::fromDate($array,$sheet,__('custom.from_date').' ');
                            self::toDate($array,$sheet);
                            self::currency($array,$sheet,'A5');

                        }
                        else if(($array['type']) == 5)
                        {
                            $i = $i - 1;
                            self::fromDate($array,$sheet,__('custom.as_of_date'));
                            self::currency($array,$sheet,'A4');

                        }
                        else if(($array['type']) == 6)
                        {
                            self::fromDate($array,$sheet,__('custom.from_date').' ');
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

                    // Set right-to-left for Arabic locale
                    if (app()->getLocale() == 'ar') {
                        $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $sheet->setRightToLeft(true);
                    }

                });
            }

            $lastrow = $excel->getActiveSheet()->getHighestRow();
        })->download($type);

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

}
