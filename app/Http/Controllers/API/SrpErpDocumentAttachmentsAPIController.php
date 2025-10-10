<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSrpErpDocumentAttachmentsAPIRequest;
use App\Http\Requests\API\UpdateSrpErpDocumentAttachmentsAPIRequest;
use App\Models\SrpErpDocumentAttachments;
use App\Repositories\SrpErpDocumentAttachmentsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SrpErpDocumentAttachmentsController
 * @package App\Http\Controllers\API
 */

class SrpErpDocumentAttachmentsAPIController extends AppBaseController
{
    /** @var  SrpErpDocumentAttachmentsRepository */
    private $srpErpDocumentAttachmentsRepository;

    public function __construct(SrpErpDocumentAttachmentsRepository $srpErpDocumentAttachmentsRepo)
    {
        $this->srpErpDocumentAttachmentsRepository = $srpErpDocumentAttachmentsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/srpErpDocumentAttachments",
     *      summary="Get a listing of the SrpErpDocumentAttachments.",
     *      tags={"SrpErpDocumentAttachments"},
     *      description="Get all SrpErpDocumentAttachments",
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
     *                  @SWG\Items(ref="#/definitions/SrpErpDocumentAttachments")
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
        $this->srpErpDocumentAttachmentsRepository->pushCriteria(new RequestCriteria($request));
        $this->srpErpDocumentAttachmentsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $srpErpDocumentAttachments = $this->srpErpDocumentAttachmentsRepository->all();

        return $this->sendResponse($srpErpDocumentAttachments->toArray(), trans('custom.srp_erp_document_attachments_retrieved_successfull'));
    }

    public function geDocumentAttachments(Request $request){
        $documentID = $request['documentID'];
        $documentSystemCode = $request['documentSystemCode'];
        $srpErpDocumentAttachments = SrpErpDocumentAttachments::where('documentID',$documentID)
                                                                ->where('documentSystemCode',$documentSystemCode)
                                                                ->get();

        return $this->sendResponse($srpErpDocumentAttachments, trans('custom.srp_erp_document_attachments_retrieved_successfull'));

    }

    /**
     * @param CreateSrpErpDocumentAttachmentsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/srpErpDocumentAttachments",
     *      summary="Store a newly created SrpErpDocumentAttachments in storage",
     *      tags={"SrpErpDocumentAttachments"},
     *      description="Store SrpErpDocumentAttachments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SrpErpDocumentAttachments that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SrpErpDocumentAttachments")
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
     *                  ref="#/definitions/SrpErpDocumentAttachments"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSrpErpDocumentAttachmentsAPIRequest $request)
    {
        $input = $request->all();

        $srpErpDocumentAttachments = $this->srpErpDocumentAttachmentsRepository->create($input);

        return $this->sendResponse($srpErpDocumentAttachments->toArray(), trans('custom.srp_erp_document_attachments_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/srpErpDocumentAttachments/{id}",
     *      summary="Display the specified SrpErpDocumentAttachments",
     *      tags={"SrpErpDocumentAttachments"},
     *      description="Get SrpErpDocumentAttachments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpDocumentAttachments",
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
     *                  ref="#/definitions/SrpErpDocumentAttachments"
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
        /** @var SrpErpDocumentAttachments $srpErpDocumentAttachments */
        $srpErpDocumentAttachments = $this->srpErpDocumentAttachmentsRepository->findWithoutFail($id);

        if (empty($srpErpDocumentAttachments)) {
            return $this->sendError(trans('custom.srp_erp_document_attachments_not_found'));
        }

        return $this->sendResponse($srpErpDocumentAttachments->toArray(), trans('custom.srp_erp_document_attachments_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdateSrpErpDocumentAttachmentsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/srpErpDocumentAttachments/{id}",
     *      summary="Update the specified SrpErpDocumentAttachments in storage",
     *      tags={"SrpErpDocumentAttachments"},
     *      description="Update SrpErpDocumentAttachments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpDocumentAttachments",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SrpErpDocumentAttachments that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SrpErpDocumentAttachments")
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
     *                  ref="#/definitions/SrpErpDocumentAttachments"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSrpErpDocumentAttachmentsAPIRequest $request)
    {
        $input = $request->all();

        /** @var SrpErpDocumentAttachments $srpErpDocumentAttachments */
        $srpErpDocumentAttachments = $this->srpErpDocumentAttachmentsRepository->findWithoutFail($id);

        if (empty($srpErpDocumentAttachments)) {
            return $this->sendError(trans('custom.srp_erp_document_attachments_not_found'));
        }

        $srpErpDocumentAttachments = $this->srpErpDocumentAttachmentsRepository->update($input, $id);

        return $this->sendResponse($srpErpDocumentAttachments->toArray(), trans('custom.srperpdocumentattachments_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/srpErpDocumentAttachments/{id}",
     *      summary="Remove the specified SrpErpDocumentAttachments from storage",
     *      tags={"SrpErpDocumentAttachments"},
     *      description="Delete SrpErpDocumentAttachments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpDocumentAttachments",
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
        /** @var SrpErpDocumentAttachments $srpErpDocumentAttachments */
        $srpErpDocumentAttachments = $this->srpErpDocumentAttachmentsRepository->findWithoutFail($id);

        if (empty($srpErpDocumentAttachments)) {
            return $this->sendError(trans('custom.srp_erp_document_attachments_not_found'));
        }

        $srpErpDocumentAttachments->delete();

        return $this->sendSuccess('Srp Erp Document Attachments deleted successfully');
    }
}
