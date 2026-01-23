<?php

namespace App\Repositories;

use App\Models\SecondaryCompany;
use App\Repositories\BaseRepository;

/**
 * Class SecondaryCompanyRepository
 * @package App\Repositories
 * @version March 19, 2020, 4:07 pm +04
 *
 * @method SecondaryCompany findWithoutFail($id, $columns = ['*'])
 * @method SecondaryCompany find($id, $columns = ['*'])
 * @method SecondaryCompany first($columns = ['*'])
*/
class SecondaryCompanyRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'logo',
        'name'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SecondaryCompany::class;
    }
}
