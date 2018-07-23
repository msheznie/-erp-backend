<?php

namespace App\Repositories;

use App\Models\StockReceiveDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StockReceiveDetailsRepository
 * @package App\Repositories
 * @version July 23, 2018, 4:57 am UTC
 *
 * @method StockReceiveDetails findWithoutFail($id, $columns = ['*'])
 * @method StockReceiveDetails find($id, $columns = ['*'])
 * @method StockReceiveDetails first($columns = ['*'])
*/
class StockReceiveDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'stockReceiveAutoID',
        'stockReceiveCode',
        'stockTransferAutoID',
        'stockTransferCode',
        'stockTransferDate',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBS',
        'localCurrencyID',
        'unitCostLocal',
        'reportingCurrencyID',
        'unitCostRpt',
        'qty',
        'comments',
        'timesReferred',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockReceiveDetails::class;
    }
}
