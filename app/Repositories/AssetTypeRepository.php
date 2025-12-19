<?php

namespace App\Repositories;

use App\Models\AssetType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AssetTypeRepository
 * @package App\Repositories
 * @version October 7, 2018, 5:06 am UTC
 *
 * @method AssetType findWithoutFail($id, $columns = ['*'])
 * @method AssetType find($id, $columns = ['*'])
 * @method AssetType first($columns = ['*'])
*/
class AssetTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'typeDes',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetType::class;
    }
}
