<?php

namespace App\Repositories;

use App\Models\SalaryProcessDetail;
use App\Repositories\BaseRepository;

/**
 * Class SalaryProcessDetailRepository
 * @package App\Repositories
 * @version August 28, 2019, 4:28 pm +04
 *
 * @method SalaryProcessDetail findWithoutFail($id, $columns = ['*'])
 * @method SalaryProcessDetail find($id, $columns = ['*'])
 * @method SalaryProcessDetail first($columns = ['*'])
*/
class SalaryProcessDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'salaryProcessMasterID',
        'CompanyID',
        'location',
        'designationID',
        'departmentID',
        'schedulemasterID',
        'empGrade',
        'empGroup',
        'processPeriod',
        'startDate',
        'endDate',
        'empID',
        'currency',
        'noOfDays',
        'bankMasterID',
        'bankName',
        'SwiftCode',
        'accountNo',
        'fixedPayments',
        'fixedPaymentAdjustments',
        'radioactiveBenifits',
        'OverTime',
        'extraDayPay',
        'noPay',
        'jobBonus',
        'desertAllowance',
        'monthlyAddition',
        'MA_IsSSO',
        'monthlyDedcution',
        'balancePayments',
        'loanDeductions',
        'mobileCharges',
        'passiEmployee',
        'passiEmployer',
        'pasiEmployerUE',
        'splitSalary',
        'taxAmount',
        'expenseClaimAmount',
        'netSalary',
        'grossSalary',
        'localCurrencyID',
        'localCurrencyER',
        'localAmount',
        'rptCurrencyID',
        'rptCurrencyER',
        'rptAmount',
        'isRA',
        'isHold',
        'isSettled',
        'holdSalary',
        'heldSalaryPay',
        'finalsettlementmasterID',
        'modifieduser',
        'modifiedpc',
        'createduserGroup',
        'createdpc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SalaryProcessDetail::class;
    }
}
