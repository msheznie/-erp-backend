<?php

namespace App\Repositories;

use App\Models\ERPAssetTransferDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ERPAssetTransferDetailRepository
 * @package App\Repositories
 * @version July 15, 2021, 4:46 pm +04
 *
 * @method ERPAssetTransferDetail findWithoutFail($id, $columns = ['*'])
 * @method ERPAssetTransferDetail find($id, $columns = ['*'])
 * @method ERPAssetTransferDetail first($columns = ['*'])
*/
class ERPAssetTransferDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'erp_fa_fa_asset_transfer_id',
        'erp_fa_fa_asset_request_id',
        'erp_fa_fa_asset_request_detail_id',
        'from_location_id',
        'to_location_id',
        'fa_master_id',
        'pr_created_yn',
        'company_id',
        'created_user_id',
        'from_emp_id',
        'to_emp_id',
        'departmentSystemID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ERPAssetTransferDetail::class;
    }
}
