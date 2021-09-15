<?php

namespace App\Repositories;

use App\Models\VatReturnFillingMaster;
use App\Models\TaxLedgerDetail;
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

    public function generateFilling($date, $categoryID, $companySystemID, $forUpdate = false, $returnFilledDetailID = null, $confirmedYN = 0)
    {
        $linkedTaxLedgerDetails = [];
        $taxableAmount = 0;
        $taxAmount = 0;
        $taxLedgerDetail = [];
        $taxLedgerDetailData = [];
        switch ($categoryID) {
            case 2:
                $taxLedgerDetailData = TaxLedgerDetail::with(['supplier','customer','document_master', 'sub_category'])
                                                  ->whereDate('documentDate', '<=', $date)
                                                  ->where('companySystemID', $companySystemID)
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

        return ['status' => true, 'data' => ['linkedTaxLedgerDetails' => $linkedTaxLedgerDetails, 'taxAmount' => $taxAmount, 'taxableAmount' => $taxableAmount, 'taxLedgerDetailData' => $taxLedgerDetailData]];
    }
}
