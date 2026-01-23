<?php

namespace App\Repositories;

use App\Models\ReportColumnTemplate;
use App\Repositories\BaseRepository;

/**
 * Class ReportColumnTemplateRepository
 * @package App\Repositories
 * @version April 9, 2020, 1:42 pm +04
 *
 * @method ReportColumnTemplate findWithoutFail($id, $columns = ['*'])
 * @method ReportColumnTemplate find($id, $columns = ['*'])
 * @method ReportColumnTemplate first($columns = ['*'])
*/
class ReportColumnTemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templateName',
        'templateImage'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportColumnTemplate::class;
    }
}
