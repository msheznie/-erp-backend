<?php

namespace App\Repositories;

use App\Models\POSSTAGShiftDetails;
use App\Repositories\BaseRepository;

/**
 * Class POSSTAGShiftDetailsRepository
 * @package App\Repositories
 * @version August 8, 2022, 8:22 am +04
 *
 * @method POSSTAGShiftDetails findWithoutFail($id, $columns = ['*'])
 * @method POSSTAGShiftDetails find($id, $columns = ['*'])
 * @method POSSTAGShiftDetails first($columns = ['*'])
*/
class POSSTAGShiftDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'cashSales',
        'cashSales_local',
        'cashSales_reporting',
        'closingCashBalance_local',
        'closingCashBalance_reporting',
        'closingCashBalance_transaction',
        'companyCode',
        'companyID',
        'companyLocalCurrency',
        'companyLocalCurrencyDecimalPlaces',
        'companyLocalCurrencyID',
        'companyLocalExchangeRate',
        'companyReportingCurrency',
        'companyReportingCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingExchangeRate',
        'counterID',
        'createdDateTime',
        'createdPCID',
        'createdUserGroup',
        'createdUserID',
        'createdUserName',
        'different_local',
        'different_local_reporting',
        'different_transaction',
        'empID',
        'endingBalance_local',
        'endingBalance_reporting',
        'endingBalance_transaction',
        'endTime',
        'giftCardTopUp',
        'giftCardTopUp_local',
        'giftCardTopUp_reporting',
        'id_store',
        'is_sync',
        'isClosed',
        'modifiedDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'startingBalance_local',
        'startingBalance_reporting',
        'startingBalance_transaction',
        'startTime',
        'timestamp',
        'transaction_log_id',
        'transactionCurrency',
        'transactionCurrencyDecimalPlaces',
        'transactionCurrencyID',
        'transactionExchangeRate',
        'wareHouseID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSSTAGShiftDetails::class;
    }
}
