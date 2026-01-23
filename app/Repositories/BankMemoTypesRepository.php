<?php

namespace App\Repositories;

use App\Models\BankMemoTypes;
use App\Repositories\BaseRepository;

/**
 * Class BankMemoTypesRepository
 * @package App\Repositories
 * @version October 2, 2018, 6:58 am UTC
 *
 * @method BankMemoTypes findWithoutFail($id, $columns = ['*'])
 * @method BankMemoTypes find($id, $columns = ['*'])
 * @method BankMemoTypes first($columns = ['*'])
*/
class BankMemoTypesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bankMemoHeader',
        'sortOrder'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankMemoTypes::class;
    }
}
