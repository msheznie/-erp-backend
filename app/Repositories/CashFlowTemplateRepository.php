<?php

namespace App\Repositories;

use App\Models\CashFlowTemplate;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CashFlowTemplateRepository
 * @package App\Repositories
 * @version June 22, 2022, 2:06 pm +04
 *
 * @method CashFlowTemplate findWithoutFail($id, $columns = ['*'])
 * @method CashFlowTemplate find($id, $columns = ['*'])
 * @method CashFlowTemplate first($columns = ['*'])
*/
class CashFlowTemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'type',
        'companySystemID',
        'isActive',
        'presentationType',
        'showNumbersIn',
        'showDecimalPlaceYN',
        'showZeroGlYN',
        'createdPCID',
        'createdUserSystemID',
        'modifiedPCID',
        'modifiedUserSystemID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CashFlowTemplate::class;
    }
}
