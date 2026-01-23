<?php

namespace App\Repositories;

use App\Models\SalaryProcessMaster;
use App\Repositories\BaseRepository;

/**
 * Class SalaryProcessMasterRepository
 * @package App\Repositories
 * @version November 7, 2018, 10:03 am UTC
 *
 * @method SalaryProcessMaster findWithoutFail($id, $columns = ['*'])
 * @method SalaryProcessMaster find($id, $columns = ['*'])
 * @method SalaryProcessMaster first($columns = ['*'])
*/
class SalaryProcessMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'CompanyID',
        'salaryProcessCode',
        'documentID',
        'serialNo',
        'processPeriod',
        'startDate',
        'endDate',
        'Currency',
        'salaryMonth',
        'description',
        'createDate',
        'RollLevForApp_curr',
        'isReferredBack',
        'confirmedYN',
        'confirmedby',
        'approvedYN',
        'approvedby',
        'approvedDate',
        'confirmedDate',
        'isRGLConfirm',
        'localCurrencyID',
        'localCurrencyExchangeRate',
        'rptCurrencyID',
        'rptCurrencyExchangeRate',
        'updateNoOfDaysBtnFlag',
        'updateSalaryBtnFlag',
        'getEmployeeBtnFlag',
        'updateSSOBtnFlag',
        'updateRABenefitBtnFlag',
        'updateTaxStep1BtnFlag',
        'updateTaxStep2BtnFlag',
        'updateTaxStep3BtnFlag',
        'updateTaxStep4BtnFlag',
        'updateHeldSalaryBtnFlag',
        'isHeldSalary',
        'showpaySlip',
        'paymentGenerated',
        'PayMasterAutoId',
        'bankIDForPayment',
        'bankAccountIDForPayment',
        'salaryProcessType',
        'thirteenMonthJVID',
        'gratuityJVID',
        'gratuityReversalJVID',
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
        return SalaryProcessMaster::class;
    }
}
