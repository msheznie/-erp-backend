<?php

namespace App\Repositories;

use App\Models\SalesReturnRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class SalesReturnRefferedBackRepository
 * @package App\Repositories
 * @version December 24, 2020, 2:05 pm +04
 *
 * @method SalesReturnRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method SalesReturnRefferedBack find($id, $columns = ['*'])
 * @method SalesReturnRefferedBack first($columns = ['*'])
*/
class SalesReturnRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'salesReturnID',
        'returnType',
        'salesReturnCode',
        'serialNo',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'salesReturnDate',
        'wareHouseSystemCode',
        'serviceLineSystemID',
        'serviceLineCode',
        'referenceNo',
        'customerID',
        'custGLAccountSystemID',
        'custGLAccountCode',
        'custUnbilledAccountSystemID',
        'custUnbilledAccountCode',
        'salesPersonID',
        'narration',
        'notes',
        'contactPersonNumber',
        'contactPersonName',
        'transactionCurrencyID',
        'transactionCurrencyER',
        'transactionAmount',
        'companyLocalCurrencyID',
        'companyLocalCurrencyER',
        'companyLocalAmount',
        'companyReportingCurrencyID',
        'companyReportingCurrencyER',
        'companyReportingAmount',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedDate',
        'approvedEmpSystemID',
        'approvedbyEmpID',
        'approvedbyEmpName',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'createdUserSystemID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedUserSystemID',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'postedDate',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SalesReturnRefferedBack::class;
    }
}
