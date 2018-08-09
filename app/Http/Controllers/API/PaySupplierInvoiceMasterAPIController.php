<?php
/**
 * =============================================
 * -- File Name : PaySupplierInvoiceMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  PaySupplierInvoiceMaster
 * -- Author : Mohamed Nazir
 * -- Create date : 08 - August 2018
 * -- Description : This file contains the all CRUD for Pay Supplier Invoice Master
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaySupplierInvoiceMasterAPIRequest;
use App\Http\Requests\API\UpdatePaySupplierInvoiceMasterAPIRequest;
use App\Models\PaySupplierInvoiceMaster;
use App\Repositories\PaySupplierInvoiceMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaySupplierInvoiceMasterController
 * @package App\Http\Controllers\API
 */

class PaySupplierInvoiceMasterAPIController extends AppBaseController
{
    /** @var  PaySupplierInvoiceMasterRepository */
    private $paySupplierInvoiceMasterRepository;

    public function __construct(PaySupplierInvoiceMasterRepository $paySupplierInvoiceMasterRepo)
    {
        $this->paySupplierInvoiceMasterRepository = $paySupplierInvoiceMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceMasters",
     *      summary="Get a listing of the PaySupplierInvoiceMasters.",
     *      tags={"PaySupplierInvoiceMaster"},
     *      description="Get all PaySupplierInvoiceMasters",
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
     *                  @SWG\Items(ref="#/definitions/PaySupplierInvoiceMaster")
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
        $this->paySupplierInvoiceMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->paySupplierInvoiceMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paySupplierInvoiceMasters = $this->paySupplierInvoiceMasterRepository->all();

        return $this->sendResponse($paySupplierInvoiceMasters->toArray(), 'Pay Supplier Invoice Masters retrieved successfully');
    }

    /**
     * @param CreatePaySupplierInvoiceMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paySupplierInvoiceMasters",
     *      summary="Store a newly created PaySupplierInvoiceMaster in storage",
     *      tags={"PaySupplierInvoiceMaster"},
     *      description="Store PaySupplierInvoiceMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceMaster")
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
     *                  ref="#/definitions/PaySupplierInvoiceMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaySupplierInvoiceMasterAPIRequest $request)
    {
        $input = $request->all();

        $paySupplierInvoiceMasters = $this->paySupplierInvoiceMasterRepository->create($input);

        return $this->sendResponse($paySupplierInvoiceMasters->toArray(), 'Pay Supplier Invoice Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceMasters/{id}",
     *      summary="Display the specified PaySupplierInvoiceMaster",
     *      tags={"PaySupplierInvoiceMaster"},
     *      description="Get PaySupplierInvoiceMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceMaster",
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
     *                  ref="#/definitions/PaySupplierInvoiceMaster"
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
        /** @var PaySupplierInvoiceMaster $paySupplierInvoiceMaster */
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMaster)) {
            return $this->sendError('Pay Supplier Invoice Master not found');
        }

        return $this->sendResponse($paySupplierInvoiceMaster->toArray(), 'Pay Supplier Invoice Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePaySupplierInvoiceMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paySupplierInvoiceMasters/{id}",
     *      summary="Update the specified PaySupplierInvoiceMaster in storage",
     *      tags={"PaySupplierInvoiceMaster"},
     *      description="Update PaySupplierInvoiceMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceMaster")
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
     *                  ref="#/definitions/PaySupplierInvoiceMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaySupplierInvoiceMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaySupplierInvoiceMaster $paySupplierInvoiceMaster */
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMaster)) {
            return $this->sendError('Pay Supplier Invoice Master not found');
        }

        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->update($input, $id);

        return $this->sendResponse($paySupplierInvoiceMaster->toArray(), 'PaySupplierInvoiceMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paySupplierInvoiceMasters/{id}",
     *      summary="Remove the specified PaySupplierInvoiceMaster from storage",
     *      tags={"PaySupplierInvoiceMaster"},
     *      description="Delete PaySupplierInvoiceMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceMaster",
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
        /** @var PaySupplierInvoiceMaster $paySupplierInvoiceMaster */
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMaster)) {
            return $this->sendError('Pay Supplier Invoice Master not found');
        }

        $paySupplierInvoiceMaster->delete();

        return $this->sendResponse($id, 'Pay Supplier Invoice Master deleted successfully');
    }

    public function getPaymentVoucherMaster(Request $request)
    {
        $input = $request->all();

        $output = PaySupplierInvoiceMaster::where('PayMasterAutoId', $input['PayMasterAutoId'])
            ->first();

        return $this->sendResponse($output, 'Data retrieved successfully');

    }
}
