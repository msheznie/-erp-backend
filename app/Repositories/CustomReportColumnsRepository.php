<?php

namespace App\Repositories;

use App\Models\CustomReportColumns;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomReportColumnsRepository
 * @package App\Repositories
 * @version July 21, 2020, 2:55 pm +04
 *
 * @method CustomReportColumns findWithoutFail($id, $columns = ['*'])
 * @method CustomReportColumns find($id, $columns = ['*'])
 * @method CustomReportColumns first($columns = ['*'])
*/
class CustomReportColumnsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'report_master_id',
        'label',
        'column',
        'column_type',
        'sort_order',
        'is_sortabel',
        'sort_by',
        'is_group_by',
        'is_default_sort',
        'is_default_group_by',
        'is_filter'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomReportColumns::class;
    }
}
