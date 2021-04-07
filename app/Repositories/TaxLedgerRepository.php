<?php

namespace App\Repositories;

use App\Models\TaxLedger;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TaxLedgerRepository
 * @package App\Repositories
 * @version April 5, 2021, 8:59 am +04
 *
 * @method TaxLedger findWithoutFail($id, $columns = ['*'])
 * @method TaxLedger find($id, $columns = ['*'])
 * @method TaxLedger first($columns = ['*'])
*/
class TaxLedgerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentMasterAutoID',
        'documentCode',
        'documentDate',
        'subCategoryID',
        'masterCategoryID',
        'rcmApplicableYN',
        'localAmount',
        'rptAmount',
        'transAmount',
        'transER',
        'localER',
        'comRptER',
        'localCurrencyID',
        'rptCurrencyID',
        'transCurrencyID',
        'isClaimable',
        'isClaimed',
        'taxAuthorityAutoID',
        'inputVATGlAccountID',
        'inputVatTransferAccountID',
        'outputVatTransferGLAccountID',
        'outputVatGLAccountID',
        'companySystemID',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TaxLedger::class;
    }
}
