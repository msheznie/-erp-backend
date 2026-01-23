<?php

namespace App\Repositories;

use App\Models\AccessTokens;
use App\Repositories\BaseRepository;

/**
 * Class AccessTokensRepository
 * @package App\Repositories
 * @version May 1, 2018, 6:43 am UTC
 *
 * @method AccessTokens findWithoutFail($id, $columns = ['*'])
 * @method AccessTokens find($id, $columns = ['*'])
 * @method AccessTokens first($columns = ['*'])
*/
class AccessTokensRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'client_id',
        'name',
        'scopes',
        'revoked',
        'expires_at'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AccessTokens::class;
    }
}
