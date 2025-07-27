<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\BankStatementDetail;
use App\Models\BankStatementMaster;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class UploadBankStatement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $db;
    protected $uploadData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db, $uploadData)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
        $this->db = $db;
        $this->uploadData = $uploadData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $uploadData = $this->uploadData;
        $db = $this->db;
        CommonJobService::db_switch($db);
        Log::useFiles(storage_path().'/logs/upload_bank_statement.log');

        DB::beginTransaction();
        try {
            $uploadedCompany = $uploadData['uploadedCompany'];
            $statementMaster = $uploadData['statementMaster'];
            $transactionCount = $uploadData['transactionCount'];
            $template = $uploadData['template'];
            $objPHPExcel = $uploadData['objPHPExcel'];
            $sheet = $objPHPExcel->getActiveSheet();

            if(is_null($template['category'])) {
                $templateHeaderDetails = [strtolower(trim($template['transactionNumber'])), strtolower(trim($template['transactionDate'])), strtolower(trim($template['debit'])), strtolower(trim($template['credit'])), strtolower(trim($template['description']))]; //$template['transactionDate'], $template['debit'], $template['credit'], $template['description'], $template['category']];
            } else {
                $templateHeaderDetails = [strtolower(trim($template['transactionNumber'])), strtolower(trim($template['transactionDate'])), strtolower(trim($template['debit'])), strtolower(trim($template['credit'])), strtolower(trim($template['description'])), strtolower(trim($template['category']))];
            }

            $rowValues = [];
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
            for ($col = 0; $col <= $highestColumnIndex +1; $col++) {
                $cellValue = strtolower(trim($sheet->getCellByColumnAndRow($col, $template['headerLine'])->getValue()));
                $columnPosition[strtolower(trim($cellValue))] = $col;
                if (!empty($cellValue)) {
                    $rowValues[] = $cellValue;
                }
            }
            $valuesExist = empty(array_diff($templateHeaderDetails, $rowValues));

            if ($valuesExist) {
                $firstLine = $template['firstLine'];
                $lastLine = ($template['firstLine'] + $transactionCount) - 1;
                $detailsArray = [];
                for ($row = $firstLine; $row <= $lastLine; $row++) {
                    $transactionNo = $sheet->getCellByColumnAndRow($columnPosition[strtolower(trim($template['transactionNumber']))], $row)->getValue();
                    $transactionDate = $sheet->getCellByColumnAndRow($columnPosition[strtolower(trim($template['transactionDate']))], $row)->getValue();
                    $debit = $sheet->getCellByColumnAndRow($columnPosition[strtolower(trim($template['debit']))], $row)->getValue();
                    $credit = $sheet->getCellByColumnAndRow($columnPosition[strtolower(trim($template['credit']))], $row)->getValue();
                    $description = $sheet->getCellByColumnAndRow($columnPosition[strtolower(trim($template['description']))], $row)->getValue();
                    if(!is_null($template['category'])) {
                        $category = $sheet->getCellByColumnAndRow($columnPosition[strtolower(trim($template['category']))], $row)->getValue();
                    }
                    if(is_null($transactionNo) || is_null($transactionDate) || is_null($description) || (is_null($credit) && is_null($debit))) {
                        BankStatementMaster::where('statementId', $statementMaster['statementId'])
                            ->update([
                                'importStatus' => 2,
                                'importError' => 'Statement values are missing for bank statement details'
                            ]);
                        DB::commit();
                        return;
                    }

                    $transactionDate = self::dateValidation($transactionDate);
                    if(is_null($transactionDate)) {
                        BankStatementMaster::where('statementId', $statementMaster['statementId'])
                            ->update([
                                'importStatus' => 2,
                                'importError' => 'Wrong date format for transaction date - Correct format "DD/MM/YYYY"'
                            ]);
                        DB::commit();
                        return;
                    }

                    $credit = str_replace(',', '', $credit);
                    $debit = str_replace(',', '', $debit);

                    $detailsArray[] = [
                        'statementId' => $statementMaster['statementId'],
                        'transactionNumber' => $transactionNo,
                        'transactionDate' => $transactionDate,
                        'debit' => $debit,
                        'credit' => $credit,
                        'description' => $description,
                        'category' => isset($category)?$category:null,
                        'createdDateTime' => Carbon::now(),
                        'timeStamp' => Carbon::now()
                    ];
                }
                BankStatementDetail::insert($detailsArray);
                BankStatementMaster::where('statementId', $statementMaster['statementId'])
                    ->update([
                        'importStatus' => 1
                    ]);
            } else {
                BankStatementMaster::where('statementId', $statementMaster['statementId'])
                    ->update([
                        'importStatus' => 2,
                        'importError' => 'Some detail columns are missing'
                    ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            BankStatementMaster::where('statementId', $statementMaster['statementId'])
                ->update([
                    'importStatus' => 2,
                    'importError' => "Statement upload failed. Please try re-uploading."
                ]);
        }
    }

    function dateValidation($date)
    {
        if (is_numeric($date)) {
            return Date::excelToDateTimeObject($date)->format('Y-m-d');
        } else {
            try {
                return Carbon::createFromFormat('d/m/Y', trim($date))->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }     
    }
}
