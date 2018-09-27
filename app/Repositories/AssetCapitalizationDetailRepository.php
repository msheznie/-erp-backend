<?php

namespace App\Repositories;

use App\Models\AssetCapitalizationDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AssetCapitalizationDetailRepository
 * @package App\Repositories
 * @version September 26, 2018, 7:04 am UTC
 *
 * @method AssetCapitalizationDetail findWithoutFail($id, $columns = ['*'])
 * @method AssetCapitalizationDetail find($id, $columns = ['*'])
 * @method AssetCapitalizationDetail first($columns = ['*'])
*/
class AssetCapitalizationDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'capitalizationID',
        'faID',
        'faCode',
        'assetDescription',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'dateAQ',
        'assetNBVLocal',
        'assetNBVRpt',
        'allocatedAmountLocal',
        'allocatedAmountRpt',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetCapitalizationDetail::class;
    }
}
