<?php

namespace App\Repositories;

use App\Models\DirectPaymentReferback;
use App\Repositories\BaseRepository;

/**
 * Class DirectPaymentReferbackRepository
 * @package App\Repositories
 * @version November 21, 2018, 5:32 am UTC
 *
 * @method DirectPaymentReferback findWithoutFail($id, $columns = ['*'])
 * @method DirectPaymentReferback find($id, $columns = ['*'])
 * @method DirectPaymentReferback first($columns = ['*'])
*/
class DirectPaymentReferbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'directPaymentDetailsID',
        'directPaymentAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'supplierID',
        'expenseClaimMasterAutoID',
        'chartOfAccountSystemID',
        'glCode',
        'glCodeDes',
        'glCodeIsBank',
        'comments',
        'supplierTransCurrencyID',
        'supplierTransER',
        'DPAmountCurrency',
        'DPAmountCurrencyER',
        'DPAmount',
        'bankAmount',
        'bankCurrencyID',
        'bankCurrencyER',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'budgetYear',
        'timesReferred',
        'relatedPartyYN',
        'pettyCashYN',
        'glCompanySystemID',
        'glCompanyID',
        'toBankID',
        'toBankAccountID',
        'toBankCurrencyID',
        'toBankCurrencyER',
        'toBankAmount',
        'toBankGlCodeSystemID',
        'toBankGlCode',
        'toBankGLDescription',
        'toCompanyLocalCurrencyID',
        'toCompanyLocalCurrencyER',
        'toCompanyLocalCurrencyAmount',
        'toCompanyRptCurrencyID',
        'toCompanyRptCurrencyER',
        'toCompanyRptCurrencyAmount',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DirectPaymentReferback::class;
    }
}
