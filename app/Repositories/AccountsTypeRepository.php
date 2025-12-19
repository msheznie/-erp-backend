<?php

namespace App\Repositories;

use App\Models\AccountsType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AccountsTypeRepository
 * @package App\Repositories
 * @version March 16, 2018, 8:44 am UTC
 *
 * @method AccountsType findWithoutFail($id, $columns = ['*'])
 * @method AccountsType find($id, $columns = ['*'])
 * @method AccountsType first($columns = ['*'])
*/
class AccountsTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'code'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AccountsType::class;
    }
}
