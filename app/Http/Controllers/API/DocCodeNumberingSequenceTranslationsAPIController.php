<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocCodeNumberingSequenceTranslationsAPIRequest;
use App\Http\Requests\API\UpdateDocCodeNumberingSequenceTranslationsAPIRequest;
use App\Models\DocCodeNumberingSequenceTranslations;
use App\Repositories\DocCodeNumberingSequenceTranslationsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocCodeNumberingSequenceTranslationsController
 * @package App\Http\Controllers\API
 */

class DocCodeNumberingSequenceTranslationsAPIController extends AppBaseController
{
    /** @var  DocCodeNumberingSequenceTranslationsRepository */
    private $docCodeNumberingSequenceTranslationsRepository;

    public function __construct(DocCodeNumberingSequenceTranslationsRepository $docCodeNumberingSequenceTranslationsRepo)
    {
        $this->docCodeNumberingSequenceTranslationsRepository = $docCodeNumberingSequenceTranslationsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/docCodeNumberingSequenceTranslations",
     *      summary="getDocCodeNumberingSequenceTranslationsList",
     *      tags={"DocCodeNumberingSequenceTranslations"},
     *      description="Get all DocCodeNumberingSequenceTranslations",
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
     *                  @OA\Items(ref="#/definitions/DocCodeNumberingSequenceTranslations")
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
        $this->docCodeNumberingSequenceTranslationsRepository->pushCriteria(new RequestCriteria($request));
        $this->docCodeNumberingSequenceTranslationsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $docCodeNumberingSequenceTranslations = $this->docCodeNumberingSequenceTranslationsRepository->all();

        return $this->sendResponse($docCodeNumberingSequenceTranslations->toArray(), 'Doc Code Numbering Sequence Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/docCodeNumberingSequenceTranslations",
     *      summary="createDocCodeNumberingSequenceTranslations",
     *      tags={"DocCodeNumberingSequenceTranslations"},
     *      description="Create DocCodeNumberingSequenceTranslations",
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
     *                  ref="#/definitions/DocCodeNumberingSequenceTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocCodeNumberingSequenceTranslationsAPIRequest $request)
    {
        $input = $request->all();

        $docCodeNumberingSequenceTranslations = $this->docCodeNumberingSequenceTranslationsRepository->create($input);

        return $this->sendResponse($docCodeNumberingSequenceTranslations->toArray(), 'Doc Code Numbering Sequence Translations saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/docCodeNumberingSequenceTranslations/{id}",
     *      summary="getDocCodeNumberingSequenceTranslationsItem",
     *      tags={"DocCodeNumberingSequenceTranslations"},
     *      description="Get DocCodeNumberingSequenceTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocCodeNumberingSequenceTranslations",
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
     *                  ref="#/definitions/DocCodeNumberingSequenceTranslations"
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
        /** @var DocCodeNumberingSequenceTranslations $docCodeNumberingSequenceTranslations */
        $docCodeNumberingSequenceTranslations = $this->docCodeNumberingSequenceTranslationsRepository->findWithoutFail($id);

        if (empty($docCodeNumberingSequenceTranslations)) {
            return $this->sendError('Doc Code Numbering Sequence Translations not found');
        }

        return $this->sendResponse($docCodeNumberingSequenceTranslations->toArray(), 'Doc Code Numbering Sequence Translations retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/docCodeNumberingSequenceTranslations/{id}",
     *      summary="updateDocCodeNumberingSequenceTranslations",
     *      tags={"DocCodeNumberingSequenceTranslations"},
     *      description="Update DocCodeNumberingSequenceTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocCodeNumberingSequenceTranslations",
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
     *                  ref="#/definitions/DocCodeNumberingSequenceTranslations"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocCodeNumberingSequenceTranslationsAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocCodeNumberingSequenceTranslations $docCodeNumberingSequenceTranslations */
        $docCodeNumberingSequenceTranslations = $this->docCodeNumberingSequenceTranslationsRepository->findWithoutFail($id);

        if (empty($docCodeNumberingSequenceTranslations)) {
            return $this->sendError('Doc Code Numbering Sequence Translations not found');
        }

        $docCodeNumberingSequenceTranslations = $this->docCodeNumberingSequenceTranslationsRepository->update($input, $id);

        return $this->sendResponse($docCodeNumberingSequenceTranslations->toArray(), 'DocCodeNumberingSequenceTranslations updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/docCodeNumberingSequenceTranslations/{id}",
     *      summary="deleteDocCodeNumberingSequenceTranslations",
     *      tags={"DocCodeNumberingSequenceTranslations"},
     *      description="Delete DocCodeNumberingSequenceTranslations",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocCodeNumberingSequenceTranslations",
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
        /** @var DocCodeNumberingSequenceTranslations $docCodeNumberingSequenceTranslations */
        $docCodeNumberingSequenceTranslations = $this->docCodeNumberingSequenceTranslationsRepository->findWithoutFail($id);

        if (empty($docCodeNumberingSequenceTranslations)) {
            return $this->sendError('Doc Code Numbering Sequence Translations not found');
        }

        $docCodeNumberingSequenceTranslations->delete();

        return $this->sendSuccess('Doc Code Numbering Sequence Translations deleted successfully');
    }
}
