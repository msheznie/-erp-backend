<?php

namespace App\Repositories;

use App\Models\UnitConversion;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UnitConversionRepository
 * @package App\Repositories
 * @version March 22, 2018, 10:07 am UTC
 *
 * @method UnitConversion findWithoutFail($id, $columns = ['*'])
 * @method UnitConversion find($id, $columns = ['*'])
 * @method UnitConversion first($columns = ['*'])
*/
class UnitConversionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'masterUnitID',
        'subUnitID',
        'conversion',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UnitConversion::class;
    }
}
