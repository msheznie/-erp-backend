<?php

namespace App\Repositories;

use App\Models\YesNoSelectionForMinus;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class YesNoSelectionForMinusRepository
 * @package App\Repositories
 * @version March 27, 2018, 7:38 am UTC
 *
 * @method YesNoSelectionForMinus findWithoutFail($id, $columns = ['*'])
 * @method YesNoSelectionForMinus find($id, $columns = ['*'])
 * @method YesNoSelectionForMinus first($columns = ['*'])
*/
class YesNoSelectionForMinusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'selection'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return YesNoSelectionForMinus::class;
    }
}
