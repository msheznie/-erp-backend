<?php
/**
 * =============================================
 * -- File Name : ItemAssignedAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Item Assigned
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Item Assigned
 * -- Date: 6-September 2018 By: Fayas Description: Added new functions named as getAllAssignedItemsByCompany(),
 *
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemAssignedAPIRequest;
use App\Http\Requests\API\UpdateItemAssignedAPIRequest;
use App\Models\ItemAssigned;
use App\Models\Company;
use App\Repositories\ItemAssignedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemAssignedController
 * @package App\Http\Controllers\API
 */
class ItemAssignedAPIController extends AppBaseController
{
    /** @var  ItemAssignedRepository */
    private $itemAssignedRepository;

    public function __construct(ItemAssignedRepository $itemAssignedRepo)
    {
        $this->itemAssignedRepository = $itemAssignedRepo;
    }

    /**
     * Display a listing of the ItemAssigned.
     * GET|HEAD /itemAssigneds
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->itemAssignedRepository->pushCriteria(new RequestCriteria($request));
        $this->itemAssignedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemAssigneds = $this->itemAssignedRepository->all();

        return $this->sendResponse($itemAssigneds->toArray(), 'Item Assigneds retrieved successfully');
    }

    /**
     * Store a newly created ItemAssigned in storage.
     * POST /itemAssigneds
     *
     * @param CreateItemAssignedAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateItemAssignedAPIRequest $request)
    {
        $input = $request->all();

        unset($input['company']);
        unset($input['final_approved_by']);

        $input = $this->convertArrayToValue($input);

        if (array_key_exists("idItemAssigned", $input)) {
            $itemAssigneds = ItemAssigned::where('idItemAssigned', $input['idItemAssigned'])->first();
            $itemAssigneds->isActive = $input['isActive'];

            if ($input['isAssigned'] == 1 || $input['isAssigned'] == true) {
                $input['isAssigned'] = -1;
            }

            $itemAssigneds->isAssigned = $input['isAssigned'];
            $itemAssigneds->save();
        } else {
            $company = Company::where('companySystemID', $input['companySystemID'])->first();
            $input['wacValueReportingCurrencyID'] = $company->reportingCurrency;
            $input['wacValueLocalCurrencyID'] = $company->localCurrencyID;
            $input['companyID'] = $company->CompanyID;
            $input['isActive'] = 1;
            $input['isAssigned'] = -1;
            $input['itemPrimaryCode'] = $input['primaryCode'];
            $input['itemUnitOfMeasure'] = $input['unit'];
            $itemAssigneds = $this->itemAssignedRepository->create($input);
        }

        return $this->sendResponse($itemAssigneds->toArray(), 'Item Assigned saved successfully');
    }

    /**
     * Display the specified ItemAssigned.
     * GET|HEAD /itemAssigneds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ItemAssigned $itemAssigned */
        $itemAssigned = $this->itemAssignedRepository->findWithoutFail($id);

        if (empty($itemAssigned)) {
            return $this->sendError('Item Assigned not found');
        }

