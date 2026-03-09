<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerCatalogDetailAPIRequest;
use App\Http\Requests\API\UpdateCustomerCatalogDetailAPIRequest;
use App\Models\CustomerCatalogDetail;
use App\Models\CustomerCatalogMaster;
use App\Models\CustomerMaster;
use App\Models\ItemMaster;
use App\Repositories\CustomerCatalogDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Arr;

/**
 * Class CustomerCatalogDetailController
 * @package App\Http\Controllers\API
 */

class CustomerCatalogDetailAPIController extends AppBaseController
{
    /** @var  CustomerCatalogDetailRepository */
    private $customerCatalogDetailRepository;

    public function __construct(CustomerCatalogDetailRepository $customerCatalogDetailRepo)
    {
        $this->customerCatalogDetailRepository = $customerCatalogDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerCatalogDetails",
     *      summary="Get a listing of the CustomerCatalogDetails.",
     *      tags={"CustomerCatalogDetail"},
     *      description="Get all CustomerCatalogDetails",
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
     *                  @SWG\Items(ref="#/definitions/CustomerCatalogDetail")
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
        $this->customerCatalogDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->customerCatalogDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerCatalogDetails = $this->customerCatalogDetailRepository->all();

        return $this->sendResponse($customerCatalogDetails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_catalog_details')]));
    }

    /**
     * @param CreateCustomerCatalogDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerCatalogDetails",
     *      summary="Store a newly created CustomerCatalogDetail in storage",
     *      tags={"CustomerCatalogDetail"},
     *      description="Store CustomerCatalogDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerCatalogDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerCatalogDetail")
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
     *                  ref="#/definitions/CustomerCatalogDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerCatalogDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'customerCatalogMasterID' => 'required|numeric|min:1',
            'itemCodeSystem' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        $catalogMaster = CustomerCatalogMaster::find($input['customerCatalogMasterID']);
        if(empty($catalogMaster)){
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.catalog_master')]),500);
        }

        $isAlreadyAdded = CustomerCatalogDetail::where('customerCatalogMasterID',$input['customerCatalogMasterID'])
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.item')]),500);
        }

        $customer = CustomerMaster::find($catalogMaster->customerID);
        if (isset($customer->customer_default_currency->currencyID) == null) {
            return $this->sendError(trans('custom.currency_not_configured'),500);
        }

        $input['localCurrencyID'] = $customer->customer_default_currency->currencyID;
        $input['itemPrimaryCode'] = $item->primaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['partNo'] = $item->secondaryItemCode;
        $input['itemUnitOfMeasure'] = $item->unit;

        $customerCatalogDetail = $this->customerCatalogDetailRepository->create($input);

        return $this->sendResponse($customerCatalogDetail->toArray(), trans('custom.save', ['attribute' => trans('custom.customer_catalog_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerCatalogDetails/{id}",
     *      summary="Display the specified CustomerCatalogDetail",
     *      tags={"CustomerCatalogDetail"},
     *      description="Get CustomerCatalogDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerCatalogDetail",
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
     *                  ref="#/definitions/CustomerCatalogDetail"
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
        /** @var CustomerCatalogDetail $customerCatalogDetail */
        $customerCatalogDetail = $this->customerCatalogDetailRepository->findWithoutFail($id);

        if (empty($customerCatalogDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_catalog_details')]));
        }

        return $this->sendResponse($customerCatalogDetail->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_catalog_details')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomerCatalogDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerCatalogDetails/{id}",
     *      summary="Update the specified CustomerCatalogDetail in storage",
     *      tags={"CustomerCatalogDetail"},
     *      description="Update CustomerCatalogDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerCatalogDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerCatalogDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerCatalogDetail")
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
     *                  ref="#/definitions/CustomerCatalogDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerCatalogDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = Arr::except($input,['uom_default','item_by','local_currency']);
        $validator = \Validator::make($input, [
            'customerCatalogMasterID' => 'required|numeric|min:1',
            'itemCodeSystem' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if(isset($input['localCurrencyID']) && is_array($input['localCurrencyID'])){
            $input = $this->convertArrayToValue($input);
        }

        $catalogMaster = CustomerCatalogMaster::find($input['customerCatalogMasterID']);
        if(empty($catalogMaster)){
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.catalog_master')]),500);
        }

        /** @var CustomerCatalogDetail $customerCatalogDetail */
        $customerCatalogDetail = $this->customerCatalogDetailRepository->findWithoutFail($id);

        if (empty($customerCatalogDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_catalog_details')]));
        }

        $customerCatalogDetail = $this->customerCatalogDetailRepository->update($input, $id);

        return $this->sendResponse($customerCatalogDetail->toArray(), trans('custom.update', ['attribute' => trans('custom.customer_catalog_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerCatalogDetails/{id}",
     *      summary="Remove the specified CustomerCatalogDetail from storage",
     *      tags={"CustomerCatalogDetail"},
     *      description="Delete CustomerCatalogDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerCatalogDetail",
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
        /** @var CustomerCatalogDetail $customerCatalogDetail */
        $customerCatalogDetail = $this->customerCatalogDetailRepository->findWithoutFail($id);

        if (empty($customerCatalogDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_catalog_details')]));
        }

//        $customerCatalogDetail->delete();
        $this->customerCatalogDetailRepository->update(['isDeleted'=>1], $id);
        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.customer_catalog_details')]));
    }
}
