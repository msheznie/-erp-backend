<?php

namespace App\Repositories;

use App\Models\SupplierTransactions;
use InfyOm\Generator\Common\BaseRepository;


/**
 * Class SupplierMasterRepository
 * @package App\Repositories
 * @version March 3, 2022, 11:27 am UTC
 *
 * @method SupplierMaster findWithoutFail($id, $columns = ['*'])
 * @method SupplierMaster find($id, $columns = ['*'])
 * @method SupplierMaster first($columns = ['*'])
 */
class SupplierTransactionsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [

    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierTransactions::class;
    }

}
