<?php

namespace App\Repositories;

use App\Models\SMENationality;
use App\Repositories\BaseRepository;

/**
 * Class SMENationalityRepository
 * @package App\Repositories
 * @version March 9, 2021, 8:42 am +04
 *
 * @method SMENationality findWithoutFail($id, $columns = ['*'])
 * @method SMENationality find($id, $columns = ['*'])
 * @method SMENationality first($columns = ['*'])
*/
class SMENationalityRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'Nationality',
        'NationalityAr',
        'SchMasterID',
        'BranchID',
        'Erp_companyID',
        'countryID',
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
        return SMENationality::class;
    }
}
