<?php

namespace App\Repositories;

use App\Models\ReportCustomColumn;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ReportCustomColumnRepository
 * @package App\Repositories
 * @version May 2, 2025, 8:06 am +04
 *
 * @method ReportCustomColumn findWithoutFail($id, $columns = ['*'])
 * @method ReportCustomColumn find($id, $columns = ['*'])
 * @method ReportCustomColumn first($columns = ['*'])
*/
class ReportCustomColumnRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'column_name',
        'column_reference',
        'column_slug',
        'isActive',
        'isDefault',
        'master_column_reference'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportCustomColumn::class;
    }
}
