<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankReconciliationDocumentsAPIRequest;
use App\Http\Requests\API\UpdateBankReconciliationDocumentsAPIRequest;
use App\Models\BankReconciliationDocuments;
use App\Repositories\BankReconciliationDocumentsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BankReconciliationDocumentsController
 * @package App\Http\Controllers\API
 */

class BankReconciliationDocumentsAPIController extends AppBaseController
{
    /** @var  BankReconciliationDocumentsRepository */
    private $bankReconciliationDocumentsRepository;

    public function __construct(BankReconciliationDocumentsRepository $bankReconciliationDocumentsRepo)
    {
        $this->bankReconciliationDocumentsRepository = $bankReconciliationDocumentsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/bankReconciliationDocuments",
     *      summary="getBankReconciliationDocumentsList",
     *      tags={"BankReconciliationDocuments"},
     *      description="Get all BankReconciliationDocuments",
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
     *                  @OA\Items(ref="#/definitions/BankReconciliationDocuments")
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
        $this->bankReconciliationDocumentsRepository->pushCriteria(new RequestCriteria($request));
        $this->bankReconciliationDocumentsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankReconciliationDocuments = $this->bankReconciliationDocumentsRepository->all();

        return $this->sendResponse($bankReconciliationDocuments->toArray(), trans('custom.bank_reconciliation_documents_retrieved_successful'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/bankReconciliationDocuments",
     *      summary="createBankReconciliationDocuments",
     *      tags={"BankReconciliationDocuments"},
     *      description="Create BankReconciliationDocuments",
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
     *                  ref="#/definitions/BankReconciliationDocuments"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBankReconciliationDocumentsAPIRequest $request)
    {
        $input = $request->all();

        $bankReconciliationDocuments = $this->bankReconciliationDocumentsRepository->create($input);

        return $this->sendResponse($bankReconciliationDocuments->toArray(), trans('custom.bank_reconciliation_documents_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/bankReconciliationDocuments/{id}",
     *      summary="getBankReconciliationDocumentsItem",
     *      tags={"BankReconciliationDocuments"},
     *      description="Get BankReconciliationDocuments",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankReconciliationDocuments",
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
     *                  ref="#/definitions/BankReconciliationDocuments"
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
        /** @var BankReconciliationDocuments $bankReconciliationDocuments */
        $bankReconciliationDocuments = $this->bankReconciliationDocumentsRepository->findWithoutFail($id);

        if (empty($bankReconciliationDocuments)) {
            return $this->sendError(trans('custom.bank_reconciliation_documents_not_found'));
        }

        return $this->sendResponse($bankReconciliationDocuments->toArray(), trans('custom.bank_reconciliation_documents_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/bankReconciliationDocuments/{id}",
     *      summary="updateBankReconciliationDocuments",
     *      tags={"BankReconciliationDocuments"},
     *      description="Update BankReconciliationDocuments",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankReconciliationDocuments",
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
     *                  ref="#/definitions/BankReconciliationDocuments"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBankReconciliationDocumentsAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankReconciliationDocuments $bankReconciliationDocuments */
        $bankReconciliationDocuments = $this->bankReconciliationDocumentsRepository->findWithoutFail($id);

        if (empty($bankReconciliationDocuments)) {
            return $this->sendError(trans('custom.bank_reconciliation_documents_not_found'));
        }

        $bankReconciliationDocuments = $this->bankReconciliationDocumentsRepository->update($input, $id);

        return $this->sendResponse($bankReconciliationDocuments->toArray(), trans('custom.bankreconciliationdocuments_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/bankReconciliationDocuments/{id}",
     *      summary="deleteBankReconciliationDocuments",
     *      tags={"BankReconciliationDocuments"},
     *      description="Delete BankReconciliationDocuments",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BankReconciliationDocuments",
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
        /** @var BankReconciliationDocuments $bankReconciliationDocuments */
        $bankReconciliationDocuments = $this->bankReconciliationDocumentsRepository->findWithoutFail($id);

        if (empty($bankReconciliationDocuments)) {
            return $this->sendError(trans('custom.bank_reconciliation_documents_not_found'));
        }

        $bankReconciliationDocuments->delete();

        return $this->sendSuccess('Bank Reconciliation Documents deleted successfully');
    }
}
