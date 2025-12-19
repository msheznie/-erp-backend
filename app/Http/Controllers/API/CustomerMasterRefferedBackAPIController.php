<?php
/**
 * =============================================
 * -- File Name : CustomerMasterRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Customer Master Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 18 - December 2018
 * -- Description : This file contains the all CRUD for Customer Master Reffered Back
 * -- REVISION HISTORY
 * -- Date: 18-December 2018 By: Fayas Description: Added new functions named as referBackHistoryByCustomerMaster()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerMasterRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateCustomerMasterRefferedBackAPIRequest;
use App\Models\CustomerMasterRefferedBack;
use App\Repositories\CustomerMasterRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerMasterRefferedBackController
 * @package App\Http\Controllers\API
 */

class CustomerMasterRefferedBackAPIController extends AppBaseController
{
    /** @var  CustomerMasterRefferedBackRepository */
    private $customerMasterRefferedBackRepository;

    public function __construct(CustomerMasterRefferedBackRepository $customerMasterRefferedBackRepo)
    {
        $this->customerMasterRefferedBackRepository = $customerMasterRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerMasterRefferedBacks",
     *      summary="Get a listing of the CustomerMasterRefferedBacks.",
     *      tags={"CustomerMasterRefferedBack"},
     *      description="Get all CustomerMasterRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/CustomerMasterRefferedBack")
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
        $this->customerMasterRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->customerMasterRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerMasterRefferedBacks = $this->customerMasterRefferedBackRepository->all();

        return $this->sendResponse($customerMasterRefferedBacks->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_master_reffered_backs')]));
    }

    /**
     * @param CreateCustomerMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerMasterRefferedBacks",
     *      summary="Store a newly created CustomerMasterRefferedBack in storage",
     *      tags={"CustomerMasterRefferedBack"},
     *      description="Store CustomerMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerMasterRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerMasterRefferedBack")
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
     *                  ref="#/definitions/CustomerMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $customerMasterRefferedBacks = $this->customerMasterRefferedBackRepository->create($input);

        return $this->sendResponse($customerMasterRefferedBacks->toArray(), trans('custom.save', ['attribute' => trans('custom.customer_master_reffered_backs')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerMasterRefferedBacks/{id}",
     *      summary="Display the specified CustomerMasterRefferedBack",
     *      tags={"CustomerMasterRefferedBack"},
     *      description="Get CustomerMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerMasterRefferedBack",
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
     *                  ref="#/definitions/CustomerMasterRefferedBack"
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
        /** @var CustomerMasterRefferedBack $customerMasterRefferedBack */
        $customerMasterRefferedBack = $this->customerMasterRefferedBackRepository->with(['finalApprovedBy'])->findWithoutFail($id);

        if (empty($customerMasterRefferedBack)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_master_reffered_backs')]));
        }

        return $this->sendResponse($customerMasterRefferedBack->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_master_reffered_backs')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomerMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerMasterRefferedBacks/{id}",
     *      summary="Update the specified CustomerMasterRefferedBack in storage",
     *      tags={"CustomerMasterRefferedBack"},
     *      description="Update CustomerMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerMasterRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerMasterRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerMasterRefferedBack")
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
     *                  ref="#/definitions/CustomerMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerMasterRefferedBack $customerMasterRefferedBack */
        $customerMasterRefferedBack = $this->customerMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($customerMasterRefferedBack)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_master_reffered_backs')]));
        }

        $customerMasterRefferedBack = $this->customerMasterRefferedBackRepository->update($input, $id);

        return $this->sendResponse($customerMasterRefferedBack->toArray(), trans('custom.update', ['attribute' => trans('custom.customer_master_reffered_backs')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerMasterRefferedBacks/{id}",
     *      summary="Remove the specified CustomerMasterRefferedBack from storage",
     *      tags={"CustomerMasterRefferedBack"},
     *      description="Delete CustomerMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerMasterRefferedBack",
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
        /** @var CustomerMasterRefferedBack $customerMasterRefferedBack */
        $customerMasterRefferedBack = $this->customerMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($customerMasterRefferedBack)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_master_reffered_backs')]));
        }

        $customerMasterRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.customer_master_reffered_backs')]));
    }

    public function referBackHistoryByCustomerMaster(Request $request)
    {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $customerMasters = CustomerMasterRefferedBack::with(['country'])
                                                ->where('customerCodeSystem',$input['id']);

        $search = $request->input('search.value');
        if ($search) {
            $customerMasters = $customerMasters->where(function ($query) use ($search) {
                $query->where('CutomerCode', 'LIKE', "%{$search}%")
                    ->orWhere('customerShortCode', 'LIKE', "%{$search}%")
                    ->orWhere('CustomerName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($customerMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('customerCodeSystemRefferedBack', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }
}
