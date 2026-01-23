<?php

namespace App\Repositories;

use App\Models\InsurancePolicyType;
use App\Repositories\BaseRepository;

/**
 * Class InsurancePolicyTypeRepository
 * @package App\Repositories
 * @version October 11, 2018, 4:50 am UTC
 *
 * @method InsurancePolicyType findWithoutFail($id, $columns = ['*'])
 * @method InsurancePolicyType find($id, $columns = ['*'])
 * @method InsurancePolicyType first($columns = ['*'])
*/
class InsurancePolicyTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'policyDescription'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return InsurancePolicyType::class;
    }
}
