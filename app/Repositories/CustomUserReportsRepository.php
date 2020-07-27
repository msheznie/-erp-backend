<?php

namespace App\Repositories;

use App\Models\CustomUserReports;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomUserReportsRepository
 * @package App\Repositories
 * @version July 21, 2020, 2:55 pm +04
 *
 * @method CustomUserReports findWithoutFail($id, $columns = ['*'])
 * @method CustomUserReports find($id, $columns = ['*'])
 * @method CustomUserReports first($columns = ['*'])
*/
class CustomUserReportsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'report_master_id',
        'name',
        'is_private'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomUserReports::class;
    }
}
