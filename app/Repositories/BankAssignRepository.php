<?php

namespace App\Repositories;

use App\Models\BankAssign;
use App\Repositories\BaseRepository;

/**
 * Class BankAssignRepository
 * @package App\Repositories
 * @version March 21, 2018, 8:34 am UTC
 *
 * @method BankAssign findWithoutFail($id, $columns = ['*'])
 * @method BankAssign find($id, $columns = ['*'])
 * @method BankAssign first($columns = ['*'])
*/
class BankAssignRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bankmasterAutoID',
        'companyID',
        'bankShortCode',
        'bankName',
        'isAssigned',
        'isDefault',
        'isActive',
        'createdDateTime',
        'createdByEmpID',
        'TimeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankAssign::class;
    }
}
