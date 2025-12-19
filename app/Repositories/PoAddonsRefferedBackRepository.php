<?php

namespace App\Repositories;

use App\Models\PoAddonsRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PoAddonsRefferedBackRepository
 * @package App\Repositories
 * @version July 25, 2018, 5:19 am UTC
 *
 * @method PoAddonsRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method PoAddonsRefferedBack find($id, $columns = ['*'])
 * @method PoAddonsRefferedBack first($columns = ['*'])
*/
class PoAddonsRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'idpoAddons',
        'poId',
        'idaddOnCostCategories',
        'supplierID',
        'currencyID',
        'amount',
        'glCode',
        'timesReferred',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PoAddonsRefferedBack::class;
    }
}
