<?php

namespace App\Repositories;

use App\Models\ChartOfAccount;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ChartOfAccountRepository
 * @package App\Repositories
 * @version February 27, 2018, 9:57 am UTC
 *
 * @method ChartOfAccount findWithoutFail($id, $columns = ['*'])
 * @method ChartOfAccount find($id, $columns = ['*'])
 * @method ChartOfAccount first($columns = ['*'])
*/
class ChartOfAccountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentID',
        'AccountCode',
        'AccountDescription',
        'masterAccount',
        'catogaryBLorPL',
        'controllAccountYN',
        'controlAccounts',
        'isApproved',
        'approvedBy',
        'approvedDate',
        'approvedComment',
        'isActive',
        'isBank',
        'AllocationID',
        'relatedPartyYN',
        'interCompanyID',
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
        return ChartOfAccount::class;
    }
}
