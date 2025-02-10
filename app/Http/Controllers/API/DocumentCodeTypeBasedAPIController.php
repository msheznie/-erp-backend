<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentCodeTypeBasedAPIRequest;
use App\Http\Requests\API\UpdateDocumentCodeTypeBasedAPIRequest;
use App\Models\DocumentCodeTypeBased;
use App\Repositories\DocumentCodeTypeBasedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentCodeTypeBasedController
 * @package App\Http\Controllers\API
 */

class DocumentCodeTypeBasedAPIController extends AppBaseController
{
    /** @var  DocumentCodeTypeBasedRepository */
    private $documentCodeTypeBasedRepository;

    public function __construct(DocumentCodeTypeBasedRepository $documentCodeTypeBasedRepo)
    {
        $this->documentCodeTypeBasedRepository = $documentCodeTypeBasedRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/documentCodeTypeBaseds",
     *      summary="getDocumentCodeTypeBasedList",
     *      tags={"DocumentCodeTypeBased"},
     *      description="Get all DocumentCodeTypeBaseds",
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
     *                  @OA\Items(ref="#/definitions/DocumentCodeTypeBased")
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
        $this->documentCodeTypeBasedRepository->pushCriteria(new RequestCriteria($request));
        $this->documentCodeTypeBasedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentCodeTypeBaseds = $this->documentCodeTypeBasedRepository->all();

        return $this->sendResponse($documentCodeTypeBaseds->toArray(), 'Document Code Type Baseds retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/documentCodeTypeBaseds",
     *      summary="createDocumentCodeTypeBased",
     *      tags={"DocumentCodeTypeBased"},
     *      description="Create DocumentCodeTypeBased",
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
     *                  ref="#/definitions/DocumentCodeTypeBased"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentCodeTypeBasedAPIRequest $request)
    {
        $input = $request->all();

        $documentCodeTypeBased = $this->documentCodeTypeBasedRepository->create($input);

        return $this->sendResponse($documentCodeTypeBased->toArray(), 'Document Code Type Based saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/documentCodeTypeBaseds/{id}",
     *      summary="getDocumentCodeTypeBasedItem",
     *      tags={"DocumentCodeTypeBased"},
     *      description="Get DocumentCodeTypeBased",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeTypeBased",
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
     *                  ref="#/definitions/DocumentCodeTypeBased"
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
        /** @var DocumentCodeTypeBased $documentCodeTypeBased */
        $documentCodeTypeBased = $this->documentCodeTypeBasedRepository->findWithoutFail($id);

        if (empty($documentCodeTypeBased)) {
            return $this->sendError('Document Code Type Based not found');
        }

        return $this->sendResponse($documentCodeTypeBased->toArray(), 'Document Code Type Based retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/documentCodeTypeBaseds/{id}",
     *      summary="updateDocumentCodeTypeBased",
     *      tags={"DocumentCodeTypeBased"},
     *      description="Update DocumentCodeTypeBased",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeTypeBased",
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
     *                  ref="#/definitions/DocumentCodeTypeBased"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentCodeTypeBasedAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentCodeTypeBased $documentCodeTypeBased */
        $documentCodeTypeBased = $this->documentCodeTypeBasedRepository->findWithoutFail($id);

        if (empty($documentCodeTypeBased)) {
            return $this->sendError('Document Code Type Based not found');
        }

        $documentCodeTypeBased = $this->documentCodeTypeBasedRepository->update($input, $id);

        return $this->sendResponse($documentCodeTypeBased->toArray(), 'DocumentCodeTypeBased updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/documentCodeTypeBaseds/{id}",
     *      summary="deleteDocumentCodeTypeBased",
     *      tags={"DocumentCodeTypeBased"},
     *      description="Delete DocumentCodeTypeBased",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeTypeBased",
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
        /** @var DocumentCodeTypeBased $documentCodeTypeBased */
        $documentCodeTypeBased = $this->documentCodeTypeBasedRepository->findWithoutFail($id);

        if (empty($documentCodeTypeBased)) {
            return $this->sendError('Document Code Type Based not found');
        }

        $documentCodeTypeBased->delete();

        return $this->sendSuccess('Document Code Type Based deleted successfully');
    }
}
