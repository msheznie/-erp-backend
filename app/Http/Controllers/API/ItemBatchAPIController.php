<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemBatchAPIRequest;
use App\Http\Requests\API\UpdateItemBatchAPIRequest;
use App\Models\ItemBatch;
use App\Models\WarehouseMaster;
use App\Models\DocumentSubProduct;
use App\Repositories\ItemSerialRepository;
use App\Repositories\ItemBatchRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class ItemBatchController
 * @package App\Http\Controllers\API
 */

class ItemBatchAPIController extends AppBaseController
{
    /** @var  ItemBatchRepository */
    private $itemBatchRepository;
    private $itemSerialRepository;

    public function __construct(ItemBatchRepository $itemBatchRepo, ItemSerialRepository $itemSerialRepo)
    {
        $this->itemBatchRepository = $itemBatchRepo;
        $this->itemSerialRepository = $itemSerialRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemBatches",
     *      summary="Get a listing of the ItemBatches.",
     *      tags={"ItemBatch"},
     *      description="Get all ItemBatches",
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
     *                  @SWG\Items(ref="#/definitions/ItemBatch")
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
        $this->itemBatchRepository->pushCriteria(new RequestCriteria($request));
        $this->itemBatchRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemBatches = $this->itemBatchRepository->all();

        return $this->sendResponse($itemBatches->toArray(), trans('custom.item_batches_retrieved_successfully'));
    }

    /**
     * @param CreateItemBatchAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemBatches",
     *      summary="Store a newly created ItemBatch in storage",
     *      tags={"ItemBatch"},
     *      description="Store ItemBatch",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemBatch that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemBatch")
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
     *                  ref="#/definitions/ItemBatch"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemBatchAPIRequest $request)
    {
        $input = $request->all();

        $itemBatch = $this->itemBatchRepository->create($input);

        return $this->sendResponse($itemBatch->toArray(), trans('custom.item_batch_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemBatches/{id}",
     *      summary="Display the specified ItemBatch",
     *      tags={"ItemBatch"},
     *      description="Get ItemBatch",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemBatch",
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
     *                  ref="#/definitions/ItemBatch"
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
        /** @var ItemBatch $itemBatch */
        $itemBatch = $this->itemBatchRepository->findWithoutFail($id);

        if (empty($itemBatch)) {
            return $this->sendError(trans('custom.item_batch_not_found'));
        }

