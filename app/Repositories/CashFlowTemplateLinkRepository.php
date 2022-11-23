<?php

namespace App\Repositories;

use App\Models\CashFlowTemplateLink;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CashFlowTemplateLinkRepository
 * @package App\Repositories
 * @version June 27, 2022, 9:16 am +04
 *
 * @method CashFlowTemplateLink findWithoutFail($id, $columns = ['*'])
 * @method CashFlowTemplateLink find($id, $columns = ['*'])
 * @method CashFlowTemplateLink first($columns = ['*'])
*/
class CashFlowTemplateLinkRepository extends BaseRepository
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
        'subCategory',
        'categoryType',
        'companySystemID',
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
        return CashFlowTemplateLink::class;
    }
}
