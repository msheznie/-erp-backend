<?php

namespace App\Repositories;

use App\Models\SMECompanyPolicyValue;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SMECompanyPolicyValueRepository
 * @package App\Repositories
 * @version March 9, 2021, 8:39 am +04
 *
 * @method SMECompanyPolicyValue findWithoutFail($id, $columns = ['*'])
 * @method SMECompanyPolicyValue find($id, $columns = ['*'])
 * @method SMECompanyPolicyValue first($columns = ['*'])
*/
class SMECompanyPolicyValueRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companypolicymasterID',
        'value',
        'systemValue',
        'companyID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SMECompanyPolicyValue::class;
    }
}
