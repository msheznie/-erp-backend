<?php

namespace App\Repositories;

use App\Models\MobileBillMaster;
use App\Repositories\BaseRepository;

/**
 * Class MobileBillMasterRepository
 * @package App\Repositories
 * @version July 12, 2020, 12:35 pm +04
 *
 * @method MobileBillMaster findWithoutFail($id, $columns = ['*'])
 * @method MobileBillMaster find($id, $columns = ['*'])
 * @method MobileBillMaster first($columns = ['*'])
*/
class MobileBillMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'billPeriod',
        'mobilebillmasterCode',
        'serialNo',
        'documentSystemID',
        'documentID',
        'companyID',
        'Description',
        'createDate',
        'confirmedYN',
        'confirmedDate',
        'confirmedby',
        'confirmedByEmployeeSystemID',
        'approvedby',
        'approvedbyEmployeeSystemID',
        'ApprovedYN',
        'approvedDate',
        'createUserID',
        'createPCID',
        'modifiedpc',
        'modifiedUser',
        'modifiedUserSystemID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MobileBillMaster::class;
    }
}
