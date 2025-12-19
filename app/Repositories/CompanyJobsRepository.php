<?php

namespace App\Repositories;

use App\Models\CompanyJobs;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CompanyJobsRepository
 * @package App\Repositories
 * @version February 17, 2022, 10:48 am +04
 *
 * @method CompanyJobs findWithoutFail($id, $columns = ['*'])
 * @method CompanyJobs find($id, $columns = ['*'])
 * @method CompanyJobs first($columns = ['*'])
*/
class CompanyJobsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'system_job_id',
        'company_id',
        'is_active'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CompanyJobs::class;
    }
}
