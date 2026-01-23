<?php

namespace App\Repositories;

use App\Models\SMEEmpContractType;
use App\Repositories\BaseRepository;

/**
 * Class SMEEmpContractTypeRepository
 * @package App\Repositories
 * @version March 9, 2021, 8:43 am +04
 *
 * @method SMEEmpContractType findWithoutFail($id, $columns = ['*'])
 * @method SMEEmpContractType find($id, $columns = ['*'])
 * @method SMEEmpContractType first($columns = ['*'])
*/
class SMEEmpContractTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'Description',
        'typeID',
        'probation_period',
        'period',
        'is_open_contract',
        'SchMasterID',
        'BranchID',
        'Erp_CompanyID',
        'CreatedUserName',
        'CreatedDate',
        'CreatedPC',
        'ModifiedUserName',
        'Timestamp',
        'ModifiedPC'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SMEEmpContractType::class;
    }
}
