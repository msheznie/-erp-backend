<?php
/**
 * =============================================
 * -- File Name : WarehouseItemsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Warehouse Items
 * -- Author : Mohamed Fayas
 * -- Create date : 07 - September 2018
 * -- Description : This file contains the all CRUD for Warehouse Items
 * -- REVISION HISTORY
 * -- Date: 07-September 2018 By: Fayas Description: Added new functions named as getAllAssignedItemsByWarehouse()
 * -- Date: 28- April 2019 By: Fayas Description: Added new functions named as exportItemAssignedByWarehouse()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWarehouseItemsAPIRequest;
use App\Http\Requests\API\UpdateWarehouseItemsAPIRequest;
use App\Models\WarehouseItems;
use App\Models\WarehouseMaster;
use App\Repositories\WarehouseItemsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\WarehouseBinLocation;
use App\Models\ItemBatch;
use App\Models\ItemMaster;
use App\Models\ItemSerial;
use App\Models\ErpItemLedger;

/**
 * Class WarehouseItemsController
 * @package App\Http\Controllers\API
 */
class WarehouseItemsAPIController extends AppBaseController
{
    /** @var  WarehouseItemsRepository */
    private $warehouseItemsRepository;

    public function __construct(WarehouseItemsRepository $warehouseItemsRepo)
    {
        $this->warehouseItemsRepository = $warehouseItemsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/warehouseItems",
     *      summary="Get a listing of the WarehouseItems.",
     *      tags={"WarehouseItems"},
     *      description="Get all WarehouseItems",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/WarehouseItems")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->warehouseItemsRepository->pushCriteria(new RequestCriteria($request));
        $this->warehouseItemsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $warehouseItems = $this->warehouseItemsRepository->all();

        return $this->sendResponse($warehouseItems->toArray(), 'Warehouse Items retrieved successfully');
    }

    /**
     * @param CreateWarehouseItemsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/warehouseItems",
     *      summary="Store a newly created WarehouseItems in storage",
     *      tags={"WarehouseItems"},
     *      description="Store WarehouseItems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="WarehouseItems that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/WarehouseItems")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/WarehouseItems"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateWarehouseItemsAPIRequest $request)
    {
        $input = $request->all();

        $warehouseItems = $this->warehouseItemsRepository->create($input);

        return $this->sendResponse($warehouseItems->toArray(), 'Warehouse Items saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/warehouseItems/{id}",
     *      summary="Display the specified WarehouseItems",
     *      tags={"WarehouseItems"},
     *      description="Get WarehouseItems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseItems",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/WarehouseItems"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var WarehouseItems $warehouseItems */
        $warehouseItems = $this->warehouseItemsRepository->findWithoutFail($id);

        if (empty($warehouseItems)) {
            return $this->sendError('Warehouse Items not found');
        }

        return $this->sendResponse($warehouseItems->toArray(), 'Warehouse Items retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateWarehouseItemsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/warehouseItems/{id}",
     *      summary="Update the specified WarehouseItems in storage",
     *      tags={"WarehouseItems"},
     *      description="Update WarehouseItems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseItems",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="WarehouseItems that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/WarehouseItems")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/WarehouseItems"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateWarehouseItemsAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, ['binNumber']);
        /** @var WarehouseItems $warehouseItems */
        $warehouseItems = $this->warehouseItemsRepository->findWithoutFail($id);

        if (empty($warehouseItems)) {
            return $this->sendError('Warehouse Items not found');
        }

        if (!isset($input['binNumber'])) {
            $input['binNumber'] = 0;
        }


        if(isset($input['binLocation']) && !empty($input['binLocation']))
        {
            if (!empty($input['binLocation']['IDS']) && is_array($input['binLocation']['IDS'])) {
                $productIDs = array_filter($input['binLocation']['IDS'], 'is_numeric');
                $itemMaster = ItemMaster::find($warehouseItems->itemSystemCode);
                if (!empty($productIDs) && $itemMaster) {
                    $model = $itemMaster->trackingType == 1 ? ItemBatch::class : ItemSerial::class;
                    $model::whereIn('id', $productIDs)->update(['binLocation' => $input['binNumber']]);
                }
            }
        }
        else
        {
            $warehouseItems = $this->warehouseItemsRepository->update(array_only($input, ['binNumber']), $id);
        }
        return $this->sendResponse($warehouseItems->toArray(), 'WarehouseItems updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/warehouseItems/{id}",
     *      summary="Remove the specified WarehouseItems from storage",
     *      tags={"WarehouseItems"},
     *      description="Delete WarehouseItems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseItems",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var WarehouseItems $warehouseItems */
        $warehouseItems = $this->warehouseItemsRepository->findWithoutFail($id);

        if (empty($warehouseItems)) {
            return $this->sendError('Warehouse Items not found');
        }

        $warehouseItems->delete();

        return $this->sendResponse($id, 'Warehouse Items deleted successfully');
    }

    /**
     * Display a listing of the Items by warehouse.
     * POST /getAllAssignedItemsByWarehouse
     *
     * @param Request $request
     * @return Response
     */

