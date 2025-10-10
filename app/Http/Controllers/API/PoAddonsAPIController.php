<?php
/**
 * =============================================
 * -- File Name : PoAddonsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Po Payment Terms
 * -- Author : Mohamed Nazir
 * -- Create date : 20 - July 2018
 * -- Description : This file contains the all CRUD for Po Addons
 * -- REVISION HISTORY
 * -- Date: 20-July 2018 By: Nazir Description: Added new functions named as getProcumentOrderAddons(),
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePoAddonsAPIRequest;
use App\Http\Requests\API\UpdatePoAddonsAPIRequest;
use App\Models\PoAddons;
use App\Models\ProcumentOrder;
use App\Repositories\PoAddonsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\PurchaseOrderDetails;
use Illuminate\Support\Facades\DB;
/**
 * Class PoAddonsController
 * @package App\Http\Controllers\API
 */

class PoAddonsAPIController extends AppBaseController
{
    /** @var  PoAddonsRepository */
    private $poAddonsRepository;

    public function __construct(PoAddonsRepository $poAddonsRepo)
    {
        $this->poAddonsRepository = $poAddonsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/poAddons",
     *      summary="Get a listing of the PoAddons.",
     *      tags={"PoAddons"},
     *      description="Get all PoAddons",
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
     *                  @SWG\Items(ref="#/definitions/PoAddons")
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
        $this->poAddonsRepository->pushCriteria(new RequestCriteria($request));
        $this->poAddonsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $poAddons = $this->poAddonsRepository->all();

        return $this->sendResponse($poAddons->toArray(), trans('custom.po_addons_retrieved_successfully'));
    }

    /**
     * @param CreatePoAddonsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/poAddons",
     *      summary="Store a newly created PoAddons in storage",
     *      tags={"PoAddons"},
     *      description="Store PoAddons",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PoAddons that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PoAddons")
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
     *                  ref="#/definitions/PoAddons"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePoAddonsAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $input['poId'])
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError(trans('custom.purchase_order_not_found'));
        }

        $input['supplierID'] =  $purchaseOrder->supplierID;
        $input['currencyID'] =  $purchaseOrder->supplierTransactionCurrencyID;
        $input['glCode'] =  0;

        $poAddons = $this->poAddonsRepository->create($input);

        return $this->sendResponse($poAddons->toArray(), trans('custom.po_addon_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/poAddons/{id}",
     *      summary="Display the specified PoAddons",
     *      tags={"PoAddons"},
     *      description="Get PoAddons",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoAddons",
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
     *                  ref="#/definitions/PoAddons"
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
        /** @var PoAddons $poAddons */
        $poAddons = $this->poAddonsRepository->findWithoutFail($id);

        if (empty($poAddons)) {
            return $this->sendError(trans('custom.po_addons_not_found'));
        }

        return $this->sendResponse($poAddons->toArray(), trans('custom.po_addons_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePoAddonsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/poAddons/{id}",
     *      summary="Update the specified PoAddons in storage",
     *      tags={"PoAddons"},
     *      description="Update PoAddons",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoAddons",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PoAddons that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PoAddons")
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
     *                  ref="#/definitions/PoAddons"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePoAddonsAPIRequest $request)
    {
       
        DB::beginTransaction();
        try {
   

                   
        $input = $request->all();

        $input = array_except($input, ['category']);

        $input = $this->convertArrayToValue($input);

        /** @var PoAddons $poAddons */
        $poAddons = $this->poAddonsRepository->findWithoutFail($id);

        if (empty($poAddons)) {
            return $this->sendError(trans('custom.po_addons_not_found'));
        }
        
        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $input['poId'])
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError(trans('custom.purchase_order_not_found'));
        }

        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
        ->where('purchaseOrderMasterID', $input['poId'])
        ->first();

    

        $poMasterVATSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(VATAmount * noQty),0) as masterTotalVATSum'))
        ->where('purchaseOrderMasterID', $input['poId'])
        ->first();

      

        $poAddonMasterSum = PoAddons::select(DB::raw('COALESCE(SUM(amount),0) as addonTotalSum'))
        ->where('poId', $input['poId'])
        ->first();

     

        $new_add_on_amount = $poAddonMasterSum->addonTotalSum + $input['amount'];
        $poMasterSumDeductedNotRounded = ($poMasterSum['masterTotalSum'] + $new_add_on_amount + $poMasterVATSum['masterTotalVATSum'] - $purchaseOrder->poDiscountAmount);

     
        $input['supplierID'] =  $purchaseOrder->supplierID;
        $input['currencyID'] =  $purchaseOrder->supplierTransactionCurrencyID;
        $input['glCode'] =  0;



        $purchaseOrder->poTotalSupplierTransactionCurrency = \Helper::roundValue($poMasterSumDeductedNotRounded);
        $purchaseOrder->update();

        $poAddons = $this->poAddonsRepository->update($input, $id);

        DB::commit();
        return $this->sendResponse($poAddons->toArray(), trans('custom.poaddons_updated_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
       
       

    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/poAddons/{id}",
     *      summary="Remove the specified PoAddons from storage",
     *      tags={"PoAddons"},
     *      description="Delete PoAddons",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoAddons",
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
        /** @var PoAddons $poAddons */

        DB::beginTransaction();
        try {
    
            $poAddons = $this->poAddonsRepository->findWithoutFail($id);

            $po_id = $poAddons->poId;
            $deduct_amount = $poAddons->amount;
    
            $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $po_id)
            ->first();
            $purchaseOrder->poTotalSupplierTransactionCurrency -=$deduct_amount;
            $purchaseOrder->update();
     
            if (empty($poAddons)) {
                return $this->sendError(trans('custom.po_addons_not_found'));
            }
    
            $poAddons->delete();
            DB::commit();
            return $this->sendResponse($id, trans('custom.po_addons_deleted_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }


    public function getProcumentOrderAddons(Request $request)
    {
        $input = $request->all();

        $orderAddons = PoAddons::where('poId', $input['purchaseOrderID'])
            ->with(['category'])
            ->orderBy('idpoAddons', 'DESC')
            ->get();

        return $this->sendResponse($orderAddons->toArray(), trans('custom.data_retrieved_successfully'));
    }
}
