<?php

namespace App\Repositories;

use App\Models\ItemMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ItemMasterRepository
 * @package App\Repositories
 * @version March 8, 2018, 10:35 am UTC
 *
 * @method ItemMaster findWithoutFail($id, $columns = ['*'])
 * @method ItemMaster find($id, $columns = ['*'])
 * @method ItemMaster first($columns = ['*'])
*/
class ItemMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'sentConfirmationEmail',
        'confirmationEmailSentByEmpID',
        'confirmationEmailSentByEmpName',
        'itemConfirmedYN',
        'itemConfirmedByEMPID',
        'itemConfirmedByEMPName',
        'itemConfirmedDate',
        'itemApprovedBy',
        'itemApprovedYN',
        'itemApprovedDate',
        'itemApprovedComment',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemMaster::class;
    }
}
