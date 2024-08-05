<?php

namespace App\Repositories;

use App\Models\TemplateSectionTableRow;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TemplateSectionTableRowRepository
 * @package App\Repositories
 * @version June 28, 2024, 4:37 pm +04
 *
 * @method TemplateSectionTableRow findWithoutFail($id, $columns = ['*'])
 * @method TemplateSectionTableRow find($id, $columns = ['*'])
 * @method TemplateSectionTableRow first($columns = ['*'])
*/
class TemplateSectionTableRowRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'table_id',
        'row_data',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TemplateSectionTableRow::class;
    }
}
