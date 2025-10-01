<?php

namespace App\Http\Controllers\API\Budget;

use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Services\BudgetReportService;
use App\Services\Excel\ExportReportToExcelService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BudgetReportController extends AppBaseController
{
    public function generateReport(Request $request)
    {
        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', -1);

        $reportID = $request->reportID;
        $budgetReportService = new BudgetReportService();
        $checkIsGroup = Company::find($request->companySystemID);

        if(empty($reportID))
            $this->sendError("Report Not Found!",401);

        switch($reportID)
        {
            case "BCD";
            // Budget commitmentes details report
                $output = $budgetReportService->generateBudgetCommitmentDetailsReport($request);
            default;
                $this->sendError("Report Not Found!",401);
                break;
        }

        return \DataTables::of($output['data'])
            ->addIndexColumn()
            ->with('companyName', $checkIsGroup->CompanyName)
            ->with('isGroup', $checkIsGroup->isGroup)
            ->with('currencyID', "")
            ->with('total', $output['total'])
            ->with('decimalPlace', 2)
            ->with('currencyCode', "")
            ->addIndexColumn()
            ->make(true);
    }

    public function export(Request $request)
    {
        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', -1);

        $reportID = $request->reportID;
        $budgetReportService = new BudgetReportService();
        $company = Company::find($request->companySystemID);
        $exportReportToExcelService = new ExportReportToExcelService();

        if(empty($reportID))
            $this->sendError("Report Not Found!",401);

        switch($reportID)
        {
            case "BCD";
                // Budget commitmentes details report
                $data = $budgetReportService->generateBudgetCommitmentDetailsReport($request);
                $serviceLines =  collect($request->selectedServicelines)->pluck('ServiceLineCode')->toArray();
                $currency = $request->currencyID[0];
                $fileName = 'Budget Commitments Detail';
                $title = 'Budget Commitments Detail Report';
                $excelColumnFormat = [];
                $path = "";
                $companyCode = isset($company->CompanyID) ? $company->CompanyID : 'common';
                $company_name = $company->CompanyName;
                $from_date = $request->fromDate;
                $to_date = $request->fromDate;
                $date = $request->fromDate.'-'.$request->toDate;


                $outputData = array('reportData' => $data['data'],
                    'companyName' => $company_name,
                    'fromDate' => $date,
                    'total' => $data['total'],
                    'serviceLines' => implode(' & ',$serviceLines),
                    'currency' => $currency,

                );

                $excelColumnFormat = [
                    'D' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'E' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                ];

                return \Excel::create('budget_commitment_details_report', function ($excel) use ($outputData,$excelColumnFormat) {
                    $excel->sheet('New sheet', function ($sheet) use ($outputData,$excelColumnFormat) {
                        $sheet->setColumnFormat($excelColumnFormat);
                        $sheet->loadView('export_report.budget.budget_commitment_details_report', $outputData);
                        
                        // Set right-to-left for Arabic locale
                        if (app()->getLocale() == 'ar') {
                            $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                            $sheet->setRightToLeft(true);
                        }
                    });
                })->download('xlsx');
            default;
                $this->sendError("Report Not Found!",401);
                break;
        }
    }
}
