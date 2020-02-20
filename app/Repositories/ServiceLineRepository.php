<?php

namespace App\Repositories;

use App\Models\ServiceLine;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ServiceLineRepository
 * @package App\Repositories
 * @version February 20, 2020, 8:10 am +04
 *
 * @method ServiceLine findWithoutFail($id, $columns = ['*'])
 * @method ServiceLine find($id, $columns = ['*'])
 * @method ServiceLine first($columns = ['*'])
*/
class ServiceLineRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'ServiceLineCode',
        'serviceLineMasterCode',
        'companySystemID',
        'companyID',
        'ServiceLineDes',
        'locationID',
        'isActive',
        'isPublic',
        'isServiceLine',
        'isDepartment',
        'isMaster',
        'consoleCode',
        'consoleDescription',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ServiceLine::class;
    }
}
