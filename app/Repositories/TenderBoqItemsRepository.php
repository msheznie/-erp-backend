<?php

namespace App\Repositories;

use App\Models\TenderBoqItems;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderBoqItemsRepository
 * @package App\Repositories
 * @version April 8, 2022, 6:26 pm +04
 *
 * @method TenderBoqItems findWithoutFail($id, $columns = ['*'])
 * @method TenderBoqItems find($id, $columns = ['*'])
 * @method TenderBoqItems first($columns = ['*'])
*/
class TenderBoqItemsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'main_work_id',
        'item_id',
        'uom',
        'qty',
        'created_by',
        'updated_by',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderBoqItems::class;
    }
}
