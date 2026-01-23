<?php

namespace App\Repositories;

use App\Models\HRMSJvDetails;
use App\Repositories\BaseRepository;

/**
 * Class HRMSJvDetailsRepository
 * @package App\Repositories
 * @version October 4, 2018, 4:04 am UTC
 *
 * @method HRMSJvDetails findWithoutFail($id, $columns = ['*'])
 * @method HRMSJvDetails find($id, $columns = ['*'])
 * @method HRMSJvDetails first($columns = ['*'])
*/
class HRMSJvDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'accMasterID',
        'salaryProcessMasterID',
        'accrualNarration',
        'accrualDateAsOF',
        'companyID',
        'serviceLine',
        'departureDate',
        'callOfDate',
        'GlCode',
        'accrualAmount',
        'accrualCurrency',
        'localAmount',
        'localCurrency',
        'rptAmount',
        'rptCurrency',
        'jvMasterAutoID',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HRMSJvDetails::class;
    }
}
