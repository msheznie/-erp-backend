<?php

namespace App\Repositories;

use App\Models\GposPaymentGlConfigDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class GposPaymentGlConfigDetailRepository
 * @package App\Repositories
 * @version January 8, 2019, 8:57 am +04
 *
 * @method GposPaymentGlConfigDetail findWithoutFail($id, $columns = ['*'])
 * @method GposPaymentGlConfigDetail find($id, $columns = ['*'])
 * @method GposPaymentGlConfigDetail first($columns = ['*'])
*/
class GposPaymentGlConfigDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'paymentConfigMasterID',
        'GLCode',
        'companyID',
        'companyCode',
        'warehouseID',
        'isAuthRequired',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdUserName',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return GposPaymentGlConfigDetail::class;
    }
}
