<?php

namespace App\Repositories;

use App\Models\HrPayrollDetails;
use App\Repositories\BaseRepository;

/**
 * Class HrPayrollDetailsRepository
 * @package App\Repositories
 * @version August 1, 2021, 10:23 am +04
 *
 * @method HrPayrollDetails findWithoutFail($id, $columns = ['*'])
 * @method HrPayrollDetails find($id, $columns = ['*'])
 * @method HrPayrollDetails first($columns = ['*'])
*/
class HrPayrollDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'payrollMasterID',
        'empID',
        'detailTBID',
        'fromTB',
        'calculationTB',
        'detailType',
        'salCatID',
        'percentage',
        'GLCode',
        'liabilityGL',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionER',
        'transactionCurrencyDecimalPlaces',
        'transactionAmount',
        'pasiActualAmount',
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
        return HrPayrollDetails::class;
    }
}
