<?php

namespace App\Repositories;

use App\Models\GrvDetailsPrn;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class GrvDetailsPrnRepository
 * @package App\Repositories
 * @version January 8, 2021, 12:51 pm +04
 *
 * @method GrvDetailsPrn findWithoutFail($id, $columns = ['*'])
 * @method GrvDetailsPrn find($id, $columns = ['*'])
 * @method GrvDetailsPrn first($columns = ['*'])
*/
class GrvDetailsPrnRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'grvDetailsID',
        'purhasereturnDetailID',
        'prnQty'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return GrvDetailsPrn::class;
    }
}
