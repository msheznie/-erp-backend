<?php
/**
=============================================
-- File Name : HRMSPersonalDocumentsAPIController.php
-- Project Name : ERP
-- Module Name :  LEAVE
-- Author : Mohamed Rilwan
-- Create date : 20 - November 2019
-- Description :
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHRMSPersonalDocumentsAPIRequest;
use App\Http\Requests\API\UpdateHRMSPersonalDocumentsAPIRequest;
use App\Models\HRMSPersonalDocuments;
use App\Repositories\HRMSPersonalDocumentsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HRMSPersonalDocumentsController
 * @package App\Http\Controllers\API
 */

class HRMSPersonalDocumentsAPIController extends AppBaseController
{
    /** @var  HRMSPersonalDocumentsRepository */
    private $hRMSPersonalDocumentsRepository;

    public function __construct(HRMSPersonalDocumentsRepository $hRMSPersonalDocumentsRepo)
    {
        $this->hRMSPersonalDocumentsRepository = $hRMSPersonalDocumentsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSPersonalDocuments",
     *      summary="Get a listing of the HRMSPersonalDocuments.",
     *      tags={"HRMSPersonalDocuments"},
     *      description="Get all HRMSPersonalDocuments",
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
     *                  @SWG\Items(ref="#/definitions/HRMSPersonalDocuments")
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
        $this->hRMSPersonalDocumentsRepository->pushCriteria(new RequestCriteria($request));
        $this->hRMSPersonalDocumentsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hRMSPersonalDocuments = $this->hRMSPersonalDocumentsRepository->all();

        return $this->sendResponse($hRMSPersonalDocuments->toArray(), trans('custom.h_r_m_s_personal_documents_retrieved_successfully'));
    }

    /**
     * @param CreateHRMSPersonalDocumentsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hRMSPersonalDocuments",
     *      summary="Store a newly created HRMSPersonalDocuments in storage",
     *      tags={"HRMSPersonalDocuments"},
     *      description="Store HRMSPersonalDocuments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSPersonalDocuments that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSPersonalDocuments")
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
     *                  ref="#/definitions/HRMSPersonalDocuments"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHRMSPersonalDocumentsAPIRequest $request)
    {
        $input = $request->all();

        $hRMSPersonalDocuments = $this->hRMSPersonalDocumentsRepository->create($input);

        return $this->sendResponse($hRMSPersonalDocuments->toArray(), trans('custom.h_r_m_s_personal_documents_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSPersonalDocuments/{id}",
     *      summary="Display the specified HRMSPersonalDocuments",
     *      tags={"HRMSPersonalDocuments"},
     *      description="Get HRMSPersonalDocuments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSPersonalDocuments",
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
     *                  ref="#/definitions/HRMSPersonalDocuments"
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
        /** @var HRMSPersonalDocuments $hRMSPersonalDocuments */
        $hRMSPersonalDocuments = $this->hRMSPersonalDocumentsRepository->findWithoutFail($id);

        if (empty($hRMSPersonalDocuments)) {
            return $this->sendError(trans('custom.h_r_m_s_personal_documents_not_found'));
        }

        return $this->sendResponse($hRMSPersonalDocuments->toArray(), trans('custom.h_r_m_s_personal_documents_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHRMSPersonalDocumentsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hRMSPersonalDocuments/{id}",
     *      summary="Update the specified HRMSPersonalDocuments in storage",
     *      tags={"HRMSPersonalDocuments"},
     *      description="Update HRMSPersonalDocuments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSPersonalDocuments",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSPersonalDocuments that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSPersonalDocuments")
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
     *                  ref="#/definitions/HRMSPersonalDocuments"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHRMSPersonalDocumentsAPIRequest $request)
    {
        $input = $request->all();

        /** @var HRMSPersonalDocuments $hRMSPersonalDocuments */
        $hRMSPersonalDocuments = $this->hRMSPersonalDocumentsRepository->findWithoutFail($id);

        if (empty($hRMSPersonalDocuments)) {
            return $this->sendError(trans('custom.h_r_m_s_personal_documents_not_found'));
        }

        $hRMSPersonalDocuments = $this->hRMSPersonalDocumentsRepository->update($input, $id);

        return $this->sendResponse($hRMSPersonalDocuments->toArray(), trans('custom.hrmspersonaldocuments_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hRMSPersonalDocuments/{id}",
     *      summary="Remove the specified HRMSPersonalDocuments from storage",
     *      tags={"HRMSPersonalDocuments"},
     *      description="Delete HRMSPersonalDocuments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSPersonalDocuments",
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
        /** @var HRMSPersonalDocuments $hRMSPersonalDocuments */
        $hRMSPersonalDocuments = $this->hRMSPersonalDocumentsRepository->findWithoutFail($id);

        if (empty($hRMSPersonalDocuments)) {
            return $this->sendError(trans('custom.h_r_m_s_personal_documents_not_found'));
        }

        $hRMSPersonalDocuments->delete();

        return $this->sendResponse($id, trans('custom.h_r_m_s_personal_documents_deleted_successfully'));
    }
}
