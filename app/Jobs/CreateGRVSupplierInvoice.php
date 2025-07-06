<?php

namespace App\Jobs;

use App\Models\BookInvSuppMaster;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\InterCompanyAssetDisposal;
use App\Models\SupplierInvoiceItemDetail;
use App\Repositories\BookInvSuppDetRepository;
use App\Repositories\BookInvSuppMasterRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateGRVSupplierInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $grvMasterAutoID;
    protected $dataBase;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($grvMasterAutoID, $dataBase)
    {
        //
        $this->grvMasterAutoID = $grvMasterAutoID;
        $this->dataBase = $dataBase;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(BookInvSuppMasterRepository $bookInvSuppMasterRepo, BookInvSuppDetRepository $bookInvSuppDetRepo)
    {
        DB::beginTransaction();
        try {
            Log::useFiles(storage_path() . '/logs/create_supplier_invoice_jobs.log');
            $grvMaster = GRVMaster::find($this->grvMasterAutoID);
            if ($grvMaster) {
                if ($grvMaster->interCompanyTransferYN == -1) {
                    $grvDetail = GRVDetails::selectRaw('SUM(landingCost_LocalCur) as landingCost_LocalCur,SUM(landingCost_RptCur) as landingCost_RptCur , SUM(landingCost_TransCur) as landingCost_TransCur, VATAmount ,VATAmountLocal, VATAmountRpt ,grvDetailsID,vatMasterCategoryID,vatSubCategoryID,exempt_vat_portion,purchaseOrderMastertID,grvAutoID')->where('grvAutoID', $this->grvMasterAutoID)->first();
                    $today = NOW();
                    $fromCompanyFinanceYear = CompanyFinanceYear::where('companySystemID', $grvMaster->companySystemID)->where('bigginingDate', '<', NOW())->where('endingDate', '>', NOW())->first();

                    $fromCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $grvMaster->companySystemID)->where('departmentSystemID', 10)->where('companyFinanceYearID', $fromCompanyFinanceYear->companyFinanceYearID)->where('dateFrom', '<', NOW())->where('dateTo', '>', NOW())->first();

                    if (!empty($fromCompanyFinanceYear)) {

                        $supplierInvoiceData['FYBiggin'] = $fromCompanyFinanceYear->bigginingDate;
                        $supplierInvoiceData['FYEnd'] = $fromCompanyFinanceYear->endingDate;

                        if (!empty($fromCompanyFinancePeriod)) {
                            $supplierInvoiceData['companyFinanceYearID'] = $fromCompanyFinancePeriod->companyFinanceYearID;
                            $supplierInvoiceData['companyFinancePeriodID'] = $fromCompanyFinancePeriod->companyFinancePeriodID;
                            $supplierInvoiceData['FYPeriodDateFrom'] = $fromCompanyFinancePeriod->dateFrom;
                            $supplierInvoiceData['FYPeriodDateTo'] = $fromCompanyFinancePeriod->dateTo;
                        }
                    }

                    $bookingInvLastSerial = BookInvSuppMaster::where('companySystemID', $grvMaster->companySystemID)
                        ->where('companyFinanceYearID', $fromCompanyFinancePeriod->companyFinanceYearID)
                        ->where('serialNo', '>', 0)
                        ->orderBy('serialNo', 'desc')
                        ->first();

                    $supInvLastSerialNumber = 1;
                    if ($bookingInvLastSerial) {
                        $supInvLastSerialNumber = intval($bookingInvLastSerial->serialNo) + 1;
                    }

                    $suppFinYear = '';
                    if ($fromCompanyFinancePeriod) {
                        $suppStartYear = $fromCompanyFinanceYear->bigginingDate;
                        $suppFinYearExp = explode('-', $suppStartYear);
                        $suppFinYear = $suppFinYearExp[0];
                    } else {
                        $suppFinYear = date("Y");
                    }

                    $comment = $grvMaster->grvNarration.','.$grvMaster->grvPrimaryCode;

                    $bookingAmountRpt = 0;
                    $bookingAmountLocal = 0;
                    $bookingAmountTrans = 0;

                    if ($grvDetail) {
                        $VATAmount = $grvDetail->VATAmount;
                        $VATAmountLocal = $grvDetail->VATAmountLocal;
                        $VATAmountRpt = $grvDetail->VATAmountRpt;

                        $bookingAmountLocal = $grvDetail->landingCost_LocalCur + $VATAmountLocal;
                        $bookingAmountRpt = $grvDetail->landingCost_RptCur + $VATAmountRpt;
                        $bookingAmountTrans = $grvDetail->landingCost_TransCur + $VATAmount;

                    }

                    $bookingInvCode = ($grvMaster->companyID . '\\' . $suppFinYear . '\\' . 'BSI' . str_pad($supInvLastSerialNumber, 6, '0', STR_PAD_LEFT));
                    $supplierInvoiceData['serialNo'] = $supInvLastSerialNumber;
                    $supplierInvoiceData['companySystemID'] = $grvMaster->companySystemID;
                    $supplierInvoiceData['companyID'] = $grvMaster->companyID;
                    $supplierInvoiceData['documentSystemID'] = 11;
                    $supplierInvoiceData['documentID'] = 'SI';

                    $supplierInvoiceData['bookingInvCode'] = $bookingInvCode;
                    $supplierInvoiceData['bookingDate'] = $today;
                    $supplierInvoiceData['comments'] = $comment;
                    $supplierInvoiceData['secondaryRefNo'] = null;
                    $supplierInvoiceData['supplierID'] = $grvMaster->supplierID;
                    $supplierInvoiceData['supplierGLCode'] = $grvMaster->liabilityAccount;
                    $supplierInvoiceData['supplierGLCodeSystemID'] = $grvMaster->liabilityAccountSysemID;
                    $supplierInvoiceData['UnbilledGRVAccountSystemID'] = $grvMaster->UnbilledGRVAccountSystemID;
                    $supplierInvoiceData['UnbilledGRVAccount'] = $grvMaster->UnbilledGRVAccount;
                    $supplierInvoiceData['supplierInvoiceNo'] = $grvMaster->grvDoRefNo;
                    $supplierInvoiceData['supplierInvoiceDate'] = $today;
                    $supplierInvoiceData['supplierTransactionCurrencyID'] = $grvMaster->supplierTransactionCurrencyID;
                    $supplierInvoiceData['supplierTransactionCurrencyER'] = 1;
                    $supplierInvoiceData['companyReportingCurrencyID'] = $grvMaster->companyReportingCurrencyID;
                    $supplierInvoiceData['companyReportingER'] = $grvMaster->companyReportingER;
                    $supplierInvoiceData['localCurrencyID'] = $grvMaster->localCurrencyID;
                    $supplierInvoiceData['localCurrencyER'] = $grvMaster->localCurrencyER;
                    $supplierInvoiceData['bookingAmountTrans'] = $bookingAmountTrans;
                    $supplierInvoiceData['bookingAmountLocal'] = $bookingAmountLocal;
                    $supplierInvoiceData['bookingAmountRpt'] = $bookingAmountRpt;
                    $supplierInvoiceData['confirmedYN'] = 1;
                    $supplierInvoiceData['confirmedByEmpSystemID'] = $grvMaster->grvConfirmedByEmpSystemID;
                    $supplierInvoiceData['confirmedByEmpID'] = $grvMaster->grvConfirmedByEmpID;
                    $supplierInvoiceData['confirmedByName'] = $grvMaster->grvConfirmedByName;
                    $supplierInvoiceData['confirmedDate'] = $today;
                    $supplierInvoiceData['approved'] = -1;
                    $supplierInvoiceData['approvedDate'] = $today;
                    $supplierInvoiceData['approvedByUserSystemID'] = $grvMaster->grvConfirmedByEmpSystemID;
                    $supplierInvoiceData['approvedByUserID'] = $grvMaster->grvConfirmedByEmpID;
                    $supplierInvoiceData['postedDate'] = $today;
                    $supplierInvoiceData['documentType'] =  2;
                    $supplierInvoiceData['RollLevForApp_curr'] = 1;
                    $supplierInvoiceData['interCompanyTransferYN'] = $grvMaster->interCompanyTransferYN;
                    $supplierInvoiceData['createdUserSystemID'] = $grvMaster->grvConfirmedByEmpSystemID;
                    $supplierInvoiceData['createdUserID'] = $grvMaster->grvConfirmedByName;
                    $supplierInvoiceData['createdPcID'] = gethostname();
                    $supplierInvoiceData['vatRegisteredYN'] = $grvMaster->vatRegisteredYN;
                    $bookInvSuppMaster = $bookInvSuppMasterRepo->create($supplierInvoiceData);
                    // supplier Invoice master end

                    if (!empty($bookInvSuppMaster)) {
                        // supplier Invoice details start
                        $supplierInvoiceDetail = array();
                        $supplierInvoiceDetail['bookingSuppMasInvAutoID'] = $bookInvSuppMaster->bookingSuppMasInvAutoID;
                        $supplierInvoiceDetail['unbilledgrvAutoID'] = $grvMaster->UnbilledGRVAccountSystemID;
                        $supplierInvoiceDetail['companySystemID'] = $bookInvSuppMaster->companySystemID;
                        $supplierInvoiceDetail['companyID'] = $bookInvSuppMaster->companyID;
                        $supplierInvoiceDetail['supplierID'] = $grvMaster->supplierID;
                        $supplierInvoiceDetail['purchaseOrderID'] = 0;
                        $supplierInvoiceDetail['grvAutoID'] = $grvMaster->grvAutoID;
                        $supplierInvoiceDetail['grvType'] = $grvMaster->grvType;
                        $supplierInvoiceDetail['supplierTransactionCurrencyID'] = $grvMaster->supplierTransactionCurrencyID;
                        $supplierInvoiceDetail['supplierTransactionCurrencyER'] = 1;
                        $supplierInvoiceDetail['companyReportingCurrencyID'] = $grvMaster->companyReportingCurrencyID;
                        $supplierInvoiceDetail['companyReportingER'] = $grvMaster->companyReportingER;
                        $supplierInvoiceDetail['localCurrencyID'] = $grvMaster->localCurrencyID;
                        $supplierInvoiceDetail['localCurrencyER'] = $grvMaster->localCurrencyER;
                        $supplierInvoiceDetail['supplierInvoOrderedAmount'] = 0;
                        $supplierInvoiceDetail['supplierInvoAmount'] = $bookingAmountTrans;
                        $supplierInvoiceDetail['transSupplierInvoAmount'] = $bookingAmountTrans;
                        $supplierInvoiceDetail['localSupplierInvoAmount'] = $bookingAmountLocal;
                        $supplierInvoiceDetail['rptSupplierInvoAmount'] = $bookingAmountRpt;
                        $supplierInvoiceDetail['totTransactionAmount'] = $bookingAmountTrans;
                        $supplierInvoiceDetail['totLocalAmount'] = $bookingAmountLocal;
                        $supplierInvoiceDetail['totRptAmount'] = $bookingAmountRpt;
                        $supplierInvoiceDetail['isAddon'] = 0;
                        $supplierInvoiceDetail['invoiceBeforeGRVYN'] = 0;
                        $supplierInvoiceDetail['timesReferred'] = 0;
                        $supplierInvoiceDetail['timeStamp'] = $today;
                        $supplierInvoiceDetail['VATAmount'] = $VATAmount;
                        $supplierInvoiceDetail['VATAmountLocal'] = $VATAmountLocal;
                        $supplierInvoiceDetail['VATAmountRpt'] = $VATAmountRpt;
                        $bookInvSuppDet = $bookInvSuppDetRepo->create($supplierInvoiceDetail);

                        if($bookInvSuppDet){
                            $details = [
                                'bookingSupInvoiceDetAutoID' => $bookInvSuppDet->bookingSupInvoiceDetAutoID,
                                'bookingSuppMasInvAutoID' => $bookInvSuppDet->bookingSuppMasInvAutoID,
                                'unbilledgrvAutoID' => $grvMaster->UnbilledGRVAccountSystemID,
                                'companySystemID' => $grvMaster->companySystemID,
                                'grvDetailsID' => $grvDetail->grvDetailsID,
                                'logisticID' => $grvMaster->grvLocation,
                                'vatMasterCategoryID' => $grvDetail->vatMasterCategoryID,
                                'vatSubCategoryID' => $grvDetail->vatSubCategoryID,
                                'exempt_vat_portion' => $grvDetail->exempt_vat_portion,
                                'purchaseOrderID' => $grvDetail->purchaseOrderMastertID,
                                'grvAutoID' => $grvDetail->grvAutoID,
                                'supplierTransactionCurrencyID' => $grvMaster->supplierTransactionCurrencyID,
                                'supplierTransactionCurrencyER' => 1,
                                'companyReportingCurrencyID' => $grvMaster->companyReportingCurrencyID,
                                'companyReportingER' => $grvMaster->companyReportingER,
                                'localCurrencyID' => $grvMaster->localCurrencyID,
                                'localCurrencyER' => $grvMaster->localCurrencyER,
                                'supplierInvoOrderedAmount' => 0,
                                'supplierInvoAmount' =>$bookingAmountTrans,
                                'transSupplierInvoAmount' =>$bookingAmountTrans,
                                'localSupplierInvoAmount' =>$bookingAmountLocal,
                                'rptSupplierInvoAmount' =>$bookingAmountRpt,
                                'totTransactionAmount' =>$bookingAmountTrans,
                                'totLocalAmount' =>$bookingAmountLocal,
                                'totRptAmount' =>$bookingAmountRpt,
                                'VATAmount' =>$VATAmount,
                                'VATAmountLocal' =>$VATAmountLocal,
                                'VATAmountRpt' =>$VATAmountRpt,
                            ];
                            $createInvoiceItemDetail = SupplierInvoiceItemDetail::create($details);
                        }
                    }

                    $masterModel = ['documentSystemID' => 11, 'autoID' => $bookInvSuppMaster->bookingSuppMasInvAutoID, 'companySystemID' => $bookInvSuppMaster->companySystemID, 'employeeSystemID' => $bookInvSuppMaster->confirmedByEmpSystemID];
                    $generalLedgerInsert = GeneralLedgerInsert::dispatch($masterModel, $this->dataBase);

                    $assetDisposal = InterCompanyAssetDisposal::where('grvID', $this->grvMasterAutoID)->first();

                    if ($assetDisposal) {
                        $assetDisposal->supplierInvoiceID = $bookInvSuppMaster->bookingSuppMasInvAutoID;
                        $assetDisposal->save();
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
