<?php

namespace App\Repositories;

use App\Models\ItemReturnDetails;
use App\Repositories\BaseRepository;

/**
 * Class ItemReturnDetailsRepository
 * @package App\Repositories
 * @version July 16, 2018, 4:51 am UTC
 *
 * @method ItemReturnDetails findWithoutFail($id, $columns = ['*'])
 * @method ItemReturnDetails find($id, $columns = ['*'])
 * @method ItemReturnDetails first($columns = ['*'])
*/
class ItemReturnDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'itemReturnAutoID',
        'itemReturnCode',
        'issueCodeSystem',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'unitOfMeasureIssued',
        'qtyIssued',
        'convertionMeasureVal',
        'qtyIssuedDefaultMeasure',
        'comments',
        'localCurrencyID',
        'unitCostLocal',
        'reportingCurrencyID',
        'unitCostRpt',
        'qtyFromIssue',
        'selectedForBillingOP',
        'selectedForBillingOPtemp',
        'opTicketNo',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemReturnDetails::class;
    }
}
