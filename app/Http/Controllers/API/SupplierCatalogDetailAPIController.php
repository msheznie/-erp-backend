<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierCatalogDetailAPIRequest;
use App\Http\Requests\API\UpdateSupplierCatalogDetailAPIRequest;
use App\Models\ItemMaster;
use App\Models\SupplierCatalogDetail;
use App\Models\SupplierCatalogMaster;
use App\Models\SupplierMaster;
use App\Repositories\SupplierCatalogDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierCatalogDetailController
 * @package App\Http\Controllers\API
 */

class SupplierCatalogDetailAPIController extends AppBaseController
{
    /** @var  SupplierCatalogDetailRepository */
    private $supplierCatalogDetailRepository;

    public function __construct(SupplierCatalogDetailRepository $supplierCatalogDetailRepo)
    {
        $this->supplierCatalogDetailRepository = $supplierCatalogDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierCatalogDetails",
     *      summary="Get a listing of the SupplierCatalogDetails.",
     *      tags={"SupplierCatalogDetail"},
     *      description="Get all SupplierCatalogDetails",
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
     *                  @SWG\Items(ref="#/definitions/SupplierCatalogDetail")
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
        $this->supplierCatalogDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierCatalogDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierCatalogDetails = $this->supplierCatalogDetailRepository->all();

        return $this->sendResponse($supplierCatalogDetails->toArray(), trans('custom.supplier_catalog_details_retrieved_successfully'));
    }

    /**
     * @param CreateSupplierCatalogDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/supplierCatalogDetails",
     *      summary="Store a newly created SupplierCatalogDetail in storage",
     *      tags={"SupplierCatalogDetail"},
     *      description="Store SupplierCatalogDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierCatalogDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierCatalogDetail")
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
     *                  ref="#/definitions/SupplierCatalogDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierCatalogDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'supplierCatalogMasterID' => 'required|numeric|min:1',
            'itemCodeSystem' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        $catalogMaster = SupplierCatalogMaster::find($input['supplierCatalogMasterID']);
        if(empty($catalogMaster)){
            return $this->sendError(trans('custom.catalog_master_not_found'),500);
        }

        $isAlreadyAdded = SupplierCatalogDetail::where('supplierCatalogMasterID',$input['supplierCatalogMasterID'])
            ->where('itemCodeSystem',$input['itemCodeSystem'])
            ->where(function ($query){
                $query->whereNull('isDeleted')
                    ->orWhere('isDeleted',0);
            })
            ->first();

        if(!empty($isAlreadyAdded)){
            return $this->sendError(trans('custom.item_already_added'),500);
        }

        $item = ItemMaster::find($input['itemCodeSystem']);
        if(empty($item)){
            return $this->sendError(trans('custom.item_not_found'),500);
        }

        $supplier = SupplierMaster::find($catalogMaster->supplierID);

        $input['localCurrencyID'] = $supplier->currency;
        $input['itemPrimaryCode'] = $item->primaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['itemUnitOfMeasure'] = $item->unit;

        $supplierCatalogDetail = $this->supplierCatalogDetailRepository->create($input);

        return $this->sendResponse($supplierCatalogDetail->toArray(), trans('custom.supplier_catalog_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierCatalogDetails/{id}",
     *      summary="Display the specified SupplierCatalogDetail",
     *      tags={"SupplierCatalogDetail"},
     *      description="Get SupplierCatalogDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierCatalogDetail",
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
     *                  ref="#/definitions/SupplierCatalogDetail"
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
        /** @var SupplierCatalogDetail $supplierCatalogDetail */
        $supplierCatalogDetail = $this->supplierCatalogDetailRepository->findWithoutFail($id);

        if (empty($supplierCatalogDetail)) {
            return $this->sendError(trans('custom.supplier_catalog_detail_not_found'));
        }

        return $this->sendResponse($supplierCatalogDetail->toArray(), trans('custom.supplier_catalog_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSupplierCatalogDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/supplierCatalogDetails/{id}",
     *      summary="Update the specified SupplierCatalogDetail in storage",
     *      tags={"SupplierCatalogDetail"},
     *      description="Update SupplierCatalogDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierCatalogDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierCatalogDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierCatalogDetail")
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
     *                  ref="#/definitions/SupplierCatalogDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierCatalogDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input,['uom_default','item_by','local_currency']);
        $validator = \Validator::make($input, [
            'supplierCatalogMasterID' => 'required|numeric|min:1',
            'itemCodeSystem' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if(isset($input['localCurrencyID']) && is_array($input['localCurrencyID'])){
            $input = $this->convertArrayToValue($input);
        }

        $catalogMaster = SupplierCatalogMaster::find($input['supplierCatalogMasterID']);
        if(empty($catalogMaster)){
            return $this->sendError(trans('custom.catalog_master_not_found'),500);
        }



        /** @var SupplierCatalogDetail $supplierCatalogDetail */
        $supplierCatalogDetail = $this->supplierCatalogDetailRepository->findWithoutFail($id);

        if (empty($supplierCatalogDetail)) {
            return $this->sendError(trans('custom.supplier_catalog_detail_not_found'));
        }

        $supplierCatalogDetail = $this->supplierCatalogDetailRepository->update($input, $id);

        return $this->sendResponse($supplierCatalogDetail->toArray(), trans('custom.suppliercatalogdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/supplierCatalogDetails/{id}",
     *      summary="Remove the specified SupplierCatalogDetail from storage",
     *      tags={"SupplierCatalogDetail"},
     *      description="Delete SupplierCatalogDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierCatalogDetail",
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
        /** @var SupplierCatalogDetail $supplierCatalogDetail */
        $supplierCatalogDetail = $this->supplierCatalogDetailRepository->findWithoutFail($id);

        if (empty($supplierCatalogDetail)) {
            return $this->sendError(trans('custom.supplier_catalog_detail_not_found'));
        }

//        $supplierCatalogDetail->delete();
        $this->supplierCatalogDetailRepository->update(['isDeleted'=>1], $id);
        return $this->sendResponse($id, trans('custom.supplier_catalog_detail_deleted_successfully'));
    }
}
