<?php

namespace App\Repositories;

use App\Models\PreDefinedReportTemplate;
use App\Repositories\BaseRepository;

/**
 * Class PreDefinedReportTemplateRepository
 * @package App\Repositories
 * @version January 31, 2020, 8:06 am +04
 *
 * @method PreDefinedReportTemplate findWithoutFail($id, $columns = ['*'])
 * @method PreDefinedReportTemplate find($id, $columns = ['*'])
 * @method PreDefinedReportTemplate first($columns = ['*'])
*/
class PreDefinedReportTemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'preDefinedReportTemplateCode',
        'templateName'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PreDefinedReportTemplate::class;
    }
}
