<?php

namespace App\Repositories;

use App\Models\FinalReturnIncomeTemplateLinks;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FinalReturnIncomeTemplateLinksRepository
 * @package App\Repositories
 * @version August 17, 2025, 4:48 am +04
 *
 * @method FinalReturnIncomeTemplateLinks findWithoutFail($id, $columns = ['*'])
 * @method FinalReturnIncomeTemplateLinks find($id, $columns = ['*'])
 * @method FinalReturnIncomeTemplateLinks first($columns = ['*'])
*/
class FinalReturnIncomeTemplateLinksRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templateMasterID',
        'templateDetailID',
        'sortOrder',
        'glAutoID',
        'glCode',
        'glDescription',
        'companySystemID',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FinalReturnIncomeTemplateLinks::class;
    }
}
