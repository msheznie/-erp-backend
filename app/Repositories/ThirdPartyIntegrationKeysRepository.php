<?php

namespace App\Repositories;

use App\Models\ThirdPartyIntegrationKeys;
use App\Repositories\BaseRepository;

/**
 * Class ThirdPartyIntegrationKeysRepository
 * @package App\Repositories
 * @version June 15, 2022, 3:17 pm +04
 *
 * @method ThirdPartyIntegrationKeys findWithoutFail($id, $columns = ['*'])
 * @method ThirdPartyIntegrationKeys find($id, $columns = ['*'])
 * @method ThirdPartyIntegrationKeys first($columns = ['*'])
*/
class ThirdPartyIntegrationKeysRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'third_party_system_id',
        'api_key'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ThirdPartyIntegrationKeys::class;
    }
}
