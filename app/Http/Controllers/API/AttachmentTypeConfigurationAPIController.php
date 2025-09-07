<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAttachmentTypeConfigurationAPIRequest;
use App\Http\Requests\API\UpdateAttachmentTypeConfigurationAPIRequest;
use App\Models\AttachmentTypeConfiguration;
use App\Repositories\AttachmentTypeConfigurationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AttachmentTypeConfigurationController
 * @package App\Http\Controllers\API
 */

class AttachmentTypeConfigurationAPIController extends AppBaseController
{
    /** @var  AttachmentTypeConfigurationRepository */
    private $attachmentTypeConfigurationRepository;

    public function __construct(AttachmentTypeConfigurationRepository $attachmentTypeConfigurationRepo)
    {
        $this->attachmentTypeConfigurationRepository = $attachmentTypeConfigurationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/attachmentTypeConfigurations",
     *      summary="getAttachmentTypeConfigurationList",
     *      tags={"AttachmentTypeConfiguration"},
     *      description="Get all AttachmentTypeConfigurations",
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
     *                  @OA\Items(ref="#/definitions/AttachmentTypeConfiguration")
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
        $this->attachmentTypeConfigurationRepository->pushCriteria(new RequestCriteria($request));
        $this->attachmentTypeConfigurationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $attachmentTypeConfigurations = $this->attachmentTypeConfigurationRepository->all();

        return $this->sendResponse($attachmentTypeConfigurations->toArray(), trans('custom.attachment_type_configurations_retrieved_successfu'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/attachmentTypeConfigurations",
     *      summary="createAttachmentTypeConfiguration",
     *      tags={"AttachmentTypeConfiguration"},
     *      description="Create AttachmentTypeConfiguration",
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
     *                  ref="#/definitions/AttachmentTypeConfiguration"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAttachmentTypeConfigurationAPIRequest $request)
    {
        $input = $request->all();
        try{
            $saveConfiguration = $this->attachmentTypeConfigurationRepository->storeAttachmentConfig($input);
            if(!$saveConfiguration['success']){
                return $this->sendError($saveConfiguration['message']);
            }
            return $this->sendResponse([], trans('custom.attachment_configuration_saved_successfully'));
        } catch (\Exception $exception){
            return $this->sendError(trans('custom.failed_to_save_attachment_configuration') . $exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/attachmentTypeConfigurations/{id}",
     *      summary="getAttachmentTypeConfigurationItem",
     *      tags={"AttachmentTypeConfiguration"},
     *      description="Get AttachmentTypeConfiguration",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of AttachmentTypeConfiguration",
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
     *                  ref="#/definitions/AttachmentTypeConfiguration"
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
        /** @var AttachmentTypeConfiguration $attachmentTypeConfiguration */
        $attachmentTypeConfiguration = $this->attachmentTypeConfigurationRepository->findWithoutFail($id);

        if (empty($attachmentTypeConfiguration)) {
            return $this->sendError(trans('custom.attachment_type_configuration_not_found'));
        }

        return $this->sendResponse($attachmentTypeConfiguration->toArray(), trans('custom.attachment_type_configuration_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/attachmentTypeConfigurations/{id}",
     *      summary="updateAttachmentTypeConfiguration",
     *      tags={"AttachmentTypeConfiguration"},
     *      description="Update AttachmentTypeConfiguration",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of AttachmentTypeConfiguration",
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
     *                  ref="#/definitions/AttachmentTypeConfiguration"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAttachmentTypeConfigurationAPIRequest $request)
    {
        $input = $request->all();

        /** @var AttachmentTypeConfiguration $attachmentTypeConfiguration */
        $attachmentTypeConfiguration = $this->attachmentTypeConfigurationRepository->findWithoutFail($id);

        if (empty($attachmentTypeConfiguration)) {
            return $this->sendError(trans('custom.attachment_type_configuration_not_found'));
        }

        $attachmentTypeConfiguration = $this->attachmentTypeConfigurationRepository->update($input, $id);

        return $this->sendResponse($attachmentTypeConfiguration->toArray(), trans('custom.attachmenttypeconfiguration_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/attachmentTypeConfigurations/{id}",
     *      summary="deleteAttachmentTypeConfiguration",
     *      tags={"AttachmentTypeConfiguration"},
     *      description="Delete AttachmentTypeConfiguration",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of AttachmentTypeConfiguration",
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
        /** @var AttachmentTypeConfiguration $attachmentTypeConfiguration */
        $attachmentTypeConfiguration = $this->attachmentTypeConfigurationRepository->findWithoutFail($id);

        if (empty($attachmentTypeConfiguration)) {
            return $this->sendError(trans('custom.attachment_type_configuration_not_found'));
        }

        $attachmentTypeConfiguration->delete();

        return $this->sendSuccess('Attachment Type Configuration deleted successfully');
    }
    public function getAttachmentTypeConfig(Request $request){
        try{
            $companyDocumentAttachmentID = $request->input('companyDocumentAttachmentID');
            $attachmentTypes = $this->attachmentTypeConfigurationRepository->getAttachmentTypeConfig($companyDocumentAttachmentID);
            if(!$attachmentTypes['success']){
                return $this->sendError($attachmentTypes['message'] ?? 'Failed to load attachment types.');
            }
            return $this->sendResponse($attachmentTypes['data'], trans('custom.document_attachment_types_retrieved_successfully'));
        } catch(\Exception $exception){
            return $this->sendError(trans('custom.unexpected_error') . $exception->getMessage());
        }
    }
}
