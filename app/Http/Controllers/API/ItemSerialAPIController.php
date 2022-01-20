<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemSerialAPIRequest;
use App\Http\Requests\API\UpdateItemSerialAPIRequest;
use App\Models\ItemSerial;
use App\Models\DocumentSubProduct;
use App\Repositories\ItemSerialRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class ItemSerialController
 * @package App\Http\Controllers\API
 */

class ItemSerialAPIController extends AppBaseController
{
    /** @var  ItemSerialRepository */
    private $itemSerialRepository;

    public function __construct(ItemSerialRepository $itemSerialRepo)
    {
        $this->itemSerialRepository = $itemSerialRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemSerials",
     *      summary="Get a listing of the ItemSerials.",
     *      tags={"ItemSerial"},
     *      description="Get all ItemSerials",
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
     *                  @SWG\Items(ref="#/definitions/ItemSerial")
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
        $this->itemSerialRepository->pushCriteria(new RequestCriteria($request));
        $this->itemSerialRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemSerials = $this->itemSerialRepository->all();

        return $this->sendResponse($itemSerials->toArray(), 'Item Serials retrieved successfully');
    }

    /**
     * @param CreateItemSerialAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemSerials",
     *      summary="Store a newly created ItemSerial in storage",
     *      tags={"ItemSerial"},
     *      description="Store ItemSerial",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemSerial that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemSerial")
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
     *                  ref="#/definitions/ItemSerial"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemSerialAPIRequest $request)
    {
        $input = $request->all();

        $itemSerial = $this->itemSerialRepository->create($input);

        return $this->sendResponse($itemSerial->toArray(), 'Item Serial saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemSerials/{id}",
     *      summary="Display the specified ItemSerial",
     *      tags={"ItemSerial"},
     *      description="Get ItemSerial",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemSerial",
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
     *                  ref="#/definitions/ItemSerial"
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
        /** @var ItemSerial $itemSerial */
        $itemSerial = $this->itemSerialRepository->findWithoutFail($id);

        if (empty($itemSerial)) {
            return $this->sendError('Item Serial not found');
        }

        return $this->sendResponse($itemSerial->toArray(), 'Item Serial retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateItemSerialAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemSerials/{id}",
     *      summary="Update the specified ItemSerial in storage",
     *      tags={"ItemSerial"},
     *      description="Update ItemSerial",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemSerial",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemSerial that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemSerial")
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
     *                  ref="#/definitions/ItemSerial"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemSerialAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        
        $checkSerialCode = ItemSerial::where('id', '!=', $input['id'])
                                     ->where('serialCode', $input['serialCode'])
                                     ->where('itemSystemCode', $input['itemSystemCode'])
                                     ->first();

        if ($checkSerialCode) {
            return $this->sendError('Serial code cannot be duplicate');
        }

        if (isset($input['serialCode']) && strlen($input['serialCode']) > 50) {
            return $this->sendError('Serial code length cannot greater than 50');
        }

        if (!preg_match('/^[a-zA-Z0-9\-\/]*$/', $input['serialCode'])) {
            return $this->sendError('Serial code can contain only / and - in special character');
        }

        if (!is_null($input['expireDate'])) {
            $input['expireDate'] = new Carbon($input['expireDate']);
        }

        /** @var ItemSerial $itemSerial */
        $itemSerial = $this->itemSerialRepository->findWithoutFail($id);

        if (empty($itemSerial)) {
            return $this->sendError('Item Serial not found');
        }

        $itemSerial = $this->itemSerialRepository->update($input, $id);

        return $this->sendResponse($itemSerial->toArray(), 'ItemSerial updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemSerials/{id}",
     *      summary="Remove the specified ItemSerial from storage",
     *      tags={"ItemSerial"},
     *      description="Delete ItemSerial",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemSerial",
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
        /** @var ItemSerial $itemSerial */
        $itemSerial = $this->itemSerialRepository->findWithoutFail($id);

        if (empty($itemSerial)) {
            return $this->sendError('Item Serial not found');
        }

        if ($itemSerial->soldFlag) {
            return $this->sendError('Item Serial cannot be deleted. It has been sold');
        }

        $delteSubProduct = DocumentSubProduct::where('productSerialID', $itemSerial->id)
                                             ->whereNull('productInID')
                                             ->delete();

        $itemSerial->delete();

        return $this->sendResponse([], 'Item Serial deleted successfully');
    }

