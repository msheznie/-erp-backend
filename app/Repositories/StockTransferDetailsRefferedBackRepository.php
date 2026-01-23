<?php

namespace App\Repositories;

use App\Models\StockTransferDetailsRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class StockTransferDetailsRefferedBackRepository
 * @package App\Repositories
 * @version November 29, 2018, 5:39 am UTC
 *
 * @method StockTransferDetailsRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method StockTransferDetailsRefferedBack find($id, $columns = ['*'])
 * @method StockTransferDetailsRefferedBack first($columns = ['*'])
*/
class StockTransferDetailsRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'stockTransferDetailsID',
        'stockTransferAutoID',
        'stockTransferCode',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBS',
        'financeGLcodebBSSystemID',
        'qty',
        'currentStockQty',
        'warehouseStockQty',
        'localCurrencyID',
        'unitCostLocal',
        'reportingCurrencyID',
        'unitCostRpt',
        'comments',
        'addedToRecieved',
        'stockRecieved',
        'timesReferred',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockTransferDetailsRefferedBack::class;
    }
}
