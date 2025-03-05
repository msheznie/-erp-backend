<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentCodeTransactionAPIRequest;
use App\Http\Requests\API\UpdateDocumentCodeTransactionAPIRequest;
use App\Models\DocumentCodeTransaction;
use App\Repositories\DocumentCodeTransactionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentCodeTransactionController
 * @package App\Http\Controllers\API
 */

class DocumentCodeTransactionAPIController extends AppBaseController
{
    /** @var  DocumentCodeTransactionRepository */
    private $documentCodeTransactionRepository;

    public function __construct(DocumentCodeTransactionRepository $documentCodeTransactionRepo)
    {
        $this->documentCodeTransactionRepository = $documentCodeTransactionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/documentCodeTransactions",
     *      summary="getDocumentCodeTransactionList",
     *      tags={"DocumentCodeTransaction"},
     *      description="Get all DocumentCodeTransactions",
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
     *                  @OA\Items(ref="#/definitions/DocumentCodeTransaction")
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
        $this->documentCodeTransactionRepository->pushCriteria(new RequestCriteria($request));
        $this->documentCodeTransactionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentCodeTransactions = $this->documentCodeTransactionRepository->all();

        return $this->sendResponse($documentCodeTransactions->toArray(), 'Document Code Transactions retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/documentCodeTransactions",
     *      summary="createDocumentCodeTransaction",
     *      tags={"DocumentCodeTransaction"},
     *      description="Create DocumentCodeTransaction",
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
     *                  ref="#/definitions/DocumentCodeTransaction"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentCodeTransactionAPIRequest $request)
    {
        $input = $request->all();

        $documentCodeTransaction = $this->documentCodeTransactionRepository->create($input);

        return $this->sendResponse($documentCodeTransaction->toArray(), 'Document Code Transaction saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/documentCodeTransactions/{id}",
     *      summary="getDocumentCodeTransactionItem",
     *      tags={"DocumentCodeTransaction"},
     *      description="Get DocumentCodeTransaction",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeTransaction",
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
     *                  ref="#/definitions/DocumentCodeTransaction"
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
        /** @var DocumentCodeTransaction $documentCodeTransaction */
        $documentCodeTransaction = $this->documentCodeTransactionRepository->findWithoutFail($id);

        if (empty($documentCodeTransaction)) {
            return $this->sendError('Document Code Transaction not found');
        }

        return $this->sendResponse($documentCodeTransaction->toArray(), 'Document Code Transaction retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/documentCodeTransactions/{id}",
     *      summary="updateDocumentCodeTransaction",
     *      tags={"DocumentCodeTransaction"},
     *      description="Update DocumentCodeTransaction",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeTransaction",
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
     *                  ref="#/definitions/DocumentCodeTransaction"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentCodeTransactionAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentCodeTransaction $documentCodeTransaction */
        $documentCodeTransaction = $this->documentCodeTransactionRepository->findWithoutFail($id);

        if (empty($documentCodeTransaction)) {
            return $this->sendError('Document Code Transaction not found');
        }

        $documentCodeTransaction = $this->documentCodeTransactionRepository->update($input, $id);

        return $this->sendResponse($documentCodeTransaction->toArray(), 'DocumentCodeTransaction updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/documentCodeTransactions/{id}",
     *      summary="deleteDocumentCodeTransaction",
     *      tags={"DocumentCodeTransaction"},
     *      description="Delete DocumentCodeTransaction",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodeTransaction",
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
        /** @var DocumentCodeTransaction $documentCodeTransaction */
        $documentCodeTransaction = $this->documentCodeTransactionRepository->findWithoutFail($id);

        if (empty($documentCodeTransaction)) {
            return $this->sendError('Document Code Transaction not found');
        }

        $documentCodeTransaction->delete();

        return $this->sendSuccess('Document Code Transaction deleted successfully');
    }
}
