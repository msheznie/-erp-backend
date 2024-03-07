<?php

namespace App\Repositories;

use App\Models\PaymentTermTemplate;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PaymentTermTemplateRepository
 * @package App\Repositories
 * @version February 1, 2024, 5:19 pm +04
 *
 * @method PaymentTermTemplate findWithoutFail($id, $columns = ['*'])
 * @method PaymentTermTemplate find($id, $columns = ['*'])
 * @method PaymentTermTemplate first($columns = ['*'])
*/
class PaymentTermTemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templateName',
        'description',
        'isDefault',
        'isActive'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaymentTermTemplate::class;
    }
}
