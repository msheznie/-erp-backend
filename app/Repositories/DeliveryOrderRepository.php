<?php

namespace App\Repositories;

use App\Models\DeliveryOrder;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DeliveryOrderRepository
 * @package App\Repositories
 * @version May 8, 2020, 2:34 pm +04
 *
 * @method DeliveryOrder findWithoutFail($id, $columns = ['*'])
 * @method DeliveryOrder find($id, $columns = ['*'])
 * @method DeliveryOrder first($columns = ['*'])
*/
class DeliveryOrderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'orderType',
        'deliveryOrderCode',
        'companySystemId',
        'documentSystemId',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'deliveryOrderDate',
        'wareHouseSystemCode',
        'serviceLineSystemID',
        'referenceNo',
        'customerID',
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
        'closedYN',
        'closedDate',
        'closedReason',
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
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DeliveryOrder::class;
    }
}
