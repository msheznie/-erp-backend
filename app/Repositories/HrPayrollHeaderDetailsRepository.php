<?php

namespace App\Repositories;

use App\Models\HrPayrollHeaderDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HrPayrollHeaderDetailsRepository
 * @package App\Repositories
 * @version August 1, 2021, 10:23 am +04
 *
 * @method HrPayrollHeaderDetails findWithoutFail($id, $columns = ['*'])
 * @method HrPayrollHeaderDetails find($id, $columns = ['*'])
 * @method HrPayrollHeaderDetails first($columns = ['*'])
*/
class HrPayrollHeaderDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'payrollMasterID',
        'EmpID',
        'accessGroupID',
        'ECode',
        'Ename1',
        'Ename2',
        'Ename3',
        'Ename4',
        'EmpShortCode',
        'secondaryCode',
        'Designation',
        'Gender',
        'Tel',
        'Mobile',
        'DOJ',
        'payCurrencyID',
        'payCurrency',
        'nationality',
        'totDayAbsent',
        'totDayPresent',
        'totOTHours',
        'civilOrPassport',
        'salaryArrearsDays',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionER',
        'transactionCurrencyDecimalPlaces',
        'transactionAmount',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalER',
        'companyLocalCurrencyDecimalPlaces',
        'companyLocalAmount',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingER',
        'companyReportingCurrencyDecimalPlaces',
        'companyReportingAmount',
        'segmentID',
        'segmentCode',
        'payComment',
        'companyID',
        'companyCode'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HrPayrollHeaderDetails::class;
    }
}
