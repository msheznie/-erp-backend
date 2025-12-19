<?php

namespace App\Repositories;

use App\Models\IOUBookingMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class IOUBookingMasterRepository
 * @package App\Repositories
 * @version August 15, 2022, 12:49 pm +04
 *
 * @method IOUBookingMaster findWithoutFail($id, $columns = ['*'])
 * @method IOUBookingMaster find($id, $columns = ['*'])
 * @method IOUBookingMaster first($columns = ['*'])
*/
class IOUBookingMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentID',
        'serialNo',
        'iouVoucherAutoID',
        'bookingCode',
        'bookingDate',
        'pullFromFuelYN',
        'empID',
        'empName',
        'userType',
        'comments',
        'submittedYN',
        'submittedDate',
        'submittedEmpID',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedByEmpID',
        'approvedByEmpName',
        'approvedDate',
        'approvalComments',
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
        'empCurrencyID',
        'empCurrency',
        'empCurrencyExchangeRate',
        'empCurrencyAmount',
        'empCurrencyDecimalPlaces',
        'isDeleted',
        'deletedEmpID',
        'deletedDate',
        'currentLevelNo',
        'companyFinanceYearID',
        'companyFinanceYear',
        'FYBegin',
        'FYEnd',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'companyFinancePeriodID',
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
        return IOUBookingMaster::class;
    }
}
