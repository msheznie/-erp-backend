<?php

namespace App\Repositories;

use App\Models\BarcodeConfiguration;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BarcodeConfigurationRepository
 * @package App\Repositories
 * @version May 31, 2022, 12:35 pm +04
 *
 * @method BarcodeConfiguration findWithoutFail($id, $columns = ['*'])
 * @method BarcodeConfiguration find($id, $columns = ['*'])
 * @method BarcodeConfiguration first($columns = ['*'])
*/
class BarcodeConfigurationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'barcode_font',
        'height',
        'no_of_coulmns',
        'no_of_rows',
        'page_size',
        'width'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BarcodeConfiguration::class;
    }
}
