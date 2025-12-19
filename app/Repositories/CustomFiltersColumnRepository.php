<?php

namespace App\Repositories;

use App\Models\CustomFiltersColumn;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomFiltersColumnRepository
 * @package App\Repositories
 * @version August 4, 2020, 2:50 pm +04
 *
 * @method CustomFiltersColumn findWithoutFail($id, $columns = ['*'])
 * @method CustomFiltersColumn find($id, $columns = ['*'])
 * @method CustomFiltersColumn first($columns = ['*'])
*/
class CustomFiltersColumnRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'column_id',
        'operator',
        'value'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomFiltersColumn::class;
    }
}
