<?php

namespace App\Repositories;

use App\Models\PoAddons;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PoAddonsRepository
 * @package App\Repositories
 * @version July 20, 2018, 4:54 am UTC
 *
 * @method PoAddons findWithoutFail($id, $columns = ['*'])
 * @method PoAddons find($id, $columns = ['*'])
 * @method PoAddons first($columns = ['*'])
*/
class PoAddonsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'poId',
        'idaddOnCostCategories',
        'supplierID',
        'currencyID',
        'amount',
        'glCode',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PoAddons::class;
    }
}