    public function getAllAssignedItemsByWarehouse(Request $request)
    {

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        if(isset($request['financeCategorySubLocation']) && !empty($request['financeCategorySubLocation']))
        {
            $input['financeCategorySubLocation'] = collect($request['financeCategorySubLocation'])->pluck('id');   
        }

        if(isset($request['itemSystemCode']) && !empty($request['itemSystemCode']))
        {
            $input['itemSystemCode'] = collect($request['itemSystemCode'])->pluck('id');   
        }

        $itemMasters = ($this->getAssignedItemsByWareHouse($input));

        $itemMasters = collect($itemMasters);

        $direction = $input['order'][0]['dir'] ?? 'asc';
        $itemMasters = $itemMasters->sortBy('warehouseItemsID', SORT_REGULAR, $direction === 'desc');

        $data = \DataTables::collection($itemMasters)
        ->addIndexColumn()
        ->with('orderCondition', $sort)
        ->filter(function ($instance) use ($input) {  
            $search = $input['search']['value'] ?? null;
            if (!empty($search)) {
                $instance->collection = $instance->collection->filter(function ($item) use ($search) {
                    return stripos($item['itemPrimaryCode'] ?? '', $search) !== false ||
                    stripos($item['itemDescription'] ?? '', $search) !== false ||
                    stripos($item['warehouse_by']['warehouseDescription'] ?? '', $search) !== false ||
                    stripos($item['binLocation']['binLocationDes'] ?? '', $search) !== false ||
                    stripos($item['financeSubCategory']['categoryDescription'] ?? '', $search) !== false;
                });
            }
        })
        ->addColumn('Actions', function ($row) {
            return '<button class="btn btn-sm btn-primary">Edit</button>';
        })
        ->rawColumns(['Actions'])
        ->make(true);

        return $data;
    }


    public function exportItemAssignedByWarehouse(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        if(isset($request['financeCategorySubLocation']) && !empty($request['financeCategorySubLocation']))
        {
            $input['financeCategorySubLocation'] = collect($request['financeCategorySubLocation'])->pluck('id');   
        }

        if(isset($request['itemSystemCode']) && !empty($request['itemSystemCode']))
        {
            $input['itemSystemCode'] = collect($request['itemSystemCode'])->pluck('id');   
        }
        
        $data = array();
        $output = ($this->getAssignedItemsByWareHouse($input));
        $output = collect($output)->map(function ($item) {
            return (object) $item;
        });
        
        $output = $output->sortBy('warehouseItemsID', SORT_REGULAR, $sort)->values();

        $type = $request->type;
        if (!empty($output)) {
            $x = 0;
            foreach ($output as $value) {

                $data[$x]['Item code'] = $value->itemPrimaryCode;
                $data[$x]['Item Description'] = $value->itemDescription;

                if ($value->unit) {
                    $data[$x]['Unit'] = $value->unit['UnitShortCode'];
                } else {
                    $data[$x]['Unit'] = '-';
                }

                if ($value->finance_sub_category) {
                    $data[$x]['Category'] = $value->finance_sub_category['categoryDescription'];
                } else {
                    $data[$x]['Category'] = '-';
                }

                $data[$x]['warehouse'] =  $value->warehouse_by ? $value->warehouse_by['wareHouseDescription'] : '-';
                $bin = WarehouseBinLocation::find($value->binNumber);
                $data[$x]['Bin Location'] = $value->isTrack == 1? $value->binLocation['binLocationDes'] : $bin ? $bin->binLocationDes : '-';
              
                $data[$x]['Min Qty'] = number_format($value->minimumQty, 2);
                $data[$x]['Max Qty'] = number_format($value->maximunQty, 2);

                $localDecimal = 3;
                $rptDecimal = 2;
                if ($value->local_currency) {
                    $localDecimal = $value->local_currency['DecimalPlaces'];
                }
                if ($value->rpt_currency) {
                    $rptDecimal = $value->rpt_currency['DecimalPlaces'];
                }

                $data1 = array('companySystemID' => $value->companySystemID,
                    'itemCodeSystem' => $value->itemSystemCode,
                    'wareHouseId' => $value->warehouseSystemCode);
                 $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data1);                

                 $data[$x]['Stock Qty'] = $value->isTrack == 1? number_format($value->binLocation['quantity'],2) :number_format($value->current['wareHouseStock'],2);
                 $data[$x]['WAC Local'] = number_format($itemCurrentCostAndQty['wacValueLocalWarehouse'],$localDecimal);
                 $data[$x]['WAC Rpt'] = number_format($itemCurrentCostAndQty['wacValueReportingWarehouse'],$rptDecimal);
                 $data[$x]['WAC Local Val'] = $value->isTrack == 1? number_format($value->binLocation['totalWacCostLocal'],$localDecimal) :number_format($value->current['totalWacCostLocal'],$localDecimal);
                 $data[$x]['WAC Rpt Val'] = $value->isTrack == 1? number_format($value->binLocation['totalWacCostRpt'],$rptDecimal) :number_format($value->current['totalWacCostRpt'],$rptDecimal);
                 $x++;
            }
        }


