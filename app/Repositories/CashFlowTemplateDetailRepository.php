<?php

namespace App\Repositories;

use App\Models\CashFlowTemplateDetail;
use App\Repositories\BaseRepository;

/**
 * Class CashFlowTemplateDetailRepository
 * @package App\Repositories
 * @version June 22, 2022, 2:06 pm +04
 *
 * @method CashFlowTemplateDetail findWithoutFail($id, $columns = ['*'])
 * @method CashFlowTemplateDetail find($id, $columns = ['*'])
 * @method CashFlowTemplateDetail first($columns = ['*'])
*/
class CashFlowTemplateDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'cashFlowTemplateID',
        'description',
        'type',
        'masterID',
        'sortOrder',
        'subExits',
        'logicType',
        'controlAccountType',
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
        return CashFlowTemplateDetail::class;
    }
}
