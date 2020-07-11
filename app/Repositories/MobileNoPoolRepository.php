<?php

namespace App\Repositories;

use App\Models\MobileNoPool;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class MobileNoPoolRepository
 * @package App\Repositories
 * @version July 9, 2020, 11:06 am +04
 *
 * @method MobileNoPool findWithoutFail($id, $columns = ['*'])
 * @method MobileNoPool find($id, $columns = ['*'])
 * @method MobileNoPool first($columns = ['*'])
*/
class MobileNoPoolRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'mobileNo',
        'companySystemID',
        'companyID',
        'isRoaming',
        'isIDD',
        'mobilePlan',
        'isMobileDataActivated',
        'isDataRoaming',
        'DataLimit',
        'isAssigned',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MobileNoPool::class;
    }
}
