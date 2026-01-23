<?php

namespace App\Repositories;

use App\Models\FixedAssetCost;
use App\Repositories\BaseRepository;

/**
 * Class FixedAssetCostRepository
 * @package App\Repositories
 * @version October 8, 2018, 5:09 am UTC
 *
 * @method FixedAssetCost findWithoutFail($id, $columns = ['*'])
 * @method FixedAssetCost find($id, $columns = ['*'])
 * @method FixedAssetCost first($columns = ['*'])
*/
class FixedAssetCostRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'originDocumentSystemCode',
        'originDocumentID',
        'itemCode',
        'faID',
        'assetID',
        'assetDescription',
        'costDate',
        'localCurrencyID',
        'localAmount',
        'rptCurrencyID',
        'rptAmount',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FixedAssetCost::class;
    }
}
