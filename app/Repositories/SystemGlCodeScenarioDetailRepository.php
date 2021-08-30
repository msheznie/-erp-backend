<?php

namespace App\Repositories;

use App\Models\SystemGlCodeScenarioDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SystemGlCodeScenarioDetailRepository
 * @package App\Repositories
 * @version August 30, 2021, 1:49 pm +04
 *
 * @method SystemGlCodeScenarioDetail findWithoutFail($id, $columns = ['*'])
 * @method SystemGlCodeScenarioDetail find($id, $columns = ['*'])
 * @method SystemGlCodeScenarioDetail first($columns = ['*'])
*/
class SystemGlCodeScenarioDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'systemGlScenarioID',
        'companySystemID',
        'chartOfAccountSystemID',
        'serviceLineSystemID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SystemGlCodeScenarioDetail::class;
    }
}
