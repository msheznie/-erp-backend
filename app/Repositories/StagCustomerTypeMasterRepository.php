<?php

namespace App\Repositories;

use App\Models\StagCustomerTypeMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StagCustomerTypeMasterRepository
 * @package App\Repositories
 * @version July 27, 2022, 12:18 pm +04
 *
 * @method StagCustomerTypeMaster findWithoutFail($id, $columns = ['*'])
 * @method StagCustomerTypeMaster find($id, $columns = ['*'])
 * @method StagCustomerTypeMaster first($columns = ['*'])
*/
class StagCustomerTypeMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customerDescription',
        'displayDescription',
        'isThirdPartyDelivery',
        'isDineIn',
        'isDefault',
        'company_id',
        'createdBy',
        'createdDatetime',
        'createdPc',
        'timestamp',
        'imageName',
        'transaction_log_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StagCustomerTypeMaster::class;
    }
}
