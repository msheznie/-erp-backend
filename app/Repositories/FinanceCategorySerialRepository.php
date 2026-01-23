<?php

namespace App\Repositories;

use App\Models\FinanceCategorySerial;
use App\Repositories\BaseRepository;

/**
 * Class FinanceCategorySerialRepository
 * @package App\Repositories
 * @version June 3, 2021, 1:17 pm +04
 *
 * @method FinanceCategorySerial findWithoutFail($id, $columns = ['*'])
 * @method FinanceCategorySerial find($id, $columns = ['*'])
 * @method FinanceCategorySerial first($columns = ['*'])
*/
class FinanceCategorySerialRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'faFinanceCatID',
        'lastSerialNo',
        'companySystemID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FinanceCategorySerial::class;
    }
}
