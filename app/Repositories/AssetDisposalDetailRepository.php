<?php

namespace App\Repositories;

use App\Models\AssetDisposalDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AssetDisposalDetailRepository
 * @package App\Repositories
 * @version September 28, 2018, 10:07 am UTC
 *
 * @method AssetDisposalDetail findWithoutFail($id, $columns = ['*'])
 * @method AssetDisposalDetail find($id, $columns = ['*'])
 * @method AssetDisposalDetail first($columns = ['*'])
*/
class AssetDisposalDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'condition',
        'depMonth',
        'lastDepDate',
        'COSTUNIT',
        'costUnitRpt',
        'netBookValueLocal',
        'depAmountLocal',
        'depAmountRpt',
        'netBookValueRpt',
        'COSTGLCODE',
        'ACCDEPGLCODE',
        'DISPOGLCODE',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetDisposalDetail::class;
    }
}