        return $this->sendResponse($itemAssigned->toArray(), 'Item Assigned retrieved successfully');
    }

    /**
     * Update the specified ItemAssigned in storage.
     * PUT/PATCH /itemAssigneds/{id}
     *
     * @param  int $id
     * @param UpdateItemAssignedAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateItemAssignedAPIRequest $request)
    {
        $input = array_except($request->all(), ['unit', 'financeMainCategory', 'financeSubCategory', 'local_currency', 'rpt_currency']);

        /** @var ItemAssigned $itemAssigned */
        $itemAssigned = $this->itemAssignedRepository->findWithoutFail($id);

        if (empty($itemAssigned)) {
            return $this->sendError('Item not found');
        }

        $itemAssigned = $this->itemAssignedRepository->update(array_only($input, ['minimumQty', 'maximunQty', 'rolQuantity']), $id);

        return $this->sendResponse($itemAssigned->toArray(), 'Item updated successfully');
    }

    /**
     * Remove the specified ItemAssigned from storage.
     * DELETE /itemAssigneds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ItemAssigned $itemAssigned */
        $itemAssigned = $this->itemAssignedRepository->findWithoutFail($id);

        if (empty($itemAssigned)) {
            return $this->sendError('Item Assigned not found');
        }

        $itemAssigned->delete();

        return $this->sendResponse($id, 'Item Assigned deleted successfully');
    }

    /**
     * Display a listing of the Items by company.
     * POST /getAllAssignedItemsByCompany
     *
     * @param Request $request
     * @return Response
     */

    public function getAllAssignedItemsByCompany(Request $request)
    {

        $input = $request->all();
        $itemMasters = $this->getAssignedItemsByCompanyQry($input);
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $data = \DataTables::eloquent($itemMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('idItemAssigned', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addColumn('current', function ($row) {
                $data = array('companySystemID' => $row->companySystemID,
                    'itemCodeSystem' => $row->itemCodeSystem,
                    'wareHouseId' => null);
                $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);

                $array = array('local' => $itemCurrentCostAndQty['wacValueLocal'],
                    'rpt' => $itemCurrentCostAndQty['wacValueReporting'],
                    'stock' => $itemCurrentCostAndQty['currentStockQty']);
                return $array;

            })
            ->make(true);
        return $data;
        ///return $this->sendResponse($itemMasters->toArray(), 'Item Masters retrieved successfully');*/
    }


    public function exportItemAssignedByCompanyReport(Request $request)
    {
        $input = $request->all();
        $data = array();
        $output = ($this->getAssignedItemsByCompanyQry($input))->orderBy('idItemAssigned', 'DES')->get();
        $output = $this->getCurrentCostAndQty($output);
        $type = $request->type;
        if (!empty($output)) {
            $x = 0;
            foreach ($output as $value) {
                $data[$x]['Item code'] = $value->itemPrimaryCode;
                $data[$x]['Mfg No'] = $value->secondaryItemCode;
                $data[$x]['Item Description'] = $value->itemDescription;

                if ($value->unit) {
                    $data[$x]['Unit'] = $value->unit->UnitShortCode;
                } else {
                    $data[$x]['Unit'] = '';
                }

                if ($value->financeMainCategory) {
                    $data[$x]['Main Category'] = $value->financeMainCategory->categoryDescription;
                } else {
                    $data[$x]['Main Category'] = '';
                }

                if ($value->financeSubCategory) {
                    $data[$x]['Sub Category'] = $value->financeSubCategory->categoryDescription;
                    $data[$x]['Finance BS Code'] = $value->financeSubCategory->financeGLcodebBS;
                    $data[$x]['Finance PL Code'] = $value->financeSubCategory->financeGLcodePL;
                    $data[$x]['Include PL For GRV YN'] = $value->financeSubCategory->includePLForGRVYN;
                } else {
                    $data[$x]['Sub Category'] = '';
                    $data[$x]['Finance BS Code'] = '';
                    $data[$x]['Finance PL Code'] = '';
                    $data[$x]['Include PL For GRV YN'] = '';
                }

                $data[$x]['Min Qty'] = round($value->minimumQty, 2);
                $data[$x]['MAx Qty'] = round($value->maximunQty, 2);
                $data[$x]['Total Qty'] = round($value->totalQty, 2);
                $localDecimal = 3;
                $rptDecimal = 2;
                if ($value->local_currency) {
                    $localDecimal = $value->local_currency->DecimalPlaces;
                }
                if ($value->rpt_currency) {
                    $rptDecimal = $value->rpt_currency->DecimalPlaces;
                }

                $data[$x]['WAC Value Local'] = round($value->wacValueLocal, $localDecimal);
                $data[$x]['WAC Value Rpt'] = round($value->wacValueReporting, $rptDecimal);
                $data[$x]['Category'] = $value->itemMovementCategory;
                $status = "Not Active";
                if ($value->isActive == 1) {
                    $status = "Active Only";
                }

                $data[$x]['Status'] = $status;
                $x++;
            }
        }

        $csv = \Excel::create('items_by_company', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');
    }

    public function getCurrentCostAndQty($array)
    {
        foreach ($array as $item) {
            $data = array('companySystemID' => $item->companySystemID,
                'itemCodeSystem' => $item->itemCodeSystem,
                'wareHouseId' => null);
            $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);
            $item->totalQty = $itemCurrentCostAndQty['currentStockQty'];
            $item->wacValueLocal = $itemCurrentCostAndQty['wacValueLocal'];
            $item->wacValueReporting = $itemCurrentCostAndQty['wacValueReporting'];
        }

        return $array;
    }

    public function getAssignedItemsByCompanyQry($request)
    {
        $input = $request;
        $input = $this->convertArrayToSelectedValue($input, array('financeCategoryMaster', 'financeCategorySub', 'isActive'));

        $companyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $itemMasters = ItemAssigned::with(['unit', 'financeMainCategory', 'financeSubCategory', 'local_currency', 'rpt_currency'])
            ->whereIn('companySystemID', $childCompanies);

        if (array_key_exists('financeCategoryMaster', $input)) {
            if ($input['financeCategoryMaster'] > 0 && !is_null($input['financeCategoryMaster'])) {
                $itemMasters->where('financeCategoryMaster', $input['financeCategoryMaster']);
            }
        }

        if (array_key_exists('financeCategorySub', $input)) {
            if ($input['financeCategorySub'] > 0 && !is_null($input['financeCategorySub'])) {
                $itemMasters->where('financeCategorySub', $input['financeCategorySub']);
            }
        }

        if (array_key_exists('isActive', $input)) {
            if (($input['isActive'] == 0 || $input['isActive'] == 1) && !is_null($input['isActive'])) {
                $itemMasters->where('isActive', $input['isActive']);
            }
        }
        if (array_key_exists('itemApprovedYN', $input)) {
            if (($input['itemApprovedYN'] == 0 || $input['itemApprovedYN'] == 1) && !is_null($input['itemApprovedYN'])) {
                $itemMasters->where('itemApprovedYN', $input['itemApprovedYN']);
            }
        }

        $search = $input['search']['value'];
        if ($search) {
            $itemMasters = $itemMasters->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }
        return $itemMasters;
    }

}
