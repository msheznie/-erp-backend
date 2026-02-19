<?php

namespace App\Repositories;

use App\Models\AssetDisposalDetailReferred;
use App\Repositories\BaseRepository;

/**
 * Class AssetDisposalDetailReferredRepository
 * @package App\Repositories
 * @version December 6, 2018, 11:35 am UTC
 *
 * @method AssetDisposalDetailReferred findWithoutFail($id, $columns = ['*'])
 * @method AssetDisposalDetailReferred find($id, $columns = ['*'])
 * @method AssetDisposalDetailReferred first($columns = ['*'])
*/
class AssetDisposalDetailReferredRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'assetDisposalDetailAutoID',
        'assetdisposalMasterAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'itemCode',
        'faID',
        'faCode',
        'faUnitSerialNo',
        'assetDescription',
        'COSTUNIT',
        'costUnitRpt',
        'netBookValueLocal',
        'depAmountLocal',
        'depAmountRpt',
        'netBookValueRpt',
        'COSTGLCODESystemID',
        'COSTGLCODE',
        'ACCDEPGLCODESystemID',
        'ACCDEPGLCODE',
        'DISPOGLCODESystemID',
        'DISPOGLCODE',
        'timesReferred',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetDisposalDetailReferred::class;
    }
}
