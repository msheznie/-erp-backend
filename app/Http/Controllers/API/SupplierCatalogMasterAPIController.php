<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateSupplierCatalogMasterAPIRequest;
use App\Http\Requests\API\UpdateSupplierCatalogMasterAPIRequest;
use App\Models\ItemAssigned;
use App\Models\ItemMaster;
use App\Models\ProcumentOrder;
use App\Models\SupplierCatalogDetail;
use App\Models\SupplierCatalogMaster;
use App\Models\SupplierMaster;
use App\Repositories\SupplierCatalogMasterRepository;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Validation\Rule;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierCatalogMasterController
 * @package App\Http\Controllers\API
 */

class SupplierCatalogMasterAPIController extends AppBaseController
{
    /** @var  SupplierCatalogMasterRepository */
    private $supplierCatalogMasterRepository;

    public function __construct(SupplierCatalogMasterRepository $supplierCatalogMasterRepo)
    {
        $this->supplierCatalogMasterRepository = $supplierCatalogMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierCatalogMasters",
     *      summary="Get a listing of the SupplierCatalogMasters.",
     *      tags={"SupplierCatalogMaster"},
     *      description="Get all SupplierCatalogMasters",
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
     *                  @SWG\Items(ref="#/definitions/SupplierCatalogMaster")
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
        $this->supplierCatalogMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierCatalogMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierCatalogMasters = $this->supplierCatalogMasterRepository->all();

        return $this->sendResponse($supplierCatalogMasters->toArray(), trans('custom.supplier_catalog_masters_retrieved_successfully'));
    }

    /**
     * @param CreateSupplierCatalogMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/supplierCatalogMasters",
     *      summary="Store a newly created SupplierCatalogMaster in storage",
     *      tags={"SupplierCatalogMaster"},
     *      description="Store SupplierCatalogMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierCatalogMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierCatalogMaster")
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
     *                  ref="#/definitions/SupplierCatalogMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierCatalogMasterAPIRequest $request)
    {
        $input = $request->all();

        $messages = [
            'catalogID.required' => 'catalog Code field is required'
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

        $hasCatalogID = SupplierCatalogMaster::where('catalogID',$input['catalogID'])
            ->where(function ($q){
                $q->whereNull('isDeleted')
                    ->orWhere('isDeleted',0);
            })
            ->exists();

        if($hasCatalogID){
            return $this->sendError('Duplicate Catalog Code Found',500);
        }

        if(isset($input['fromDate'])){
            $input['fromDate'] = new Carbon($input['fromDate']);
        }

        if(isset($input['toDate'])){
            $input['toDate'] = new Carbon($input['toDate']);
        }

        $employee = Helper::getEmployeeInfo();
        $input['createdBy'] = $employee->employeeSystemID;
        $supplierCatalogMaster = $this->supplierCatalogMasterRepository->create($input);

        return $this->sendResponse($supplierCatalogMaster->toArray(), trans('custom.supplier_catalog_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierCatalogMasters/{id}",
     *      summary="Display the specified SupplierCatalogMaster",
     *      tags={"SupplierCatalogMaster"},
     *      description="Get SupplierCatalogMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierCatalogMaster",
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
     *                  ref="#/definitions/SupplierCatalogMaster"
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
        /** @var SupplierCatalogMaster $supplierCatalogMaster */
        $supplierCatalogMaster = $this->supplierCatalogMasterRepository->with(['details' => function($query){
            $query->where(function ($q){
                $q->whereNull('isDeleted')
                    ->orWhere('isDeleted',0);
            })->with(['uom_default','item_by','local_currency']);
        }])->findWithoutFail($id);

        if (empty($supplierCatalogMaster)) {
            return $this->sendError(trans('custom.supplier_catalog_master_not_found'));
        }

