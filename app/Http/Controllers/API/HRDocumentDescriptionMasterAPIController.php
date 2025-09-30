<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHRDocumentDescriptionMasterAPIRequest;
use App\Http\Requests\API\UpdateHRDocumentDescriptionMasterAPIRequest;
use App\Models\HRDocumentDescriptionMaster;
use App\Repositories\HRDocumentDescriptionMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HRDocumentDescriptionMasterController
 * @package App\Http\Controllers\API
 */

class HRDocumentDescriptionMasterAPIController extends AppBaseController
{
    /** @var  HRDocumentDescriptionMasterRepository */
    private $hRDocumentDescriptionMasterRepository;

    public function __construct(HRDocumentDescriptionMasterRepository $hRDocumentDescriptionMasterRepo)
    {
        $this->hRDocumentDescriptionMasterRepository = $hRDocumentDescriptionMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRDocumentDescriptionMasters",
     *      summary="Get a listing of the HRDocumentDescriptionMasters.",
     *      tags={"HRDocumentDescriptionMaster"},
     *      description="Get all HRDocumentDescriptionMasters",
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
     *                  @SWG\Items(ref="#/definitions/HRDocumentDescriptionMaster")
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
        $this->hRDocumentDescriptionMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->hRDocumentDescriptionMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hRDocumentDescriptionMasters = $this->hRDocumentDescriptionMasterRepository->all();

        return $this->sendResponse($hRDocumentDescriptionMasters->toArray(), trans('custom.h_r_document_description_masters_retrieved_success'));
    }

    /**
     * @param CreateHRDocumentDescriptionMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hRDocumentDescriptionMasters",
     *      summary="Store a newly created HRDocumentDescriptionMaster in storage",
     *      tags={"HRDocumentDescriptionMaster"},
     *      description="Store HRDocumentDescriptionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRDocumentDescriptionMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRDocumentDescriptionMaster")
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
     *                  ref="#/definitions/HRDocumentDescriptionMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHRDocumentDescriptionMasterAPIRequest $request)
    {
        $input = $request->all();

        $hRDocumentDescriptionMaster = $this->hRDocumentDescriptionMasterRepository->create($input);

        return $this->sendResponse($hRDocumentDescriptionMaster->toArray(), trans('custom.h_r_document_description_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRDocumentDescriptionMasters/{id}",
     *      summary="Display the specified HRDocumentDescriptionMaster",
     *      tags={"HRDocumentDescriptionMaster"},
     *      description="Get HRDocumentDescriptionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRDocumentDescriptionMaster",
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
     *                  ref="#/definitions/HRDocumentDescriptionMaster"
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
        /** @var HRDocumentDescriptionMaster $hRDocumentDescriptionMaster */
        $hRDocumentDescriptionMaster = $this->hRDocumentDescriptionMasterRepository->findWithoutFail($id);

        if (empty($hRDocumentDescriptionMaster)) {
            return $this->sendError(trans('custom.h_r_document_description_master_not_found'));
        }

        return $this->sendResponse($hRDocumentDescriptionMaster->toArray(), trans('custom.h_r_document_description_master_retrieved_successf'));
    }

    /**
     * @param int $id
     * @param UpdateHRDocumentDescriptionMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hRDocumentDescriptionMasters/{id}",
     *      summary="Update the specified HRDocumentDescriptionMaster in storage",
     *      tags={"HRDocumentDescriptionMaster"},
     *      description="Update HRDocumentDescriptionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRDocumentDescriptionMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRDocumentDescriptionMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRDocumentDescriptionMaster")
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
     *                  ref="#/definitions/HRDocumentDescriptionMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHRDocumentDescriptionMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var HRDocumentDescriptionMaster $hRDocumentDescriptionMaster */
        $hRDocumentDescriptionMaster = $this->hRDocumentDescriptionMasterRepository->findWithoutFail($id);

        if (empty($hRDocumentDescriptionMaster)) {
            return $this->sendError(trans('custom.h_r_document_description_master_not_found'));
        }

        $hRDocumentDescriptionMaster = $this->hRDocumentDescriptionMasterRepository->update($input, $id);

        return $this->sendResponse($hRDocumentDescriptionMaster->toArray(), trans('custom.hrdocumentdescriptionmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hRDocumentDescriptionMasters/{id}",
     *      summary="Remove the specified HRDocumentDescriptionMaster from storage",
     *      tags={"HRDocumentDescriptionMaster"},
     *      description="Delete HRDocumentDescriptionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRDocumentDescriptionMaster",
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
        /** @var HRDocumentDescriptionMaster $hRDocumentDescriptionMaster */
        $hRDocumentDescriptionMaster = $this->hRDocumentDescriptionMasterRepository->findWithoutFail($id);

        if (empty($hRDocumentDescriptionMaster)) {
            return $this->sendError(trans('custom.h_r_document_description_master_not_found'));
        }

        $hRDocumentDescriptionMaster->delete();

        return $this->sendSuccess('H R Document Description Master deleted successfully');
    }
}
