<?php

namespace App\Repositories;

use App\Models\CustomReportMaster;
use App\Repositories\BaseRepository;

/**
 * Class CustomReportMasterRepository
 * @package App\Repositories
 * @version July 21, 2020, 2:54 pm +04
 *
 * @method CustomReportMaster findWithoutFail($id, $columns = ['*'])
 * @method CustomReportMaster find($id, $columns = ['*'])
 * @method CustomReportMaster first($columns = ['*'])
*/
class CustomReportMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'report_type_id',
        'is_active'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomReportMaster::class;
    }
}
