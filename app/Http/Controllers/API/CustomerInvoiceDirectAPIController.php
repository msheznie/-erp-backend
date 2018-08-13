<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceDirectAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceDirectAPIRequest;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Repositories\CustomerInvoiceDirectRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerInvoiceDirectController
 * @package App\Http\Controllers\API
 */
class CustomerInvoiceDirectAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceDirectRepository */
    private $customerInvoiceDirectRepository;

    public function __construct(CustomerInvoiceDirectRepository $customerInvoiceDirectRepo)
    {
        $this->customerInvoiceDirectRepository = $customerInvoiceDirectRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceDirects",
     *      summary="Get a listing of the CustomerInvoiceDirects.",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Get all CustomerInvoiceDirects",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceDirect")
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
        $this->customerInvoiceDirectRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceDirectRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceDirects = $this->customerInvoiceDirectRepository->all();

        return $this->sendResponse($customerInvoiceDirects->toArray(), 'Customer Invoice Directs retrieved successfully');
    }

    /**
     * @param CreateCustomerInvoiceDirectAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceDirects",
     *      summary="Store a newly created CustomerInvoiceDirect in storage",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Store CustomerInvoiceDirect",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceDirect that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceDirect")
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
     *                  ref="#/definitions/CustomerInvoiceDirect"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceDirectAPIRequest $request)
    {
        $input = $request->all();

        $customerInvoiceDirects = $this->customerInvoiceDirectRepository->create($input);

        return $this->sendResponse($customerInvoiceDirects->toArray(), 'Customer Invoice Direct saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceDirects/{id}",
     *      summary="Display the specified CustomerInvoiceDirect",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Get CustomerInvoiceDirect",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirect",
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
     *                  ref="#/definitions/CustomerInvoiceDirect"
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
        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found');
        }

        return $this->sendResponse($customerInvoiceDirect->toArray(), 'Customer Invoice Direct retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceDirectAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceDirects/{id}",
     *      summary="Update the specified CustomerInvoiceDirect in storage",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Update CustomerInvoiceDirect",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirect",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceDirect that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceDirect")
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
     *                  ref="#/definitions/CustomerInvoiceDirect"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceDirectAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found');
        }

        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->update($input, $id);

        return $this->sendResponse($customerInvoiceDirect->toArray(), 'CustomerInvoiceDirect updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceDirects/{id}",
     *      summary="Remove the specified CustomerInvoiceDirect from storage",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Delete CustomerInvoiceDirect",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirect",
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
        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found');
        }

        $customerInvoiceDirect->delete();

        return $this->sendResponse($id, 'Customer Invoice Direct deleted successfully');
    }

    public function customerInvoiceDetails(request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->with(['company', 'customer','tax', 'invoicedetails'
        => function ($query) {
                $query->with(['performadetails' => function ($query) {
                    $query->with(['freebillingmaster' => function ($query) {
                        $query->with(['ticketmaster' => function ($query) {
                            $query->with(['field']);
                        }]);
                    }]);
                }]);
            }
        ])->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found', 500);
        } else {
            /*   $CustomerInvoiceDirectDetail = CustomerInvoiceDirectDetail::select('*')->where('custInvoiceDirectID', $id)->get();
               $data['data']['master'] = $customerInvoiceDirect;
               $data['data']['detail'] = $CustomerInvoiceDirectDetail;*/

            return $this->sendResponse($customerInvoiceDirect, 'Customer Invoice Direct deleted successfully');
        }
    }
}
