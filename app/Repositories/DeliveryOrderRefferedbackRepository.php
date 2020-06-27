<?php

namespace App\Repositories;

use App\Models\DeliveryOrderRefferedback;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DeliveryOrderRefferedbackRepository
 * @package App\Repositories
 * @version June 24, 2020, 8:13 am +04
 *
 * @method DeliveryOrderRefferedback findWithoutFail($id, $columns = ['*'])
 * @method DeliveryOrderRefferedback find($id, $columns = ['*'])
 * @method DeliveryOrderRefferedback first($columns = ['*'])
*/
class DeliveryOrderRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'deliveryOrderID',
        'orderType',
        'deliveryOrderCode',
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
        'deliveryOrderDate',
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
        'invoiceStatus',
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
        'selectedForCustomerInvoice',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DeliveryOrderRefferedback::class;
    }
}
