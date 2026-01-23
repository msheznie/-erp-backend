<?php

namespace App\Repositories;

use App\Models\VatReturnFillingDetail;
use App\Repositories\BaseRepository;

/**
 * Class VatReturnFillingDetailRepository
 * @package App\Repositories
 * @version September 14, 2021, 8:31 am +04
 *
 * @method VatReturnFillingDetail findWithoutFail($id, $columns = ['*'])
 * @method VatReturnFillingDetail find($id, $columns = ['*'])
 * @method VatReturnFillingDetail first($columns = ['*'])
*/
class VatReturnFillingDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'vatReturnFilledCategoryID',
        'vatReturnFillingID',
        'vatReturnFillingSubCatgeoryID',
        'taxAmount',
        'taxableAmount'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return VatReturnFillingDetail::class;
    }
}
