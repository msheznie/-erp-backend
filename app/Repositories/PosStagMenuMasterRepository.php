<?php

namespace App\Repositories;

use App\Models\PosStagMenuMaster;
use App\Repositories\BaseRepository;

/**
 * Class PosStagMenuMasterRepository
 * @package App\Repositories
 * @version July 27, 2022, 12:21 pm +04
 *
 * @method PosStagMenuMaster findWithoutFail($id, $columns = ['*'])
 * @method PosStagMenuMaster find($id, $columns = ['*'])
 * @method PosStagMenuMaster first($columns = ['*'])
*/
class PosStagMenuMasterRepository extends BaseRepository
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
        return PosStagMenuMaster::class;
    }
}
