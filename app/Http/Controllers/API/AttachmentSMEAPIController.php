<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAttachmentSMEAPIRequest;
use App\Http\Requests\API\UpdateAttachmentSMEAPIRequest;
use App\helper\SME;
use App\Models\AttachmentSME;
use App\Repositories\AttachmentSMERepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AttachmentSMEController
 * @package App\Http\Controllers\API
 */

class AttachmentSMEAPIController extends AppBaseController
{
    /** @var  AttachmentSMERepository */
    private $attachmentSMERepository;

    public function __construct(AttachmentSMERepository $attachmentSMERepo)
    {
        $this->attachmentSMERepository = $attachmentSMERepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/attachmentSMEs",
     *      summary="Get a listing of the AttachmentSMEs.",
     *      tags={"AttachmentSME"},
     *      description="Get all AttachmentSMEs",
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
     *                  @SWG\Items(ref="#/definitions/AttachmentSME")
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
        $this->attachmentSMERepository->pushCriteria(new RequestCriteria($request));
        $this->attachmentSMERepository->pushCriteria(new LimitOffsetCriteria($request));
        $attachmentSMEs = $this->attachmentSMERepository->all();

        return $this->sendResponse($attachmentSMEs->toArray(), trans('custom.attachment_s_m_es_retrieved_successfully'));
    }

    /**
     * @param CreateAttachmentSMEAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/attachmentSMEs",
     *      summary="Store a newly created AttachmentSME in storage",
     *      tags={"AttachmentSME"},
     *      description="Store AttachmentSME",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AttachmentSME that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AttachmentSME")
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
     *                  ref="#/definitions/AttachmentSME"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAttachmentSMEAPIRequest $request)
    {
        $input = $request->all();

        $attachmentSME = $this->attachmentSMERepository->create($input);

        return $this->sendResponse($attachmentSME->toArray(), trans('custom.attachment_s_m_e_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/attachmentSMEs/{id}",
     *      summary="Display the specified AttachmentSME",
     *      tags={"AttachmentSME"},
     *      description="Get AttachmentSME",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AttachmentSME",
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
     *                  ref="#/definitions/AttachmentSME"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id, $docID, $companyID)
    {
        /** @var AttachmentSME $attachmentSME */
        $where = ['documentSystemCode'=> $id, 'documentID'=> $docID, 'companyID'=> $companyID];
        $column = ['attachmentID', 'attachmentDescription','myFileName', 'fileType'];
        $attachmentSME = $this->attachmentSMERepository->findWhere($where, $column);

        return $this->sendResponse($attachmentSME->toArray(), trans('custom.attachment_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateAttachmentSMEAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/attachmentSMEs/{id}",
     *      summary="Update the specified AttachmentSME in storage",
     *      tags={"AttachmentSME"},
     *      description="Update AttachmentSME",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AttachmentSME",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AttachmentSME that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AttachmentSME")
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
     *                  ref="#/definitions/AttachmentSME"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAttachmentSMEAPIRequest $request)
    {
        $input = $request->all();

        /** @var AttachmentSME $attachmentSME */
        $attachmentSME = $this->attachmentSMERepository->findWithoutFail($id);

        if (empty($attachmentSME)) {
            return $this->sendError(trans('custom.attachment_s_m_e_not_found'));
        }

        $attachmentSME = $this->attachmentSMERepository->update($input, $id);

        return $this->sendResponse($attachmentSME->toArray(), trans('custom.attachmentsme_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/attachmentSMEs/{id}",
     *      summary="Remove the specified AttachmentSME from storage",
     *      tags={"AttachmentSME"},
     *      description="Delete AttachmentSME",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AttachmentSME",
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
        /** @var AttachmentSME $attachmentSME */
        $attachmentSME = $this->attachmentSMERepository->findWithoutFail($id);

        if (empty($attachmentSME)) {
            return $this->sendError(trans('custom.attachment_s_m_e_not_found'));
        }

        $attachmentSME->delete();

        return $this->sendSuccess('Attachment S M E deleted successfully');
    }
}
