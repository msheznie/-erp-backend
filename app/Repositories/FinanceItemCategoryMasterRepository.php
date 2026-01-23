<?php

namespace App\Repositories;

use App\Models\FinanceItemCategoryMaster;
use App\Repositories\BaseRepository;

/**
 * Class FinanceItemCategoryMasterRepository
 * @package App\Repositories
 * @version March 8, 2018, 12:18 pm UTC
 *
 * @method FinanceItemCategoryMaster findWithoutFail($id, $columns = ['*'])
 * @method FinanceItemCategoryMaster find($id, $columns = ['*'])
 * @method FinanceItemCategoryMaster first($columns = ['*'])
*/
class FinanceItemCategoryMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'categoryDescription' => 'like',
      /*  'itemCodeDef',
        'numberOfDigits',
        'lastSerialOrder',
        'timeStamp',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime'*/
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FinanceItemCategoryMaster::class;
    }
}
