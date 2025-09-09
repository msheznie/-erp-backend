<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentCodeFormatAPIRequest;
use App\Http\Requests\API\UpdateDocumentCodeFormatAPIRequest;
use App\Models\DocumentCodeFormat;
use App\Repositories\DocumentCodeFormatRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentCodeFormatController
 * @package App\Http\Controllers\API
 */

class DocumentCodeFormatAPIController extends AppBaseController
{
    /** @var  DocumentCodeFormatRepository */
    private $documentCodeFormatRepository;

    public function __construct(DocumentCodeFormatRepository $documentCodeFormatRepo)
    {
        $this->documentCodeFormatRepository = $documentCodeFormatRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/documentCodeFormats",
     *      summary="getDocumentCodeFormatList",
     *      tags={"DocumentCodeFormat"},
     *      description="Get all DocumentCodeFormats",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/DocumentCodeFormat")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->documentCodeFormatRepository->pushCriteria(new RequestCriteria($request));
        $this->documentCodeFormatRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentCodeFormats = $this->documentCodeFormatRepository->all();

        return $this->sendResponse($documentCodeFormats->toArray(), trans('custom.document_code_formats_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/documentCodeFormats",
     *      summary="createDocumentCodeFormat",
     *      tags={"DocumentCodeFormat"},
     *      description="Create DocumentCodeFormat",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/DocumentCodeFormat"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentCodeFormatAPIRequest $request)
    {
        $input = $request->all();

        $documentCodeFormat = $this->documentCodeFormatRepository->create($input);

        return $this->sendResponse($documentCodeFormat->toArray(), trans('custom.document_code_format_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/documentCodeFormats/{id}",
     *      summary="getDocumentCodeFormatItem",
     *      tags={"DocumentCodeFormat"},
     *      description="Get DocumentCodeFormat",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeFormat",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/DocumentCodeFormat"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var DocumentCodeFormat $documentCodeFormat */
        $documentCodeFormat = $this->documentCodeFormatRepository->findWithoutFail($id);

        if (empty($documentCodeFormat)) {
            return $this->sendError(trans('custom.document_code_format_not_found'));
        }

        return $this->sendResponse($documentCodeFormat->toArray(), trans('custom.document_code_format_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/documentCodeFormats/{id}",
     *      summary="updateDocumentCodeFormat",
     *      tags={"DocumentCodeFormat"},
     *      description="Update DocumentCodeFormat",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeFormat",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/DocumentCodeFormat"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentCodeFormatAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentCodeFormat $documentCodeFormat */
        $documentCodeFormat = $this->documentCodeFormatRepository->findWithoutFail($id);

        if (empty($documentCodeFormat)) {
            return $this->sendError(trans('custom.document_code_format_not_found'));
        }

        $documentCodeFormat = $this->documentCodeFormatRepository->update($input, $id);

        return $this->sendResponse($documentCodeFormat->toArray(), trans('custom.documentcodeformat_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/documentCodeFormats/{id}",
     *      summary="deleteDocumentCodeFormat",
     *      tags={"DocumentCodeFormat"},
     *      description="Delete DocumentCodeFormat",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeFormat",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var DocumentCodeFormat $documentCodeFormat */
        $documentCodeFormat = $this->documentCodeFormatRepository->findWithoutFail($id);

        if (empty($documentCodeFormat)) {
            return $this->sendError(trans('custom.document_code_format_not_found'));
        }

        $documentCodeFormat->delete();

        return $this->sendSuccess('Document Code Format deleted successfully');
    }
}
