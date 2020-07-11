<?php

namespace App\Repositories;

use App\Models\MobileMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class MobileMasterRepository
 * @package App\Repositories
 * @version July 9, 2020, 1:46 pm +04
 *
 * @method MobileMaster findWithoutFail($id, $columns = ['*'])
 * @method MobileMaster find($id, $columns = ['*'])
 * @method MobileMaster first($columns = ['*'])
*/
class MobileMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'empID',
        'employeeSystemID',
        'assignDate',
        'mobileNoPoolID',
        'mobileNo',
        'description',
        'currentPlan',
        'isIDDActive',
        'isRoamingActive',
        'currency',
        'creditlimit',
        'isDataRoaming',
        'isActive',
        'datedeactivated',
        'recoverYN',
        'isInternetSim',
        'createDate',
        'createUserID',
        'createPCID',
        'modifiedpc',
        'modifiedUser',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MobileMaster::class;
    }
}
