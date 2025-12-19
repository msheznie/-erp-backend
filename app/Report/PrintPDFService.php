<?php

namespace App\Report;

use Illuminate\Support\Facades\Storage;

class PrintPDFService
{
    public $data;
    public function __construct(PdfReport $data)
    {
        $this->data = $data;
    }

    public function printPdfBulk()
    {
        $dataArray = $this->data->dataArray;
        $viewName = ($this->data->viewName) ? : '';
        $rootPath = ($this->data->rootPath) ? : '';
        $fileNameStr = ($this->data->fileName) ? : 'report';


        $html = view($viewName,$dataArray);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf_content =  $pdf->setPaper('a4', 'landscape')->setWarnings(false)->output();
        $rootPaths = $rootPath;
        $fileName = $fileNameStr.strtotime(date("Y-m-d H:i:s")).'_Part_'.$this->data->reportCount.'.pdf';
        $path = $rootPaths.'/'.$fileName;
        $result = Storage::disk('local_public')->put($path, $pdf_content);
    }

}
