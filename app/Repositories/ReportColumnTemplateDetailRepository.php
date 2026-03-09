<?php

namespace App\Repositories;

use App\Models\ReportColumnTemplateDetail;
use App\Repositories\BaseRepository;

/**
 * Class ReportColumnTemplateDetailRepository
 * @package App\Repositories
 * @version April 9, 2020, 2:23 pm +04
 *
 * @method ReportColumnTemplateDetail findWithoutFail($id, $columns = ['*'])
 * @method ReportColumnTemplateDetail find($id, $columns = ['*'])
 * @method ReportColumnTemplateDetail first($columns = ['*'])
*/
class ReportColumnTemplateDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'reportColumnTemplateID',
        'columnID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportColumnTemplateDetail::class;
    }
}
