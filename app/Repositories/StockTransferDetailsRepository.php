<?php

namespace App\Repositories;

use App\Models\StockTransferDetails;
use App\Repositories\BaseRepository;

/**
 * Class StockTransferDetailsRepository
 * @package App\Repositories
 * @version July 16, 2018, 10:12 am UTC
 *
 * @method StockTransferDetails findWithoutFail($id, $columns = ['*'])
 * @method StockTransferDetails find($id, $columns = ['*'])
 * @method StockTransferDetails first($columns = ['*'])
*/
class StockTransferDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'stockTransferAutoID',
        'stockTransferCode',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBS',
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
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockTransferDetails::class;
    }
}
