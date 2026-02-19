<?php

namespace App\Repositories;

use App\Models\ExpenseClaimCategoriesMaster;
use App\Repositories\BaseRepository;

/**
 * Class ExpenseClaimCategoriesMasterRepository
 * @package App\Repositories
 * @version January 6, 2022, 2:05 pm +04
 *
 * @method ExpenseClaimCategoriesMaster findWithoutFail($id, $columns = ['*'])
 * @method ExpenseClaimCategoriesMaster find($id, $columns = ['*'])
 * @method ExpenseClaimCategoriesMaster first($columns = ['*'])
*/
class ExpenseClaimCategoriesMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'claimcategoriesDescription',
        'glAutoID',
        'glCode',
        'glCodeDescription',
        'type',
        'fuelUsageYN',
        'companyID',
        'companyCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ExpenseClaimCategoriesMaster::class;
    }
}
