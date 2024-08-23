<?php

namespace App\Repositories;

use App\Models\VatReturnFillingMaster;
use App\Models\TaxLedgerDetail;
use App\Models\VatReturnFillingDetail;
use App\Models\VatReturnFillingCategory;
use App\Models\Company;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Support\Facades\DB;

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
   public function generateFilling($date, $categoryID, $companySystemID, $forUpdate = false, $returnFilledDetailID = null, $confirmedYN = 0, $vatReturnFillingMasterID = null,$isCollection = true)
    {
        $linkedTaxLedgerDetails = [];
        $taxableAmount = 0;
        $taxAmount = 0;
        $taxLedgerDetail = [];
        $taxLedgerDetailData = [];
        $company = Company::where('companySystemID', $companySystemID)->first();
        $companyCountry = isset($company->companyCountry) ? $company->companyCountry : null;
        switch ($categoryID) {
            case 2://Supplies of goods/ services taxed @5% - 1 (a)
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                    ->whereDate('documentDate', '<=', $date)
                    ->where('companySystemID', $companySystemID)
                    ->whereHas('customer', function($query) use ($companyCountry){
                        $query->where('customerCountry', 1); // oman based customers
                    })
                    ->whereIn('documentSystemID',[20,87,19])
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
                    })
                    ->orWhereHas('creditNode', function ($query) {
                        $query->where('isVATApplicable', true);
                    })
                    ->select('*')
                    ->addSelect(DB::raw('CASE WHEN documentSystemID = 19 THEN ROUND((-1) * VATAmountLocal,3) ELSE ROUND(VATAmountLocal,3) END AS VATAmountLocal'))
                    ->addSelect(DB::raw('CASE WHEN documentSystemID = 19 THEN ROUND((-1) * taxableAmountLocal,3) ELSE ROUND(taxableAmountLocal,3) END AS taxableAmountLocal'));


                $taxLedgerDetail = ($isCollection) ? $taxLedgerDetailData->get() : $taxLedgerDetailData;

                break;
            case 3://Supplies of goods/ services taxed @0% - 1 (b)
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                    ->whereDate('documentDate', '<=', $date)
                    ->where('companySystemID', $companySystemID)
                    ->whereIn('documentSystemID',[20,87])
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


                $taxLedgerDetail = ($isCollection) ? $taxLedgerDetailData->get() : $taxLedgerDetailData;
                break;
            case 4: // Supplies of goods/ services tax exempt - 1 (c)
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                    ->whereDate('documentDate', '<=', $date)
                    ->where('companySystemID', $companySystemID)
                    ->whereIn('documentSystemID',[20,87])
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


                $taxLedgerDetail = ($isCollection) ? $taxLedgerDetailData->get() : $taxLedgerDetailData;
                break;
            case 9 : // Purchases from the GCC subject to Reverse Charge Mechanism - 2 (a)
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
                    ->whereIn('documentSystemID', [4])
                    ->orWhereHas('payment_voucher', function ($query) {
                        $query->whereIn('invoiceType',[3,5])->where('rcmActivated',1);
                    })
                    ->whereHas('supplier', function($query) use ($companyCountry){
                        $query->subjectToGCC();
                    });


                $taxLedgerDetail = ($isCollection) ? $taxLedgerDetailData->get() : $taxLedgerDetailData;
                break;
            case 10: // Purchases from outside of GCC subject to Reverse Charge Mechanism - 2 (b)
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
                            $query->where('itemFinanceCategoryID', 2);
                        });
                    })
                    ->whereIn('documentSystemID', [11,4])
                    ->whereHas('supplier_invoice', function($query) {
                        $query->where('documentType', 0);
                    })
                    ->orWhereHas('payment_voucher', function ($query) {
                        $query->whereIn('invoiceType',[3,5])->where('rcmActivated',1);
                    })
                    ->whereHas('supplier', function($query) use ($companyCountry){
                        $query->outsideOfGCC();
                    });


                $taxLedgerDetail = ($isCollection) ? $taxLedgerDetailData->get() : $taxLedgerDetailData;
                break;
            case 12: // Exports - 3 (a)
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                    ->whereDate('documentDate', '<=', $date)
                    ->where('companySystemID', $companySystemID)
                    ->whereNotNull('outputVatGLAccountID')
                    ->when($forUpdate == false, function($query) {
                        $query->select('VATAmountLocal', 'taxableAmountLocal', 'id')
                            ->whereNull('returnFilledDetailID');
                    })
                    ->where(function($query) {
                        $query->where(function($query) {
                            $query->whereHas('customer_invoice', function($query) {
                                $query->whereIn('isPerforma', [0,2,3,4,5]);
                            })
                                ->where('documentSystemID', 20)
                                ->orWhereHas('customer_invoice_details', function($query) {
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
                            $query->whereIn('id', [1,2]);
                        });
                    })->whereHas('customer', function($query) use ($companyCountry){
                        $query->where('customerCountry', '!=',$companyCountry);
                    });


                $taxLedgerDetail = ($isCollection) ? $taxLedgerDetailData->get() : $taxLedgerDetailData;
                break;
            case 15: // Total goods imported - 4 (b)
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
                                ->whereHas('supplier_invoice_details', function($query) {
                                    $query->whereHas('grv_detail', function($query) {
                                        $query->where('itemFinanceCategoryID',1);
                                    });
                                })
                                ->where('documentSystemID', 11)
                                ->whereHas('supplier_invoice', function($query) {
                                    $query->where('documentType', 0);
                                })
                                ->where('logisticYN', 0);
                        })->orWhere(function($query) {
                            $query->where('addVATonPO', 1)
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
                                ->whereHas('supplier_invoice_details', function($query) {
                                    $query->whereHas('grv_detail', function($query) {
                                        $query->where('itemFinanceCategoryID',1);
                                    });
                                })
                                ->where('documentSystemID', 11)
                                ->whereHas('supplier_invoice', function($query) {
                                    $query->where('documentType', 0);
                                })
                                ->where('exempt_vat_portion', '>', 0)
                                ->where('logisticYN', 0);
                        });
                    });


                $taxLedgerDetailResultTwo = $taxLedgerDetailDataPortion->get();

                $taxLedgerDetailResultTwo = $this->portionateExemptVAT($taxLedgerDetailResultTwo);

                $taxLedgerDetail = collect($taxLedgerDetailResultOne)->merge(collect($taxLedgerDetailResultTwo))->all();
                break;
            case 17: // Total VAT due under 1(a) + 1(f) + 2(b) + 4(a)
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
            case 20: // Purchases (except import of goods) - 6 (a)
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category','supplier_invoice' => function($q) {
                                                        $q->with(['employee']);
                                                  }, 'payment_voucher'])
                                                  ->whereDate('documentDate', '<=', $date)
                                                  ->where('companySystemID', $companySystemID)
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
                                                  ->whereIn('documentSystemID', [11,4,15])
                                                    ->where(function ($query) {
                                                        $query->whereHas('supplier_invoice', function($query) {
                                                            $query->whereIn('documentType', [0,1,2,3,4]);
                                                        })->orWhereHas('payment_voucher', function($query) {
                                                            $query->whereIn('invoiceType', [3,5]);
                                                        })->orWhereHas('debitNode', function ($query) {
                                                            $query->where('isVATApplicable', true);
                                                        });
                                                  })
                                                  ->when(('supplier_invoice.documentType' == 1 || 'supplier_invoice.documentType' == 3),function($query) {
                                                        $query->whereHas('supplier_invoice_details', function($query) {
                                                            $query->whereHas('grv_detail', function($query) {
                                                                $query->where('itemFinanceCategoryID','!=',3);
                                                            });
                                                        });
                                                  })
                                                    ->whereHas('supplier', function($query) use ($companyCountry){
                                                        $query->where('supplierCountryID', 1);
                                                    })
                                                 ->select('*')
                                                 ->addSelect(DB::raw('CASE WHEN documentSystemID = 15 THEN ROUND((-1) * VATAmountLocal,3) ELSE ROUND(VATAmountLocal,3) END AS VATAmountLocal'))
                                                 ->addSelect(DB::raw('CASE WHEN documentSystemID = 15 THEN ROUND((-1) * taxableAmountLocal,3) ELSE ROUND(taxableAmountLocal,3) END AS taxableAmountLocal'));




                $taxLedgerDetail = ($isCollection) ? $taxLedgerDetailData->get() : $taxLedgerDetailData;
                break;
            case 21: // Import of goods - 6 (b)
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category','supplier_invoice'])
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
                                                  })
                                                  ->where('documentSystemID', 11)
                                                  ->whereHas('supplier_invoice', function($query) {
                                                        $query->whereIn('documentType', [0,1,2,3,4]);
                                                  })
                                                 ->orWhereHas('payment_voucher',function($query) use($companyCountry) {
                                                     $query->whereIn('invoiceType',[3,5])->where('rcmActivated',0);
                                                 })
                                                ->when(('supplier_invoice.documentType' == 1 || 'supplier_invoice.documentType' == 3),function($query) {
                                                    $query->whereHas('supplier_invoice_details', function($query) {
                                                        $query->whereHas('grv_detail', function($query) {
                                                            $query->where('itemFinanceCategoryID','!=',3);
                                                        });
                                                    });
                                                });


                $taxLedgerDetail = ($isCollection) ? $taxLedgerDetailData->get() : $taxLedgerDetailData;

                break;
            case 22: // VAT on acquisition of fixed assets - 6 (c)
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category','supplier_invoice'])
                    ->whereDate('documentDate', '<=', $date)
                    ->where('companySystemID', $companySystemID)
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


                $taxLedgerDetail = ($isCollection) ? $taxLedgerDetailData->get() : $taxLedgerDetailData;
                break;
            case 25: // Total VAT due [5(a) - 5(b)]
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
            case 26: // Total input VAT credit [6(a) + 6(b) + 6(c) - 6(d)]
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
            case 27: // Total [7(a) - 7(b)]
                $sevenA = VatReturnFillingDetail::where('vatReturnFillingSubCatgeoryID', 25)
                    ->where('vatReturnFillingID', $vatReturnFillingMasterID)
                    ->selectRaw('SUM(taxAmount) as totalTaxAmount, SUM(taxableAmount) as totalTaxableAmount')
                    ->first();

                $sevenB = VatReturnFillingDetail::where('vatReturnFillingSubCatgeoryID', 26)
                    ->where('vatReturnFillingID', $vatReturnFillingMasterID)
                    ->selectRaw('SUM(taxAmount) as totalTaxAmount, SUM(taxableAmount) as totalTaxableAmount')
                    ->first();

                $taxAmount = (($sevenA) ? $sevenA->totalTaxAmount : 0) - (($sevenB) ? $sevenB->totalTaxAmount : 0);
                $taxableAmount = (($sevenA) ? $sevenA->totalTaxableAmount : 0) - (($sevenB) ? $sevenB->totalTaxableAmount : 0);
                break;
            default:
                # code...
                break;
        }


        if($isCollection) {
            if (count($taxLedgerDetail) > 0) {
                $linkedTaxLedgerDetails = collect($taxLedgerDetail)->pluck('id')->toArray();
                $taxAmount = collect($taxLedgerDetail)->sum('VATAmountLocal');
                $taxableAmount = collect($taxLedgerDetail)->sum('taxableAmountLocal');
            }

            return ['status' => true, 'data' => ['linkedTaxLedgerDetails' => $linkedTaxLedgerDetails, 'taxAmount' => $taxAmount, 'taxableAmount' => $taxableAmount, 'taxLedgerDetailData' => $taxLedgerDetail]];

        }else {
            return $taxLedgerDetail;
        }
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
            $res = $this->generateFilling(null, $value->id, null, false, null, 0, $vatReturnFillingID,true);

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

    /*
     * Start update vat return filling total amounts after document pull for vat return filling & amend document
     */
    public function updateVatReturnFillingDetails($returnFilledDetailID){
        $taxDetail = TaxLedgerDetail::where('returnFilledDetailID', $returnFilledDetailID)->get();

        $taxAmount = collect($taxDetail)->whereNotIn('documentSystemID',[19,15])->sum('VATAmountLocal') - collect($taxDetail)->whereIn('documentSystemID',[19,15])->sum('VATAmountLocal');
        $taxableAmount = collect($taxDetail)->whereNotIn('documentSystemID',[19,15])->sum('taxableAmountLocal') - collect($taxDetail)->whereIn('documentSystemID',[19,15])->sum('taxableAmountLocal');

        $fillingDetail = VatReturnFillingDetail::find($returnFilledDetailID);

        $fillingDetail->update(['taxableAmount' => $taxableAmount, 'taxAmount' => $taxAmount]);

        $this->updateFillingFormula($fillingDetail->vatReturnFillingID);
    }
}
