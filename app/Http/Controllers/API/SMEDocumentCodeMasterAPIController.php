<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMEDocumentCodeMasterAPIRequest;
use App\Http\Requests\API\UpdateSMEDocumentCodeMasterAPIRequest;
use App\Models\SMEDocumentCodeMaster;
use App\Repositories\SMEDocumentCodeMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMEDocumentCodeMasterController
 * @package App\Http\Controllers\API
 */

class SMEDocumentCodeMasterAPIController extends AppBaseController
{
    /** @var  SMEDocumentCodeMasterRepository */
    private $sMEDocumentCodeMasterRepository;

    public function __construct(SMEDocumentCodeMasterRepository $sMEDocumentCodeMasterRepo)
    {
        $this->sMEDocumentCodeMasterRepository = $sMEDocumentCodeMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMEDocumentCodeMasters",
     *      summary="Get a listing of the SMEDocumentCodeMasters.",
     *      tags={"SMEDocumentCodeMaster"},
     *      description="Get all SMEDocumentCodeMasters",
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
     *                  @SWG\Items(ref="#/definitions/SMEDocumentCodeMaster")
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
        $this->sMEDocumentCodeMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->sMEDocumentCodeMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMEDocumentCodeMasters = $this->sMEDocumentCodeMasterRepository->all();

        return $this->sendResponse($sMEDocumentCodeMasters->toArray(), trans('custom.s_m_e_document_code_masters_retrieved_successfully'));
    }

    /**
     * @param CreateSMEDocumentCodeMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMEDocumentCodeMasters",
     *      summary="Store a newly created SMEDocumentCodeMaster in storage",
     *      tags={"SMEDocumentCodeMaster"},
     *      description="Store SMEDocumentCodeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMEDocumentCodeMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMEDocumentCodeMaster")
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
     *                  ref="#/definitions/SMEDocumentCodeMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMEDocumentCodeMasterAPIRequest $request)
    {
        $input = $request->all();

        $sMEDocumentCodeMaster = $this->sMEDocumentCodeMasterRepository->create($input);

        return $this->sendResponse($sMEDocumentCodeMaster->toArray(), trans('custom.s_m_e_document_code_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMEDocumentCodeMasters/{id}",
     *      summary="Display the specified SMEDocumentCodeMaster",
     *      tags={"SMEDocumentCodeMaster"},
     *      description="Get SMEDocumentCodeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEDocumentCodeMaster",
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
     *                  ref="#/definitions/SMEDocumentCodeMaster"
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
        /** @var SMEDocumentCodeMaster $sMEDocumentCodeMaster */
        $sMEDocumentCodeMaster = $this->sMEDocumentCodeMasterRepository->findWithoutFail($id);

        if (empty($sMEDocumentCodeMaster)) {
            return $this->sendError(trans('custom.s_m_e_document_code_master_not_found'));
        }

        return $this->sendResponse($sMEDocumentCodeMaster->toArray(), trans('custom.s_m_e_document_code_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSMEDocumentCodeMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMEDocumentCodeMasters/{id}",
     *      summary="Update the specified SMEDocumentCodeMaster in storage",
     *      tags={"SMEDocumentCodeMaster"},
     *      description="Update SMEDocumentCodeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEDocumentCodeMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMEDocumentCodeMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMEDocumentCodeMaster")
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
     *                  ref="#/definitions/SMEDocumentCodeMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMEDocumentCodeMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMEDocumentCodeMaster $sMEDocumentCodeMaster */
        $sMEDocumentCodeMaster = $this->sMEDocumentCodeMasterRepository->findWithoutFail($id);

        if (empty($sMEDocumentCodeMaster)) {
            return $this->sendError(trans('custom.s_m_e_document_code_master_not_found'));
        }

        $sMEDocumentCodeMaster = $this->sMEDocumentCodeMasterRepository->update($input, $id);

        return $this->sendResponse($sMEDocumentCodeMaster->toArray(), trans('custom.smedocumentcodemaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMEDocumentCodeMasters/{id}",
     *      summary="Remove the specified SMEDocumentCodeMaster from storage",
     *      tags={"SMEDocumentCodeMaster"},
     *      description="Delete SMEDocumentCodeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEDocumentCodeMaster",
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
        /** @var SMEDocumentCodeMaster $sMEDocumentCodeMaster */
        $sMEDocumentCodeMaster = $this->sMEDocumentCodeMasterRepository->findWithoutFail($id);

        if (empty($sMEDocumentCodeMaster)) {
            return $this->sendError(trans('custom.s_m_e_document_code_master_not_found'));
        }

        $sMEDocumentCodeMaster->delete();

        return $this->sendSuccess('S M E Document Code Master deleted successfully');
    }
}
