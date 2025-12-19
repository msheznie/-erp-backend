<?php

namespace App\Repositories;

use App\Models\HRMSJvMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HRMSJvMasterRepository
 * @package App\Repositories
 * @version October 4, 2018, 4:09 am UTC
 *
 * @method HRMSJvMaster findWithoutFail($id, $columns = ['*'])
 * @method HRMSJvMaster find($id, $columns = ['*'])
 * @method HRMSJvMaster first($columns = ['*'])
*/
class HRMSJvMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'salaryProcessMasterID',
        'accruvalNarration',
        'accrualDateAsOF',
        'documentID',
        'JVCode',
        'serialNo',
        'companyID',
        'accmonth',
        'accYear',
        'accConfirmedYN',
        'accConfirmedBy',
        'accConfirmedDate',
        'jvMasterAutoID',
        'accJVSelectedYN',
        'accJVpostedYN',
        'jvPostedBy',
        'jvPostedDate',
        'createdby',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HRMSJvMaster::class;
    }
}