    public function generateItemSerialNumbers(Request $request)
    {
        $input = $request->all();

        if (!isset($input['noQty'])) {
            return $this->sendError("No of quantity is required", 500);
        }

        if (isset($input['noQty']) && $input['noQty'] == 0) {
            return $this->sendError("No of quantity should be greater than zero", 500);
        }


        $iterate = 0;
        $startSN = floatval($input['first_sn']);
        $duplicateSerials = [];

        $subProducts = DocumentSubProduct::where('documentDetailID', $input['documentDetailID'])
                                         ->where('documentSystemID', $input['documentSystemID'])
                                         ->count();

        $noOfQty = $input['noQty'] - $subProducts;

        DB::beginTransaction();
        try {
            while ($iterate < $noOfQty) {
                $productSerial = isset($input['prefix']) ? $input['prefix'].$startSN : $startSN;
                $startSN++;

                if (strlen($productSerial) > 50) {
                    return $this->sendError("Serial length cannot be greater than 50", 500);
                }

                $checkProductSerialDuplication = ItemSerial::where('itemSystemCode', $input['itemID'])
                                                           ->where('serialCode', $productSerial)
                                                           ->first();

                if ($checkProductSerialDuplication) {
                    $duplicateSerials[] = $productSerial;
                }

                if (!preg_match('/^[a-zA-Z0-9\-\/]*$/', $productSerial)) {
                    return $this->sendError('Serial code can contain only / and - in special character');
                }

                $saveData = [
                    'itemSystemCode' => $input['itemID'],
                    'wareHouseSystemID' => $input['wareHouseSystemCode'],
                    'serialCode' => $productSerial,
                ];

                $res = ItemSerial::create($saveData);

                if ($res) {
                    $this->itemSerialRepository->mapSubProducts($res->id, $input['documentSystemID'], $input['documentDetailID']);
                }

                $iterate++;
            }

            if (count($duplicateSerials) > 0) {
                $str = implode (", ", $duplicateSerials);
                DB::rollBack();
                return $this->sendError("Serial Number(s) ".$str. " are already exists for this product", 500);      
            }

            DB::commit();
            return $this->sendResponse([], 'product serial generated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage(), 422);
        }

    }

    public function getGeneratedSerialNumbers(Request $request)
    {
        $input = $request->all();

        $subProducts = DocumentSubProduct::where('documentDetailID', $input['documentDetailID'])
                                         ->where('documentSystemID', $input['documentSystemID'])
                                         ->with(['serial_data'])
                                         ->get();

        return $this->sendResponse($subProducts, 'product serial retrieved successfully');
    } 

    public function serialItemDeleteAllDetails(Request $request)
    {
        $input = $request->all();

        $subProducts = DocumentSubProduct::where('documentDetailID', $input['documentDetailID'])
                                         ->where('documentSystemID', $input['documentSystemID'])
                                         ->get();

        $serialIds = collect($subProducts)->pluck('productSerialID')->toArray();

        $checkItemSerialForDelete = ItemSerial::whereIn('id', $serialIds)
                                              ->where('soldFlag', 1)
                                              ->first();

        if ($checkItemSerialForDelete) {
             return $this->sendError("There are some items are already sold, therefore cannot delete ", 500);    
        }

        $deleteRes = ItemSerial::whereIn('id', $serialIds)
                                              ->delete();

        $deleteRes = DocumentSubProduct::where('documentDetailID', $input['documentDetailID'])
                                         ->where('documentSystemID', $input['documentSystemID'])
                                         ->delete();


        return $this->sendResponse([], 'product serial deleted successfully');
    }

    public function getSerialNumbersForOut(Request $request)
    {
        $input = $request->all();

        $itemSerials = ItemSerial::where('itemSystemCode', $input['itemSystemCode'])
                                 ->when($input['documentSystemID'] != 13, function($query) use ($input){
                                    $query->where('wareHouseSystemID',$input['warehouse']);
                                 })
                                 ->when($input['documentSystemID'] == 13, function($query) use ($input){
                                    $query->where(function($query) use ($input) {
                                        $query->where('wareHouseSystemID',$input['warehouse'])
                                          ->orWhere('wareHouseSystemID',$input['wareHouseCodeTo']);    
                                      });                                
                                 })
                                 ->where(function($query) use ($input){
                                        $query->where(function($query) use ($input) {
                                                $query->where('soldFlag', 1)
                                                      ->whereHas('document_product', function($query) use ($input) {
                                                            $query->where('documentSystemID', $input['documentSystemID'])
                                                                  ->where('documentDetailID', $input['documentDetailID']);
                                                      });
                                            })->orWhere(function($query) use ($input) {
                                                $query->where('soldFlag', 0)
                                                      ->whereDoesntHave('document_product', function($query) use ($input) {
                                                            $query->where('documentSystemID', $input['documentSystemID'])
                                                                  ->where('documentDetailID', $input['documentDetailID']);
                                                      });
                                            })->orWhere(function($query) use ($input) {
                                                $query->when($input['documentSystemID'] == 13, function($query) use ($input){
                                                    $query->where('soldFlag', 0)
                                                      ->whereHas('document_product', function($query) use ($input) {
                                                            $query->where('documentSystemID', $input['documentSystemID'])
                                                                  ->where('documentDetailID', $input['documentDetailID']);
                                                      }); 
                                                });
                                            })->orWhere(function($query) use ($input) {
                                                $query->when(in_array($input['documentSystemID'], [71, 20]), function($query) use ($input){
                                                    $query->where('soldFlag', 0)
                                                      ->whereHas('document_product', function($query) use ($input) {
                                                            $query->where('documentSystemID', $input['documentSystemID'])
                                                                  ->where('documentDetailID', $input['documentDetailID']);
                                                      }); 
                                                });
                                            });
                                  })
                                  ->with(['document_product' => function($query) use ($input) {
                                        $query->where('documentSystemID', $input['documentSystemID'])
                                              ->where('documentDetailID', $input['documentDetailID']);
                                  }, 'warehouse', 'bin_location'])
                                  ->whereHas('document_in_product', function($query) use ($input) { 
                                        $query->where(function($query) use ($input) {
                                            $query->whereHas('grv_master', function($query) use ($input) {
                                                    $query->where('approved', -1);
                                                })->orWhereHas('material_issue', function($query) use ($input) {
                                                    $query->where('approved', -1);
                                                })->orWhereHas('material_return', function($query) use ($input) {
                                                    $query->where('approved', -1);
                                                })->orWhereHas('purchase_return', function($query) use ($input) {
                                                    $query->where('approved', -1);
                                                })->orWhereHas('delivery_order', function($query) {
                                                    $query->where('approvedYN', -1);
                                                })->orWhereHas('sales_return', function($query) {
                                                    $query->where('approvedYN', -1);
                                                })->orWhereHas('customer_invoice', function($query) {
                                                    $query->where('approved', -1);
                                                });
                                        });
                                    })
                                  ->get();

        return $this->sendResponse($itemSerials, 'product serial retrived successfully');
    }

    public function getSerialNumbersForReturn(Request $request)
    {
        $input = $request->all();

        $itemSerials = ItemSerial::where('itemSystemCode', $input['itemSystemCode'])
                                 ->where('wareHouseSystemID',$input['wareHouseSystemCode'])
                                  ->where(function($query) use ($input){
                                        $query->where(function($query) use ($input) {
                                                $query->where('soldFlag', 0)
                                                      ->whereHas('document_product', function($query) use ($input) {
                                                            $query->where('documentSystemID', $input['documentSystemID'])
                                                                  ->where('documentDetailID', $input['documentDetailID']);
                                                      });
                                            })->orWhere(function($query) use ($input) {
                                                $query->where('soldFlag', 1)
                                                      ->whereDoesntHave('document_product', function($query) use ($input) {
                                                            $query->where('documentSystemID', $input['documentSystemID'])
                                                                  ->where('documentDetailID', $input['documentDetailID']);
                                                      });
                                            });
                                  })
                                  ->with(['document_in_product', 'document_product' => function($query) use ($input) {
                                        $query->where('documentSystemID', $input['documentSystemID'])
                                              ->where('documentDetailID', $input['documentDetailID']);
                                  }, 'warehouse', 'bin_location'])
                                  ->whereHas('document_in_product', function($query) use ($input){
                                        $query->where(function($query) {
                                            $query->whereHas('material_issue', function($query) {
                                                    $query->where('approved', -1);
                                                })
                                                ->orWhereHas('delivery_order', function($query) {
                                                    $query->where('approvedYN', -1);
                                                })->orWhereHas('customer_invoice', function($query) {
                                                    $query->where('approved', -1);
                                                });
                                        })
                                        ->where('documentSystemCode', $input['rootDocumentID']);
                                  })
                                  ->get();

        return $this->sendResponse($itemSerials, 'product serial retrived successfully');
    }

    public function updateSoldStatusOfSerial(Request $request) 
    {
        $input = $request->all();

        $cehckSerial = ItemSerial::find($input['id']);

        if (!$cehckSerial) {
            return $this->sendError("Serial not found");
        }



        DB::beginTransaction();
        try {

            if (isset($input['isChecked']) && $input['isChecked']) {

                $checkCountOfOut = DocumentSubProduct::where('documentSystemID', $input['documentSystemID'])
                                                             ->where('documentDetailID', $input['documentDetailID'])
                                                             ->count();

                if (($checkCountOfOut + 1) > floatval($input['noQty'])) {
                    return $this->sendError("Out quantity cannot be greater than issue quantity");
                }

                if (isset($input['wareHouseCodeTo']) && $input['wareHouseCodeTo'] > 0) {
                    $cehckSerial->wareHouseSystemID = $input['wareHouseCodeTo'];
                } else {
                    $cehckSerial->soldFlag = 1;
                }

                $cehckSerial->save();

                $checkInData = DocumentSubProduct::where('productSerialID', $input['id'])
                                                 ->where('sold', 0)
                                                 ->first();     
                                                 
                if (!$checkInData) {
                    return $this->sendError("Serial has been sold.");
                }      

                $this->itemSerialRepository->mapSubProducts($input['id'], $input['documentSystemID'], $input['documentDetailID'], $checkInData->id);

                
                $checkInData->sold = 1;
                $checkInData->soldQty = 1;

                $checkInData->save();
            } else {
                $checkDocumentSubProduct = DocumentSubProduct::where('documentSystemID', $input['documentSystemID'])
                                                             ->where('documentDetailID', $input['documentDetailID'])
                                                             ->where('productSerialID', $input['id'])
                                                             ->first();

                if ($checkDocumentSubProduct) {
                    $soldProduct = DocumentSubProduct::find($checkDocumentSubProduct->productInID);
                    if ($soldProduct) {
                        $soldProduct->sold = 0;
                        $soldProduct->soldQty = 0;
                        $soldProduct->save();
                    }

                    if (isset($input['wareHouseCodeTo']) && $input['wareHouseCodeTo'] > 0) {
                        $cehckSerial->wareHouseSystemID = isset($input['wareHouseCodeFrom']) ? $input['wareHouseCodeFrom'] : null;
                    } else {
                        $cehckSerial->soldFlag = 0;
                    }
                    $cehckSerial->save();


                    DocumentSubProduct::where('documentSystemID', $input['documentSystemID'])
                                                             ->where('documentDetailID', $input['documentDetailID'])
                                                             ->where('productSerialID', $input['id'])
                                                             ->delete();
                }
            }

            DB::commit();
            return $this->sendResponse([], 'product serial generated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage(), 422);
        }
    }

    public function updateReturnStatusOfSerial(Request $request) 
    {
        $input = $request->all();

        $cehckSerial = ItemSerial::find($input['id']);

        if (!$cehckSerial) {
            return $this->sendError("Serial not found");
        }



        DB::beginTransaction();
        try {

            if (isset($input['isChecked']) && $input['isChecked']) {

                $checkCountOfOut = DocumentSubProduct::where('documentSystemID', $input['documentSystemID'])
                                                             ->where('documentDetailID', $input['documentDetailID'])
                                                             ->count();

                if (($checkCountOfOut + 1) > floatval($input['noQty'])) {
                    return $this->sendError("Out quantity cannot be greater than received quantity");
                }


                $cehckSerial->soldFlag = 0;
                $cehckSerial->save();

                $checkInData = DocumentSubProduct::where('productSerialID', $input['id'])
                                                 ->where('sold', 0)
                                                 ->first();     
                                                 
                if (!$checkInData) {
                    return $this->sendError("Serial has been sold.");
                }      

                $this->itemSerialRepository->mapSubProducts($input['id'], $input['documentSystemID'], $input['documentDetailID'], $checkInData->id);

                $checkInData->sold = 1;
                $checkInData->soldQty = 1;
                $checkInData->save();
            } else {
                $checkDocumentSubProduct = DocumentSubProduct::where('documentSystemID', $input['documentSystemID'])
                                                             ->where('documentDetailID', $input['documentDetailID'])
                                                             ->where('productSerialID', $input['id'])
                                                             ->first();

                if ($checkDocumentSubProduct) {
                    $soldProduct = DocumentSubProduct::find($checkDocumentSubProduct->productInID);
                    if ($soldProduct) {
                        $soldProduct->sold = 0;
                        $soldProduct->soldQty = 0;
                        $soldProduct->save();
                    }

                    $cehckSerial->soldFlag = 1;
                    $cehckSerial->save();

                    DocumentSubProduct::where('documentSystemID', $input['documentSystemID'])
                                                             ->where('documentDetailID', $input['documentDetailID'])
                                                             ->where('productSerialID', $input['id'])
                                                             ->delete();
                }
            }

            DB::commit();
            return $this->sendResponse([], 'product serial generated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage(), 422);
        }
    }
}
