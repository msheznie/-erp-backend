<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateCustomerCatalogMasterAPIRequest;
use App\Http\Requests\API\UpdateCustomerCatalogMasterAPIRequest;
use App\Models\CurrencyMaster;
use App\Models\CustomerCatalogDetail;
use App\Models\CustomerCatalogMaster;
use App\Models\CustomerCurrency;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerMaster;
use App\Models\ItemAssigned;
use App\Models\ItemMaster;
use App\Repositories\CustomerCatalogMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Arr;

/**
 * Class CustomerCatalogMasterController
 * @package App\Http\Controllers\API
 */

class CustomerCatalogMasterAPIController extends AppBaseController
{
    /** @var  CustomerCatalogMasterRepository */
    private $customerCatalogMasterRepository;

    public function __construct(CustomerCatalogMasterRepository $customerCatalogMasterRepo)
    {
        $this->customerCatalogMasterRepository = $customerCatalogMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerCatalogMasters",
     *      summary="Get a listing of the CustomerCatalogMasters.",
     *      tags={"CustomerCatalogMaster"},
     *      description="Get all CustomerCatalogMasters",
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
     *                  @SWG\Items(ref="#/definitions/CustomerCatalogMaster")
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
        $this->customerCatalogMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->customerCatalogMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerCatalogMasters = $this->customerCatalogMasterRepository->all();

        return $this->sendResponse($customerCatalogMasters->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_catalog_masters')]));
    }

    /**
     * @param CreateCustomerCatalogMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerCatalogMasters",
     *      summary="Store a newly created CustomerCatalogMaster in storage",
     *      tags={"CustomerCatalogMaster"},
     *      description="Store CustomerCatalogMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerCatalogMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerCatalogMaster")
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
     *                  ref="#/definitions/CustomerCatalogMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerCatalogMasterAPIRequest $request)
    {
        $input = $request->all();

        $messages = [
            'catalogID.required' => trans('custom.catalog_code_required')
        ];

        $validator = \Validator::make($request->all(), [
            'catalogID' => 'required',
            'catalogName' => 'required',
            'fromDate' => 'required|date',
            'toDate' => 'required|date|after_or_equal:fromDate',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $hasCatalogID = CustomerCatalogMaster::where('catalogID',$input['catalogID'])
            ->where(function ($q){
                $q->whereNull('isDeleted')
                    ->orWhere('isDeleted',0);
            })
            ->exists();

        if($hasCatalogID){
            return $this->sendError(trans('custom.duplicate_catalog_code_found'),500);
        }

        if(isset($input['fromDate'])){
            $input['fromDate'] = new Carbon($input['fromDate']);
        }

        if(isset($input['toDate'])){
            $input['toDate'] = new Carbon($input['toDate']);
        }

        $employee = Helper::getEmployeeInfo();
        $input['createdBy'] = $employee->employeeSystemID;
        $customerCatalogMaster = $this->customerCatalogMasterRepository->create($input);

        return $this->sendResponse($customerCatalogMaster->toArray(), trans('custom.save', ['attribute' => trans('custom.customer_catalog_masters')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerCatalogMasters/{id}",
     *      summary="Display the specified CustomerCatalogMaster",
     *      tags={"CustomerCatalogMaster"},
     *      description="Get CustomerCatalogMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerCatalogMaster",
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
     *                  ref="#/definitions/CustomerCatalogMaster"
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
        /** @var CustomerCatalogMaster $customerCatalogMaster */
        $customerCatalogMaster = $this->customerCatalogMasterRepository->with(['details' => function($query){
            $query->where(function ($q){
                $q->whereNull('isDeleted')
                    ->orWhere('isDeleted',0);
            })->with(['uom_default','item_by','local_currency']);
        }])->findWithoutFail($id);

        if (empty($customerCatalogMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_catalog_masters')]));
        }

        return $this->sendResponse($customerCatalogMaster->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_catalog_masters')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomerCatalogMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerCatalogMasters/{id}",
     *      summary="Update the specified CustomerCatalogMaster in storage",
     *      tags={"CustomerCatalogMaster"},
     *      description="Update CustomerCatalogMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerCatalogMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerCatalogMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerCatalogMaster")
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
     *                  ref="#/definitions/CustomerCatalogMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerCatalogMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = Arr::except($input,'created_by');
        $messages = [
            'catalogID.required' => trans('custom.catalog_code_required')
        ];

        $validator = \Validator::make($request->all(), [
            'catalogID' => 'required',
            'catalogName' => 'required',
            'fromDate' => 'required|date',
            'toDate' => 'required|date|after_or_equal:fromDate',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        $hasCatalogID = CustomerCatalogMaster::where('catalogID',$input['catalogID'])
            ->where('customerCatalogMasterID','!=',$id)
            ->where(function ($q){
                $q->whereNull('isDeleted')
                    ->orWhere('isDeleted',0);
            })
            ->exists();

        if($hasCatalogID){
            return $this->sendError(trans('custom.duplicate_catalog_code_found'), 500);
        }

        /** @var customerCatalogMaster $v */
        $customerCatalogMaster = $this->customerCatalogMasterRepository->findWithoutFail($id);

        if (empty($customerCatalogMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_catalog_masters')]));
        }

        if(isset($input['fromDate'])){
            $input['fromDate'] = new Carbon($input['fromDate']);
        }

        if(isset($input['toDate'])){
            $input['toDate'] = new Carbon($input['toDate']);
        }

        if((isset($input['isActive']) && $input['isActive']==1)){
            CustomerCatalogMaster::where('customerID',$customerCatalogMaster->customerID)->update(['isActive' => 0]);
        }

        $employee = Helper::getEmployeeInfo();
        $input['modifiedBy'] = $employee->employeeSystemID;

        $customerCatalogMaster = $this->customerCatalogMasterRepository->update($input, $id);

        return $this->sendResponse($customerCatalogMaster->toArray(), trans('custom.update', ['attribute' => trans('custom.customer_catalog_masters')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerCatalogMasters/{id}",
     *      summary="Remove the specified CustomerCatalogMaster from storage",
     *      tags={"CustomerCatalogMaster"},
     *      description="Delete CustomerCatalogMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerCatalogMaster",
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
        /** @var CustomerCatalogMaster $customerCatalogMaster */
        $customerCatalogMaster = $this->customerCatalogMasterRepository->findWithoutFail($id);

        if (empty($customerCatalogMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_catalog_masters')]));
        }

        $customerCatalogMaster->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.customer_catalog_masters')]));
    }

    public function getAllCustomerCatalogsByCompany(Request $request){

        $input = $request->all();
        $companyId = $request->companyId;
        $documentId = $request->documentId;
        $customerID = $request->customerID;
        $isGroup = Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companies = Helper::getGroupCompany($companyId);
        } else {
            $companies = [$companyId];
        }

        $customerCatalog = CustomerCatalogMaster::where(function ($query){
                $query->whereNull('isDeleted')
                    ->orWhere('isDeleted',0);
            })
            ->where('documentSystemID',$documentId)
            ->where('customerID',$customerID)
            ->with(['created_by']);

        $search = $request->input('search.value');
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $customerCatalog = $customerCatalog->where(function ($query) use ($search) {
                $query->where('catalogID', 'LIKE', "%{$search}%")
                    ->orWhere('catalogName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($customerCatalog)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('customerCatalogMasterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getItemsOptionsCustomerCatalog(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];

        $items = ItemMaster::select(['primaryCode', 'itemDescription', 'itemCodeSystem', 'secondaryItemCode']);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('primaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
            });
        }
        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.data')]));
    }

    function getCustomerCatalogDetailByCustomerItem(Request $request){

         $input  = $request->all();

        $company_id = $input['companyId'];
        $invoice_id = $input['id'];
        $itemArray = $input['item'];
        $item_assign_id = isset($itemArray['itemCode'])?$itemArray['itemCode']:0;
        $itemAssigned = ItemAssigned::find($item_assign_id);
        $item_id = isset($itemAssigned->itemCodeSystem)?$itemAssigned->itemCodeSystem:0;

        $invoice = CustomerInvoiceDirect::find($invoice_id);

        if (empty($invoice)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice')]));
        }

        $invoiceDate = Carbon::parse($invoice->bookingDate)->format('y-m-d');
        $customerID = $invoice->customerID;
        $catalog = CustomerCatalogDetail::whereHas('master', function($query) use($customerID,$invoiceDate){
            $query->whereDate('fromDate','<=',$invoiceDate)
                ->whereDate('toDate','>=',$invoiceDate)
                ->where('customerID',$customerID)
                ->where('isDeleted',0)
                ->where('isActive',1);
            })
            ->where('itemCodeSystem',$item_id)
            ->where(function ($q){
                $q->whereNull('isDeleted')
                    ->orWhere('isDeleted',0);
            })
            ->with(['uom_default','item_by','local_currency','master'])
            ->first();

        $output = [];
        if(!empty($catalog)){


            if($invoice->custTransactionCurrencyID != $catalog->localCurrencyID){

                $currency = CurrencyMaster::find($invoice->custTransactionCurrencyID);

                if(!empty($currency)){
                    $catalog['currency'] = $currency;
                    $currencyConversion = Helper::currencyConversion($invoice->companySystemID,$catalog->localCurrencyID, $invoice->custTransactionCurrencyID,$catalog->localPrice);
                    if(!empty($currencyConversion)){
                        $catalog->localPrice = round($currencyConversion['documentAmount'],$currency->DecimalPlaces);
                    }
                }else{
                    $catalog['currency'] = $catalog->local_currency;
                }

            }else{
                $catalog['currency'] = $catalog->local_currency;
            }


            $output = $catalog->toArray();
        }

        return $this->sendResponse($output,trans('custom.retrieve', ['attribute' => trans('custom.catalog')]));

    }

    public function getAssignedCurrenciesByCustomer(Request $request)
    {
        $customerId = $request['customerId'];
        $customer = CustomerMaster::where('customerCodeSystem', '=', $customerId)->first();
        if ($customer) {
            $customerCurrencies = DB::table('customercurrency')
                ->leftJoin('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')
                ->where('customerCodeSystem', '=', $customerId)
                ->where('isAssigned', -1)
                ->get();
        } else {
            $customerCurrencies = [];
        }

        return $this->sendResponse($customerCurrencies, trans('custom.retrieve', ['attribute' => trans('custom.customer_currencies')]));
    }
}
