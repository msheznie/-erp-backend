<?php

namespace App\Repositories;

use App\Models\TaxLedgerDetail;
use App\Repositories\BaseRepository;

/**
 * Class TaxLedgerDetailRepository
 * @package App\Repositories
 * @version July 6, 2021, 9:34 am +04
 *
 * @method TaxLedgerDetail findWithoutFail($id, $columns = ['*'])
 * @method TaxLedgerDetail find($id, $columns = ['*'])
 * @method TaxLedgerDetail first($columns = ['*'])
*/
class TaxLedgerDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentMasterAutoID',
        'documentDetailID',
        'taxLedgerID',
        'vatSubCategoryID',
        'vatMasterCategoryID',
        'serviceLineSystemID',
        'documentDate',
        'postedDate',
        'documentNumber',
        'chartOfAccountSystemID',
        'accountCode',
        'accountDescription',
        'transactionCurrencyID',
        'originalInvoice',
        'originalInvoiceDate',
        'dateOfSupply',
        'partyType',
        'partyAutoID',
        'partyVATRegisteredYN',
        'partyVATRegNo',
        'countryID',
        'itemSystemCode',
        'itemCode',
        'itemDescription',
        'VATPercentage',
        'taxableAmount',
        'VATAmount',
        'localER',
        'localAmount',
        'reportingER',
        'reportingAmount',
        'taxableAmountLocal',
        'taxableAmountReporting',
        'VATAmountLocal',
        'VATAmountRpt',
        'inputVATGlAccountID',
        'inputVatTransferAccountID',
        'outputVatTransferGLAccountID',
        'outputVatGLAccountID',
        'companySystemID',
        'createdPCID',
        'createdUserSystemID',
        'createdDateTime'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TaxLedgerDetail::class;
    }
}
