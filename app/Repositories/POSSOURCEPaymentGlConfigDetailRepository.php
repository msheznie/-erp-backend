<?php

namespace App\Repositories;

use App\Models\POSSOURCEPaymentGlConfigDetail;
use App\Repositories\BaseRepository;

/**
 * Class POSSOURCEPaymentGlConfigDetailRepository
 * @package App\Repositories
 * @version August 10, 2022, 1:39 pm +04
 *
 * @method POSSOURCEPaymentGlConfigDetail findWithoutFail($id, $columns = ['*'])
 * @method POSSOURCEPaymentGlConfigDetail find($id, $columns = ['*'])
 * @method POSSOURCEPaymentGlConfigDetail first($columns = ['*'])
*/
class POSSOURCEPaymentGlConfigDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyCode',
        'companyID',
        'createdDateTime',
        'createdPCID',
        'createdUserGroup',
        'createdUserID',
        'createdUserName',
        'GLCode',
        'isAuthRequired',
        'isSync',
        'modifiedDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'paymentConfigMasterID',
        'timestamp',
        'transaction_log_id',
        'warehouseID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSSOURCEPaymentGlConfigDetail::class;
    }
}
