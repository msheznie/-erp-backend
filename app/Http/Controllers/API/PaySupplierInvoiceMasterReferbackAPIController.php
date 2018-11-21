<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaySupplierInvoiceMasterReferbackAPIRequest;
use App\Http\Requests\API\UpdatePaySupplierInvoiceMasterReferbackAPIRequest;
use App\Models\PaySupplierInvoiceMasterReferback;
use App\Repositories\PaySupplierInvoiceMasterReferbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaySupplierInvoiceMasterReferbackController
 * @package App\Http\Controllers\API
 */

class PaySupplierInvoiceMasterReferbackAPIController extends AppBaseController
{
    /** @var  PaySupplierInvoiceMasterReferbackRepository */
    private $paySupplierInvoiceMasterReferbackRepository;

    public function __construct(PaySupplierInvoiceMasterReferbackRepository $paySupplierInvoiceMasterReferbackRepo)
    {
        $this->paySupplierInvoiceMasterReferbackRepository = $paySupplierInvoiceMasterReferbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceMasterReferbacks",
     *      summary="Get a listing of the PaySupplierInvoiceMasterReferbacks.",
     *      tags={"PaySupplierInvoiceMasterReferback"},
     *      description="Get all PaySupplierInvoiceMasterReferbacks",
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
     *                  @SWG\Items(ref="#/definitions/PaySupplierInvoiceMasterReferback")
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
        $this->paySupplierInvoiceMasterReferbackRepository->pushCriteria(new RequestCriteria($request));
        $this->paySupplierInvoiceMasterReferbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paySupplierInvoiceMasterReferbacks = $this->paySupplierInvoiceMasterReferbackRepository->all();

        return $this->sendResponse($paySupplierInvoiceMasterReferbacks->toArray(), 'Pay Supplier Invoice Master Referbacks retrieved successfully');
    }

    /**
     * @param CreatePaySupplierInvoiceMasterReferbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paySupplierInvoiceMasterReferbacks",
     *      summary="Store a newly created PaySupplierInvoiceMasterReferback in storage",
     *      tags={"PaySupplierInvoiceMasterReferback"},
     *      description="Store PaySupplierInvoiceMasterReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceMasterReferback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceMasterReferback")
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
     *                  ref="#/definitions/PaySupplierInvoiceMasterReferback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaySupplierInvoiceMasterReferbackAPIRequest $request)
    {
        $input = $request->all();

        $paySupplierInvoiceMasterReferbacks = $this->paySupplierInvoiceMasterReferbackRepository->create($input);

        return $this->sendResponse($paySupplierInvoiceMasterReferbacks->toArray(), 'Pay Supplier Invoice Master Referback saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceMasterReferbacks/{id}",
     *      summary="Display the specified PaySupplierInvoiceMasterReferback",
     *      tags={"PaySupplierInvoiceMasterReferback"},
     *      description="Get PaySupplierInvoiceMasterReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceMasterReferback",
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
     *                  ref="#/definitions/PaySupplierInvoiceMasterReferback"
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
        /** @var PaySupplierInvoiceMasterReferback $paySupplierInvoiceMasterReferback */
        $paySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMasterReferback)) {
            return $this->sendError('Pay Supplier Invoice Master Referback not found');
        }

        return $this->sendResponse($paySupplierInvoiceMasterReferback->toArray(), 'Pay Supplier Invoice Master Referback retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePaySupplierInvoiceMasterReferbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paySupplierInvoiceMasterReferbacks/{id}",
     *      summary="Update the specified PaySupplierInvoiceMasterReferback in storage",
     *      tags={"PaySupplierInvoiceMasterReferback"},
     *      description="Update PaySupplierInvoiceMasterReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceMasterReferback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceMasterReferback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceMasterReferback")
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
     *                  ref="#/definitions/PaySupplierInvoiceMasterReferback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaySupplierInvoiceMasterReferbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaySupplierInvoiceMasterReferback $paySupplierInvoiceMasterReferback */
        $paySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMasterReferback)) {
            return $this->sendError('Pay Supplier Invoice Master Referback not found');
        }

        $paySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepository->update($input, $id);

        return $this->sendResponse($paySupplierInvoiceMasterReferback->toArray(), 'PaySupplierInvoiceMasterReferback updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paySupplierInvoiceMasterReferbacks/{id}",
     *      summary="Remove the specified PaySupplierInvoiceMasterReferback from storage",
     *      tags={"PaySupplierInvoiceMasterReferback"},
     *      description="Delete PaySupplierInvoiceMasterReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceMasterReferback",
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
        /** @var PaySupplierInvoiceMasterReferback $paySupplierInvoiceMasterReferback */
        $paySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMasterReferback)) {
            return $this->sendError('Pay Supplier Invoice Master Referback not found');
        }

        $paySupplierInvoiceMasterReferback->delete();

        return $this->sendResponse($id, 'Pay Supplier Invoice Master Referback deleted successfully');
    }
}
