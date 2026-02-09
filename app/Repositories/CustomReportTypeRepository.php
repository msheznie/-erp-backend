<?php

namespace App\Repositories;

use App\Models\CustomReportType;
use App\Repositories\BaseRepository;

/**
 * Class CustomReportTypeRepository
 * @package App\Repositories
 * @version July 21, 2020, 2:51 pm +04
 *
 * @method CustomReportType findWithoutFail($id, $columns = ['*'])
 * @method CustomReportType find($id, $columns = ['*'])
 * @method CustomReportType first($columns = ['*'])
*/
class CustomReportTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'is_active'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomReportType::class;
    }
}
