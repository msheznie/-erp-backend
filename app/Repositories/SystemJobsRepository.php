<?php

namespace App\Repositories;

use App\Models\SystemJobs;
use App\Repositories\BaseRepository;

/**
 * Class SystemJobsRepository
 * @package App\Repositories
 * @version February 17, 2022, 10:46 am +04
 *
 * @method SystemJobs findWithoutFail($id, $columns = ['*'])
 * @method SystemJobs find($id, $columns = ['*'])
 * @method SystemJobs first($columns = ['*'])
*/
class SystemJobsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'job_description',
        'job_signature',
        'is_active'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SystemJobs::class;
    }
}
