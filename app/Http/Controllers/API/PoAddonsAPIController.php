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

        return $this->sendResponse($poAddons->toArray(), 'Po Addons retrieved successfully');
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
            return $this->sendError('Purchase Order not found');
        }

        $input['supplierID'] =  $purchaseOrder->supplierID;
        $input['currencyID'] =  $purchaseOrder->supplierTransactionCurrencyID;
        $input['glCode'] =  0;

        $poAddons = $this->poAddonsRepository->create($input);

        return $this->sendResponse($poAddons->toArray(), 'Po Addon saved successfully');
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
            return $this->sendError('Po Addons not found');
        }

        return $this->sendResponse($poAddons->toArray(), 'Po Addons retrieved successfully');
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
        $input = $request->all();

        $input = array_except($input, ['category']);

        $input = $this->convertArrayToValue($input);

        /** @var PoAddons $poAddons */
        $poAddons = $this->poAddonsRepository->findWithoutFail($id);

        if (empty($poAddons)) {
            return $this->sendError('Po Addons not found');
        }

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $input['poId'])
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $input['supplierID'] =  $purchaseOrder->supplierID;
        $input['currencyID'] =  $purchaseOrder->supplierTransactionCurrencyID;
        $input['glCode'] =  0;

        $poAddons = $this->poAddonsRepository->update($input, $id);

        return $this->sendResponse($poAddons->toArray(), 'PoAddons updated successfully');
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
        $poAddons = $this->poAddonsRepository->findWithoutFail($id);

        if (empty($poAddons)) {
            return $this->sendError('Po Addons not found');
        }

        $poAddons->delete();

        return $this->sendResponse($id, 'Po Addons deleted successfully');
    }


    public function getProcumentOrderAddons(Request $request)
    {
        $input = $request->all();

        $orderAddons = PoAddons::where('poId', $input['purchaseOrderID'])
            ->with(['category'])
            ->orderBy('idpoAddons', 'DESC')
            ->get();

        return $this->sendResponse($orderAddons->toArray(), 'Data retrieved successfully');
    }
}
