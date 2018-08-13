<?php

namespace App\Repositories;

use App\Models\AssetFinanceCategory;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AssetFinanceCategoryRepository
 * @package App\Repositories
 * @version July 12, 2018, 5:44 am UTC
 *
 * @method AssetFinanceCategory findWithoutFail($id, $columns = ['*'])
 * @method AssetFinanceCategory find($id, $columns = ['*'])
 * @method AssetFinanceCategory first($columns = ['*'])
*/
class AssetFinanceCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'financeCatDescription',
        'COSTGLCODE',
        'ACCDEPGLCODE',
        'DEPGLCODE',
        'DISPOGLCODE',
        'isActive',
        'sortOrder',
        'createdPcID',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'modifiedPc',
        'modifiedUser',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetFinanceCategory::class;
    }
}
