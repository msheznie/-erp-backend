<?php

namespace App\Http\Controllers\API\B2B;

use App\Exports\B2B\VendorFile\VendorFile;
use App\Http\Controllers\AppBaseController;
use App\Models\BankAccount;
use App\Models\BankConfig;
use App\Models\BankMaster;
use App\Models\BankMemoSupplier;
use App\Models\Company;
use App\Models\CurrencyMaster;
use App\Models\PaymentBankTransfer;
use App\Services\B2B\BankConfigService;
use App\Services\B2B\BankTransferService;
use Carbon\Carbon;
use Illuminate\Http\Request;
class B2BResourceAPIController extends AppBaseController
{

    private $vendorFile;
    private $details;
    private $headerDetails;

    private $bankTransferService;

    private $bankConfigService;

    private $requestType;

    private $bankTransferID;

    public function __construct(VendorFile $vendorFile, BankTransferService $bankTransferService,BankConfigService $bankConfigService)
    {
        $this->vendorFile = $vendorFile;
        $this->bankTransferService = $bankTransferService;
        $this->bankConfigService = $bankConfigService;
    }

    public function generateVendorFile(Request $request) {

        $bankMaster = BankMaster::with(['config'])
            ->where('bankmasterAutoID', PaymentBankTransfer::find($request->bankTransferID)->bankMasterID)
            ->whereHas('config')
            ->exists();

        if(!$bankMaster)
            return $this->sendError("The vendor file format is not available for the selected bank",500,[]);

        $requestNew = new Request([
            'companyId' => $request->companyID,
            'paymentBankTransferID' => $request->bankTransferID,
            'bankAccountAutoID' => PaymentBankTransfer::find($request->bankTransferID)->bankAccountAutoID,
            'isFromHistory' => 0
        ]);
        $data = app('App\Http\Controllers\API\BankLedgerAPIController')->getPaymentsByBankTransfer($requestNew);
        $result = $data->original['data'];

        if(collect($result)->where('pulledToBankTransferYN',-1)->isEmpty())
        {
            return $this->sendError("There is no payment voucher selected in the bank transfer list.",500,[]);
        }


        $this->requestType = $request->requestType;
        $this->bankTransferID = $request->bankTransferID;
        $this->setHeaderDetails($request);
        $bankTransferBankAccountDetails = BankAccount::find(PaymentBankTransfer::find($request->bankTransferID)->bankAccountAutoID);
        $detailsArray = array();

        foreach ($result as $rs)
        {
            $detailObject = new \App\Classes\B2B\Detail();
            $bankMemoDetails = BankMemoSupplier::where('supplierCodeSystem',$rs['payment_voucher']['BPVsupplierID'])->where('supplierCurrencyID',$rs['payment_voucher']['supplierTransCurrencyID'])->get();

            $detailObject->setSectionIndex('S2');
            $detailObject->setTransferMethod($this->setBankTransferMethod($rs));
            $detailObject->setCreditAmount(($rs['payment_voucher']['payAmountBank'] + $rs['payment_voucher']['VATAmountBank']),$rs['payment_voucher']['BPVbankCurrency']);
            $detailObject->setCreditCurrency($rs['payment_voucher']['supplierTransCurrencyID']);
            $detailObject->setExchangeRate($rs['payment_voucher']['BPVbankCurrencyER']);
            $detailObject->setDealRefNo("");
            $detailObject->setValueDate($rs['payment_voucher']['BPVdate']);
            $detailObject->setDebitAccountNo($bankTransferBankAccountDetails->AccountNo ?? null);
            $detailObject->setCreditAccountNo($bankMemoDetails->where('bankMemoTypeID',4)->first()['memoDetail'] ?? null);
            $detailObject->setTransactionReference($this->bankTransferService->generateBatchNo($request->companyID,$rs['documentCode'],$rs['payment_voucher']['serialNo']));
            $detailObject->setDebitNarrative($rs['payment_voucher']['BPVNarration'] ?? null);
            $detailObject->setDebitNarrative2("");
            $detailObject->setCreditNarrative("");
            $detailObject->setPaymentDetails1("payment details");
            $detailObject->setPaymentDetails2("payment details");
            $detailObject->setPaymentDetails3("");
            $detailObject->setPaymentDetails4("");
            $detailObject->setBeneficiaryName($rs['payment_voucher']['supplier']['supplierName']);
            $detailObject->setBeneficiaryAddress1("");
            $detailObject->setBeneficiaryAddress2("");
            $detailObject->setInstitutionNameAddress1($bankMemoDetails->where('bankMemoTypeID',2)->first()['memoDetail'] ?? null);
            $detailObject->setInstitutionNameAddress2($bankMemoDetails->where('bankMemoTypeID',3)->first()['memoDetail'] ?? null);
            $detailObject->setInstitutionNameAddress3($bankMemoDetails->where('bankMemoTypeID',3)->first()['memoDetail'] ?? null);
            $detailObject->setInstitutionNameAddress4($bankMemoDetails->where('bankMemoTypeID',3)->first()['memoDetail'] ?? null);
            $detailObject->setSwift($bankMemoDetails->where('bankMemoTypeID',9)->first()['memoDetail'] ?? null);
            $detailObject->setIntermediaryAccount($bankMemoDetails->where('bankMemoTypeID',11)->first()['memoDetail'] ?? null);
            $detailObject->setIntermediarySwift($bankMemoDetails->where('bankMemoTypeID',12)->first()['memoDetail'] ?? null);
            $detailObject->setIntermediaryName($bankMemoDetails->where('bankMemoTypeID',10)->first()['memoDetail'] ?? null);
            $detailObject->setIntermediaryAddress1(null);
            $detailObject->setIntermediaryAddress2(null);
            $detailObject->setIntermediaryAddress3(null);
            $detailObject->setChargesType("BEN");
            $detailObject->setSortCodeBeneficiaryBank($bankMemoDetails->where('bankMemoTypeID',14)->first()['memoDetail'] ?? null);
            $detailObject->setIFSC($bankMemoDetails->where('bankMemoTypeID',16)->first()['memoDetail'] ?? null);
            $detailObject->setFedwire($bankMemoDetails->where('bankMemoTypeID',18)->first()['memoDetail'] ?? null);
            $detailObject->setEmail($rs['payment_voucher']['supplier']['supEmail'] ?? null);
            $detailObject->setDispatchMode("E");
            $detailObject->setTransactorCode("transcator code");
            $detailObject->setSupportingDocumentName("");
            $detailObject->setPaymentVoucherCode($rs['documentCode']);

            array_push($detailsArray, (array) $detailObject);

        }

        $this->details = $detailsArray;

        return $this->downloadExcel();

    }

