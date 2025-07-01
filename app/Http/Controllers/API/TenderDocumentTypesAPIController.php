<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderDocumentTypesAPIRequest;
use App\Http\Requests\API\UpdateTenderDocumentTypesAPIRequest;
use App\Models\TenderDocumentTypes;
use App\Repositories\TenderDocumentTypesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\TenderDocumentTypeAssign;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderDocumentTypesController
 * @package App\Http\Controllers\API
 */

class TenderDocumentTypesAPIController extends AppBaseController
{
    /** @var  TenderDocumentTypesRepository */
    private $tenderDocumentTypesRepository;

    public function __construct(TenderDocumentTypesRepository $tenderDocumentTypesRepo)
    {
        $this->tenderDocumentTypesRepository = $tenderDocumentTypesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderDocumentTypes",
     *      summary="Get a listing of the TenderDocumentTypes.",
     *      tags={"TenderDocumentTypes"},
     *      description="Get all TenderDocumentTypes",
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
     *                  @SWG\Items(ref="#/definitions/TenderDocumentTypes")
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
        $this->tenderDocumentTypesRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderDocumentTypesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderDocumentTypes = $this->tenderDocumentTypesRepository->all();

        return $this->sendResponse($tenderDocumentTypes->toArray(), 'Tender Document Types retrieved successfully');
    }

    /**
     * @param CreateTenderDocumentTypesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderDocumentTypes",
     *      summary="Store a newly created TenderDocumentTypes in storage",
     *      tags={"TenderDocumentTypes"},
     *      description="Store TenderDocumentTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderDocumentTypes that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderDocumentTypes")
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
     *                  ref="#/definitions/TenderDocumentTypes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderDocumentTypesAPIRequest $request)
    {
        $input = $request->all();

        $tenderDocumentTypes = $this->tenderDocumentTypesRepository->create($input);

        return $this->sendResponse($tenderDocumentTypes->toArray(), 'Tender Document Types saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderDocumentTypes/{id}",
     *      summary="Display the specified TenderDocumentTypes",
     *      tags={"TenderDocumentTypes"},
     *      description="Get TenderDocumentTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderDocumentTypes",
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
     *                  ref="#/definitions/TenderDocumentTypes"
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
        /** @var TenderDocumentTypes $tenderDocumentTypes */
        $tenderDocumentTypes = $this->tenderDocumentTypesRepository->findWithoutFail($id);

        if (empty($tenderDocumentTypes)) {
            return $this->sendError('Tender Document Types not found');
        }

        return $this->sendResponse($tenderDocumentTypes->toArray(), 'Tender Document Types retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTenderDocumentTypesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderDocumentTypes/{id}",
     *      summary="Update the specified TenderDocumentTypes in storage",
     *      tags={"TenderDocumentTypes"},
     *      description="Update TenderDocumentTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderDocumentTypes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderDocumentTypes that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderDocumentTypes")
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
     *                  ref="#/definitions/TenderDocumentTypes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderDocumentTypesAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderDocumentTypes $tenderDocumentTypes */
        $tenderDocumentTypes = $this->tenderDocumentTypesRepository->findWithoutFail($id);

        if (empty($tenderDocumentTypes)) {
            return $this->sendError('Tender Document Types not found');
        }

        $tenderDocumentTypes = $this->tenderDocumentTypesRepository->update($input, $id);

        return $this->sendResponse($tenderDocumentTypes->toArray(), 'TenderDocumentTypes updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderDocumentTypes/{id}",
     *      summary="Remove the specified TenderDocumentTypes from storage",
     *      tags={"TenderDocumentTypes"},
     *      description="Delete TenderDocumentTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderDocumentTypes",
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
        /** @var TenderDocumentTypes $tenderDocumentTypes */
        $tenderDocumentTypes = $this->tenderDocumentTypesRepository->findWithoutFail($id);

        if (empty($tenderDocumentTypes)) {
            return $this->sendError('Tender Document Types not found');
        }

        $tenderDocumentTypes->delete();

        return $this->sendSuccess('Tender Document Types deleted successfully');
    }

    public function getTenderAttachmentType(Request $request)
    {
        $input = $request->all();
        return $this->tenderDocumentTypesRepository->getTenderAttachmentTypes($input);
    }

    public function assignDocumentTypes(Request $request)
    {
        $input = $request->all();
        try {
            $response = $this->tenderDocumentTypesRepository->assignTenderDocumentType($input);
            if(!$response['success']){
                return ['success' => false, 'message' => $response['message']];
            } else {
                return ['success' => true, 'message' => $response['message']];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e];
        }
    }

    public function deleteAssignDocumentTypes(Request $request)
    {
        $input = $request->all();
        try {
            $response = $this->tenderDocumentTypesRepository->deleteAssignDocumentTypes($input);
            if(!$response['success']){
                return ['success' => false, 'message' => $response['message']];
            } else {
                return ['success' => true, 'message' => $response['message']];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e];
        }
    }
}
