<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRegisteredSupplierAPIRequest;
use App\Http\Requests\API\UpdateRegisteredSupplierAPIRequest;
use App\Models\RegisteredSupplier;
use App\Repositories\RegisteredSupplierRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RegisteredSupplierController
 * @package App\Http\Controllers\API
 */

class RegisteredSupplierAPIController extends AppBaseController
{
    /** @var  RegisteredSupplierRepository */
    private $registeredSupplierRepository;

    public function __construct(RegisteredSupplierRepository $registeredSupplierRepo)
    {
        $this->registeredSupplierRepository = $registeredSupplierRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/registeredSuppliers",
     *      summary="Get a listing of the RegisteredSuppliers.",
     *      tags={"RegisteredSupplier"},
     *      description="Get all RegisteredSuppliers",
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
     *                  @SWG\Items(ref="#/definitions/RegisteredSupplier")
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
        $this->registeredSupplierRepository->pushCriteria(new RequestCriteria($request));
        $this->registeredSupplierRepository->pushCriteria(new LimitOffsetCriteria($request));
        $registeredSuppliers = $this->registeredSupplierRepository->all();

        return $this->sendResponse($registeredSuppliers->toArray(), trans('custom.registered_suppliers_retrieved_successfully'));
    }

    /**
     * @param CreateRegisteredSupplierAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/registeredSuppliers",
     *      summary="Store a newly created RegisteredSupplier in storage",
     *      tags={"RegisteredSupplier"},
     *      description="Store RegisteredSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RegisteredSupplier that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RegisteredSupplier")
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
     *                  ref="#/definitions/RegisteredSupplier"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRegisteredSupplierAPIRequest $request)
    {
        $input = $request->all();

        $registeredSupplier = $this->registeredSupplierRepository->create($input);

        return $this->sendResponse($registeredSupplier->toArray(), trans('custom.registered_supplier_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/registeredSuppliers/{id}",
     *      summary="Display the specified RegisteredSupplier",
     *      tags={"RegisteredSupplier"},
     *      description="Get RegisteredSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredSupplier",
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
     *                  ref="#/definitions/RegisteredSupplier"
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
        /** @var RegisteredSupplier $registeredSupplier */
        $registeredSupplier = $this->registeredSupplierRepository->findWithoutFail($id);

        if (empty($registeredSupplier)) {
            return $this->sendError(trans('custom.registered_supplier_not_found'));
        }

        return $this->sendResponse($registeredSupplier->toArray(), trans('custom.registered_supplier_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateRegisteredSupplierAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/registeredSuppliers/{id}",
     *      summary="Update the specified RegisteredSupplier in storage",
     *      tags={"RegisteredSupplier"},
     *      description="Update RegisteredSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredSupplier",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RegisteredSupplier that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RegisteredSupplier")
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
     *                  ref="#/definitions/RegisteredSupplier"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRegisteredSupplierAPIRequest $request)
    {
        $input = $request->all();

        /** @var RegisteredSupplier $registeredSupplier */
        $registeredSupplier = $this->registeredSupplierRepository->findWithoutFail($id);

        if (empty($registeredSupplier)) {
            return $this->sendError(trans('custom.registered_supplier_not_found'));
        }

        $registeredSupplier = $this->registeredSupplierRepository->update($input, $id);

        return $this->sendResponse($registeredSupplier->toArray(), trans('custom.registeredsupplier_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/registeredSuppliers/{id}",
     *      summary="Remove the specified RegisteredSupplier from storage",
     *      tags={"RegisteredSupplier"},
     *      description="Delete RegisteredSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredSupplier",
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
        /** @var RegisteredSupplier $registeredSupplier */
        $registeredSupplier = $this->registeredSupplierRepository->findWithoutFail($id);

        if (empty($registeredSupplier)) {
            return $this->sendError(trans('custom.registered_supplier_not_found'));
        }

        $registeredSupplier->delete();

        return $this->sendSuccess('Registered Supplier deleted successfully');
    }
}
