<?php

namespace App\Repositories;

use App\Models\POSSOURCECustomerMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSSOURCECustomerMasterRepository
 * @package App\Repositories
 * @version August 8, 2022, 2:56 pm +04
 *
 * @method POSSOURCECustomerMaster findWithoutFail($id, $columns = ['*'])
 * @method POSSOURCECustomerMaster find($id, $columns = ['*'])
 * @method POSSOURCECustomerMaster first($columns = ['*'])
*/
class POSSOURCECustomerMasterRepository extends BaseRepository
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
        return POSSOURCECustomerMaster::class;
    }
}
