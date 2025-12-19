<?php

namespace App\Repositories;

use App\Models\CountryMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CountryMasterRepository
 * @package App\Repositories
 * @version February 27, 2018, 11:30 am UTC
 *
 * @method CountryMaster findWithoutFail($id, $columns = ['*'])
 * @method CountryMaster find($id, $columns = ['*'])
 * @method CountryMaster first($columns = ['*'])
*/
class CountryMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'countryCode',
        'countryName',
        'countryName_O',
        'nationality',
        'isLocal',
        'countryFlag'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CountryMaster::class;
    }
}
