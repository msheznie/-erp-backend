<?php

namespace App\Repositories;

use App\Models\DirectPaymentDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DirectPaymentDetailsRepository
 * @package App\Repositories
 * @version August 9, 2018, 9:59 am UTC
 *
 * @method DirectPaymentDetails findWithoutFail($id, $columns = ['*'])
 * @method DirectPaymentDetails find($id, $columns = ['*'])
 * @method DirectPaymentDetails first($columns = ['*'])
*/
class DirectPaymentDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'directPaymentAutoID',
        'companyID',
        'serviceLineCode',
        'supplierID',
        'expenseClaimMasterAutoID',
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
        return DirectPaymentDetails::class;
    }
}
