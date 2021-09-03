<?php

namespace App\Repositories;

use App\Models\SMEPayAsset;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SMEPayAssetRepository
 * @package App\Repositories
 * @version August 10, 2021, 8:11 am +04
 *
 * @method SMEPayAsset findWithoutFail($id, $columns = ['*'])
 * @method SMEPayAsset find($id, $columns = ['*'])
 * @method SMEPayAsset first($columns = ['*'])
*/
class SMEPayAssetRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'empID',
        'assetTypeID',
        'description',
        'asset_serial_no',
        'assetConditionID',
        'handOverDate',
        'returnStatus',
        'returnDate',
        'returnComment',
        'companyID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SMEPayAsset::class;
    }
}
