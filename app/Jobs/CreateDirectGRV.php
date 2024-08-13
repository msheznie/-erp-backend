<?php

namespace App\Jobs;

use App\Models\AssetDisposalDetail;
use App\Models\AssetDisposalMaster;
use App\Models\Company;
use App\Models\InterCompanyAssetDisposal;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
use App\Models\GRVMaster;
use App\Models\SegmentMaster;
use App\Models\SupplierCurrency;
use App\Models\SupplierMaster;
use App\Repositories\GRVDetailsRepository;
use App\Repositories\GRVMasterRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateDirectGRV implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $disposalMaster;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($disposalMaster)
    {
        //
        $this->disposalMaster = $disposalMaster;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GRVMasterRepository $grvMasterRepo,
                           GRVDetailsRepository $grvDetailsRepository)
    {
        //
        Log::useFiles(storage_path() . '/logs/create_direct_grv_jobs.log');
        $data = $this->disposalMaster;
        $dpMaster = AssetDisposalMaster::find($data['assetdisposalMasterAutoID']);
        $invoiceCode = $data['invoiceCode'];
        DB::beginTransaction();
        try {
            $directGRV = array();
            $directGRV["grvTypeID"] = 1;
            $directGRV["grvType"] = 'DIG';
            $directGRV["companySystemID"] = $dpMaster->toCompanySystemID;
            $directGRV["companyID"] = $dpMaster->toCompanyID;

            $serviceLine = SegmentMaster::ofCompany([$dpMaster->toCompanySystemID])->isPublic()->first();

            if ($serviceLine) {
                $directGRV["serviceLineSystemID"] = $serviceLine->serviceLineSystemID;
                $directGRV["serviceLineCode"] = $serviceLine->ServiceLineCode;
            }

            $disposalDocumentDate = (new Carbon($dpMaster->disposalDocumentDate))->format('Y-m-d');

            $fromCompanyFinanceYear = CompanyFinanceYear::where('companySystemID', $dpMaster->toCompanySystemID)
                ->whereDate('bigginingDate', '<=', $disposalDocumentDate)
                ->whereDate('endingDate', '>=', $disposalDocumentDate)
                ->first();

            if (!empty($fromCompanyFinanceYear)) {

                $fromCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $dpMaster->toCompanySystemID)
                    ->where('departmentSystemID', 10)
                    ->where('companyFinanceYearID', $fromCompanyFinanceYear->companyFinanceYearID)
                    ->whereDate('dateFrom', '<=', $disposalDocumentDate)
                    ->whereDate('dateTo', '>=', $disposalDocumentDate)
                    ->first();

                if(!empty($fromCompanyFinancePeriod)){
                    $today = $dpMaster->disposalDocumentDate;
                    $comment = "Inter Company Asset transfer from " . $dpMaster->companyID . " to " . $dpMaster->toCompanyID . " - " . $dpMaster->disposalDocumentCode . ',' . $invoiceCode;

                    $directGRV['FYBiggin'] = $fromCompanyFinanceYear->bigginingDate;
                    $directGRV['FYEnd'] = $fromCompanyFinanceYear->endingDate;

                    $directGRV['companyFinanceYearID'] = $fromCompanyFinancePeriod->companyFinanceYearID;
                    $directGRV['companyFinancePeriodID'] = $fromCompanyFinancePeriod->companyFinancePeriodID;

                    $directGRV['documentSystemID'] = 3;
                    $directGRV['documentID'] = 'GRV';
                    $grvLastSerial = GRVMaster::where('companySystemID', $dpMaster->toCompanySystemID)
                        ->where('companyFinanceYearID', $fromCompanyFinancePeriod->companyFinanceYearID)
                        ->where('grvSerialNo', '>', 0)
                        ->orderBy('grvSerialNo', 'desc')
                        ->first();

                    $grvInvLastSerialNumber = 1;
                    if ($grvLastSerial) {
                        $grvInvLastSerialNumber = intval($grvLastSerial->grvSerialNo) + 1;
                    }
                    $directGRV['grvSerialNo'] = $grvInvLastSerialNumber;
                    if ($fromCompanyFinancePeriod) {
                        $grvStartYear = $fromCompanyFinanceYear->bigginingDate;
                        $grvFinYearExp = explode('-', $grvStartYear);
                        $grvFinYear = $grvFinYearExp[0];
                    } else {
                        $grvFinYear = date("Y");
                    }
                    $grvCode = ($dpMaster->toCompanyID . '\\' . $grvFinYear . '\\' . $directGRV['documentID'] . str_pad($grvInvLastSerialNumber, 6, '0', STR_PAD_LEFT));
                    $directGRV['grvPrimaryCode'] = $grvCode;
                    $directGRV['grvDate'] = $today;
                    $directGRV['stampDate'] = $today;
                    $directGRV['grvNarration'] = $comment;

                    $supplier = SupplierMaster::where('companyLinkedToSystemID', $dpMaster->companySystemID)->first();

                    if (!empty($supplier)) {
                        $directGRV['supplierID'] = $supplier->supplierCodeSystem;
                        $directGRV['supplierPrimaryCode'] = $supplier->primarySupplierCode;
                        $directGRV['supplierName'] = $supplier->supplierName;
                        $directGRV['supplierAddress'] = $supplier->address;
                        $directGRV['supplierTelephone'] = $supplier->telephone;
                        $directGRV['supplierFax'] = $supplier->fax;
                        $directGRV['supplierEmail'] = $supplier->supEmail;
                        $directGRV['liabilityAccountSysemID'] = $supplier->liabilityAccountSysemID;
                        $directGRV['liabilityAccount'] = $supplier->liabilityAccount;
                        $directGRV['UnbilledGRVAccountSystemID'] = $supplier->UnbilledGRVAccountSystemID;
                        $directGRV['UnbilledGRVAccount'] = $supplier->UnbilledGRVAccount;
                    }

                    $fromCompany = Company::where('companySystemID', $dpMaster->companySystemID)->first();
                    $toCompany = Company::where('companySystemID', $dpMaster->toCompanySystemID)->first();
                    $companyCurrencyConversion = \Helper::currencyConversion($dpMaster->toCompanySystemID, $fromCompany->reportingCurrency, $fromCompany->reportingCurrency, 0);

                    $directGRV['localCurrencyID'] = $toCompany->localCurrencyID;
                    $directGRV['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $directGRV['companyReportingCurrencyID'] = $toCompany->reportingCurrency;
                    $directGRV['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];

                    $supplierCurrency = SupplierCurrency::where('supplierCodeSystem', $supplier->supplierCodeSystem)
                        ->where('isDefault', -1)
                        ->first();

                    if ($supplierCurrency) {
                        $erCurrency = CurrencyMaster::where('currencyID', $supplierCurrency->currencyID)->first();
                        $directGRV['supplierDefaultCurrencyID'] = $supplierCurrency->currencyID;
                        if ($erCurrency) {
                            $directGRV['supplierDefaultER'] = $erCurrency->ExchangeRate;
                        }
                    }

                    $directGRV['supplierTransactionCurrencyID'] = $fromCompany->reportingCurrency;
                    $directGRV['supplierTransactionER'] = 1;
                    $directGRV['interCompanyTransferYN'] = -1;
                    $directGRV['FromCompanyID'] = $dpMaster->companyID;
                    $directGRV['FromCompanySystemID'] = $dpMaster->companySystemID;
                    $directGRV['grvDoRefNo'] = $invoiceCode;
                    $directGRV['vatRegisteredYN'] = $dpMaster->vatRegisteredYN;

                    $directGRV['createdPcID'] = gethostname();
                    $directGRV['createdUserSystemID'] = $dpMaster->confimedByEmpSystemID;
                    $directGRV['createdUserID'] = $dpMaster->confimedByEmpID;

                    $grvMaster = $grvMasterRepo->create($directGRV);

                    $interComapnyData = InterCompanyAssetDisposal::where('assetDisposalID', $data['assetdisposalMasterAutoID'])
                                                                 ->first();

                    $interComapnyData->grvID = $grvMaster['grvAutoID'];

                    $interComapnyData->save();


                    $disposalDetail = AssetDisposalDetail::with(['item_by' => function ($query) {
                        $query->with('financeSubCategory');
                    }])->OfMaster($dpMaster->assetdisposalMasterAutoID)->get();

                    if ($disposalDetail) {
                        foreach ($disposalDetail as $val) {
                            $directGRVDet['grvAutoID'] = $grvMaster['grvAutoID'];
                            $directGRVDet['companySystemID'] = $dpMaster->toCompanySystemID;
                            $directGRVDet['companyID'] = $dpMaster->toCompanyID;
                            if ($serviceLine) {
                                //$directGRVDet["serviceLineSystemID"] = $serviceLine->serviceLineSystemID;
                                $directGRVDet["serviceLineCode"] = $serviceLine->ServiceLineCode;
                            }
                            $directGRVDet['itemCode'] = $val->itemCode;
                            $directGRVDet['itemPrimaryCode'] = $val->item_by->primaryCode;
                            $directGRVDet['itemDescription'] = $val->item_by->itemDescription;
                            $directGRVDet['itemFinanceCategoryID'] = $val->item_by->financeCategoryMaster;
                            $directGRVDet['itemFinanceCategorySubID'] = $val->item_by->financeCategorySub;
                            $directGRVDet['financeGLcodebBSSystemID'] = $val->item_by->financeSubCategory->financeGLcodebBSSystemID;
                            $directGRVDet['financeGLcodebBS'] = $val->item_by->financeSubCategory->financeGLcodebBS;
                            $directGRVDet['financeGLcodePLSystemID'] = $val->item_by->financeSubCategory->financeGLcodePLSystemID;
                            $directGRVDet['financeGLcodePL'] = $val->item_by->financeSubCategory->financeGLcodePL;
                            $directGRVDet['includePLForGRVYN'] = $val->item_by->financeSubCategory->includePLForGRVYN;
                            $directGRVDet['supplierPartNumber'] = $val->item_by->secondaryItemCode;
                            $directGRVDet['unitOfMeasure'] = 7;

                            $directGRVDet['noQty'] = 1;
                            $directGRVDet['prvRecievedQty'] = 1;
                            $directGRVDet['poQty'] = 1;

                            /*$comRptAmountDetail = 0;

                            if (round($val->netBookValueRpt, 2) == 0) {
                                $comRptAmountDetail = $val->costUnitRpt * ($dpMaster->revenuePercentage / 100);
                            } else {
                                $comRptAmountDetail = ($val->netBookValueRpt + (($val->netBookValueRpt) * ($dpMaster->revenuePercentage / 100)));
                            }*/

                            $comRptAmountDetail = $val->sellingPriceRpt;

                            $directGRVDet['unitCost'] = $comRptAmountDetail;
                            $directGRVDet['netAmount'] = $comRptAmountDetail;
                            $directGRVDet['comment'] = $val->faCode;
                            if ($supplierCurrency) {
                                $erCurrency = CurrencyMaster::where('currencyID', $supplierCurrency->currencyID)->first();
                                $directGRVDet['supplierDefaultCurrencyID'] = $supplierCurrency->currencyID;
                                if ($erCurrency) {
                                    $directGRVDet['supplierDefaultER'] = $erCurrency->ExchangeRate;
                                }
                            }

                            $currency = \Helper::convertAmountToLocalRpt($grvMaster->documentSystemID, $grvMaster['grvAutoID'], $comRptAmountDetail);

                            $directGRVDet['supplierDefaultCurrencyID'] = $grvMaster['supplierDefaultCurrencyID'];
                            $directGRVDet['supplierDefaultER'] = $grvMaster['supplierDefaultER'];
                            $directGRVDet['supplierItemCurrencyID'] = $grvMaster['supplierTransactionCurrencyID'];
                            $directGRVDet['foreignToLocalER'] = $grvMaster['supplierTransactionER'];
                            $directGRVDet['companyReportingCurrencyID'] = $grvMaster['companyReportingCurrencyID'];
                            $directGRVDet['companyReportingER'] = $grvMaster['companyReportingER'];
                            $directGRVDet['localCurrencyID'] = $grvMaster['localCurrencyID'];
                            $directGRVDet['localCurrencyER'] = $grvMaster['localCurrencyER'];

                            $currencyVat = \Helper::convertAmountToLocalRpt($grvMaster->documentSystemID, $grvMaster['grvAutoID'], $val->vatAmount);

                            $directGRVDet['VATPercentage'] = $val->vatPercentage;
                            $directGRVDet['VATAmount'] = \Helper::roundValue($val->vatAmount);
                            $directGRVDet['VATAmountLocal'] = $currencyVat['localAmount'];
                            $directGRVDet['VATAmountRpt'] = $currencyVat['reportingAmount'];

                            $directGRVDet['GRVcostPerUnitLocalCur'] = \Helper::roundValue($currency['localAmount'] + $currencyVat['localAmount']);
                            $directGRVDet['GRVcostPerUnitSupDefaultCur'] = \Helper::roundValue($currency['defaultAmount'] + $directGRVDet['VATAmount']);
                            $directGRVDet['GRVcostPerUnitSupTransCur'] = \Helper::roundValue($comRptAmountDetail + $currencyVat['reportingAmount']);
                            $directGRVDet['GRVcostPerUnitComRptCur'] = \Helper::roundValue($currency['reportingAmount'] + $currencyVat['reportingAmount']);
                            $directGRVDet['landingCost_LocalCur'] = \Helper::roundValue($currency['localAmount'] + $currencyVat['localAmount']);
                            $directGRVDet['landingCost_TransCur'] = \Helper::roundValue($comRptAmountDetail + $currencyVat['reportingAmount']);
                            $directGRVDet['landingCost_RptCur'] = \Helper::roundValue($currency['reportingAmount'] + $currencyVat['reportingAmount']);

                            $directGRVDet['vatRegisteredYN'] = $dpMaster->vatRegisteredYN;



                            $directGRVDet['createdPcID'] = gethostname();
                            $directGRVDet['createdUserSystemID'] = $dpMaster->confimedByEmpSystemID;
                            $directGRVDet['createdUserID'] = $dpMaster->confimedByEmpID;
                            $item = $grvDetailsRepository->create($directGRVDet);
                        }
                        DB::commit();
                    }
                }else{
                }

            }else{
            }


        } catch
        (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
        }
    }
}
