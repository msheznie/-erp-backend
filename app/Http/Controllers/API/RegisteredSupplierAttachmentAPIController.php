<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRegisteredSupplierAttachmentAPIRequest;
use App\Http\Requests\API\UpdateRegisteredSupplierAttachmentAPIRequest;
use App\Models\RegisteredSupplierAttachment;
use App\Repositories\RegisteredSupplierAttachmentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RegisteredSupplierAttachmentController
 * @package App\Http\Controllers\API
 */

class RegisteredSupplierAttachmentAPIController extends AppBaseController
{
    /** @var  RegisteredSupplierAttachmentRepository */
    private $registeredSupplierAttachmentRepository;

    public function __construct(RegisteredSupplierAttachmentRepository $registeredSupplierAttachmentRepo)
    {
        $this->registeredSupplierAttachmentRepository = $registeredSupplierAttachmentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/registeredSupplierAttachments",
     *      summary="Get a listing of the RegisteredSupplierAttachments.",
     *      tags={"RegisteredSupplierAttachment"},
     *      description="Get all RegisteredSupplierAttachments",
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
     *                  @SWG\Items(ref="#/definitions/RegisteredSupplierAttachment")
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
        $this->registeredSupplierAttachmentRepository->pushCriteria(new RequestCriteria($request));
        $this->registeredSupplierAttachmentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $registeredSupplierAttachments = $this->registeredSupplierAttachmentRepository->all();

        return $this->sendResponse($registeredSupplierAttachments->toArray(), trans('custom.registered_supplier_attachments_retrieved_successf'));
    }

    /**
     * @param CreateRegisteredSupplierAttachmentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/registeredSupplierAttachments",
     *      summary="Store a newly created RegisteredSupplierAttachment in storage",
     *      tags={"RegisteredSupplierAttachment"},
     *      description="Store RegisteredSupplierAttachment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RegisteredSupplierAttachment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RegisteredSupplierAttachment")
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
     *                  ref="#/definitions/RegisteredSupplierAttachment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRegisteredSupplierAttachmentAPIRequest $request)
    {
        $input = $request->all();

        $registeredSupplierAttachment = $this->registeredSupplierAttachmentRepository->create($input);

        return $this->sendResponse($registeredSupplierAttachment->toArray(), trans('custom.registered_supplier_attachment_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/registeredSupplierAttachments/{id}",
     *      summary="Display the specified RegisteredSupplierAttachment",
     *      tags={"RegisteredSupplierAttachment"},
     *      description="Get RegisteredSupplierAttachment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredSupplierAttachment",
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
     *                  ref="#/definitions/RegisteredSupplierAttachment"
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
        /** @var RegisteredSupplierAttachment $registeredSupplierAttachment */
        $registeredSupplierAttachment = $this->registeredSupplierAttachmentRepository->findWithoutFail($id);

        if (empty($registeredSupplierAttachment)) {
            return $this->sendError(trans('custom.registered_supplier_attachment_not_found'));
        }

        return $this->sendResponse($registeredSupplierAttachment->toArray(), trans('custom.registered_supplier_attachment_retrieved_successfu'));
    }

    /**
     * @param int $id
     * @param UpdateRegisteredSupplierAttachmentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/registeredSupplierAttachments/{id}",
     *      summary="Update the specified RegisteredSupplierAttachment in storage",
     *      tags={"RegisteredSupplierAttachment"},
     *      description="Update RegisteredSupplierAttachment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredSupplierAttachment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RegisteredSupplierAttachment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RegisteredSupplierAttachment")
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
     *                  ref="#/definitions/RegisteredSupplierAttachment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRegisteredSupplierAttachmentAPIRequest $request)
    {
        $input = $request->all();

        /** @var RegisteredSupplierAttachment $registeredSupplierAttachment */
        $registeredSupplierAttachment = $this->registeredSupplierAttachmentRepository->findWithoutFail($id);

        if (empty($registeredSupplierAttachment)) {
            return $this->sendError(trans('custom.registered_supplier_attachment_not_found'));
        }

        $registeredSupplierAttachment = $this->registeredSupplierAttachmentRepository->update($input, $id);

        return $this->sendResponse($registeredSupplierAttachment->toArray(), trans('custom.registeredsupplierattachment_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/registeredSupplierAttachments/{id}",
     *      summary="Remove the specified RegisteredSupplierAttachment from storage",
     *      tags={"RegisteredSupplierAttachment"},
     *      description="Delete RegisteredSupplierAttachment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredSupplierAttachment",
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
        /** @var RegisteredSupplierAttachment $registeredSupplierAttachment */
        $registeredSupplierAttachment = $this->registeredSupplierAttachmentRepository->findWithoutFail($id);

        if (empty($registeredSupplierAttachment)) {
            return $this->sendError(trans('custom.registered_supplier_attachment_not_found'));
        }

        $registeredSupplierAttachment->delete();

        return $this->sendResponse([], trans('custom.registered_supplier_attachment_deleted_successfull'));
    }
}
