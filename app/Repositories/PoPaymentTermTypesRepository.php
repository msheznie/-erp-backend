<?php

namespace App\Repositories;

use App\Models\PoPaymentTermTypes;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PoPaymentTermTypesRepository
 * @package App\Repositories
 * @version April 10, 2018, 1:07 pm UTC
 *
 * @method PoPaymentTermTypes findWithoutFail($id, $columns = ['*'])
 * @method PoPaymentTermTypes find($id, $columns = ['*'])
 * @method PoPaymentTermTypes first($columns = ['*'])
*/
class PoPaymentTermTypesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'categoryDescription'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PoPaymentTermTypes::class;
    }
}
