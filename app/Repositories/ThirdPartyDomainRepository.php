<?php

namespace App\Repositories;

use App\Models\ThirdPartyDomain;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ThirdPartyDomainRepository
 * @package App\Repositories
 * @version October 14, 2024, 4:36 pm +04
 *
 * @method ThirdPartyDomain findWithoutFail($id, $columns = ['*'])
 * @method ThirdPartyDomain find($id, $columns = ['*'])
 * @method ThirdPartyDomain first($columns = ['*'])
*/
class ThirdPartyDomainRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'thirdPartySystemId',
        'name'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ThirdPartyDomain::class;
    }
}
