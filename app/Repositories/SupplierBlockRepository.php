<?php

namespace App\Repositories;

use App\Models\SupplierBlock;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierBlockRepository
 * @package App\Repositories
 * @version February 14, 2024, 11:39 am +04
 *
 * @method SupplierBlock findWithoutFail($id, $columns = ['*'])
 * @method SupplierBlock find($id, $columns = ['*'])
 * @method SupplierBlock first($columns = ['*'])
*/
class SupplierBlockRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supplierCodeSytem',
        'blockType',
        'blockFrom',
        'blockTo',
        'blockReason'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierBlock::class;
    }
}