    private function setHeaderDetails($request)
    {
        $companyMaster = Company::find($request->companyID,['registrationNumber','companySystemID']);
        $bankTransfer = PaymentBankTransfer::find($request->bankTransferID,['bankAccountAutoID','documentDate','paymentBankTransferID','bankMasterID','narration','bankTransferDocumentCode','serialNumber']);
        $bankAccount = BankAccount::find($bankTransfer->bankAccountAutoID,['AccountNo']);

        $batchNo = $this->bankTransferService->generateBatchNo($request->companyID, $bankTransfer->bankTransferDocumentCode,$bankTransfer->serialNumber);
        $headerDetails = [
            [
                "S1",
                $companyMaster->registrationNumber,
                $bankAccount->AccountNo,
                'MXD',
                1,
                $bankTransfer->narration,
                Carbon::parse($bankTransfer->documentDate)->format('d/m/Y'),
                $batchNo
            ]
        ];

        $bankTransfer->batchReference = $batchNo;
        $bankTransfer->save();
        $this->headerDetails = $headerDetails;
    }

    private function setBankTransferMethod($rs): string
    {
        $bankTransfer = PaymentBankTransfer::find($rs['paymentBankTransferID']);
        $data = [
            "from" => [
                "bankID" => (int) $rs['payment_voucher']['BPVbank'],
                "bankAccountID" => (int) $rs['payment_voucher']['BPVAccount'],
                "currency" => (int) $rs['payment_voucher']['BPVbankCurrency']
            ],
            "to" => [
                "bankID" => (int) $bankTransfer->bankMasterID,
                "bankAccountID" => (int) $bankTransfer->bankAccountAutoID,
                "currency" => ""
            ]
        ];

        return BankTransferService::getBankTransferType($data);
    }

