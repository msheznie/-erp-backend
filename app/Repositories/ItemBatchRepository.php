<?php

namespace App\Repositories;

use App\Models\ItemBatch;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ItemBatchRepository
 * @package App\Repositories
 * @version March 16, 2022, 7:49 am +04
 *
 * @method ItemBatch findWithoutFail($id, $columns = ['*'])
 * @method ItemBatch find($id, $columns = ['*'])
 * @method ItemBatch first($columns = ['*'])
*/
class ItemBatchRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'itemSystemCode',
        'batchCode',
        'expireDate',
        'wareHouseSystemID',
        'binLocation',
        'soldFlag',
        'quantity',
        'copiedQty'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemBatch::class;
    }
}
