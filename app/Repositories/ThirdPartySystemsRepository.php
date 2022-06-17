<?php

namespace App\Repositories;

use App\Models\ThirdPartySystems;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ThirdPartySystemsRepository
 * @package App\Repositories
 * @version June 15, 2022, 3:08 pm +04
 *
 * @method ThirdPartySystems findWithoutFail($id, $columns = ['*'])
 * @method ThirdPartySystems find($id, $columns = ['*'])
 * @method ThirdPartySystems first($columns = ['*'])
*/
class ThirdPartySystemsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'status'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ThirdPartySystems::class;
    }
}
