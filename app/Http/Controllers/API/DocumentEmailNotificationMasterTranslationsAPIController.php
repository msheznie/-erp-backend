<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentEmailNotificationMasterTranslationsAPIRequest;
use App\Http\Requests\API\UpdateDocumentEmailNotificationMasterTranslationsAPIRequest;
use App\Models\DocumentEmailNotificationMasterTranslations;
use App\Repositories\DocumentEmailNotificationMasterTranslationsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentEmailNotificationMasterTranslationsController
 * @package App\Http\Controllers\API
 */

class DocumentEmailNotificationMasterTranslationsAPIController extends AppBaseController
{
    /** @var  DocumentEmailNotificationMasterTranslationsRepository */
    private $documentEmailNotificationMasterTranslationsRepository;

    public function __construct(DocumentEmailNotificationMasterTranslationsRepository $documentEmailNotificationMasterTranslationsRepo)
    {
        $this->documentEmailNotificationMasterTranslationsRepository = $documentEmailNotificationMasterTranslationsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/documentEmailNotificationMasterTranslations",
     *      summary="getDocumentEmailNotificationMasterTranslationsList",
     *      tags={"DocumentEmailNotificationMasterTranslations"},
     *      description="Get all DocumentEmailNotificationMasterTranslations",
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
     *                  @OA\Items(ref="#/definitions/DocumentEmailNotificationMasterTranslations")
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
        $this->documentEmailNotificationMasterTranslationsRepository->pushCriteria(new RequestCriteria($request));
        $this->documentEmailNotificationMasterTranslationsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentEmailNotificationMasterTranslations = $this->documentEmailNotificationMasterTranslationsRepository->all();

        return $this->sendResponse($documentEmailNotificationMasterTranslations->toArray(), 'Document Email Notification Master Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/documentEmailNotificationMasterTranslations",
     *      summary="createDocumentEmailNotificationMasterTranslations",
     *      tags={"DocumentEmailNotificationMasterTranslations"},
     *      description="Create DocumentEmailNotificationMasterTranslations",
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
     *                  ref="#/definitions/DocumentEmailNotificationMasterTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentEmailNotificationMasterTranslationsAPIRequest $request)
    {
        $input = $request->all();

        $documentEmailNotificationMasterTranslations = $this->documentEmailNotificationMasterTranslationsRepository->create($input);

        return $this->sendResponse($documentEmailNotificationMasterTranslations->toArray(), 'Document Email Notification Master Translations saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/documentEmailNotificationMasterTranslations/{id}",
     *      summary="getDocumentEmailNotificationMasterTranslationsItem",
     *      tags={"DocumentEmailNotificationMasterTranslations"},
     *      description="Get DocumentEmailNotificationMasterTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentEmailNotificationMasterTranslations",
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
     *                  ref="#/definitions/DocumentEmailNotificationMasterTranslations"
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
        /** @var DocumentEmailNotificationMasterTranslations $documentEmailNotificationMasterTranslations */
        $documentEmailNotificationMasterTranslations = $this->documentEmailNotificationMasterTranslationsRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationMasterTranslations)) {
            return $this->sendError('Document Email Notification Master Translations not found');
        }

        return $this->sendResponse($documentEmailNotificationMasterTranslations->toArray(), 'Document Email Notification Master Translations retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/documentEmailNotificationMasterTranslations/{id}",
     *      summary="updateDocumentEmailNotificationMasterTranslations",
     *      tags={"DocumentEmailNotificationMasterTranslations"},
     *      description="Update DocumentEmailNotificationMasterTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentEmailNotificationMasterTranslations",
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
     *                  ref="#/definitions/DocumentEmailNotificationMasterTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentEmailNotificationMasterTranslationsAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentEmailNotificationMasterTranslations $documentEmailNotificationMasterTranslations */
        $documentEmailNotificationMasterTranslations = $this->documentEmailNotificationMasterTranslationsRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationMasterTranslations)) {
            return $this->sendError('Document Email Notification Master Translations not found');
        }

        $documentEmailNotificationMasterTranslations = $this->documentEmailNotificationMasterTranslationsRepository->update($input, $id);

        return $this->sendResponse($documentEmailNotificationMasterTranslations->toArray(), 'DocumentEmailNotificationMasterTranslations updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/documentEmailNotificationMasterTranslations/{id}",
     *      summary="deleteDocumentEmailNotificationMasterTranslations",
     *      tags={"DocumentEmailNotificationMasterTranslations"},
     *      description="Delete DocumentEmailNotificationMasterTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentEmailNotificationMasterTranslations",
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
        /** @var DocumentEmailNotificationMasterTranslations $documentEmailNotificationMasterTranslations */
        $documentEmailNotificationMasterTranslations = $this->documentEmailNotificationMasterTranslationsRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationMasterTranslations)) {
            return $this->sendError('Document Email Notification Master Translations not found');
        }

        $documentEmailNotificationMasterTranslations->delete();

        return $this->sendSuccess('Document Email Notification Master Translations deleted successfully');
    }
}
