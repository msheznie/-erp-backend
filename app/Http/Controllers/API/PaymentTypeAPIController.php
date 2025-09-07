<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymentTypeAPIRequest;
use App\Http\Requests\API\UpdatePaymentTypeAPIRequest;
use App\Models\PaymentType;
use App\Repositories\PaymentTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaymentTypeController
 * @package App\Http\Controllers\API
 */

class PaymentTypeAPIController extends AppBaseController
{
    /** @var  PaymentTypeRepository */
    private $paymentTypeRepository;

    public function __construct(PaymentTypeRepository $paymentTypeRepo)
    {
        $this->paymentTypeRepository = $paymentTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paymentTypes",
     *      summary="Get a listing of the PaymentTypes.",
     *      tags={"PaymentType"},
     *      description="Get all PaymentTypes",
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
     *                  @SWG\Items(ref="#/definitions/PaymentType")
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
        $this->paymentTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->paymentTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paymentTypes = $this->paymentTypeRepository->all();

        return $this->sendResponse($paymentTypes->toArray(), trans('custom.payment_types_retrieved_successfully'));
    }

    /**
     * @param CreatePaymentTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paymentTypes",
     *      summary="Store a newly created PaymentType in storage",
     *      tags={"PaymentType"},
     *      description="Store PaymentType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaymentType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaymentType")
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
     *                  ref="#/definitions/PaymentType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaymentTypeAPIRequest $request)
    {
        $input = $request->all();

        $paymentType = $this->paymentTypeRepository->create($input);

        return $this->sendResponse($paymentType->toArray(), trans('custom.payment_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paymentTypes/{id}",
     *      summary="Display the specified PaymentType",
     *      tags={"PaymentType"},
     *      description="Get PaymentType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentType",
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
     *                  ref="#/definitions/PaymentType"
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
        /** @var PaymentType $paymentType */
        $paymentType = $this->paymentTypeRepository->findWithoutFail($id);

        if (empty($paymentType)) {
            return $this->sendError(trans('custom.payment_type_not_found'));
        }

        return $this->sendResponse($paymentType->toArray(), trans('custom.payment_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePaymentTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paymentTypes/{id}",
     *      summary="Update the specified PaymentType in storage",
     *      tags={"PaymentType"},
     *      description="Update PaymentType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaymentType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaymentType")
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
     *                  ref="#/definitions/PaymentType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaymentTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaymentType $paymentType */
        $paymentType = $this->paymentTypeRepository->findWithoutFail($id);

        if (empty($paymentType)) {
            return $this->sendError(trans('custom.payment_type_not_found'));
        }

        $paymentType = $this->paymentTypeRepository->update($input, $id);

        return $this->sendResponse($paymentType->toArray(), trans('custom.paymenttype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paymentTypes/{id}",
     *      summary="Remove the specified PaymentType from storage",
     *      tags={"PaymentType"},
     *      description="Delete PaymentType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentType",
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
        /** @var PaymentType $paymentType */
        $paymentType = $this->paymentTypeRepository->findWithoutFail($id);

        if (empty($paymentType)) {
            return $this->sendError(trans('custom.payment_type_not_found'));
        }

        $paymentType->delete();

        return $this->sendSuccess('Payment Type deleted successfully');
    }
}
