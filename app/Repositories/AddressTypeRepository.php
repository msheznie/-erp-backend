<?php

namespace App\Repositories;

use App\Models\AddressType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AddressTypeRepository
 * @package App\Repositories
 * @version May 4, 2018, 11:11 am UTC
 *
 * @method AddressType findWithoutFail($id, $columns = ['*'])
 * @method AddressType find($id, $columns = ['*'])
 * @method AddressType first($columns = ['*'])
*/
class AddressTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'addressTypeDescription',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AddressType::class;
    }
}
