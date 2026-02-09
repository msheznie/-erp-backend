<?php

namespace App\Repositories;

use App\Models\TemplatesMaster;
use App\Repositories\BaseRepository;

/**
 * Class TemplatesMasterRepository
 * @package App\Repositories
 * @version October 17, 2018, 6:26 am UTC
 *
 * @method TemplatesMaster findWithoutFail($id, $columns = ['*'])
 * @method TemplatesMaster find($id, $columns = ['*'])
 * @method TemplatesMaster first($columns = ['*'])
*/
class TemplatesMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templateDescription',
        'templateType',
        'templateReportName',
        'isActive',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TemplatesMaster::class;
    }
}
