<?php

namespace App\Repositories;

use App\Models\ReportTemplateColumns;
use App\Repositories\BaseRepository;

/**
 * Class ReportTemplateColumnsRepository
 * @package App\Repositories
 * @version December 27, 2018, 6:52 am UTC
 *
 * @method ReportTemplateColumns findWithoutFail($id, $columns = ['*'])
 * @method ReportTemplateColumns find($id, $columns = ['*'])
 * @method ReportTemplateColumns first($columns = ['*'])
*/
class ReportTemplateColumnsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'shortCode',
        'type',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportTemplateColumns::class;
    }
}
