<?php

namespace App\Repositories;

use App\Models\FinalReturnIncomeTemplateDefaults;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FinalReturnIncomeTemplateDefaultsRepository
 * @package App\Repositories
 * @version August 19, 2025, 4:26 am +04
 *
 * @method FinalReturnIncomeTemplateDefaults findWithoutFail($id, $columns = ['*'])
 * @method FinalReturnIncomeTemplateDefaults find($id, $columns = ['*'])
 * @method FinalReturnIncomeTemplateDefaults first($columns = ['*'])
*/
class FinalReturnIncomeTemplateDefaultsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'line_no',
        'type',
        'description',
        'appendix',
        'sectionType'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FinalReturnIncomeTemplateDefaults::class;
    }
}
