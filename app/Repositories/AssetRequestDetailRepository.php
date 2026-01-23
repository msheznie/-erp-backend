<?php

namespace App\Repositories;

use App\Models\AssetRequestDetail;
use App\Repositories\BaseRepository;

/**
 * Class AssetRequestDetailRepository
 * @package App\Repositories
 * @version July 6, 2021, 12:09 pm +04
 *
 * @method AssetRequestDetail findWithoutFail($id, $columns = ['*'])
 * @method AssetRequestDetail find($id, $columns = ['*'])
 * @method AssetRequestDetail first($columns = ['*'])
*/
class AssetRequestDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'erp_fa_fa_asset_request_id',
        'detail',
        'qty',
        'comment',
        'company_id',
        'created_user_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetRequestDetail::class;
    }
}
