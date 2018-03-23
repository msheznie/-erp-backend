<?php

namespace App\Repositories;

use App\Models\YesNoSelection;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class YesNoSelectionRepository
 * @package App\Repositories
 * @version March 5, 2018, 12:29 pm UTC
 *
 * @method YesNoSelection findWithoutFail($id, $columns = ['*'])
 * @method YesNoSelection find($id, $columns = ['*'])
 * @method YesNoSelection first($columns = ['*'])
*/
class YesNoSelectionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'YesNo'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return YesNoSelection::class;
    }
}
