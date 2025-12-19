<?php

namespace App\Repositories;

use App\Models\PaymentTermConfig;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PaymentTermConfigRepository
 * @package App\Repositories
 * @version February 7, 2024, 7:30 pm +04
 *
 * @method PaymentTermConfig findWithoutFail($id, $columns = ['*'])
 * @method PaymentTermConfig find($id, $columns = ['*'])
 * @method PaymentTermConfig first($columns = ['*'])
*/
class PaymentTermConfigRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templateId',
        'term',
        'description',
        'sortOrder',
        'isSelected'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaymentTermConfig::class;
    }
}
