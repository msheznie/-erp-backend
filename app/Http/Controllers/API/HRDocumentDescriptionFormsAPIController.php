<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHRDocumentDescriptionFormsAPIRequest;
use App\Http\Requests\API\UpdateHRDocumentDescriptionFormsAPIRequest;
use App\Models\HRDocumentDescriptionForms;
use App\Repositories\HRDocumentDescriptionFormsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HRDocumentDescriptionFormsController
 * @package App\Http\Controllers\API
 */

class HRDocumentDescriptionFormsAPIController extends AppBaseController
{
    /** @var  HRDocumentDescriptionFormsRepository */
    private $hRDocumentDescriptionFormsRepository;

    public function __construct(HRDocumentDescriptionFormsRepository $hRDocumentDescriptionFormsRepo)
    {
        $this->hRDocumentDescriptionFormsRepository = $hRDocumentDescriptionFormsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRDocumentDescriptionForms",
     *      summary="Get a listing of the HRDocumentDescriptionForms.",
     *      tags={"HRDocumentDescriptionForms"},
     *      description="Get all HRDocumentDescriptionForms",
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
     *                  @SWG\Items(ref="#/definitions/HRDocumentDescriptionForms")
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
        $this->hRDocumentDescriptionFormsRepository->pushCriteria(new RequestCriteria($request));
        $this->hRDocumentDescriptionFormsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hRDocumentDescriptionForms = $this->hRDocumentDescriptionFormsRepository->all();

        return $this->sendResponse($hRDocumentDescriptionForms->toArray(), trans('custom.h_r_document_description_forms_retrieved_successfu'));
    }

    /**
     * @param CreateHRDocumentDescriptionFormsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hRDocumentDescriptionForms",
     *      summary="Store a newly created HRDocumentDescriptionForms in storage",
     *      tags={"HRDocumentDescriptionForms"},
     *      description="Store HRDocumentDescriptionForms",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRDocumentDescriptionForms that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRDocumentDescriptionForms")
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
     *                  ref="#/definitions/HRDocumentDescriptionForms"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHRDocumentDescriptionFormsAPIRequest $request)
    {
        $input = $request->all();

        $hRDocumentDescriptionForms = $this->hRDocumentDescriptionFormsRepository->create($input);

        return $this->sendResponse($hRDocumentDescriptionForms->toArray(), trans('custom.h_r_document_description_forms_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRDocumentDescriptionForms/{id}",
     *      summary="Display the specified HRDocumentDescriptionForms",
     *      tags={"HRDocumentDescriptionForms"},
     *      description="Get HRDocumentDescriptionForms",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRDocumentDescriptionForms",
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
     *                  ref="#/definitions/HRDocumentDescriptionForms"
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
        /** @var HRDocumentDescriptionForms $hRDocumentDescriptionForms */
        $hRDocumentDescriptionForms = $this->hRDocumentDescriptionFormsRepository->findWithoutFail($id);

        if (empty($hRDocumentDescriptionForms)) {
            return $this->sendError(trans('custom.h_r_document_description_forms_not_found'));
        }

        return $this->sendResponse($hRDocumentDescriptionForms->toArray(), trans('custom.h_r_document_description_forms_retrieved_successfu'));
    }

    /**
     * @param int $id
     * @param UpdateHRDocumentDescriptionFormsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hRDocumentDescriptionForms/{id}",
     *      summary="Update the specified HRDocumentDescriptionForms in storage",
     *      tags={"HRDocumentDescriptionForms"},
     *      description="Update HRDocumentDescriptionForms",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRDocumentDescriptionForms",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRDocumentDescriptionForms that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRDocumentDescriptionForms")
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
     *                  ref="#/definitions/HRDocumentDescriptionForms"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHRDocumentDescriptionFormsAPIRequest $request)
    {
        $input = $request->all();

        /** @var HRDocumentDescriptionForms $hRDocumentDescriptionForms */
        $hRDocumentDescriptionForms = $this->hRDocumentDescriptionFormsRepository->findWithoutFail($id);

        if (empty($hRDocumentDescriptionForms)) {
            return $this->sendError(trans('custom.h_r_document_description_forms_not_found'));
        }

        $hRDocumentDescriptionForms = $this->hRDocumentDescriptionFormsRepository->update($input, $id);

        return $this->sendResponse($hRDocumentDescriptionForms->toArray(), trans('custom.hrdocumentdescriptionforms_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hRDocumentDescriptionForms/{id}",
     *      summary="Remove the specified HRDocumentDescriptionForms from storage",
     *      tags={"HRDocumentDescriptionForms"},
     *      description="Delete HRDocumentDescriptionForms",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRDocumentDescriptionForms",
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
        /** @var HRDocumentDescriptionForms $hRDocumentDescriptionForms */
        $hRDocumentDescriptionForms = $this->hRDocumentDescriptionFormsRepository->findWithoutFail($id);

        if (empty($hRDocumentDescriptionForms)) {
            return $this->sendError(trans('custom.h_r_document_description_forms_not_found'));
        }

        $hRDocumentDescriptionForms->delete();

        return $this->sendSuccess('H R Document Description Forms deleted successfully');
    }
}
