<?php

namespace App\Repositories;

use App\Models\ExternalLinkHash;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ExternalLinkHashRepository
 * @package App\Repositories
 * @version November 8, 2020, 10:34 am +04
 *
 * @method ExternalLinkHash findWithoutFail($id, $columns = ['*'])
 * @method ExternalLinkHash find($id, $columns = ['*'])
 * @method ExternalLinkHash first($columns = ['*'])
*/
class ExternalLinkHashRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'hashKey',
        'generatedBy',
        'genratedDate',
        'expiredIn',
        'isUsed'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ExternalLinkHash::class;
    }
}
