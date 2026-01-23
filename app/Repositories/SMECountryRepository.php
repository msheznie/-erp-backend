<?php

namespace App\Repositories;

use App\Models\SMECountry;
use App\Repositories\BaseRepository;

/**
 * Class SMECountryRepository
 * @package App\Repositories
 * @version March 9, 2021, 8:41 am +04
 *
 * @method SMECountry findWithoutFail($id, $columns = ['*'])
 * @method SMECountry find($id, $columns = ['*'])
 * @method SMECountry first($columns = ['*'])
*/
class SMECountryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'countryShortCode',
        'CountryDes',
        'CountryTelCode',
        'countryMasterID',
        'SchMasterId',
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
        return SMECountry::class;
    }
}
