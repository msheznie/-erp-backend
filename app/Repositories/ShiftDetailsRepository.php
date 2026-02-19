<?php

namespace App\Repositories;

use App\Models\ShiftDetails;
use App\Repositories\BaseRepository;

/**
 * Class ShiftDetailsRepository
 * @package App\Repositories
 * @version January 14, 2019, 12:57 pm +04
 *
 * @method ShiftDetails findWithoutFail($id, $columns = ['*'])
 * @method ShiftDetails find($id, $columns = ['*'])
 * @method ShiftDetails first($columns = ['*'])
*/
class ShiftDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'wareHouseID',
        'empID',
        'counterID',
        'startTime',
        'endTime',
        'isClosed',
        'cashSales',
        'giftCardTopUp',
        'startingBalance_transaction',
        'endingBalance_transaction',
        'different_transaction',
        'cashSales_local',
        'giftCardTopUp_local',
        'startingBalance_local',
        'endingBalance_local',
        'different_local',
        'cashSales_reporting',
        'giftCardTopUp_reporting',
        'closingCashBalance_transaction',
        'closingCashBalance_local',
        'startingBalance_reporting',
        'endingBalance_reporting',
        'different_local_reporting',
        'closingCashBalance_reporting',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionExchangeRate',
        'transactionCurrencyDecimalPlaces',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalExchangeRate',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingExchangeRate',
        'companyReportingCurrencyDecimalPlaces',
        'companyID',
        'companyCode',
        'segmentID',
        'segmentCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp',
        'id_store',
        'is_sync'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ShiftDetails::class;
    }
}
