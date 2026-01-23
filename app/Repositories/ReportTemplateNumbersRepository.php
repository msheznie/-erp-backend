<?php

namespace App\Repositories;

use App\Models\ReportTemplateNumbers;
use App\Repositories\BaseRepository;

/**
 * Class ReportTemplateNumbersRepository
 * @package App\Repositories
 * @version January 29, 2019, 9:55 am +04
 *
 * @method ReportTemplateNumbers findWithoutFail($id, $columns = ['*'])
 * @method ReportTemplateNumbers find($id, $columns = ['*'])
 * @method ReportTemplateNumbers first($columns = ['*'])
*/
class ReportTemplateNumbersRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'value',
        'timesStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportTemplateNumbers::class;
    }
}
