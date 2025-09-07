<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocCodeNumberingSequenceAPIRequest;
use App\Http\Requests\API\UpdateDocCodeNumberingSequenceAPIRequest;
use App\Models\DocCodeNumberingSequence;
use App\Repositories\DocCodeNumberingSequenceRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocCodeNumberingSequenceController
 * @package App\Http\Controllers\API
 */

class DocCodeNumberingSequenceAPIController extends AppBaseController
{
    /** @var  DocCodeNumberingSequenceRepository */
    private $docCodeNumberingSequenceRepository;

    public function __construct(DocCodeNumberingSequenceRepository $docCodeNumberingSequenceRepo)
    {
        $this->docCodeNumberingSequenceRepository = $docCodeNumberingSequenceRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/docCodeNumberingSequences",
     *      summary="getDocCodeNumberingSequenceList",
     *      tags={"DocCodeNumberingSequence"},
     *      description="Get all DocCodeNumberingSequences",
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
     *                  @OA\Items(ref="#/definitions/DocCodeNumberingSequence")
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
        $this->docCodeNumberingSequenceRepository->pushCriteria(new RequestCriteria($request));
        $this->docCodeNumberingSequenceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $docCodeNumberingSequences = $this->docCodeNumberingSequenceRepository->all();

        return $this->sendResponse($docCodeNumberingSequences->toArray(), trans('custom.doc_code_numbering_sequences_retrieved_successfull'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/docCodeNumberingSequences",
     *      summary="createDocCodeNumberingSequence",
     *      tags={"DocCodeNumberingSequence"},
     *      description="Create DocCodeNumberingSequence",
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
     *                  ref="#/definitions/DocCodeNumberingSequence"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocCodeNumberingSequenceAPIRequest $request)
    {
        $input = $request->all();

        $docCodeNumberingSequence = $this->docCodeNumberingSequenceRepository->create($input);

        return $this->sendResponse($docCodeNumberingSequence->toArray(), trans('custom.doc_code_numbering_sequence_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/docCodeNumberingSequences/{id}",
     *      summary="getDocCodeNumberingSequenceItem",
     *      tags={"DocCodeNumberingSequence"},
     *      description="Get DocCodeNumberingSequence",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocCodeNumberingSequence",
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
     *                  ref="#/definitions/DocCodeNumberingSequence"
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
        /** @var DocCodeNumberingSequence $docCodeNumberingSequence */
        $docCodeNumberingSequence = $this->docCodeNumberingSequenceRepository->findWithoutFail($id);

        if (empty($docCodeNumberingSequence)) {
            return $this->sendError(trans('custom.doc_code_numbering_sequence_not_found'));
        }

        return $this->sendResponse($docCodeNumberingSequence->toArray(), trans('custom.doc_code_numbering_sequence_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/docCodeNumberingSequences/{id}",
     *      summary="updateDocCodeNumberingSequence",
     *      tags={"DocCodeNumberingSequence"},
     *      description="Update DocCodeNumberingSequence",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocCodeNumberingSequence",
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
     *                  ref="#/definitions/DocCodeNumberingSequence"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocCodeNumberingSequenceAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocCodeNumberingSequence $docCodeNumberingSequence */
        $docCodeNumberingSequence = $this->docCodeNumberingSequenceRepository->findWithoutFail($id);

        if (empty($docCodeNumberingSequence)) {
            return $this->sendError(trans('custom.doc_code_numbering_sequence_not_found'));
        }

        $docCodeNumberingSequence = $this->docCodeNumberingSequenceRepository->update($input, $id);

        return $this->sendResponse($docCodeNumberingSequence->toArray(), trans('custom.doccodenumberingsequence_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/docCodeNumberingSequences/{id}",
     *      summary="deleteDocCodeNumberingSequence",
     *      tags={"DocCodeNumberingSequence"},
     *      description="Delete DocCodeNumberingSequence",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocCodeNumberingSequence",
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
        /** @var DocCodeNumberingSequence $docCodeNumberingSequence */
        $docCodeNumberingSequence = $this->docCodeNumberingSequenceRepository->findWithoutFail($id);

        if (empty($docCodeNumberingSequence)) {
            return $this->sendError(trans('custom.doc_code_numbering_sequence_not_found'));
        }

        $docCodeNumberingSequence->delete();

        return $this->sendSuccess('Doc Code Numbering Sequence deleted successfully');
    }
}
