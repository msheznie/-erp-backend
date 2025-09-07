<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTemplatesGLCodeAPIRequest;
use App\Http\Requests\API\UpdateTemplatesGLCodeAPIRequest;
use App\Models\TemplatesGLCode;
use App\Repositories\TemplatesGLCodeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TemplatesGLCodeController
 * @package App\Http\Controllers\API
 */

class TemplatesGLCodeAPIController extends AppBaseController
{
    /** @var  TemplatesGLCodeRepository */
    private $templatesGLCodeRepository;

    public function __construct(TemplatesGLCodeRepository $templatesGLCodeRepo)
    {
        $this->templatesGLCodeRepository = $templatesGLCodeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/templatesGLCodes",
     *      summary="Get a listing of the TemplatesGLCodes.",
     *      tags={"TemplatesGLCode"},
     *      description="Get all TemplatesGLCodes",
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
     *                  @SWG\Items(ref="#/definitions/TemplatesGLCode")
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
        $this->templatesGLCodeRepository->pushCriteria(new RequestCriteria($request));
        $this->templatesGLCodeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $templatesGLCodes = $this->templatesGLCodeRepository->all();

        return $this->sendResponse($templatesGLCodes->toArray(), trans('custom.templates_g_l_codes_retrieved_successfully'));
    }

    /**
     * @param CreateTemplatesGLCodeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/templatesGLCodes",
     *      summary="Store a newly created TemplatesGLCode in storage",
     *      tags={"TemplatesGLCode"},
     *      description="Store TemplatesGLCode",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TemplatesGLCode that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TemplatesGLCode")
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
     *                  ref="#/definitions/TemplatesGLCode"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTemplatesGLCodeAPIRequest $request)
    {
        $input = $request->all();

        $templatesGLCodes = $this->templatesGLCodeRepository->create($input);

        return $this->sendResponse($templatesGLCodes->toArray(), trans('custom.templates_g_l_code_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/templatesGLCodes/{id}",
     *      summary="Display the specified TemplatesGLCode",
     *      tags={"TemplatesGLCode"},
     *      description="Get TemplatesGLCode",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TemplatesGLCode",
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
     *                  ref="#/definitions/TemplatesGLCode"
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
        /** @var TemplatesGLCode $templatesGLCode */
        $templatesGLCode = $this->templatesGLCodeRepository->findWithoutFail($id);

        if (empty($templatesGLCode)) {
            return $this->sendError(trans('custom.templates_g_l_code_not_found'));
        }

        return $this->sendResponse($templatesGLCode->toArray(), trans('custom.templates_g_l_code_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTemplatesGLCodeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/templatesGLCodes/{id}",
     *      summary="Update the specified TemplatesGLCode in storage",
     *      tags={"TemplatesGLCode"},
     *      description="Update TemplatesGLCode",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TemplatesGLCode",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TemplatesGLCode that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TemplatesGLCode")
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
     *                  ref="#/definitions/TemplatesGLCode"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTemplatesGLCodeAPIRequest $request)
    {
        $input = $request->all();

        /** @var TemplatesGLCode $templatesGLCode */
        $templatesGLCode = $this->templatesGLCodeRepository->findWithoutFail($id);

        if (empty($templatesGLCode)) {
            return $this->sendError(trans('custom.templates_g_l_code_not_found'));
        }

        $templatesGLCode = $this->templatesGLCodeRepository->update($input, $id);

        return $this->sendResponse($templatesGLCode->toArray(), trans('custom.templatesglcode_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/templatesGLCodes/{id}",
     *      summary="Remove the specified TemplatesGLCode from storage",
     *      tags={"TemplatesGLCode"},
     *      description="Delete TemplatesGLCode",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TemplatesGLCode",
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
        /** @var TemplatesGLCode $templatesGLCode */
        $templatesGLCode = $this->templatesGLCodeRepository->findWithoutFail($id);

        if (empty($templatesGLCode)) {
            return $this->sendError(trans('custom.templates_g_l_code_not_found'));
        }

        $templatesGLCode->delete();

        return $this->sendResponse($id, trans('custom.templates_g_l_code_deleted_successfully'));
    }
}
