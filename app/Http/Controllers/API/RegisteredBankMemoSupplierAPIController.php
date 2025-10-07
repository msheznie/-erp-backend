<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRegisteredBankMemoSupplierAPIRequest;
use App\Http\Requests\API\UpdateRegisteredBankMemoSupplierAPIRequest;
use App\Models\RegisteredBankMemoSupplier;
use App\Repositories\RegisteredBankMemoSupplierRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RegisteredBankMemoSupplierController
 * @package App\Http\Controllers\API
 */

class RegisteredBankMemoSupplierAPIController extends AppBaseController
{
    /** @var  RegisteredBankMemoSupplierRepository */
    private $registeredBankMemoSupplierRepository;

    public function __construct(RegisteredBankMemoSupplierRepository $registeredBankMemoSupplierRepo)
    {
        $this->registeredBankMemoSupplierRepository = $registeredBankMemoSupplierRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/registeredBankMemoSuppliers",
     *      summary="Get a listing of the RegisteredBankMemoSuppliers.",
     *      tags={"RegisteredBankMemoSupplier"},
     *      description="Get all RegisteredBankMemoSuppliers",
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
     *                  @SWG\Items(ref="#/definitions/RegisteredBankMemoSupplier")
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
        $this->registeredBankMemoSupplierRepository->pushCriteria(new RequestCriteria($request));
        $this->registeredBankMemoSupplierRepository->pushCriteria(new LimitOffsetCriteria($request));
        $registeredBankMemoSuppliers = $this->registeredBankMemoSupplierRepository->all();

        return $this->sendResponse($registeredBankMemoSuppliers->toArray(), trans('custom.registered_bank_memo_suppliers_retrieved_successfu'));
    }

    /**
     * @param CreateRegisteredBankMemoSupplierAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/registeredBankMemoSuppliers",
     *      summary="Store a newly created RegisteredBankMemoSupplier in storage",
     *      tags={"RegisteredBankMemoSupplier"},
     *      description="Store RegisteredBankMemoSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RegisteredBankMemoSupplier that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RegisteredBankMemoSupplier")
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
     *                  ref="#/definitions/RegisteredBankMemoSupplier"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRegisteredBankMemoSupplierAPIRequest $request)
    {
        $input = $request->all();

        $registeredBankMemoSupplier = $this->registeredBankMemoSupplierRepository->create($input);

        return $this->sendResponse($registeredBankMemoSupplier->toArray(), trans('custom.registered_bank_memo_supplier_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/registeredBankMemoSuppliers/{id}",
     *      summary="Display the specified RegisteredBankMemoSupplier",
     *      tags={"RegisteredBankMemoSupplier"},
     *      description="Get RegisteredBankMemoSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredBankMemoSupplier",
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
     *                  ref="#/definitions/RegisteredBankMemoSupplier"
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
        /** @var RegisteredBankMemoSupplier $registeredBankMemoSupplier */
        $registeredBankMemoSupplier = $this->registeredBankMemoSupplierRepository->findWithoutFail($id);

        if (empty($registeredBankMemoSupplier)) {
            return $this->sendError(trans('custom.registered_bank_memo_supplier_not_found'));
        }

        return $this->sendResponse($registeredBankMemoSupplier->toArray(), trans('custom.registered_bank_memo_supplier_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param UpdateRegisteredBankMemoSupplierAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/registeredBankMemoSuppliers/{id}",
     *      summary="Update the specified RegisteredBankMemoSupplier in storage",
     *      tags={"RegisteredBankMemoSupplier"},
     *      description="Update RegisteredBankMemoSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredBankMemoSupplier",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RegisteredBankMemoSupplier that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RegisteredBankMemoSupplier")
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
     *                  ref="#/definitions/RegisteredBankMemoSupplier"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRegisteredBankMemoSupplierAPIRequest $request)
    {
        $input = $request->all();

        /** @var RegisteredBankMemoSupplier $registeredBankMemoSupplier */
        $registeredBankMemoSupplier = $this->registeredBankMemoSupplierRepository->findWithoutFail($id);

        if (empty($registeredBankMemoSupplier)) {
            return $this->sendError(trans('custom.registered_bank_memo_supplier_not_found'));
        }

        $registeredBankMemoSupplier = $this->registeredBankMemoSupplierRepository->update($input, $id);

        return $this->sendResponse($registeredBankMemoSupplier->toArray(), trans('custom.registeredbankmemosupplier_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/registeredBankMemoSuppliers/{id}",
     *      summary="Remove the specified RegisteredBankMemoSupplier from storage",
     *      tags={"RegisteredBankMemoSupplier"},
     *      description="Delete RegisteredBankMemoSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredBankMemoSupplier",
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
        /** @var RegisteredBankMemoSupplier $registeredBankMemoSupplier */
        $registeredBankMemoSupplier = $this->registeredBankMemoSupplierRepository->findWithoutFail($id);

        if (empty($registeredBankMemoSupplier)) {
            return $this->sendError(trans('custom.registered_bank_memo_supplier_not_found'));
        }

        $registeredBankMemoSupplier->delete();

        return $this->sendSuccess('Registered Bank Memo Supplier deleted successfully');
    }
}
