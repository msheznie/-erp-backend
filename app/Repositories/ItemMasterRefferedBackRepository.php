<?php

namespace App\Repositories;

use App\Models\ItemMasterRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ItemMasterRefferedBackRepository
 * @package App\Repositories
 * @version December 14, 2018, 11:17 am UTC
 *
 * @method ItemMasterRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method ItemMasterRefferedBack find($id, $columns = ['*'])
 * @method ItemMasterRefferedBack first($columns = ['*'])
*/
class ItemMasterRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'itemCodeSystem',
        'primaryItemCode',
        'runningSerialOrder',
        'documentSystemID',
        'documentID',
        'primaryCompanySystemID',
        'primaryCompanyID',
        'primaryCode',
        'secondaryItemCode',
        'barcode',
        'itemDescription',
        'itemShortDescription',
        'itemUrl',
        'unit',
        'financeCategoryMaster',
        'financeCategorySub',
        'itemPicture',
        'selectedForAssign',
        'isActive',
        'RollLevForApp_curr',
        'sentConfirmationEmail',
        'confirmationEmailSentByEmpID',
        'confirmationEmailSentByEmpName',
        'itemConfirmedYN',
        'itemConfirmedByEMPSystemID',
        'itemConfirmedByEMPID',
        'itemConfirmedByEMPName',
        'itemConfirmedDate',
        'itemApprovedBySystemID',
        'itemApprovedBy',
        'itemApprovedYN',
        'itemApprovedDate',
        'itemApprovedComment',
        'timesReferred',
        'refferedBackYN',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timestamp',
        'createdUserSystemID',
        'modifiedUserSystemID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemMasterRefferedBack::class;
    }
}
