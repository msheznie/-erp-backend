<?php

namespace App\Repositories;

use App\Models\ERPAssetTransferDetailsRefferedback;
use App\Repositories\BaseRepository;

/**
 * Class ERPAssetTransferDetailsRefferedbackRepository
 * @package App\Repositories
 * @version August 1, 2021, 8:47 am +04
 *
 * @method ERPAssetTransferDetailsRefferedback findWithoutFail($id, $columns = ['*'])
 * @method ERPAssetTransferDetailsRefferedback find($id, $columns = ['*'])
 * @method ERPAssetTransferDetailsRefferedback first($columns = ['*'])
*/
class ERPAssetTransferDetailsRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'erp_fa_fa_asset_transfer_id',
        'erp_fa_fa_asset_request_id',
        'erp_fa_fa_asset_request_detail_id',
        'from_location_id',
        'to_location_id',
        'receivedYN',
        'fa_master_id',
        'pr_created_yn',
        'timesReferred',
        'company_id',
        'created_user_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ERPAssetTransferDetailsRefferedback::class;
    }
}
