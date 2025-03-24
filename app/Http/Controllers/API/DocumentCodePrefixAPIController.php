<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentCodePrefixAPIRequest;
use App\Http\Requests\API\UpdateDocumentCodePrefixAPIRequest;
use App\Models\DocumentCodePrefix;
use App\Repositories\DocumentCodePrefixRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentCodePrefixController
 * @package App\Http\Controllers\API
 */

class DocumentCodePrefixAPIController extends AppBaseController
{
    /** @var  DocumentCodePrefixRepository */
    private $documentCodePrefixRepository;

    public function __construct(DocumentCodePrefixRepository $documentCodePrefixRepo)
    {
        $this->documentCodePrefixRepository = $documentCodePrefixRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/documentCodePrefixes",
     *      summary="getDocumentCodePrefixList",
     *      tags={"DocumentCodePrefix"},
     *      description="Get all DocumentCodePrefixes",
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
     *                  @OA\Items(ref="#/definitions/DocumentCodePrefix")
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
        $this->documentCodePrefixRepository->pushCriteria(new RequestCriteria($request));
        $this->documentCodePrefixRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentCodePrefixes = $this->documentCodePrefixRepository->all();

        return $this->sendResponse($documentCodePrefixes->toArray(), 'Document Code Prefixes retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/documentCodePrefixes",
     *      summary="createDocumentCodePrefix",
     *      tags={"DocumentCodePrefix"},
     *      description="Create DocumentCodePrefix",
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
     *                  ref="#/definitions/DocumentCodePrefix"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentCodePrefixAPIRequest $request)
    {
        $input = $request->all();

        $documentCodePrefix = $this->documentCodePrefixRepository->create($input);

        return $this->sendResponse($documentCodePrefix->toArray(), 'Document Code Prefix saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/documentCodePrefixes/{id}",
     *      summary="getDocumentCodePrefixItem",
     *      tags={"DocumentCodePrefix"},
     *      description="Get DocumentCodePrefix",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodePrefix",
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
     *                  ref="#/definitions/DocumentCodePrefix"
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
        /** @var DocumentCodePrefix $documentCodePrefix */
        $documentCodePrefix = $this->documentCodePrefixRepository->findWithoutFail($id);

        if (empty($documentCodePrefix)) {
            return $this->sendError('Document Code Prefix not found');
        }

        return $this->sendResponse($documentCodePrefix->toArray(), 'Document Code Prefix retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/documentCodePrefixes/{id}",
     *      summary="updateDocumentCodePrefix",
     *      tags={"DocumentCodePrefix"},
     *      description="Update DocumentCodePrefix",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodePrefix",
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
     *                  ref="#/definitions/DocumentCodePrefix"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentCodePrefixAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentCodePrefix $documentCodePrefix */
        $documentCodePrefix = $this->documentCodePrefixRepository->findWithoutFail($id);

        if (empty($documentCodePrefix)) {
            return $this->sendError('Document Code Prefix not found');
        }

        $documentCodePrefix = $this->documentCodePrefixRepository->update($input, $id);

        return $this->sendResponse($documentCodePrefix->toArray(), 'DocumentCodePrefix updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/documentCodePrefixes/{id}",
     *      summary="deleteDocumentCodePrefix",
     *      tags={"DocumentCodePrefix"},
     *      description="Delete DocumentCodePrefix",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentCodePrefix",
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
        /** @var DocumentCodePrefix $documentCodePrefix */
        $documentCodePrefix = $this->documentCodePrefixRepository->findWithoutFail($id);

        if (empty($documentCodePrefix)) {
            return $this->sendError('Document Code Prefix not found');
        }

        $documentCodePrefix->delete();

        return $this->sendSuccess('Document Code Prefix deleted successfully');
    }

    public function getDocumentCodePrefix(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $isTypeBased = $input['isTypeBased'];
        $isCommonBased = $input['isCommonBased'];
        $formatNumber = $input['formatNumber'];
        $company_id = $input['company_id'];
        $type_based_id = $input['type_based_id'];
        $common_id = $input['common_id'];

        if($type_based_id > 0 ){

            $documentCodePrefix = DocumentCodePrefix::where('type_based_id', $type_based_id)
                                                    ->where('format', $formatNumber)
                                                    ->where('company_id', $company_id)
                                                    ->first();
            if (!$documentCodePrefix) {
                return $this->sendError('Document Code Prefix not found');
            }
    
            return $this->sendResponse($documentCodePrefix->toArray(), 'Document Code Prefix retrieved successfully');
        }
        
        if($common_id  > 0){
            $documentCodePrefix = DocumentCodePrefix::where('common_id', $common_id)
                                                    ->where('format', $formatNumber)
                                                    ->where('company_id', $company_id)
                                                    ->first();
            if (!$documentCodePrefix) {
                return $this->sendError('Document Code Prefix not found');
            }
    
            return $this->sendResponse($documentCodePrefix->toArray(), 'Document Code Prefix retrieved successfully');
        }

    }

    public function updateDocumentCodePrefix(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $id = $input['id'];

        $documentCodePrefix = $this->documentCodePrefixRepository->findWithoutFail($id);

        if (empty($documentCodePrefix)) {
            return $this->sendError('Document Code Prefix not found');
        }

        // Validation for common_id
        if ($input['common_id'] > 0) {
            $existingDescription = $this->documentCodePrefixRepository
                ->where('company_id', $documentCodePrefix->company_id)
                ->where('description', $input['description'])
                ->where(function ($query) use ($input) {
                    $query->where('common_id', '!=', $input['common_id']) // Different common_id
                        ->orWhereNotNull('type_based_id'); // Exists in type_based_id
                })
                ->exists();

            if ($existingDescription) {
                return $this->sendError('Description already exists for this company with a different transaction or type.');
            }
        }

        // Validation for type_based_id
        if ($input['type_based_id'] > 0) {
            $existingDescription = $this->documentCodePrefixRepository
                ->where('company_id', $documentCodePrefix->company_id)
                ->where('description', $input['description'])
                ->where(function ($query) use ($input) {
                    $query->where('type_based_id', '!=', $input['type_based_id']) // Different type_based_id
                        ->orWhereNotNull('common_id'); // Exists in common_id
                })
                ->exists();

            if ($existingDescription) {
                return $this->sendError('Description already exists for this company with a different type or transaction.');
            }
        }

        $updateDocumentCodePrefix = $this->documentCodePrefixRepository->update($input, $id);

        return $this->sendResponse($updateDocumentCodePrefix->toArray(), 'Document Code Prefix updated successfully');
    }
}
