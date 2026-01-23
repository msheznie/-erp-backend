<?php

namespace App\Repositories;

use App\Models\SMEReligion;
use App\Repositories\BaseRepository;

/**
 * Class SMEReligionRepository
 * @package App\Repositories
 * @version March 9, 2021, 8:40 am +04
 *
 * @method SMEReligion findWithoutFail($id, $columns = ['*'])
 * @method SMEReligion find($id, $columns = ['*'])
 * @method SMEReligion first($columns = ['*'])
*/
class SMEReligionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'Religion',
        'ReligionAr',
        'SchMasterID',
        'BranchID',
        'Erp_companyID',
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
        return SMEReligion::class;
    }
}
