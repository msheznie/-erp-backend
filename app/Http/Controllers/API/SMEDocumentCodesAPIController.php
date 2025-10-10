<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMEDocumentCodesAPIRequest;
use App\Http\Requests\API\UpdateSMEDocumentCodesAPIRequest;
use App\Models\SMEDocumentCodes;
use App\Repositories\SMEDocumentCodesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMEDocumentCodesController
 * @package App\Http\Controllers\API
 */

class SMEDocumentCodesAPIController extends AppBaseController
{
    /** @var  SMEDocumentCodesRepository */
    private $sMEDocumentCodesRepository;

    public function __construct(SMEDocumentCodesRepository $sMEDocumentCodesRepo)
    {
        $this->sMEDocumentCodesRepository = $sMEDocumentCodesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMEDocumentCodes",
     *      summary="Get a listing of the SMEDocumentCodes.",
     *      tags={"SMEDocumentCodes"},
     *      description="Get all SMEDocumentCodes",
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
     *                  @SWG\Items(ref="#/definitions/SMEDocumentCodes")
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
        $this->sMEDocumentCodesRepository->pushCriteria(new RequestCriteria($request));
        $this->sMEDocumentCodesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMEDocumentCodes = $this->sMEDocumentCodesRepository->all();

        return $this->sendResponse($sMEDocumentCodes->toArray(), trans('custom.s_m_e_document_codes_retrieved_successfully'));
    }

    /**
     * @param CreateSMEDocumentCodesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMEDocumentCodes",
     *      summary="Store a newly created SMEDocumentCodes in storage",
     *      tags={"SMEDocumentCodes"},
     *      description="Store SMEDocumentCodes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMEDocumentCodes that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMEDocumentCodes")
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
     *                  ref="#/definitions/SMEDocumentCodes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMEDocumentCodesAPIRequest $request)
    {
        $input = $request->all();

        $sMEDocumentCodes = $this->sMEDocumentCodesRepository->create($input);

        return $this->sendResponse($sMEDocumentCodes->toArray(), trans('custom.s_m_e_document_codes_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMEDocumentCodes/{id}",
     *      summary="Display the specified SMEDocumentCodes",
     *      tags={"SMEDocumentCodes"},
     *      description="Get SMEDocumentCodes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEDocumentCodes",
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
     *                  ref="#/definitions/SMEDocumentCodes"
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
        /** @var SMEDocumentCodes $sMEDocumentCodes */
        $sMEDocumentCodes = $this->sMEDocumentCodesRepository->findWithoutFail($id);

        if (empty($sMEDocumentCodes)) {
            return $this->sendError(trans('custom.s_m_e_document_codes_not_found'));
        }

        return $this->sendResponse($sMEDocumentCodes->toArray(), trans('custom.s_m_e_document_codes_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSMEDocumentCodesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMEDocumentCodes/{id}",
     *      summary="Update the specified SMEDocumentCodes in storage",
     *      tags={"SMEDocumentCodes"},
     *      description="Update SMEDocumentCodes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEDocumentCodes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMEDocumentCodes that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMEDocumentCodes")
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
     *                  ref="#/definitions/SMEDocumentCodes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMEDocumentCodesAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMEDocumentCodes $sMEDocumentCodes */
        $sMEDocumentCodes = $this->sMEDocumentCodesRepository->findWithoutFail($id);

        if (empty($sMEDocumentCodes)) {
            return $this->sendError(trans('custom.s_m_e_document_codes_not_found'));
        }

        $sMEDocumentCodes = $this->sMEDocumentCodesRepository->update($input, $id);

        return $this->sendResponse($sMEDocumentCodes->toArray(), trans('custom.smedocumentcodes_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMEDocumentCodes/{id}",
     *      summary="Remove the specified SMEDocumentCodes from storage",
     *      tags={"SMEDocumentCodes"},
     *      description="Delete SMEDocumentCodes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEDocumentCodes",
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
        /** @var SMEDocumentCodes $sMEDocumentCodes */
        $sMEDocumentCodes = $this->sMEDocumentCodesRepository->findWithoutFail($id);

        if (empty($sMEDocumentCodes)) {
            return $this->sendError(trans('custom.s_m_e_document_codes_not_found'));
        }

        $sMEDocumentCodes->delete();

        return $this->sendSuccess('S M E Document Codes deleted successfully');
    }
}
