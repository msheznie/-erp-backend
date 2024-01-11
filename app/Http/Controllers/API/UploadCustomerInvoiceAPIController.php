<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUploadCustomerInvoiceAPIRequest;
use App\Http\Requests\API\UpdateUploadCustomerInvoiceAPIRequest;
use App\Models\UploadCustomerInvoice;
use App\Repositories\UploadCustomerInvoiceRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class UploadCustomerInvoiceController
 * @package App\Http\Controllers\API
 */

class UploadCustomerInvoiceAPIController extends AppBaseController
{
    /** @var  UploadCustomerInvoiceRepository */
    private $uploadCustomerInvoiceRepository;

    public function __construct(UploadCustomerInvoiceRepository $uploadCustomerInvoiceRepo)
    {
        $this->uploadCustomerInvoiceRepository = $uploadCustomerInvoiceRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/uploadCustomerInvoices",
     *      summary="getUploadCustomerInvoiceList",
     *      tags={"UploadCustomerInvoice"},
     *      description="Get all UploadCustomerInvoices",
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
     *                  @OA\Items(ref="#/definitions/UploadCustomerInvoice")
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
        $this->uploadCustomerInvoiceRepository->pushCriteria(new RequestCriteria($request));
        $this->uploadCustomerInvoiceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $uploadCustomerInvoices = $this->uploadCustomerInvoiceRepository->all();

        return $this->sendResponse($uploadCustomerInvoices->toArray(), 'Upload Customer Invoices retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/uploadCustomerInvoices",
     *      summary="createUploadCustomerInvoice",
     *      tags={"UploadCustomerInvoice"},
     *      description="Create UploadCustomerInvoice",
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
     *                  ref="#/definitions/UploadCustomerInvoice"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateUploadCustomerInvoiceAPIRequest $request)
    {
        $input = $request->all();

        $uploadCustomerInvoice = $this->uploadCustomerInvoiceRepository->create($input);

        return $this->sendResponse($uploadCustomerInvoice->toArray(), 'Upload Customer Invoice saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/uploadCustomerInvoices/{id}",
     *      summary="getUploadCustomerInvoiceItem",
     *      tags={"UploadCustomerInvoice"},
     *      description="Get UploadCustomerInvoice",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of UploadCustomerInvoice",
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
     *                  ref="#/definitions/UploadCustomerInvoice"
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
        /** @var UploadCustomerInvoice $uploadCustomerInvoice */
        $uploadCustomerInvoice = $this->uploadCustomerInvoiceRepository->findWithoutFail($id);

        if (empty($uploadCustomerInvoice)) {
            return $this->sendError('Upload Customer Invoice not found');
        }

        return $this->sendResponse($uploadCustomerInvoice->toArray(), 'Upload Customer Invoice retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/uploadCustomerInvoices/{id}",
     *      summary="updateUploadCustomerInvoice",
     *      tags={"UploadCustomerInvoice"},
     *      description="Update UploadCustomerInvoice",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of UploadCustomerInvoice",
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
     *                  ref="#/definitions/UploadCustomerInvoice"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateUploadCustomerInvoiceAPIRequest $request)
    {
        $input = $request->all();

        /** @var UploadCustomerInvoice $uploadCustomerInvoice */
        $uploadCustomerInvoice = $this->uploadCustomerInvoiceRepository->findWithoutFail($id);

        if (empty($uploadCustomerInvoice)) {
            return $this->sendError('Upload Customer Invoice not found');
        }

        $uploadCustomerInvoice = $this->uploadCustomerInvoiceRepository->update($input, $id);

        return $this->sendResponse($uploadCustomerInvoice->toArray(), 'UploadCustomerInvoice updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/uploadCustomerInvoices/{id}",
     *      summary="deleteUploadCustomerInvoice",
     *      tags={"UploadCustomerInvoice"},
     *      description="Delete UploadCustomerInvoice",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of UploadCustomerInvoice",
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
        /** @var UploadCustomerInvoice $uploadCustomerInvoice */
        $uploadCustomerInvoice = $this->uploadCustomerInvoiceRepository->findWithoutFail($id);

        if (empty($uploadCustomerInvoice)) {
            return $this->sendError('Upload Customer Invoice not found');
        }

        $uploadCustomerInvoice->delete();

        return $this->sendSuccess('Upload Customer Invoice deleted successfully');
    }
}
