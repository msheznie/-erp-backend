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

        if(isset($input['binLocation']) && !empty($input['binLocation']) && ($input['binLocation']['binLocationID'] == null))
        {
            if (!empty($input['binLocation']['IDS']) && is_array($input['binLocation']['IDS'])) {
                $productIDs = array_filter($input['binLocation']['IDS'], 'is_numeric');
                if (!empty($productIDs)) {
                    ItemBatch::whereIn('id', $productIDs)->update(['binLocation' => $input['binNumber']]);
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

        if(isset($request['financeCategorySub']) && !empty($request['financeCategorySub']) && !is_null($input['financeCategorySub']))
        {
            $input['financeCategorySub'] = collect($request['financeCategorySub'])->pluck('id');   
        }

        if(isset($request['itemSystemCode']) && !empty($request['itemSystemCode']) && !is_null($input['itemSystemCode']))
        {
            $input['itemSystemCode'] = collect($request['itemSystemCode'])->pluck('id');   
        }

        $itemMasters = $this->getAssignedItemsByWareHouse($input);

        $data = \DataTables::eloquent($itemMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('warehouseItemsID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addColumn('current', function ($row) {
                $data = array('companySystemID' => $row->companySystemID,
                    'itemCodeSystem' => $row->itemSystemCode,
                    'wareHouseId' => $row->warehouseSystemCode);
                $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);

                $array = array('local' => $itemCurrentCostAndQty['wacValueLocalWarehouse'],
                    'rpt' => $itemCurrentCostAndQty['wacValueReportingWarehouse'],
                    'wareHouseStock' => $itemCurrentCostAndQty['currentWareHouseStockQty'],
                    'totalWacCostLocal' => $itemCurrentCostAndQty['totalWacCostLocalWarehouse'],
                    'totalWacCostRpt' => $itemCurrentCostAndQty['totalWacCostRptWarehouse'],
                );
                return $array;

            })
            ->addColumn('binLocation', function ($row) {
                $data = array('companySystemID' => $row->companySystemID,
                    'itemCodeSystem' => $row->itemSystemCode,
                    'wareHouseId' => $row->warehouseSystemCode,
                    'itemReport' => true);
                $itemBinLocation = \Inventory::itemCurrentCostAndQty($data);

                $array = $itemBinLocation['binLocation'];
                return $array;

            })
            ->addColumn('isTrack', function ($row) {
                $data = array('companySystemID' => $row->companySystemID,
                    'itemCodeSystem' => $row->itemSystemCode,
                    'wareHouseId' => $row->warehouseSystemCode,
                    'itemReport' => true);
                $itemBinLocation = \Inventory::itemCurrentCostAndQty($data);

                $array = $itemBinLocation['isTrackable'];
                return $array;

            })
            ->make(true);
            
            $responseData = json_decode($data->getContent(), true);
            $transformedData = [];
            $x = 1;
            $transformedData = array_reduce($responseData['data'], function ($carry, $item) use(&$x) {
                if ($item['isTrack'] == "1" && !empty($item['binLocation'])) {
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

            if(isset($request['binNumber']) && !empty($request['binNumber'])) {
                $transformedData = ($this->filterBinLocation($transformedData,$request));
            } 
        
            
            $responseData['data'] = $transformedData;
            return response()->json($responseData);
    }


    public function exportItemAssignedByWarehouse(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $data = array();
        $output = ($this->getAssignedItemsByWareHouse($input))->orderBy('warehouseItemsID', $sort)->get();

        if(isset($request['binNumber']) && !empty($request['binNumber'])) {
            $binumberValues = collect($request['binNumber'])->pluck('id')->toArray();;   

        } 

        $type = $request->type;
        if (!empty($output)) {
            $x = 0;
            foreach ($output as $value) {


                $localDecimal = 3;
                $rptDecimal = 2;
                if ($value->local_currency) {
                    $localDecimal = $value->local_currency->DecimalPlaces;
                }
                if ($value->rpt_currency) {
                    $rptDecimal = $value->rpt_currency->DecimalPlaces;
                }

                $data1 = array('companySystemID' => $value->companySystemID,
                    'itemCodeSystem' => $value->itemSystemCode,
                    'wareHouseId' => $value->warehouseSystemCode);
                 $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data1);


                $data2 = array('companySystemID' => $value->companySystemID,
                'itemCodeSystem' => $value->itemSystemCode,
                'wareHouseId' => $value->warehouseSystemCode,
                'itemReport' => true);
                     $itemBinLocation = \Inventory::itemCurrentCostAndQty($data2);
                
                
                     if (!empty($itemBinLocation['isTrackable']) && $itemBinLocation['isTrackable'] == "1" && !empty($itemBinLocation['binLocation'])) {
                        foreach ($itemBinLocation['binLocation'] as $bin) {
                            $data[$x] = [
                                'Item code' => $value->itemPrimaryCode,
                                'Item Description' => $value->itemDescription,
                                'Unit' => $value->unit ? $value->unit->UnitShortCode : '-',
                                'Category' => $value->financeSubCategory ? $value->financeSubCategory->categoryDescription : '-',
                                'Bin Location' => $bin['binLocationDes'],
                                'Min Qty' => number_format($value->minimumQty, 2),
                                'Max Qty' => number_format($value->maximunQty, 2),
                                'Stock Qty' => number_format($itemCurrentCostAndQty['currentWareHouseStockQty'], 2),
                                'WAC Local' => number_format($itemCurrentCostAndQty['wacValueLocalWarehouse'], $localDecimal),
                                'WAC Rpt' => number_format($itemCurrentCostAndQty['wacValueReportingWarehouse'], $rptDecimal),
                                'WAC Local Val' => number_format($itemCurrentCostAndQty['totalWacCostLocalWarehouse'], $localDecimal),
                                'WAC Rpt Val' => number_format($itemCurrentCostAndQty['totalWacCostRptWarehouse'], $rptDecimal),
                                'binNumber' => $bin['binLocationID'],
                            ];

                            $x++; 
                        }
                    } else {
                         $bin = WarehouseBinLocation::find($value->binNumber);
                        
                        $data[$x] = [
                            'Item code' => $value->itemPrimaryCode,
                            'Item Description' => $value->itemDescription,
                            'Unit' => $value->unit ? $value->unit->UnitShortCode : '-',
                            'Category' => $value->financeSubCategory ? $value->financeSubCategory->categoryDescription : '-',
                            'Bin Location' =>  $bin ? $bin->binLocationDes : '-',
                            'Min Qty' => number_format($value->minimumQty, 2),
                            'Max Qty' => number_format($value->maximunQty, 2),
                            'Stock Qty' => number_format($itemCurrentCostAndQty['currentWareHouseStockQty'], 2),
                            'WAC Local' => number_format($itemCurrentCostAndQty['wacValueLocalWarehouse'], $localDecimal),
                            'WAC Rpt' => number_format($itemCurrentCostAndQty['wacValueReportingWarehouse'], $rptDecimal),
                            'WAC Local Val' => number_format($itemCurrentCostAndQty['totalWacCostLocalWarehouse'], $localDecimal),
                            'WAC Rpt Val' => number_format($itemCurrentCostAndQty['totalWacCostRptWarehouse'], $rptDecimal),
                            'binNumber' => $bin ? $bin->binLocationID : '-',
                        ];

                        $x++; 
                    }

            }
        }

        if(isset($request['binNumber']) && !empty($request['binNumber'])) {
            $data = ($this->filterBinLocation($data,$request));

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

        $input = $this->convertArrayToSelectedValue($input, array('financeCategoryMaster', 'financeCategorySub', 'isActive'));
        $companyId = $input['companyId'];
        $warehouseSystemCode = isset($input['warehouseSystemCode']) ? $input['warehouseSystemCode'] : 0;

        $warehouse           =  WarehouseMaster::find($warehouseSystemCode);

        if(!empty($warehouse)){
            $companyId = $warehouse->companySystemID;
        }

        $isGroup = \Helper::checkIsCompanyGroup($companyId);


        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $itemMasters = WarehouseItems::with(['warehouse_by', 'binLocation', 'unit', 'financeMainCategory', 'financeSubCategory', 'local_currency', 'rpt_currency'])
            ->whereIn('companySystemID', $childCompanies)
            ->where('warehouseSystemCode', $input['warehouseSystemCode'])
            ->where('financeCategoryMaster', 1);

        if (array_key_exists('financeCategoryMaster', $input)) {
            if ($input['financeCategoryMaster'] > 0 && !is_null($input['financeCategoryMaster'])) {
                $itemMasters->where('financeCategoryMaster', $input['financeCategoryMaster']);
            }
        }

        if (array_key_exists('financeCategorySub', $input)) {
            if ($input['financeCategorySub']  && !is_null($input['financeCategorySub']) && !empty($input['financeCategorySub'])) {
                $itemMasters->whereIn('financeCategorySub',$input['financeCategorySub']);
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

        if (array_key_exists('itemSystemCode', $input)) {
            if ($input['itemSystemCode']  && !is_null($input['itemSystemCode']) && !empty($input['itemSystemCode'])) {
                $itemMasters->whereIn('itemSystemCode',$input['itemSystemCode']);
            }
        }
        

        $search = $input['search']['value'];
        if ($search) {
            $itemMasters = $itemMasters->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }


        return $itemMasters;

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
