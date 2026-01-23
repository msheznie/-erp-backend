<?php

namespace App\Repositories;

use App\Models\VatReturnFilledCategoryRefferedback;
use App\Repositories\BaseRepository;

/**
 * Class VatReturnFilledCategoryRefferedbackRepository
 * @package App\Repositories
 * @version September 15, 2021, 12:56 pm +04
 *
 * @method VatReturnFilledCategoryRefferedback findWithoutFail($id, $columns = ['*'])
 * @method VatReturnFilledCategoryRefferedback find($id, $columns = ['*'])
 * @method VatReturnFilledCategoryRefferedback first($columns = ['*'])
*/
class VatReturnFilledCategoryRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'returnFilledCategoryID',
        'categoryID',
        'vatReturnFillingID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return VatReturnFilledCategoryRefferedback::class;
    }
}
