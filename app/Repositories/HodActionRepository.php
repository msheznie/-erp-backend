<?php

namespace App\Repositories;

use App\Models\HodAction;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HodActionRepository
 * @package App\Repositories
 * @version July 24, 2025, 1:47 pm +04
 *
 * @method HodAction findWithoutFail($id, $columns = ['*'])
 * @method HodAction find($id, $columns = ['*'])
 * @method HodAction first($columns = ['*'])
*/
class HodActionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HodAction::class;
    }
}
