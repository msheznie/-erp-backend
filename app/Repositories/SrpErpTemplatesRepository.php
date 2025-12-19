<?php

namespace App\Repositories;

use App\Models\SrpErpTemplates;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SrpErpTemplatesRepository
 * @package App\Repositories
 * @version September 3, 2021, 2:42 pm +04
 *
 * @method SrpErpTemplates findWithoutFail($id, $columns = ['*'])
 * @method SrpErpTemplates find($id, $columns = ['*'])
 * @method SrpErpTemplates first($columns = ['*'])
*/
class SrpErpTemplatesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'TempMasterID',
        'FormCatID',
        'navigationMenuID',
        'templateKey',
        'CreatedUserName',
        'CreatedDate',
        'CreatedPC',
        'ModifiedUserName',
        'Timestamp',
        'ModifiedPC'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SrpErpTemplates::class;
    }
}
