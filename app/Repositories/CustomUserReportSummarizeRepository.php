<?php

namespace App\Repositories;

use App\Models\CustomUserReportSummarize;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomUserReportSummarizeRepository
 * @package App\Repositories
 * @version August 26, 2020, 4:06 pm +04
 *
 * @method CustomUserReportSummarize findWithoutFail($id, $columns = ['*'])
 * @method CustomUserReportSummarize find($id, $columns = ['*'])
 * @method CustomUserReportSummarize first($columns = ['*'])
*/
class CustomUserReportSummarizeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_report_id',
        'column_id',
        'type_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomUserReportSummarize::class;
    }
}
