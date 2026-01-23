<?php

namespace App\Repositories;

use App\Models\AssetCapitalizatioDetReferred;
use App\Repositories\BaseRepository;

/**
 * Class AssetCapitalizatioDetReferredRepository
 * @package App\Repositories
 * @version December 6, 2018, 4:45 am UTC
 *
 * @method AssetCapitalizatioDetReferred findWithoutFail($id, $columns = ['*'])
 * @method AssetCapitalizatioDetReferred find($id, $columns = ['*'])
 * @method AssetCapitalizatioDetReferred first($columns = ['*'])
*/
class AssetCapitalizatioDetReferredRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'capitalizationDetailID',
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
        'timesReferred',
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
        return AssetCapitalizatioDetReferred::class;
    }
}
