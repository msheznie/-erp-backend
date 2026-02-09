<?php

namespace App\Repositories;

use App\Models\BankMemoSupplierMaster;
use App\Repositories\BaseRepository;

/**
 * Class BankMemoSupplierMasterRepository
 * @package App\Repositories
 * @version March 8, 2018, 5:51 am UTC
 *
 * @method BankMemoSupplierMaster findWithoutFail($id, $columns = ['*'])
 * @method BankMemoSupplierMaster find($id, $columns = ['*'])
 * @method BankMemoSupplierMaster first($columns = ['*'])
*/
class BankMemoSupplierMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'memoHeader',
        'memoDetail',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankMemoSupplierMaster::class;
    }
}
