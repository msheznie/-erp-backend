<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceTracking;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomerInvoiceTrackingRepository
 * @package App\Repositories
 * @version February 9, 2020, 3:11 pm +04
 *
 * @method CustomerInvoiceTracking findWithoutFail($id, $columns = ['*'])
 * @method CustomerInvoiceTracking find($id, $columns = ['*'])
 * @method CustomerInvoiceTracking first($columns = ['*'])
*/
class CustomerInvoiceTrackingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentID',
        'companyID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'customerInvoiceTrackingCode',
        'manualTrackingNo',
        'customerID',
        'contractNumber',
        'serviceLineCode',
        'comments',
        'approvalType',
        'submittedYN',
        'submittedEmpID',
        'submittedEmpName',
        'submittedDate',
        'submittedYear',
        'closeYN',
        'closedByEmpID',
        'closedByEmpName',
        'closedDate',
        'totalBatchAmount',
        'totalApprovedAmount',
        'totalRejectedAmount',
        'createdUserID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerInvoiceTracking::class;
    }
}