    public function downloadExcel()
    {

        $footerDetails = [
            ['S3',count($this->details),collect($this->details)->sum('credit_amount')]
        ];


        $this->vendorFile->setHeaderData($this->headerDetails);
        $this->vendorFile->setDetailsData($this->details);
        $this->vendorFile->setFooterData($footerDetails);

        if(!empty(array_flatten($this->vendorFile->detailsDataErros)) || !empty(array_flatten($this->vendorFile->headerErrors)))
        {
            return $this->sendError("Validaiton failed on some documents", 500, [
                'detailsErrors' => (!empty(array_flatten($this->vendorFile->detailsDataErros))) ? $this->vendorFile->detailsDataErros : null,
                'headerErrors' => (!empty(array_flatten($this->vendorFile->headerErrors))) ? $this->vendorFile->headerErrors : null
            ]);

        }


        $templateName = "export_report.B2B.vendor_file";
        $reportData = [
            "header" => $this->vendorFile->header(),
            "detail" => $this->vendorFile->detail(),
            "footer" => $this->vendorFile->footer()
        ];

        $excelColumnFormat = [
            'A' => \PHPExcel_Style_NumberFormat::FORMAT_GENERAL,
            'C' => \PHPExcel_Style_NumberFormat::FORMAT_GENERAL,
            'G' => \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
        ];


        if($this->requestType == 0)
        {
            return \Excel::create('vendorFile', function ($excel) use ($reportData, $templateName, $excelColumnFormat) {
                $excel->sheet('New sheet', function ($sheet) use ($reportData, $templateName, $excelColumnFormat) {
                    $sheet->setColumnFormat($excelColumnFormat);
                    $sheet->loadView($templateName, $reportData);
                    $sheet->setAutoSize(true);
                });
            })->download('xlsx');
        }else {

            return $this->submitVendorFile($reportData,$templateName,$excelColumnFormat);
        }
    }


