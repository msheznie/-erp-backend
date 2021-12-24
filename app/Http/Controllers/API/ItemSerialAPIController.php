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

                $checkProductSerialDuplication = ItemSerial::where('itemSystemCode', $input['itemID'])
                                                           ->where('serialCode', $productSerial)
                                                           ->first();

                if ($checkProductSerialDuplication) {
                    $duplicateSerials[] = $productSerial;
                }

                $saveData = [
                    'itemSystemCode' => $input['itemID'],
                    'serialCode' => $productSerial,
                ];

                $res = ItemSerial::create($saveData);

                if ($res) {
                    $this->itemSerialRepository->mapSubProducts($res, $input['documentSystemID'], $input['documentDetailID']);
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
}