        return $this->sendResponse($supplierCatalogMaster->toArray(), trans('custom.supplier_catalog_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSupplierCatalogMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/supplierCatalogMasters/{id}",
     *      summary="Update the specified SupplierCatalogMaster in storage",
     *      tags={"SupplierCatalogMaster"},
     *      description="Update SupplierCatalogMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierCatalogMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierCatalogMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierCatalogMaster")
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
     *                  ref="#/definitions/SupplierCatalogMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierCatalogMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input,'created_by');
        $messages = [
            'catalogID.required' => 'catalog Code field is required'
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
        $hasCatalogID = SupplierCatalogMaster::where('catalogID',$input['catalogID'])
            ->where('supplierCatalogMasterID','!=',$id)
            ->where(function ($q){
                $q->whereNull('isDeleted')
                    ->orWhere('isDeleted',0);
            })
            ->exists();

        if($hasCatalogID){
            return $this->sendError('Duplicate Catalog Code Found', 500);
        }



        /** @var SupplierCatalogMaster $supplierCatalogMaster */
        $supplierCatalogMaster = $this->supplierCatalogMasterRepository->findWithoutFail($id);

        if (empty($supplierCatalogMaster)) {
            return $this->sendError(trans('custom.supplier_catalog_master_not_found'));
        }

        if (isset($input['isDeleted']) && $input['isDeleted'] == 1 && $supplierCatalogMaster->isDeleted == 0) {
            if ($supplierCatalogMaster->isActive == 1) {
                return $this->sendError(trans('custom.active_catalog_cannot_delete'), 406);
            }
        }

        if(isset($input['fromDate'])){
            $input['fromDate'] = new Carbon($input['fromDate']);
        }

        if(isset($input['toDate'])){
            $input['toDate'] = new Carbon($input['toDate']);
        }

        if((isset($input['isActive']) && $input['isActive']==1)){
            SupplierCatalogMaster::where('supplierID',$supplierCatalogMaster->supplierID)->update(['isActive' => 0]);
        }

        $employee = Helper::getEmployeeInfo();
        $input['modifiedBy'] = $employee->employeeSystemID;

        $supplierCatalogMaster = $this->supplierCatalogMasterRepository->update($input, $id);

        return $this->sendResponse($supplierCatalogMaster->toArray(), trans('custom.suppliercatalogmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/supplierCatalogMasters/{id}",
     *      summary="Remove the specified SupplierCatalogMaster from storage",
     *      tags={"SupplierCatalogMaster"},
     *      description="Delete SupplierCatalogMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierCatalogMaster",
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
        /** @var SupplierCatalogMaster $supplierCatalogMaster */
        $supplierCatalogMaster = $this->supplierCatalogMasterRepository->findWithoutFail($id);

        if (empty($supplierCatalogMaster)) {
            return $this->sendError(trans('custom.supplier_catalog_master_not_found'));
        }

        $supplierCatalogMaster->delete();

        return $this->sendResponse($id, trans('custom.supplier_catalog_master_deleted_successfully'));
    }

    public function getAllCatalogsByCompany(Request $request){

        $input = $request->all();
        $companyId = $request->companyId;
        $documentId = $request->documentId;
        $supplierID = $request->supplierID;
        $isGroup = Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companies = Helper::getGroupCompany($companyId);
        } else {
            $companies = [$companyId];
        }

        $supplierCatalog = SupplierCatalogMaster::when($input['deletedFlag'] == false, function($query) {
                $query->where(function ($query){
                            $query->whereNull('isDeleted')
                                ->orWhere('isDeleted',0);
                        });
            })
            ->where('documentSystemID',$documentId)
            ->where('supplierID',$supplierID)
            ->with(['created_by']);

        $search = $request->input('search.value');
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $supplierCatalog = $supplierCatalog->where(function ($query) use ($search) {
                $query->where('catalogID', 'LIKE', "%{$search}%")
                    ->orWhere('catalogName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($supplierCatalog)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('supplierCatalogMasterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getItemsOptionsSupplierCatalog(Request $request)
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
        return $this->sendResponse($items->toArray(), trans('custom.data_retrieved_successfully'));
    }

    function getSupplierCatalogDetailBySupplierItem(Request $request){

        $input  = $request->all();

        $company_id = $input['companyId'];
        $po_id = $input['id'];
        $item_id = $input['item_id'];

        $po = ProcumentOrder::find($po_id);
        $poDate = Carbon::parse($po->createdDateTime)->format('y-m-d');
        $supplierID = $po->supplierID;
        $catalog = SupplierCatalogDetail::whereHas('master', function($query) use($company_id,$supplierID,$poDate){
            $query->whereDate('fromDate','<=',$poDate)
                ->whereDate('toDate','>=',$poDate)
                ->where('supplierID',$supplierID)
                ->where('isActive',1);
        })
            ->where('itemCodeSystem',$item_id)
            ->where(function ($q){
                $q->whereNull('isDeleted')
                    ->orWhere('isDeleted',0);
            })
            ->with(['uom_default','item_by','local_currency','master'])
            ->get();

        return $this->sendResponse($catalog->toArray(),trans('custom.catalog_retrieved_successfully'));

    }

    function getSupplierCatalogDetailBySupplierAllItem(Request $request){

        $input  = $request->all();

        $company_id = $input['companyId'];
        $po_id = $input['id'];
        $details = $input['details'];
        $item_array = [];
        foreach ($details as $row){
            $isCatalogShowed = isset($row['isCatalogShowed'])? $row['isCatalogShowed'] : false;
            if(isset($row['isChecked']) && $row['isChecked'] && !$isCatalogShowed){
                $item_array[] = $row['itemCode'];
            }
        }

        $catalog = $this->getCatelogDataForPO($po_id, $item_array);


        return $this->sendResponse($catalog,trans('custom.catalog_retrieved_successfully'));

    }

    public function getCatelogDataForPO($po_id, $item_array)
    {
        $po = ProcumentOrder::find($po_id);
        $poDate = Carbon::parse($po->createdDateTime)->format('y-m-d');
        $supplierID = $po->supplierID;

        return $catalog = SupplierCatalogMaster::whereDate('fromDate','<=',$poDate)
            ->whereDate('toDate','>=',$poDate)
            ->where('supplierID',$supplierID)
            ->where('isActive',1)
            ->where(function ($q){
                $q->whereNull('isDeleted')
                    ->orWhere('isDeleted',0);
            })
            ->whereHas('details', function ($query) use($item_array){
                $query->whereIn('itemCodeSystem',$item_array)
                    ->where(function ($q){
                        $q->whereNull('isDeleted')
                            ->orWhere('isDeleted',0);
                    });
            })
            ->with(['details'=> function($query) use($item_array){
                $query->whereIn('itemCodeSystem',$item_array)
                    ->where(function ($q){
                        $q->whereNull('isDeleted')
                            ->orWhere('isDeleted',0);
                    })
                    ->with(['uom_default','item_by','local_currency','master']);
            }])->first();
    }

    function getSupplierCatalogDetailBySupplierItemForPo(Request $request){

        $input  = $request->all();

        $company_id = $input['companyId'];
        $po_id = $input['id'];
        $item_array[] = $input['item_id'];

        $catalog = $this->getCatelogDataForPO($po_id, $item_array);


        return $this->sendResponse($catalog,trans('custom.catalog_retrieved_successfully'));

    }
}
