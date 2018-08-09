<?php
/**
 * =============================================
 * -- File Name : PaySupplierInvoiceDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  PaySupplierInvoiceDetail
 * -- Author : Mohamed Nazir
 * -- Create date : 09 - August 2018
 * -- Description : This file contains the all CRUD for Pay Pay Supplier Invoice Detail
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaySupplierInvoiceDetailAPIRequest;
use App\Http\Requests\API\UpdatePaySupplierInvoiceDetailAPIRequest;
use App\Models\PaySupplierInvoiceDetail;
use App\Repositories\PaySupplierInvoiceDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaySupplierInvoiceDetailController
 * @package App\Http\Controllers\API
 */

class PaySupplierInvoiceDetailAPIController extends AppBaseController
{
    /** @var  PaySupplierInvoiceDetailRepository */
    private $paySupplierInvoiceDetailRepository;

    public function __construct(PaySupplierInvoiceDetailRepository $paySupplierInvoiceDetailRepo)
    {
        $this->paySupplierInvoiceDetailRepository = $paySupplierInvoiceDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceDetails",
     *      summary="Get a listing of the PaySupplierInvoiceDetails.",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Get all PaySupplierInvoiceDetails",
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
     *                  @SWG\Items(ref="#/definitions/PaySupplierInvoiceDetail")
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
        $this->paySupplierInvoiceDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->paySupplierInvoiceDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paySupplierInvoiceDetails = $this->paySupplierInvoiceDetailRepository->all();

        return $this->sendResponse($paySupplierInvoiceDetails->toArray(), 'Pay Supplier Invoice Details retrieved successfully');
    }

    /**
     * @param CreatePaySupplierInvoiceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paySupplierInvoiceDetails",
     *      summary="Store a newly created PaySupplierInvoiceDetail in storage",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Store PaySupplierInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceDetail")
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
     *                  ref="#/definitions/PaySupplierInvoiceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaySupplierInvoiceDetailAPIRequest $request)
    {
        $input = $request->all();

        $paySupplierInvoiceDetails = $this->paySupplierInvoiceDetailRepository->create($input);

        return $this->sendResponse($paySupplierInvoiceDetails->toArray(), 'Pay Supplier Invoice Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceDetails/{id}",
     *      summary="Display the specified PaySupplierInvoiceDetail",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Get PaySupplierInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceDetail",
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
     *                  ref="#/definitions/PaySupplierInvoiceDetail"
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
        /** @var PaySupplierInvoiceDetail $paySupplierInvoiceDetail */
        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetail)) {
            return $this->sendError('Pay Supplier Invoice Detail not found');
        }

        return $this->sendResponse($paySupplierInvoiceDetail->toArray(), 'Pay Supplier Invoice Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePaySupplierInvoiceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paySupplierInvoiceDetails/{id}",
     *      summary="Update the specified PaySupplierInvoiceDetail in storage",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Update PaySupplierInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceDetail")
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
     *                  ref="#/definitions/PaySupplierInvoiceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaySupplierInvoiceDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaySupplierInvoiceDetail $paySupplierInvoiceDetail */
        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetail)) {
            return $this->sendError('Pay Supplier Invoice Detail not found');
        }

        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->update($input, $id);

        return $this->sendResponse($paySupplierInvoiceDetail->toArray(), 'PaySupplierInvoiceDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paySupplierInvoiceDetails/{id}",
     *      summary="Remove the specified PaySupplierInvoiceDetail from storage",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Delete PaySupplierInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceDetail",
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
        /** @var PaySupplierInvoiceDetail $paySupplierInvoiceDetail */
        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetail)) {
            return $this->sendError('Pay Supplier Invoice Detail not found');
        }

        $paySupplierInvoiceDetail->delete();

        return $this->sendResponse($id, 'Pay Supplier Invoice Detail deleted successfully');
    }
}
