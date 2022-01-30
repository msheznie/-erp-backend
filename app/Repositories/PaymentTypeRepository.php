<?php

namespace App\Repositories;

use App\Models\PaymentType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PaymentTypeRepository
 * @package App\Repositories
 * @version January 26, 2022, 3:04 pm +04
 *
 * @method PaymentType findWithoutFail($id, $columns = ['*'])
 * @method PaymentType find($id, $columns = ['*'])
 * @method PaymentType first($columns = ['*'])
*/
class PaymentTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaymentType::class;
    }
}
