<?php

namespace App\Repositories;

use App\Models\ReportTemplateFieldType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ReportTemplateFieldTypeRepository
 * @package App\Repositories
 * @version January 16, 2019, 10:41 am +04
 *
 * @method ReportTemplateFieldType findWithoutFail($id, $columns = ['*'])
 * @method ReportTemplateFieldType find($id, $columns = ['*'])
 * @method ReportTemplateFieldType first($columns = ['*'])
*/
class ReportTemplateFieldTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'fieldType'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportTemplateFieldType::class;
    }
}
