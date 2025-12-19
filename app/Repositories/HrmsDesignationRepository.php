<?php

namespace App\Repositories;

use App\Models\HrmsDesignation;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HrmsDesignationRepository
 * @package App\Repositories
 * @version April 9, 2021, 12:56 pm +04
 *
 * @method HrmsDesignation findWithoutFail($id, $columns = ['*'])
 * @method HrmsDesignation find($id, $columns = ['*'])
 * @method HrmsDesignation first($columns = ['*'])
*/
class HrmsDesignationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'DesDescription',
        'isRequiredSelection',
        'SelectionID',
        'DesDashboardID',
        'SchMasterID',
        'BranchID',
        'Erp_companyID',
        'isDeleted',
        'CreatedUserName',
        'CreatedDate',
        'CreatedPC',
        'ModifiedUserName',
        'Timestamp',
        'ModifiedPC',
        'SortOrder'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HrmsDesignation::class;
    }
}
