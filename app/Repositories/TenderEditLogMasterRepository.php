<?php

namespace App\Repositories;

use App\Models\TenderEditLogMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderEditLogMasterRepository
 * @package App\Repositories
 * @version March 9, 2023, 12:47 pm +04
 *
 * @method TenderEditLogMaster findWithoutFail($id, $columns = ['*'])
 * @method TenderEditLogMaster find($id, $columns = ['*'])
 * @method TenderEditLogMaster first($columns = ['*'])
*/
class TenderEditLogMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'approved',
        'approved_by_user_system_id',
        'approved_date',
        'companyID',
        'companySystemID',
        'departmentID',
        'departmentSystemID',
        'description',
        'documentCode',
        'documentSystemCode',
        'employeeID',
        'employeeSystemID',
        'status',
        'type',
        'version'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderEditLogMaster::class;
    }
}
