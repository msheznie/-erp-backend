<?php

namespace App\Repositories;

use App\Models\VatReturnFillingMaster;
use App\Models\TaxLedgerDetail;
use App\Models\VatReturnFillingDetail;
use App\Models\VatReturnFillingCategory;
use App\Models\Company;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class VatReturnFillingMasterRepository
 * @package App\Repositories
 * @version September 9, 2021, 1:08 pm +04
 *
 * @method VatReturnFillingMaster findWithoutFail($id, $columns = ['*'])
 * @method VatReturnFillingMaster find($id, $columns = ['*'])
 * @method VatReturnFillingMaster first($columns = ['*'])
*/
class VatReturnFillingMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'documentSystemID',
        'date',
        'comment',
        'confirmedYN',
        'confirmedDate',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByEmpName',
        'approvedYN',
        'approvedDate',
        'approvedByUserSystemID',
        'approvedEmpID',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return VatReturnFillingMaster::class;
    }

    public function generateFilling($date, $categoryID, $companySystemID, $forUpdate = false, $returnFilledDetailID = null, $confirmedYN = 0, $vatReturnFillingMasterID = null)
    {
        $linkedTaxLedgerDetails = [];
        $taxableAmount = 0;
        $taxAmount = 0;
        $taxLedgerDetail = [];
        $taxLedgerDetailData = [];
        $company = Company::where('companySystemID', $companySystemID)->first();
        $companyCountry = isset($company->companyCountry) ? $company->companyCountry : null;
        switch ($categoryID) {
            case 2:
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                                                  ->whereDate('documentDate', '<=', $date)
                                                  ->where('companySystemID', $companySystemID)
                                                  ->whereHas('customer', function($query) use ($companyCountry){
                                                      $query->where('customerCountry', $companyCountry);
                                                  })
                                                  ->whereNotNull('outputVatGLAccountID')
                                                  ->when($forUpdate == false, function($query) {
                                                    $query->select('VATAmountLocal', 'taxableAmountLocal', 'id')
                                                          ->whereNull('returnFilledDetailID');
                                                  })
                                                  ->when($forUpdate == true && $confirmedYN == 0, function($query) use ($returnFilledDetailID){
                                                    $query->where(function($query) use ($returnFilledDetailID) {
                                                          $query->whereNull('returnFilledDetailID')
                                                                ->orWhere('returnFilledDetailID', $returnFilledDetailID);
                                                        });
                                                  })
                                                   ->when($forUpdate == true && $confirmedYN == 1, function($query) use ($returnFilledDetailID){
                                                    $query->where(function($query) use ($returnFilledDetailID) {
                                                          $query->where('returnFilledDetailID', $returnFilledDetailID);
                                                        });
                                                  })
                                                  ->whereHas('sub_category', function($query) {
                                                        $query->whereHas('type', function($query) { 
                                                            $query->where('id', 1);
                                                        });
                                                  });


                $taxLedgerDetail = $taxLedgerDetailData->get();
                break;
            case 3:
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                                                  ->whereDate('documentDate', '<=', $date)
                                                  ->where('companySystemID', $companySystemID)
                                                  ->whereHas('customer', function($query) use ($companyCountry){
                                                      $query->where('customerCountry', $companyCountry);
                                                  })
                                                  ->whereNotNull('outputVatGLAccountID')
                                                  ->when($forUpdate == false, function($query) {
                                                    $query->select('VATAmountLocal', 'taxableAmountLocal', 'id')
                                                          ->whereNull('returnFilledDetailID');
                                                  })
                                                  ->when($forUpdate == true && $confirmedYN == 0, function($query) use ($returnFilledDetailID){
                                                    $query->where(function($query) use ($returnFilledDetailID) {
                                                          $query->whereNull('returnFilledDetailID')
                                                                ->orWhere('returnFilledDetailID', $returnFilledDetailID);
                                                        });
                                                  })
                                                   ->when($forUpdate == true && $confirmedYN == 1, function($query) use ($returnFilledDetailID){
                                                    $query->where(function($query) use ($returnFilledDetailID) {
                                                          $query->where('returnFilledDetailID', $returnFilledDetailID);
                                                        });
                                                  })
                                                  ->whereHas('sub_category', function($query) {
                                                        $query->whereHas('type', function($query) { 
                                                            $query->where('id', 2);
                                                        });
                                                  });


                $taxLedgerDetail = $taxLedgerDetailData->get();
                break;
            case 4:
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                                                  ->whereDate('documentDate', '<=', $date)
                                                  ->where('companySystemID', $companySystemID)
                                                  ->whereHas('customer', function($query) use ($companyCountry){
                                                      $query->where('customerCountry', $companyCountry);
                                                  })
                                                  ->whereNotNull('outputVatGLAccountID')
                                                  ->when($forUpdate == false, function($query) {
                                                    $query->select('VATAmountLocal', 'taxableAmountLocal', 'id')
                                                          ->whereNull('returnFilledDetailID');
                                                  })
                                                  ->when($forUpdate == true && $confirmedYN == 0, function($query) use ($returnFilledDetailID){
                                                    $query->where(function($query) use ($returnFilledDetailID) {
                                                          $query->whereNull('returnFilledDetailID')
                                                                ->orWhere('returnFilledDetailID', $returnFilledDetailID);
                                                        });
                                                  })
                                                   ->when($forUpdate == true && $confirmedYN == 1, function($query) use ($returnFilledDetailID){
                                                    $query->where(function($query) use ($returnFilledDetailID) {
                                                          $query->where('returnFilledDetailID', $returnFilledDetailID);
                                                        });
                                                  })
                                                  ->whereHas('sub_category', function($query) {
                                                        $query->whereHas('type', function($query) { 
                                                            $query->where('id', 3);
                                                        });
                                                  });


                $taxLedgerDetail = $taxLedgerDetailData->get();
                break;
            case 10:
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                                                  ->whereDate('documentDate', '<=', $date)
                                                  ->where('companySystemID', $companySystemID)
                                                  ->whereHas('supplier', function($query) use ($companyCountry){
                                                      $query->where('supplierCountryID', '!=', $companyCountry);
                                                  })
                                                  ->where('rcmApplicableYN', 1)
                                                  ->whereNotNull('inputVATGlAccountID')
                                                  ->when($forUpdate == false, function($query) {
                                                        $query->select('VATAmountLocal', 'taxableAmountLocal', 'id')
                                                              ->whereNull('returnFilledDetailID');
                                                  })
                                                  ->when($forUpdate == true && $confirmedYN == 0, function($query) use ($returnFilledDetailID){
                                                        $query->where(function($query) use ($returnFilledDetailID) {
                                                                  $query->whereNull('returnFilledDetailID')
                                                                        ->orWhere('returnFilledDetailID', $returnFilledDetailID);
                                                                });
                                                  })
                                                   ->when($forUpdate == true && $confirmedYN == 1, function($query) use ($returnFilledDetailID){
                                                        $query->where(function($query) use ($returnFilledDetailID) {
                                                              $query->where('returnFilledDetailID', $returnFilledDetailID);
                                                            });
                                                  })
                                                  ->whereHas('supplier_invoice_details', function($query) {
                                                        $query->whereHas('grv_detail', function($query) { 
                                                            $query->where('itemFinanceCategoryID', 2);
                                                        });
                                                  })
                                                  ->where('documentSystemID', 11)
                                                  ->whereHas('supplier_invoice', function($query) {
                                                        $query->where('documentType', 0);
                                                  });


                $taxLedgerDetail = $taxLedgerDetailData->get();
                break;
            case 12:
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                                                  ->whereDate('documentDate', '<=', $date)
                                                  ->where('companySystemID', $companySystemID)
                                                  ->whereHas('customer', function($query) use ($companyCountry){
                                                      $query->where('customerCountry', '!=',$companyCountry);
                                                  })
                                                  ->whereNotNull('outputVatGLAccountID')
                                                  ->when($forUpdate == false, function($query) {
                                                    $query->select('VATAmountLocal', 'taxableAmountLocal', 'id')
                                                          ->whereNull('returnFilledDetailID');
                                                  })
                                                  ->where(function($query) {
                                                        $query->where(function($query) {
                                                                  $query->whereHas('customer_invoice', function($query) {
                                                                            $query->whereIn('isPerforma', [2,3,4,5]);
                                                                        })
                                                                        ->where('documentSystemID', 20)
                                                                        ->whereHas('customer_invoice_details', function($query) {
                                                                            $query->whereIn('itemFinanceCategoryID', [1,2,4]);
                                                                        });
                                                              })
                                                              ->orWhere(function($query) {
                                                                  $query->where('documentSystemID', 87)
                                                                        ->whereHas('sales_return_details', function($query) {
                                                                            $query->whereIn('itemFinanceCategoryID', [1,2,4]);
                                                                        });
                                                              });
                                                  })
                                                  ->when($forUpdate == true && $confirmedYN == 0, function($query) use ($returnFilledDetailID){
                                                        $query->where(function($query) use ($returnFilledDetailID) {
                                                              $query->whereNull('returnFilledDetailID')
                                                                    ->orWhere('returnFilledDetailID', $returnFilledDetailID);
                                                            });
                                                  })
                                                  ->when($forUpdate == true && $confirmedYN == 1, function($query) use ($returnFilledDetailID){
                                                        $query->where(function($query) use ($returnFilledDetailID) {
                                                              $query->where('returnFilledDetailID', $returnFilledDetailID);
                                                            });
                                                  })
                                                  ->whereHas('sub_category', function($query) {
                                                        $query->whereHas('type', function($query) { 
                                                            $query->where('id', 2);
                                                        });
                                                  });


                $taxLedgerDetail = $taxLedgerDetailData->get();
                break;
            case 15:
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                                                  ->whereDate('documentDate', '<=', $date)
                                                  ->where('companySystemID', $companySystemID)
                                                  ->where('rcmApplicableYN', 1)
                                                  ->whereNotNull('inputVATGlAccountID')
                                                  ->when($forUpdate == false, function($query) {
                                                        $query->select('VATAmountLocal', 'taxableAmountLocal', 'id')
                                                              ->whereNull('returnFilledDetailID');
                                                  })
                                                  ->when($forUpdate == true && $confirmedYN == 0, function($query) use ($returnFilledDetailID){
                                                        $query->where(function($query) use ($returnFilledDetailID) {
                                                                  $query->whereNull('returnFilledDetailID')
                                                                        ->orWhere('returnFilledDetailID', $returnFilledDetailID);
                                                                });
                                                  })
                                                   ->when($forUpdate == true && $confirmedYN == 1, function($query) use ($returnFilledDetailID){
                                                        $query->where(function($query) use ($returnFilledDetailID) {
                                                              $query->where('returnFilledDetailID', $returnFilledDetailID);
                                                            });
                                                  })
                                                  ->where(function($query) {
                                                    $query->where(function($query) {
                                                            $query->whereHas('sub_category', function($query) {
                                                                      $query->whereIn('subCatgeoryType', [2,3]);
                                                                  })
                                                                  ->where('logisticYN', 0);
                                                        })->orWhere(function($query) {
                                                            $query->where('addVATonPO', 1)
                                                                  ->whereHas('sub_category', function($query) {
                                                                      $query->where('subCatgeoryType', 1);
                                                                  })
                                                                  ->where('logisticYN', 1);
                                                        });
                                                  });


                $taxLedgerDetailResultOne = $taxLedgerDetailData->get();


                $taxLedgerDetailDataPortion = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                                                  ->whereDate('documentDate', '<=', $date)
                                                  ->where('companySystemID', $companySystemID)
                                                  ->where('rcmApplicableYN', 1)
                                                  ->whereNotNull('inputVATGlAccountID')
                                                  ->when($forUpdate == false, function($query) {
                                                        $query->select('VATAmountLocal', 'taxableAmountLocal', 'id')
                                                              ->whereNull('returnFilledDetailID');
                                                  })
                                                  ->when($forUpdate == true && $confirmedYN == 0, function($query) use ($returnFilledDetailID){
                                                        $query->where(function($query) use ($returnFilledDetailID) {
                                                                  $query->whereNull('returnFilledDetailID')
                                                                        ->orWhere('returnFilledDetailID', $returnFilledDetailID);
                                                                });
                                                  })
                                                   ->when($forUpdate == true && $confirmedYN == 1, function($query) use ($returnFilledDetailID){
                                                        $query->where(function($query) use ($returnFilledDetailID) {
                                                              $query->where('returnFilledDetailID', $returnFilledDetailID);
                                                            });
                                                  })
                                                  ->where(function($query) {
                                                    $query->where(function($query) {
                                                            $query->whereHas('sub_category', function($query) {
                                                                      $query->whereIn('subCatgeoryType', [1]);
                                                                  })
                                                                  ->where('exempt_vat_portion', '>', 0)
                                                                  ->where('logisticYN', 0);
                                                        });
                                                  });


                $taxLedgerDetailResultTwo = $taxLedgerDetailDataPortion->get();

                $taxLedgerDetailResultTwo = $this->portionateExemptVAT($taxLedgerDetailResultTwo);

                $taxLedgerDetail = collect($taxLedgerDetailResultOne)->merge(collect($taxLedgerDetailResultTwo))->all();
                break;
            case 17:
                $oneA = VatReturnFillingDetail::where('vatReturnFillingSubCatgeoryID', 2)
                                              ->where('vatReturnFillingID', $vatReturnFillingMasterID)
                                              ->selectRaw('SUM(taxAmount) as totalTaxAmount, SUM(taxableAmount) as totalTaxableAmount')
                                              ->first();

                // $onef = 
                $twoB = VatReturnFillingDetail::where('vatReturnFillingSubCatgeoryID', 10)
                                              ->where('vatReturnFillingID', $vatReturnFillingMasterID)
                                              ->selectRaw('SUM(taxAmount) as totalTaxAmount, SUM(taxableAmount) as totalTaxableAmount')
                                              ->first();

                // $fourA = 

                $taxAmount = (($oneA) ? $oneA->totalTaxAmount : 0) + (($twoB) ? $twoB->totalTaxAmount : 0);
                $taxableAmount = (($oneA) ? $oneA->totalTaxableAmount : 0) + (($twoB) ? $twoB->totalTaxableAmount : 0);
                break;
            case 20:
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                                                  ->whereDate('documentDate', '<=', $date)
                                                  ->where('companySystemID', $companySystemID)
                                                  ->whereHas('supplier', function($query) use ($companyCountry){
                                                      $query->where('supplierCountryID', $companyCountry);
                                                  })
                                                  ->where('rcmApplicableYN', 1)
                                                  ->whereNotNull('inputVATGlAccountID')
                                                  ->when($forUpdate == false, function($query) {
                                                        $query->select('VATAmountLocal', 'taxableAmountLocal', 'id')
                                                              ->whereNull('returnFilledDetailID');
                                                  })
                                                  ->when($forUpdate == true && $confirmedYN == 0, function($query) use ($returnFilledDetailID){
                                                        $query->where(function($query) use ($returnFilledDetailID) {
                                                                  $query->whereNull('returnFilledDetailID')
                                                                        ->orWhere('returnFilledDetailID', $returnFilledDetailID);
                                                                });
                                                  })
                                                   ->when($forUpdate == true && $confirmedYN == 1, function($query) use ($returnFilledDetailID){
                                                        $query->where(function($query) use ($returnFilledDetailID) {
                                                              $query->where('returnFilledDetailID', $returnFilledDetailID);
                                                            });
                                                  })
                                                  ->whereHas('supplier_invoice_details', function($query) {
                                                        $query->whereHas('grv_detail', function($query) { 
                                                            $query->where('itemFinanceCategoryID','!=' ,3);
                                                        });
                                                  })
                                                  ->where('documentSystemID', 11)
                                                  ->whereHas('supplier_invoice', function($query) {
                                                        $query->where('documentType', 0);
                                                  });


                  $taxLedgerDetail = $taxLedgerDetailData->get();
                break;
            case 21:
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                                                  ->whereDate('documentDate', '<=', $date)
                                                  ->where('companySystemID', $companySystemID)
                                                  ->whereHas('supplier', function($query) use ($companyCountry){
                                                      $query->where('supplierCountryID', '!=',$companyCountry);
                                                  })
                                                  ->whereNotNull('inputVATGlAccountID')
                                                  ->when($forUpdate == false, function($query) {
                                                        $query->select('VATAmountLocal', 'taxableAmountLocal', 'id')
                                                              ->whereNull('returnFilledDetailID');
                                                  })
                                                  ->when($forUpdate == true && $confirmedYN == 0, function($query) use ($returnFilledDetailID){
                                                        $query->where(function($query) use ($returnFilledDetailID) {
                                                                  $query->whereNull('returnFilledDetailID')
                                                                        ->orWhere('returnFilledDetailID', $returnFilledDetailID);
                                                                });
                                                  })
                                                   ->when($forUpdate == true && $confirmedYN == 1, function($query) use ($returnFilledDetailID){
                                                        $query->where(function($query) use ($returnFilledDetailID) {
                                                              $query->where('returnFilledDetailID', $returnFilledDetailID);
                                                            });
                                                  })
                                                  ->where('addVATonPO', 0)
                                                  ->whereHas('sub_category', function($query) {
                                                        $query->whereHas('type', function($query) { 
                                                            $query->where('id', 1);
                                                        });
                                                  });


                  $taxLedgerDetail = $taxLedgerDetailData->get();
                break;
            case 22:
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                                                  ->whereDate('documentDate', '<=', $date)
                                                  ->where('companySystemID', $companySystemID)
                                                  ->where('rcmApplicableYN', 1)
                                                  ->whereNotNull('inputVATGlAccountID')
                                                  ->when($forUpdate == false, function($query) {
                                                        $query->select('VATAmountLocal', 'taxableAmountLocal', 'id')
                                                              ->whereNull('returnFilledDetailID');
                                                  })
                                                  ->when($forUpdate == true && $confirmedYN == 0, function($query) use ($returnFilledDetailID){
                                                        $query->where(function($query) use ($returnFilledDetailID) {
                                                                  $query->whereNull('returnFilledDetailID')
                                                                        ->orWhere('returnFilledDetailID', $returnFilledDetailID);
                                                                });
                                                  })
                                                   ->when($forUpdate == true && $confirmedYN == 1, function($query) use ($returnFilledDetailID){
                                                        $query->where(function($query) use ($returnFilledDetailID) {
                                                              $query->where('returnFilledDetailID', $returnFilledDetailID);
                                                            });
                                                  })
                                                  ->whereHas('supplier_invoice_details', function($query) {
                                                        $query->whereHas('grv_detail', function($query) { 
                                                            $query->where('itemFinanceCategoryID',3);
                                                        });
                                                  })
                                                  ->where('documentSystemID', 11)
                                                  ->whereHas('supplier_invoice', function($query) {
                                                        $query->where('documentType', 0);
                                                  });


                  $taxLedgerDetail = $taxLedgerDetailData->get();
                break;
            case 25:
                $fiveA = VatReturnFillingDetail::where('vatReturnFillingSubCatgeoryID', 17)
                                              ->where('vatReturnFillingID', $vatReturnFillingMasterID)
                                              ->selectRaw('SUM(taxAmount) as totalTaxAmount, SUM(taxableAmount) as totalTaxableAmount')
                                              ->first();

                $fiveB = VatReturnFillingDetail::where('vatReturnFillingSubCatgeoryID', 18)
                                              ->where('vatReturnFillingID', $vatReturnFillingMasterID)
                                              ->selectRaw('SUM(taxAmount) as totalTaxAmount, SUM(taxableAmount) as totalTaxableAmount')
                                              ->first();

                $taxAmount = (($fiveA) ? $fiveA->totalTaxAmount : 0) - (($fiveB) ? $fiveB->totalTaxAmount : 0);
                $taxableAmount = (($fiveA) ? $fiveA->totalTaxableAmount : 0) - (($fiveB) ? $fiveB->totalTaxableAmount : 0);
                break;
            case 26:
                $sixA = VatReturnFillingDetail::where('vatReturnFillingSubCatgeoryID', 20)
                                              ->where('vatReturnFillingID', $vatReturnFillingMasterID)
                                              ->selectRaw('SUM(taxAmount) as totalTaxAmount, SUM(taxableAmount) as totalTaxableAmount')
                                              ->first();

                $sixB = VatReturnFillingDetail::where('vatReturnFillingSubCatgeoryID', 21)
                                              ->where('vatReturnFillingID', $vatReturnFillingMasterID)
                                              ->selectRaw('SUM(taxAmount) as totalTaxAmount, SUM(taxableAmount) as totalTaxableAmount')
                                              ->first();

                $sixC = VatReturnFillingDetail::where('vatReturnFillingSubCatgeoryID', 22)
                                              ->where('vatReturnFillingID', $vatReturnFillingMasterID)
                                              ->selectRaw('SUM(taxAmount) as totalTaxAmount, SUM(taxableAmount) as totalTaxableAmount')
                                              ->first();

                $sixD = VatReturnFillingDetail::where('vatReturnFillingSubCatgeoryID', 23)
                                              ->where('vatReturnFillingID', $vatReturnFillingMasterID)
                                              ->selectRaw('SUM(taxAmount) as totalTaxAmount, SUM(taxableAmount) as totalTaxableAmount')
                                              ->first();

                $taxAmount = (($sixA) ? $sixA->totalTaxAmount : 0) + (($sixB) ? $sixB->totalTaxAmount : 0) + (($sixC) ? $sixC->totalTaxAmount : 0) - (($sixD) ? $sixD->totalTaxAmount : 0);
                $taxableAmount = (($sixA) ? $sixA->totalTaxableAmount : 0) + (($sixB) ? $sixB->totalTaxableAmount : 0) + (($sixC) ? $sixC->totalTaxableAmount : 0) - (($sixD) ? $sixD->totalTaxableAmount : 0);
                break;
            case 27:
                $sevenA = VatReturnFillingDetail::where('vatReturnFillingSubCatgeoryID', 25)
                                              ->where('vatReturnFillingID', $vatReturnFillingMasterID)
                                              ->selectRaw('SUM(taxAmount) as totalTaxAmount, SUM(taxableAmount) as totalTaxableAmount')
                                              ->first();

                $sevenB = VatReturnFillingDetail::where('vatReturnFillingSubCatgeoryID', 26)
                                              ->where('vatReturnFillingID', $vatReturnFillingMasterID)
                                              ->selectRaw('SUM(taxAmount) as totalTaxAmount, SUM(taxableAmount) as totalTaxableAmount')
                                              ->first();

                $taxAmount = (($sevenA) ? $sevenA->totalTaxAmount : 0) + (($sevenB) ? $sevenB->totalTaxAmount : 0);
                $taxableAmount = (($sevenA) ? $sevenA->totalTaxableAmount : 0) + (($sevenB) ? $sevenB->totalTaxableAmount : 0);
                break;
            default:
                # code...
                break;
        }


        if (count($taxLedgerDetail) > 0) {
            $linkedTaxLedgerDetails = collect($taxLedgerDetail)->pluck('id')->toArray();
            $taxAmount = collect($taxLedgerDetail)->sum('VATAmountLocal');
            $taxableAmount = collect($taxLedgerDetail)->sum('taxableAmountLocal');
        }

        return ['status' => true, 'data' => ['linkedTaxLedgerDetails' => $linkedTaxLedgerDetails, 'taxAmount' => $taxAmount, 'taxableAmount' => $taxableAmount, 'taxLedgerDetailData' => $taxLedgerDetail]];
    }

    public function portionateExemptVAT($ledgerDetail)
    {
        foreach ($ledgerDetail as $key => $value) {
            $value->VATAmount = $value->VATAmount * ($value->exempt_vat_portion/100);
            $value->VATAmountLocal = $value->VATAmountLocal * ($value->exempt_vat_portion/100);
            $value->VATAmountRpt = $value->VATAmountRpt * ($value->exempt_vat_portion/100);
        }

        return $ledgerDetail;
    }

    public function updateFillingFormula($vatReturnFillingID)
    {
        $subCategories = VatReturnFillingCategory::where('isActive', 1)
                                                  ->where('isFormula', 1)
                                                  ->get();

        foreach ($subCategories as $key => $value) {
            $res = $this->generateFilling(null, $value->id, null, false, null, 0, $vatReturnFillingID);

            if ($res['status']) {
                $detailData = [
                    'taxAmount' => $res['data']['taxAmount'],
                    'taxableAmount' => $res['data']['taxableAmount']
                ];

                $detailRes = VatReturnFillingDetail::where('vatReturnFillingSubCatgeoryID', $value->id)
                                                   ->where('vatReturnFillingID', $vatReturnFillingID)
                                                   ->update($detailData);
            }
        }

        return ['status' => true];
    }
}
