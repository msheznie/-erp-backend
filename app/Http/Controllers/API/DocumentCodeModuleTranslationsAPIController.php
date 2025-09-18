<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentCodeModuleTranslationsAPIRequest;
use App\Http\Requests\API\UpdateDocumentCodeModuleTranslationsAPIRequest;
use App\Models\DocumentCodeModuleTranslations;
use App\Repositories\DocumentCodeModuleTranslationsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentCodeModuleTranslationsController
 * @package App\Http\Controllers\API
 */

class DocumentCodeModuleTranslationsAPIController extends AppBaseController
{
    /** @var  DocumentCodeModuleTranslationsRepository */
    private $documentCodeModuleTranslationsRepository;

    public function __construct(DocumentCodeModuleTranslationsRepository $documentCodeModuleTranslationsRepo)
    {
        $this->documentCodeModuleTranslationsRepository = $documentCodeModuleTranslationsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/documentCodeModuleTranslations",
     *      summary="getDocumentCodeModuleTranslationsList",
     *      tags={"DocumentCodeModuleTranslations"},
     *      description="Get all DocumentCodeModuleTranslations",
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
     *                  @OA\Items(ref="#/definitions/DocumentCodeModuleTranslations")
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
        $this->documentCodeModuleTranslationsRepository->pushCriteria(new RequestCriteria($request));
        $this->documentCodeModuleTranslationsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentCodeModuleTranslations = $this->documentCodeModuleTranslationsRepository->all();

        return $this->sendResponse($documentCodeModuleTranslations->toArray(), 'Document Code Module Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/documentCodeModuleTranslations",
     *      summary="createDocumentCodeModuleTranslations",
     *      tags={"DocumentCodeModuleTranslations"},
     *      description="Create DocumentCodeModuleTranslations",
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
     *                  ref="#/definitions/DocumentCodeModuleTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentCodeModuleTranslationsAPIRequest $request)
    {
        $input = $request->all();

        $documentCodeModuleTranslations = $this->documentCodeModuleTranslationsRepository->create($input);

        return $this->sendResponse($documentCodeModuleTranslations->toArray(), 'Document Code Module Translations saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/documentCodeModuleTranslations/{id}",
     *      summary="getDocumentCodeModuleTranslationsItem",
     *      tags={"DocumentCodeModuleTranslations"},
     *      description="Get DocumentCodeModuleTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeModuleTranslations",
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
     *                  ref="#/definitions/DocumentCodeModuleTranslations"
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
        /** @var DocumentCodeModuleTranslations $documentCodeModuleTranslations */
        $documentCodeModuleTranslations = $this->documentCodeModuleTranslationsRepository->findWithoutFail($id);

        if (empty($documentCodeModuleTranslations)) {
            return $this->sendError('Document Code Module Translations not found');
        }

        return $this->sendResponse($documentCodeModuleTranslations->toArray(), 'Document Code Module Translations retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/documentCodeModuleTranslations/{id}",
     *      summary="updateDocumentCodeModuleTranslations",
     *      tags={"DocumentCodeModuleTranslations"},
     *      description="Update DocumentCodeModuleTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeModuleTranslations",
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
     *                  ref="#/definitions/DocumentCodeModuleTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentCodeModuleTranslationsAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentCodeModuleTranslations $documentCodeModuleTranslations */
        $documentCodeModuleTranslations = $this->documentCodeModuleTranslationsRepository->findWithoutFail($id);

        if (empty($documentCodeModuleTranslations)) {
            return $this->sendError('Document Code Module Translations not found');
        }

        $documentCodeModuleTranslations = $this->documentCodeModuleTranslationsRepository->update($input, $id);

        return $this->sendResponse($documentCodeModuleTranslations->toArray(), 'DocumentCodeModuleTranslations updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/documentCodeModuleTranslations/{id}",
     *      summary="deleteDocumentCodeModuleTranslations",
     *      tags={"DocumentCodeModuleTranslations"},
     *      description="Delete DocumentCodeModuleTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeModuleTranslations",
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
        /** @var DocumentCodeModuleTranslations $documentCodeModuleTranslations */
        $documentCodeModuleTranslations = $this->documentCodeModuleTranslationsRepository->findWithoutFail($id);

        if (empty($documentCodeModuleTranslations)) {
            return $this->sendError('Document Code Module Translations not found');
        }

        $documentCodeModuleTranslations->delete();

        return $this->sendSuccess('Document Code Module Translations deleted successfully');
    }
}