    private function submitVendorFile($reportData,$templateName,$excelColumnFormat)
    {

        $paymentBankTransfer = PaymentBankTransfer::find($this->bankTransferID);
        $getConfigDetails = BankConfig::where('slug','ahlibank')->where('bank_master_id',($paymentBankTransfer->bankMasterID))->first();

        if(empty($getConfigDetails))
            return $this->sendError("The vendor file format is not available for the selected bank",500,[]);

        $filePath = storage_path('app/temp/');
        $fileName = "vendorFile".Carbon::now()->format('dmyHis');



        $isStored  =  \Excel::create($fileName, function ($excel) use ($reportData, $templateName, $excelColumnFormat) {
            $excel->sheet('New sheet', function ($sheet) use ($reportData, $templateName, $excelColumnFormat) {
                $sheet->setColumnFormat($excelColumnFormat);
                $sheet->loadView($templateName, $reportData);
                $sheet->setAutoSize(true);
            });
        })->store('xlsx',$filePath);


        if($isStored) {
//            $submitFile = $this->bankConfigService->uploadFileToBank($fileName,$this->bankTransferID);
//            if (!is_null($submitFile) && $submitFile->getData() && !$submitFile->getData()->success) {
//                return $this->sendError($submitFile->getData()->message, 500,[]);
//            }

            $getConfigDetails = BankConfig::where('slug', 'ahlibank')->first();
            $config = collect($getConfigDetails['details'])->where('fileType', 0)->first();
            $pathDetails = $getConfigDetails;
            $configDetails = [
                'driver' => 'sftp',
                'host' => $config['connectionDetails']['host'] ?? '',
                'username' => $config['connectionDetails']['username'] ?? '',
                'password' => $config['connectionDetails']['password'] ?? '',
                'port' => $config['connectionDetails']['port'] ?? 22,
                'root' => $config['connectionDetails']['root'] ?? '/',
                'timeout' => 50,
            ];
            config(['filesystems.disks.sftp' => $configDetails]);
            $storage = \Storage::disk('sftp');

            $disk = $storage;
            try {

                if (!isset($configDetails))
                    throw new \Exception("The vendor file format is not available for the selected bank");

                if (!isset($pathDetails) || !isset($pathDetails->details[0]['upload_path']))
                    throw new \Exception("Upload path not found!");

                $filePath = storage_path('app/temp/' . $fileName) . '.xlsx';

                $remotePath = $pathDetails->details[0]['upload_path'] . "/" . $fileName . '.xlsx';
                if (file_exists($filePath)) {
                    $disk->put($remotePath, file_get_contents($filePath));
                    $this->bankTransferService->updateStatus($this->bankTransferID, 'success');
                } else {
                    $this->bankTransferService->updateStatus($this->bankTransferID, 'failed');
                }

            } catch (\Exception $exception) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage()
                ], 500);
            }
            $fullFilePath = $filePath . $fileName.'.xlsx';
            if (file_exists($fullFilePath)) {
                unlink($fullFilePath);
            }
        }

        return $this->sendResponse([],'File submitted');

    }

    public function downloadErrorLogFromPortal(Request $request)
    {
        $supplierBankTransfer = PaymentBankTransfer::find($request->bankTransferID);
        $getConfigDetails = BankConfig::where('slug','ahlibank')->where('bank_master_id',$supplierBankTransfer->bankMasterID)->first();

        if(!isset($getConfigDetails))
            return $this->sendError("The vendor file format is not available for the selected bank!",500,[]);

        $supplierBankTransfer = PaymentBankTransfer::find($request->bankTransferID);
        $getConfigDetails = BankConfig::where('slug', 'ahlibank')->where('bank_master_id', $supplierBankTransfer->bankMasterID)->first();
        if (!isset($getConfigDetails))
            return $this->sendError("The vendor file format is not available for the selected bank");

        $config = collect($getConfigDetails['details'])->where('fileType', 0)->first();
        if ($config['failure_path']) {
            $configDetails = [
                'driver' => 'sftp',
                'host' => $config['connectionDetails']['host'] ?? '',
                'username' => $config['connectionDetails']['username'] ?? '',
                'password' => $config['connectionDetails']['password'] ?? '',
                'port' => $config['connectionDetails']['port'] ?? 22,
                'root' => $config['connectionDetails']['root'] ?? '/',
                'timeout' => 50,
            ];
            config(['filesystems.disks.sftp' => $configDetails]);
            $storage = \Storage::disk('sftp');
            try {
                $disk = $storage;
                $files = $disk->files($config['failure_path']);
                foreach ($files as $file) {
                    $filePath = $file->getRealPath();
                    $file = file_get_contents($filePath);
                    $batchReference = preg_quote($supplierBankTransfer->batchReference, '/'); // Escape special characters
                    $pattern = "/Batch Number:\s*" . $batchReference . "/"; // Proper regex with delimiters
                    if (preg_match($pattern, $file, $matches)) {
                        return response()->file($filePath);
                    }
                }
            } catch (\Exception $exception) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage()
                ], 500);
            }
        }
    }
}
