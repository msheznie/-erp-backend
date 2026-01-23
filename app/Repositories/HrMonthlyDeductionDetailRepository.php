<?php

namespace App\Repositories;

use App\Models\HrMonthlyDeductionDetail;
use App\Repositories\BaseRepository;

/**
 * Class HrMonthlyDeductionDetailRepository
 * @package App\Repositories
 * @version August 1, 2021, 3:48 pm +04
 *
 * @method HrMonthlyDeductionDetail findWithoutFail($id, $columns = ['*'])
 * @method HrMonthlyDeductionDetail find($id, $columns = ['*'])
 * @method HrMonthlyDeductionDetail first($columns = ['*'])
*/
class HrMonthlyDeductionDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'monthlyDeductionMasterID',
        'empID',
        'accessGroupID',
        'description',
        'declarationID',
        'GLCode',
        'categoryID',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionExchangeRate',
        'transactionCurrencyDecimalPlaces',
        'transactionAmount',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalExchangeRate',
        'companyLocalAmount',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingExchangeRate',
        'companyReportingAmount',
        'companyReportingCurrencyDecimalPlaces',
        'IsSSO',
        'IsTax',
        'companyID',
        'companyCode',
        'segmentID',
        'segmentCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HrMonthlyDeductionDetail::class;
    }
}
