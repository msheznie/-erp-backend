<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceUploadDetailAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceUploadDetailAPIRequest;
use App\Models\CustomerInvoiceUploadDetail;
use App\Repositories\CustomerInvoiceUploadDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerInvoiceUploadDetailController
 * @package App\Http\Controllers\API
 */

class CustomerInvoiceUploadDetailAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceUploadDetailRepository */
    private $customerInvoiceUploadDetailRepository;

    public function __construct(CustomerInvoiceUploadDetailRepository $customerInvoiceUploadDetailRepo)
    {
        $this->customerInvoiceUploadDetailRepository = $customerInvoiceUploadDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/customerInvoiceUploadDetails",
     *      summary="getCustomerInvoiceUploadDetailList",
     *      tags={"CustomerInvoiceUploadDetail"},
     *      description="Get all CustomerInvoiceUploadDetails",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/CustomerInvoiceUploadDetail")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->customerInvoiceUploadDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceUploadDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceUploadDetails = $this->customerInvoiceUploadDetailRepository->all();

        return $this->sendResponse($customerInvoiceUploadDetails->toArray(), 'Customer Invoice Upload Details retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/customerInvoiceUploadDetails",
     *      summary="createCustomerInvoiceUploadDetail",
     *      tags={"CustomerInvoiceUploadDetail"},
     *      description="Create CustomerInvoiceUploadDetail",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/CustomerInvoiceUploadDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceUploadDetailAPIRequest $request)
    {
        $input = $request->all();

        $customerInvoiceUploadDetail = $this->customerInvoiceUploadDetailRepository->create($input);

        return $this->sendResponse($customerInvoiceUploadDetail->toArray(), 'Customer Invoice Upload Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/customerInvoiceUploadDetails/{id}",
     *      summary="getCustomerInvoiceUploadDetailItem",
     *      tags={"CustomerInvoiceUploadDetail"},
     *      description="Get CustomerInvoiceUploadDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceUploadDetail",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/CustomerInvoiceUploadDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var CustomerInvoiceUploadDetail $customerInvoiceUploadDetail */
        $customerInvoiceUploadDetail = $this->customerInvoiceUploadDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceUploadDetail)) {
            return $this->sendError('Customer Invoice Upload Detail not found');
        }

        return $this->sendResponse($customerInvoiceUploadDetail->toArray(), 'Customer Invoice Upload Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/customerInvoiceUploadDetails/{id}",
     *      summary="updateCustomerInvoiceUploadDetail",
     *      tags={"CustomerInvoiceUploadDetail"},
     *      description="Update CustomerInvoiceUploadDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceUploadDetail",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/CustomerInvoiceUploadDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceUploadDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerInvoiceUploadDetail $customerInvoiceUploadDetail */
        $customerInvoiceUploadDetail = $this->customerInvoiceUploadDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceUploadDetail)) {
            return $this->sendError('Customer Invoice Upload Detail not found');
        }

        $customerInvoiceUploadDetail = $this->customerInvoiceUploadDetailRepository->update($input, $id);

        return $this->sendResponse($customerInvoiceUploadDetail->toArray(), 'CustomerInvoiceUploadDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/customerInvoiceUploadDetails/{id}",
     *      summary="deleteCustomerInvoiceUploadDetail",
     *      tags={"CustomerInvoiceUploadDetail"},
     *      description="Delete CustomerInvoiceUploadDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceUploadDetail",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var CustomerInvoiceUploadDetail $customerInvoiceUploadDetail */
        $customerInvoiceUploadDetail = $this->customerInvoiceUploadDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceUploadDetail)) {
            return $this->sendError('Customer Invoice Upload Detail not found');
        }

        $customerInvoiceUploadDetail->delete();

        return $this->sendSuccess('Customer Invoice Upload Detail deleted successfully');
    }
}
