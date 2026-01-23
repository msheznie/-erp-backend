<?php
/**
 * =============================================
 * -- File Name : CustomerInvoiceCollectionDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  CustomerInvoiceCollectionDetail
 * -- Author : Mohamed Nazir
 * -- Create date : 08 - August 2018
 * -- Description : This file contains the all CRUD for Customer Invoice Collection Detail
 * -- REVISION HISTORY
 * -- Date: 17-December 2018 By: Nazir Description: Added new function getCustomerCollectionItems(),
 */


namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceCollectionDetailAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceCollectionDetailAPIRequest;
use App\Models\CustomerInvoiceCollectionDetail;
use App\Models\CustomerInvoiceDirect;
use App\Repositories\CustomerInvoiceCollectionDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Arr;
use App\helper\Helper;

/**
 * Class CustomerInvoiceCollectionDetailController
 * @package App\Http\Controllers\API
 */

class CustomerInvoiceCollectionDetailAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceCollectionDetailRepository */
    private $customerInvoiceCollectionDetailRepository;

    public function __construct(CustomerInvoiceCollectionDetailRepository $customerInvoiceCollectionDetailRepo)
    {
        $this->customerInvoiceCollectionDetailRepository = $customerInvoiceCollectionDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceCollectionDetails",
     *      summary="Get a listing of the CustomerInvoiceCollectionDetails.",
     *      tags={"CustomerInvoiceCollectionDetail"},
     *      description="Get all CustomerInvoiceCollectionDetails",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceCollectionDetail")
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
        $this->customerInvoiceCollectionDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceCollectionDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceCollectionDetails = $this->customerInvoiceCollectionDetailRepository->all();

        return $this->sendResponse($customerInvoiceCollectionDetails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice_collection_details')]));
    }

    /**
     * @param CreateCustomerInvoiceCollectionDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceCollectionDetails",
     *      summary="Store a newly created CustomerInvoiceCollectionDetail in storage",
     *      tags={"CustomerInvoiceCollectionDetail"},
     *      description="Store CustomerInvoiceCollectionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceCollectionDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceCollectionDetail")
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
     *                  ref="#/definitions/CustomerInvoiceCollectionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceCollectionDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $customerInvoiceID = $input['customerInvoiceID'];

        $invoiceMasterData = CustomerInvoiceDirect::find($customerInvoiceID);
        if (empty($invoiceMasterData)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.invoice')]));
        }

        if (isset($input['collectionDate']) && $input['collectionDate']) {
            $input['collectionDate'] = new Carbon($input['collectionDate']);
        }

        $input['companySystemID'] = $invoiceMasterData->companySystemID;
        $input['companyID'] = $invoiceMasterData->companyID;
        $input['createdUserID'] = Helper::getEmployeeID();
        $input['createdPcID'] = getenv('COMPUTERNAME');
        $input['modifiedUser'] = Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');
        $input['createdUserSystemID'] = Helper::getEmployeeSystemID();
        $input['modifiedUserSystemID'] = Helper::getEmployeeSystemID();

        $customerInvoiceCollectionDetails = $this->customerInvoiceCollectionDetailRepository->create($input);

        return $this->sendResponse($customerInvoiceCollectionDetails->toArray(), trans('custom.save', ['attribute' => trans('custom.customer_invoice_collection_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceCollectionDetails/{id}",
     *      summary="Display the specified CustomerInvoiceCollectionDetail",
     *      tags={"CustomerInvoiceCollectionDetail"},
     *      description="Get CustomerInvoiceCollectionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceCollectionDetail",
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
     *                  ref="#/definitions/CustomerInvoiceCollectionDetail"
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
        /** @var CustomerInvoiceCollectionDetail $customerInvoiceCollectionDetail */
        $customerInvoiceCollectionDetail = $this->customerInvoiceCollectionDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceCollectionDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_collection_details')]));
        }

        return $this->sendResponse($customerInvoiceCollectionDetail->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice_collection_details')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceCollectionDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceCollectionDetails/{id}",
     *      summary="Update the specified CustomerInvoiceCollectionDetail in storage",
     *      tags={"CustomerInvoiceCollectionDetail"},
     *      description="Update CustomerInvoiceCollectionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceCollectionDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceCollectionDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceCollectionDetail")
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
     *                  ref="#/definitions/CustomerInvoiceCollectionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceCollectionDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = Arr::except($input, ['modified_by']);
        $input = $this->convertArrayToValue($input);
        /** @var CustomerInvoiceCollectionDetail $customerInvoiceCollectionDetail */
        $customerInvoiceCollectionDetail = $this->customerInvoiceCollectionDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceCollectionDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_collection_details')]));
        }

        $invoiceMasterData = CustomerInvoiceDirect::find($input['customerInvoiceID']);
        if (empty($invoiceMasterData)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.invoice')]));
        }

        if (isset($input['collectionDate']) && $input['collectionDate']) {
            $input['collectionDate'] = new Carbon($input['collectionDate']);
        }

        $input['modifiedUser'] = Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');
        $input['modifiedUserSystemID'] = Helper::getEmployeeSystemID();

        $customerInvoiceCollectionDetail = $this->customerInvoiceCollectionDetailRepository->update($input, $id);

        return $this->sendResponse($customerInvoiceCollectionDetail->toArray(), trans('custom.update', ['attribute' => trans('custom.customer_invoice_collection_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceCollectionDetails/{id}",
     *      summary="Remove the specified CustomerInvoiceCollectionDetail from storage",
     *      tags={"CustomerInvoiceCollectionDetail"},
     *      description="Delete CustomerInvoiceCollectionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceCollectionDetail",
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
        /** @var CustomerInvoiceCollectionDetail $customerInvoiceCollectionDetail */
        $customerInvoiceCollectionDetail = $this->customerInvoiceCollectionDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceCollectionDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_collection_details')]));
        }

        $customerInvoiceCollectionDetail->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.customer_invoice_collection_details')]));
    }

    public function getCustomerCollectionItems(Request $request)
    {
        $input = $request->all();
        $invoiceID = $input['invoiceID'];

        $items = CustomerInvoiceCollectionDetail::where('customerInvoiceID', $invoiceID)
            ->with(['modified_by'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice_collection_details')]));
    }
}
