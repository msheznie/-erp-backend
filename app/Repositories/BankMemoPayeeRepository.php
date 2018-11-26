<?php

namespace App\Repositories;

use App\Models\BankMemoPayee;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BankMemoPayeeRepository
 * @package App\Repositories
 * @version November 26, 2018, 5:26 am UTC
 *
 * @method BankMemoPayee findWithoutFail($id, $columns = ['*'])
 * @method BankMemoPayee find($id, $columns = ['*'])
 * @method BankMemoPayee first($columns = ['*'])
*/
class BankMemoPayeeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'bankMemoTypeID',
        'memoHeader',
        'memoDetail',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankMemoPayee::class;
    }
}
