<?php

namespace App\Repositories;

use App\Models\SMELaveGroup;
use App\Repositories\BaseRepository;

/**
 * Class SMELaveGroupRepository
 * @package App\Repositories
 * @version March 9, 2021, 8:49 am +04
 *
 * @method SMELaveGroup findWithoutFail($id, $columns = ['*'])
 * @method SMELaveGroup find($id, $columns = ['*'])
 * @method SMELaveGroup first($columns = ['*'])
*/
class SMELaveGroupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'companyID',
        'isMonthly',
        'isDefault',
        'approvalLevels',
        'timestamp',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SMELaveGroup::class;
    }
}
