<?php

namespace App\Repositories;

use App\Models\TaxType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TaxTypeRepository
 * @package App\Repositories
 * @version April 23, 2018, 8:04 am UTC
 *
 * @method TaxType findWithoutFail($id, $columns = ['*'])
 * @method TaxType find($id, $columns = ['*'])
 * @method TaxType first($columns = ['*'])
*/
class TaxTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'typeDescription'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TaxType::class;
    }
}