         \Excel::create('items_by_warehouse', function ($excel) use ($data) {
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

    public function getAssignedItemsByWareHouse($input)
    {
        $input = $this->convertArrayToSelectedValue($input,
            array('financeCategoryMaster', 'financeCategorySubLocation', 'isActive'));
        $childCompanies = [];
        $companyIds = $input['companyId'];
        $warehouseSystemCode = is_array($input['warehouseSystemCode'])
            ? $input['warehouseSystemCode'] : [$input['warehouseSystemCode']];

        $type = isset($input['categoryTypeValue'])
            ? (is_array($input['categoryTypeValue'])
                ? array_map('strval', $input['categoryTypeValue'])
                : explode(',', $input['categoryTypeValue']))
            : [];

        $warehouse = WarehouseMaster::whereIn('warehouseSystemCode', $warehouseSystemCode)->get();

        if(!empty($warehouse)){
            $companyIds = $warehouse->pluck('companySystemID')->unique()->toArray();
        }

        foreach ($companyIds as $companyId) {
            if (\Helper::checkIsCompanyGroup($companyId)) {
                $childCompanies = array_merge($childCompanies, \Helper::getGroupCompany($companyId));
            } else {
                $childCompanies[] = $companyId;
            }
        }

        $childCompanies = array_unique($childCompanies);

        $itemMasters = WarehouseItems::with(['warehouse_by', 'binLocation', 'unit', 'item_by', 'financeSubCategory', 'local_currency', 'rpt_currency'])
            ->whereIn('companySystemID', $childCompanies)
            ->whereIn('warehouseSystemCode', $warehouseSystemCode)
            ->where('financeCategoryMaster', 1) 
            ->when(!empty($type) && is_array($type), function ($query) use ($type) {
                $query->whereHas('item_by', function ($query) use ($type) {
                    $query->whereHas('item_category_type', function ($subQuery) use ($type) {
                        $subQuery->whereIn('categoryTypeID', $type);
                    });
                });
            });

        if (array_key_exists('financeCategorySubLocation', $input)) {
            if (isset($input['financeCategorySubLocation'])  && !empty($input['financeCategorySubLocation'])) {
                $itemMasters->whereIn('financeCategorySub',$input['financeCategorySubLocation']);
            }
        }

        if (array_key_exists('itemApprovedYN', $input)) {
            if (($input['itemApprovedYN'] == 0 || $input['itemApprovedYN'] == 1) && !is_null($input['itemApprovedYN'])) {
                $itemMasters->where('itemApprovedYN', $input['itemApprovedYN']);
            }
        }

        if (array_key_exists('itemSystemCode', $input)) {
            if (isset($input['itemSystemCode'])  && !empty($input['itemSystemCode'])) {
                $itemMasters->whereIn('itemSystemCode',$input['itemSystemCode']);
            }
        }

        $details = $itemMasters->get();
        foreach($details as &$row)
        {
            $data = array('companySystemID' => $row->companySystemID,
            'itemCodeSystem' => $row->itemSystemCode,
            'wareHouseId' => $row->warehouseSystemCode,
            'itemReport' => true);
            $itemBinLocation = \Inventory::itemCurrentCostAndQty($data);
            
            $row['binLocation'] =$itemBinLocation['binLocation'] ?? [];
            $row['isTrack'] =$itemBinLocation['isTrackable'] ?? [];

            $array = array('local' => $itemBinLocation['wacValueLocalWarehouse'],
            'rpt' => $itemBinLocation['wacValueReportingWarehouse'],
            'wareHouseStock' => $itemBinLocation['currentWareHouseStockQty'],
            'totalWacCostLocal' => $itemBinLocation['totalWacCostLocalWarehouse'],
            'totalWacCostRpt' => $itemBinLocation['totalWacCostRptWarehouse'],
             );
            $row['current'] = $array;
        }

        $transformedData = [];
        $x = 1;
        $details = $details->toArray(); 
        $transformedData = array_reduce($details, function ($carry, $item) use(&$x) {
            if (($item['isTrack'] == "1" || $item['isTrack'] == "2") && !empty($item['binLocation'])) {
                foreach ($item['binLocation'] as $bin) {
                    $carry[] = array_merge($item, [
                        'binLocation' => $bin,
                        'binNumber' => $bin['binLocationID'],
                        'order' => $x
                    ]);
                    $x++;
                }
            } else {
                $carry[] = array_merge($item, ['binLocation' => null,'order' => $x]);
                $x++;
            }
            return $carry;
        }, []);

        if(isset($input['binNumber']) && !empty($input['binNumber'])) {
            $transformedData = ($this->filterBinLocation($transformedData,$input));

        } 

        return $transformedData;

    }

    public function filterBinLocation($data,$request)
    {
        $binumberValues = collect($request['binNumber'])->pluck('id')->toArray();;   

        $filteredData = array_filter($data, function ($item) use ($binumberValues) {
            return in_array($item['binNumber'], $binumberValues);
        });

        $data = array_values($filteredData); 
        return $data;
    }
}
