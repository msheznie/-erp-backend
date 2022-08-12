<?php

namespace App\Repositories;

use App\Models\POSSTAGCustomerMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSSTAGCustomerMasterRepository
 * @package App\Repositories
 * @version August 8, 2022, 2:55 pm +04
 *
 * @method POSSTAGCustomerMaster findWithoutFail($id, $columns = ['*'])
 * @method POSSTAGCustomerMaster find($id, $columns = ['*'])
 * @method POSSTAGCustomerMaster first($columns = ['*'])
*/
class POSSTAGCustomerMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'capAmount',
        'companyCode',
        'companyID',
        'createdDateTime',
        'createdPCID',
        'createdUserGroup',
        'createdUserID',
        'createdUserName',
        'customerAddress1',
        'customerAddress2',
        'customerCountry',
        'customerCountryID',
        'customerCreditLimit',
        'customerCreditPeriod',
        'customerCurrency',
        'customerCurrencyDecimalPlaces',
        'customerCurrencyID',
        'customerEmail',
        'customerFax',
        'customerName',
        'customerSystemCode',
        'customerTelephone',
        'customerUrl',
        'deleteByEmpID',
        'deletedDatetime',
        'deletedYN',
        'erp_customer_master_id',
        'IdCardNumber',
        'isActive',
        'isSync',
        'levelNo',
        'locationID',
        'masterID',
        'modifiedDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'partyCategoryID',
        'secondaryCode',
        'taxGroupID',
        'timestamp',
        'transaction_log_id',
        'vatEligible',
        'vatIdNo',
        'vatNumber',
        'vatPercentage'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSSTAGCustomerMaster::class;
    }
}
