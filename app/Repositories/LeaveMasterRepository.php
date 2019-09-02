<?php

namespace App\Repositories;

use App\Models\LeaveMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class LeaveMasterRepository
 * @package App\Repositories
 * @version September 1, 2019, 9:07 am +04
 *
 * @method LeaveMaster findWithoutFail($id, $columns = ['*'])
 * @method LeaveMaster find($id, $columns = ['*'])
 * @method LeaveMaster first($columns = ['*'])
*/
class LeaveMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'leaveCode',
        'leavetype',
        'deductSalary',
        'restrictDays',
        'isAttachmentMandatory',
        'managerDeadline',
        'maxDays',
        'allowMultipleLeave',
        'isProbation',
        'createdUserGroup',
        'createdPCid',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LeaveMaster::class;
    }
}
