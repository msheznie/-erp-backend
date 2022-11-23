<?php

namespace App\Repositories;

use App\Models\POSSTAGPaymentGlConfigDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSSTAGPaymentGlConfigDetailRepository
 * @package App\Repositories
 * @version August 10, 2022, 1:39 pm +04
 *
 * @method POSSTAGPaymentGlConfigDetail findWithoutFail($id, $columns = ['*'])
 * @method POSSTAGPaymentGlConfigDetail find($id, $columns = ['*'])
 * @method POSSTAGPaymentGlConfigDetail first($columns = ['*'])
*/
class POSSTAGPaymentGlConfigDetailRepository extends BaseRepository
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
        return POSSTAGPaymentGlConfigDetail::class;
    }
}
