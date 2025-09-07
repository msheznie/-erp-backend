<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRegisteredSupplierCurrencyAPIRequest;
use App\Http\Requests\API\UpdateRegisteredSupplierCurrencyAPIRequest;
use App\Models\RegisteredSupplierCurrency;
use App\Repositories\RegisteredSupplierCurrencyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RegisteredSupplierCurrencyController
 * @package App\Http\Controllers\API
 */

class RegisteredSupplierCurrencyAPIController extends AppBaseController
{
    /** @var  RegisteredSupplierCurrencyRepository */
    private $registeredSupplierCurrencyRepository;

    public function __construct(RegisteredSupplierCurrencyRepository $registeredSupplierCurrencyRepo)
    {
        $this->registeredSupplierCurrencyRepository = $registeredSupplierCurrencyRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/registeredSupplierCurrencies",
     *      summary="Get a listing of the RegisteredSupplierCurrencies.",
     *      tags={"RegisteredSupplierCurrency"},
     *      description="Get all RegisteredSupplierCurrencies",
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
     *                  @SWG\Items(ref="#/definitions/RegisteredSupplierCurrency")
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
        $this->registeredSupplierCurrencyRepository->pushCriteria(new RequestCriteria($request));
        $this->registeredSupplierCurrencyRepository->pushCriteria(new LimitOffsetCriteria($request));
        $registeredSupplierCurrencies = $this->registeredSupplierCurrencyRepository->all();

        return $this->sendResponse($registeredSupplierCurrencies->toArray(), trans('custom.registered_supplier_currencies_retrieved_successfu'));
    }

    /**
     * @param CreateRegisteredSupplierCurrencyAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/registeredSupplierCurrencies",
     *      summary="Store a newly created RegisteredSupplierCurrency in storage",
     *      tags={"RegisteredSupplierCurrency"},
     *      description="Store RegisteredSupplierCurrency",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RegisteredSupplierCurrency that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RegisteredSupplierCurrency")
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
     *                  ref="#/definitions/RegisteredSupplierCurrency"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRegisteredSupplierCurrencyAPIRequest $request)
    {
        $input = $request->all();

        $registeredSupplierCurrency = $this->registeredSupplierCurrencyRepository->create($input);

        return $this->sendResponse($registeredSupplierCurrency->toArray(), trans('custom.registered_supplier_currency_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/registeredSupplierCurrencies/{id}",
     *      summary="Display the specified RegisteredSupplierCurrency",
     *      tags={"RegisteredSupplierCurrency"},
     *      description="Get RegisteredSupplierCurrency",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredSupplierCurrency",
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
     *                  ref="#/definitions/RegisteredSupplierCurrency"
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
        /** @var RegisteredSupplierCurrency $registeredSupplierCurrency */
        $registeredSupplierCurrency = $this->registeredSupplierCurrencyRepository->findWithoutFail($id);

        if (empty($registeredSupplierCurrency)) {
            return $this->sendError(trans('custom.registered_supplier_currency_not_found'));
        }

        return $this->sendResponse($registeredSupplierCurrency->toArray(), trans('custom.registered_supplier_currency_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdateRegisteredSupplierCurrencyAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/registeredSupplierCurrencies/{id}",
     *      summary="Update the specified RegisteredSupplierCurrency in storage",
     *      tags={"RegisteredSupplierCurrency"},
     *      description="Update RegisteredSupplierCurrency",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredSupplierCurrency",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RegisteredSupplierCurrency that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RegisteredSupplierCurrency")
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
     *                  ref="#/definitions/RegisteredSupplierCurrency"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRegisteredSupplierCurrencyAPIRequest $request)
    {
        $input = $request->all();

        /** @var RegisteredSupplierCurrency $registeredSupplierCurrency */
        $registeredSupplierCurrency = $this->registeredSupplierCurrencyRepository->findWithoutFail($id);

        if (empty($registeredSupplierCurrency)) {
            return $this->sendError(trans('custom.registered_supplier_currency_not_found'));
        }

        $registeredSupplierCurrency = $this->registeredSupplierCurrencyRepository->update($input, $id);

        return $this->sendResponse($registeredSupplierCurrency->toArray(), trans('custom.registeredsuppliercurrency_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/registeredSupplierCurrencies/{id}",
     *      summary="Remove the specified RegisteredSupplierCurrency from storage",
     *      tags={"RegisteredSupplierCurrency"},
     *      description="Delete RegisteredSupplierCurrency",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RegisteredSupplierCurrency",
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
        /** @var RegisteredSupplierCurrency $registeredSupplierCurrency */
        $registeredSupplierCurrency = $this->registeredSupplierCurrencyRepository->findWithoutFail($id);

        if (empty($registeredSupplierCurrency)) {
            return $this->sendError(trans('custom.registered_supplier_currency_not_found'));
        }

        $registeredSupplierCurrency->delete();

        return $this->sendSuccess('Registered Supplier Currency deleted successfully');
    }
}
