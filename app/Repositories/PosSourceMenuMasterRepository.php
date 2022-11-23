<?php

namespace App\Repositories;

use App\Models\PosSourceMenuMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PosSourceMenuMasterRepository
 * @package App\Repositories
 * @version July 27, 2022, 12:22 pm +04
 *
 * @method PosSourceMenuMaster findWithoutFail($id, $columns = ['*'])
 * @method PosSourceMenuMaster find($id, $columns = ['*'])
 * @method PosSourceMenuMaster first($columns = ['*'])
*/
class PosSourceMenuMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'menuMasterDescription',
        'menuImage',
        'menuCategoryID',
        'menuCost',
        'barcode',
        'sellingPrice',
        'pricewithoutTax',
        'revenueGLAutoID',
        'TAXpercentage',
        'totalTaxAmount',
        'taxMasterID',
        'totalServiceCharge',
        'companyID',
        'menuStatus',
        'kotID',
        'preparationTime',
        'isPass',
        'isPack',
        'isVeg',
        'isAddOn',
        'showImageYN',
        'menuSizeID',
        'sortOrder',
        'sortOder',
        'isDeleted',
        'deletedBy',
        'deletedDatetime',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'createdUserGroup',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timeStamp',
        'transaction_log_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PosSourceMenuMaster::class;
    }
}
