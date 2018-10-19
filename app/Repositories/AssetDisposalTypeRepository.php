<?php

namespace App\Repositories;

use App\Models\AssetDisposalType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AssetDisposalTypeRepository
 * @package App\Repositories
 * @version October 19, 2018, 4:15 am UTC
 *
 * @method AssetDisposalType findWithoutFail($id, $columns = ['*'])
 * @method AssetDisposalType find($id, $columns = ['*'])
 * @method AssetDisposalType first($columns = ['*'])
*/
class AssetDisposalTypeRepository extends BaseRepository
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
        return AssetDisposalType::class;
    }
}
