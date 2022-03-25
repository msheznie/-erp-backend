<?php

namespace App\Repositories;

use App\Models\SrpErpPayShiftMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SrpErpPayShiftMasterRepository
 * @package App\Repositories
 * @version February 14, 2022, 9:18 am +04
 *
 * @method SrpErpPayShiftMaster findWithoutFail($id, $columns = ['*'])
 * @method SrpErpPayShiftMaster find($id, $columns = ['*'])
 * @method SrpErpPayShiftMaster first($columns = ['*'])
*/
class SrpErpPayShiftMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'Description',
        'isFlexyHour',
        'companyID',
        'isDefault',
        'companyCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SrpErpPayShiftMaster::class;
    }
}
