<?php

namespace App\Repositories;

use App\Models\ItemIssueDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ItemIssueDetailsRepository
 * @package App\Repositories
 * @version June 20, 2018, 4:20 am UTC
 *
 * @method ItemIssueDetails findWithoutFail($id, $columns = ['*'])
 * @method ItemIssueDetails find($id, $columns = ['*'])
 * @method ItemIssueDetails first($columns = ['*'])
*/
class ItemIssueDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'itemIssueAutoID',
        'itemIssueCode',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'unitOfMeasureIssued',
        'clientReferenceNumber',
        'qtyRequested',
        'qtyIssued',
        'comments',
        'convertionMeasureVal',
        'qtyIssuedDefaultMeasure',
        'localCurrencyID',
        'issueCostLocal',
        'issueCostLocalTotal',
        'reportingCurrencyID',
        'issueCostRpt',
        'issueCostRptTotal',
        'currentStockQty',
        'currentWareHouseStockQty',
        'currentStockQtyInDamageReturn',
        'maxQty',
        'minQty',
        'selectedForBillingOP',
        'selectedForBillingOPtemp',
        'opTicketNo',
        'del',
        'backLoad',
        'used',
        'grvDocumentNO',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'p1',
        'p2',
        'p3',
        'p4',
        'p5',
        'p6',
        'p7',
        'p8',
        'p9',
        'p10',
        'p11',
        'p12',
        'p13',
        'pl10',
        'pl3',
        'timestamp',
        'qtyAvailableToIssue'
    ];
    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemIssueDetails::class;
    }
}