        return $this->sendResponse($itemBatch->toArray(), trans('custom.item_batch_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateItemBatchAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemBatches/{id}",
     *      summary="Update the specified ItemBatch in storage",
     *      tags={"ItemBatch"},
     *      description="Update ItemBatch",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemBatch",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemBatch that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemBatch")
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
     *                  ref="#/definitions/ItemBatch"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemBatchAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        
        $checkBatchCode = ItemBatch::where('id', '!=', $input['id'])
                                     ->where('batchCode', $input['batchCode'])
                                     ->where('itemSystemCode', $input['itemSystemCode'])
                                     ->first();

        if ($checkBatchCode) {
            return $this->sendError(trans('custom.batch_code_cannot_be_duplicate'));
        }

        if (isset($input['batchCode']) && strlen($input['batchCode']) > 20) {
            return $this->sendError(trans('custom.batch_code_length_cannot_greater_than_20'));
        }

        if (!preg_match('/^[a-zA-Z0-9\-\/]*$/', $input['batchCode'])) {
            return $this->sendError('Batch code can contain only / and - in special character');
        }


        $subProducts = DocumentSubProduct::where('documentDetailID', $input['documentDetailID'])
                                          ->where('documentSystemID', $input['documentSystemID'])
                                          ->where('productBatchID', '!=', $input['id'])
                                          ->sum('quantity');
        
        $newTotalQty = $subProducts + floatval($input['quantity']);

        if ($newTotalQty > $input['noQty']) {
            return $this->sendError(trans('custom.batch_quantity_cannot_be_greater_than_total_quanti'));
        }

        if (!is_null($input['expireDate'])) {
            $input['expireDate'] = new Carbon($input['expireDate']);
        }

        /** @var ItemBatch $itemBatch */
        $itemBatch = $this->itemBatchRepository->findWithoutFail($id);

        if (empty($itemBatch)) {
            return $this->sendError(trans('custom.item_batch_not_found'));
        }


        $subProducts = DocumentSubProduct::where('documentDetailID', $input['documentDetailID'])
                                          ->where('documentSystemID', $input['documentSystemID'])
                                          ->where('productBatchID', $input['id'])
                                          ->update(['quantity' => floatval($input['quantity'])]);

        $itemBatch = $this->itemBatchRepository->update($input, $id);

        return $this->sendResponse($itemBatch->toArray(), trans('custom.item_batch_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemBatches/{id}",
     *      summary="Remove the specified ItemBatch from storage",
     *      tags={"ItemBatch"},
     *      description="Delete ItemBatch",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemBatch",
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
        /** @var ItemBatch $itemBatch */
        $itemBatch = $this->itemBatchRepository->findWithoutFail($id);

        if (empty($itemBatch)) {
            return $this->sendError(trans('custom.item_batch_not_found'));
        }

        if ($itemBatch->copiedQty > 0) {
            return $this->sendError(trans('custom.item_batch_cannot_be_deleted_it_has_been_sold'));
        }

        $delteSubProduct = DocumentSubProduct::where('productBatchID', $itemBatch->id)
                                             ->whereNull('productInID')
                                             ->delete();

        $itemBatch->delete();

        return $this->sendResponse([],trans('custom.item_batch_deleted_successfully'));
    }


    public function getBatchNumbersForOut(Request $request)
    {
        $input = $request->all();

        if(!isset($input['warehouse'])) {
            return $this->sendError("Warehouse not selected");
        }

        $itemSerials = ItemBatch::where('itemSystemCode', $input['itemSystemCode'])
                                 ->when($input['documentSystemID'] == 13, function($query) use ($input){
                                    $query->where(function($query) use ($input) {
                                        $query->whereHas('document_in_product', function($query) use ($input) { 
                                                    $query->where('wareHouseSystemID',$input['warehouse']);
                                                })
                                                ->orWhereHas('document_product', function($query) use ($input) {
                                                        $query->where('documentSystemID', $input['documentSystemID'])
                                                              ->where('documentDetailID', $input['documentDetailID'])
                                                              ->where('wareHouseSystemID',$input['wareHouseCodeTo']);
                                                });
                                      });                                
                                 })
                                 ->where(function($query) use ($input){
                                        $query->where(function($query) use ($input) {
                                                $query->whereHas('document_product', function($query) use ($input) {
                                                            $query->where('documentSystemID', $input['documentSystemID'])
                                                                  ->where('documentDetailID', $input['documentDetailID']);
                                                      });
                                            })->orWhere(function($query) use ($input) {
                                                $query->whereDoesntHave('document_product', function($query) use ($input) {
                                                            $query->where('documentSystemID', $input['documentSystemID'])
                                                                  ->where('documentDetailID', $input['documentDetailID']);
                                                      });
                                            })->orWhere(function($query) use ($input) {
                                                $query->when($input['documentSystemID'] == 13, function($query) use ($input){
                                                    $query->whereHas('document_product', function($query) use ($input) {
                                                            $query->where('documentSystemID', $input['documentSystemID'])
                                                                  ->where('documentDetailID', $input['documentDetailID']);
                                                      }); 
                                                });
                                            })->orWhere(function($query) use ($input) {
                                                $query->when(in_array($input['documentSystemID'], [71, 20]), function($query) use ($input){
                                                    $query->whereHas('document_product', function($query) use ($input) {
                                                            $query->where('documentSystemID', $input['documentSystemID'])
                                                                  ->where('documentDetailID', $input['documentDetailID']);
                                                      }); 
                                                });
                                            });
                                  })
                                  ->with(['document_products' => function($query) use ($input) {
                                        $query->where('documentSystemID', $input['documentSystemID'])
                                              ->where('documentDetailID', $input['documentDetailID']);
                                  },'document_in_products_data' => function($query) use ($input) {
                                        $query->where('documentSystemID','!=', $input['documentSystemID'])
                                              ->where('documentDetailID', '!=', $input['documentDetailID'])
                                              ->where('wareHouseSystemID', $input['warehouse'])
                                              ->selectRaw('SUM(quantity - soldQty) as remaingQty, productBatchID')
                                              ->groupBy('productBatchID');
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
                                        })
                                        ->when($input['documentSystemID'] != 13, function($query) use ($input){
                                            $query->where('wareHouseSystemID',$input['warehouse']);
                                         })
                                        ->when((isset($input['rootDocumentSystemID']) && $input['rootDocumentSystemID'] > 0 && isset($input['rootDocumentSystemCode']) && $input['rootDocumentSystemCode'] > 0), function($query) use ($input) {
                                            $query->where('documentSystemID', $input['rootDocumentSystemID'])
                                                  ->where('documentSystemCode', $input['rootDocumentSystemCode']);
                                        });
                                    })
                                  ->get();

        return $this->sendResponse($itemSerials, trans('custom.product_batch_retrived_successfully'));
    }

    public function updateSoldStatusOfBatch(Request $request) 
    {
        $input = $request->all();

        $checkBatch = ItemBatch::find($input['id']);

        if (!$checkBatch) {
            return $this->sendError("Batch not found");
        }

        DB::beginTransaction();
        try {
            $input['quantityCopied'] = isset($input['quantityCopied']) ? floatval($input['quantityCopied']) : 0;

            $wareHouseCodeTo = (isset($input['wareHouseCodeTo']) && $input['wareHouseCodeTo'] > 0) ? $input['wareHouseCodeTo'] : null;
            $checkDocumentSubProduct = DocumentSubProduct::where('documentSystemID', $input['documentSystemID'])
                                                             ->where('documentDetailID', $input['documentDetailID'])
                                                             ->where('productBatchID', $input['id'])
                                                             ->get();

            if ($checkDocumentSubProduct) {
                $totalQty = 0;
                foreach ($checkDocumentSubProduct as $key => $value) {
                    
                    $soldProduct = DocumentSubProduct::find($value->productInID);
                    if ($soldProduct) {
                        $soldProduct->sold = 0;
                        $soldProduct->soldQty = $soldProduct->soldQty - $value->quantity;
                        $soldProduct->save();
                    }
                    
                    $totalQty += $value->quantity;
                }

                if (is_null($wareHouseCodeTo)) {
                    $checkBatch->soldFlag = 0;
                    $checkBatch->copiedQty = $checkBatch->copiedQty - $totalQty;
                    $checkBatch->save();
                }
                


                DocumentSubProduct::where('documentSystemID', $input['documentSystemID'])
                                 ->where('documentDetailID', $input['documentDetailID'])
                                 ->where('productBatchID', $input['id'])
                                 ->delete();
            }

            if ($input['quantityCopied'] > 0) {

                $checkCountOfOut = DocumentSubProduct::where('documentSystemID', $input['documentSystemID'])
                                                             ->where('documentDetailID', $input['documentDetailID'])
                                                             ->where('productBatchID', '!=', $input['id'])
                                                             ->sum('quantity');

                $previousOutCount = DocumentSubProduct::where('documentSystemID', '!=', $input['documentSystemID'])
                                                             ->where('documentDetailID', '!=', $input['documentDetailID'])
                                                             ->where('productBatchID',  $input['id'])
                                                             ->whereNotNull('productInID')
                                                             ->sum('quantity');

                $previousLineOutCount = DocumentSubProduct::where('documentSystemID', $input['documentSystemID'])
                                                             ->where('documentDetailID', $input['documentDetailID'])
                                                             ->where('productBatchID',  $input['id'])
                                                             ->whereNotNull('productInID')
                                                             ->sum('quantity');

                if (($checkCountOfOut + $input['quantityCopied']) > floatval($input['noQty'])) {
                    return $this->sendError("Out quantity cannot be greater than issue quantity");
                }

                $newCopiedQty = ($previousOutCount + $input['quantityCopied']);
                if (is_null($wareHouseCodeTo)) {
                    $checkBatch->soldFlag = ($checkBatch->quantity == $newCopiedQty) ? 1 : 0;
                    $checkBatch->copiedQty = $newCopiedQty;

                    $checkBatch->save();
                }

                $checkInData = DocumentSubProduct::selectRaw('SUM(quantity - soldQty) as remaingQty')
                                                 ->where('productBatchID', $input['id'])
                                                 ->whereIn('documentSystemID', [3, 12, 87, 13])
                                                 ->where('wareHouseSystemID', $input['wareHouseCodeFrom'])
                                                 ->where('sold', 0)
                                                 ->first();     
                                                 
                if (!$checkInData) {
                    return $this->sendError("Batch has been sold.");
                }      

                if (($checkInData->remaingQty - $previousLineOutCount) < $input['quantityCopied']) {
                    return $this->sendError("Batch quantity cannot be greater than remaining quantity.");
                }


                $productInDatas = DocumentSubProduct::where('productBatchID', $input['id'])
                                                 ->whereIn('documentSystemID', [3, 12, 87, 13])
                                                 ->where('sold', 0)
                                                 ->where('wareHouseSystemID', $input['wareHouseCodeFrom'])
                                                 ->get();

                $quantityCopied = $input['quantityCopied'];
                foreach ($productInDatas as $key => $value) {
                    $remaingQtyToCopied = floatval($value->quantity) - floatval($value->soldQty);
                    if ($quantityCopied > 0) {
                        if ($quantityCopied <= $remaingQtyToCopied) {
                            $this->itemSerialRepository->mapBatchSubProducts($input['id'], $input['documentSystemID'], $input['documentDetailID'], $value->id, $quantityCopied, $wareHouseCodeTo);

                            $updateData = [
                                'soldQty' => $value->soldQty + $quantityCopied,
                                'sold' => (($value->soldQty + $quantityCopied) == $value->quantity) ? 1 : 0
                            ];

                            DocumentSubProduct::where('id', $value->id)->update($updateData);
                            $quantityCopied = 0;
                        } else {
                            $this->itemSerialRepository->mapBatchSubProducts($input['id'], $input['documentSystemID'], $input['documentDetailID'], $value->id, $remaingQtyToCopied, $wareHouseCodeTo);

                            $updateData = [
                                'soldQty' => $value->soldQty + $remaingQtyToCopied,
                                'sold' => (($value->soldQty + $remaingQtyToCopied) == $value->quantity) ? 1 : 0
                            ];

                            DocumentSubProduct::where('id', $value->id)->update($updateData);
                            $quantityCopied = $quantityCopied - $remaingQtyToCopied;
                        }
                    }
                }     
            } 

            DB::commit();
            return $this->sendResponse([], trans('custom.product_serial_generated_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage(), 422);
        }
    }


    public function getBatchNumbersForReturn(Request $request)
    {
        $input = $request->all();

        $itemSerials = ItemBatch::where('itemSystemCode', $input['itemSystemCode'])
                                  ->where(function($query) use ($input){
                                        $query->where(function($query) use ($input) {
                                                $query->where(function($query) {
                                                            $query->where('soldFlag', 0)
                                                                  ->orWhere('copiedQty', 0);
                                                        })
                                                      ->whereHas('document_product', function($query) use ($input) {
                                                            $query->where('documentSystemID', $input['documentSystemID'])
                                                                  ->where('documentDetailID', $input['documentDetailID']);
                                                      });
                                            })->orWhere(function($query) use ($input) {
                                                $query->where(function($query) {
                                                            $query->where('soldFlag', 1)
                                                                  ->orWhere('copiedQty','>', 0);
                                                        })
                                                      ->whereDoesntHave('document_product', function($query) use ($input) {
                                                            $query->where('documentSystemID', $input['documentSystemID'])
                                                                  ->where('documentDetailID', $input['documentDetailID']);
                                                      });
                                            });
                                  })
                                  ->with(['document_in_products' => function($query) use ($input) {
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
                                  }, 'document_product' => function($query) use ($input) {
                                        $query->where('documentSystemID', $input['documentSystemID'])
                                              ->where('documentDetailID', $input['documentDetailID']);
                                  }, 'warehouse', 'bin_location'])
                                  ->whereHas('document_in_products', function($query) use ($input){
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
                                        ->where('documentSystemCode', $input['rootDocumentID'])
                                        ->where('wareHouseSystemID',$input['wareHouseSystemCode']);
                                  })
                                  ->get();

        return $this->sendResponse($itemSerials, trans('custom.product_batch_retrived_successfully'));
    }

    public function updateReturnStatusOfBatch(Request $request) 
    {
        $input = $request->all();

        $checkBatch = ItemBatch::find($input['id']);

        if (!$checkBatch) {
            return $this->sendError("Batch not found");
        }



        DB::beginTransaction();
        try {
            $input['quantityCopied'] = isset($input['quantityCopied']) ? floatval($input['quantityCopied']) : 0;

            $checkDocumentSubProduct = DocumentSubProduct::where('documentSystemID', $input['documentSystemID'])
                                                             ->where('documentDetailID', $input['documentDetailID'])
                                                             ->where('productBatchID', $input['id'])
                                                             ->get();

            if (count($checkDocumentSubProduct) > 0) {
                $totalQty = 0;
                foreach ($checkDocumentSubProduct as $key => $value) {
                    
                    $soldProduct = DocumentSubProduct::find($value->productInID);
                    if ($soldProduct) {
                        $soldProduct->sold = 0;
                        $soldProduct->soldQty = $soldProduct->soldQty - $value->quantity;
                        $soldProduct->save();
                    }
                    
                    $totalQty += $value->quantity;
                }

              
                $checkBatch->soldFlag = (($checkBatch->copiedQty + $totalQty) == $checkBatch->quantity) ? 1 : 0;
                $checkBatch->copiedQty = $checkBatch->copiedQty + $totalQty;
                
                $checkBatch->save();


                DocumentSubProduct::where('documentSystemID', $input['documentSystemID'])
                                 ->where('documentDetailID', $input['documentDetailID'])
                                 ->where('productBatchID', $input['id'])
                                 ->delete();
            }

            if ($input['quantityCopied'] > 0) {

                $checkCountOfOut = DocumentSubProduct::where('documentSystemID', $input['documentSystemID'])
                                                             ->where('documentDetailID', $input['documentDetailID'])
                                                             ->where('productBatchID', '!=', $input['id'])
                                                             ->sum('quantity');

                $previousOutCount = DocumentSubProduct::where('documentSystemID', '!=', $input['documentSystemID'])
                                                             ->where('documentDetailID', '!=', $input['documentDetailID'])
                                                             ->where('productBatchID',  $input['id'])
                                                             ->whereNotNull('productInID')
                                                             ->sum('quantity');

                $previousLineOutCount = DocumentSubProduct::where('documentSystemID', $input['documentSystemID'])
                                                             ->where('documentDetailID', $input['documentDetailID'])
                                                             ->where('productBatchID',  $input['id'])
                                                             ->sum('quantity');

                if (($checkCountOfOut + $input['quantityCopied']) > floatval($input['noQty'])) {
                    return $this->sendError("Out quantity cannot be greater than issue quantity");
                }

                if ($previousOutCount < $input['quantityCopied']) {
                    return $this->sendError("Out quantity cannot be greater than remaining quantity");
                }

               
                $newCopiedQty = ($previousOutCount - $input['quantityCopied']);
                $checkBatch->soldFlag = 0;
                $checkBatch->copiedQty = $newCopiedQty;
                $checkBatch->save();

                $checkInData = DocumentSubProduct::selectRaw('SUM(quantity - soldQty) as remaingQty')
                                                 ->where('productBatchID', $input['id'])
                                                 ->when(isset($input['rootDocumentID']) && isset($input['rootDocumentSystemID']), function($query) use ($input) {
                                                    $query->where('documentSystemID', $input['rootDocumentSystemID'])
                                                          ->where('documentSystemCode', $input['rootDocumentID']);
                                                 })
                                                 ->where('sold', 0)
                                                 ->first();     
                                                 
                if (!$checkInData) {
                    return $this->sendError("Batch has been sold.");
                }      

                if (($checkInData->remaingQty - $previousLineOutCount) < $input['quantityCopied']) {
                    return $this->sendError("Batch quantity cannot be greater than remaining quantity.");
                }


                $productInDatas = DocumentSubProduct::where('productBatchID', $input['id'])
                                                 ->when(isset($input['rootDocumentID']) && isset($input['rootDocumentSystemID']), function($query) use ($input) {
                                                    $query->where('documentSystemID', $input['rootDocumentSystemID'])
                                                          ->where('documentSystemCode', $input['rootDocumentID']);
                                                 })
                                                 ->where('sold', 0)
                                                 ->get();

                $quantityCopied = $input['quantityCopied'];
                foreach ($productInDatas as $key => $value) {
                    $remaingQtyToCopied = floatval($value->quantity) - floatval($value->soldQty);
                    if ($quantityCopied > 0) {
                        if ($quantityCopied <= $remaingQtyToCopied) {
                            $this->itemSerialRepository->mapBatchSubProducts($input['id'], $input['documentSystemID'], $input['documentDetailID'], $value->id, $quantityCopied);

                            $updateData = [
                                'soldQty' => $value->soldQty + $quantityCopied,
                                'sold' => (($value->soldQty + $quantityCopied) == $value->quantity) ? 1 : 0
                            ];

                            DocumentSubProduct::where('id', $value->id)->update($updateData);
                            $quantityCopied = 0;
                        } else {
                            $this->itemSerialRepository->mapBatchSubProducts($input['id'], $input['documentSystemID'], $input['documentDetailID'], $value->id, $remaingQtyToCopied);

                            $updateData = [
                                'soldQty' => $value->soldQty + $remaingQtyToCopied,
                                'sold' => (($value->soldQty + $remaingQtyToCopied) == $value->quantity) ? 1 : 0
                            ];

                            DocumentSubProduct::where('id', $value->id)->update($updateData);
                            $quantityCopied = $quantityCopied - $remaingQtyToCopied;
                        }
                    }
                }     
            } 


            DB::commit();
            return $this->sendResponse([], trans('custom.product_batch_generated_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage(), 422);
        }
    }

    public function getWareHouseDataForItemOut(Request $request)
    {
        $input = $request->all();

        $warehouseFrom = "";
        $warehouseTo = "";


        if (isset($input['warehouse']) && $input['warehouse'] > 0) {
            $warehouse = WarehouseMaster::find($input['warehouse']);
            $warehouseFrom = $warehouse ? $warehouse->wareHouseCode ." - ".$warehouse->wareHouseDescription : "";
        }

        if (isset($input['wareHouseCodeTo']) && $input['wareHouseCodeTo'] > 0) {
            $warehouse = WarehouseMaster::find($input['wareHouseCodeTo']);
            $warehouseTo = $warehouse ? $warehouse->wareHouseCode ." - ".$warehouse->wareHouseDescription : "";
        }


        return $this->sendResponse(['warehouseTo' => $warehouseTo, 'warehouseFrom' => $warehouseFrom], 'warehouse data retrieved successfully');
    }

}
