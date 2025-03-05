<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentCodeModuleAPIRequest;
use App\Http\Requests\API\UpdateDocumentCodeModuleAPIRequest;
use App\Models\DocumentCodeModule;
use App\Repositories\DocumentCodeModuleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentCodeModuleController
 * @package App\Http\Controllers\API
 */

class DocumentCodeModuleAPIController extends AppBaseController
{
    /** @var  DocumentCodeModuleRepository */
    private $documentCodeModuleRepository;

    public function __construct(DocumentCodeModuleRepository $documentCodeModuleRepo)
    {
        $this->documentCodeModuleRepository = $documentCodeModuleRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/documentCodeModules",
     *      summary="getDocumentCodeModuleList",
     *      tags={"DocumentCodeModule"},
     *      description="Get all DocumentCodeModules",
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
     *                  @OA\Items(ref="#/definitions/DocumentCodeModule")
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
        $this->documentCodeModuleRepository->pushCriteria(new RequestCriteria($request));
        $this->documentCodeModuleRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentCodeModules = $this->documentCodeModuleRepository->all();

        return $this->sendResponse($documentCodeModules->toArray(), 'Document Code Modules retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/documentCodeModules",
     *      summary="createDocumentCodeModule",
     *      tags={"DocumentCodeModule"},
     *      description="Create DocumentCodeModule",
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
     *                  ref="#/definitions/DocumentCodeModule"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentCodeModuleAPIRequest $request)
    {
        $input = $request->all();

        $documentCodeModule = $this->documentCodeModuleRepository->create($input);

        return $this->sendResponse($documentCodeModule->toArray(), 'Document Code Module saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/documentCodeModules/{id}",
     *      summary="getDocumentCodeModuleItem",
     *      tags={"DocumentCodeModule"},
     *      description="Get DocumentCodeModule",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeModule",
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
     *                  ref="#/definitions/DocumentCodeModule"
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
        /** @var DocumentCodeModule $documentCodeModule */
        $documentCodeModule = $this->documentCodeModuleRepository->findWithoutFail($id);

        if (empty($documentCodeModule)) {
            return $this->sendError('Document Code Module not found');
        }

        return $this->sendResponse($documentCodeModule->toArray(), 'Document Code Module retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/documentCodeModules/{id}",
     *      summary="updateDocumentCodeModule",
     *      tags={"DocumentCodeModule"},
     *      description="Update DocumentCodeModule",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeModule",
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
     *                  ref="#/definitions/DocumentCodeModule"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentCodeModuleAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentCodeModule $documentCodeModule */
        $documentCodeModule = $this->documentCodeModuleRepository->findWithoutFail($id);

        if (empty($documentCodeModule)) {
            return $this->sendError('Document Code Module not found');
        }

        $documentCodeModule = $this->documentCodeModuleRepository->update($input, $id);

        return $this->sendResponse($documentCodeModule->toArray(), 'DocumentCodeModule updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/documentCodeModules/{id}",
     *      summary="deleteDocumentCodeModule",
     *      tags={"DocumentCodeModule"},
     *      description="Delete DocumentCodeModule",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeModule",
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
        /** @var DocumentCodeModule $documentCodeModule */
        $documentCodeModule = $this->documentCodeModuleRepository->findWithoutFail($id);

        if (empty($documentCodeModule)) {
            return $this->sendError('Document Code Module not found');
        }

        $documentCodeModule->delete();

        return $this->sendSuccess('Document Code Module deleted successfully');
    }
}
