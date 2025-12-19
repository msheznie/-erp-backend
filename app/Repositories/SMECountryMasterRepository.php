<?php

namespace App\Repositories;

use App\Models\SMECountryMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SMECountryMasterRepository
 * @package App\Repositories
 * @version March 9, 2021, 8:41 am +04
 *
 * @method SMECountryMaster findWithoutFail($id, $columns = ['*'])
 * @method SMECountryMaster find($id, $columns = ['*'])
 * @method SMECountryMaster first($columns = ['*'])
*/
class SMECountryMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'countryShortCode',
        'CountryDes',
        'Nationality',
        'countryCode',
        'countryTimeZone'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SMECountryMaster::class;
    }
}
