<?php

namespace App\Repositories;

use App\Models\LogisticShippingMode;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class LogisticShippingModeRepository
 * @package App\Repositories
 * @version September 12, 2018, 5:08 am UTC
 *
 * @method LogisticShippingMode findWithoutFail($id, $columns = ['*'])
 * @method LogisticShippingMode find($id, $columns = ['*'])
 * @method LogisticShippingMode first($columns = ['*'])
*/
class LogisticShippingModeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'modeShippingDescription',
        'createdUserID',
        'createdPCID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LogisticShippingMode::class;
    }
}
