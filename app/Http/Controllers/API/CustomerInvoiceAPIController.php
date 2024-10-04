<?php
/**
 * =============================================
 * -- File Name : CustomerInvoiceAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 12 - June 2018
 * -- Description : This file contains the all CRUD for Customer Invoice
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceAPIRequest;
use App\Models\CustomerInvoice;
use App\Repositories\CustomerInvoiceRepository;
use App\Services\API\CustomerInvoiceAPIService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Traits\DocumentSystemMappingTrait;

/**
 * Class CustomerInvoiceController
 * @package App\Http\Controllers\API
 */

class CustomerInvoiceAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceRepository */
    private $customerInvoiceRepository;
    use DocumentSystemMappingTrait;
    public function __construct(CustomerInvoiceRepository $customerInvoiceRepo)
    {
        $this->customerInvoiceRepository = $customerInvoiceRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoices",
     *      summary="Get a listing of the CustomerInvoices.",
     *      tags={"CustomerInvoice"},
     *      description="Get all CustomerInvoices",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoice")
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
        $this->customerInvoiceRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoices = $this->customerInvoiceRepository->all();

        return $this->sendResponse($customerInvoices->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice')]));
    }

    /**
     * @param CreateCustomerInvoiceAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoices",
     *      summary="Store a newly created CustomerInvoice in storage",
     *      tags={"CustomerInvoice"},
     *      description="Store CustomerInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoice that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoice")
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
     *                  ref="#/definitions/CustomerInvoice"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceAPIRequest $request)
    {
        $input = $request->all();

        $customerInvoices = $this->customerInvoiceRepository->create($input);

        return $this->sendResponse($customerInvoices->toArray(), trans('custom.save', ['attribute' => trans('custom.customer_invoice')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoices/{id}",
     *      summary="Display the specified CustomerInvoice",
     *      tags={"CustomerInvoice"},
     *      description="Get CustomerInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoice",
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
     *                  ref="#/definitions/CustomerInvoice"
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
        /** @var CustomerInvoice $customerInvoice */
        $customerInvoice = $this->customerInvoiceRepository->findWithoutFail($id);

        if (empty($customerInvoice)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice')]));
        }

        return $this->sendResponse($customerInvoice->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoices/{id}",
     *      summary="Update the specified CustomerInvoice in storage",
     *      tags={"CustomerInvoice"},
     *      description="Update CustomerInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoice",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoice that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoice")
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
     *                  ref="#/definitions/CustomerInvoice"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerInvoice $customerInvoice */
        $customerInvoice = $this->customerInvoiceRepository->findWithoutFail($id);

        if (empty($customerInvoice)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice')]));
        }

        $customerInvoice = $this->customerInvoiceRepository->update($input, $id);

        return $this->sendResponse($customerInvoice->toArray(), trans('custom.update', ['attribute' => trans('custom.customer_invoice')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoices/{id}",
     *      summary="Remove the specified CustomerInvoice from storage",
     *      tags={"CustomerInvoice"},
     *      description="Delete CustomerInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoice",
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
        /** @var CustomerInvoice $customerInvoice */
        $customerInvoice = $this->customerInvoiceRepository->findWithoutFail($id);

        if (empty($customerInvoice)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice')]));
        }

        $customerInvoice->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.customer_invoice')]));
    }

    public function createCustomerInvoiceAPI(Request $request){

        $input = $request->all();

        $header = $request->header('Authorization');

        $createCustomerInvoice = CustomerInvoiceAPIService::storeCustomerInvoicesFromAPI($input);

        if($createCustomerInvoice['status']){
            if (count($createCustomerInvoice['data']) > 0) {
                $this->storeToDocumentSystemMapping(20,$createCustomerInvoice['data'],$header);
            }
            return $this->sendResponse($createCustomerInvoice['responseData'],"Customer Invoice Created Successfully!");
        }
        else{
            return $this->sendAPIError($createCustomerInvoice['message'],422, $createCustomerInvoice['responseData']);
        }

    }
}
