<?php

namespace App\Repositories;

use App\Models\ReportTemplateEquity;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ReportTemplateEquityRepository
 * @package App\Repositories
 * @version November 6, 2024, 9:01 am +04
 *
 * @method ReportTemplateEquity findWithoutFail($id, $columns = ['*'])
 * @method ReportTemplateEquity find($id, $columns = ['*'])
 * @method ReportTemplateEquity first($columns = ['*'])
*/
class ReportTemplateEquityRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templateMasterID',
        'description',
        'sort_order'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportTemplateEquity::class;
    }
}
