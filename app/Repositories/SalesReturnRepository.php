<?php

namespace App\Repositories;

use App\Models\SalesReturn;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SalesReturnRepository
 * @package App\Repositories
 * @version December 21, 2020, 4:03 pm +04
 *
 * @method SalesReturn findWithoutFail($id, $columns = ['*'])
 * @method SalesReturn find($id, $columns = ['*'])
 * @method SalesReturn first($columns = ['*'])
*/
class SalesReturnRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        return SalesReturn::class;
    }
}
