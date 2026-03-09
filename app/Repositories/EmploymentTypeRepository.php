<?php

namespace App\Repositories;

use App\Models\EmploymentType;
use App\Repositories\BaseRepository;

/**
 * Class EmploymentTypeRepository
 * @package App\Repositories
 * @version November 7, 2018, 10:00 am UTC
 *
 * @method EmploymentType findWithoutFail($id, $columns = ['*'])
 * @method EmploymentType find($id, $columns = ['*'])
 * @method EmploymentType first($columns = ['*'])
*/
class EmploymentTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EmploymentType::class;
    }
}
