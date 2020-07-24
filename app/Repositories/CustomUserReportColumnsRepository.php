<?php

namespace App\Repositories;

use App\Models\CustomUserReportColumns;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomUserReportColumnsRepository
 * @package App\Repositories
 * @version July 21, 2020, 2:56 pm +04
 *
 * @method CustomUserReportColumns findWithoutFail($id, $columns = ['*'])
 * @method CustomUserReportColumns find($id, $columns = ['*'])
 * @method CustomUserReportColumns first($columns = ['*'])
*/
class CustomUserReportColumnsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_report_id',
        'column_id',
        'label',
        'is_sortable',
        'sort_by',
        'is_group_by',
        'is_filter'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomUserReportColumns::class;
    }
}
