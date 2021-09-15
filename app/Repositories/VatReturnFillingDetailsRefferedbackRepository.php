<?php

namespace App\Repositories;

use App\Models\VatReturnFillingDetailsRefferedback;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class VatReturnFillingDetailsRefferedbackRepository
 * @package App\Repositories
 * @version September 15, 2021, 12:57 pm +04
 *
 * @method VatReturnFillingDetailsRefferedback findWithoutFail($id, $columns = ['*'])
 * @method VatReturnFillingDetailsRefferedback find($id, $columns = ['*'])
 * @method VatReturnFillingDetailsRefferedback first($columns = ['*'])
*/
class VatReturnFillingDetailsRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'returnFillingDetailID',
        'vatReturnFilledCategoryID',
        'vatReturnFillingID',
        'vatReturnFillingSubCatgeoryID',
        'taxAmount',
        'taxableAmount',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return VatReturnFillingDetailsRefferedback::class;
    }
}
