<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentSubProductAPIRequest;
use App\Http\Requests\API\UpdateDocumentSubProductAPIRequest;
use App\Models\DocumentSubProduct;
use App\Repositories\DocumentSubProductRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentSubProductController
 * @package App\Http\Controllers\API
 */

class DocumentSubProductAPIController extends AppBaseController
{
    /** @var  DocumentSubProductRepository */
    private $documentSubProductRepository;

    public function __construct(DocumentSubProductRepository $documentSubProductRepo)
    {
        $this->documentSubProductRepository = $documentSubProductRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/documentSubProducts",
     *      summary="Get a listing of the DocumentSubProducts.",
     *      tags={"DocumentSubProduct"},
     *      description="Get all DocumentSubProducts",
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
     *                  @SWG\Items(ref="#/definitions/DocumentSubProduct")
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
        $this->documentSubProductRepository->pushCriteria(new RequestCriteria($request));
        $this->documentSubProductRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentSubProducts = $this->documentSubProductRepository->all();

        return $this->sendResponse($documentSubProducts->toArray(), trans('custom.document_sub_products_retrieved_successfully'));
    }

    /**
     * @param CreateDocumentSubProductAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/documentSubProducts",
     *      summary="Store a newly created DocumentSubProduct in storage",
     *      tags={"DocumentSubProduct"},
     *      description="Store DocumentSubProduct",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DocumentSubProduct that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DocumentSubProduct")
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
     *                  ref="#/definitions/DocumentSubProduct"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentSubProductAPIRequest $request)
    {
        $input = $request->all();

        $documentSubProduct = $this->documentSubProductRepository->create($input);

        return $this->sendResponse($documentSubProduct->toArray(), trans('custom.document_sub_product_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/documentSubProducts/{id}",
     *      summary="Display the specified DocumentSubProduct",
     *      tags={"DocumentSubProduct"},
     *      description="Get DocumentSubProduct",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentSubProduct",
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
     *                  ref="#/definitions/DocumentSubProduct"
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
        /** @var DocumentSubProduct $documentSubProduct */
        $documentSubProduct = $this->documentSubProductRepository->findWithoutFail($id);

        if (empty($documentSubProduct)) {
            return $this->sendError(trans('custom.document_sub_product_not_found'));
        }

        return $this->sendResponse($documentSubProduct->toArray(), trans('custom.document_sub_product_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateDocumentSubProductAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/documentSubProducts/{id}",
     *      summary="Update the specified DocumentSubProduct in storage",
     *      tags={"DocumentSubProduct"},
     *      description="Update DocumentSubProduct",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentSubProduct",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DocumentSubProduct that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DocumentSubProduct")
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
     *                  ref="#/definitions/DocumentSubProduct"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentSubProductAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentSubProduct $documentSubProduct */
        $documentSubProduct = $this->documentSubProductRepository->findWithoutFail($id);

        if (empty($documentSubProduct)) {
            return $this->sendError(trans('custom.document_sub_product_not_found'));
        }

        $documentSubProduct = $this->documentSubProductRepository->update($input, $id);

        return $this->sendResponse($documentSubProduct->toArray(), trans('custom.documentsubproduct_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/documentSubProducts/{id}",
     *      summary="Remove the specified DocumentSubProduct from storage",
     *      tags={"DocumentSubProduct"},
     *      description="Delete DocumentSubProduct",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentSubProduct",
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
        /** @var DocumentSubProduct $documentSubProduct */
        $documentSubProduct = $this->documentSubProductRepository->findWithoutFail($id);

        if (empty($documentSubProduct)) {
            return $this->sendError(trans('custom.document_sub_product_not_found'));
        }

        $documentSubProduct->delete();

        return $this->sendSuccess('Document Sub Product deleted successfully');
    }
}
