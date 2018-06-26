<?php

namespace App\Repositories;

use App\Models\CompanyPolicyCategory;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CompanyPolicyCategoryRepository
 * @package App\Repositories
 * @version May 11, 2018, 5:05 am UTC
 *
 * @method CompanyPolicyCategory findWithoutFail($id, $columns = ['*'])
 * @method CompanyPolicyCategory find($id, $columns = ['*'])
 * @method CompanyPolicyCategory first($columns = ['*'])
*/
class CompanyPolicyCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyPolicyCategoryDescription',
        'applicableDocumentID',
        'documentID',
        'impletemed',
        'isActive',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CompanyPolicyCategory::class;
    }
}
