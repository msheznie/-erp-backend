<?php

namespace App\Repositories;

use App\Models\PulledItemFromMR;
use App\Repositories\BaseRepository;

/**
 * Class HRMSChartOfAccountsRepository
 * @package App\Repositories
 * @version November 12, 2018, 5:07 am UTC
 *
 * @method HRMSChartOfAccounts findWithoutFail($id, $columns = ['*'])
 * @method HRMSChartOfAccounts find($id, $columns = ['*'])
 * @method HRMSChartOfAccounts first($columns = ['*'])
*/
class ERPPulledMRDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'itemCodeSystem',
        'purcahseRequestID',
        'RequestID',
        'RequestDetailsID',
        'itemPrimaryCode',
        'mr_qnty',
        'pr_qnty',
        'createdUserSystemID',
        'modifiedUserSystemID',
        'companySystemID',
        'updated_at',
        'createdDate'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PulledItemFromMR::class;
    }
}
