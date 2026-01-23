<?php

namespace App\Repositories;

use App\Models\DocumentSubProduct;
use App\Repositories\BaseRepository;

/**
 * Class DocumentSubProductRepository
 * @package App\Repositories
 * @version December 23, 2021, 3:44 pm +04
 *
 * @method DocumentSubProduct findWithoutFail($id, $columns = ['*'])
 * @method DocumentSubProduct find($id, $columns = ['*'])
 * @method DocumentSubProduct first($columns = ['*'])
*/
class DocumentSubProductRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentSystemCode',
        'documentDetailID',
        'productSerialID',
        'productBatchID',
        'quantity',
        'sold',
        'soldQty'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentSubProduct::class;
    }
}
