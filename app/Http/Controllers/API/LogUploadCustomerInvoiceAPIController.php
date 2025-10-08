<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLogUploadCustomerInvoiceAPIRequest;
use App\Http\Requests\API\UpdateLogUploadCustomerInvoiceAPIRequest;
use App\Models\LogUploadCustomerInvoice;
use App\Repositories\LogUploadCustomerInvoiceRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LogUploadCustomerInvoiceController
 * @package App\Http\Controllers\API
 */

class LogUploadCustomerInvoiceAPIController extends AppBaseController
{
    /** @var  LogUploadCustomerInvoiceRepository */
    private $logUploadCustomerInvoiceRepository;

    public function __construct(LogUploadCustomerInvoiceRepository $logUploadCustomerInvoiceRepo)
    {
        $this->logUploadCustomerInvoiceRepository = $logUploadCustomerInvoiceRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/logUploadCustomerInvoices",
     *      summary="getLogUploadCustomerInvoiceList",
     *      tags={"LogUploadCustomerInvoice"},
     *      description="Get all LogUploadCustomerInvoices",
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
     *                  @OA\Items(ref="#/definitions/LogUploadCustomerInvoice")
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
        $this->logUploadCustomerInvoiceRepository->pushCriteria(new RequestCriteria($request));
        $this->logUploadCustomerInvoiceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $logUploadCustomerInvoices = $this->logUploadCustomerInvoiceRepository->all();

        return $this->sendResponse($logUploadCustomerInvoices->toArray(), trans('custom.log_upload_customer_invoices_retrieved_successfull'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/logUploadCustomerInvoices",
     *      summary="createLogUploadCustomerInvoice",
     *      tags={"LogUploadCustomerInvoice"},
     *      description="Create LogUploadCustomerInvoice",
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
     *                  ref="#/definitions/LogUploadCustomerInvoice"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLogUploadCustomerInvoiceAPIRequest $request)
    {
        $input = $request->all();

        $logUploadCustomerInvoice = $this->logUploadCustomerInvoiceRepository->create($input);

        return $this->sendResponse($logUploadCustomerInvoice->toArray(), trans('custom.log_upload_customer_invoice_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/logUploadCustomerInvoices/{id}",
     *      summary="getLogUploadCustomerInvoiceItem",
     *      tags={"LogUploadCustomerInvoice"},
     *      description="Get LogUploadCustomerInvoice",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of LogUploadCustomerInvoice",
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
     *                  ref="#/definitions/LogUploadCustomerInvoice"
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
        /** @var LogUploadCustomerInvoice $logUploadCustomerInvoice */
        $logUploadCustomerInvoice = $this->logUploadCustomerInvoiceRepository->findWithoutFail($id);

        if (empty($logUploadCustomerInvoice)) {
            return $this->sendError(trans('custom.log_upload_customer_invoice_not_found'));
        }

        return $this->sendResponse($logUploadCustomerInvoice->toArray(), trans('custom.log_upload_customer_invoice_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/logUploadCustomerInvoices/{id}",
     *      summary="updateLogUploadCustomerInvoice",
     *      tags={"LogUploadCustomerInvoice"},
     *      description="Update LogUploadCustomerInvoice",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of LogUploadCustomerInvoice",
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
     *                  ref="#/definitions/LogUploadCustomerInvoice"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLogUploadCustomerInvoiceAPIRequest $request)
    {
        $input = $request->all();

        /** @var LogUploadCustomerInvoice $logUploadCustomerInvoice */
        $logUploadCustomerInvoice = $this->logUploadCustomerInvoiceRepository->findWithoutFail($id);

        if (empty($logUploadCustomerInvoice)) {
            return $this->sendError(trans('custom.log_upload_customer_invoice_not_found'));
        }

        $logUploadCustomerInvoice = $this->logUploadCustomerInvoiceRepository->update($input, $id);

        return $this->sendResponse($logUploadCustomerInvoice->toArray(), trans('custom.loguploadcustomerinvoice_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/logUploadCustomerInvoices/{id}",
     *      summary="deleteLogUploadCustomerInvoice",
     *      tags={"LogUploadCustomerInvoice"},
     *      description="Delete LogUploadCustomerInvoice",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of LogUploadCustomerInvoice",
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
        /** @var LogUploadCustomerInvoice $logUploadCustomerInvoice */
        $logUploadCustomerInvoice = $this->logUploadCustomerInvoiceRepository->findWithoutFail($id);

        if (empty($logUploadCustomerInvoice)) {
            return $this->sendError(trans('custom.log_upload_customer_invoice_not_found'));
        }

        $logUploadCustomerInvoice->delete();

        return $this->sendSuccess('Log Upload Customer Invoice deleted successfully');
    }
}
