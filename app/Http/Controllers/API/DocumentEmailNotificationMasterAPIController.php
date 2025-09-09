<?php
/**
 * =============================================
 * -- File Name : DocumentEmailNotificationMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  DocumentEmailNotificationMaster
 * -- Author : Mohamed Nazir
 * -- Create date : 10 - January 2019
 * -- Description : This file contains the all CRUD for Document Email Notification Master
 * -- REVISION HISTORY
 * -- Date: 08-January 2019 By: Nazir Description: Added new function getInvoiceMasterRecord(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentEmailNotificationMasterAPIRequest;
use App\Http\Requests\API\UpdateDocumentEmailNotificationMasterAPIRequest;
use App\Models\DocumentEmailNotificationMaster;
use App\Repositories\DocumentEmailNotificationMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentEmailNotificationMasterController
 * @package App\Http\Controllers\API
 */

class DocumentEmailNotificationMasterAPIController extends AppBaseController
{
    /** @var  DocumentEmailNotificationMasterRepository */
    private $documentEmailNotificationMasterRepository;

    public function __construct(DocumentEmailNotificationMasterRepository $documentEmailNotificationMasterRepo)
    {
        $this->documentEmailNotificationMasterRepository = $documentEmailNotificationMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/documentEmailNotificationMasters",
     *      summary="Get a listing of the DocumentEmailNotificationMasters.",
     *      tags={"DocumentEmailNotificationMaster"},
     *      description="Get all DocumentEmailNotificationMasters",
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
     *                  @SWG\Items(ref="#/definitions/DocumentEmailNotificationMaster")
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
        $this->documentEmailNotificationMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->documentEmailNotificationMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentEmailNotificationMasters = $this->documentEmailNotificationMasterRepository->all();

        return $this->sendResponse($documentEmailNotificationMasters->toArray(), trans('custom.document_email_notification_masters_retrieved_succ'));
    }

    /**
     * @param CreateDocumentEmailNotificationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/documentEmailNotificationMasters",
     *      summary="Store a newly created DocumentEmailNotificationMaster in storage",
     *      tags={"DocumentEmailNotificationMaster"},
     *      description="Store DocumentEmailNotificationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DocumentEmailNotificationMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DocumentEmailNotificationMaster")
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
     *                  ref="#/definitions/DocumentEmailNotificationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentEmailNotificationMasterAPIRequest $request)
    {
        $input = $request->all();

        $documentEmailNotificationMasters = $this->documentEmailNotificationMasterRepository->create($input);

        return $this->sendResponse($documentEmailNotificationMasters->toArray(), trans('custom.document_email_notification_master_saved_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/documentEmailNotificationMasters/{id}",
     *      summary="Display the specified DocumentEmailNotificationMaster",
     *      tags={"DocumentEmailNotificationMaster"},
     *      description="Get DocumentEmailNotificationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentEmailNotificationMaster",
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
     *                  ref="#/definitions/DocumentEmailNotificationMaster"
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
        /** @var DocumentEmailNotificationMaster $documentEmailNotificationMaster */
        $documentEmailNotificationMaster = $this->documentEmailNotificationMasterRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationMaster)) {
            return $this->sendError(trans('custom.document_email_notification_master_not_found'));
        }

        return $this->sendResponse($documentEmailNotificationMaster->toArray(), trans('custom.document_email_notification_master_retrieved_succe'));
    }

    /**
     * @param int $id
     * @param UpdateDocumentEmailNotificationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/documentEmailNotificationMasters/{id}",
     *      summary="Update the specified DocumentEmailNotificationMaster in storage",
     *      tags={"DocumentEmailNotificationMaster"},
     *      description="Update DocumentEmailNotificationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentEmailNotificationMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DocumentEmailNotificationMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DocumentEmailNotificationMaster")
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
     *                  ref="#/definitions/DocumentEmailNotificationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentEmailNotificationMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentEmailNotificationMaster $documentEmailNotificationMaster */
        $documentEmailNotificationMaster = $this->documentEmailNotificationMasterRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationMaster)) {
            return $this->sendError(trans('custom.document_email_notification_master_not_found'));
        }

        $documentEmailNotificationMaster = $this->documentEmailNotificationMasterRepository->update($input, $id);

        return $this->sendResponse($documentEmailNotificationMaster->toArray(), trans('custom.documentemailnotificationmaster_updated_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/documentEmailNotificationMasters/{id}",
     *      summary="Remove the specified DocumentEmailNotificationMaster from storage",
     *      tags={"DocumentEmailNotificationMaster"},
     *      description="Delete DocumentEmailNotificationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentEmailNotificationMaster",
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
        /** @var DocumentEmailNotificationMaster $documentEmailNotificationMaster */
        $documentEmailNotificationMaster = $this->documentEmailNotificationMasterRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationMaster)) {
            return $this->sendError(trans('custom.document_email_notification_master_not_found'));
        }

        $documentEmailNotificationMaster->delete();

        return $this->sendResponse($id, trans('custom.document_email_notification_master_deleted_success'));
    }
}
