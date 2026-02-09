<?php

namespace App\Repositories;

use App\Models\HRMSChartOfAccounts;
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
class HRMSChartOfAccountsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'AccountCode',
        'AccountDescription',
        'empGroup',
        'createdPcID',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'modifiedPc',
        'modifiedUser',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HRMSChartOfAccounts::class;
    }
}
