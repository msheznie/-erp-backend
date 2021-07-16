<?php

namespace App\Repositories;

use App\Models\StockCountDetail;
use App\Models\StockCount;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\helper\Helper;
use App\helper\inventory;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StockCountDetailRepository
 * @package App\Repositories
 * @version June 10, 2021, 2:09 pm +04
 *
 * @method StockCountDetail findWithoutFail($id, $columns = ['*'])
 * @method StockCountDetail find($id, $columns = ['*'])
 * @method StockCountDetail first($columns = ['*'])
*/
class StockCountDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'stockCountAutoID',
        'stockCountAutoIDCode',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'partNumber',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'includePLForGRVYN',
        'systemQty',
        'noQty',
        'adjustedQty',
        'comments',
        'currentWacLocalCurrencyID',
        'currentWaclocal',
        'currentWacRptCurrencyID',
        'currentWacRpt',
        'wacAdjLocal',
        'wacAdjRptER',
        'wacAdjRpt',
        'wacAdjLocalER',
        'currenctStockQty',
        'timesReferred',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockCountDetail::class;
    }

    public function addStockCountItems($itemCodeSystem, $stockCount, $companySystemID)
    {
        $item = ItemAssigned::where('itemCodeSystem', $itemCodeSystem)
            ->where('companySystemID', $companySystemID)
            ->first();

        if (!empty($item)) {
            $input['stockCountAutoIDCode'] = $stockCount->stockCountCode;
            $input['stockCountAutoID'] = $stockCount->stockCountAutoID;
            $input['comments'] = null;
            $input['noQty'] = null;

            $input['itemCodeSystem'] = $item->itemCodeSystem;
            $input['itemPrimaryCode'] = $item->itemPrimaryCode;
            $input['itemDescription'] = $item->itemDescription;
            $input['itemUnitOfMeasure'] = $item->itemUnitOfMeasure;
            $input['partNumber'] = $item->secondaryItemCode;
            $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
            $input['itemFinanceCategorySubID'] = $item->financeCategorySub;

            $checkWhether = StockCount::where('stockCountAutoID', '!=', $stockCount->stockCountAutoID)
                                        ->where('companySystemID', $companySystemID)
                                        ->where('location', $stockCount->location)
                                        ->select([
                                            'stockCountAutoID',
                                            'companySystemID',
                                            'location',
                                            'stockCountCode',
                                            'approved'
                                        ])
                                        ->whereHas('details', function ($query) use ($itemCodeSystem, $input) {
                                            $query->where('itemCodeSystem', $itemCodeSystem);
                                        })
                                        ->where('approved', 0)
                                        ->where('refferedBackYN', 0)
                                        ->first();

            if (!empty($checkWhether)) {
                return ['status' => true, 'message' => "Item ".$item->itemDescription." cannot be used, Since, Stock Count (" . $checkWhether->stockCountCode . ") pending for approval with this item."];
            }

            $data = array('companySystemID' => $companySystemID,
                        'itemCodeSystem' => $itemCodeSystem,
                        'wareHouseId' => $stockCount->location);

            $input['currentWacLocalCurrencyID'] = $item->wacValueLocalCurrencyID;
            $input['currentWacRptCurrencyID'] = $item->wacValueReportingCurrencyID;

            $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);
            $input['currenctStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
            $input['systemQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];

            $input['wacAdjRpt'] = $itemCurrentCostAndQty['wacValueReporting'];
            $input['currentWacRpt'] = $itemCurrentCostAndQty['wacValueReporting'];


            $companyCurrencyConversion = Helper::currencyConversion($stockCount->companySystemID,$item->wacValueReportingCurrencyID,$item->wacValueReportingCurrencyID,$itemCurrentCostAndQty['wacValueReporting']);

            $input['currentWaclocal'] = $companyCurrencyConversion['localAmount'];
            $input['wacAdjLocal'] = $companyCurrencyConversion['localAmount'];
            $input['wacAdjRptER'] = $companyCurrencyConversion['trasToRptER'];
            $input['wacAdjLocalER'] = 1;

            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
                                                                        ->where('mainItemCategoryID', $input['itemFinanceCategoryID'])
                                                                        ->where('itemCategorySubID', $input['itemFinanceCategorySubID'])
                                                                        ->first();

            if (!empty($financeItemCategorySubAssigned)) {
                $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
            } else {
                return ['status' => true, 'message' => "Item ".$item->itemDescription." cannot be used, Since, Account code not updated."];
            }

            if (!$input['financeGLcodebBS'] || !$input['financeGLcodebBSSystemID'] || !$input['financeGLcodePL'] || !$input['financeGLcodePLSystemID']) {
                return ['status' => true, 'message' => "Item ".$item->itemDescription." cannot be used, Since, Account code not updated."];
            }

            if ($input['itemFinanceCategoryID'] == 1) {
                $alreadyAdded = StockCount::where('stockCountAutoID', $input['stockCountAutoID'])
                                            ->whereHas('details', function ($query) use ($itemCodeSystem) {
                                                $query->where('itemCodeSystem', $itemCodeSystem);
                                            })
                                            ->first();

                if ($alreadyAdded) {
                    return ['status' => true,  'message' => "Item ".$item->itemDescription." cannot be used, Since, Item is already added."];
                }
            }

            $res = StockCountDetail::create($input);

        }

        return ['status' => true];
    }
}
