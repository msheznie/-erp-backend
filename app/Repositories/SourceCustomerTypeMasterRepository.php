<?php

namespace App\Repositories;

use App\Models\SourceCustomerTypeMaster;
use App\Repositories\BaseRepository;

/**
 * Class SourceCustomerTypeMasterRepository
 * @package App\Repositories
 * @version July 27, 2022, 12:19 pm +04
 *
 * @method SourceCustomerTypeMaster findWithoutFail($id, $columns = ['*'])
 * @method SourceCustomerTypeMaster find($id, $columns = ['*'])
 * @method SourceCustomerTypeMaster first($columns = ['*'])
*/
class SourceCustomerTypeMasterRepository extends BaseRepository
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
        return SourceCustomerTypeMaster::class;
    }
}
