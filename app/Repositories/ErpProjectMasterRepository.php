<?php

namespace App\Repositories;

use App\Models\ErpProjectMaster;
use App\Repositories\BaseRepository;

/**
 * Class ErpProjectMasterRepository
 * @package App\Repositories
 * @version June 21, 2021, 11:55 am +04
 *
 * @method ErpProjectMaster findWithoutFail($id, $columns = ['*'])
 * @method ErpProjectMaster find($id, $columns = ['*'])
 * @method ErpProjectMaster first($columns = ['*'])
*/
class ErpProjectMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'projectCode',
        'description',
        'companyID',
        'companySystemID',
        'serviceLineSystemID',
        'serviceLineCode',
        'projectCurrencyID',
        'estimatedAmount',
        'start_date',
        'end_date',
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
        return ErpProjectMaster::class;
    }
}
