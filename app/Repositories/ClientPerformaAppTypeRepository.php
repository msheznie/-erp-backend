<?php

namespace App\Repositories;

use App\Models\ClientPerformaAppType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ClientPerformaAppTypeRepository
 * @package App\Repositories
 * @version February 9, 2020, 2:01 pm +04
 *
 * @method ClientPerformaAppType findWithoutFail($id, $columns = ['*'])
 * @method ClientPerformaAppType find($id, $columns = ['*'])
 * @method ClientPerformaAppType first($columns = ['*'])
*/
class ClientPerformaAppTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ClientPerformaAppType::class;
    }
}
